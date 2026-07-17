<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{

    public function index(Request $request)
    {

        $logs = Log::with('project');

        if($request->filled('search'))
        {

            $logs->whereHas('project',function($q) use($request){

                $q->where('name','like','%'.$request->search.'%');

            });

        }

        if($request->filled('status'))
        {

            $logs->where('status',$request->status);

        }

        if($request->filled('tanggal'))
        {

            $logs->whereDate(
                'checked_at',
                $request->tanggal
            );

        }

        $logs = $logs
                    ->latest()
                    ->paginate(15);

        return view(
            'logs.index',
            compact('logs')
        );

    }

    public function exportCsv()
    {

        $filename="monitoring.csv";

        $headers=[

            "Content-type"=>"text/csv",

            "Content-Disposition"=>"attachment; filename=$filename"

        ];

        $callback=function(){

            $file=fopen('php://output','w');

            fputcsv($file,[

                'Website',

                'Status',

                'HTTP',

                'Response',

                'SSL',

                'Checked At'

            ]);

            foreach(Log::with('project')->get() as $log){

                fputcsv($file,[

                    $log->project->name,

                    $log->status,

                    $log->http_code,

                    $log->response_time,

                    $log->ssl_status,

                    $log->checked_at

                ]);

            }

            fclose($file);

        };

        return response()->stream(
            $callback,
            200,
            $headers
        );

    }

    public function exportJson()
    {

        return response()->json(

            Log::with('project')->get()

        );

    }

}