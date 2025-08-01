<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $domain = $request->getHost();
        
        // Find tenant by domain
        $tenant = Tenant::query()->where('database', $domain)->first();
        
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        
        // Configure tenant database connection
        $tenant->configure();
        
        // Set default database connection to tenant
        Config::set('database.default', 'tenant');
        
        // Store tenant in request for later use
        $request->attributes->set('tenant', $tenant);
        
        return $next($request);
    }
}