<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware yang memblokir akses user dengan role 'staff'
 * ke route yang hanya diperuntukkan bagi Administrator.
 *
 * Cara daftarkan alias di bootstrap/app.php:
 *
 *   ->withMiddleware(function (Middleware $middleware) {
 *       $middleware->alias([
 *           'admin' => \App\Http\Middleware\CheckAdmin::class,
 *       ]);
 *   })
 */
class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Blokir jika bukan admin
        if ($user->role !== UserRole::Admin) {
            abort(403, 'Akses ditolak. Halaman ini hanya dapat diakses oleh Administrator.');
        }

        return $next($request);
    }
}
