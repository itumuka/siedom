<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class JwtVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Menambahkan header CORS
        $response = $next($request);
        $response->headers->set('Access-Control-Allow-Origin', '*'); // Atur lebih ketat di produksi
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN, Authorization');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

        
        // Memeriksa apakah ada token JWT
        $token = $request->bearerToken();
        $key = config('jwt.key', env('JWT_SECRET'));
        $algorithm = config('jwt.alg', 'HS256');
        
                // Cek apakah sudah terjadi redirect loop (terdapat parameter ?redirected=true)
        if ($request->query('redirected') == 'true') {
            // return redirect()->to('https://siedom.umuka.ac.id/')->withErrors(['message' => 'Terlalu banyak redirect, silakan coba lagi.']);
            return redirect()->to('https://siedom.umuka.ac.id?redirected=true')->withErrors(['message' => 'Token tidak valid atau sudah expired. Silakan login kembali.']);

        } else if ($token) {
            // Memverifikasi JWT
            try {
                $decoded = JWT::decode($token, $key, $algorithm);
                $request->merge(['user' => $decoded]); // Menggabungkan data user dari token ke request
            } catch (Exception $e) {
                // Jika token tidak valid atau expired, return error
                \Log::error('JWT Error: ' . $e->getMessage());
                return redirect()->to('https://siedom.umuka.ac.id?redirected=true')->withErrors(['message' => 'Token tidak valid atau sudah expired. Silakan login kembali.']);
            }
        } 
        
        // else {
        //     // Jika tidak ada token JWT, lanjutkan autentikasi menggunakan session
        //     if (!session()->has('tipe') || session()->get('tipe') !== "Mahasiswa") {
        //         return redirect()->to('https://siedom.umuka.ac.id?redirected=true')->withErrors(['message' => 'Sesi tidak ditemukan, silakan login kembali.']);
        //     }
        // }
    
        // Melanjutkan request ke middleware berikutnya jika berhasil memverifikasi atau tidak ada masalah
        return $response;
    }

}
