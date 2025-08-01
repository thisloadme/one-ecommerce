# Laravel Multi-Tenancy E-commerce Application

This is a simple multi-tenancy Laravel application that demonstrates how to build a SaaS e-commerce platform where each tenant has their own isolated database and can be accessed via different domains.

## Features

- **Multi-Tenancy**: Each tenant has their own database
- **Domain-based Tenant Resolution**: Tenants are identified by their domain
- **Isolated Data**: Complete data separation between tenants
- **RESTful API**: Full CRUD operations for products and categories
- **Dashboard**: Simple web interface for each tenant
- **Artisan Commands**: Easy tenant management via command line

## Architecture Overview

### Multi-Tenancy Strategy

This application uses the **Database-per-Tenant** strategy:
- Each tenant has a separate MySQL database
- Tenant information is stored in a central `tenants` table
- Domain-based tenant identification
- Dynamic database connection switching

### Key Components

1. **Tenant Model** (`app/Models/Tenant.php`): Manages tenant data and database connections
2. **TenantMiddleware** (`app/Http/Middleware/TenantMiddleware.php`): Identifies tenant and switches database
3. **TenantServiceProvider** (`app/Providers/TenantServiceProvider.php`): Handles tenant operations
4. **Artisan Commands**: For tenant management
5. **Tenant-specific Models**: Product and Category models with tenant database connection

## Installation & Setup

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL 8.0 or higher
- Node.js (for frontend assets)

### Step 1: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 2: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 3: Database Configuration

Edit your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=one_ecommerce_main
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 4: Create Main Database

Create the main database that will store tenant information:

```sql
CREATE DATABASE one_ecommerce_main;
```

### Step 5: Run Main Database Migrations

```bash
# Run migrations for the main database
php artisan migrate
```

This will create the `tenants` table in your main database.

## Tenant Management

### Creating a New Tenant

Use the custom Artisan command to create a new tenant:

```bash
php artisan tenant:create "Acme Corp" "acme.localhost"
```

This command will:
1. Create a new tenant record in the main database
2. Create a new database for the tenant (e.g., `tenant_acme_corp`)
3. Run tenant-specific migrations

### Running Tenant Migrations

To run migrations for all tenants:

```bash
php artisan tenant:migrate
```

To run migrations for a specific tenant:

```bash
php artisan tenant:migrate --tenant=1
```

To run fresh migrations (drop all tables and re-run):

```bash
php artisan tenant:migrate --fresh
```

## Testing the Application

### Step 1: Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Step 2: Create Test Tenants

Create a few test tenants:

```bash
# Create first tenant
php artisan tenant:create "Store One" "store1.localhost"

# Create second tenant
php artisan tenant:create "Store Two" "store2.localhost"
```

### Step 3: Configure Local Domains

Add the following entries to your hosts file:

**Windows**: `C:\Windows\System32\drivers\etc\hosts`
**macOS/Linux**: `/etc/hosts`

```
127.0.0.1 store1.localhost
127.0.0.1 store2.localhost
```

### Step 4: Test Tenant Access

Now you can access each tenant:

- **Store One**: `http://store1.localhost:8000/dashboard`
- **Store Two**: `http://store2.localhost:8000/dashboard`

Each tenant will have their own isolated dashboard and data.

## API Testing

### Tenant Information

Get tenant information:

```bash
curl -H "Host: store1.localhost" http://localhost:8000/api/tenant
```

### Categories API

```bash
# List categories
curl -H "Host: store1.localhost" http://localhost:8000/api/categories

# Create a category
curl -X POST -H "Host: store1.localhost" -H "Content-Type: application/json" \
  -d '{"name":"Electronics","description":"Electronic products"}' \
  http://localhost:8000/api/categories

# Get a specific category
curl -H "Host: store1.localhost" http://localhost:8000/api/categories/1
```

### Products API

