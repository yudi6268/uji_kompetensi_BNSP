@extends('layouts.app')

@section('content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.12);
    }
    .hero-gradient {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(17, 153, 142, 0.2);
    }
    .headline-img {
        height: 400px;
        object-fit: cover;
        border-radius: 1rem 1rem 0 0;
        width: 100%;
    }
    .article-img {
        height: 200px;
        object-fit: cover;
        border-radius: 1rem 1rem 0 0;
        width: 100%;
    }
    .video-player {
        width: 100%;
        border-radius: 1rem 1rem 0 0;
        background-color: #000;
        max-height: 400px;
    }
</style>

<!-- Hero Section -->
<div class="row">
    <div class="col-md-12">
        <div class="hero-gradient d-flex justify-content-center align-items-center text-center flex-wrap">
            <h2 class="fw-bold mb-0">Portal Edukasi SehatKu</h2>
        </div>
    </div>
</div>

<!-- Top Section: Headline News (Left) & Chart (Right) -->
<div class="row mb-5">
    <!-- Headline News (Proportional: col-lg-8) -->
    <div class="col-lg-8 mb-4 mb-lg-0">
        <h4 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="fas fa-newspaper text-primary me-2"></i> Berita Utama</h4>
        @if($headline)
            <div class="card glass-card border-0 h-100">
                <div class="card-body p-4">
                    <span class="badge bg-danger mb-2">Headline</span>
                    <h3 class="card-title fw-bold">{{ $headline->title }}</h3>
                    <p class="text-muted small mb-3"><i class="far fa-clock me-1"></i> {{ $headline->created_at->format('d M Y - H:i') }}</p>
                    <p class="card-text text-secondary mb-4" style="font-size: 1.1rem;">
                        {{ Str::limit($headline->content, 250) }}
                    </p>
                    
                    @php
                        $hasImages = is_array($headline->image_url) && count($headline->image_url) > 0;
                        $hasVideos = is_array($headline->video_url) && count($headline->video_url) > 0;
                        $colClass = ($hasImages && $hasVideos) ? 'col-md-6' : 'col-12';
                    @endphp

                    @if($hasImages || $hasVideos)
                        <div class="row mt-3">
                            @if($hasImages)
                                <div class="{{ $colClass }} mb-3">
                                    <h6 class="fw-bold text-muted small border-bottom pb-1"><i class="fas fa-image me-1"></i> Dokumentasi Gambar</h6>
                                    @foreach($headline->image_url as $img)
                                        <img src="{{ asset($img) }}" class="img-fluid rounded mb-2 shadow-sm" style="width: 100%; height: auto;" alt="Gambar">
                                    @endforeach
                                </div>
                            @endif

                            @if($hasVideos)
                                <div class="{{ $colClass }} mb-3">
                                    <h6 class="fw-bold text-muted small border-bottom pb-1"><i class="fas fa-video me-1"></i> Dokumentasi Video</h6>
                                    @foreach($headline->video_url as $vid)
                                        @php
                                            $isYt = \Illuminate\Support\Str::contains($vid, ['youtube.com', 'youtu.be']);
                                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $vid, $match);
                                            $ytId = $match[1] ?? '';
                                        @endphp
                                        @if($isYt && $ytId)
                                            <iframe class="video-player rounded mb-2 shadow-sm" src="https://www.youtube.com/embed/{{ $ytId }}" frameborder="0" allowfullscreen style="width:100%; aspect-ratio: 16/9;"></iframe>
                                        @else
                                            <video class="video-player rounded mb-2 shadow-sm" controls preload="metadata" style="width:100%; aspect-ratio: 16/9; background-color: #000;">
                                                <source src="{{ asset($vid) }}" type="video/mp4">
                                                Browser Anda tidak mendukung pemutar video.
                                            </video>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <a href="{{ route('article.show', $headline->id) }}" class="btn btn-primary rounded-pill px-4 mt-2">Baca Selengkapnya</a>
                </div>
            </div>
        @else
            <div class="alert alert-secondary border-0 glass-card text-center p-5 h-100 d-flex flex-column justify-content-center">
                <i class="fas fa-info-circle fa-3x mb-3 text-muted"></i>
                <h5>Belum ada berita utama</h5>
            </div>
        @endif
    </div>

    <!-- Comparative Chart (Proportional: col-lg-4) -->
    <div class="col-lg-4">
        <h4 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="fas fa-chart-pie text-success me-2"></i> Data Statistik</h4>
        <div class="card glass-card border-0 h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-center">
                <h6 class="text-center fw-bold mb-3">Penderita Jantung (Berdasarkan Usia & Gender)</h6>
                <canvas id="diseaseChart" style="max-height: 300px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- All Articles Grid -->
