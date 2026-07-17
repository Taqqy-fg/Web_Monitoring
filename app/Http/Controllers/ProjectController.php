<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\Http\Controllers\ProjectController;

class ProjectController extends Controller
{
    // Tampilkan Dashboard Utama (Hanya menampilkan Induk / parent_id = NULL)
    public function index(Request $request)
    {
        $search = $request->input('search');

        $projects = Project::whereNull('parent_id') // Hanya munculkan Induk di dashboard utama
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('monitor_url', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monitor_url' => 'required|url',
        ]);

        $data = $request->only(['name', 'monitor_url', 'company', 'interval_minutes']);
        $data['base_url'] = $request->monitor_url;

        if (!isset($data['interval_minutes']) || empty($data['interval_minutes'])) {
            $data['interval_minutes'] = 60;
        }

        Project::create($data);

        return redirect()->route('dashboard')->with('success', 'Website berhasil ditambahkan ke monitoring!');
    }

    // Menyimpan URL Anak (Sub-halaman) langsung dari detail Induk
    public function storeChild(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monitor_url' => 'required|url',
        ]);

        Project::create([
            'name' => $request->name,
            'monitor_url' => $request->monitor_url,
            'base_url' => $request->monitor_url,
            'parent_id' => $project->id, // Mengaitkan ke ID Induk
            'company' => $project->company,
            'interval_minutes' => $project->interval_minutes ?? 60,
        ]);

        return back()->with('success', 'Sub-halaman (URL Anak) berhasil ditambahkan!');
    }

    public function show(Project $project)
    {
        // 1. Dapatkan daftar ID gabungan (ID Induk + semua ID Anak-anaknya)
        $projectIds = $project->children()->pluck('id')->push($project->id);

        // 2. Ambil 15 log terakhir gabungan untuk grafik
        $chartLogs = Log::whereIn('project_id', $projectIds)
                    ->with('project')
                    ->latest()
                    ->take(15)
                    ->get()
                    ->reverse()
                    ->values(); // Mereset index array

        // Format label grafik dan paksa menjadi array murni
        $chartLabels = $chartLogs->map(function($log) {
            return $log->project->name . ' (' . Carbon::parse($log->checked_at)->format('H:i') . ')';
        })->values()->toArray();

        // Format data response_time dan paksa menjadi array murni
        $chartData = $chartLogs->map(function($log) {
            return $log->response_time ?? 0;
        })->values()->toArray();

        // 3. Ambil log gabungan untuk tabel riwayat (Induk + Anak)
        $logs = Log::whereIn('project_id', $projectIds)
                    ->with('project')
                    ->latest()
                    ->paginate(10);

        // 4. Ambil daftar anak untuk ditampilkan di tabel kelola sub-halaman
        $children = $project->children;

        return view('projects.show', compact('project', 'logs', 'chartLabels', 'chartData', 'children'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monitor_url' => 'required|url',
        ]);

        $data = $request->only(['name', 'monitor_url', 'company', 'interval_minutes']);
        $data['base_url'] = $request->monitor_url;

        if (!isset($data['interval_minutes']) || empty($data['interval_minutes'])) {
            $data['interval_minutes'] = 60;
        }

        $project->update($data);

        return redirect()->route('dashboard')->with('success', 'Website berhasil diperbarui!');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('dashboard')->with('success', 'Website berhasil dihapus!');
    }

    // ====================================================================
    // LOGIKA PING MANUAL (MENDELEGASIKAN KE ARTISAN COMMAND)
    // ====================================================================
    public function ping(Request $request, Project $project)
    {
        // 1. Jalankan ping ke target utama (Induk atau Anak) menggunakan Artisan Command
        Artisan::call('monitor:ping', ['project_id' => $project->id]);

        // 2. Jika project ini adalah INDUK, dan user TIDAK meminta ping mandiri (single = 1)
        // Maka otomatis ping juga semua sub-halaman (Anak) di latar belakang!
        if (is_null($project->parent_id) && !$request->has('single')) {
            foreach ($project->children as $child) {
                Artisan::call('monitor:ping', ['project_id' => $child->id]);
            }
            return back()->with('success', "Berhasil melakukan PING massal ke {$project->name} beserta seluruh sub-halamannya!");
        }

        // Jika mode single atau memang yang di-ping adalah halaman Anak
        return back()->with('success', "Berhasil melakukan PING mandiri ke {$project->name}!");
    }

    public function pingAll()
    {
        // Memanggil Artisan Command tanpa project_id untuk mem-ping seluruh database sekaligus
        Artisan::call('monitor:ping');

        return back()->with('success', 'Perintah PING massal berhasil dijalankan!');
    }

    // ====================================================================
    // EKSPOR DATA KE CSV (LOG DATABASE UNTUK LAPORAN PEMBIMBING)
    // ====================================================================
    public function export(Project $project)
    {
        $fileName = 'log_gabungan_' . str_replace(' ', '_', strtolower($project->name)) . '_' . date('Y-m-d') . '.csv';
        
        // Ekspor log gabungan Induk + Anak
        $projectIds = $project->children()->pluck('id')->push($project->id);
        $logs = Log::whereIn('project_id', $projectIds)->with('project')->latest()->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Menambahkan kolom URL target di file CSV hasil export
        $columns = ['No', 'Nama Bagian/Sub-URL', 'URL Target', 'Status Website', 'HTTP Code', 'Response Time (ms)', 'Tanggal & Waktu Cek'];

        $callback = function() use($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $index => $log) {
                fputcsv($file, [
                    $index + 1,
                    $log->project->name,
                    $log->project->monitor_url,
                    $log->status,
                    $log->http_code ?? '-',
                    $log->response_time ? $log->response_time . ' ms' : '-',
                    $log->checked_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}