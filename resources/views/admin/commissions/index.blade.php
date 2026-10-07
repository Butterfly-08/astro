@extends('layouts.admin')

@section('title', 'Referral Commissions')
@section('page_title', 'Referral Commissions Management')

@section('content')
<!-- Financial Metrics Row -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Commissions</span>
                <h3 class="fw-bold my-1 text-dark">{{ number_format($metrics['total_commissions']) }}</h3>
                <small class="text-muted">Lifetime transactions</small>
            </div>
            <div class="stat-icon icon-purple">
                <i class="bi bi-percent"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Pending Hold Period</span>
                <h3 class="fw-bold my-1 text-warning">₹{{ number_format($metrics['pending_amount'], 2) }}</h3>
                <small class="text-warning fw-semibold">{{ $metrics['pending_count'] }} pending orders</small>
            </div>
            <div class="stat-icon icon-gold">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Available for Payout</span>
                <h3 class="fw-bold my-1 text-success">₹{{ number_format($metrics['available_amount'], 2) }}</h3>
                <small class="text-success fw-semibold">Ready in astrologer wallets</small>
            </div>
            <div class="stat-icon icon-green">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Paid Out</span>
                <h3 class="fw-bold my-1 text-primary">₹{{ number_format($metrics['paid_amount'], 2) }}</h3>
                <small class="text-muted">Disbursed successfully</small>
            </div>
            <div class="stat-icon icon-blue">
                <i class="bi bi-check2-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Commission Table Card -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0">Commission Records</h5>
                <p class="text-muted small mb-0">Item-level commission audit trail linked to orders and astrologers</p>
            </div>
        </div>
    </div>

    <div class="card-body p-4 pt-2">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.commissions.index') }}" class="row g-2 align-items-center mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Commission # / Order # / Astrologer" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="reversed" {{ request('status') === 'reversed' ? 'selected' : '' }}>Reversed</option>
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
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="From Date">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm fw-bold w-50">Filter</button>
                <a href="{{ route('admin.commissions.index') }}" class="btn btn-outline-secondary btn-sm w-50">Reset</a>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase">Commission ID</th>
                        <th class="small text-uppercase">Astrologer</th>
                        <th class="small text-uppercase">Order & Product</th>
                        <th class="small text-uppercase text-end">Order Base</th>
                        <th class="small text-uppercase text-end">Rate</th>
                        <th class="small text-uppercase text-end">Commission</th>
                        <th class="small text-uppercase text-center">Status</th>
                        <th class="small text-uppercase">Matures On</th>
                        <th class="small text-uppercase text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commissions as $com)
                    <tr>
                        <td>
                            <a href="{{ route('admin.commissions.show', $com->id) }}" class="font-monospace fw-bold text-dark text-decoration-none">
                                {{ $com->commission_number }}
                            </a>
                        </td>
                        <td>
                            @if($com->astrologer)
                                <div>
                                    <strong class="d-block text-dark small">{{ $com->astrologer->display_name }}</strong>
                                    <span class="badge bg-light text-warning border font-monospace" style="font-size:0.7rem;">
                                        {{ $com->astrologer->referral_code }}
                                    </span>
                                </div>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <span class="small fw-semibold d-block text-dark">{{ $com->product->name ?? 'Product #'.$com->product_id }}</span>
                                <span class="text-muted small">Order: <span class="font-monospace">{{ $com->order->order_number ?? 'ORD-'.$com->order_id }}</span></span>
                            </div>
                        </td>
                        <td class="text-end small">
                            ₹{{ number_format($com->order_amount, 2) }}
                        </td>
                        <td class="text-end small">
                            @if($com->commission_type === 'percent')
                                {{ $com->commission_rate }}%
                            @else
                                ₹{{ number_format($com->commission_rate, 2) }}
                            @endif
                        </td>
                        <td class="text-end fw-bold text-success">
                            ₹{{ number_format($com->commission_amount, 2) }}
                        </td>
                        <td class="text-center">
                            @php
                                $badges = [
                                    'pending'   => 'bg-warning text-dark',
                                    'approved'  => 'bg-primary text-white',
                                    'available' => 'bg-success text-white',
                                    'paid'      => 'bg-info text-white',
                                    'rejected'  => 'bg-danger text-white',
                                    'reversed'  => 'bg-secondary text-white',
                                ];
                                $bClass = $badges[$com->status] ?? 'bg-light text-dark';
                            @endphp
                            <span class="badge {{ $bClass }} px-2 py-1 text-capitalize">
                                {{ $com->status }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $com->available_at ? $com->available_at->format('d M Y') : '—' }}
                            </small>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border py-0 px-2" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <a class="dropdown-item small" href="{{ route('admin.commissions.show', $com->id) }}">
                                            <i class="bi bi-eye text-primary me-2"></i> View Details
                                        </a>
                                    </li>
                                    @if($com->status === 'pending')
                                    <li>
                                        <form method="POST" action="{{ route('admin.commissions.approve', $com->id) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item small text-success" onclick="return confirm('Approve commission immediately?')">
                                                <i class="bi bi-check-circle me-2"></i> Fast Approve
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <button class="dropdown-item small text-danger" type="button" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $com->id }}">
                                            <i class="bi bi-x-circle me-2"></i> Reject Commission
                                        </button>
                                    </li>
                                    @endif
                                </ul>
                            </div>

                            <!-- Reject Modal -->
                            @if($com->status === 'pending')
                            <div class="modal fade text-start" id="rejectModal{{ $com->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.commissions.reject', $com->id) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Reject Commission #{{ $com->commission_number }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Reason for Rejection</label>
                                                    <textarea name="reason" rows="3" class="form-control" placeholder="e.g. Self-referral detected, returned order, or fraud suspicion" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-sm btn-danger fw-bold">Confirm Reject</button>
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
                            <i class="bi bi-receipt fs-1 d-block mb-2 opacity-50"></i>
                            <h6 class="fw-semibold">No Commission Records</h6>
                            <p class="small mb-0">Commissions will be logged when referred orders are successfully placed.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($commissions->hasPages())
        <div class="mt-4 d-flex justify-content-end">
            {{ $commissions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
