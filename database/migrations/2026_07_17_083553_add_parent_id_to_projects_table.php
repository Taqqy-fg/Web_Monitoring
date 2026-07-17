<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            // foreignId secara otomatis mendeteksi tipe data ID dari tabel tujuan (projects)
            $table->foreignId('parent_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('projects')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            // dropConstrainedForeignId langsung menghapus relasi sekaligus kolomnya sekaligus agar aman di SQLite/MySQL
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};