@extends('astrologer.layouts.app')

@section('title', 'My Wallet & Ledger')
@section('page_title', 'Wallet & Transaction Ledger')

@section('content')
<!-- Financial Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card bg-white border-0 shadow-sm p-4" style="border-radius: 14px;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Available for Withdrawal</span>
                    <h3 class="fw-bold text-success mt-1 mb-0">₹{{ number_format($wallet->available_balance ?? 0, 2) }}</h3>
                </div>
                <div class="metric-icon" style="background: rgba(34, 197, 94, 0.12); color: #16A34A;">
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                <a href="{{ route('astrologer.withdrawals.index') }}" class="btn btn-sm btn-success fw-bold px-3">
                    <i class="bi bi-arrow-up-right-circle me-1"></i> Request Payout
                </a>
                <small class="text-muted">Instant hold & review</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="metric-card bg-white border-0 shadow-sm p-4" style="border-radius: 14px;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Pending in Hold Period</span>
                    <h3 class="fw-bold text-warning mt-1 mb-0">₹{{ number_format($wallet->pending_balance ?? 0, 2) }}</h3>
                </div>
                <div class="metric-icon" style="background: rgba(245, 176, 65, 0.15); color: #D4AC0D;">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top">
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Credits after return / hold window</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="metric-card bg-white border-0 shadow-sm p-4" style="border-radius: 14px;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Held in Processing</span>
                    <h3 class="fw-bold text-primary mt-1 mb-0">₹{{ number_format($wallet->held_balance ?? 0, 2) }}</h3>
                </div>
                <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                    <i class="bi bi-lock-fill fs-4"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top">
                <small class="text-muted"><i class="bi bi-shield-check me-1"></i> Active withdrawal requests</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="metric-card bg-white border-0 shadow-sm p-4" style="border-radius: 14px;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Lifetime Withdrawn</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">₹{{ number_format($wallet->lifetime_withdrawn ?? 0, 2) }}</h3>
                </div>
                <div class="metric-icon" style="background: rgba(107, 114, 128, 0.12); color: #4B5563;">
                    <i class="bi bi-bank fs-4"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top d-flex justify-content-between">
                <span class="text-muted small">Total Earnings:</span>
                <span class="fw-bold small text-dark">₹{{ number_format($wallet->lifetime_earnings ?? 0, 2) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Ledger Transactions Card -->
<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="fw-bold mb-0"><i class="bi bi-journal-text text-warning me-2"></i>Double-Entry Wallet Ledger</h5>
            <p class="text-muted small mb-0">Immutable record of every balance movement, hold, and payout</p>
        </div>
    </div>

    <div class="card-body p-4 pt-2">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('astrologer.wallet.index') }}" class="row g-2 align-items-center mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Transaction Types</option>
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
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From Date">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="To Date">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm fw-bold w-50">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('astrologer.wallet.index') }}" class="btn btn-outline-secondary btn-sm w-50">
                    Reset
                </a>
            </div>
        </form>

        <!-- Ledger Table -->
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase">Reference</th>
                        <th class="small text-uppercase">Type</th>
                        <th class="small text-uppercase">Description</th>
                        <th class="small text-uppercase text-end">Amount</th>
                        <th class="small text-uppercase text-end">Balance After</th>
                        <th class="small text-uppercase text-center">Status</th>
                        <th class="small text-uppercase">Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                    <tr>
                        <td>
                            <span class="font-monospace fw-bold text-dark" style="font-size: 0.85rem;">
                                {{ $txn->reference_key ?? 'TXN-'.$txn->id }}
                            </span>
                        </td>
                        <td>
                            @php
                                $typeLabels = [
                                    'commission_credit' => ['bg' => 'bg-success bg-opacity-10 text-success', 'label' => 'Commission Credit', 'icon' => 'bi-arrow-down-left-circle'],
                                    'commission_reversal' => ['bg' => 'bg-danger bg-opacity-10 text-danger', 'label' => 'Reversal', 'icon' => 'bi-arrow-counterclockwise'],
                                    'withdrawal_hold' => ['bg' => 'bg-warning bg-opacity-10 text-warning', 'label' => 'Withdrawal Hold', 'icon' => 'bi-lock'],
                                    'withdrawal_release' => ['bg' => 'bg-info bg-opacity-10 text-info', 'label' => 'Hold Released', 'icon' => 'bi-unlock'],
                                    'withdrawal_paid' => ['bg' => 'bg-primary bg-opacity-10 text-primary', 'label' => 'Payout Paid', 'icon' => 'bi-check-all'],
                                    'bonus_credit' => ['bg' => 'bg-success bg-opacity-10 text-success', 'label' => 'Bonus Credit', 'icon' => 'bi-gift'],
                                    'manual_adjustment' => ['bg' => 'bg-secondary bg-opacity-10 text-secondary', 'label' => 'Adjustment', 'icon' => 'bi-sliders'],
                                ];
                                $tMeta = $typeLabels[$txn->type] ?? ['bg' => 'bg-light text-dark', 'label' => ucfirst(str_replace('_', ' ', $txn->type)), 'icon' => 'bi-dot'];
                            @endphp
                            <span class="badge {{ $tMeta['bg'] }} px-2 py-1">
                                <i class="bi {{ $tMeta['icon'] }} me-1"></i>{{ $tMeta['label'] }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $txn->description }}</span>
                        </td>
                        <td class="text-end fw-bold">
                            @if(in_array($txn->type, ['commission_credit', 'bonus_credit', 'withdrawal_release']) || (float)$txn->amount > 0 && !in_array($txn->type, ['withdrawal_hold', 'withdrawal_paid', 'commission_reversal']))
                                <span class="text-success">+₹{{ number_format(abs($txn->amount), 2) }}</span>
                            @else
                                <span class="text-danger">-₹{{ number_format(abs($txn->amount), 2) }}</span>
                            @endif
                        </td>
                        <td class="text-end font-monospace text-muted small">
                            ₹{{ number_format($txn->balance_after ?? 0, 2) }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-check-circle-fill me-1"></i>Completed
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $txn->created_at->format('d M Y, h:i A') }}</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <h6 class="fw-semibold">No Transactions Found</h6>
                            <p class="small mb-0">Your wallet ledger history will be logged here as purchases and withdrawals occur.</p>
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
