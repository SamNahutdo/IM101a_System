-- ==============================================================================
-- FALCONSYSTEM: ADVANCED DATABASE SYSTEMS AUTOMATION SCRIPT
-- Triggers, Stored Procedures, Stored Functions, and Views
-- ==============================================================================

USE falcon_system;

-- ------------------------------------------------------------------------------
-- 1. STORED FUNCTIONS
-- ------------------------------------------------------------------------------

DROP FUNCTION IF EXISTS fn_overdue_days;
DELIMITER $$
CREATE FUNCTION fn_overdue_days(p_expected_date DATE, p_actual_date DATE)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_days INT DEFAULT 0;
    
    IF p_actual_date IS NOT NULL THEN
        IF p_actual_date > p_expected_date THEN
            SET v_days = DATEDIFF(p_actual_date, p_expected_date);
        ELSE
            SET v_days = 0;
        END IF;
    ELSE
        IF CURRENT_DATE() > p_expected_date THEN
            SET v_days = DATEDIFF(CURRENT_DATE(), p_expected_date);
        ELSE
            SET v_days = 0;
        END IF;
    END IF;
    
    RETURN v_days;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS fn_equipment_availability;
DELIMITER $$
CREATE FUNCTION fn_equipment_availability(p_equipment_id BIGINT)
RETURNS VARCHAR(50)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_qty INT DEFAULT 0;
    DECLARE v_avail INT DEFAULT 0;
    DECLARE v_status VARCHAR(30) DEFAULT 'Available';
    
    SELECT quantity, available_quantity, status 
    INTO v_qty, v_avail, v_status
    FROM equipment
    WHERE id = p_equipment_id;
    
    IF v_status != 'Available' THEN
        RETURN v_status;
    ELSEIF v_avail <= 0 THEN
        RETURN 'Out of Stock';
    ELSEIF v_avail <= 2 THEN
        RETURN 'Low Stock';
    ELSE
        RETURN 'Available';
    END IF;
END$$
DELIMITER ;

-- ------------------------------------------------------------------------------
-- 2. DATABASE TRIGGERS
-- ------------------------------------------------------------------------------

-- Trigger 1: Audit INSERT on Equipment
DROP TRIGGER IF EXISTS trg_audit_equipment_insert;
DELIMITER $$
CREATE TRIGGER trg_audit_equipment_insert
AFTER INSERT ON equipment
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, created_at)
    VALUES (
        NULL,
        'INSERT',
        'equipment',
        NEW.id,
        NULL,
        JSON_OBJECT(
            'equipment_code', NEW.equipment_code,
            'equipment_name', NEW.equipment_name,
            'category', NEW.category,
            'quantity', NEW.quantity,
            'available_quantity', NEW.available_quantity,
            'condition', NEW.condition,
            'status', NEW.status
        ),
        NOW()
    );
END$$
DELIMITER ;

-- Trigger 2: Audit UPDATE on Equipment
DROP TRIGGER IF EXISTS trg_audit_equipment_update;
DELIMITER $$
CREATE TRIGGER trg_audit_equipment_update
AFTER UPDATE ON equipment
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, created_at)
    VALUES (
        NULL,
        'UPDATE',
        'equipment',
        NEW.id,
        JSON_OBJECT(
            'available_quantity', OLD.available_quantity,
            'condition', OLD.condition,
            'status', OLD.status
        ),
        JSON_OBJECT(
            'available_quantity', NEW.available_quantity,
            'condition', NEW.condition,
            'status', NEW.status
        ),
        NOW()
    );
END$$
DELIMITER ;

-- Trigger 3: Audit DELETE on Equipment
DROP TRIGGER IF EXISTS trg_audit_equipment_delete;
DELIMITER $$
CREATE TRIGGER trg_audit_equipment_delete
AFTER DELETE ON equipment
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, created_at)
    VALUES (
        NULL,
        'DELETE',
        'equipment',
        OLD.id,
        JSON_OBJECT(
            'equipment_code', OLD.equipment_code,
            'equipment_name', OLD.equipment_name,
            'quantity', OLD.quantity
        ),
        NULL,
        NOW()
    );
END$$
DELIMITER ;

