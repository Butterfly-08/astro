<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AstrologerWalletController extends Controller
{
    public function index(Request $request)
    {
        $astrologer = Auth::user()->astrologer;
        $wallet = $astrologer->wallet;

        $query = WalletTransaction::where('astrologer_id', $astrologer->id)
            ->latest();

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('astrologer.wallet.index', compact('astrologer', 'wallet', 'transactions'));
    }
}
