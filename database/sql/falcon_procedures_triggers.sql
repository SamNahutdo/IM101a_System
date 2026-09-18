-- =============================================================================
-- FalconSystem: Athletic Equipment Services & Resource Management System
-- Advanced Database Systems: Stored Procedures, Functions, and Triggers (MySQL 8+)
-- =============================================================================

USE falcon_system;

-- -----------------------------------------------------------------------------
-- 1. STORED FUNCTION: fn_is_equipment_available
-- Checks whether a piece of equipment is available and free from reservation overlap
-- -----------------------------------------------------------------------------
DELIMITER //

DROP FUNCTION IF EXISTS fn_is_equipment_available //
CREATE FUNCTION fn_is_equipment_available(
    p_equipment_id BIGINT UNSIGNED,
    p_start_time DATETIME,
    p_end_time DATETIME
) RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_status VARCHAR(50);
    DECLARE v_overlap_count INT DEFAULT 0;

    -- Check base equipment status
    SELECT status INTO v_status FROM equipment WHERE id = p_equipment_id;
    IF v_status IS NULL OR v_status NOT IN ('Available', 'Reserved') THEN
        RETURN FALSE;
    END IF;

    -- Check if active approved reservation overlaps the window
    SELECT COUNT(*) INTO v_overlap_count
    FROM reservation_items ri
    JOIN reservations r ON ri.reservation_id = r.id
    WHERE ri.equipment_id = p_equipment_id
      AND ri.item_status = 'Approved'
      AND r.status = 'Approved'
      AND (
          (p_start_time BETWEEN r.start_time AND r.end_time)
          OR (p_end_time BETWEEN r.start_time AND r.end_time)
          OR (r.start_time BETWEEN p_start_time AND p_end_time)
      );

    IF v_overlap_count > 0 THEN
        RETURN FALSE;
    END IF;

    RETURN TRUE;
END //

