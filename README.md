# FalconSystem: Athletic Equipment Services & Resource Management System

FalconSystem is an enterprise-grade full-stack athletic equipment services and resource management web application developed for the **3rd-Year IT Advanced Database Systems (IM101)** course curriculum.

The application demonstrates strict relational database engineering, Third Normal Form (3NF) normalization, server-side automation (MySQL Stored Procedures, Functions, and Triggers), database audit logging, ACID transactions, and Role-Based Access Control (RBAC) built on Laravel MVC with pure Vanilla CSS, HTML5, and Vanilla JavaScript.

---

## 1. System Features & Highlights

* **Third Normal Form (3NF)**: 18 normalized relational tables eliminating repeating groups, partial key dependencies, and transitive functional dependencies.
* **Meaningful Many-to-Many Relationships**:
  1. `teams` $\leftrightarrow$ `athlete_profiles` (via `team_members` junction table with intersection attributes `jersey_number`, `position`, `membership_status`, `joined_at`)
  2. `reservations` $\leftrightarrow$ `equipment` (via `reservation_items` junction table with `requested_quantity`, `approved_quantity`, `item_status`)
  3. `borrowing_transactions` $\leftrightarrow$ `equipment` (via `borrowing_items` junction table with `checkout_condition`, `return_condition`, `return_inspected_by`, `status`)
* **Server-Side Automation (MySQL 8+ / MariaDB)**:
  * **Stored Procedure `sp_process_checkout`**: Atomic multi-item checkout, validation, status synchronization, and transaction rollback handling.
  * **Stored Procedure `sp_process_return`**: Atomic return inspection, condition recording, and equipment status updates.
  * **Stored Function `fn_is_equipment_available`**: Deterministic reservation conflict & availability evaluator.
  * **Database Trigger `trg_damage_severe_equipment_status`**: Automatically flags equipment as `Damaged` upon insertion of severe damage reports.
  * **Database Trigger `trg_equipment_status_audit`**: Records before/after JSON audit payloads directly from the database engine.
* **Role-Based Access Control (RBAC)**:
  * 4 Distinct Portals: **System Administrator**, **Equipment Staff**, **Coach**, and **Student Athlete**.
  * Server-side route authorization enforced via `RoleMiddleware` and Policy gates.
* **Defense-Ready Analytics**: Real-time analytical reports on inventory distribution, equipment utilization, overdue loans, and maintenance costs.

---

## 2. Course Defense Credentials

Default development password for all seed accounts: `password123`

| Role | Username | Purpose & Clearance |
| :--- | :--- | :--- |
| **System Administrator** | `admin` | Full system oversight, user account management, equipment master, audit logs, and reports. |
| **Equipment Staff** | `staff1` | Operational checkout dispatch, return inspection, reservation reviews, and maintenance work orders. |
| **Coach** | `coach_carter` | Team roster management, team equipment reservations, athlete assignments, and condition tracking. |
| **Student Athlete** | `athlete1` | Catalog browsing, individual borrowing requests, personal loan tracking, and damage reporting. |

---

## 3. Normalized Database Schema (3NF)

```text
1.  roles                    (id, name, display_name, description)
2.  users                    (id, username, email, password, first_name, last_name, phone, is_active)
3.  user_roles               (id, user_id, role_id, is_primary, assigned_at)
4.  athlete_profiles         (id, user_id, student_id, emergency_contact_name, emergency_contact_phone, medical_clearance_status, year_level)
5.  coach_profiles           (id, user_id, employee_id, specialization, department, license_number)
6.  teams                    (id, name, sport, coach_id, gender_category, status)
7.  team_members             (id, team_id, athlete_id, jersey_number, position, membership_status, joined_at, left_at) [N:M]
8.  equipment_categories     (id, name, code, description)
9.  equipment_locations      (id, building, room, shelf_bin, description)
10. equipment                (id, asset_code, name, category_id, location_id, serial_number, brand, model, purchase_date, purchase_cost, current_condition, status)
11. equipment_status_history (id, equipment_id, old_status, new_status, changed_by, reason, created_at)
12. reservations             (id, reservation_code, requester_id, team_id, start_time, end_time, purpose, status, reviewed_by, reviewed_at, review_notes)
13. reservation_items        (id, reservation_id, equipment_id, requested_quantity, approved_quantity, item_status, notes) [N:M]
14. borrowing_transactions   (id, transaction_code, reservation_id, borrower_id, team_id, processed_by, checkout_time, expected_return_time, actual_return_time, status, notes)
15. borrowing_items          (id, transaction_id, equipment_id, checkout_condition, return_condition, return_inspected_by, returned_at, status, remarks) [N:M]
16. equipment_assignments    (id, equipment_id, assignable_type, athlete_id, team_id, assigned_by, assigned_date, expected_end_date, actual_return_date, assignment_status, condition_on_assignment, condition_on_return)
17. damage_reports           (id, report_code, equipment_id, borrowing_item_id, reported_by, damage_type, severity, description, reported_at, status, resolution, resolved_by, resolved_at)
18. maintenance_records      (id, equipment_id, damage_report_id, maintenance_type, scheduled_date, start_date, completion_date, assigned_staff_id, cost, status, description, result_notes)
19. audit_logs               (id, user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent, created_at)
```

---

## 4. Local Installation & Development Guide

### Prerequisites
* PHP 8.2 or higher (with `pdo_mysql`, `mbstring`, `openssl`, `curl`, and `zip` enabled)
* MySQL 8.0+ or MariaDB 10.4+ (e.g. through XAMPP)
* Composer 2.x

### Setup Steps
1. **Clone repository**:
   ```bash
   git clone https://github.com/SamNahutdo/IM101a_System.git
   cd IM101a_System
   ```
2. **Install Composer dependencies**:
   ```bash
   composer install --no-security-blocking
   ```
3. **Configure Environment (`.env`)**:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3308
   DB_DATABASE=falcon_system
   DB_USERNAME=falcon_app
   DB_PASSWORD=FalconPass123!
   ```
4. **Execute Migrations & Seed Data**:
   ```bash
   php artisan migrate:fresh --seed
   ```
5. **Install Stored Procedures, Functions, and Triggers**:
   ```bash
   mysql -u falcon_app -pFalconPass123! -P 3308 falcon_system < database/sql/falcon_procedures_triggers.sql
   ```
6. **Start Local Development Server**:
   ```bash
   php artisan serve
   ```
   Access the system at `http://localhost:8000`.

---

## 5. Defense Flow Reference

```text
User Action
  ↓
Browser (HTML5 / Vanilla CSS3 / Blade)
  ↓
Laravel Route (routes/web.php)
  ↓
Middleware (RoleMiddleware — authenticates & verifies RBAC clearance)
  ↓
Controller (app/Http/Controllers/*)
  ↓
Service Layer (BorrowingService / ReservationService / MaintenanceService)
  ↓
Eloquent ORM / DB Transaction (Atomic commit/rollback)
  ↓
MySQL Database (falcon_system)
  ↓
Stored Procedure (sp_process_checkout / sp_process_return)
  ↓
Database Trigger (trg_damage_severe_equipment_status / trg_equipment_status_audit)
  ↓
Audit Log / Status History (audit_logs / equipment_status_history)
```