-- Trigger 4: Prevent Negative Inventory & Decrease Inventory on Borrowing Item Insert
DROP TRIGGER IF EXISTS trg_borrowing_items_insert_inventory;
DELIMITER $$
CREATE TRIGGER trg_borrowing_items_insert_inventory
BEFORE INSERT ON borrowing_items
FOR EACH ROW
BEGIN
    DECLARE v_current_avail INT;
    
    -- Check live availability with row lock
    SELECT available_quantity INTO v_current_avail
    FROM equipment
    WHERE id = NEW.equipment_id;
    
    IF v_current_avail < NEW.quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Negative inventory violation: Requested quantity exceeds available inventory.';
    ELSE
        -- Automatically decrease available quantity
        UPDATE equipment
        SET available_quantity = available_quantity - NEW.quantity
        WHERE id = NEW.equipment_id;
    END IF;
END$$
DELIMITER ;

-- Trigger 5: Increase Inventory on Return of Borrowing Item
DROP TRIGGER IF EXISTS trg_borrowing_items_return_inventory;
DELIMITER $$
CREATE TRIGGER trg_borrowing_items_return_inventory
AFTER UPDATE ON borrowing_items
FOR EACH ROW
BEGIN
    DECLARE v_returned_delta INT;
    
    IF NEW.returned_quantity > OLD.returned_quantity THEN
        SET v_returned_delta = NEW.returned_quantity - OLD.returned_quantity;
        
        UPDATE equipment
        SET available_quantity = available_quantity + v_returned_delta
        WHERE id = NEW.equipment_id;
    END IF;
END$$
DELIMITER ;

-- ------------------------------------------------------------------------------
-- 3. STORED PROCEDURES
-- ------------------------------------------------------------------------------

-- Procedure 1: Atomic Borrowing Processing with Row Locking & Rollback
DROP PROCEDURE IF EXISTS sp_process_borrowing;
DELIMITER $$
CREATE PROCEDURE sp_process_borrowing(
    IN p_athlete_id BIGINT,
    IN p_equipment_id BIGINT,
    IN p_quantity INT,
    IN p_expected_return_date DATE,
    IN p_approved_by BIGINT,
    IN p_remarks TEXT,
    OUT p_success BOOLEAN,
    OUT p_message VARCHAR(255),
    OUT p_transaction_id BIGINT
)
proc_block: BEGIN
    DECLARE v_avail_qty INT DEFAULT 0;
    DECLARE v_condition VARCHAR(50) DEFAULT 'Good';
    DECLARE v_athlete_status VARCHAR(20);
    DECLARE v_tx_id BIGINT;

    -- Exception handler with rollback
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_success = FALSE;
        SET p_message = 'Transaction aborted: An unexpected database error occurred during borrowing.';
        SET p_transaction_id = NULL;
    END;

    -- Validate parameters
    IF p_quantity <= 0 THEN
        SET p_success = FALSE;
        SET p_message = 'Validation error: Borrow quantity must be at least 1.';
        SET p_transaction_id = NULL;
        LEAVE proc_block;
    END IF;

    -- Validate Athlete
    SELECT status INTO v_athlete_status
    FROM athletes
    WHERE id = p_athlete_id;

    IF v_athlete_status IS NULL OR v_athlete_status != 'Active' THEN
        SET p_success = FALSE;
        SET p_message = 'Validation error: Athlete record is invalid or inactive.';
        SET p_transaction_id = NULL;
        LEAVE proc_block;
    END IF;

    -- START ATOMIC TRANSACTION
    START TRANSACTION;

    -- Critical: Row lock on equipment to prevent race condition & negative inventory
    SELECT available_quantity, `condition`
    INTO v_avail_qty, v_condition
    FROM equipment
    WHERE id = p_equipment_id
    FOR UPDATE;

    IF v_avail_qty IS NULL THEN
        ROLLBACK;
        SET p_success = FALSE;
        SET p_message = 'Validation error: Equipment does not exist.';
        SET p_transaction_id = NULL;
        LEAVE proc_block;
    END IF;

    IF v_avail_qty < p_quantity THEN
        ROLLBACK;
        SET p_success = FALSE;
        SET p_message = CONCAT('Insufficient inventory: Only ', v_avail_qty, ' available, but ', p_quantity, ' requested.');
        SET p_transaction_id = NULL;
        LEAVE proc_block;
    END IF;

    -- Insert Borrowing Transaction
    INSERT INTO borrowing_transactions (
        athlete_id, approved_by, borrow_date, expected_return_date, status, remarks, created_at, updated_at
    ) VALUES (
        p_athlete_id, p_approved_by, CURRENT_DATE(), p_expected_return_date, 'Borrowed', p_remarks, NOW(), NOW()
    );

    SET v_tx_id = LAST_INSERT_ID();

    -- Insert Borrowing Item (Trigger 4 will safely deduct inventory)
    INSERT INTO borrowing_items (
        borrowing_transaction_id, equipment_id, quantity, condition_before, returned_quantity, created_at, updated_at
    ) VALUES (
        v_tx_id, p_equipment_id, p_quantity, v_condition, 0, NOW(), NOW()
    );

    -- Log transaction in audit
    INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, created_at)
    VALUES (
        p_approved_by,
        'BORROW',
        'borrowing_transactions',
        v_tx_id,
        NULL,
        JSON_OBJECT(
            'athlete_id', p_athlete_id,
            'equipment_id', p_equipment_id,
            'quantity', p_quantity,
            'expected_return', p_expected_return_date
        ),
        NOW()
    );

    COMMIT;

    SET p_success = TRUE;
    SET p_message = 'Borrowing transaction successfully completed.';
    SET p_transaction_id = v_tx_id;
