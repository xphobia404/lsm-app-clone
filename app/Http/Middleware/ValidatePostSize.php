<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Override Laravel default ValidatePostSize agar membaca
 * upload_max_filesize / post_max_size dari php.ini yang sudah
 * kita set ke 200 MB, bukan dari nilai default PHP-nya.
 *
 * Cara kerja: raise batas di sini menjadi 210 MB (sama dengan
 * post_max_size kita) sehingga Laravel tidak memblokir sebelum
 * PHP sempat menolak sendiri.
 */
class ValidatePostSize
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maxSize = $this->getPostMaxSize();

        if ($maxSize > 0 && $request->server('CONTENT_LENGTH') > $maxSize) {
            return $this->requestEntityTooLargeResponse();
        }

        return $next($request);
    }

    /**
     * Kembalikan batas post_max_size dalam byte.
     * Ambil nilai terbesar antara php.ini dan 210 MB hardcode.
     */
    protected function getPostMaxSize(): int
    {
        $hardcoded = 210 * 1024 * 1024; // 210 MB
        $iniRaw    = ini_get('post_max_size');
        $iniBytes  = $this->iniToBytes($iniRaw);

        return max($hardcoded, $iniBytes);
    }

    protected function iniToBytes(string $val): int
    {
        $val  = trim($val);
        $unit = strtolower(substr($val, -1));
        $num  = (int) $val;

        return match ($unit) {
            'g' => $num * 1024 * 1024 * 1024,
            'm' => $num * 1024 * 1024,
            'k' => $num * 1024,
            default => $num,
        };
    }

    protected function requestEntityTooLargeResponse(): Response
    {
        return response('Request Entity Too Large.  Ukuran file melebihi batas 200 MB.', 413);
    }
}
