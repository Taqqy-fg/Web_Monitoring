<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Log;
use App\Services\PingService;

class MonitorWebsite extends Command
{
    protected $signature = 'monitor:run';

    protected $description = 'Monitoring semua website';

    public function handle(PingService $ping)
    {
        $projects = Project::where('is_active',1)->get();

        $this->info("================================");
        $this->info(" Website Monitoring Started");
        $this->info("================================");

        foreach($projects as $project){

            $result = $ping->checkWebsite($project->monitor_url);

            Log::create([

                'project_id'=>$project->id,

                'status'=>$result['status'],

                'http_code'=>$result['http_code'],

                'response_time'=>$result['response_time'],

                'ssl_status'=>$result['ssl_status'],

                'ssl_expired_at'=>$result['ssl_expired_at'],

                'error_message'=>$result['error_message'],

                'checked_at'=>now()

            ]);

            $this->line(
                $project->name.
                " | ".
                $result['status']
            );

        }

        $this->info("");

        $this->info("Monitoring selesai.");

        return Command::SUCCESS;
    }
}