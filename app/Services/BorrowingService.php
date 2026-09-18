<?php

namespace App\Services;

use App\Models\BorrowingTransaction;
use App\Models\BorrowingItem;
use App\Models\Equipment;
use App\Models\EquipmentStatusHistory;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class BorrowingService
{
    /**
     * Process checkout of equipment units inside a strict atomic transaction.
     */
    public function processCheckout(
        int $borrowerId,
        int $staffId,
        ?int $teamId,
        ?int $reservationId,
        string $expectedReturnTime,
        array $equipmentItems,
        ?string $notes = null
    ): BorrowingTransaction {
        return DB::transaction(function () use (
            $borrowerId,
            $staffId,
            $teamId,
            $reservationId,
            $expectedReturnTime,
            $equipmentItems,
            $notes
        ) {
            // 1. Verify equipment availability
            foreach ($equipmentItems as $item) {
                $eq = Equipment::lockForUpdate()->findOrFail($item['equipment_id']);
                if ($eq->status !== 'Available' && $eq->status !== 'Reserved') {
                    throw new Exception("Equipment {$eq->name} ({$eq->asset_code}) is currently '{$eq->status}' and cannot be checked out.");
                }
            }

            // 2. Generate unique transaction code
            $code = 'BOR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $transaction = BorrowingTransaction::create([
                'transaction_code' => $code,
                'reservation_id' => $reservationId,
                'borrower_id' => $borrowerId,
                'team_id' => $teamId,
                'processed_by' => $staffId,
                'checkout_time' => Carbon::now(),
                'expected_return_time' => Carbon::parse($expectedReturnTime),
                'status' => 'Active',
                'notes' => $notes,
            ]);

            // 3. Attach borrowing items and update equipment status
            foreach ($equipmentItems as $item) {
                $eq = Equipment::find($item['equipment_id']);

                BorrowingItem::create([
                    'transaction_id' => $transaction->id,
                    'equipment_id' => $eq->id,
                    'checkout_condition' => $item['checkout_condition'] ?? $eq->current_condition,
                    'status' => 'Borrowed',
                    'remarks' => $item['remarks'] ?? null,
                ]);

                $oldStatus = $eq->status;
                $eq->update(['status' => 'Borrowed']);

                EquipmentStatusHistory::create([
                    'equipment_id' => $eq->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'Borrowed',
                    'changed_by' => $staffId,
                    'reason' => "Checked out under transaction {$code}",
                ]);

                AuditService::log('UPDATE', 'equipment', $eq->id, ['status' => $oldStatus], ['status' => 'Borrowed']);
            }

            // 4. Update reservation status if linked
            if ($reservationId) {
                $res = Reservation::find($reservationId);
                if ($res) {
                    $res->update(['status' => 'Completed']);
                }
            }

            AuditService::log('INSERT', 'borrowing_transactions', $transaction->id, null, $transaction->toArray());

            return $transaction;
        });
    }

    /**
     * Process return and inspection of an equipment item.
     */
    public function processReturn(
        int $borrowingItemId,
        int $staffId,
        string $returnCondition,
        ?string $remarks = null
    ): BorrowingItem {
        return DB::transaction(function () use ($borrowingItemId, $staffId, $returnCondition, $remarks) {
            $item = BorrowingItem::lockForUpdate()->with(['equipment', 'transaction'])->findOrFail($borrowingItemId);

            if ($item->status === 'Returned') {
                throw new Exception("This equipment item has already been marked as returned.");
            }

            $item->update([
                'return_condition' => $returnCondition,
                'return_inspected_by' => $staffId,
                'returned_at' => Carbon::now(),
                'status' => 'Returned',
                'remarks' => $remarks,
            ]);

            $eq = $item->equipment;
            $oldStatus = $eq->status;

            // Determine next status based on return condition
            $newStatus = in_array($returnCondition, ['Fair', 'Poor']) ? 'Maintenance' : 'Available';

            $eq->update([
                'status' => $newStatus,
                'current_condition' => $returnCondition,
            ]);

            EquipmentStatusHistory::create([
                'equipment_id' => $eq->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => $staffId,
                'reason' => "Returned from borrowing {$item->transaction->transaction_code} with condition '{$returnCondition}'",
            ]);

            // If all items in this transaction are returned, complete transaction
            $remaining = BorrowingItem::where('transaction_id', $item->transaction_id)
                                     ->where('status', 'Borrowed')
                                     ->count();

            if ($remaining === 0) {
                $item->transaction->update([
                    'status' => 'Completed',
                    'actual_return_time' => Carbon::now(),
                ]);
            }

            AuditService::log('UPDATE', 'borrowing_items', $item->id, null, $item->toArray());

            return $item;
        });
    }
}
