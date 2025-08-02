<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\UserToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class LoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ctoken = $request->header('ctoken');

        $expiresToken = UserToken::query()
            ->where('token', $ctoken)
            ->where('expires_at', '>', now())
            ->exists();
        if (!$expiresToken) {
            abort(401, 'Unauthorized');
        }

        return $next($request);
    }
}