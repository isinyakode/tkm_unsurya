<?php

namespace App\Libraries;

use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class SsoService
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;
    private $jwtSecret;

    public function __construct()
    {
        $this->baseUrl      = env('SSO_API_URL');
        $this->clientId     = env('SSO_CLIENT_ID');
        $this->clientSecret = env('SSO_CLIENT_SECRET');
        $this->jwtSecret    = env('JWT_ACCESS_SECRET');
    }

    public function loginSso($username, $password)
    {
        $client = Services::curlrequest();

        try {
            $response = $client->post(rtrim($this->baseUrl, '/') . "/login", [
                "form_params" => [
                    "username"      => $username,
                    "password"      => $password,
                    "client_id"     => $this->clientId,
                    "client_secret" => $this->clientSecret,
                ],
                "headers" => [
                    "Accept" => "application/json"
                ],
                "http_errors" => false
            ]);
            $result = json_decode($response->getBody(), true);
            // dd($result);

            // Cek jika login berhasil
            if (isset($result['access_token'])) {
                return $result;
            }

            return null;
        } catch (\Exception $e) {
            log_message('error', 'SSO Login Error: ' . $e->getMessage());
            return null;
        }
    }

    // Tambahkan parameter $token
    public function getMahasiswaByNIM($nim, $token = null)
    {
        $client = Services::curlrequest();

        // Jika token tidak dioper, coba ambil dari session
        $refreshToken = $token ?? session()->get('refresh_token');

        if (!$refreshToken) {
            log_message('error', 'getMahasiswaByNIM: Refresh Token Kosong');
            return null;
        }

        $response = $client->get(rtrim($this->baseUrl, '/') . "/get_mahasiswa", [
            "query" => [
                "nim"           => $nim,
                "refresh_token" => $refreshToken
            ],
            "headers" => ["Accept" => "application/json"],
            "http_errors" => false
        ]);

        $result = json_decode($response->getBody(), true);

        // DEBUG: Cek apa isi response dari API sebenarnya
        // log_message('debug', 'API Response: ' . json_encode($result));

        if (isset($result['data'])) {
            return $result['data'];
        }

        return null;
    }

    public function decodeToken($token)
    {
        try {
            // Tambahkan toleransi waktu 60-120 detik
            // Ini akan mengatasi error "iat prior to..." atau "nbf prior to..."
            JWT::$leeway = 60;

            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));

            // Pastikan properti 'data' memang ada di dalam payload token Anda
            return (array) ($decoded->data ?? $decoded);
        } catch (\Exception $e) {
            // Log ini akan memberitahu Anda ALASAN sebenarnya mengapa gagal
            log_message('error', 'JWT Decode Error: ' . $e->getMessage());
            return null;
        }
    }
}
