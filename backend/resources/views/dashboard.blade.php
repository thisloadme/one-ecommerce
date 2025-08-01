<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant->name }} - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ $tenant->name }} Dashboard</h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Products Card -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Products</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="products-count">Loading...</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="#" class="font-medium text-green-700 hover:text-green-900">View all products</a>
                            </div>
                        </div>
                    </div>

                    <!-- API Info Card -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">API Endpoints</dt>
                                        <dd class="text-lg font-medium text-gray-900">Available</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="/api/tenant" class="font-medium text-purple-700 hover:text-purple-900">View API info</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Endpoints Documentation -->
                <div class="mt-8 bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">API Endpoints</h3>
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-gray-700">Products</h4>
                                <ul class="mt-2 text-sm text-gray-600 space-y-1">
                                    <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/products</code> - List all products</li>
                                    <li><code class="bg-gray-100 px-2 py-1 rounded">POST /api/products</code> - Create a new product</li>
                                    <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/products/{id}</code> - Get a specific product</li>
                                    <li><code class="bg-gray-100 px-2 py-1 rounded">PUT /api/products/{id}</code> - Update a product</li>
                                    <li><code class="bg-gray-100 px-2 py-1 rounded">DELETE /api/products/{id}</code> - Delete a product</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Load statistics

        fetch('/api/products')
            .then(response => response.json())
            .then(data => {
                document.getElementById('products-count').textContent = data.total || 0;
            })
            .catch(() => {
                document.getElementById('products-count').textContent = '0';
            });
    </script>
</body>
</html>