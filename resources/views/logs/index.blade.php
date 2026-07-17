@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
    /* Styling Dasar Logs */
    .filter-wrapper { transition: all 0.3s ease; border-radius: 16px; }
    .table-logs th { background: transparent !important; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
    .table-logs td { vertical-align: middle; }
    .log-time-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; font-weight: 600; }
    
    /* LIGHT MODE Overrides */
    [data-bs-theme="light"] .filter-wrapper { background: linear-gradient(145deg, #ffffff, #f8f9fa); border: 1px solid #e9ecef; }
    [data-bs-theme="light"] .table-logs th { color: #a1a5b7; border-bottom: 2px dashed #eff2f5 !important; }
    [data-bs-theme="light"] .table-logs td { border-bottom: 1px dashed #eff2f5; }
    [data-bs-theme="light"] .log-time-badge { background: #f1f1f4; color: #7e8299; }
    
    /* DARK MODE Overrides */
    [data-bs-theme="dark"] .filter-wrapper { background: #1f2937; border: 1px solid #374151; }
    [data-bs-theme="dark"] .table-logs th { color: #9ca3af; border-bottom: 2px dashed #4b5563 !important; }
    [data-bs-theme="dark"] .table-logs td { border-bottom: 1px dashed #4b5563; }
    [data-bs-theme="dark"] .log-time-badge { background: #374151; color: #d1d5db; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bolder text-body-emphasis mb-1">Riwayat Log Server</h3>
            <p class="text-muted mb-0">Catatan detail setiap proses ping yang dilakukan oleh sistem</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('export.csv') }}" class="btn btn-outline-secondary shadow-sm fw-medium rounded-pill px-4 bg-body">
                <i class="bi bi-file-earmark-spreadsheet text-success me-1"></i> CSV
            </a>
            <a href="{{ route('export.json') }}" class="btn btn-outline-secondary shadow-sm fw-medium rounded-pill px-4 bg-body">
                <i class="bi bi-braces text-primary me-1"></i> JSON
            </a>
        </div>
    </div>

    <div class="filter-wrapper p-4 shadow-sm mb-4">
        <form method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Cari Nama Web</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0 bg-body text-body-emphasis" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Filter Status</label>
                    <select name="status" class="form-select bg-body text-body-emphasis">
                        <option value="">-- Semua Status --</option>
                        <option value="UP" {{ request('status') == 'UP' ? 'selected' : '' }}>🟢 UP (Online)</option>
                        <option value="DOWN" {{ request('status') == 'DOWN' ? 'selected' : '' }}>🔴 DOWN (Offline)</option>
                        <option value="WARN" {{ request('status') == 'WARN' ? 'selected' : '' }}>🟡 WARN (Warning)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Pilih Tanggal</label>
                    <input type="date" name="tanggal" class="form-control bg-body text-body-emphasis" value="{{ request('tanggal') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-medium">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-logs w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="ps-2 py-3">Website</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">HTTP</th>
                            <th class="py-3 text-center">Waktu Respon</th>
                            <th class="py-3">Pesan Detail</th>
                            <th class="pe-2 py-3 text-end">Waktu Pengecekan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-2 py-3 fw-bold text-body-emphasis">{{ $log->project ? $log->project->name : 'Web Terhapus' }}</td>
                            <td>
                                @if($log->status == "UP")
                                    <span class="text-success fw-bold small"><i class="bi bi-circle-fill me-1" style="font-size:8px;"></i> UP</span>
                                @elseif($log->status == "DOWN")
                                    <span class="text-danger fw-bold small"><i class="bi bi-circle-fill me-1" style="font-size:8px;"></i> DOWN</span>
                                @else
                                    <span class="text-warning fw-bold small"><i class="bi bi-circle-fill me-1" style="font-size:8px;"></i> WARN</span>
                                @endif
                            </td>
                            <td class="text-center font-monospace text-muted">{{ $log->http_code ?? '-' }}</td>
                            <td class="text-center fw-medium text-body-emphasis">
                                {{ $log->response_time ? $log->response_time.' ms' : '-' }}
                            </td>
                            <td class="small text-muted" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $log->error_message }}">
                                {{ $log->error_message ?? 'OK' }}
                            </td>
                            <td class="pe-2 text-end">
                                <span class="log-time-badge">
                                    {{ \Carbon\Carbon::parse($log->checked_at)->format('d M, H:i') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 border-bottom-0">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                    <h6>Belum ada riwayat tercatat</h6>
                                    <small>Pastikan cron job / jadwal ping sudah berjalan.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 d-flex justify-content-center">
                {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection