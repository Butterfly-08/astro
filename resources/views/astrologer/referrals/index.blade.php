@extends('astrologer.layouts.app')

@section('title', 'Referral Links & Products')
@section('page_title', 'Product Referral Links & Tools')

@section('content')
<div class="row g-4 mb-4">
    <!-- Main Referral Hub Card -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="metric-icon" style="background: rgba(245, 176, 65, 0.15); color: #D4AC0D;">
                        <i class="bi bi-link-45deg fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">General Store Referral Link</h5>
                        <p class="text-muted small mb-0">Clients who visit via this link get attributed to you for all catalog purchases</p>
                    </div>
                </div>

                <div class="bg-light p-3 rounded-3 border mb-3">
                    <label class="small text-muted fw-semibold mb-1">Your Shareable URL</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-white fw-bold font-monospace" id="generalUrl" value="{{ $generalReferralUrl }}" readonly>
                        <button class="btn btn-warning px-3 fw-bold" type="button" onclick="navigator.clipboard.writeText(document.getElementById('generalUrl').value); alert('General referral link copied!');">
                            <i class="bi bi-clipboard"></i> Copy Link
                        </button>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="https://api.whatsapp.com/send?text={{ urlencode('Consultation Remedies recommended by ' . $astrologer->display_name . ': ' . $generalReferralUrl) }}" target="_blank" class="btn btn-success btn-sm px-3">
                        <i class="bi bi-whatsapp me-1"></i> Share via WhatsApp
                    </a>
                    <button class="btn btn-outline-secondary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#qrModal" onclick="loadQr('{{ route('astrologer.referrals.qr') }}', 'General Referral QR Code')">
                        <i class="bi bi-qr-code me-1"></i> View QR Code
                    </button>
                    <span class="badge bg-light text-dark border align-self-center py-2 px-3">
                        Referral Code: <strong class="text-warning">{{ $referralCode }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Performance Overview</h6>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <span class="text-muted small">Total Link Clicks</span>
                    <span class="fw-bold fs-5">{{ $totalClicks }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <span class="text-muted small">Converted Orders</span>
                    <span class="fw-bold fs-5 text-success">{{ $totalConversions }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Conversion Rate</span>
                    <span class="fw-bold fs-5 text-primary">
                        {{ $totalClicks > 0 ? round(($totalConversions / $totalClicks) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Links Catalog -->
<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="card-header bg-white border-0 py-3">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <h5 class="fw-bold mb-0">Generate Product-Specific Referral Links</h5>
                <small class="text-muted">Recommend specific Gemstones, Rudraksha, or Yantras directly to your clients</small>
            </div>
            <div class="col-md-6">
                <form method="GET" action="{{ route('astrologer.referrals.index') }}" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search remedies..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-dark px-3">Search</button>
                    @if(request('search'))
                        <a href="{{ route('astrologer.referrals.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border rounded-3 p-3 shadow-sm hover-shadow">
                        <div class="d-flex gap-3 mb-3">
                            <div style="width: 70px; height: 70px; background: #f1f5f9; border-radius: 8px; overflow: hidden; flex-shrink: 0;" class="d-flex align-items-center justify-content-center text-muted">
                                @if($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <i class="bi bi-gem fs-2 text-secondary"></i>
                                @endif
                            </div>
                            <div>
                                <span class="badge bg-secondary-subtle text-secondary small" style="font-size:0.7rem;">
                                    {{ $product->category->name ?? 'Category' }}
                                </span>
                                <h6 class="fw-bold mb-1 text-truncate" style="max-width: 190px;" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h6>
                                <div class="small">
                                    <span class="fw-bold text-dark">₹{{ number_format($product->effective_price, 2) }}</span>
                                    <span class="badge bg-success-subtle text-success ms-1">
                                        Comm: {{ $product->est_commission_display }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light p-2 rounded small font-monospace text-truncate mb-3 border" style="font-size: 0.75rem;" id="prodUrl_{{ $product->id }}">
                            {{ $product->referral_url }}
                        </div>

                        <div class="d-flex gap-2 mt-auto">
                            <button class="btn btn-outline-primary btn-sm flex-grow-1" type="button" onclick="navigator.clipboard.writeText('{{ $product->referral_url }}'); alert('Product referral link copied!');">
                                <i class="bi bi-clipboard"></i> Copy Link
                            </button>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode('I recommend this sanctified ' . $product->name . ' for your astrological remedy: ' . $product->referral_url) }}" target="_blank" class="btn btn-outline-success btn-sm" title="Share on WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <button class="btn btn-outline-secondary btn-sm" title="Show QR Code" data-bs-toggle="modal" data-bs-target="#qrModal" onclick="loadQr('{{ route('astrologer.referrals.qr', ['product_slug' => $product->slug]) }}', '{{ addslashes($product->name) }}')">
                                <i class="bi bi-qr-code"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-5 text-center text-muted">
                    <i class="bi bi-search fs-2 d-block mb-2"></i>
                    No referral products found matching your search.
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-3">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="qrModalTitle">Referral QR Code</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">Clients can scan with their phone camera to open and automatically link to your code.</p>
                <div class="p-3 bg-white border rounded d-inline-block shadow-sm mb-3">
                    <img id="qrImage" src="" alt="QR Code" style="width: 200px; height: 200px;">
                </div>
                <div class="d-grid">
                    <button class="btn btn-warning btn-sm fw-bold" onclick="window.open(document.getElementById('qrImage').src, '_blank')">
                        <i class="bi bi-download"></i> Open / Download QR
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function loadQr(url, title) {
        document.getElementById('qrModalTitle').innerText = title;
        document.getElementById('qrImage').src = url;
    }
</script>
@endpush
