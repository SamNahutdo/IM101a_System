<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\User;
use App\Models\Team;
use App\Models\Reservation;
use App\Models\BorrowingTransaction;
use App\Services\BorrowingService;
use Illuminate\Http\Request;
use Exception;

class CheckoutController extends Controller
{
    public function index()
    {
        $transactions = BorrowingTransaction::with(['borrower', 'staff', 'items.equipment', 'team'])
                                            ->latest('checkout_time')
                                            ->paginate(15);

        return view('staff.checkout.index', compact('transactions'));
    }

    public function create(Request $request)
    {
        $borrowers = User::where('is_active', true)
                         ->whereHas('roles', fn ($q) => $q->whereIn('name', ['athlete', 'coach']))
                         ->orderBy('first_name')
                         ->get();

        $availableEquipment = Equipment::where('status', 'Available')->orderBy('name')->get();
        $teams = Team::where('status', 'Active')->get();

        $selectedReservation = null;
        if ($request->filled('reservation_id')) {
            $selectedReservation = Reservation::with(['items.equipment', 'requester', 'team'])
                                              ->where('status', 'Approved')
                                              ->find($request->reservation_id);
        }

        return view('staff.checkout.create', compact('borrowers', 'availableEquipment', 'teams', 'selectedReservation'));
    }

    public function store(Request $request, BorrowingService $borrowingService)
    {
        $validated = $request->validate([
            'borrower_id' => ['required', 'exists:users,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'reservation_id' => ['nullable', 'exists:reservations,id'],
            'expected_return_time' => ['required', 'date', 'after:now'],
            'equipment_ids' => ['required', 'array', 'min:1'],
            'equipment_ids.*' => ['exists:equipment,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $equipmentItems = array_map(function ($id) {
                return ['equipment_id' => $id];
            }, $validated['equipment_ids']);

            $transaction = $borrowingService->processCheckout(
                borrowerId: $validated['borrower_id'],
                staffId: auth()->id(),
                teamId: $validated['team_id'] ?? null,
                reservationId: $validated['reservation_id'] ?? null,
                expectedReturnTime: $validated['expected_return_time'],
                equipmentItems: $equipmentItems,
                notes: $validated['notes'] ?? null
            );

            return redirect()->route('staff.checkout.index')
                             ->with('success', "Checkout processed successfully! Transaction Code: {$transaction->transaction_code}");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
