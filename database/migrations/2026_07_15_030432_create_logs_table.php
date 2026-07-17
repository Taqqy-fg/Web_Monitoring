<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('route_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('status', ['UP', 'DOWN', 'WARN']);

            $table->integer('http_code')->nullable();

            $table->integer('response_time')->nullable();

            $table->string('ssl_status')->nullable();

            $table->date('ssl_expired_at')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamp('checked_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};