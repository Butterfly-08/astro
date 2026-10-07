@extends('layouts.admin')

@section('title', 'Withdrawal #' . $withdrawal->withdrawal_number)
@section('page_title', 'Withdrawal Claim Details')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Withdrawals
    </a>
</div>

<div class="row g-4">
    <!-- Main Column -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Withdrawal Claim</span>
                    <h4 class="fw-bold mb-0 font-monospace text-dark">{{ $withdrawal->withdrawal_number }}</h4>
                </div>
                <div>
                    @php
                        $badges = [
                            'pending'      => 'bg-warning text-dark',
                            'under_review' => 'bg-info text-white',
                            'approved'     => 'bg-primary text-white',
                            'processing'   => 'bg-info bg-opacity-75 text-white',
                            'paid'         => 'bg-success text-white',
                            'rejected'     => 'bg-danger text-white',
                            'cancelled'    => 'bg-secondary text-white',
                        ];
                        $bClass = $badges[$withdrawal->status] ?? 'bg-light text-dark';
                    @endphp
                    <span class="badge {{ $bClass }} fs-6 px-3 py-2 text-capitalize">
                        {{ str_replace('_', ' ', $withdrawal->status) }}
                    </span>
                </div>
            </div>

            <div class="card-body p-4 pt-2">
                <!-- Payout Summary -->
                <div class="row g-3 bg-light p-3 rounded-3 border mb-4">
                    <div class="col-sm-4 text-center border-end">
                        <small class="text-muted text-uppercase fw-semibold d-block">Requested Amount</small>
                        <h4 class="fw-bold mb-0 text-dark">₹{{ number_format($withdrawal->amount, 2) }}</h4>
                    </div>
                    <div class="col-sm-4 text-center border-end">
                        <small class="text-muted text-uppercase fw-semibold d-block">Processing Fee</small>
                        <h4 class="fw-bold mb-0 text-secondary">₹{{ number_format($withdrawal->fee, 2) }}</h4>
                    </div>
                    <div class="col-sm-4 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Net Payable</small>
                        <h4 class="fw-bold mb-0 text-success">₹{{ number_format($withdrawal->net_amount, 2) }}</h4>
                    </div>
                </div>

                <!-- Payee Banking Details -->
                <h6 class="fw-bold mb-3"><i class="bi bi-bank text-warning me-2"></i>Payee Disbursement Details</h6>
                <div class="p-3 bg-white rounded-3 border mb-4">
                    @if($withdrawal->payment_method === 'upi')
                        <div class="row g-2 small">
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Payment Mode</span>
                                <strong class="text-primary"><i class="bi bi-qr-code me-1"></i> Unified Payments Interface (UPI)</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">UPI ID / Virtual Address</span>
                                <code class="fs-6">{{ $withdrawal->upi_id }}</code>
                            </div>
                        </div>
                    @else
                        <div class="row g-3 small">
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Account Holder</span>
                                <strong>{{ $withdrawal->account_holder_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Bank Name</span>
                                <strong>{{ $withdrawal->bank_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Account Number</span>
                                <code class="fs-6">{{ $withdrawal->account_number }}</code>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">IFSC Code</span>
                                <code class="fs-6 text-uppercase">{{ $withdrawal->ifsc_code }}</code>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Settlement Info -->
                @if($withdrawal->transaction_reference)
                    <div class="alert alert-success border-0 p-3 rounded-3 mb-4">
                        <strong class="d-block mb-1"><i class="bi bi-check-all me-1"></i> Disbursed Successfully</strong>
                        <span class="small">Bank UTR / Settlement Reference: <code class="fw-bold">{{ $withdrawal->transaction_reference }}</code></span>
                        <div class="small text-muted mt-1">Processed at: {{ $withdrawal->processed_at ? $withdrawal->processed_at->format('d M Y, h:i A') : 'N/A' }}</div>
                    </div>
                @endif

                @if($withdrawal->rejection_reason)
                    <div class="alert alert-danger border-0 p-3 rounded-3 mb-4">
                        <strong class="d-block mb-1"><i class="bi bi-x-circle me-1"></i> Claim Rejected</strong>
                        <span class="small">{{ $withdrawal->rejection_reason }}</span>
                    </div>
                @endif

                <!-- Action Controls -->
                @if(in_array($withdrawal->status, ['pending', 'approved', 'processing']))
                    <div class="d-flex flex-wrap gap-2 justify-content-end pt-3 border-top">
                        @if($withdrawal->status === 'pending')
                            <form method="POST" action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary fw-bold" onclick="return confirm('Approve this withdrawal claim?')">
                                    <i class="bi bi-check-circle me-1"></i> Approve Claim
                                </button>
                            </form>
                        @endif

                        @if(in_array($withdrawal->status, ['pending', 'approved']))
                            <form method="POST" action="{{ route('admin.withdrawals.processing', $withdrawal->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-info text-white fw-bold">
                                    <i class="bi bi-arrow-repeat me-1"></i> Mark Processing
                                </button>
                            </form>
                        @endif

                        <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#paidModal">
                            <i class="bi bi-check-all me-1"></i> Record as Paid (Enter UTR)
                        </button>

                        <button type="button" class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-1"></i> Reject & Release Funds
                        </button>
                    </div>

                    <!-- Mark Paid Modal -->
                    <div class="modal fade" id="paidModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.withdrawals.mark-paid', $withdrawal->id) }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-bold">Disburse Claim #{{ $withdrawal->withdrawal_number }}</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Bank UTR / Transaction Reference</label>
                                            <input type="text" name="transaction_reference" class="form-control font-monospace" placeholder="e.g. UTR987654321012" required>
                                            <small class="text-muted">This action finalizes the withdrawal, deducts the held balance, and writes to the ledger.</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-success fw-bold">Record Settlement</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-bold">Reject Withdrawal #{{ $withdrawal->withdrawal_number }}</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Reason for Rejection</label>
                                            <textarea name="reason" rows="3" class="form-control" placeholder="Specify clear reason (e.g. Invalid account number, bank IFSC closed)" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold">Confirm Rejection</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Astrologer Wallet Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-wallet2 text-warning me-2"></i>Astrologer Wallet</h6>
            </div>
            <div class="card-body p-4 pt-2">
                @if($withdrawal->astrologer)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 50px; height: 50px;">
                            {{ strtoupper(substr($withdrawal->astrologer->display_name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <strong class="d-block text-dark">{{ $withdrawal->astrologer->display_name }}</strong>
                            <span class="badge bg-light text-warning border font-monospace">{{ $withdrawal->astrologer->referral_code }}</span>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Available:</span>
                            <strong class="text-success">₹{{ number_format($withdrawal->astrologer->wallet->available_balance ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Held in Payouts:</span>
                            <strong class="text-primary">₹{{ number_format($withdrawal->astrologer->wallet->held_balance ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Lifetime Withdrawn:</span>
                            <strong class="text-dark">₹{{ number_format($withdrawal->astrologer->wallet->lifetime_withdrawn ?? 0, 2) }}</strong>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Audit Dates Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-warning me-2"></i>Audit Timeline</h6>
            </div>
            <div class="card-body p-4 pt-2 small">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <span class="text-muted d-block">Requested</span>
                        <strong>{{ $withdrawal->created_at->format('d M Y, h:i A') }}</strong>
                    </li>
                    @if($withdrawal->approved_at)
                    <li class="mb-2">
                        <span class="text-muted d-block">Approved</span>
                        <strong>{{ $withdrawal->approved_at->format('d M Y, h:i A') }}</strong>
                    </li>
                    @endif
                    @if($withdrawal->processed_at)
                    <li class="mb-2">
                        <span class="text-muted d-block">Disbursed / Paid</span>
                        <strong>{{ $withdrawal->processed_at->format('d M Y, h:i A') }}</strong>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
