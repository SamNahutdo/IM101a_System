<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Exception;

class ReservationReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['requester', 'team', 'items.equipment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservations = $query->latest()->paginate(15);

        return view('staff.reservations.index', compact('reservations'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['requester', 'team', 'items.equipment', 'reviewer']);
        return view('staff.reservations.show', compact('reservation'));
    }

    public function review(Request $request, Reservation $reservation, ReservationService $reservationService)
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:Approve,Reject'],
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $reviewed = $reservationService->reviewReservation(
                reservationId: $reservation->id,
                reviewerId: auth()->id(),
                decision: $validated['decision'],
                notes: $validated['review_notes'] ?? null
            );

            $action = $reviewed->status === 'Approved' ? 'approved' : 'rejected';
            return redirect()->route('staff.reservations.index')
                             ->with('success', "Reservation {$reviewed->reservation_code} has been {$action}.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
