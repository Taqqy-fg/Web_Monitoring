<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Log;

class DashboardController extends Controller
{
   public function index()
{
    $projects = Project::with('logs')->get();

    $total = $projects->count();

    $active = Project::where('is_active',1)->count();

    $inactive = Project::where('is_active',0)->count();

    $up = 0;
    $down = 0;
    $response = [];

    foreach($projects as $project){

        $last = $project->logs->sortByDesc('checked_at')->first();

        if($last){

            if($last->status=="UP"){

                $up++;

            }else{

                $down++;

            }

            if($last->response_time){

                $response[] = $last->response_time;

            }

        }

    }

    $avg = count($response)
        ? round(array_sum($response)/count($response))
        : 0;

    $latest = Log::with('project')
                ->latest()
                ->take(10)
                ->get();

    return view('dashboard.index', compact(

        'projects',
        'total',
        'active',
        'inactive',
        'up',
        'down',
        'avg',
        'latest'

    ));
} 
}