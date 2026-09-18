<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\Equipment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class ReservationService
{
    /**
     * Check if an equipment unit has overlapping approved reservations.
     */
    public function hasConflict(int $equipmentId, string $startTime, string $endTime, ?int $excludeReservationId = null): bool
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        $query = ReservationItem::where('equipment_id', $equipmentId)
            ->where('item_status', 'Approved')
            ->whereHas('reservation', function ($q) use ($start, $end, $excludeReservationId) {
                $q->where('status', 'Approved')
                  ->where(function ($sub) use ($start, $end) {
                      $sub->whereBetween('start_time', [$start, $end])
                          ->orWhereBetween('end_time', [$start, $end])
                          ->orWhere(function ($nested) use ($start, $end) {
                              $nested->where('start_time', '<=', $start)
                                     ->where('end_time', '>=', $end);
                          });
                  });

                if ($excludeReservationId) {
                    $q->where('id', '!=', $excludeReservationId);
                }
            });

        return $query->exists();
    }

    /**
     * Create a reservation with items atomically.
     */
    public function createReservation(
        int $requesterId,
        ?int $teamId,
        string $startTime,
        string $endTime,
        string $purpose,
        array $equipmentIds
    ): Reservation {
        return DB::transaction(function () use ($requesterId, $teamId, $startTime, $endTime, $purpose, $equipmentIds) {
            $start = Carbon::parse($startTime);
            $end = Carbon::parse($endTime);

            if ($start->greaterThanOrEqualTo($end)) {
                throw new Exception("Reservation start time must precede end time.");
            }

            foreach ($equipmentIds as $eqId) {
                if ($this->hasConflict($eqId, $startTime, $endTime)) {
                    $eq = Equipment::find($eqId);
                    throw new Exception("Equipment '{$eq->name}' is already reserved for the selected timeframe.");
                }
            }

            $code = 'RES-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $reservation = Reservation::create([
                'reservation_code' => $code,
                'requester_id' => $requesterId,
                'team_id' => $teamId,
                'start_time' => $start,
                'end_time' => $end,
                'purpose' => $purpose,
                'status' => 'Pending',
            ]);

            foreach ($equipmentIds as $eqId) {
                ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'equipment_id' => $eqId,
                    'requested_quantity' => 1,
                    'approved_quantity' => 0,
                    'item_status' => 'Pending',
                ]);
            }

            AuditService::log('INSERT', 'reservations', $reservation->id, null, $reservation->toArray());

            return $reservation;
        });
    }

    /**
     * Staff reviews reservation (Approval or Rejection).
     */
    public function reviewReservation(int $reservationId, int $reviewerId, string $decision, ?string $notes = null): Reservation
    {
        return DB::transaction(function () use ($reservationId, $reviewerId, $decision, $notes) {
            $reservation = Reservation::with('items')->findOrFail($reservationId);

            if ($reservation->status !== 'Pending') {
                throw new Exception("Only pending reservations can be reviewed.");
            }

            $status = ($decision === 'Approve') ? 'Approved' : 'Rejected';

            $reservation->update([
                'status' => $status,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => Carbon::now(),
                'review_notes' => $notes,
            ]);

            foreach ($reservation->items as $item) {
                $item->update([
                    'item_status' => $status,
                    'approved_quantity' => ($status === 'Approved') ? $item->requested_quantity : 0,
                ]);

                if ($status === 'Approved') {
                    $eq = Equipment::find($item->equipment_id);
                    if ($eq && $eq->status === 'Available') {
                        $eq->update(['status' => 'Reserved']);
                    }
                }
            }

            AuditService::log('UPDATE', 'reservations', $reservation->id, ['status' => 'Pending'], ['status' => $status]);

            return $reservation;
        });
    }
}
