<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Log;
use App\Services\PingService;

class MonitorController extends Controller
{
    protected $pingService;

    public function __construct(PingService $pingService)
    {
        $this->pingService = $pingService;
    }

    /**
     * Ping satu website
     */
public function ping($id)
{
    $project = Project::findOrFail($id);

    $result = $this->pingService->checkWebsite($project->monitor_url);

    Log::create([

        'project_id'      => $project->id,
        'route_id'        => null,
        'status'          => $result['status'],
        'http_code'       => $result['http_code'],
        'response_time'   => $result['response_time'],
        'ssl_status'      => $result['ssl_status'],
        'ssl_expired_at'  => $result['ssl_expired_at'],
        'ssl_days_left'   => $result['ssl_days_left'],
        'error_message'   => $result['error_message'],
        'checked_at'      => now(),

    ]);

    return redirect()->back()->with(
        'success',
        $project->name . ' berhasil diping.'
    );
}

    /**
     * Ping semua website aktif
     */
    public function pingAll()
    {
        $projects = Project::where('is_active', true)->get();

        foreach ($projects as $project) {

            $result = $this->pingService->checkWebsite($project->monitor_url);

            Log::create([

                'project_id'      => $project->id,

                'route_id'        => null,

                'status'          => $result['status'],

                'http_code'       => $result['http_code'],

                'response_time'   => $result['response_time'],

                'ssl_status'      => $result['ssl_status'],

                'ssl_expired_at'  => $result['ssl_expired_at'],

                'ssl_days_left'   => $result['ssl_days_left'],

                'error_message'   => $result['error_message'],

                'checked_at'      => now(),

            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Semua website berhasil diping.');
    }
}