<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <h1 class="text-2xl font-bold text-gray-900">One Ecommerce</h1>
          <div class="flex items-center space-x-4">
            <template v-if="!isAuthenticated">
              <NuxtLink to="/login" class="text-gray-600 hover:text-gray-900 transition-colors">
                Login
              </NuxtLink>
              <NuxtLink to="/register" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Register
              </NuxtLink>
            </template>
            <template v-else>
              <span class="text-gray-600">Welcome back!</span>
              <button 
                @click="handleLogout" 
                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors"
              >
                Logout
              </button>
            </template>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
        <div class="flex">
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Error</h3>
            <div class="mt-2 text-sm text-red-700">
              <p>{{ error }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div v-else>
        <!-- Main Layout Container - Horizontal -->
        <div class="flex flex-row gap-8">
          <!-- Tenants Section - Left Side -->
          <section class="w-1/4">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Tenants</h2>
            <div v-if="tenants.length === 0" class="text-gray-500 text-center py-8">
              No tenants available
            </div>
            <div v-else class="space-y-4">
              <div v-for="tenant in tenants" :key="tenant.id" 
                   class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-4 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ tenant.name }}</h3>
                <button @click="selectTenant(tenant)" 
                        :class="[
                          'w-full px-4 py-2 rounded-md transition-colors text-sm',
                          selectedTenant?.id === tenant.id 
                            ? 'bg-blue-600 text-white' 
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]">
                  {{ selectedTenant?.id === tenant.id ? 'Selected' : 'Select Tenant' }}
                </button>
              </div>
            </div>
          </section>

          <!-- Products Section - Right Side -->
          <section class="w-3/4">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-3xl font-bold text-gray-900">
                {{ selectedTenant ? `${selectedTenant.name} Products` : 'All Products' }}
              </h2>
              <div class="flex items-center space-x-4">
                <button v-if="selectedTenant" @click="clearTenantSelection" 
                        class="text-blue-600 hover:text-blue-800 transition-colors">
                  View All Products
                </button>
              </div>
            </div>
            
            <div v-if="products.length === 0" class="text-gray-500 text-center py-8">
              No products available
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
              <div v-for="product in products" :key="product.id" 
                   class="bg-white w-1/3 rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                <div class="p-6">
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ product.name }}</h3>
                  <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ product.description }}</p>
                  <div class="flex justify-between items-center mb-3">
                    <span class="text-2xl font-bold text-blue-600">IDR {{ product.price }}</span>
                    <span class="text-sm text-gray-500">Stock: {{ product.stock }}</span>
                  </div>
                  <div class="flex justify-between items-center mb-4">
                    <span class="text-xs text-gray-400">SKU: {{ product.sku }}</span>
                    <span :class="[
                      'px-2 py-1 rounded-full text-xs font-medium',
                      product.is_active 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-red-100 text-red-800'
                    ]">
                      {{ product.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                  <button class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Add to Cart
                  </button>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
const { getTenants, getAllProducts, getTenantProducts } = useApi()

// Reactive data
const tenants = ref([])
const products = ref([])
const selectedTenant = ref(null)
const loading = ref(true)
const error = ref(null)
const isAuthenticated = ref(false)

function checkAuthStatus() {
  if (process.client) {
    const token = localStorage.getItem('auth_token')
    isAuthenticated.value = !!token
  }
}

function handleLogout() {
  if (process.client) {
    localStorage.removeItem('auth_token')
    isAuthenticated.value = false
    // Optionally redirect to home or show success message
    window.location.reload() // Refresh to reset any authenticated state
  }
}

// Fetch initial data
onMounted(async () => {
  checkAuthStatus()
  await loadData()
})

async function loadData() {
  try {
    loading.value = true
    error.value = null
    
    // Load tenants and all products initially
    const [tenantsResponse, productsResponse] = await Promise.all([
      getTenants(),
      getAllProducts()
    ])
    
    tenants.value = tenantsResponse.data || []
    products.value = productsResponse.data || []
  } catch (err) {
    console.error('Error loading data:', err)
    error.value = 'Failed to load data. Please try again later.'
  } finally {
    loading.value = false
  }
}

async function selectTenant(tenant) {
  try {
    selectedTenant.value = tenant
    loading.value = true
    
    // Load tenant-specific products
    const response = await getTenantProducts(tenant.id)
    products.value = response.data || []
  } catch (err) {
    console.error('Error loading tenant products:', err)
    error.value = 'Failed to load tenant products.'
  } finally {
    loading.value = false
  }
}

async function clearTenantSelection() {
  try {
    selectedTenant.value = null
    loading.value = true
    
    // Load all products again
    const response = await getAllProducts()
    products.value = response.data || []
  } catch (err) {
    console.error('Error loading all products:', err)
    error.value = 'Failed to load products.'
  } finally {
    loading.value = false
  }
}

if (process.client) {
  window.addEventListener('storage', (e) => {
    if (e.key === 'auth_token') {
      checkAuthStatus()
    }
  })
}
</script>
