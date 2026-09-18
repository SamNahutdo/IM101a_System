<?php

namespace App\Services;

use App\Models\MaintenanceRecord;
use App\Models\DamageReport;
use App\Models\Equipment;
use App\Models\EquipmentStatusHistory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class MaintenanceService
{
    /**
     * Schedule a new maintenance work order.
     */
    public function scheduleMaintenance(
        int $equipmentId,
        ?int $damageReportId,
        string $maintenanceType,
        string $scheduledDate,
        int $assignedStaffId,
        float $cost,
        string $description
    ): MaintenanceRecord {
        return DB::transaction(function () use (
            $equipmentId,
            $damageReportId,
            $maintenanceType,
            $scheduledDate,
            $assignedStaffId,
            $cost,
            $description
        ) {
            $record = MaintenanceRecord::create([
                'equipment_id' => $equipmentId,
                'damage_report_id' => $damageReportId,
                'maintenance_type' => $maintenanceType,
                'scheduled_date' => $scheduledDate,
                'assigned_staff_id' => $assignedStaffId,
                'cost' => $cost,
                'status' => 'Scheduled',
                'description' => $description,
            ]);

            if ($damageReportId) {
                DamageReport::where('id', $damageReportId)->update(['status' => 'Repair Required']);
            }

            AuditService::log('INSERT', 'maintenance_records', $record->id, null, $record->toArray());

            return $record;
        });
    }

    /**
     * Update maintenance work order status.
     */
    public function updateStatus(int $recordId, string $status, ?string $resultNotes = null): MaintenanceRecord
    {
        return DB::transaction(function () use ($recordId, $status, $resultNotes) {
            $record = MaintenanceRecord::with('equipment')->findOrFail($recordId);
            $eq = $record->equipment;
            $oldStatus = $eq->status;

            $updateData = ['status' => $status];
            if ($resultNotes) {
                $updateData['result_notes'] = $resultNotes;
            }

            if ($status === 'In Progress' && is_null($record->start_date)) {
                $updateData['start_date'] = Carbon::now();
                $eq->update(['status' => 'Maintenance']);

                EquipmentStatusHistory::create([
                    'equipment_id' => $eq->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'Maintenance',
                    'changed_by' => auth()->id(),
                    'reason' => "Maintenance in progress: {$record->maintenance_type}",
                ]);
            }

            if ($status === 'Completed') {
                $updateData['completion_date'] = Carbon::now();
                $eq->update(['status' => 'Available', 'current_condition' => 'Good']);

                EquipmentStatusHistory::create([
                    'equipment_id' => $eq->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'Available',
                    'changed_by' => auth()->id(),
                    'reason' => "Maintenance completed: {$record->maintenance_type}",
                ]);

                if ($record->damage_report_id) {
                    DamageReport::where('id', $record->damage_report_id)->update([
                        'status' => 'Resolved',
                        'resolution' => $resultNotes ?? 'Repairs completed via maintenance.',
                        'resolved_by' => auth()->id(),
                        'resolved_at' => Carbon::now(),
                    ]);
                }
            }

            $record->update($updateData);

            AuditService::log('UPDATE', 'maintenance_records', $record->id, null, $record->toArray());

            return $record;
        });
    }
}
