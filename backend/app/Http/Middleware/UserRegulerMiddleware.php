<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class UserRegulerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ctoken = $request->header('ctoken');
        if (empty($ctoken)) {
            return $next($request);
        }

        $userId = UserToken::query()
            ->where('token', $ctoken)
            ->where('expires_at', '>', now())
            ->value('user_id');
        if (!$userId) {
            abort(401, 'Unauthorized');
        }

        $userData = User::query()
            ->where('id', $userId)
            ->where('role', 'user')
            ->first();
        if (!$userData) {
            abort(404, 'User not found');
        }

        $request->attributes->set('user', $userData);

        return $next($request);
    }
}