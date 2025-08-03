<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <AppHeader 
      :show-breadcrumb="true" 
      breadcrumb-title="Shopping Cart" 
      :show-cart-button="false" 
    />

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

      <!-- Cart Content -->
      <div v-else>
        <!-- Empty Cart -->
        <div v-if="cartItems.length === 0" class="text-center py-16">
          <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6M20 13v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2-2v4m16 0H4"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Your cart is empty</h3>
          <p class="text-gray-500 mb-6">Start shopping to add items to your cart</p>
          <NuxtLink to="/" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors">
            Continue Shopping
          </NuxtLink>
        </div>

        <!-- Cart Items -->
        <div v-else>
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-900">Shopping Cart ({{ cartItems.length }} items)</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
              <div v-for="item in cartItems" :key="item.id" class="p-6">
                <div class="flex items-center space-x-4">
                  <!-- Product Info -->
                  <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900">{{ item.product_name }}</h3>
                    <p class="text-gray-600 text-sm mt-1">{{ item.description }}</p>
                    <div class="flex items-center mt-2 space-x-4">
                      <span class="text-sm text-gray-500">SKU: {{ item.product_sku }}</span>
                      <span class="text-lg font-bold text-blue-600">IDR {{ item.price.toLocaleString() }}</span>
                    </div>
                  </div>

                  <!-- Quantity Controls -->
                  <div class="flex items-center space-x-3">
                    <button 
                      @click="updateQuantity(item, item.quantity - 1)"
                      :disabled="item.quantity <= 1"
                      class="w-8 h-8 rounded-full border text-gray-500 border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    </button>
                    <span class="w-12 text-center text-gray-700 font-medium">{{ item.quantity }}</span>
                    <button 
                      @click="updateQuantity(item, item.quantity + 1)"
                      :disabled="item.quantity >= item.stock"
                      class="w-8 h-8 rounded-full border text-blue-600 border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                      </svg>
                    </button>
                  </div>

                  <!-- Subtotal -->
                  <div class="text-right">
                    <div class="text-lg font-bold text-gray-900">
                      IDR {{ (item.price * item.quantity).toLocaleString() }}
                    </div>
                    <button 
                      @click="removeItem(item)"
                      class="text-red-600 hover:text-red-800 text-sm mt-1 transition-colors"
                    >
                      Remove
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cart Summary -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
              <div class="flex justify-between items-center">
                <div>
                  <p class="text-sm text-gray-600">Total Items: {{ totalItems }}</p>
                  <p class="text-2xl font-bold text-gray-900">Total: IDR {{ totalPrice.toLocaleString() }}</p>
                </div>
                <div class="flex space-x-4">
                  <NuxtLink to="/" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Continue Shopping
                  </NuxtLink>
                  <button 
                    @click="handleCheckout"
                    :disabled="loading"
                    class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {{ loading ? 'Processing...' : 'Proceed to Checkout' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
const { getCart, removeFromCart, updateCartQuantity, checkoutCart } = useApi()

// Reactive data
const cartItems = ref([])
const loading = ref(true)
const error = ref(null)
const isAuthenticated = ref(false)

// Computed properties
const totalItems = computed(() => {
  return cartItems.value.reduce((total, item) => total + item.quantity, 0)
})

const totalPrice = computed(() => {
  return cartItems.value.reduce((total, item) => total + (item.price * item.quantity), 0)
})

function checkAuthStatus() {
  if (process.client) {
    const token = localStorage.getItem('auth_token')
    isAuthenticated.value = !!token
  }
}

// Load cart data
onMounted(async () => {
  checkAuthStatus()
  if (!isAuthenticated.value) {
    await navigateTo('/login')
    return
  }
  await loadCart()
})

async function loadCart() {
  try {
    loading.value = true
    error.value = null
    
    const response = await getCart()
    cartItems.value = response.data || []
  } catch (err) {
    console.error('Error loading cart:', err)
    error.value = 'Failed to load cart. Please try again later.'
  } finally {
    loading.value = false
  }
}

async function updateQuantity(item, newQuantity) {
  if (newQuantity < 1 || newQuantity > item.stock) return
  
  try {
    await updateCartQuantity(item.id, newQuantity, item.tenant_id)
    item.quantity = newQuantity
  } catch (err) {
    console.error('Error updating quantity:', err)
    alert('Failed to update quantity')
  }
}

async function removeItem(item) {
  if (!confirm('Are you sure you want to remove this item from cart?')) return
  
  try {
    await removeFromCart(item.id, item.tenant_id)
    cartItems.value = cartItems.value.filter(cartItem => cartItem.id !== item.id)
  } catch (err) {
    console.error('Error removing item:', err)
    alert('Failed to remove item from cart')
  }
}

async function handleCheckout() {
  const confirmed = confirm(`Are you sure you want to proceed with checkout?\n\nTotal: IDR ${totalPrice.value.toLocaleString()}\nItems: ${totalItems.value}`);
  if (!confirmed) return;
  
  try {
    loading.value = true;
    await checkoutCart();
    alert('Checkout successful! Your order has been placed.');
    await loadCart();
  } catch (err) {
    console.error('Error during checkout:', err);
    alert('Failed to process checkout. Please try again.');
  } finally {
    loading.value = false;
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