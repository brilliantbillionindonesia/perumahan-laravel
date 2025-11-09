<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WebTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Ambil token dari query / header / session
        $tokenRequest = $request->query('token')
            ?? $request->header('X-Access-Token')
            ?? session('web_token');

        // Token valid dari .env
        $validToken = config('app.web_token');

        // Jika token tidak ada atau salah
        if (!$tokenRequest || $tokenRequest !== $validToken) {
            return redirect()->route('login')->with([
                'error' => 'Akses ditolak. Token tidak valid.',
            ]);
        }

        // Simpan token ke session supaya tidak perlu kirim ulang
        session(['web_token' => $tokenRequest]);

        return $next($request);
    }
}
