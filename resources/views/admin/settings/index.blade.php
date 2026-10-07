@extends('layouts.admin')

@section('title', 'Referral & Commission Settings')
@section('page_title', 'Platform Settings & Rules')

@section('content')
<div class="row">
    <div class="col-lg-9 col-xl-8">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf

            <!-- Referral Rules -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-link-45deg text-warning me-2"></i>Referral Tracking Configuration</h5>
                    <p class="text-muted small mb-0">Manage referral cookies, attribution models, and fraud prevention settings</p>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Referral Cookie Duration (Days) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="referral_cookie_days" class="form-control" min="1" max="365" value="{{ old('referral_cookie_days', \App\Models\Setting::get('referral_cookie_days', 30)) }}" required>
                                <span class="input-group-text">days</span>
                            </div>
                            <small class="text-muted">How long a visitor's referral attribution persists after clicking.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Attribution Model <span class="text-danger">*</span></label>
                            <select name="referral_attribution_model" class="form-select form-select-sm" required>
                                <option value="last_click" {{ old('referral_attribution_model', \App\Models\Setting::get('referral_attribution_model', 'last_click')) === 'last_click' ? 'selected' : '' }}>Last-Click (Most recent astrologer gets commission)</option>
                                <option value="first_click" {{ old('referral_attribution_model', \App\Models\Setting::get('referral_attribution_model', 'last_click')) === 'first_click' ? 'selected' : '' }}>First-Click (First astrologer who introduced user)</option>
                            </select>
                            <small class="text-muted">Determines commission credit if a buyer clicks multiple referral links.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Allow Self-Referral <span class="text-danger">*</span></label>
                            <select name="referral_allow_self_referral" class="form-select form-select-sm" required>
                                <option value="false" {{ old('referral_allow_self_referral', \App\Models\Setting::get('referral_allow_self_referral', 'false')) === 'false' ? 'selected' : '' }}>No — Disallow astrologers referring their own purchases</option>
                                <option value="true" {{ old('referral_allow_self_referral', \App\Models\Setting::get('referral_allow_self_referral', 'false')) === 'true' ? 'selected' : '' }}>Yes — Allow self referrals</option>
                            </select>
                            <small class="text-muted">Prevents astrologers from earning commissions on orders made from their own user account.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Referral Code Prefix</label>
                            <input type="text" name="referral_code_prefix" class="form-control form-control-sm text-uppercase font-monospace" placeholder="e.g. ASTRO" value="{{ old('referral_code_prefix', \App\Models\Setting::get('referral_code_prefix', 'ASTRO')) }}">
                            <small class="text-muted">Prefix used when generating new astrologer partner codes.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission Calculation Rules -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-percent text-success me-2"></i>Global Commission Calculation Rules</h5>
                    <p class="text-muted small mb-0">Fallback rules applied when a product or category does not have a specific custom rate</p>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Default Commission Type <span class="text-danger">*</span></label>
                            <select name="commission_default_type" class="form-select form-select-sm" required>
                                <option value="percent" {{ old('commission_default_type', \App\Models\Setting::get('commission_default_type', 'percent')) === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('commission_default_type', \App\Models\Setting::get('commission_default_type', 'percent')) === 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Default Commission Rate / Amount <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.01" min="0" name="commission_default_value" class="form-control" value="{{ old('commission_default_value', \App\Models\Setting::get('commission_default_value', 10)) }}" required>
                                <span class="input-group-text">% / ₹</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Commission Hold Period (Days) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="commission_hold_days" class="form-control" min="0" max="90" value="{{ old('commission_hold_days', \App\Models\Setting::get('commission_hold_days', 7)) }}" required>
                                <span class="input-group-text">days</span>
                            </div>
                            <small class="text-muted">Return/refund dispute window before commission moves from pending to available.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Commission Calculation Base <span class="text-danger">*</span></label>
                            <select name="commission_base" class="form-select form-select-sm" required>
                                <option value="product_subtotal" {{ old('commission_base', \App\Models\Setting::get('commission_base', 'product_subtotal')) === 'product_subtotal' ? 'selected' : '' }}>Product Subtotal (Recommended - Excludes shipping & tax)</option>
                                <option value="subtotal_before_tax" {{ old('commission_base', \App\Models\Setting::get('commission_base', 'product_subtotal')) === 'subtotal_before_tax' ? 'selected' : '' }}>Subtotal Before Tax</option>
                                <option value="total" {{ old('commission_base', \App\Models\Setting::get('commission_base', 'product_subtotal')) === 'total' ? 'selected' : '' }}>Gross Total</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Withdrawal Settings -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-wallet2 text-primary me-2"></i>Payout & Withdrawal Settings</h5>
                    <p class="text-muted small mb-0">Thresholds, fees, and payout scheduling for astrologer balance redemptions</p>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Minimum Withdrawal Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="10" name="withdrawal_min_amount" class="form-control form-control-sm" value="{{ old('withdrawal_min_amount', \App\Models\Setting::get('withdrawal_min_amount', 500)) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Maximum Single Withdrawal Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="10" name="withdrawal_max_amount" class="form-control form-control-sm" value="{{ old('withdrawal_max_amount', \App\Models\Setting::get('withdrawal_max_amount', 50000)) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Withdrawal Fee Type <span class="text-danger">*</span></label>
                            <select name="withdrawal_fee_type" class="form-select form-select-sm" required>
                                <option value="fixed" {{ old('withdrawal_fee_type', \App\Models\Setting::get('withdrawal_fee_type', 'fixed')) === 'fixed' ? 'selected' : '' }}>Fixed Fee (₹)</option>
                                <option value="percent" {{ old('withdrawal_fee_type', \App\Models\Setting::get('withdrawal_fee_type', 'fixed')) === 'percent' ? 'selected' : '' }}>Percentage Fee (%)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Withdrawal Processing Fee <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="withdrawal_fee_value" class="form-control form-control-sm" value="{{ old('withdrawal_fee_value', \App\Models\Setting::get('withdrawal_fee_value', 0)) }}" required>
                            <small class="text-muted">Enter 0 for fee-free payouts.</small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Standard Payout Schedule Days</label>
                            <input type="text" name="withdrawal_payout_days" class="form-control form-control-sm" placeholder="e.g. Every Monday & Thursday" value="{{ old('withdrawal_payout_days', \App\Models\Setting::get('withdrawal_payout_days', 'Every Monday')) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fraud & Anti-Abuse Settings -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock-fill text-danger me-2"></i>Fraud & Anti-Abuse Protection</h5>
                    <p class="text-muted small mb-0">Safeguards against click farming, bots, and artificial commission inflation</p>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Block Repeated Clicks From Same IP <span class="text-danger">*</span></label>
                            <select name="fraud_block_same_ip" class="form-select form-select-sm" required>
                                <option value="true" {{ old('fraud_block_same_ip', \App\Models\Setting::get('fraud_block_same_ip', 'true')) === 'true' ? 'selected' : '' }}>Yes — Prevent duplicate clicks within cooling window</option>
                                <option value="false" {{ old('fraud_block_same_ip', \App\Models\Setting::get('fraud_block_same_ip', 'true')) === 'false' ? 'selected' : '' }}>No — Record all clicks</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Max Referral Clicks Per Minute Per IP <span class="text-danger">*</span></label>
                            <input type="number" name="fraud_max_clicks_per_minute" class="form-control form-control-sm" min="1" max="100" value="{{ old('fraud_max_clicks_per_minute', \App\Models\Setting::get('fraud_max_clicks_per_minute', 5)) }}" required>
                            <small class="text-muted">Threshold for rate-limiting incoming referral traffic.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-warning fw-bold px-4">
                    <i class="bi bi-check2-circle me-1"></i> Save Platform Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Info Sidebar -->
    <div class="col-lg-3 col-xl-4">
        <div class="card border-0 shadow-sm bg-primary text-white mb-4" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold"><i class="bi bi-info-circle me-1"></i> Commission Precedence</h6>
                <p class="small opacity-90 mb-2">Commissions are calculated with the following strict hierarchy:</p>
                <ol class="small ps-3 mb-0">
                    <li><strong>Product Level:</strong> Custom rate on individual item</li>
                    <li><strong>Category Level:</strong> Custom rate on product category</li>
                    <li><strong>Global Fallback:</strong> Default rate configured here</li>
                </ol>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark"><i class="bi bi-clock-history me-1"></i> Release Scheduler</h6>
                <p class="small text-muted mb-2">Commissions are released from hold via the automated schedule:</p>
                <div class="bg-light p-2 rounded small font-monospace text-dark mb-2">
                    php artisan commission:release
                </div>
                <small class="text-muted d-block">Runs automatically in production every hour to unlock matured earnings into astrologer wallets.</small>
            </div>
        </div>
    </div>
</div>
@endsection
