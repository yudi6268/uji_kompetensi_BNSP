@extends('layouts.app')

@section('content')
<div class="container py-5 mt-4">
    <a href="{{ url('/') }}" class="btn btn-outline-secondary mb-4 rounded-pill px-4">
        <i class="fas fa-arrow-left me-2"></i> Kembali ke Beranda
    </a>

    <div class="card glass-card border-0 shadow-lg">
        <div class="card-body p-md-5">
            <div class="text-center mb-5">
                <span class="badge bg-primary mb-3 px-3 py-2 rounded-pill">Berita Kesehatan</span>
                <h1 class="fw-bold text-dark display-5">{{ $article->title }}</h1>
                <div class="text-muted mt-3">
                    <i class="far fa-clock me-1"></i> Dipublikasikan pada {{ $article->created_at->format('d M Y - H:i') }}
                </div>
            </div>

            <div class="article-content" style="font-size: 1.15rem; line-height: 1.8; color: #444;">
                {!! nl2br(e($article->content)) !!}
            </div>

            @php
                $hasImages = is_array($article->image_url) && count($article->image_url) > 0;
                $hasVideos = is_array($article->video_url) && count($article->video_url) > 0;
            @endphp

            @if($hasImages || $hasVideos)
                <hr class="my-5">
                <h4 class="fw-bold mb-4 text-center">Dokumentasi Terkait</h4>
                
                @if($hasImages)
                    <div class="row g-4 mb-5">
                        @foreach($article->image_url as $img)
                            <div class="col-md-6 col-lg-4 text-center">
                                <img src="{{ asset($img) }}" class="img-fluid rounded shadow" style="object-fit: cover; width: 100%; aspect-ratio: 4/3;" alt="Dokumentasi">
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($hasVideos)
                    <div class="row g-4">
                        @foreach($article->video_url as $vid)
                            <div class="col-md-6 text-center">
                                @php
                                    $isYt = \Illuminate\Support\Str::contains($vid, ['youtube.com', 'youtu.be']);
                                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $vid, $match);
                                    $ytId = $match[1] ?? '';
                                @endphp
                                @if($isYt && $ytId)
                                    <iframe class="rounded shadow" src="https://www.youtube.com/embed/{{ $ytId }}" frameborder="0" allowfullscreen style="width: 100%; aspect-ratio: 16/9;"></iframe>
                                @else
                                    <video class="rounded shadow" controls preload="metadata" style="width: 100%; aspect-ratio: 16/9; background-color: #000;">
                                        <source src="{{ asset($vid) }}" type="video/mp4">
                                        Browser Anda tidak mendukung pemutar video.
                                    </video>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