<div class="row mb-4">

    @forelse($articles as $article)
        <div class="col-md-4 mb-4">
            <div class="card glass-card border-0 h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold text-dark">{{ $article->title }}</h5>
                    <p class="card-text text-muted mb-3" style="font-size: 0.95rem;">
                        {{ Str::limit($article->content, 100) }}
                    </p>
                    
                    <div class="flex-grow-1 mt-3">
                        @php
                            $hasImages = is_array($article->image_url) && count($article->image_url) > 0;
                            $hasVideos = is_array($article->video_url) && count($article->video_url) > 0;
                            $colClass = ($hasImages && $hasVideos) ? 'col-6' : 'col-12';
                        @endphp

                        @if($hasImages || $hasVideos)
                            <div class="row g-2">
                                @if($hasImages)
                                    <div class="{{ $colClass }}">
                                        @foreach($article->image_url as $img)
                                            <img src="{{ asset($img) }}" class="img-fluid rounded shadow-sm mb-2" style="width: 100%; height: auto;" alt="Gambar">
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if($hasVideos)
                                    <div class="{{ $colClass }}">
                                        @foreach($article->video_url as $vid)
                                            @php
                                                $isYt = \Illuminate\Support\Str::contains($vid, ['youtube.com', 'youtu.be']);
                                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $vid, $match);
                                                $ytId = $match[1] ?? '';
                                            @endphp
                                            @if($isYt && $ytId)
                                                <iframe class="video-player rounded shadow-sm mb-2" style="width: 100%; aspect-ratio: 16/9;" src="https://www.youtube.com/embed/{{ $ytId }}" frameborder="0" allowfullscreen></iframe>
                                            @else
                                                <video class="video-player rounded shadow-sm mb-2" style="width: 100%; aspect-ratio: 16/9; background-color: #000;" controls preload="none">
                                                    <source src="{{ asset($vid) }}" type="video/mp4">
                                                </video>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <div class="mt-3 text-end">
                            <a href="{{ route('article.show', $article->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-muted small pt-0">
                    <i class="far fa-clock me-1"></i> {{ $article->created_at->format('d M Y') }}
                </div>
            </div>
        </div>
    @empty
        @if(!$headline)
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                <p>Belum ada berita yang dipublikasikan.</p>
            </div>
        </div>
        @endif
    @endforelse
</div>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const metrics = @json($metrics);
        
        let priaData = [0, 0, 0, 0]; // 30-40, 41-50, 51-60, 60+
        let wanitaData = [0, 0, 0, 0];

        metrics.forEach(m => {
            let ageGroup = m.age_group;
            let gender = m.gender;
            let count = parseInt(m.patient_count);
            let index = -1;
            
            if (ageGroup === '30-40') index = 0;
            else if (ageGroup === '41-50') index = 1;
            else if (ageGroup === '51-60') index = 2;
            else if (ageGroup === '60+') index = 3;

            if (index !== -1) {
                if (gender === 'Pria') priaData[index] += count;
                else if (gender === 'Wanita') wanitaData[index] += count;
            }
        });

        const ctx = document.getElementById('diseaseChart');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['30-40', '41-50', '51-60', '60+'],
                    datasets: [
                        {
                            label: 'Pria',
                            data: priaData,
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Wanita',
                            data: wanitaData,
                            backgroundColor: 'rgba(255, 99, 132, 0.7)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    family: "'Inter', sans-serif"
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
