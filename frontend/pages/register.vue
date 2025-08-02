<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Create your account
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Or
        <NuxtLink to="/login" class="font-medium text-blue-600 hover:text-blue-500">
          sign in to your existing account
        </NuxtLink>
      </p>
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

        <form @submit.prevent="handleRegister" class="space-y-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
              Full Name
            </label>
            <div class="mt-1">
              <input
                id="name"
                v-model="form.name"
                name="name"
                type="text"
                autocomplete="name"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Enter your full name"
              />
            </div>
          </div>

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
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
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
                autocomplete="new-password"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Enter your password"
              />
            </div>
            <p class="mt-1 text-xs text-gray-500">
              Password must be at least 8 characters long
            </p>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
              Confirm Password
            </label>
            <div class="mt-1">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Confirm your password"
              />
            </div>
          </div>

          <div class="flex items-center">
            <input
              id="agree-terms"
              v-model="form.agreeTerms"
              name="agree-terms"
              type="checkbox"
              required
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="agree-terms" class="ml-2 block text-sm text-gray-900">
              I agree to the
              <a href="#" class="text-blue-600 hover:text-blue-500">Terms and Conditions</a>
              and
              <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
            </label>
          </div>

          <div>
            <button
              type="submit"
              :disabled="loading || !isFormValid"
              class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
              </span>
              {{ loading ? 'Creating account...' : 'Create account' }}
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300" />
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">Or continue with</span>
            </div>
          </div>

          <div class="mt-6">
            <NuxtLink
              to="/"
              class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
            >
              Back to Home
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const { register } = useApi()

// Form data
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  agreeTerms: false
})

// State
const loading = ref(false)
const error = ref(null)
const success = ref(null)

// Form validation
const isFormValid = computed(() => {
  return (
    form.value.name.trim() !== '' &&
    form.value.email.trim() !== '' &&
    form.value.password.length >= 8 &&
    form.value.password === form.value.password_confirmation &&
    form.value.agreeTerms
  )
})

// Handle registration
async function handleRegister() {
  try {
    loading.value = true
    error.value = null
    success.value = null

    // Validate passwords match
    if (form.value.password !== form.value.password_confirmation) {
      error.value = 'Passwords do not match'
      return
    }

    const response = await register({
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation
    })

    success.value = 'Account created successfully! Redirecting to login...'
    
    // Store token if provided
    if (response.data?.token) {
      localStorage.setItem('auth_token', response.data.token)
    }

    // Redirect to login page after successful registration
    setTimeout(() => {
      navigateTo('/login')
    }, 2000)

  } catch (err) {
    console.error('Registration error:', err)
    if (err.data?.message) {
      error.value = err.data.message
    } else if (err.data?.errors) {
      // Handle validation errors
      const errors = Object.values(err.data.errors).flat()
      error.value = errors.join(', ')
    } else if (err.message) {
      error.value = err.message
    } else {
      error.value = 'Registration failed. Please try again.'
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