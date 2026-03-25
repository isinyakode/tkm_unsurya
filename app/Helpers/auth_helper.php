<?php
// JANGAN ADA NAMESPACE DI SINI

if (!function_exists('has_otoritas')) {
    function has_otoritas($role)
    {
        $session = session()->get('otoritas');
        return (is_array($session) && in_array($role, $session));
    }
}

if (!function_exists('is_mahasiswa')) {
    function is_mahasiswa()
    {
        return has_otoritas('MAHASISWA');
    }
}

if (!function_exists('is_warek')) {
    function is_warek()
    {
        return has_otoritas('WAREK III');
    }
}

if (!function_exists('is_kaplti')) {
    function is_kaplti()
    {
        return has_otoritas('KA PUSKOM');
    }
}

if (!function_exists('is_kaprodi')) {
    function is_kaprodi()
    {
        return has_otoritas('KETUA PROGRAM STUDI');
    }
}

if (!function_exists('is_kabiro')) {
    function is_kabiro()
    {
        return has_otoritas('KABIRO KEMAHASISWAAN');
    }
}

if (!function_exists('is_kabag')) {
    function is_kabag()
    {
        return has_otoritas('ORMAWA DAN ALUMNI');
    }
}

if (!function_exists('get_otoritas_verifikasi')) {
    function get_otoritas_verifikasi()
    {
        if (is_warek()) return "WAREK III";
        if (is_kabiro()) return "KABIRO KEMAHASISWAAN";
        if (is_kabag()) return "ORMAWA DAN ALUMNI";
        if (is_kaprodi()) return "KETUA PROGRAM STUDI";
        if (is_kaplti()) return "KA PUSKOM";
        return false;
    }
}
