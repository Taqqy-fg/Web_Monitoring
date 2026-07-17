<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'monitor_url', 
        'base_url', 
        'parent_id', // <--- Pastikan ini ada!
        'company', 
        'interval_minutes', 
        'is_active', 
        'ssl_days'
    ];

    // Relasi ke Anak-anaknya (Sub-halaman)
    public function children()
    {
        return $this->hasMany(Project::class, 'parent_id');
    }

    // Relasi balik ke Induknya
    public function parent()
    {
        return $this->belongsTo(Project::class, 'parent_id');
    }

    // Relasi ke tabel Log
    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}