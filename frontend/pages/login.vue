<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Sign in to your account
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Or
        <NuxtLink to="/register" class="font-medium text-blue-600 hover:text-blue-500">
          create a new account
        </NuxtLink>
      </p>
    </div>

    <!-- Header Navigation -->
     <div class="sm:mx-auto sm:w-full sm:max-w-md mb-4">
       <div class="flex justify-between items-center">
         <NuxtLink to="/" class="text-blue-600 hover:text-blue-800 transition-colors">
           ← Back to Home
         </NuxtLink>
       </div>
     </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <!-- Error Message -->
        <div v-if="error" class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
          <div class="flex">
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Error</h3>
              <div class="mt-2 text-sm text-red-700">
                <p>{{ error }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="success" class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
          <div class="flex">
            <div class="ml-3">
              <h3 class="text-sm font-medium text-green-800">Success</h3>
              <div class="mt-2 text-sm text-green-700">
                <p>{{ success }}</p>
              </div>
            </div>
          </div>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email address
            </label>
            <div class="mt-1">
              <input
                id="email"
                v-model="form.email"
                name="email"
                type="email"
                autocomplete="email"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Enter your email"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Password
            </label>
            <div class="mt-1">
              <input
                id="password"
                v-model="form.password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Enter your password"
              />
            </div>
          </div>

          <div>
            <button
              type="submit"
              :disabled="loading"
              class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
              </span>
              {{ loading ? 'Signing in...' : 'Sign in' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
const { login } = useApi()

// Form data
const form = ref({
  email: '',
  password: '',
  remember: false
})

// State
const loading = ref(false)
const error = ref(null)
const success = ref(null)

// Handle login
async function handleLogin() {
  try {
    loading.value = true
    error.value = null
    success.value = null

    const response = await login({
      email: form.value.email,
      password: form.value.password,
      remember: form.value.remember
    })

    success.value = 'Login successful! Redirecting...'
    
    // Store token if provided
    if (response.data?.token) {
      // You can store token in localStorage, cookie, or Pinia store
      localStorage.setItem('auth_token', response.data.token)
    }

    // Redirect to home page after successful login
    setTimeout(() => {
      navigateTo('/')
    }, 1500)

  } catch (err) {
    console.error('Login error:', err)
    if (err.data?.message) {
      error.value = err.data.message
    } else if (err.message) {
      error.value = err.message
    } else {
      error.value = 'Login failed. Please check your credentials and try again.'
    }
  } finally {
    loading.value = false
  }
}

// Clear messages when form changes
watch([() => form.value.email, () => form.value.password], () => {
  error.value = null
  success.value = null
})
</script>