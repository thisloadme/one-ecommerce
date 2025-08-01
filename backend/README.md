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
- Each tenant has a separate PostgreSQL database
- Tenant information is stored in a central `tenants` table
- Database name-based tenant identification
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
- PostgreSQL 12.0 or higher
- PostgreSQL PHP extensions (pdo_pgsql, pgsql)
- Node.js (for frontend assets)

### Step 1: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 1.1: Install PostgreSQL PHP Extensions

**For XAMPP/WAMP:**
1. Open `php.ini` file (usually in `C:\xampp\php\php.ini`)
2. Uncomment these lines by removing the semicolon (`;`):
   ```
   extension=pdo_pgsql
   extension=pgsql
   ```
3. Restart Apache/Nginx

**For Laragon:**
1. Right-click Laragon tray icon → PHP → Extensions
2. Enable `pdo_pgsql` and `pgsql`
3. Restart Laragon

**Verify Installation:**
```bash
php -m | grep pgsql
```
This should show `pdo_pgsql` and `pgsql` if properly installed.

### Step 2: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 3: Database Configuration

Edit your `.env` file with your PostgreSQL database credentials:

```env
DB_CONNECTION=owner
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=one_ecommerce
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### Step 4: Create Main Database

Create the main database that will store tenant information:

```sql
CREATE DATABASE one_ecommerce;
```

### Step 5: Run Main Database Migrations

```bash
# Run migrations for the main database
php artisan migrate
```

This will create the `tenants` table in your main database.

**Note:** If you encounter "could not find driver" error, make sure PostgreSQL PHP extensions are properly installed (see Step 1.1).

## Tenant Management

### Creating a New Tenant

Use the custom Artisan command to create a new tenant:

```bash
php artisan tenant:create "Acme Corp"
```

This command will:
1. Create a new tenant record in the main database
2. Create a new PostgreSQL database for the tenant (e.g., `tenant_acme_corp`)
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
php artisan tenant:create "Store One"

# Create second tenant
php artisan tenant:create "Store Two"
```

### Step 3: Test Tenant Access

After creating tenants, you can access them by configuring your application to use the tenant database. The middleware will identify tenants based on the database name matching the domain.

For testing purposes, you can modify the middleware or create a simple route to switch between tenants programmatically.

Each tenant will have their own isolated database and data.

## API Testing

### Tenant Information

Get tenant information (configure your application to use the specific tenant first):

```bash
curl http://localhost:8000/api/tenant
```

### Categories API

```bash
# List categories
curl http://localhost:8000/api/categories

# Create a category
curl -X POST -H "Content-Type: application/json" \
  -d '{"name":"Electronics","description":"Electronic products"}' \
  http://localhost:8000/api/categories

# Get a specific category
curl http://localhost:8000/api/categories/1
```

### Products API

```bash
# List products
curl http://localhost:8000/api/products

# Create a product
curl -X POST -H "Content-Type: application/json" \
  -d '{"name":"iPhone 15","description":"Latest iPhone","price":999.99,"stock":10,"sku":"IP15-001","category_id":1}' \
  http://localhost:8000/api/products

# Get a specific product
curl http://localhost:8000/api/products/1
```

## Database Structure

### Main Database Tables

- **tenants**: Stores tenant information (id, name, database)
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

1. **"Tenant not found" error**: Make sure the tenant exists in the database and the middleware can identify the correct tenant.

2. **"could not find driver" error**: Install PostgreSQL PHP extensions (pdo_pgsql, pgsql). For XAMPP/WAMP, uncomment the extensions in php.ini. For Laragon, enable them through the menu.

3. **Database connection errors**: Verify your PostgreSQL credentials and ensure the PostgreSQL service is running.

4. **Migration errors**: Make sure you've run the main migrations first, then create tenants and run tenant migrations.

5. **PostgreSQL syntax errors**: The application uses PostgreSQL-specific syntax. Make sure you're not mixing MySQL commands.

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
2. **Database Optimization**: Configure PostgreSQL for production workloads
3. **Caching**: Enable Redis/Memcached for better performance
4. **Queue Workers**: Set up queue workers for background tasks
5. **Monitoring**: Implement proper logging and monitoring

## Contributing

Feel free to submit issues and enhancement requests!

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
