<template>
  <header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center space-x-4">
          <NuxtLink to="/" class="text-2xl font-bold text-gray-900 hover:text-blue-600 transition-colors">
            One Ecommerce
          </NuxtLink>
          <span v-if="showBreadcrumb" class="text-gray-400">/</span>
          <h1 v-if="showBreadcrumb" class="text-xl font-semibold text-gray-700">{{ breadcrumbTitle }}</h1>
        </div>
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
            <NuxtLink v-if="showCartButton" to="/cart" class="relative text-gray-600 hover:text-gray-900 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6M20 13v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2-2v4m16 0H4"></path>
              </svg>
              <span v-if="cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ cartCount }}
              </span>
            </NuxtLink>
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
</template>

<script setup>
const props = defineProps({
  showBreadcrumb: {
    type: Boolean,
    default: false
  },
  breadcrumbTitle: {
    type: String,
    default: ''
  },
  showCartButton: {
    type: Boolean,
    default: true
  },
  cartCount: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['logout'])

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
    emit('logout')
    window.location.reload()
  }
}

onMounted(() => {
  checkAuthStatus()
})

if (process.client) {
  window.addEventListener('storage', (e) => {
    if (e.key === 'auth_token') {
      checkAuthStatus()
    }
  })
}
</script>