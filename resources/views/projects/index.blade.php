@extends('layouts.app')

@section('content')
<div class="container py-4">
    
    <!-- Header Dashboard -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-body mb-1">Dashboard Monitoring</h1>
            <p class="text-muted small mb-0">
                Pantau kesehatan seluruh sistem web Anda secara realtime 
                <span class="badge bg-success-subtle text-success border border-success-subtle ms-1 px-2.5 py-1.5 rounded-pill">
                    🟢 Live Auto-Refresh (30s)
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('projects.pingAll') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                🔄 Ping Semua
            </a>
            <a href="{{ route('projects.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                ➕ Tambah URL
            </a>
        </div>
    </div>

    <!-- Fitur Pencarian / Search Box -->
    <div class="mb-4" style="max-width: 400px;">
        <form action="{{ route('dashboard') }}" method="GET">
            <div class="input-group input-group-sm shadow-sm">
                <span class="input-group-text bg-body-tertiary text-muted">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama project atau URL...">
            </div>
        </form>
    </div>

    <!-- Tabel Monitoring Utama -->
    <div class="card shadow-sm border bg-body rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-body-tertiary text-secondary border-bottom">
                    <tr>
                        <th scope="col" class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">Halaman / URL</th>
                        <th scope="col" class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">Project</th>
                        <th scope="col" class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">Status</th>
                        <th scope="col" class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">HTTP</th>
                        <th scope="col" class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">Response Time</th>
                        <th scope="col" class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">SSL (Hari)</th>
                        <th scope="col" class="px-3 py-3 small fw-bold text-uppercase" style="font-size: 0.75rem;">Terakhir Dicek</th>
                        <th scope="col" class="px-3 py-3 text-center small fw-bold text-uppercase" style="font-size: 0.75rem;">Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        @php
                            $latestLog = $project->logs()->latest()->first();
                        @endphp
                        <tr>
                            <!-- Halaman / URL -->
                            <td class="px-3 py-3">
                                <div class="fw-bold text-body mb-0" style="font-size: 0.9rem;">{{ $project->name }}</div>
                                <a href="{{ $project->monitor_url }}" target="_blank" class="text-primary small text-decoration-none break-all" style="font-size: 0.8rem;">
                                    {{ $project->monitor_url }}
                                </a>
                            </td>
                            <!-- Project -->
                            <td class="px-3 py-3 text-muted small">
                                {{ $project->company ?? '-' }}
                            </td>
                            <!-- Status -->
                            <td class="px-3 py-3">
                                @if($project->is_active === 1)
                                    <span class="badge rounded-pill bg-success-subtle text-success px-2.5 py-1">🟢 UP</span>
                                @elseif($project->is_active === 0)
                                    <span class="badge rounded-pill bg-danger-subtle text-danger px-2.5 py-1">🔴 DOWN</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary px-2.5 py-1">⚪ PENDING</span>
                                @endif
                            </td>
                            <!-- HTTP Code -->
                            <td class="px-3 py-3 font-monospace fw-bold text-body">
                                @if($project->is_active === 0 && isset($latestLog->http_code))
                                    <span class="text-danger">{{ $latestLog->http_code }}</span>
                                @else
                                    <span>{{ $latestLog->http_code ?? '-' }}</span>
                                @endif
                            </td>
                            <!-- Response Time -->
                            <td class="px-3 py-3 font-monospace text-body">
                                {{ $latestLog && $latestLog->response_time ? $latestLog->response_time . ' ms' : '-' }}
                            </td>
                            <!-- SSL -->
                            <td class="px-3 py-3">
                                @if(is_null($project->ssl_days))
                                    <span class="text-muted small">-</span>
                                @else
                                    <span class="badge {{ $project->ssl_days < 7 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} px-2 py-1">
                                        {{ $project->ssl_days }} Hari
                                    </span>
                                @endif
                            </td>
                            <!-- Terakhir Dicek -->
                            <td class="px-3 py-3 text-muted small">
                                {{ $latestLog ? \Carbon\Carbon::parse($latestLog->checked_at)->diffForHumans() : 'Belum pernah' }}
                            </td>
                            <!-- Tombol Aksi -->
                            <td class="px-3 py-3 text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-info btn-sm text-white px-2 py-1" style="font-size: 0.75rem;">Detail</a>
                                    <a href="{{ route('projects.ping', $project->id) }}" class="btn btn-primary btn-sm px-2 py-1" style="font-size: 0.75rem;">Ping</a>
                                    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning btn-sm text-white px-2 py-1" style="font-size: 0.75rem;">Edit</a>
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline mb-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus website ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm px-2 py-1" style="font-size: 0.75rem;">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted small">
                                📭 Tidak ada data website yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- FIX: Mengunci Pagination menggunakan template Bootstrap 5 murni -->
        @if($projects->hasPages())
            <div class="card-footer bg-transparent border-top border-light-subtle py-3 d-flex justify-content-center">
                {{ $projects->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection