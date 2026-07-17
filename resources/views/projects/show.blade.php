@extends('layouts.app')

@section('content')
<div class="container py-4">
    
    <!-- Top Navigasi & Action Buttons -->
    <div class="d-flex flex-column sm:flex-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <a href="{{ route('dashboard') }}" class="text-muted small text-decoration-none mb-2 d-inline-block">
                ⬅️ Kembali ke Dashboard
            </a>
            <h1 class="h3 fw-bold text-body mb-1">Detail Monitoring: {{ $project->name }}</h1>
            <p class="text-muted small mb-0 break-all">URL Utama: 
                <a href="{{ $project->monitor_url }}" target="_blank" class="text-primary text-decoration-none fw-semibold">{{ $project->monitor_url }}</a>
            </p>
        </div>
        <div class="d-flex gap-2 align-self-start align-self-sm-center">
            <a href="{{ route('projects.ping', $project->id) }}?single=1" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                ▶️ Ping Semua Sub-Halaman
            </a>
            <a href="{{ route('projects.export', $project->id) }}" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1">
                📄 Export CSV Gabungan
            </a>
        </div>
    </div>

    <!-- Grid Layout Atas: Info vs Grafik -->
    <div class="row g-4 mb-4">
        <!-- Box Kiri: Informasi Utama Website -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border bg-body rounded-3 h-100 p-2">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-body border-bottom pb-2 mb-3" style="font-size: 1rem;">
                        Informasi Website
                    </h5>
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Nama Perusahaan/Project:</small>
                            <span class="text-body-secondary small fw-medium">{{ $project->company ?? 'Web Monitoring' }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Status Terakhir:</small>
                            <span class="badge rounded-pill mt-1 {{ $project->is_active === 1 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-2.5 py-1">
                                {{ $project->is_active === 1 ? '🟢 UP' : '🔴 DOWN' }}
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Masa Aktif SSL:</small>
                            <span class="h5 fw-bold text-body d-block mt-1 mb-0">
                                {{ $project->ssl_days !== null ? $project->ssl_days . ' Hari' : '-' }}
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Jumlah Sub-Halaman Dipantau:</small>
                            <span class="badge bg-primary-subtle text-primary mt-1 px-2.5 py-1">
                                {{ $children->count() }} Sub-Halaman
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Box Kanan: Grafik Kurva Response Time -->
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border bg-body rounded-3 h-100 p-2">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-body mb-3" style="font-size: 1rem;">
                        Grafik Response Time Gabungan (15 Cek Terakhir)
                    </h5>
                    <div style="position: relative; height: 220px; width: 100%;">
                        <canvas id="responseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Tengah: Tabel Mengelola Sub-Halaman -->
    <div class="card shadow-sm border bg-body rounded-3 overflow-hidden mb-4">
        <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="card-title fw-bold text-body mb-0" style="font-size: 0.95rem;">
                🔗 Sub-Halaman yang Dipantau (URL Anak)
            </h5>
            <button onclick="document.getElementById('form-anak').classList.toggle('d-none')" class="btn btn-success btn-sm">
                ➕ Tambah Sub-Halaman
            </button>
        </div>

        <!-- Form Tambah Sub Halaman -->
        <div id="form-anak" class="d-none card-body bg-body-tertiary border-bottom py-3">
            <form action="{{ route('projects.storeChild', $project->id) }}" method="POST" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-5">
                    <input type="text" name="name" placeholder="Contoh: Halaman Login" required class="form-control form-control-sm">
                </div>
                <div class="col-md-5">
                    <input type="url" name="monitor_url" placeholder="https://example.com/login" required class="form-control form-control-sm">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-success btn-sm fw-bold">Simpan</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-body-tertiary text-secondary border-bottom">
                    <tr>
                        <th class="px-3 py-2.5 small fw-bold text-uppercase" style="font-size: 0.75rem;">Nama Bagian</th>
                        <th class="px-3 py-2.5 small fw-bold text-uppercase" style="font-size: 0.75rem;">Sub-URL</th>
                        <th class="px-3 py-2.5 small fw-bold text-uppercase" style="font-size: 0.75rem;">Status</th>
                        <th class="px-3 py-2.5 small fw-bold text-uppercase" style="font-size: 0.75rem;">SSL</th>
                        <th class="px-3 py-2.5 text-center small fw-bold text-uppercase" style="font-size: 0.75rem;">Aksi Mandiri</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Target Utama / Induk -->
                    <tr class="table-primary-subtle">
                        <td class="px-3 py-2.5">
                            <span class="badge bg-primary text-white">Induk</span>
                            <span class="fw-bold ms-2 text-body" style="font-size: 0.9rem;">{{ $project->name }}</span>
                        </td>
                        <td class="px-3 py-2.5"><a href="{{ $project->monitor_url }}" target="_blank" class="text-primary text-decoration-none small break-all">{{ $project->monitor_url }}</a></td>
                        <td class="px-3 py-2.5"><span class="badge rounded-pill bg-success-subtle text-success">🟢 UP</span></td>
                        <td class="px-3 py-2.5 text-body small">{{ $project->ssl_days ?? '-' }} Hari</td>
                        <td class="px-3 py-2.5 text-center">
                            <a href="{{ route('projects.ping', $project->id) }}?single=1" class="btn btn-outline-primary btn-sm px-2 py-0.5" style="font-size: 0.75rem;">⚡ Ping Induk Mandiri</a>
                        </td>
                    </tr>
                    
                    <!-- Anggota Sub-Halaman / Anak -->
                    @foreach($children as $child)
                        <tr>
                            <td class="px-3 py-2.5">
                                <span class="badge bg-secondary text-white">Anak</span>
                                <span class="ms-2 text-body small">{{ $child->name }}</span>
                            </td>
                            <td class="px-3 py-2.5"><a href="{{ $child->monitor_url }}" target="_blank" class="text-primary text-decoration-none small break-all">{{ $child->monitor_url }}</a></td>
                            <td class="px-3 py-2.5"><span class="badge rounded-pill bg-success-subtle text-success">🟢 UP</span></td>
                            <td class="px-3 py-2.5 text-body small">{{ $child->ssl_days ?? '-' }} Hari</td>
                            <td class="px-3 py-2.5 text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('projects.ping', $child->id) }}?single=1" class="btn btn-outline-primary btn-sm px-2 py-0.5" style="font-size: 0.75rem;">⚡ Ping Anak</a>
                                    <form action="{{ route('projects.destroy', $child->id) }}" method="POST" class="d-inline mb-0" onsubmit="return confirm('Hapus sub-halaman?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm px-2 py-0.5" style="font-size: 0.75rem;">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section Bawah: Riwayat Log Gabungan -->
    <div class="card shadow-sm border bg-body rounded-3 overflow-hidden">
        <div class="card-header bg-body-tertiary py-3 border-bottom">
            <h5 class="card-title fw-bold text-body mb-0" style="font-size: 0.95rem;">
                ⏱️ Riwayat Monitoring Gabungan
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-body-tertiary text-secondary border-bottom">
                    <tr>
                        <th class="px-3 py-3 small fw-bold text-uppercase" style="width: 5%; font-size: 0.75rem;">No</th>
                        <th class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">Halaman / Target yang di-Ping</th>
                        <th class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">Status Website</th>
                        <th class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">HTTP Code</th>
                        <th class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">Response Time</th>
                        <th class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">Tanggal & Waktu Cek</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                        <tr>
                            <td class="px-3 py-3 text-muted small">{{ $logs->firstItem() + $index }}</td>
                            <td class="px-3 py-3">
                                <div class="d-flex align-items-center gap-2 mb-0.5">
                                    @if(is_null($log->project->parent_id))
                                        <span class="badge bg-primary text-white" style="font-size: 0.65rem;">Induk</span>
                                    @else
                                        <span class="badge text-white" style="font-size: 0.65rem; background-color: #6f42c1;">Anak</span>
                                    @endif
                                    <span class="fw-bold text-body small">{{ $log->project->name }}</span>
                                </div>
                                <small class="text-muted d-block break-all" style="font-size: 0.75rem;">{{ $log->project->monitor_url }}</small>
                            </td>
                            <td class="px-3 py-3">
                                <span class="badge rounded-pill {{ $log->status === 'UP' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-2.5 py-1">
                                    {{ $log->status === 'UP' ? '🟢 UP' : '🔴 DOWN' }}
                                </span>
                            </td>
                            <td class="px-3 py-3 font-monospace fw-bold text-body small">
                                {{ $log->http_code ?? '-' }}
                            </td>
                            <td class="px-3 py-3 font-monospace text-body small">
                                {{ $log->response_time ? $log->response_time . ' ms' : '-' }}
                            </td>
                            <td class="px-3 py-3 text-muted small">
                                {{ \Carbon\Carbon::parse($log->checked_at)->translatedFormat('d M Y, H:i:s') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">
                                📭 Belum ada riwayat pengecekan untuk grup website ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- FIX: Mengunci Pagination riwayat menggunakan template Bootstrap 5 murni -->
        @if($logs->hasPages())
            <div class="card-footer bg-transparent border-top border-light-subtle py-3 d-flex justify-content-center">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<!-- ==================================================================== -->
<!-- SKRIP CHART.JS (ADAPTIF DENGAN AUTOMATIC DARK MODE BOOTSTRAP 5) -->
<!-- ==================================================================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('responseChart').getContext('2d');
        const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';
        const textColor = isDarkMode ? '#adb5bd' : '#6c757d';

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Response Time (ms)',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: isDarkMode ? 'rgba(13, 110, 253, 0.1)' : 'rgba(13, 110, 253, 0.05)',
                    borderWidth: 2, pointBackgroundColor: '#0d6efd',
                    pointBorderColor: isDarkMode ? '#212529' : '#ffffff',
                    tension: 0.3, fill: true
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: textColor } } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor }, min: 0 }
                }
            }
        });
    });
</script>
@endsection