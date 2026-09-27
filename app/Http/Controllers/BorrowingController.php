<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\BorrowingTransaction;
use App\Models\BorrowingItem;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = BorrowingTransaction::with(['athlete.user', 'items.equipment', 'approvedByUser']);

        // Role-based scoping
        if ($user->isStudent()) {
            if ($user->athlete) {
                $query->where('athlete_id', $user->athlete->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('athlete', function ($q) use ($s) {
                $q->where('student_number', 'like', "%{$s}%")
                  ->orWhere('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%");
            });
        }

        $transactions = $query->latest('borrow_date')->latest('id')->paginate(15);

        return view('borrowing.index', compact('transactions'));
    }

    public function create()
    {
        $user = Auth::user();
        $athletes = Athlete::where('status', 'Active')->orderBy('last_name')->get();
        $equipment = Equipment::available()->with('sport')->orderBy('equipment_name')->get();

        return view('borrowing.create', compact('athletes', 'equipment', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // If student, athlete_id is fixed to logged in student
        if ($user->isStudent()) {
            if (!$user->athlete) {
                return back()->with('error', 'No student athlete profile found for your account.');
            }
            $athleteId = $user->athlete->id;
            $approvedBy = null; // Pending admin/staff approval
        } else {
            $request->validate([
                'athlete_id' => ['required', 'exists:athletes,id'],
            ]);
            $athleteId = $request->athlete_id;
            $approvedBy = $user->id;
        }

        $validated = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'expected_return_date' => ['required', 'date', 'after_or_equal:today'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        // Student direct request creates a pending loan for review
        if ($user->isStudent()) {
            // Verify stock
            $equip = Equipment::findOrFail($validated['equipment_id']);
            if ($equip->available_quantity < $validated['quantity']) {
                return back()->with('error', "Insufficient inventory: Only {$equip->available_quantity} available, but {$validated['quantity']} requested.");
            }

            $tx = BorrowingTransaction::create([
                'athlete_id' => $athleteId,
                'approved_by' => null,
                'borrow_date' => now()->toDateString(),
                'expected_return_date' => $validated['expected_return_date'],
                'status' => 'Pending',
                'remarks' => $validated['remarks'] ?? 'Requested online by student athlete',
            ]);

            BorrowingItem::create([
                'borrowing_transaction_id' => $tx->id,
                'equipment_id' => $validated['equipment_id'],
                'quantity' => $validated['quantity'],
                'condition_before' => $equip->condition,
                'returned_quantity' => 0,
            ]);

            return redirect()->route('student.borrowing.index')
                ->with('success', 'Borrowing request submitted successfully. Awaiting Equipment Custodian approval.');
        }

        // =====================================================================
        // EXECUTE MYSQL STORED PROCEDURE: sp_process_borrowing
        // Demonstrates row locking, transaction boundaries, rollback & audit!
        // =====================================================================
        try {
            DB::statement("CALL sp_process_borrowing(?, ?, ?, ?, ?, ?, @success, @message, @tx_id)", [
                $athleteId,
                $validated['equipment_id'],
                $validated['quantity'],
                $validated['expected_return_date'],
                $approvedBy,
                $validated['remarks'] ?? 'Equipment checkout processed',
            ]);

            $res = DB::select("SELECT @success AS success, @message AS message, @tx_id AS tx_id")[0];

            if (!$res->success) {
                return back()->with('error', 'Stored Procedure Error: ' . $res->message)->withInput();
            }

            return redirect()->route($user->role->name . '.borrowing.index')
                ->with('success', "MySQL Stored Procedure 'sp_process_borrowing' executed successfully: " . $res->message);

        } catch (\Throwable $e) {
            return back()->with('error', 'Database Error: ' . $e->getMessage())->withInput();
        }
    }

    public function processReturn(Request $request, BorrowingItem $item)
    {
        $validated = $request->validate([
            'returned_quantity' => ['required', 'integer', 'min:1'],
            'condition_after' => ['required', 'in:New,Good,Fair,Damaged'],
        ]);

        $user = Auth::user();

        // =====================================================================
        // EXECUTE MYSQL STORED PROCEDURE: sp_process_return
        // Atomically updates item, increases inventory, updates tx status!
        // =====================================================================
        try {
            DB::statement("CALL sp_process_return(?, ?, ?, ?, @success, @message)", [
                $item->id,
                $validated['returned_quantity'],
                $validated['condition_after'],
                $user->id,
            ]);

            $res = DB::select("SELECT @success AS success, @message AS message")[0];

            if (!$res->success) {
                return back()->with('error', 'Stored Procedure Error: ' . $res->message);
            }

            return back()->with('success', "MySQL Stored Procedure 'sp_process_return' executed: " . $res->message);

        } catch (\Throwable $e) {
            return back()->with('error', 'Database Return Error: ' . $e->getMessage());
        }
    }

    public function approve(BorrowingTransaction $transaction)
    {
        if ($transaction->status !== 'Pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $user = Auth::user();
        $transaction->update([
            'approved_by' => $user->id,
            'status' => 'Borrowed',
        ]);

        return back()->with('success', 'Borrowing request approved successfully.');
    }
}
