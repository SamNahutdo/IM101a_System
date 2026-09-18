<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\BorrowingTransaction;
use App\Models\Reservation;

class MyLoanController extends Controller
{
    public function loans()
    {
        $loans = BorrowingTransaction::with(['items.equipment', 'team', 'staff'])
                                     ->where('borrower_id', auth()->id())
                                     ->latest('checkout_time')
                                     ->paginate(15);

        return view('athlete.loans.index', compact('loans'));
    }

    public function requests()
    {
        $requests = Reservation::with(['items.equipment', 'reviewer'])
                               ->where('requester_id', auth()->id())
                               ->latest()
                               ->paginate(15);

        return view('athlete.requests.index', compact('requests'));
    }
}
