<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeUser
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user(); // Ambil data user yang login

        // Periksa apakah user ada dan terautentikasi
        if ($user && in_array($user->getRole(), $roles)) {
            return $next($request); // Jika ada dan peran sesuai, lanjutkan
        }

        // Jika user tidak ada atau tidak punya role, redirect ke login
        return redirect('login')->with('error', 'Kamu tidak punya akses ke halaman ini!');
    }
}