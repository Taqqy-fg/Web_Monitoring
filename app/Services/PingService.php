<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class PingService
{
    public function checkWebsite($url)
    {
        $client = new Client([
            'timeout' => 10,
            'verify' => false,
            'allow_redirects' => true,
            'http_errors' => false,
        ]);

        try {

            $start = microtime(true);

            $response = $client->request('HEAD', $url);

            $end = microtime(true);

            $responseTime = round(($end - $start) * 1000);

            $httpCode = $response->getStatusCode();

            // Status Website
            if ($httpCode >= 200 && $httpCode < 400) {
                $status = 'UP';
            } elseif ($httpCode >= 400 && $httpCode < 500) {
                $status = 'WARN';
            } else {
                $status = 'DOWN';
            }

            // Default SSL
            $sslStatus = 'No SSL';
            $sslExpired = null;
            $sslDaysLeft = null;

            if (str_starts_with($url, 'https://')) {

                $host = parse_url($url, PHP_URL_HOST);

                $ssl = $this->getSSLInfo($host);

                $sslStatus = $ssl['status'];
                $sslExpired = $ssl['expired_at'];
                $sslDaysLeft = $ssl['days_left'];
            }

            return [

                'status' => $status,

                'http_code' => $httpCode,

                'response_time' => $responseTime,

                'ssl_status' => $sslStatus,

                'ssl_expired_at' => $sslExpired,

                'ssl_days_left' => $sslDaysLeft,

                'error_message' => null

            ];

        } catch (Exception $e) {

            return [

                'status' => 'DOWN',

                'http_code' => null,

                'response_time' => null,

                'ssl_status' => 'Error',

                'ssl_expired_at' => null,

                'ssl_days_left' => null,

                'error_message' => $e->getMessage()

            ];
        }
    }

    /**
     * Mengambil informasi SSL Website
     */
    private function getSSLInfo($host)
    {
        $context = stream_context_create([
            "ssl" => [
                "capture_peer_cert" => true,
                "verify_peer" => false,
                "verify_peer_name" => false,
            ]
        ]);

        $client = @stream_socket_client(
            "ssl://" . $host . ":443",
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$client) {

            return [

                'status' => 'No SSL',

                'expired_at' => null,

                'days_left' => null

            ];

        }

        $params = stream_context_get_params($client);

        if (!isset($params['options']['ssl']['peer_certificate'])) {

            return [

                'status' => 'No SSL',

                'expired_at' => null,

                'days_left' => null

            ];

        }

        $cert = openssl_x509_parse(
            $params['options']['ssl']['peer_certificate']
        );

        if (!$cert) {

            return [

                'status' => 'No SSL',

                'expired_at' => null,

                'days_left' => null

            ];

        }

        $expire = $cert['validTo_time_t'];

        $daysLeft = floor(($expire - time()) / 86400);

        return [

            'status' => $daysLeft > 0 ? 'Valid' : 'Expired',

            'expired_at' => date('Y-m-d', $expire),

            'days_left' => $daysLeft    

        ];
    }
}