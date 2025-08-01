<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Providers\TenantServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant with database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $database = 'tenant_' . Str::slug($name, '_');

        $this->info("Creating tenant: {$name}");
        $this->info("Database: {$database}");

        if (Tenant::query()->where('database', $database)->exists()) {
            $this->error('Tenant with this database already exists!');
            return 1;
        }

        try {
            $tenant = Tenant::query()->create([
                'name' => $name,
                'database' => $database,
            ]);

            // Create database and run migrations
            TenantServiceProvider::createTenantDatabase($tenant);

            $this->info('Tenant created successfully!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to create tenant: ' . $e->getMessage());
            return 1;
        }
    }
}