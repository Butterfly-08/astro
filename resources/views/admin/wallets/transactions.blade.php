@extends('layouts.admin')

@section('title', 'Wallet Ledger Transactions')
@section('page_title', 'Global Wallet Ledger')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.wallets.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Wallets
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <h5 class="fw-bold mb-0"><i class="bi bi-journal-text text-warning me-2"></i>Complete Double-Entry Wallet Ledger</h5>
        <p class="text-muted small mb-0">Immutable financial audit trail of every balance movement across all astrologer wallets</p>
    </div>

    <div class="card-body p-4 pt-2">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.wallets.transactions') }}" class="row g-2 align-items-center mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Reference key / Description / Astrologer" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="commission_credit" {{ request('type') === 'commission_credit' ? 'selected' : '' }}>Commission Credit</option>
                    <option value="commission_reversal" {{ request('type') === 'commission_reversal' ? 'selected' : '' }}>Commission Reversal</option>
                    <option value="withdrawal_hold" {{ request('type') === 'withdrawal_hold' ? 'selected' : '' }}>Withdrawal Hold</option>
                    <option value="withdrawal_release" {{ request('type') === 'withdrawal_release' ? 'selected' : '' }}>Withdrawal Release</option>
                    <option value="withdrawal_paid" {{ request('type') === 'withdrawal_paid' ? 'selected' : '' }}>Withdrawal Paid</option>
                    <option value="bonus_credit" {{ request('type') === 'bonus_credit' ? 'selected' : '' }}>Bonus Credit</option>
                    <option value="manual_adjustment" {{ request('type') === 'manual_adjustment' ? 'selected' : '' }}>Manual Adjustment</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="astrologer_id" class="form-select form-select-sm">
                    <option value="">All Astrologers</option>
                    @foreach($astrologers as $astro)
                        <option value="{{ $astro->id }}" {{ request('astrologer_id') == $astro->id ? 'selected' : '' }}>
                            {{ $astro->display_name }} ({{ $astro->referral_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm fw-bold w-50">Filter</button>
                <a href="{{ route('admin.wallets.transactions') }}" class="btn btn-outline-secondary btn-sm w-50">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase">Reference Key</th>
                        <th class="text-uppercase">Astrologer</th>
                        <th class="text-uppercase">Type</th>
                        <th class="text-uppercase">Description</th>
                        <th class="text-uppercase text-end">Amount</th>
                        <th class="text-uppercase text-end">Balance Before</th>
                        <th class="text-uppercase text-end">Balance After</th>
                        <th class="text-uppercase">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                    <tr>
                        <td><code class="text-dark">{{ $txn->reference_key }}</code></td>
                        <td>
                            @if($txn->astrologer)
                                <div>
                                    <strong class="d-block">{{ $txn->astrologer->display_name }}</strong>
                                    <span class="text-warning font-monospace" style="font-size:0.72rem;">{{ $txn->astrologer->referral_code }}</span>
                                </div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $typeColors = [
                                    'commission_credit' => 'bg-success bg-opacity-10 text-success',
                                    'commission_reversal' => 'bg-danger bg-opacity-10 text-danger',
                                    'withdrawal_hold' => 'bg-warning bg-opacity-10 text-warning',
                                    'withdrawal_release' => 'bg-info bg-opacity-10 text-info',
                                    'withdrawal_paid' => 'bg-primary bg-opacity-10 text-primary',
                                    'bonus_credit' => 'bg-success bg-opacity-10 text-success',
                                    'manual_adjustment' => 'bg-secondary bg-opacity-10 text-secondary',
                                ];
                                $tc = $typeColors[$txn->type] ?? 'bg-light text-dark';
                            @endphp
                            <span class="badge {{ $tc }} px-2 py-1">{{ str_replace('_', ' ', ucwords($txn->type, '_')) }}</span>
                        </td>
                        <td class="text-muted">{{ $txn->description }}</td>
                        <td class="text-end fw-bold">
                            @if(in_array($txn->type, ['commission_credit', 'bonus_credit', 'withdrawal_release']))
                                <span class="text-success">+₹{{ number_format(abs($txn->amount), 2) }}</span>
                            @else
                                <span class="text-danger">-₹{{ number_format(abs($txn->amount), 2) }}</span>
                            @endif
                        </td>
                        <td class="text-end text-muted font-monospace">₹{{ number_format($txn->balance_before ?? 0, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">₹{{ number_format($txn->balance_after ?? 0, 2) }}</td>
                        <td class="text-muted">{{ $txn->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-journal fs-1 d-block mb-2 opacity-50"></i>
                            <h6 class="fw-semibold">No Ledger Entries Found</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="mt-4 d-flex justify-content-end">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
