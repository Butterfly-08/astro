@extends('astrologer.layouts.app')

@section('title', 'Withdrawals & Payouts')
@section('page_title', 'Payout Requests & History')

@section('content')
<!-- Top Status & Rules Row -->
<div class="row g-4 mb-4">
    <!-- Balance & Payout Limits -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 100%); border-radius: 14px;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <span class="badge bg-warning text-dark mb-2 px-3"><i class="bi bi-shield-lock-fill me-1"></i> Transaction-Safe Wallet</span>
                    <span class="text-white-50 small text-uppercase d-block fw-semibold">Available for Immediate Payout</span>
                    <h2 class="fw-bold text-white mb-3 brand-font">₹{{ number_format($wallet->available_balance ?? 0, 2) }}</h2>
                </div>

                <div class="bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10">
                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-white-50">Minimum Withdrawal:</span>
                        <strong class="text-warning">₹{{ number_format($minAmount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-white-50">Maximum Per Request:</span>
                        <strong class="text-white">₹{{ number_format($maxAmount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-white-50">Processing Fee:</span>
                        <strong class="text-white">
                            @if($feeType === 'percent')
                                {{ $feeValue }}%
                            @elseif($feeValue > 0)
                                ₹{{ number_format($feeValue, 2) }}
                            @else
                                Free (₹0)
                            @endif
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Request Form Card -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-1"><i class="bi bi-arrow-up-right-circle text-warning me-2"></i>Request New Payout</h5>
                <p class="text-muted small mb-0">Funds are placed on temporary reserve hold until reviewed and paid by administration</p>
            </div>

            <div class="card-body p-4">
                @if($hasPending)
                    <div class="alert alert-warning border-0 d-flex align-items-center gap-3 p-3 rounded-3">
                        <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                        <div>
                            <strong class="d-block">Withdrawal In Progress</strong>
                            <span class="small">You have an active withdrawal request under review. New requests can be submitted once the current payout is completed or cancelled.</span>
                        </div>
                    </div>
                @elseif(($wallet->available_balance ?? 0) < $minAmount)
                    <div class="alert alert-info border-0 d-flex align-items-center gap-3 p-3 rounded-3">
                        <i class="bi bi-info-circle-fill fs-3 text-info"></i>
                        <div>
                            <strong class="d-block">Minimum Balance Required</strong>
                            <span class="small">Your available balance is ₹{{ number_format($wallet->available_balance ?? 0, 2) }}. A minimum balance of ₹{{ number_format($minAmount, 2) }} is needed to request a withdrawal.</span>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('astrologer.withdrawals.store') }}" id="withdrawalForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Withdrawal Amount (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">₹</span>
                                <input type="number" step="0.01" min="{{ $minAmount }}" max="{{ min($maxAmount, $wallet->available_balance) }}"
                                       name="amount" id="withdrawAmount" class="form-control fw-bold"
                                       placeholder="Enter amount (min ₹{{ $minAmount }})" required>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setMaxAmount()">Max</button>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Fee: <span id="displayFee">₹0.00</span></small>
                                <small class="text-success fw-bold">Net Payout: <span id="displayNet">₹0.00</span></small>
                            </div>
                        </div>

                        <!-- Method Tabs -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Payout Destination Method</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="payment_method" id="methodUpi" value="upi" checked onchange="toggleMethod('upi')">
                                <label class="btn btn-outline-warning text-dark fw-semibold" for="methodUpi">
                                    <i class="bi bi-qr-code me-1"></i> Instant UPI
                                </label>

                                <input type="radio" class="btn-check" name="payment_method" id="methodBank" value="bank_transfer" onchange="toggleMethod('bank_transfer')">
                                <label class="btn btn-outline-warning text-dark fw-semibold" for="methodBank">
                                    <i class="bi bi-bank me-1"></i> Direct Bank Transfer
                                </label>
                            </div>
                        </div>

                        <!-- UPI Input Field -->
                        <div id="upiFields" class="mb-3">
                            <label class="form-label small fw-semibold text-muted">UPI ID / VPA</label>
                            <input type="text" name="upi_id" class="form-control font-monospace" placeholder="e.g. yourname@okhdfcbank or 9876543210@paytm">
                            <small class="text-muted">Fastest payout option directly to your UPI linked bank</small>
                        </div>

                        <!-- Bank Input Fields -->
                        <div id="bankFields" class="mb-3 d-none">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">Account Holder Name</label>
                                    <input type="text" name="account_holder_name" class="form-control" placeholder="As per bank passbook">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control" placeholder="e.g. State Bank of India">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">Bank Account Number</label>
                                    <input type="text" name="account_number" class="form-control font-monospace" placeholder="Full account number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">IFSC Code</label>
                                    <input type="text" name="ifsc_code" class="form-control font-monospace text-uppercase" placeholder="e.g. SBIN0001234">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2 mt-2" id="submitBtn">
                            <i class="bi bi-send-check-fill me-1"></i> Submit Withdrawal Request
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Withdrawal History Table Card -->
<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-warning me-2"></i>Withdrawal Requests History</h5>
        <p class="text-muted small mb-0">Track verification status, admin approvals, and bank UTR disbursement reference numbers</p>
    </div>

    <div class="card-body p-4 pt-2">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase">Request ID</th>
                        <th class="small text-uppercase">Requested At</th>
                        <th class="small text-uppercase text-end">Amount</th>
                        <th class="small text-uppercase text-end">Fee</th>
                        <th class="small text-uppercase text-end">Net Payout</th>
                        <th class="small text-uppercase">Method & Details</th>
                        <th class="small text-uppercase text-center">Status</th>
                        <th class="small text-uppercase">UTR / Reference</th>
                        <th class="small text-uppercase text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $wd)
                    <tr>
                        <td>
                            <span class="font-monospace fw-bold text-dark" style="font-size: 0.85rem;">
                                {{ $wd->withdrawal_number }}
                            </span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $wd->created_at->format('d M Y, h:i A') }}</span>
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
                                $statusBadges = [
                                    'pending'      => 'bg-warning text-dark',
                                    'under_review' => 'bg-info text-white',
                                    'approved'     => 'bg-primary text-white',
                                    'processing'   => 'bg-info bg-opacity-75 text-white',
                                    'paid'         => 'bg-success text-white',
                                    'rejected'     => 'bg-danger text-white',
                                    'cancelled'    => 'bg-secondary text-white',
                                    'failed'       => 'bg-danger text-white',
                                ];
                                $sClass = $statusBadges[$wd->status] ?? 'bg-light text-dark';
                            @endphp
                            <span class="badge {{ $sClass }} px-2 py-1 text-capitalize">
                                {{ str_replace('_', ' ', $wd->status) }}
                            </span>
                        </td>
                        <td>
                            @if($wd->transaction_reference)
                                <span class="font-monospace fw-semibold text-success small">
                                    <i class="bi bi-check-all me-1"></i>{{ $wd->transaction_reference }}
                                </span>
                            @elseif($wd->rejection_reason)
                                <span class="text-danger small" title="{{ $wd->rejection_reason }}">
                                    <i class="bi bi-x-circle me-1"></i>{{ Str::limit($wd->rejection_reason, 20) }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($wd->status === 'pending')
                                <form method="POST" action="{{ route('astrologer.withdrawals.cancel', $wd->id) }}" onsubmit="return confirm('Cancel this withdrawal request and restore held funds to available balance?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size: 0.8rem;">
                                        <i class="bi bi-x"></i> Cancel
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">Locked</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-wallet2 fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <h6 class="fw-semibold">No Payout Requests Yet</h6>
                            <p class="small mb-0">When your referral earnings mature into available balance, you can request bank/UPI disbursements here.</p>
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

@push('scripts')
<script>
    const availableBalance = {{ (float) ($wallet->available_balance ?? 0) }};
    const minAmount = {{ (float) $minAmount }};
    const maxAmount = {{ (float) $maxAmount }};
    const feeType = '{{ $feeType }}';
    const feeValue = {{ (float) $feeValue }};

    function toggleMethod(method) {
        const upiBox = document.getElementById('upiFields');
        const bankBox = document.getElementById('bankFields');
        if (method === 'upi') {
            upiBox.classList.remove('d-none');
            bankBox.classList.add('d-none');
        } else {
            upiBox.classList.add('d-none');
            bankBox.classList.remove('d-none');
        }
    }

    function setMaxAmount() {
        const maxAllowed = Math.min(availableBalance, maxAmount);
        const input = document.getElementById('withdrawAmount');
        input.value = maxAllowed.toFixed(2);
        calculateFee();
    }

    function calculateFee() {
        const input = document.getElementById('withdrawAmount');
        const val = parseFloat(input.value) || 0;
        let fee = 0;
        if (feeType === 'percent') {
            fee = (val * feeValue) / 100;
        } else {
            fee = feeValue;
        }
        const net = Math.max(0, val - fee);
        document.getElementById('displayFee').innerText = '₹' + fee.toFixed(2);
        document.getElementById('displayNet').innerText = '₹' + net.toFixed(2);
    }

    document.getElementById('withdrawAmount')?.addEventListener('input', calculateFee);
</script>
@endpush
@endsection