END$$
DELIMITER ;

-- Procedure 2: Atomic Return Processing
DROP PROCEDURE IF EXISTS sp_process_return;
DELIMITER $$
CREATE PROCEDURE sp_process_return(
    IN p_borrowing_item_id BIGINT,
    IN p_returned_qty INT,
    IN p_condition_after VARCHAR(50),
    IN p_processed_by BIGINT,
    OUT p_success BOOLEAN,
    OUT p_message VARCHAR(255)
)
proc_block: BEGIN
    DECLARE v_tx_id BIGINT;
    DECLARE v_equip_id BIGINT;
    DECLARE v_total_qty INT;
    DECLARE v_curr_returned INT;
    DECLARE v_pending_items INT;

    -- Exception Handler with Rollback
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_success = FALSE;
        SET p_message = 'Transaction aborted: An unexpected error occurred while processing return.';
    END;

    IF p_returned_qty <= 0 THEN
        SET p_success = FALSE;
        SET p_message = 'Validation error: Returned quantity must be greater than zero.';
        LEAVE proc_block;
    END IF;

    -- START ATOMIC TRANSACTION
    START TRANSACTION;

    -- Lock borrowing item
    SELECT borrowing_transaction_id, equipment_id, quantity, returned_quantity
    INTO v_tx_id, v_equip_id, v_total_qty, v_curr_returned
    FROM borrowing_items
    WHERE id = p_borrowing_item_id
    FOR UPDATE;

    IF v_tx_id IS NULL THEN
        ROLLBACK;
        SET p_success = FALSE;
        SET p_message = 'Error: Borrowing item record not found.';
        LEAVE proc_block;
    END IF;

    IF (v_curr_returned + p_returned_qty) > v_total_qty THEN
        ROLLBACK;
        SET p_success = FALSE;
        SET p_message = CONCAT('Error: Total returned (', v_curr_returned + p_returned_qty, ') cannot exceed borrowed quantity (', v_total_qty, ').');
        LEAVE proc_block;
    END IF;

    -- Update borrowing item (Trigger 5 will restore available inventory)
    UPDATE borrowing_items
    SET returned_quantity = returned_quantity + p_returned_qty,
        condition_after = p_condition_after,
        updated_at = NOW()
    WHERE id = p_borrowing_item_id;

    -- If condition is Damaged, update equipment condition
    IF p_condition_after = 'Damaged' THEN
        UPDATE equipment
        SET `condition` = 'Damaged'
        WHERE id = v_equip_id;
    END IF;

    -- Check if all items for this borrowing transaction are fully returned
    SELECT COUNT(*)
    INTO v_pending_items
    FROM borrowing_items
    WHERE borrowing_transaction_id = v_tx_id
      AND returned_quantity < quantity;

    IF v_pending_items = 0 THEN
        UPDATE borrowing_transactions
        SET status = 'Returned',
            actual_return_date = CURRENT_DATE(),
            updated_at = NOW()
        WHERE id = v_tx_id;
    END IF;

    -- Audit return
    INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, created_at)
    VALUES (
        p_processed_by,
        'RETURN',
        'borrowing_items',
        p_borrowing_item_id,
        JSON_OBJECT('previous_returned', v_curr_returned),
        JSON_OBJECT('returned_now', p_returned_qty, 'condition_after', p_condition_after),
        NOW()
    );

    COMMIT;

    SET p_success = TRUE;
    SET p_message = 'Equipment return processed successfully.';
