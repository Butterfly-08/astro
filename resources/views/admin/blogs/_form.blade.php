<div class="row"><div class="col-xl-9">
    <form action="{{ $editing ? route('admin.blogs.update', $blog) : route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="title" class="form-label fw-semibold">Title</label>
                    <input id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $blog->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="slug" class="form-label fw-semibold">URL slug <span class="text-muted fw-normal">(optional)</span></label>
                    <input id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $blog->slug) }}" placeholder="generated-from-title">
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="excerpt" class="form-label fw-semibold">Short summary</label>
                    <textarea id="excerpt" name="excerpt" rows="2" maxlength="500" class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $blog->excerpt) }}</textarea>
                    @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="body" class="form-label fw-semibold">Article</label>
                    <textarea id="body" name="body" rows="12" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $blog->body) }}</textarea>
                    <div class="form-text">Article text is displayed as plain text; line breaks are preserved.</div>
                    @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-7">
                    <label for="cover_image" class="form-label fw-semibold">Cover image</label>
                    <input id="cover_image" name="cover_image" type="file" accept="image/*" class="form-control @error('cover_image') is-invalid @enderror">
                    @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if($blog->cover_image)<img src="{{ asset('storage/' . $blog->cover_image) }}" alt="" class="mt-2 rounded" style="max-width:180px;max-height:120px;object-fit:cover;">@endif
                </div>
                <div class="col-md-5">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="draft" @selected(old('status', $blog->status) === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $blog->status) === 'published')>Published</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <label for="published_at" class="form-label fw-semibold mt-3">Publish at</label>
                    <input id="published_at" name="published_at" type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at', $blog->published_at?->format('Y-m-d\\TH:i')) }}">
                    <div class="form-text">Leave blank to publish immediately when status is Published.</div>
                    @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-save me-1"></i>{{ $editing ? 'Save Changes' : 'Create Article' }}</button>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div></div>
