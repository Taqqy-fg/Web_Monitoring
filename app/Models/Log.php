<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [

        'project_id',

        'route_id',

        'status',

        'http_code',

        'response_time',

        'ssl_status',

        'ssl_expired_at',

        'ssl_days_left',

        'error_message',

        'checked_at',

    ];

    protected $casts = [

        'checked_at' => 'datetime',

        'ssl_expired_at' => 'date',

    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}