END$$
DELIMITER ;

-- Procedure 3: Get Overdue Equipment Report
DROP PROCEDURE IF EXISTS sp_get_overdue_equipment;
DELIMITER $$
CREATE PROCEDURE sp_get_overdue_equipment()
BEGIN
    SELECT 
        bt.id AS transaction_id,
        CONCAT(a.first_name, ' ', a.last_name) AS borrower_name,
        a.student_number,
        a.contact_number,
        e.equipment_code,
        e.equipment_name,
        s.sport_name,
        bi.quantity AS borrowed_quantity,
        bi.returned_quantity,
        (bi.quantity - bi.returned_quantity) AS unreturned_quantity,
        bt.borrow_date,
        bt.expected_return_date,
        fn_overdue_days(bt.expected_return_date, bt.actual_return_date) AS overdue_days
    FROM borrowing_transactions bt
    JOIN athletes a ON bt.athlete_id = a.id
    JOIN borrowing_items bi ON bt.id = bi.borrowing_transaction_id
    JOIN equipment e ON bi.equipment_id = e.id
    JOIN sports s ON e.sport_id = s.id
    WHERE bt.status = 'Borrowed'
      AND bt.expected_return_date < CURRENT_DATE()
      AND (bi.quantity - bi.returned_quantity) > 0
    ORDER BY overdue_days DESC;
END$$
DELIMITER ;

-- ------------------------------------------------------------------------------
-- 4. DATABASE VIEWS
-- ------------------------------------------------------------------------------

-- View 1: Equipment Inventory View
DROP VIEW IF EXISTS vw_equipment_inventory;
CREATE VIEW vw_equipment_inventory AS
SELECT 
    e.id AS equipment_id,
    e.equipment_code,
    e.equipment_name,
    s.sport_name,
    e.category,
    e.quantity AS total_quantity,
    e.available_quantity,
    (e.quantity - e.available_quantity) AS borrowed_or_in_use_quantity,
    e.condition,
    e.status,
    fn_equipment_availability(e.id) AS stock_status
FROM equipment e
JOIN sports s ON e.sport_id = s.id;

-- View 2: Current Borrowings View
DROP VIEW IF EXISTS vw_current_borrowings;
CREATE VIEW vw_current_borrowings AS
SELECT 
    bt.id AS transaction_id,
    a.student_number,
    CONCAT(a.first_name, ' ', a.last_name) AS borrower_name,
    a.department,
    a.contact_number,
    e.equipment_code,
    e.equipment_name,
    s.sport_name,
    bi.quantity AS borrowed_quantity,
    bi.returned_quantity,
    bt.borrow_date,
    bt.expected_return_date,
    bt.status,
    fn_overdue_days(bt.expected_return_date, bt.actual_return_date) AS days_overdue
FROM borrowing_transactions bt
JOIN athletes a ON bt.athlete_id = a.id
JOIN borrowing_items bi ON bt.id = bi.borrowing_transaction_id
JOIN equipment e ON bi.equipment_id = e.id
JOIN sports s ON e.sport_id = s.id
WHERE bt.status IN ('Borrowed', 'Pending', 'Approved');

-- View 3: Overdue Borrowings View
DROP VIEW IF EXISTS vw_overdue_borrowings;
CREATE VIEW vw_overdue_borrowings AS
SELECT 
    bt.id AS transaction_id,
    a.student_number,
    CONCAT(a.first_name, ' ', a.last_name) AS borrower_name,
    a.contact_number,
    e.equipment_code,
    e.equipment_name,
    bi.quantity AS borrowed_quantity,
    (bi.quantity - bi.returned_quantity) AS unreturned_quantity,
    bt.borrow_date,
    bt.expected_return_date,
    fn_overdue_days(bt.expected_return_date, NULL) AS overdue_days
FROM borrowing_transactions bt
JOIN athletes a ON bt.athlete_id = a.id
JOIN borrowing_items bi ON bt.id = bi.borrowing_transaction_id
JOIN equipment e ON bi.equipment_id = e.id
WHERE bt.status = 'Borrowed'
  AND bt.expected_return_date < CURRENT_DATE()
  AND (bi.quantity - bi.returned_quantity) > 0;
