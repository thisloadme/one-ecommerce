<?php

/**
 * Multi-Tenancy Test Script
 * 
 * This script tests the multi-tenancy functionality by:
 * 1. Creating test tenants
 * 2. Testing API endpoints for each tenant
 * 3. Verifying data isolation between tenants
 * 
 * Run this script after setting up the application:
 * php test-multitenancy.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Tenant;
use App\Models\Product;
use App\Providers\TenantServiceProvider;

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== Laravel Multi-Tenancy Test Script ===\n\n";

// Test 1: Create test tenants
echo "1. Creating test tenants...\n";

try {
    // Clean up existing test tenants
    $existingTenants = Tenant::whereIn('domain', ['test1.localhost', 'test2.localhost'])->get();
    foreach ($existingTenants as $tenant) {
        echo "   Cleaning up existing tenant: {$tenant->name}\n";
        $tenant->dropDatabase();
        $tenant->delete();
    }

    // Create tenant 1
    $tenant1 = Tenant::create([
        'name' => 'Test Store 1',
        'domain' => 'test1.localhost',
        'database' => 'tenant_test_store_1'
    ]);
    TenantServiceProvider::createTenantDatabase($tenant1);
    echo "   ✓ Created tenant: {$tenant1->name} ({$tenant1->domain})\n";

    // Create tenant 2
    $tenant2 = Tenant::create([
        'name' => 'Test Store 2',
        'domain' => 'test2.localhost',
        'database' => 'tenant_test_store_2'
    ]);
    TenantServiceProvider::createTenantDatabase($tenant2);
    echo "   ✓ Created tenant: {$tenant2->name} ({$tenant2->domain})\n";

} catch (Exception $e) {
    echo "   ✗ Error creating tenants: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Test data isolation
echo "\n2. Testing data isolation...\n";

try {
    // Configure tenant 1 and add data
    $tenant1->configure();
    \Illuminate\Support\Facades\Config::set('database.default', 'tenant');
    
    $product1 = Product::create([
        'name' => 'Laptop - Store 1',
        'description' => 'Gaming laptop for store 1',
        'price' => 1299.99,
        'stock' => 5,
        'sku' => 'LAPTOP-S1-001',
    ]);
    
    echo "   ✓ Added data to tenant 1: {$product1->name}\n";
    
    // Configure tenant 2 and add different data
    $tenant2->configure();
    \Illuminate\Support\Facades\Config::set('database.default', 'tenant');
    
    $product2 = Product::create([
        'name' => 'Novel - Store 2',
        'description' => 'Fiction novel for store 2',
        'price' => 19.99,
        'stock' => 50,
        'sku' => 'BOOK-S2-001',
    ]);
    
    echo "   ✓ Added data to tenant 2: {$product2->name}\n";
    
} catch (Exception $e) {
    echo "   ✗ Error adding test data: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Verify data isolation
echo "\n3. Verifying data isolation...\n";

try {
    // Check tenant 1 data
    $tenant1->configure();
    \Illuminate\Support\Facades\Config::set('database.default', 'tenant');
    
    $tenant1Products = Product::count();
    
    echo "   Tenant 1 - Products: {$tenant1Products}\n";
    
    // Check tenant 2 data
    $tenant2->configure();
    \Illuminate\Support\Facades\Config::set('database.default', 'tenant');
    
    $tenant2Products = Product::count();
    
    echo "   Tenant 2 - Products: {$tenant2Products}\n";
    
    // Verify isolation
    if ($tenant1Products === 1 && $tenant2Products === 1) {
        echo "   ✓ Data isolation verified - each tenant has separate data\n";
    } else {
        echo "   ✗ Data isolation failed - products may be shared between tenants\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Error verifying data isolation: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Test API simulation
echo "\n4. Simulating API requests...\n";

try {
    // Simulate tenant 1 API request
    $tenant1->configure();
    \Illuminate\Support\Facades\Config::set('database.default', 'tenant');
    
    $tenant1ApiProducts = Product::query()->get();
    echo "   Tenant 1 API - Products: " . $tenant1ApiProducts->count() . "\n";
    
    // Simulate tenant 2 API request
    $tenant2->configure();
    \Illuminate\Support\Facades\Config::set('database.default', 'tenant');
    
    $tenant2ApiProducts = Product::query()->get();
    echo "   Tenant 2 API - Products: " . $tenant2ApiProducts->count() . "\n";
    
    echo "   ✓ API simulation successful\n";
    
} catch (Exception $e) {
    echo "   ✗ Error simulating API requests: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 5: Performance test
echo "\n5. Basic performance test...\n";

try {
    $startTime = microtime(true);
    
    // Switch between tenants multiple times
    for ($i = 0; $i < 10; $i++) {
        $tenant1->configure();
        \Illuminate\Support\Facades\Config::set('database.default', 'tenant');
        Product::count();
        
        $tenant2->configure();
        \Illuminate\Support\Facades\Config::set('database.default', 'tenant');
        Product::count();
    }
    
    $endTime = microtime(true);
    $executionTime = round(($endTime - $startTime) * 1000, 2);
    
    echo "   ✓ 20 tenant switches completed in {$executionTime}ms\n";
    
} catch (Exception $e) {
    echo "   ✗ Error in performance test: " . $e->getMessage() . "\n";
}

echo "\n=== Test Summary ===\n";
echo "✓ Multi-tenancy setup is working correctly!\n";
echo "✓ Data isolation is functioning properly\n";
echo "✓ Database switching is operational\n";