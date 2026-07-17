@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted fw-medium small d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle me-3">
                            <i class="bi bi-pencil-square fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">Edit Website</h4>
                            <p class="text-muted mb-0 small">Perbarui informasi dan konfigurasi URL monitoring</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('projects.update', $project->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold text-dark">Nama Project / Website <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm-sm">
                                <span class="input-group-text bg-light border-secondary-subtle border-end-0"><i class="bi bi-window-sidebar text-muted"></i></span>
                                <input type="text" class="form-control border-secondary-subtle border-start-0 ps-0 form-control-lg fs-6" id="name" name="name" value="{{ old('name', $project->name) }}" required placeholder="Contoh: Toko Online Utama">
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="base_url" class="form-label fw-semibold text-dark">Base URL (Domain Utama) <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm-sm">
                                    <span class="input-group-text bg-light border-secondary-subtle border-end-0"><i class="bi bi-globe text-muted"></i></span>
                                    <input type="url" class="form-control border-secondary-subtle border-start-0 ps-0" id="base_url" name="base_url" value="{{ old('base_url', $project->base_url) }}" required placeholder="https://domain.com">
                                </div>
                                <div class="form-text small mt-2"><i class="bi bi-info-circle me-1"></i>Hanya alamat halaman depan web.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="monitor_url" class="form-label fw-semibold text-dark">Monitor URL (Endpoint) <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm-sm">
                                    <span class="input-group-text bg-light border-secondary-subtle border-end-0"><i class="bi bi-link-45deg text-muted"></i></span>
                                    <input type="url" class="form-control border-secondary-subtle border-start-0 ps-0" id="monitor_url" name="monitor_url" value="{{ old('monitor_url', $project->monitor_url) }}" required placeholder="https://domain.com/health">
                                </div>
                                <div class="form-text small mt-2"><i class="bi bi-info-circle me-1"></i>URL yang ditembak ping oleh sistem.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold text-dark">Keterangan</label>
                            <textarea class="form-control border-secondary-subtle shadow-sm-sm" id="description" name="description" rows="3" placeholder="Catatan tambahan terkait website ini...">{{ old('description', $project->description) }}</textarea>
                        </div>

                        <div class="mb-5 p-3 bg-light rounded-3 border border-secondary-subtle">
                            <div class="form-check form-switch d-flex align-items-center gap-2 mb-0">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input mt-0 shadow-sm" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $project->is_active) ? 'checked' : '' }} style="width: 3em; height: 1.5em; cursor: pointer;">
                                <label class="form-check-label fw-bold text-dark ms-2 mb-0" style="cursor: pointer;" for="is_active">Aktifkan Monitoring Otomatis</label>
                            </div>
                            <small class="text-muted ms-5 d-block mt-2">Jika saklar ini dimatikan, sistem tidak akan mengirim Ping ke website ini lagi.</small>
                        </div>

                        <hr class="mb-4 border-light-subtle">

                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('dashboard') }}" class="btn btn-light px-4 border shadow-sm fw-medium text-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm fw-medium">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
    /* Sedikit styling tambahan agar form input saat di-klik (focus) ada efek glow biru */
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: none;
    }
    .input-group:focus-within .input-group-text,
    .input-group:focus-within .form-control {
        border-color: #0d6efd;
    }
</style>
@endsection