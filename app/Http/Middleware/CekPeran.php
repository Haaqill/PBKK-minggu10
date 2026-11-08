<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekPeran
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $hasSpatieRole = false;
        try {
            $hasSpatieRole = $request->user()->hasAnyRole($roles);
        } catch (\Throwable $e) {
            // Ignore if role check fails
        }

        if (!in_array($request->user()->role, $roles, true) && !$hasSpatieRole) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}