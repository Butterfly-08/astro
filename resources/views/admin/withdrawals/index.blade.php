@extends('layouts.admin')

@section('title', 'Withdrawal Requests')
@section('page_title', 'Astrologer Payout & Withdrawal Management')

@section('content')
<!-- Financial Metrics Row -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Pending Review</span>
                <h3 class="fw-bold my-1 text-warning">₹{{ number_format($metrics['pending_amount'], 2) }}</h3>
                <small class="text-warning fw-semibold">{{ $metrics['pending_count'] }} requests awaiting action</small>
            </div>
            <div class="stat-icon icon-gold">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Approved / Queue</span>
                <h3 class="fw-bold my-1 text-primary">{{ number_format($metrics['approved_count']) }}</h3>
                <small class="text-primary fw-semibold">{{ $metrics['processing_count'] }} in bank processing</small>
            </div>
            <div class="stat-icon icon-blue">
                <i class="bi bi-gear-wide-connected"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Paid Out</span>
                <h3 class="fw-bold my-1 text-success">₹{{ number_format($metrics['paid_amount'], 2) }}</h3>
                <small class="text-muted">Successfully disbursed</small>
            </div>
            <div class="stat-icon icon-green">
                <i class="bi bi-check2-all"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Requests</span>
                <h3 class="fw-bold my-1 text-dark">{{ number_format($metrics['total_requests']) }}</h3>
                <small class="text-muted">All-time lifetime claims</small>
            </div>
            <div class="stat-icon icon-purple">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Withdrawals Card -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <h5 class="fw-bold mb-0">Withdrawal Claims</h5>
        <p class="text-muted small mb-0">Review astrologer withdrawal claims, approve processing, and record UTR settlement numbers</p>
    </div>

    <div class="card-body p-4 pt-2">
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.withdrawals.index') }}" class="row g-2 align-items-center mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="ID / UTR / UPI / Astrologer" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">All Methods</option>
                    <option value="upi" {{ request('payment_method') === 'upi' ? 'selected' : '' }}>UPI Transfer</option>
                    <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Account</option>
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
                <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline-secondary btn-sm w-50">Reset</a>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase">Claim ID</th>
                        <th class="small text-uppercase">Astrologer</th>
                        <th class="small text-uppercase text-end">Amount</th>
                        <th class="small text-uppercase text-end">Fee</th>
                        <th class="small text-uppercase text-end">Net Payout</th>
                        <th class="small text-uppercase">Destination Details</th>
                        <th class="small text-uppercase text-center">Status</th>
                        <th class="small text-uppercase">Requested At</th>
                        <th class="small text-uppercase text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $wd)
                    <tr>
                        <td>
                            <a href="{{ route('admin.withdrawals.show', $wd->id) }}" class="font-monospace fw-bold text-dark text-decoration-none">
                                {{ $wd->withdrawal_number }}
                            </a>
                        </td>
                        <td>
                            @if($wd->astrologer)
                                <div>
                                    <strong class="d-block text-dark small">{{ $wd->astrologer->display_name }}</strong>
                                    <span class="badge bg-light text-warning border font-monospace" style="font-size:0.7rem;">
                                        {{ $wd->astrologer->referral_code }}
                                    </span>
                                </div>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold">
                            ₹{{ number_format($wd->amount, 2) }}
                        </td>
                        <td class="text-end text-muted small">
                            ₹{{ number_format($wd->fee, 2) }}
                        </td>
                        <td class="text-end fw-bold text-success">
                            ₹{{ number_format($wd->net_amount, 2) }}
                        </td>
                        <td>
                            @if($wd->payment_method === 'upi')
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-qr-code text-primary me-1"></i>{{ $wd->upi_id_masked }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-bank text-secondary me-1"></i>{{ $wd->bank_name ?? 'Bank' }} ({{ $wd->account_number_masked }})
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
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
                                $bClass = $badges[$wd->status] ?? 'bg-light text-dark';
                            @endphp
                            <span class="badge {{ $bClass }} px-2 py-1 text-capitalize">
                                {{ str_replace('_', ' ', $wd->status) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $wd->created_at->format('d M Y, h:i A') }}</small>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border py-0 px-2" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <a class="dropdown-item small" href="{{ route('admin.withdrawals.show', $wd->id) }}">
                                            <i class="bi bi-eye text-primary me-2"></i> View Details
                                        </a>
                                    </li>
                                    @if($wd->status === 'pending')
                                    <li>
                                        <form method="POST" action="{{ route('admin.withdrawals.approve', $wd->id) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item small text-primary" onclick="return confirm('Approve this withdrawal claim for bank processing?')">
                                                <i class="bi bi-check-circle me-2"></i> Approve Claim
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    @if(in_array($wd->status, ['pending', 'approved']))
                                    <li>
                                        <form method="POST" action="{{ route('admin.withdrawals.processing', $wd->id) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item small text-info">
                                                <i class="bi bi-arrow-repeat me-2"></i> Mark Processing
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    @if(in_array($wd->status, ['pending', 'approved', 'processing']))
                                    <li>
                                        <button class="dropdown-item small text-success" type="button" data-bs-toggle="modal" data-bs-target="#paidModal{{ $wd->id }}">
                                            <i class="bi bi-check-all me-2"></i> Mark as Paid (UTR)
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item small text-danger" type="button" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $wd->id }}">
                                            <i class="bi bi-x-circle me-2"></i> Reject & Refund
                                        </button>
                                    </li>
                                    @endif
                                </ul>
                            </div>

                            <!-- Mark Paid Modal -->
                            @if(in_array($wd->status, ['pending', 'approved', 'processing']))
                            <div class="modal fade text-start" id="paidModal{{ $wd->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.withdrawals.mark-paid', $wd->id) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Disburse Withdrawal #{{ $wd->withdrawal_number }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-light border small mb-3">
                                                    <div><strong>Payee:</strong> {{ $wd->astrologer->display_name ?? 'Astrologer' }}</div>
                                                    <div><strong>Net Amount:</strong> ₹{{ number_format($wd->net_amount, 2) }}</div>
                                                    @if($wd->payment_method === 'upi')
                                                        <div><strong>UPI ID:</strong> <code>{{ $wd->upi_id }}</code></div>
                                                    @else
                                                        <div><strong>Account:</strong> <code>{{ $wd->account_number }}</code> (IFSC: <code>{{ $wd->ifsc_code }}</code>)</div>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Bank UTR / Transaction Reference</label>
                                                    <input type="text" name="transaction_reference" class="form-control font-monospace" placeholder="e.g. UTR123456789012" required>
                                                    <small class="text-muted">Finalizes debit and permanently writes to astrologer ledger.</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-sm btn-success fw-bold">Record Payout as Paid</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Reject Modal -->
                            <div class="modal fade text-start" id="rejectModal{{ $wd->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.withdrawals.reject', $wd->id) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Reject Withdrawal #{{ $wd->withdrawal_number }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small text-muted mb-2">Rejecting this request will automatically release the held ₹{{ number_format($wd->amount, 2) }} back to the astrologer's available balance with an audit ledger entry.</p>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Rejection Reason</label>
                                                    <textarea name="reason" rows="3" class="form-control" placeholder="e.g. Invalid bank details, IFSC mismatch, or duplicate request" required></textarea>
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-cash-stack fs-1 d-block mb-2 opacity-50"></i>
                            <h6 class="fw-semibold">No Withdrawal Requests Found</h6>
                            <p class="small mb-0">Astrologer withdrawal submissions will appear here for verification and payout.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($withdrawals->hasPages())
        <div class="mt-4 d-flex justify-content-end">
            {{ $withdrawals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
