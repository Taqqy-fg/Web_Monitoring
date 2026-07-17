@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 px-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold m-0">Dashboard Monitoring</h2>
            <div class="d-flex align-items-center gap-2 mt-1">
                <p class="text-muted m-0">Sistem monitoring website server realtime</p>
                <span class="badge bg-success bg-opacity-10 text-success small border border-success border-opacity-25 animate-pulse">
                    <i class="bi bi-broadcast"></i> Auto-Refresh (30s)
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('monitor.pingAll') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary shadow-sm">
                    <i class="bi bi-arrow-repeat"></i> Ping Semua Website
                </button>
            </form>
            <a href="{{ route('projects.create') }}" class="btn btn-success shadow-sm">
                <i class="bi bi-plus-lg"></i> Tambah Website
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0 mt-2">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 0.85rem;" class="text-uppercase text-muted">
                            <th class="ps-4 py-3">HALAMAN / URL</th>
                            <th class="py-3">PROJECT / COMPANY</th>
                            <th class="py-3">STATUS</th>
                            <th class="py-3">HTTP</th>
                            <th class="py-3">RESPONSE TIME</th>
                            <th class="py-3">SSL HARI</th>
                            <th class="py-3">UPTIME</th>
                            <th class="py-3">TERAKHIR DICEK</th>
                            <th class="pe-4 py-3 text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            @php
                                $latestLog = \App\Models\Log::where('project_id', $project->id)->latest()->first();
                                $totalLogs = \App\Models\Log::where('project_id', $project->id)->count();
                                $upLogs = \App\Models\Log::where('project_id', $project->id)->where('status', 'UP')->count();
                                $uptime = $totalLogs > 0 ? round(($upLogs / $totalLogs) * 100) : 0;
                            @endphp
                            
                            <tr class="fw-bold table-group-divider">
                                <td class="ps-4">
                                    <span class="text-primary me-1"><i class="bi bi-folder2-open"></i></span> {{ $project->name }}
                                    <div class="fw-normal text-muted small">
                                        <a href="{{ $project->monitor_url }}" target="_blank" class="text-decoration-none text-muted">
                                            {{ Str::limit($project->monitor_url, 40) }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2 py-1">
                                        {{ $project->company ?? 'Web Monitoring' }}
                                    </span>
                                </td>
                                <td>
                                    @if($project->is_active == 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">🟢 UP</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">🔴 DOWN</span>
                                    @endif
                                </td>
                                <td>
                                    @if($latestLog && $latestLog->http_code == 200)
                                        <span class="text-success">{{ $latestLog->http_code }}</span>
                                    @else
                                        <span class="text-danger">{{ $latestLog->http_code ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>{{ $latestLog && $latestLog->response_time ? $latestLog->response_time . ' ms' : '-' }}</td>
                                <td>
                                    @if(is_null($project->ssl_days))
                                        <span class="text-muted">-</span>
                                    @elseif($project->ssl_days == 0 && Str::startsWith($project->monitor_url, 'http://'))
                                        <span class="badge bg-secondary text-white small">No SSL</span>
                                    @elseif($project->ssl_days <= 7)
                                        <span class="badge bg-danger text-white">{{ $project->ssl_days }} Hari</span>
                                    @else
                                        <span class="badge bg-success text-white">{{ $project->ssl_days }} Hari</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $uptime >= 90 ? 'text-success' : 'text-warning' }}">{{ $uptime }}%</span>
                                </td>
                                <td class="text-muted small">
                                    {{ $latestLog ? \Carbon\Carbon::parse($latestLog->checked_at)->diffForHumans() : 'Belum' }}
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-info text-white shadow-sm">Detail</a>
                                        <form action="{{ route('monitor.ping', $project->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary shadow-sm">Ping</button>
                                        </form>
                                        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-warning text-dark shadow-sm">Edit</a>
                                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button onclick="return confirm('Hapus induk beserta seluruh anak-anak sub-websitenya?')" class="btn btn-sm btn-danger shadow-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            @if($project->children->count() > 0)
                                @foreach($project->children as $child)
                                    @php
                                        $childLatestLog = \App\Models\Log::where('project_id', $child->id)->latest()->first();
                                        $childTotalLogs = \App\Models\Log::where('project_id', $child->id)->count();
                                        $childUpLogs = \App\Models\Log::where('project_id', $child->id)->where('status', 'UP')->count();
                                        $childUptime = $childTotalLogs > 0 ? round(($childUpLogs / $childTotalLogs) * 100) : 0;
                                    @endphp
                                    <tr class="align-middle bg-light bg-opacity-25" style="border-left: 4px solid #0d6efd;">
                                        <td class="ps-5">
                                            <div class="d-flex align-items-center">
                                                <span class="text-muted me-2" style="font-size: 1.1rem; line-height: 1;">└─</span>
                                                <div>
                                                    <div class="fw-bold text-dark-emphasis">
                                                        {{ $child->name }} 
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 ms-1" style="font-size: 0.65rem; padding: 2px 5px;">Anak</span>
                                                    </div>
                                                    <a href="{{ $child->monitor_url }}" target="_blank" class="text-decoration-none text-muted small" style="font-size: 0.78rem;">
                                                        {{ Str::limit($child->monitor_url, 35) }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal px-2 py-1" style="font-size: 0.75rem;">
                                                Sub-Layanan
                                            </span>
                                        </td>
                                        <td>
                                            @if($child->is_active == 1)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2 py-0.5" style="font-size: 0.75rem;">🟢 UP</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-2 py-0.5" style="font-size: 0.75rem;">🔴 DOWN</span>
                                            @endif
                                        </td>
                                        <td class="small">
                                            @if($childLatestLog && $childLatestLog->http_code == 200)
                                                <span class="text-success">{{ $childLatestLog->http_code }}</span>
                                            @else
                                                <span class="text-danger">{{ $childLatestLog->http_code ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $childLatestLog && $childLatestLog->response_time ? $childLatestLog->response_time . ' ms' : '-' }}</td>
                                        <td>
                                            @if(is_null($child->ssl_days))
                                                <span class="text-muted small">-</span>
                                            @elseif($child->ssl_days == 0 && Str::startsWith($child->monitor_url, 'http://'))
                                                <span class="badge bg-secondary text-white" style="font-size: 0.7rem;">No SSL</span>
                                            @else
                                                <span class="badge bg-success text-white" style="font-size: 0.7rem;">{{ $child->ssl_days }} Hari</span>
                                            @endif
                                        </td>
                                        <td class="small">
                                            <span class="{{ $childUptime >= 90 ? 'text-success' : 'text-warning' }}">{{ $childUptime }}%</span>
                                        </td>
                                        <td class="text-muted small" style="font-size: 0.75rem;">
                                            {{ $childLatestLog ? \Carbon\Carbon::parse($childLatestLog->checked_at)->diffForHumans() : 'Belum' }}
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('projects.show', $child->id) }}" class="btn btn-xs btn-outline-info shadow-sm">Detail</a>
                                                <form action="{{ route('monitor.ping', $child->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-outline-primary shadow-sm">Ping</button>
                                                </form>
                                                <a href="{{ route('projects.edit', $child->id) }}" class="btn btn-xs btn-outline-warning shadow-sm">Edit</a>
                                                <form action="{{ route('projects.destroy', $child->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button onclick="return confirm('Hapus sub-layanan ini?')" class="btn btn-xs btn-outline-danger shadow-sm">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                    Belum ada website induk monitoring yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Otomatis refresh halaman dashboard setiap 30 detik
        setInterval(function() {
            window.location.reload();
        }, 30000);
    });
</script>

<style>
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
    .btn-xs {
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
        border-radius: 0.25rem;
    }
</style>
@endsection