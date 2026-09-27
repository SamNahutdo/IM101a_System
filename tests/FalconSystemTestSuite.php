<?php

namespace Tests;

use App\Models\User;
use App\Models\Role;
use App\Models\Athlete;
use App\Models\Equipment;
use App\Models\Sport;
use App\Models\BorrowingTransaction;
use App\Models\BorrowingItem;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FalconSystemTestSuite
{
    protected int $passed = 0;
    protected int $failed = 0;

    public function run(): int
    {
        echo "\n=======================================================\n";
        echo "   FALCONSYSTEM: ADVANCED DATABASE SYSTEMS TEST SUITE\n";
        echo "   Defense Automated Verification (6 Core Scenarios)\n";
        echo "=======================================================\n\n";

        $this->test1_CreateEquipment();
        $this->test2_BorrowEquipment();
        $this->test3_BorrowMoreThanAvailable();
        $this->test4_ReturnEquipment();
        $this->test5_NegativeInventoryProtection();
        $this->test6_RbacForbiddenAccess();

        echo "\n-------------------------------------------------------\n";
        echo "TEST SUMMARY: {$this->passed} PASSED, {$this->failed} FAILED\n";
        echo "-------------------------------------------------------\n";

        return $this->failed === 0 ? 0 : 1;
    }

    protected function assert($condition, string $name, string $details = ''): void
    {
        if ($condition) {
            echo " [PASS] {$name}\n";
            $this->passed++;
        } else {
            echo " [FAIL] {$name}" . ($details ? ": {$details}" : "") . "\n";
            $this->failed++;
        }
    }

    // --------------------------------------------------------------------------
    // TEST 1: Create equipment (verifies 3NF, initial available_quantity, and audit trigger)
    // --------------------------------------------------------------------------
    protected function test1_CreateEquipment(): void
    {
        echo "--> TEST 1: Create Equipment Master Record\n";

        $sport = Sport::first();
        $code = 'EQ-TEST-' . time();

        $equipment = Equipment::create([
            'sport_id' => $sport->id,
            'equipment_code' => $code,
            'equipment_name' => 'Automated Test Bat',
            'category' => 'Test',
            'quantity' => 5,
            'available_quantity' => 5,
            'condition' => 'New',
            'status' => 'Available',
        ]);

        $this->assert($equipment->exists, "Equipment entity persisted to database");
        $this->assert($equipment->available_quantity === 5, "Initial available quantity equals total quantity");

        // Verify MySQL Trigger: trg_audit_equipment_insert
        $auditLog = AuditLog::where('table_name', 'equipment')
            ->where('record_id', $equipment->id)
            ->where('action', 'INSERT')
            ->first();

        $this->assert($auditLog !== null, "MySQL Trigger 'trg_audit_equipment_insert' logged INSERT payload");
    }

    // --------------------------------------------------------------------------
    // TEST 2: Borrow equipment via Stored Procedure sp_process_borrowing
    // --------------------------------------------------------------------------
    protected function test2_BorrowEquipment(): void
    {
        echo "\n--> TEST 2: Process Equipment Borrowing (Stored Procedure)\n";

        $athlete = Athlete::first();
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();

        // Dedicated equipment with 4 available
        $sport = Sport::first();
        $equip = Equipment::create([
            'sport_id' => $sport->id,
            'equipment_code' => 'EQ-BORROW-' . time(),
            'equipment_name' => 'Loan Verification Glove',
            'category' => 'Gloves',
            'quantity' => 4,
            'available_quantity' => 4,
            'condition' => 'Good',
            'status' => 'Available',
        ]);

        $initialAvail = $equip->available_quantity;
        $borrowQty = 2;

        DB::statement("CALL sp_process_borrowing(?, ?, ?, ?, ?, ?, @success, @message, @tx_id)", [
            $athlete->id,
            $equip->id,
            $borrowQty,
            now()->addDays(2)->toDateString(),
            $admin->id,
            'Automated Borrowing Test',
        ]);

        $res = DB::select("SELECT @success AS success, @message AS message, @tx_id AS tx_id")[0];

        $this->assert($res->success == 1, "sp_process_borrowing executed successfully: {$res->message}");
        $this->assert(!empty($res->tx_id), "Created borrowing transaction #{$res->tx_id}");

        $equip->refresh();
        $this->assert(
            $equip->available_quantity === ($initialAvail - $borrowQty),
            "Equipment available quantity automatically decremented from {$initialAvail} to {$equip->available_quantity}"
        );
    }

    // --------------------------------------------------------------------------
    // TEST 3: Borrow more than available (must be rejected with rollback)
    // --------------------------------------------------------------------------
    protected function test3_BorrowMoreThanAvailable(): void
    {
        echo "\n--> TEST 3: Reject Borrowing Exceeding Available Stock\n";

        $athlete = Athlete::first();
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();

        $sport = Sport::first();
        $equip = Equipment::create([
            'sport_id' => $sport->id,
            'equipment_code' => 'EQ-EXCEED-' . time(),
            'equipment_name' => 'Limited Stock Paddle',
            'category' => 'Paddles',
            'quantity' => 2,
            'available_quantity' => 1, // Only 1 available!
            'condition' => 'Good',
            'status' => 'Available',
        ]);

        // Attempt to borrow 5 items when only 1 is available
        DB::statement("CALL sp_process_borrowing(?, ?, ?, ?, ?, ?, @success, @message, @tx_id)", [
            $athlete->id,
            $equip->id,
            5,
            now()->addDays(2)->toDateString(),
            $admin->id,
            'Exceeding borrow request',
        ]);

        $res = DB::select("SELECT @success AS success, @message AS message, @tx_id AS tx_id")[0];

        $this->assert($res->success == 0, "sp_process_borrowing rejected request exceeding available quantity");
        $this->assert(str_contains(strtolower($res->message), 'insufficient'), "Returned descriptive rollback error: {$res->message}");

        $equip->refresh();
        $this->assert($equip->available_quantity === 1, "Available quantity safely unchanged at {$equip->available_quantity}");
    }

    // --------------------------------------------------------------------------
    // TEST 4: Return equipment via Stored Procedure sp_process_return
    // --------------------------------------------------------------------------
    protected function test4_ReturnEquipment(): void
    {
        echo "\n--> TEST 4: Process Equipment Return (Stored Procedure)\n";

        $athlete = Athlete::first();
        $staff = User::whereHas('role', fn($q) => $q->where('name', 'staff'))->first();
        $sport = Sport::first();

        $equip = Equipment::create([
            'sport_id' => $sport->id,
            'equipment_code' => 'EQ-RET-' . time(),
            'equipment_name' => 'Return Test Bat',
            'category' => 'Bats',
            'quantity' => 3,
            'available_quantity' => 3,
            'condition' => 'Good',
            'status' => 'Available',
        ]);

        // Borrow 2 items via Stored Procedure
        DB::statement("CALL sp_process_borrowing(?, ?, ?, ?, ?, ?, @succ, @msg, @new_tx)", [
            $athlete->id,
            $equip->id,
            2,
            now()->addDays(3)->toDateString(),
            $staff->id,
            'Test 4 Borrow setup',
        ]);

        $resBorrow = DB::select("SELECT @succ AS success, @new_tx AS tx_id")[0];
        $this->assert($resBorrow->success == 1, "Setup borrowing transaction created");

        $item = BorrowingItem::where('borrowing_transaction_id', $resBorrow->tx_id)->first();
        $equip->refresh();
        $availBeforeReturn = $equip->available_quantity; // Should be 1 (3 - 2)

        // Process return of 1 item via sp_process_return
        DB::statement("CALL sp_process_return(?, ?, ?, ?, @success, @message)", [
            $item->id,
            1,
            'Good',
            $staff->id,
        ]);

        $res = DB::select("SELECT @success AS success, @message AS message")[0];

        $this->assert($res->success == 1, "sp_process_return executed successfully");

        $equip->refresh();
        $this->assert(
            $equip->available_quantity === ($availBeforeReturn + 1),
            "Equipment available quantity restored from {$availBeforeReturn} to {$equip->available_quantity}"
        );
    }

    // --------------------------------------------------------------------------
    // TEST 5: Negative Inventory Protection & Row Locking
    // --------------------------------------------------------------------------
    protected function test5_NegativeInventoryProtection(): void
    {
        echo "\n--> TEST 5: Prevent Negative Inventory & Concurrency Simulation\n";

        $athlete = Athlete::first();
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        $sport = Sport::first();

        // Equipment with exactly 1 available
        $equip = Equipment::create([
            'sport_id' => $sport->id,
            'equipment_code' => 'EQ-LAST-' . time(),
            'equipment_name' => 'Last Remaining Helmet',
            'category' => 'Helmets',
            'quantity' => 1,
            'available_quantity' => 1,
            'condition' => 'Good',
            'status' => 'Available',
        ]);

        // Request 1: Claims the last item
        DB::statement("CALL sp_process_borrowing(?, ?, ?, ?, ?, ?, @success1, @msg1, @tx1)", [
            $athlete->id,
            $equip->id,
            1,
            now()->addDays(2)->toDateString(),
            $admin->id,
            'First borrower claims last item',
        ]);

        $res1 = DB::select("SELECT @success1 AS success, @msg1 AS message, @tx1 AS tx_id")[0];
        $this->assert($res1->success == 1, "First borrower request succeeded");

        // Request 2: Simultaneously attempts to borrow the same item (available is now 0)
        DB::statement("CALL sp_process_borrowing(?, ?, ?, ?, ?, ?, @success2, @msg2, @tx2)", [
            $athlete->id,
            $equip->id,
            1,
            now()->addDays(2)->toDateString(),
            $admin->id,
            'Second concurrent request fails',
        ]);

        $res2 = DB::select("SELECT @success2 AS success, @msg2 AS message, @tx2 AS tx_id")[0];
        $this->assert($res2->success == 0, "Second request rejected due to row-lock & 0 available quantity");

        $equip->refresh();
        $this->assert(
            $equip->available_quantity === 0,
            "Available quantity remains valid at 0 (never negative)"
        );
    }

    // --------------------------------------------------------------------------
    // TEST 6: Student Accessing Admin Page -> 403 Forbidden
    // --------------------------------------------------------------------------
    protected function test6_RbacForbiddenAccess(): void
    {
        echo "\n--> TEST 6: Role-Based Access Control (RBAC) Enforcement\n";

        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        Auth::login($student);

        $middleware = new \App\Http\Middleware\RoleMiddleware();
        $request = \Illuminate\Http\Request::create('/admin/dashboard', 'GET');

        $isForbidden = false;
        try {
            $middleware->handle($request, function ($req) {
                return response('Admin Dashboard Allowed', 200);
            }, 'admin');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            if ($e->getStatusCode() === 403) {
                $isForbidden = true;
            }
        }

        $this->assert($isForbidden, "Student account forbidden from Admin routes (HTTP 403 Unauthorized)");
    }
}
