@extends('layouts.admin')

@section('title', 'Users — Admin')
@section('page_title', 'Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h6 class="mb-1 fw-bold text-dark">Customer Accounts</h6>
        <p class="text-muted small mb-0">Search and review registered AstroVani customers.</p>
    </div>
    <span class="badge bg-primary-subtle text-primary">{{ number_format($users->total()) }} total</span>
</div>

<div class="bg-white rounded-3 border p-3 mb-3">
    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-6">
            <label for="user-search" class="form-label small fw-semibold">Search customers</label>
            <input id="user-search" type="search" name="search" class="form-control form-control-sm"
                   placeholder="Name, email, or phone" value="{{ request('search') }}">
        </div>
        <div class="col-7 col-md-3">
            <label for="user-status" class="form-label small fw-semibold">Account status</label>
            <select id="user-status" name="status" class="form-select form-select-sm">
                <option value="">All statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
            </select>
        </div>
        <div class="col-5 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-3 border shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-muted">
                <tr>
                    <th class="ps-3">Customer</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th class="pe-3">Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $statusClass = match ($user->status) {
                            'active' => 'success',
                            'blocked' => 'danger',
                            default => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold text-dark">{{ $user->full_name ?: 'Unnamed customer' }}</div>
                            <div class="small text-muted">{{ $user->email }}</div>
                        </td>
                        <td>{{ $user->phone ?: '—' }}</td>
                        <td>{{ $user->city ? $user->city . ', ' . $user->state : ($user->country ?: '—') }}</td>
                        <td><span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }}">{{ ucfirst($user->status) }}</span></td>
                        <td class="pe-3 text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-people display-6 d-block mb-2 opacity-25"></i>
                            No customer accounts match these filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="p-3 border-top">{{ $users->links() }}</div>
    @endif
</div>
@endsection
