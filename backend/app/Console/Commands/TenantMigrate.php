<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Providers\TenantServiceProvider;
use Illuminate\Console\Command;
use \Illuminate\Support\Facades\Config;
use \Illuminate\Support\Facades\Artisan;

class TenantMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate {--tenant=} {--fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for tenant databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->option('tenant');
        $fresh = $this->option('fresh');

        if ($tenantId) {
            $tenant = Tenant::query()->find($tenantId);
            if (is_null($tenant)) {
                $this->error('Tenant not found!');
                return 1;
            }
            $this->migrateTenant($tenant, $fresh);
        } else {
            $tenants = Tenant::query()->get();
            if ($tenants->isEmpty()) {
                $this->info('No tenants found.');
                return 0;
            }

            $this->info('Running migrations for all tenants...');
            foreach ($tenants as $tenant) {
                $this->migrateTenant($tenant, $fresh);
            }
        }

        return 0;
    }

    /**
     * Run migrations for a specific tenant
     */
    private function migrateTenant(Tenant $tenant, bool $fresh = false)
    {
        $this->info("Migrating tenant: {$tenant->name}");

        try {
            $tenant->configure();
            Config::set('database.default', 'tenant');

            if ($fresh) {
                Artisan::call('migrate:fresh', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);
            } else {
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);
            }

            $this->info("Migrations completed for {$tenant->name}");
        } catch (\Exception $e) {
            $this->error("Failed to migrate {$tenant->name}: " . $e->getMessage());
        }
    }
}