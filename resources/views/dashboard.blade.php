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
    .header-gradient {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border-radius: 1rem 1rem 0 0;
        border: none;
    }
    .header-gradient-primary {
        background: linear-gradient(135deg, #4A00E0 0%, #8E2DE2 100%);
        color: white;
        border-radius: 1rem 1rem 0 0;
        border: none;
    }
    .article-img {
        height: 200px;
        object-fit: cover;
        border-radius: 1rem 1rem 0 0;
    }
    .dashboard-title {
        font-weight: 700;
        background: -webkit-linear-gradient(#11998e, #38ef7d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<div class="row align-items-center mb-5">
    <div class="col-md-12">
        <h2 class="dashboard-title mb-0">Dashboard Kesehatan, {{ Auth::user()->name }}</h2>
        <p class="text-muted">Pantau grafik kesehatan Anda dan kelola berita terkini.</p>
    </div>
</div>

<div class="row mb-5">
    <div class="col-lg-3 col-md-4 mb-4">
        <div class="card glass-card h-100">
            <div class="card-header header-gradient">
                <h5 class="mb-0 fw-bold">Catat Pasien Jantung</h5>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <script>
                        alert("{{ session('success') }}");
                    </script>
                @endif
                <form action="{{ route('metrics.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelompok Usia</label>
                        <select class="form-control rounded-3" id="input_age_group">
                            <option value="30-40">30-40 Tahun</option>
                            <option value="41-50">41-50 Tahun</option>
                            <option value="51-60">51-60 Tahun</option>
                            <option value="60+">Di Atas 60 Tahun</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Kelamin</label>
                        <select class="form-control rounded-3" id="input_gender">
                            <option value="Pria">Pria</option>
                            <option value="Wanita">Wanita</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah Penderita</label>
                        <input type="number" class="form-control rounded-3" id="input_patient_count" placeholder="Contoh: 15" min="1">
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary btn-sm w-100 rounded-pill mb-4" id="btn-add-draft">+ Tambah ke Daftar</button>

                    <!-- Staging Table -->
                    <div class="table-responsive mb-4 d-none" id="draft-container">
                        <table class="table table-sm table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Usia</th>
                                    <th>Gender</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="draft-list">
                                <!-- Draft items will appear here -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="hidden-inputs-container"></div>
                    
                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-sm" id="btn-save-all" disabled>Simpan Semua Data</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-12 mb-4">
        <div class="card glass-card h-100">
            <div class="card-header header-gradient-primary">
                <h6 class="mb-0 fw-bold">Data Tersimpan</h6>
            </div>
            <div class="card-body p-2" style="max-height: 420px; overflow-y: auto;">
                @if($metrics->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-bordered text-center align-middle" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Usia</th>
                                <th>Gender</th>
                                <th>Jml</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($metrics as $m)
                            <tr>
                                <td>{{ $m->age_group }}</td>
                                <td>{{ $m->gender }}</td>
                                <td>{{ $m->patient_count }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning rounded-circle px-2" data-bs-toggle="modal" data-bs-target="#editMetricModal{{ $m->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('metrics.destroy', $m->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle px-2" onclick="return confirm('Hapus data grafik ini?')" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="text-center text-muted p-4">Belum ada data tersimpan.</div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-5 col-md-12 mb-4">
        <div class="card glass-card h-100">
            <div class="card-header header-gradient-primary">
                <h5 class="mb-0 fw-bold">Grafik Penderita Jantung (Usia & Gender)</h5>
            </div>
            <div class="card-body p-4 d-flex align-items-center justify-content-center">
                @if($metrics->count() > 0)
                    <canvas id="healthChart" style="max-height: 350px; width: 100%;"></canvas>
                @else
                    <div class="text-center text-muted">
                        <p>Belum ada data pasien.</p>
                        <small>Silakan isi form di samping untuk mulai mencatat.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<h3 class="mb-4 fw-bold text-dark border-bottom pb-2">Kelola Berita & Edukasi Terkini</h3>
<div class="row">
    @forelse($articles as $article)
        <div class="col-md-4 mb-4">
            <div class="card glass-card h-100 border-0">
                @php
                    $hasImages = is_array($article->image_url) && count($article->image_url) > 0;
                    $hasVideos = is_array($article->video_url) && count($article->video_url) > 0;
                @endphp
                
                @if($hasImages)
                    <img src="{{ asset($article->image_url[0]) }}" class="card-img-top article-img" alt="{{ $article->title }}" style="object-fit: cover; height: 200px;">
                @elseif($hasVideos)
                    @php
                        $vid = $article->video_url[0];
                        $isYt = \Illuminate\Support\Str::contains($vid, ['youtube.com', 'youtu.be']);
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $vid, $match);
                        $ytId = $match[1] ?? '';
                    @endphp
                    @if($isYt && $ytId)
                        <iframe class="card-img-top article-img" style="height: 200px; width: 100%;" src="https://www.youtube.com/embed/{{ $ytId }}" frameborder="0" allowfullscreen></iframe>
                    @else
                        <video class="card-img-top article-img" style="height: 200px; width: 100%; object-fit: cover; background-color: #000;" controls>
                            <source src="{{ asset($vid) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @endif
                @else
                    <div class="bg-light card-img-top article-img d-flex align-items-center justify-content-center" style="height: 200px;">
                        <span class="text-muted"><i class="fas fa-image fa-3x"></i></span>
                    </div>
                @endif
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold">{{ $article->title }}</h5>
                    <p class="card-text text-muted flex-grow-1">{{ Str::limit($article->content, 100) }}</p>
                    <div class="mt-3">
                        <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-outline-warning btn-sm rounded-pill px-3">Edit</a>
                        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-muted small">
                    Diperbarui {{ $article->updated_at->diffForHumans() }}
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">Belum ada berita kesehatan yang ditambahkan.</div>
        </div>
    @endforelse
</div>
<!-- Modals for Edit Metrics -->
@foreach($metrics as $m)
<div class="modal fade text-start" id="editMetricModal{{ $m->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('metrics.update', $m->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header header-gradient text-white border-0">
                <h5 class="modal-title fw-bold">Edit Data Grafik</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kelompok Usia</label>
                    <select name="age_group" class="form-select rounded-3">
                        <option value="30-40" {{ $m->age_group == '30-40' ? 'selected' : '' }}>30-40 Tahun</option>
                        <option value="41-50" {{ $m->age_group == '41-50' ? 'selected' : '' }}>41-50 Tahun</option>
                        <option value="51-60" {{ $m->age_group == '51-60' ? 'selected' : '' }}>51-60 Tahun</option>
                        <option value="60+" {{ $m->age_group == '60+' ? 'selected' : '' }}>Di Atas 60 Tahun</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jenis Kelamin</label>
                    <select name="gender" class="form-select rounded-3">
                        <option value="Pria" {{ $m->gender == 'Pria' ? 'selected' : '' }}>Pria</option>
                        <option value="Wanita" {{ $m->gender == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jumlah Penderita</label>
                    <input type="number" name="patient_count" class="form-control rounded-3" value="{{ $m->patient_count }}" min="1">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
@if($metrics->count() > 0)
<script>
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

    const ctx = document.getElementById('healthChart').getContext('2d');
    
    new Chart(ctx, {
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
</script>
@endif
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnAddDraft = document.getElementById('btn-add-draft');
        const draftContainer = document.getElementById('draft-container');
        const draftList = document.getElementById('draft-list');
        const hiddenInputsContainer = document.getElementById('hidden-inputs-container');
        const btnSaveAll = document.getElementById('btn-save-all');
        
        let drafts = [];

        function renderDrafts() {
            draftList.innerHTML = '';
            hiddenInputsContainer.innerHTML = '';
            
            if (drafts.length > 0) {
                draftContainer.classList.remove('d-none');
                btnSaveAll.disabled = false;
            } else {
                draftContainer.classList.add('d-none');
                btnSaveAll.disabled = true;
            }

            drafts.forEach((draft, index) => {
                // Add to table
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${draft.age}</td>
                    <td>${draft.gender}</td>
                    <td>${draft.count}</td>
                    <td><button type="button" class="btn btn-sm btn-danger btn-remove-draft" data-index="${index}"><i class="fas fa-times"></i></button></td>
                `;
                draftList.appendChild(tr);

                // Add hidden inputs for form submission
                hiddenInputsContainer.innerHTML += `
                    <input type="hidden" name="age_group[]" value="${draft.age}">
                    <input type="hidden" name="gender[]" value="${draft.gender}">
                    <input type="hidden" name="patient_count[]" value="${draft.count}">
                `;
            });
        }

        if (btnAddDraft) {
            btnAddDraft.addEventListener('click', function() {
                const ageInput = document.getElementById('input_age_group');
                const genderInput = document.getElementById('input_gender');
                const countInput = document.getElementById('input_patient_count');
                
                if (!countInput.value || parseInt(countInput.value) < 1) {
                    alert("Masukkan jumlah penderita yang valid (minimal 1)!");
                    return;
                }

                drafts.push({
                    age: ageInput.value,
                    gender: genderInput.value,
                    count: countInput.value
                });

                countInput.value = ''; // Reset count input
                renderDrafts();
            });

            draftList.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-remove-draft');
                if (btn) {
                    const index = parseInt(btn.getAttribute('data-index'));
                    drafts.splice(index, 1);
                    renderDrafts();
                }
            });
        }
    });
</script>
@endpush
