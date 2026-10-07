@extends('layouts.admin')

@section('title', 'Astrologer Referral Partners')
@section('page_title', 'Referral Partners Management')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.referrals.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Referral Traffic Log
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="fw-bold mb-0">Astrologer Partners & Referral Status</h5>
            <p class="text-muted small mb-0">Review astrologer referral codes, approval statuses, wallets, and commission eligibility</p>
        </div>
    </div>

    <div class="card-body p-4 pt-2">
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.referrals.partners') }}" class="row g-2 align-items-center mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, email, or referral code..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="approval_status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('approval_status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                    <option value="approved" {{ request('approval_status') === 'approved' ? 'selected' : '' }}>Approved / Active</option>
                    <option value="suspended" {{ request('approval_status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="rejected" {{ request('approval_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm fw-bold w-50">Filter</button>
                <a href="{{ route('admin.referrals.partners') }}" class="btn btn-outline-secondary btn-sm w-50">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase">Astrologer</th>
                        <th class="text-uppercase">Referral Code</th>
                        <th class="text-uppercase text-center">Partner Status</th>
                        <th class="text-uppercase text-end">Available Balance</th>
                        <th class="text-uppercase text-end">Total Earned</th>
                        <th class="text-uppercase">Approved At</th>
                        <th class="text-uppercase text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($astrologers as $astro)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-warning bg-opacity-20 text-warning d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                    {{ strtoupper(substr($astro->display_name ?? 'A', 0, 1)) }}
                                </div>
                                <div>
                                    <strong class="d-block text-dark">{{ $astro->display_name }}</strong>
                                    <span class="text-muted" style="font-size:0.75rem;">{{ $astro->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-6">
                                {{ $astro->referral_code ?? 'NO-CODE' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($astro->approval_status === 'approved')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                    <i class="bi bi-patch-check-fill me-1"></i> Approved
                                </span>
                            @elseif($astro->approval_status === 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                                    <i class="bi bi-clock-history me-1"></i> Pending
                                </span>
                            @elseif($astro->approval_status === 'suspended')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1" title="{{ $astro->suspension_reason }}">
                                    <i class="bi bi-slash-circle me-1"></i> Suspended
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1">
                                    {{ ucfirst($astro->approval_status) }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-success font-monospace">
                            ₹{{ number_format($astro->wallet->available_balance ?? 0, 2) }}
                        </td>
                        <td class="text-end font-monospace text-muted">
                            ₹{{ number_format($astro->wallet->lifetime_earnings ?? 0, 2) }}
                        </td>
                        <td class="text-muted">
                            {{ $astro->approved_at ? $astro->approved_at->format('d M Y') : '—' }}
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#editPartnerModal{{ $astro->id }}">
                                <i class="bi bi-pencil-square me-1"></i> Edit Status
                            </button>

                            <!-- Edit Partner Modal -->
                            <div class="modal fade text-start" id="editPartnerModal{{ $astro->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.referrals.partners.update-status', $astro->id) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Update Partner: {{ $astro->display_name }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Approval Status</label>
                                                    <select name="approval_status" class="form-select" id="statusSelect{{ $astro->id }}" onchange="toggleSuspensionField(this, '{{ $astro->id }}')">
                                                        <option value="approved" {{ $astro->approval_status === 'approved' ? 'selected' : '' }}>Approved (Active Referral Code & Commissions)</option>
                                                        <option value="suspended" {{ $astro->approval_status === 'suspended' ? 'selected' : '' }}>Suspended (Freezes Referral Links)</option>
                                                        <option value="rejected" {{ $astro->approval_status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Referral Code</label>
                                                    <input type="text" name="referral_code" class="form-control text-uppercase font-monospace" value="{{ $astro->referral_code }}" maxlength="20" required>
                                                    <small class="text-muted">Unique tracking code for this partner</small>
                                                </div>

                                                <div class="mb-3 {{ $astro->approval_status === 'suspended' ? '' : 'd-none' }}" id="suspensionGroup{{ $astro->id }}">
                                                    <label class="form-label small fw-semibold text-danger">Suspension Reason</label>
                                                    <textarea name="suspension_reason" class="form-control" rows="2" placeholder="Explain why the partner is suspended...">{{ $astro->suspension_reason }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-sm btn-primary fw-bold">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                            <h6 class="fw-semibold">No Astrologer Partners Found</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($astrologers->hasPages())
        <div class="mt-4 d-flex justify-content-end">
            {{ $astrologers->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function toggleSuspensionField(select, id) {
    const group = document.getElementById('suspensionGroup' + id);
    if (select.value === 'suspended') {
        group.classList.remove('d-none');
    } else {
        group.classList.add('d-none');
    }
}
</script>
@endsection
