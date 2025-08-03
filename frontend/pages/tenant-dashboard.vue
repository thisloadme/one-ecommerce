<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <AppHeader 
      :show-breadcrumb="true" 
      breadcrumb-title="Tenant Dashboard" 
      :show-cart-button="false"
      @logout="handleLogout" 
    />

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Dashboard Header -->
      <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-900">Product Management</h1>
          <button
            @click="showCreateModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Product
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
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

        <!-- Products Cards -->
        <div v-else>
          <div v-if="products.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2-2v7m14 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m14 0H6m14 0l-3-3m-3 3l-3-3"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new product.</p>
            <div class="mt-6">
              <button
                @click="showCreateModal = true"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Product
              </button>
            </div>
          </div>

          <!-- Product Cards Grid -->
          <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div v-for="product in products" :key="product.id" class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
              <div class="p-6">
                <!-- Product Header -->
                <div class="flex items-start justify-between mb-3">
                  <h3 class="text-lg font-semibold text-gray-900 truncate pr-2">{{ product.name }}</h3>
                  <div class="flex items-center space-x-1">
                    <button
                      @click="editProduct(product)"
                      class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition-colors"
                      title="Edit Product"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click="confirmDelete(product)"
                      class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors"
                      title="Delete Product"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                
                <!-- Status Badge -->
                <div class="mb-3">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                        :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ product.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </div>
                
                <!-- Description -->
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ product.description || 'No description available' }}</p>
                
                <!-- Product Details -->
                <div class="space-y-2">
                  <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Price:</span>
                    <span class="text-lg font-semibold text-gray-900">IDR {{ product.price.toLocaleString() }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Stock:</span>
                    <span class="text-sm font-medium text-gray-900">{{ product.stock }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">SKU:</span>
                    <span class="text-sm font-mono text-gray-900">{{ product.sku }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="pagination.total > 0" class="mt-8 bg-white px-4 py-3 flex items-center justify-between border border-gray-200 rounded-lg sm:px-6">
          <div class="flex flex-col sm:flex-row sm:justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                @click="loadProducts(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>
              <button
                @click="loadProducts(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing
                  <span class="font-medium">{{ (pagination.current_page - 1) * pagination.per_page + 1 }}</span>
                  to
                  <span class="font-medium">{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}</span>
                  of
                  <span class="font-medium">{{ pagination.total }}</span>
                  results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <button
                    @click="loadProducts(pagination.current_page - 1)"
                    :disabled="pagination.current_page <= 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  
                  <template v-for="page in getVisiblePages()" :key="page">
                    <button
                      v-if="page !== '...'"
                      @click="loadProducts(page)"
                      :class="[
                        page === pagination.current_page
                          ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                          : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                      ]"
                    >
                      {{ page }}
                    </button>
                    <span
                      v-else
                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                    >
                      ...
                    </span>
                  </template>
                  
                  <button
                    @click="loadProducts(pagination.current_page + 1)"
                    :disabled="pagination.current_page >= pagination.last_page"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Create/Edit Product Modal -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 backdrop-blur-lg bg-opacity-30 overflow-y-auto h-full w-full z-50 shadow-xl">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ showEditModal ? 'Edit Product' : 'Create New Product' }}
          </h3>
          
          <form @submit.prevent="showEditModal ? updateProduct() : createProduct()" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Product Name</label>
              <input
                v-model="productForm.name"
                type="text"
                required
                class="mt-1 block w-full px-3 py-2 border text-gray-700 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter product name"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700">Description</label>
              <textarea
                v-model="productForm.description"
                rows="3"
                class="mt-1 block w-full px-3 py-2 border text-gray-700 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter product description"
              ></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Price (IDR)</label>
                <input
                  v-model="productForm.price"
                  type="number"
                  step="0.01"
                  required
                  class="mt-1 block w-full px-3 py-2 border text-gray-700 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  placeholder="0.00"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Stock</label>
                <input
                  v-model="productForm.stock"
                  type="number"
                  required
                  class="mt-1 block w-full px-3 py-2 border text-gray-700 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  placeholder="0"
                />
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700">SKU</label>
              <input
                v-model="productForm.sku"
                type="text"
                required
                class="mt-1 block w-full px-3 py-2 border text-gray-700 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter SKU"
              />
            </div>
            
            <div class="flex items-center">
              <input
                v-model="productForm.is_active"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-900">
                Active
              </label>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="formLoading"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ formLoading ? 'Saving...' : (showEditModal ? 'Update' : 'Create') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 backdrop-blur-lg shadow-xl bg-opacity-30 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mt-2">Delete Product</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">
              Are you sure you want to delete "{{ productToDelete?.name }}"? This action cannot be undone.
            </p>
          </div>
          <div class="flex justify-center space-x-3 px-4 py-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
            >
              Cancel
            </button>
            <button
              @click="deleteProduct"
              :disabled="formLoading"
              class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 disabled:opacity-50"
            >
              {{ formLoading ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

<script setup>
const { getTenantProducts, createTenantProduct, updateTenantProduct, deleteTenantProduct } = useApi()

// Authentication check
const isAuthenticated = ref(false)
const userRole = ref(null)

// Data
const products = ref([])
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0
})
const loading = ref(true)
const error = ref(null)

// Modal states
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const formLoading = ref(false)

// Form data
const productForm = ref({
  name: '',
  description: '',
  price: '',
  stock: '',
  sku: '',
  is_active: true
})

const productToDelete = ref(null)
const editingProduct = ref(null)

// Check authentication and role
function checkAuthStatus() {
  if (process.client) {
    const token = localStorage.getItem('auth_token')
    const role = localStorage.getItem('user_role')
    isAuthenticated.value = !!token
    userRole.value = role
    
    // Redirect if not authenticated or not a tenant
    if (!token || role !== 'tenant') {
      navigateTo('/login')
      return false
    }
  }
  return true
}

// Load products
async function loadProducts(page = 1) {
  try {
    loading.value = true
    error.value = null
    
    const response = await getTenantProducts(page)
    
    // Handle paginated response
    if (response.data && response.data.data) {
      products.value = response.data.data
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total
      }
    } else {
      products.value = []
      pagination.value = {
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0
      }
    }
  } catch (err) {
    console.error('Error loading products:', err)
    error.value = 'Failed to load products. Please try again.'
  } finally {
    loading.value = false
  }
}

// Create product
async function createProduct() {
  try {
    formLoading.value = true
    
    await createTenantProduct({
      name: productForm.value.name,
      description: productForm.value.description,
      price: parseFloat(productForm.value.price),
      stock: parseInt(productForm.value.stock),
      sku: productForm.value.sku,
      is_active: productForm.value.is_active
    })
    
    closeModal()
    await loadProducts()
  } catch (err) {
    console.error('Error creating product:', err)
    error.value = 'Failed to create product. Please try again.'
  } finally {
    formLoading.value = false
  }
}

// Edit product
function editProduct(product) {
  editingProduct.value = product
  productForm.value = {
    name: product.name,
    description: product.description,
    price: product.price,
    stock: product.stock,
    sku: product.sku,
    is_active: product.is_active
  }
  showEditModal.value = true
}

// Update product
async function updateProduct() {
  try {
    formLoading.value = true
    
    await updateTenantProduct(editingProduct.value.id, {
      name: productForm.value.name,
      description: productForm.value.description,
      price: parseFloat(productForm.value.price),
      stock: parseInt(productForm.value.stock),
      sku: productForm.value.sku,
      is_active: productForm.value.is_active
    })
    
    closeModal()
    await loadProducts()
  } catch (err) {
    console.error('Error updating product:', err)
    error.value = 'Failed to update product. Please try again.'
  } finally {
    formLoading.value = false
  }
}

// Confirm delete
function confirmDelete(product) {
  productToDelete.value = product
  showDeleteModal.value = true
}

// Delete product
async function deleteProduct() {
  try {
    formLoading.value = true
    
    await deleteTenantProduct(productToDelete.value.id)
    
    showDeleteModal.value = false
    productToDelete.value = null
    await loadProducts()
  } catch (err) {
    console.error('Error deleting product:', err)
    error.value = 'Failed to delete product. Please try again.'
  } finally {
    formLoading.value = false
  }
}

// Close modal
function closeModal() {
  showCreateModal.value = false
  showEditModal.value = false
  editingProduct.value = null
  productForm.value = {
    name: '',
    description: '',
    price: '',
    stock: '',
    sku: '',
    is_active: true
  }
}

// Handle logout
function handleLogout() {
  if (process.client) {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_role')
    navigateTo('/login')
  }
}

// Get visible pages for pagination
function getVisiblePages() {
  const pages = []
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  
  if (last <= 7) {
    // Show all pages if total pages <= 7
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
  } else {
    // Show first page
    pages.push(1)
    
    if (current > 4) {
      pages.push('...')
    }
    
    // Show pages around current page
    const start = Math.max(2, current - 1)
    const end = Math.min(last - 1, current + 1)
    
    for (let i = start; i <= end; i++) {
      pages.push(i)
    }
    
    if (current < last - 3) {
      pages.push('...')
    }
    
    // Show last page
    if (last > 1) {
      pages.push(last)
    }
  }
  
  return pages
}

// Initialize
onMounted(async () => {
  if (checkAuthStatus()) {
    await loadProducts()
  }
})

// Listen for auth changes
if (process.client) {
  window.addEventListener('storage', (e) => {
    if (e.key === 'auth_token' && !e.newValue) {
      navigateTo('/login')
    }
  })
}
</script>