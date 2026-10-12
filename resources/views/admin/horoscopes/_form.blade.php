<div class="row"><div class="col-xl-8">
    <form action="{{ $editing ? route('admin.horoscopes.update', $horoscope) : route('admin.horoscopes.store') }}" method="POST" class="card border-0 shadow-sm">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="zodiac_sign" class="form-label fw-semibold">Zodiac sign</label>
                    <select id="zodiac_sign" name="zodiac_sign" class="form-select @error('zodiac_sign') is-invalid @enderror" required>
                        <option value="">Select a sign</option>
                        @foreach(\App\Models\Horoscope::ZODIAC_SIGNS as $sign)
                            <option value="{{ $sign }}" @selected(old('zodiac_sign', $horoscope->zodiac_sign) === $sign)>{{ $sign }}</option>
                        @endforeach
                    </select>
                    @error('zodiac_sign')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="horoscope_date" class="form-label fw-semibold">Reading date</label>
                    <input id="horoscope_date" name="horoscope_date" type="date" class="form-control @error('horoscope_date') is-invalid @enderror" value="{{ old('horoscope_date', $horoscope->horoscope_date?->format('Y-m-d')) }}" required>
                    @error('horoscope_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="title" class="form-label fw-semibold">Title</label>
                    <input id="title" name="title" maxlength="160" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $horoscope->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="content" class="form-label fw-semibold">Reading</label>
                    <textarea id="content" name="content" rows="7" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $horoscope->content) }}</textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="draft" @selected(old('status', $horoscope->status) === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $horoscope->status) === 'published')>Published</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-save me-1"></i>{{ $editing ? 'Save Changes' : 'Create Horoscope' }}</button>
            <a href="{{ route('admin.horoscopes.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div></div>
