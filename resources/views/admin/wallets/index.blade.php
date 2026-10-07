@extends('layouts.admin')

@section('title', 'Astrologer Wallets')
@section('page_title', 'Wallet & Balance Management')

@section('content')
<!-- Metrics -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Available</span>
                <h3 class="fw-bold my-1 text-success">₹{{ number_format($metrics['total_available'], 2) }}</h3>
                <small class="text-muted">Across all wallets</small>
            </div>
            <div class="stat-icon icon-green"><i class="bi bi-wallet2"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Pending</span>
                <h3 class="fw-bold my-1 text-warning">₹{{ number_format($metrics['total_pending'], 2) }}</h3>
                <small class="text-muted">In hold periods</small>
            </div>
            <div class="stat-icon icon-gold"><i class="bi bi-hourglass-split"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Held in Withdrawals</span>
                <h3 class="fw-bold my-1 text-primary">₹{{ number_format($metrics['total_held'], 2) }}</h3>
                <small class="text-muted">Locked for payouts</small>
            </div>
            <div class="stat-icon icon-blue"><i class="bi bi-lock-fill"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Lifetime Paid Out</span>
                <h3 class="fw-bold my-1 text-dark">₹{{ number_format($metrics['lifetime_payouts'], 2) }}</h3>
                <small class="text-muted">Total disbursed to astrologers</small>
            </div>
            <div class="stat-icon icon-purple"><i class="bi bi-bank"></i></div>
        </div>
    </div>
</div>

<!-- Wallet Table -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">Astrologer Wallets</h5>
            <p class="text-muted small mb-0">Track individual astrologer balances and perform authorized manual adjustments</p>
        </div>
        <a href="{{ route('admin.wallets.transactions') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-journal-text me-1"></i> View Full Ledger
        </a>
    </div>

    <div class="card-body p-4 pt-2">
        <!-- Search -->
        <form method="GET" action="{{ route('admin.wallets.index') }}" class="row g-2 mb-4">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by astrologer name, email or referral code..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm fw-bold w-50">Search</button>
                <a href="{{ route('admin.wallets.index') }}" class="btn btn-outline-secondary btn-sm w-50">Clear</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase">Astrologer</th>
                        <th class="small text-uppercase text-end">Available</th>
                        <th class="small text-uppercase text-end">Pending</th>
                        <th class="small text-uppercase text-end">Held</th>
                        <th class="small text-uppercase text-end">Lifetime Earnings</th>
                        <th class="small text-uppercase text-end">Lifetime Withdrawn</th>
                        <th class="small text-uppercase text-center">Status</th>
                        <th class="small text-uppercase text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wallets as $wallet)
                    <tr>
                        <td>
                            @if($wallet->astrologer)
                                <div>
                                    <strong class="d-block text-dark small">{{ $wallet->astrologer->display_name }}</strong>
                                    <span class="badge bg-light text-warning border font-monospace" style="font-size:0.7rem;">{{ $wallet->astrologer->referral_code }}</span>
                                </div>
                            @else
                                <span class="text-muted small">Deleted Astrologer</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-success">₹{{ number_format($wallet->available_balance, 2) }}</td>
                        <td class="text-end fw-bold text-warning">₹{{ number_format($wallet->pending_balance, 2) }}</td>
                        <td class="text-end fw-bold text-primary">₹{{ number_format($wallet->held_balance, 2) }}</td>
                        <td class="text-end text-muted small">₹{{ number_format($wallet->lifetime_earnings, 2) }}</td>
                        <td class="text-end text-muted small">₹{{ number_format($wallet->lifetime_withdrawn, 2) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $wallet->status === 'active' ? 'bg-success' : 'bg-danger' }} text-white px-2">
                                {{ ucfirst($wallet->status ?? 'active') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#adjustModal{{ $wallet->id }}">
                                <i class="bi bi-sliders"></i> Adjust
                            </button>

                            <!-- Manual Adjust Modal -->
                            <div class="modal fade text-start" id="adjustModal{{ $wallet->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.wallets.adjust', $wallet->id) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Adjust Wallet — {{ $wallet->astrologer->display_name ?? 'Astrologer' }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-warning border-0 small py-2 px-3 mb-3">
                                                    <strong>Current Available Balance:</strong> ₹{{ number_format($wallet->available_balance, 2) }}<br>
                                                    This action is permanent and will create an immutable ledger entry.
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Adjustment Type</label>
                                                    <select name="action" class="form-select" required>
                                                        <option value="credit">Credit (Add Funds)</option>
                                                        <option value="debit">Debit (Deduct Funds)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Amount (₹)</label>
                                                    <input type="number" step="0.01" min="1" max="500000" name="amount" class="form-control" placeholder="e.g. 500" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Reason for Adjustment</label>
                                                    <textarea name="reason" rows="2" class="form-control" placeholder="Specify reason (e.g. Welcome bonus, correction for manual order, etc.)" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-sm btn-primary fw-bold" onclick="return confirm('This wallet adjustment is permanent and audited. Proceed?')">
                                                    Confirm Adjustment
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-wallet2 fs-1 d-block mb-2 opacity-50"></i>
                            <h6 class="fw-semibold">No Wallets Found</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($wallets->hasPages())
        <div class="mt-4 d-flex justify-content-end">
            {{ $wallets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
