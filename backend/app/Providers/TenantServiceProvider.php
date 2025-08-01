<?php

namespace App\Providers;

use App\Models\Tenant;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\TenantMigrate::class,
                \App\Console\Commands\CreateTenant::class,
            ]);
        }
    }

    /**
     * Run migrations for a specific tenant
     */
    public static function runMigrationsForTenant(Tenant $tenant)
    {
        $tenant->configure();
        Config::set('database.default', 'tenant');
        
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);
    }

    /**
     * Create tenant database and run migrations
     */
    public static function createTenantDatabase(Tenant $tenant)
    {
        $tenant->createDatabase();
        self::runMigrationsForTenant($tenant);
    }
}