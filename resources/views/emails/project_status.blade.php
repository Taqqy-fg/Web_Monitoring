<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Status Alert</title>
    <style>
        body { font-family: 'Segoe UI', Helvetica, Arial, sans-serif; background-color: #f4f6f9; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { padding: 30px; text-align: center; color: white; }
        .header.down { background-color: #dc3545; }
        .header.recovery { background-color: #198754; }
        .header.warning { background-color: #ffc107; color: #212529; }
        .content { padding: 30px; line-height: 1.6; }
        .footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #6c757d; border-top: 1px solid #dee2e6; }
        .btn { display: inline-block; padding: 10px 20px; color: white !important; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 15px; }
        .btn.down { background-color: #dc3545; }
        .btn.recovery { background-color: #198754; }
        .btn.warning { background-color: #ffc107; color: #212529; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table td { padding: 8px 0; border-bottom: 1px solid #f2f2f2; }
        table td.label { font-weight: bold; color: #666; width: 35%; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Dinamis -->
        <div class="header {{ strtolower($statusType) }}">
            <h1 style="margin:0; font-size: 24px;">
                @if($statusType === 'DOWN')
                    🚨 WEBSITE DOWN DETECTED
                @elseif($statusType === 'RECOVERY')
                    ✅ WEBSITE RECOVERED
                @else
                    ⚠️ SYSTEM WARNING
                @endif
            </h1>
        </div>
        
        <!-- Konten Email -->
        <div class="content">
            <p>Halo Administrator,</p>
            <p>Sistem mendeteksi adanya perubahan status pada salah satu website dalam pengawasan Anda:</p>
            
            <table>
                <tr>
                    <td class="label">Nama Website</td>
                    <td><strong>{{ $project->name }}</strong></td>
                </tr>
                <tr>
                    <td class="label">URL Target</td>
                    <td><a href="{{ $project->monitor_url }}" target="_blank">{{ $project->monitor_url }}</a></td>
                </tr>
                <tr>
                    <td class="label">Status Saat Ini</td>
                    <td>
                        @if($statusType === 'DOWN')
                            <span style="color: #dc3545; font-weight: bold;">OFFLINE (DOWN)</span>
                        @elseif($statusType === 'RECOVERY')
                            <span style="color: #198754; font-weight: bold;">ONLINE (UP)</span>
                        @else
                            <span style="color: #fd7e14; font-weight: bold;">WARNING</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Penyebab / Info</td>
                    <td>{{ $issueMessage }}</td>
                </tr>
                <tr>
                    <td class="label">Waktu Kejadian</td>
                    <td>{{ now()->format('d M Y, H:i:s') }} WIB</td>
                </tr>
            </table>

            <div style="text-align: center;">
                <a href="{{ route('dashboard') }}" class="btn {{ strtolower($statusType) }}">Buka Dashboard Monitor</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Sistem ini dijalankan secara otomatis oleh WebMonitor Pro.<br>
            Harap segera lakukan pengecekan jika server terindikasi DOWN.
        </div>
    </div>
</body>
</html>