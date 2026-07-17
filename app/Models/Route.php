<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends Model
{
    use HasFactory;

    protected $table = 'routes';

    protected $fillable = [
        'project_id',
        'route_name',
        'path',
        'method',
        'route_group',
        'is_monitor',
    ];

    protected $casts = [
        'is_monitor' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}