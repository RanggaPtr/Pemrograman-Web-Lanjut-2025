<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param string $role = ''
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        // $user = $request->user(); // ambil data user yg login
        $user_role=$request->user()->getRole(); // ambil data level_kode dari user login

        // if($user->hasRole($role)) { // fungsi user() diambil dari UserModel.php
        //     return $next($request); // cek apakah user punya role yg diinginkan
        // }
        if (in_array($user_role,$roles)) { // cek apakah level_kode user ada didalam roles
            return $next($request); // jika ada maka lanjut
        }

        // jika tidak punya role, maka tampilkan error 403
        abort(403, 'Forbidden. Kamu tidak punya akses ke halaman ini!');
    }
}