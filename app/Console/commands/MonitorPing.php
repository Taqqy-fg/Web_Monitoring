<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Log as ProjectLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log as FileLog;
use App\Mail\ProjectStatusNotification;

class MonitorPing extends Command
{
    protected $signature = 'monitor:ping {project_id?}';
    protected $description = 'Melakukan ping otomatis ke website dan menganalisis kode error jika down';

    public function handle()
    {
        $projectId = $this->argument('project_id');

        if ($projectId) {
            $projects = Project::where('id', $projectId)->get();
        } else {
            $projects = Project::all(); 
        }

        foreach ($projects as $project) {
            $this->info("Sedang mengecek: {$project->monitor_url}");
            $this->executePing($project);
        }

        $this->info('Selesai! Semua data status dan log berhasil disimpan.');
        return Command::SUCCESS;
    }

    private function executePing(Project $project)
    {
        $startTime = microtime(true);
        $previousActiveState = $project->is_active; 
        $sslDaysLeft = $this->checkSslDays($project->monitor_url);
        $recipientEmail = env('MONITOR_NOTIFICATION_EMAIL', 'gorbon180@gmail.com');

        try {
            $response = Http::timeout(10)->get($project->monitor_url);
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $status = $response->successful() ? 'UP' : 'DOWN';
            $httpCode = $response->status(); // Ini otomatis mendeteksi 400, 404, 500, dll.

            $project->is_active = ($status === 'UP') ? 1 : 0;
            $project->ssl_days = $sslDaysLeft;
            $project->save();

            ProjectLog::create([
                'project_id' => $project->id,
                'status' => $status,
                'http_code' => $httpCode,
                'response_time' => $responseTime,
                'checked_at' => now(),
            ]);

            try {
                if ($status === 'UP') {
                    // KONDISI RECOVERY: Hanya kirim email jika sebelumnya MATI (0)
                    if ($previousActiveState === 0) {
                        Mail::to($recipientEmail)->send(new ProjectStatusNotification(
                            $project, 
                            'RECOVERY', 
                            "Website telah kembali online. HTTP Code: {$httpCode}."
                        ));
                    }
                    
                    if ($sslDaysLeft !== null && $sslDaysLeft < 7 && $sslDaysLeft > 0) {
                        Mail::to($recipientEmail)->send(new ProjectStatusNotification(
                            $project, 
                            'WARNING', 
                            "Sertifikat SSL Anda akan kedaluwarsa dalam {$sslDaysLeft} hari!"
                        ));
                    }
                } else {
                    // KONDISI DOWN DENGAN RESPON SERVER (400, 404, 500): Hanya kirim jika sebelumnya HIDUP (1)
                    if ($previousActiveState === 1 || is_null($previousActiveState)) {
                        Mail::to($recipientEmail)->send(new ProjectStatusNotification(
                            $project, 
                            'DOWN', 
                            "Website mengembalikan respon error dengan HTTP Status Code: {$httpCode}."
                        ));
                    }
                }
            } catch (\Exception $mailException) {
                FileLog::error("Gagal mengirim email: " . $mailException->getMessage());
            }

        } catch (\Exception $e) {
            // KONDISI DOWN TOTAL (RTO / DNS Error / Server Mati)
            // Kita analisis pesan errornya dan ubah menjadi kode HTTP standar
            $httpCode = $this->getHttpCodeFromException($e);

            $project->is_active = 0;
            $project->ssl_days = $sslDaysLeft;
            $project->save();

            // Catat log dengan kode hasil analisis (tidak akan NULL / 0 lagi!)
            ProjectLog::create([
                'project_id' => $project->id,
                'status' => 'DOWN',
                'http_code' => $httpCode,
                'response_time' => null,
                'checked_at' => now(),
            ]);

            try {
                // KONDISI DOWN TOTAL: Hanya kirim jika sebelumnya HIDUP (1)
                if ($previousActiveState === 1 || is_null($previousActiveState)) {
                    $errorDetails = $this->getFriendlyErrorMessage($httpCode);
                    Mail::to($recipientEmail)->send(new ProjectStatusNotification(
                        $project, 
                        'DOWN', 
                        "Gagal terhubung ke server (HTTP {$httpCode} - {$errorDetails})."
                    ));
                }
            } catch (\Exception $mailException) {
                FileLog::error("Gagal mengirim email (Down Total): " . $mailException->getMessage());
            }
        }
    }

    // Fungsi pintar untuk menerjemahkan Exception Network menjadi Kode HTTP
    private function getHttpCodeFromException(\Exception $e)
    {
        $message = strtolower($e->getMessage());

        if (str_contains($message, 'timeout') || str_contains($message, 'timed out')) {
            return 504; // Gateway Timeout (RTO)
        }
        if (str_contains($message, 'resolve') || str_contains($message, 'dns')) {
            return 502; // Bad Gateway / Masalah DNS Domain
        }
        if (str_contains($message, 'refused')) {
            return 503; // Service Unavailable / Koneksi Ditolak Server
        }
        return 500; // Generic Connection Error
    }

    // Fungsi untuk memberikan keterangan teks error di email agar informatif
    private function getFriendlyErrorMessage($code)
    {
        return match ($code) {
            504 => 'Connection Timeout / Request RTO',
            502 => 'DNS Resolution Failed / Domain Tidak Ditemukan',
            503 => 'Connection Refused / Server Menolak Koneksi',
            default => 'Internal Server Connection Failed'
        };
    }

    private function checkSslDays($url)
    {
        try {
            $originalUrl = $url;
            $url = parse_url($url, PHP_URL_HOST) ?? $url;
            $url = str_replace(['http://', 'https://'], '', $url);
            
            if (!str_starts_with($originalUrl, 'https://')) {
                return null; 
            }

            $get = stream_context_create(["ssl" => ["capture_peer_cert" => true]]);
            $read = stream_socket_client("ssl://" . $url . ":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
            
            if (!$read) return null;

            $cert = stream_context_get_params($read);
            $certinfo = openssl_x509_parse($cert["options"]["ssl"]["peer_certificate"]);
            
            return round(($certinfo['validTo_time_t'] - time()) / 86400);
        } catch (\Exception $e) {
            return null;
        }
    }
}