```bash
# List products
curl -H "Host: store1.localhost" http://localhost:8000/api/products

# Create a product
curl -X POST -H "Host: store1.localhost" -H "Content-Type: application/json" \
  -d '{"name":"iPhone 15","description":"Latest iPhone","price":999.99,"stock":10,"sku":"IP15-001","category_id":1}' \
  http://localhost:8000/api/products

# Get a specific product
curl -H "Host: store1.localhost" http://localhost:8000/api/products/1
```

## Database Structure

### Main Database Tables

- **tenants**: Stores tenant information (id, name, domain, database)
- **users**: Main application users (if needed)
- **cache**: Laravel cache table

### Tenant Database Tables

- **categories**: Product categories (id, name, slug, description, is_active)
- **products**: Products (id, name, description, price, stock, sku, category_id, is_active)

## File Structure

```
app/
├── Console/Commands/
│   ├── CreateTenant.php          # Create new tenant command
│   └── TenantMigrate.php         # Run tenant migrations command
├── Http/
│   ├── Controllers/
│   │   ├── CategoryController.php # Category CRUD operations
│   │   └── ProductController.php  # Product CRUD operations
│   └── Middleware/
│       └── TenantMiddleware.php   # Tenant identification middleware
├── Models/
│   ├── Category.php              # Category model (tenant DB)
│   ├── Product.php               # Product model (tenant DB)
│   └── Tenant.php                # Tenant model (main DB)
└── Providers/
    └── TenantServiceProvider.php  # Tenant service provider

database/
├── migrations/
│   └── 2025_08_01_130214_tenants.php # Main tenants table migration
└── migrations/tenant/
    ├── 2024_01_01_000000_create_categories_table.php
    └── 2024_01_01_000001_create_products_table.php

resources/views/
└── dashboard.blade.php           # Tenant dashboard view

routes/
└── web.php                       # Application routes
```

## Advanced Usage

### Adding New Tenant Migrations

To add new migrations for tenant databases:

1. Create migration in `database/migrations/tenant/` directory:

```bash
php artisan make:migration create_orders_table
# Move the generated file to database/migrations/tenant/
```

2. Run the migration for all tenants:

```bash
php artisan tenant:migrate
```

### Custom Tenant Operations

You can extend the `Tenant` model to add custom operations:

```php
// In app/Models/Tenant.php
public function seedSampleData()
{
    $this->configure();
    
    // Create sample categories and products
    $category = Category::create([
        'name' => 'Sample Category',
        'description' => 'This is a sample category'
    ]);
    
    Product::create([
        'name' => 'Sample Product',
        'price' => 99.99,
        'stock' => 10,
        'sku' => 'SAMPLE-001',
        'category_id' => $category->id
    ]);
}
```

## Troubleshooting

### Common Issues

1. **"Tenant not found" error**: Make sure the domain is correctly configured in your hosts file and the tenant exists in the database.

2. **Database connection errors**: Verify your database credentials and ensure the tenant databases exist.

3. **Migration errors**: Make sure you've run the main migrations first, then create tenants and run tenant migrations.

### Debugging

Enable Laravel debugging in your `.env` file:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Check the logs in `storage/logs/laravel.log` for detailed error information.

## Security Considerations

1. **Database Isolation**: Each tenant has complete database isolation
2. **Domain Validation**: The middleware validates tenant domains
3. **Input Validation**: All API endpoints include proper validation
4. **SQL Injection Protection**: Using Eloquent ORM prevents SQL injection

## Performance Optimization

1. **Database Connection Pooling**: Consider using connection pooling for production
2. **Caching**: Implement Redis caching for tenant information
3. **Database Indexing**: Add proper indexes to tenant databases
4. **Query Optimization**: Use eager loading and query optimization techniques

## Production Deployment

1. **Environment Configuration**: Set proper production environment variables
2. **Database Optimization**: Configure MySQL for production workloads
3. **Caching**: Enable Redis/Memcached for better performance
4. **Queue Workers**: Set up queue workers for background tasks
5. **Monitoring**: Implement proper logging and monitoring

## Contributing

Feel free to submit issues and enhancement requests!

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
