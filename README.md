# Multi-Tenancy E-commerce Application

A multi-tenant e-commerce application with database-per-tenant strategy.

## Backend Setup (Laravel)

### Prerequisites

- PHP 8.1+
- Composer
- PostgreSQL 12.0+
- PostgreSQL PHP extensions (pdo_pgsql, pgsql)
- Node.js

### Step 1: Navigate to Backend Directory

```bash
cd backend
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (for frontend)
npm install
```

### Step 3: Install PostgreSQL PHP Extensions

**XAMPP/WAMP:**
1. Open `php.ini` file (usually at `C:\xampp\php\php.ini`)
2. Uncomment the following lines by removing semicolon (`;`):
   ```
   extension=pdo_pgsql
   extension=pgsql
   ```
3. Restart Apache/Nginx

**Laragon:**
1. Right-click Laragon tray icon → PHP → Extensions
2. Enable `pdo_pgsql` and `pgsql`
3. Restart Laragon

**Verification:**
```bash
php -m | grep pgsql
```

### Step 4: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 5: Database Configuration

Edit `.env` file with PostgreSQL credentials:

```env
DB_CONNECTION=owner
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=one_ecommerce
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### Step 6: Create Main Database

```sql
CREATE DATABASE one_ecommerce;
```

### Step 7: Run Migrations

```bash
# Main database migration
php artisan migrate

# Testing database migration
php artisan migrate --env=testing
```

### Step 8: Run Development Server

```bash
php artisan serve
```

Application will be available at `http://localhost:8000`

## Frontend Setup (Nuxt.js)

### Step 1: Navigate to Frontend Directory

```bash
cd frontend
```

### Step 2: Install Frontend Dependencies

```bash
# Install Node.js dependencies
npm install
```

### Step 3: Frontend Environment Configuration

```bash
# Copy frontend environment file
cp .env.example .env
```

Edit `.env` file with backend API URL:

```env
VITE_API_URL=http://localhost:8000
```

### Step 4: Run Frontend Development Server

```bash
# Start Nuxt.js development server
npm run dev
```

Frontend will be available at `http://localhost:3000`

### Frontend Build Commands

```bash
# Build for production
npm run build

# Generate static site
npm run generate

# Preview production build
npm run preview

# Start production server
npm run start
```

## Testing Multi-Tenancy

### Step 1: Create New Tenants

```bash
# Create first tenant
php artisan tenant:create "Store One"

# Create second tenant
php artisan tenant:create "Store Two"
```

### Step 2: Run Multi-Tenancy Test

```bash
# Run multi-tenancy test script
php test-multitenancy.php
```

This script will:
1. Create 2 tenants with separate databases
2. Add products to each tenant
3. Verify data isolation between tenants
4. Display test results

### Step 3: Run Unit Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter TenantTest
```

## Tenant Management

### Creating New Tenant

```bash
php artisan tenant:create "Tenant Name"
```

### Running Tenant Migrations

```bash
# Migrate all tenants
php artisan tenant:migrate

# Migrate specific tenant
php artisan tenant:migrate --tenant=1

# Fresh migration
php artisan tenant:migrate --fresh
```

## Troubleshooting

### Common Issues

1. **"Tenant not found" error**: Make sure tenant exists in database
2. **"could not find driver" error**: Install PostgreSQL PHP extensions
3. **Database connection errors**: Check PostgreSQL credentials
4. **Migration errors**: Run main migration first
5. **Test failures**: Make sure testing database is created

### Debugging

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Check logs at `storage/logs/laravel.log`

## API Testing (Optional)

```bash
# Test API with curl
curl http://localhost:8000/api/tenants
curl http://localhost:8000/api/products
```