-- -----------------------------------------------------------------------------
-- 2. STORED PROCEDURE: sp_process_checkout
-- Executes atomic equipment checkout with status update and rollback on error
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_process_checkout //
CREATE PROCEDURE sp_process_checkout(
    IN p_borrower_id BIGINT UNSIGNED,
    IN p_staff_id BIGINT UNSIGNED,
    IN p_reservation_id BIGINT UNSIGNED,
    IN p_team_id BIGINT UNSIGNED,
    IN p_expected_return DATETIME,
    IN p_equipment_id BIGINT UNSIGNED,
    IN p_notes TEXT,
    OUT p_transaction_id BIGINT UNSIGNED,
    OUT p_status_code VARCHAR(20),
    OUT p_message VARCHAR(255)
)
proc_label: BEGIN
    DECLARE v_eq_status VARCHAR(50);
    DECLARE v_eq_cond VARCHAR(50);
    DECLARE v_trans_code VARCHAR(30);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_status_code = 'ERR_SQL';
        SET p_message = 'An unexpected SQL error occurred during checkout. Transaction rolled back.';
    END;

    START TRANSACTION;

    -- 1. Validate equipment exists and is available
    SELECT status, current_condition INTO v_eq_status, v_eq_cond 
    FROM equipment WHERE id = p_equipment_id FOR UPDATE;

    IF v_eq_status IS NULL THEN
        ROLLBACK;
        SET p_status_code = 'ERR_NOT_FOUND';
        SET p_message = 'Requested equipment does not exist.';
        LEAVE proc_label;
    END IF;

    IF v_eq_status NOT IN ('Available', 'Reserved') THEN
        ROLLBACK;
        SET p_status_code = 'ERR_UNAVAILABLE';
        SET p_message = CONCAT('Equipment is currently in status: ', v_eq_status);
        LEAVE proc_label;
    END IF;

    -- 2. Generate transaction code
    SET v_trans_code = CONCAT('BOR-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 100000), 5, '0'));

    -- 3. Insert transaction header
    INSERT INTO borrowing_transactions (
        transaction_code, reservation_id, borrower_id, team_id, processed_by,
        checkout_time, expected_return_time, status, notes, created_at, updated_at
    ) VALUES (
        v_trans_code, p_reservation_id, p_borrower_id, p_team_id, p_staff_id,
        NOW(), p_expected_return, 'Active', p_notes, NOW(), NOW()
    );

    SET p_transaction_id = LAST_INSERT_ID();

    -- 4. Insert borrowing item
    INSERT INTO borrowing_items (
        transaction_id, equipment_id, checkout_condition, status
    ) VALUES (
        p_transaction_id, p_equipment_id, v_eq_cond, 'Borrowed'
    );

    -- 5. Update equipment status
    UPDATE equipment SET status = 'Borrowed', updated_at = NOW() WHERE id = p_equipment_id;

    -- 6. Log status transition
    INSERT INTO equipment_status_history (equipment_id, old_status, new_status, changed_by, reason)
    VALUES (p_equipment_id, v_eq_status, 'Borrowed', p_staff_id, CONCAT('Checked out in transaction ', v_trans_code));

    COMMIT;

    SET p_status_code = 'SUCCESS';
    SET p_message = CONCAT('Equipment checked out successfully under transaction ', v_trans_code);
END //

-- -----------------------------------------------------------------------------
-- 3. STORED PROCEDURE: sp_process_return
-- Executes atomic equipment return, condition recording, and status routing
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_process_return //
CREATE PROCEDURE sp_process_return(
    IN p_borrowing_item_id BIGINT UNSIGNED,
    IN p_staff_id BIGINT UNSIGNED,
    IN p_return_condition VARCHAR(50),
    IN p_remarks VARCHAR(255),
    OUT p_status_code VARCHAR(20),
    OUT p_message VARCHAR(255)
)
proc_label: BEGIN
    DECLARE v_eq_id BIGINT UNSIGNED;
    DECLARE v_trans_id BIGINT UNSIGNED;
    DECLARE v_item_status VARCHAR(50);
    DECLARE v_old_eq_status VARCHAR(50);
    DECLARE v_new_eq_status VARCHAR(50);
    DECLARE v_pending_items INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_status_code = 'ERR_SQL';
        SET p_message = 'Database error occurred during equipment return. Transaction rolled back.';
    END;

    START TRANSACTION;

    -- 1. Locate borrowing item
    SELECT equipment_id, transaction_id, status 
    INTO v_eq_id, v_trans_id, v_item_status
    FROM borrowing_items WHERE id = p_borrowing_item_id FOR UPDATE;

    IF v_eq_id IS NULL THEN
        ROLLBACK;
        SET p_status_code = 'ERR_NOT_FOUND';
        SET p_message = 'Borrowing item record not found.';
        LEAVE proc_label;
    END IF;

    IF v_item_status = 'Returned' THEN
        ROLLBACK;
        SET p_status_code = 'ERR_ALREADY_RETURNED';
        SET p_message = 'Equipment item has already been marked as returned.';
        LEAVE proc_label;
    END IF;

    -- 2. Update item record
    UPDATE borrowing_items 
    SET return_condition = p_return_condition,
        return_inspected_by = p_staff_id,
        returned_at = NOW(),
        status = 'Returned',
        remarks = p_remarks
    WHERE id = p_borrowing_item_id;

    -- 3. Determine new equipment status based on return inspection condition
    IF p_return_condition IN ('Fair', 'Poor') THEN
        SET v_new_eq_status = 'Maintenance';
    ELSE
        SET v_new_eq_status = 'Available';
    END IF;

    SELECT status INTO v_old_eq_status FROM equipment WHERE id = v_eq_id;

    UPDATE equipment 
    SET status = v_new_eq_status,
        current_condition = p_return_condition,
        updated_at = NOW()
    WHERE id = v_eq_id;

    -- 4. Status history log
    INSERT INTO equipment_status_history (equipment_id, old_status, new_status, changed_by, reason)
    VALUES (v_eq_id, v_old_eq_status, v_new_eq_status, p_staff_id, CONCAT('Returned with condition: ', p_return_condition));

    -- 5. If all items in this transaction are returned, complete transaction header
    SELECT COUNT(*) INTO v_pending_items
    FROM borrowing_items 
    WHERE transaction_id = v_trans_id AND status = 'Borrowed';

    IF v_pending_items = 0 THEN
        UPDATE borrowing_transactions 
        SET status = 'Completed', actual_return_time = NOW(), updated_at = NOW()
        WHERE id = v_trans_id;
    END IF;

    COMMIT;

    SET p_status_code = 'SUCCESS';
    SET p_message = 'Equipment item successfully returned and inspected.';
END //

-- -----------------------------------------------------------------------------
-- 4. DATABASE TRIGGER: trg_damage_severe_equipment_status
-- When severe damage report is inserted, automatically set equipment to Damaged
-- -----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_damage_severe_equipment_status //
CREATE TRIGGER trg_damage_severe_equipment_status
AFTER INSERT ON damage_reports
FOR EACH ROW
BEGIN
    IF NEW.severity = 'Severe' THEN
        UPDATE equipment 
        SET status = 'Damaged',
            current_condition = 'Poor',
            updated_at = NOW()
        WHERE id = NEW.equipment_id;

        INSERT INTO equipment_status_history (equipment_id, old_status, new_status, changed_by, reason)
        VALUES (NEW.equipment_id, 'Unknown', 'Damaged', NEW.reported_by, CONCAT('Severe damage report: ', NEW.report_code));
    END IF;
END //

-- -----------------------------------------------------------------------------
-- 5. DATABASE TRIGGER: trg_equipment_status_audit
-- Database-level audit log trigger when equipment status changes
-- -----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_equipment_status_audit //
CREATE TRIGGER trg_equipment_status_audit
AFTER UPDATE ON equipment
FOR EACH ROW
BEGIN
    IF OLD.status <> NEW.status OR OLD.current_condition <> NEW.current_condition THEN
        INSERT INTO audit_logs (
            user_id, action, table_name, record_id, old_values, new_values, created_at
        ) VALUES (
            NULL,
            'UPDATE',
            'equipment',
            NEW.id,
            JSON_OBJECT('status', OLD.status, 'condition', OLD.current_condition),
            JSON_OBJECT('status', NEW.status, 'condition', NEW.current_condition),
            NOW()
        );
    END IF;
END //

DELIMITER ;
