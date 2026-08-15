@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">Tambah Berita Kesehatan</div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Judul Berita</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konten</label>
                        <textarea name="content" class="form-control" rows="5" required>{{ old('content') }}</textarea>
                    </div>
                    <!-- Images Section -->
                <div class="mb-4 p-3 border rounded bg-light">
                    <label class="form-label fw-bold">1. Media Gambar</label>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Upload Gambar dari Komputer (Bisa pilih lebih dari 1 file)</label>
                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label small text-muted">ATAU Paste Link Gambar dari Web</label>
                        <div id="image-url-container">
                            <div class="input-group mb-2 image-url-row">
                                <input type="url" name="image_urls[]" class="form-control" placeholder="https://example.com/image.jpg">
                                <button class="btn btn-outline-danger btn-remove-img-url" type="button" style="display:none;"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-img-url">+ Tambah Link Gambar Lain</button>
                    </div>
                </div>

                <!-- Videos Section -->
                <div class="mb-4 p-3 border rounded bg-light">
                    <label class="form-label fw-bold">2. Media Video (Opsional)</label>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Unggah Video dari File Anda</label>
                        <input type="file" name="videos[]" class="form-control" accept="video/mp4,video/webm" multiple>
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label small text-muted">ATAU Paste Link Video / YouTube</label>
                        <div id="video-url-container">
                            <div class="input-group mb-2 video-url-row">
                                <input type="url" name="video_urls[]" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                                <button class="btn btn-outline-danger btn-remove-vid-url" type="button" style="display:none;"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-vid-url">+ Tambah Link Video Lain</button>
                    </div>
                </div>    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('articles.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Handle adding dynamic URL inputs
    document.getElementById('btn-add-img-url').addEventListener('click', function() {
        const container = document.getElementById('image-url-container');
        const rows = container.querySelectorAll('.image-url-row');
        const newRow = rows[0].cloneNode(true);
        newRow.querySelector('input').value = '';
        newRow.querySelector('.btn-remove-img-url').style.display = 'block';
        container.appendChild(newRow);
        updateRemoveButtons(container, '.btn-remove-img-url');
    });

    document.getElementById('btn-add-vid-url').addEventListener('click', function() {
        const container = document.getElementById('video-url-container');
        const rows = container.querySelectorAll('.video-url-row');
        const newRow = rows[0].cloneNode(true);
        newRow.querySelector('input').value = '';
        newRow.querySelector('.btn-remove-vid-url').style.display = 'block';
        container.appendChild(newRow);
        updateRemoveButtons(container, '.btn-remove-vid-url');
    });

    // Handle removing dynamic URL inputs
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-img-url')) {
            const container = document.getElementById('image-url-container');
            if (container.querySelectorAll('.image-url-row').length > 1) {
                e.target.closest('.image-url-row').remove();
                updateRemoveButtons(container, '.btn-remove-img-url');
            }
        }
        if (e.target.closest('.btn-remove-vid-url')) {
            const container = document.getElementById('video-url-container');
            if (container.querySelectorAll('.video-url-row').length > 1) {
                e.target.closest('.video-url-row').remove();
                updateRemoveButtons(container, '.btn-remove-vid-url');
            }
        }
    });

    function updateRemoveButtons(container, btnClass) {
        const rows = container.querySelectorAll('.input-group');
        rows.forEach((row, index) => {
            if (rows.length > 1) {
                row.querySelector(btnClass).style.display = 'block';
            } else {
                row.querySelector(btnClass).style.display = 'none';
            }
        });
    }
</script>
@endsection
