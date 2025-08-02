export const useApi = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiUrl || 'http://localhost:8000'

  const apiCall = async (endpoint, options = {}) => {
    try {
      let headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers,
        ...(localStorage.getItem('auth_token') && {
          'ctoken': localStorage.getItem('auth_token')
        })
      }

      const response = await $fetch(`${baseURL}/api${endpoint}`, {
        ...options,
        headers,
        credentials: 'include'
      })
      return response
    } catch (error) {
      console.error('API Error:', error)
      throw error
    }
  }

  const getTenants = () => {
    return apiCall('/tenants')
  }

  const getAllProducts = () => {
    return apiCall('/products')
  }

  const getTenantProducts = (tenantId) => {
    return apiCall('/tenant/products', {
      headers: {
        'ctoken': tenantId
      }
    })
  }

  const login = (credentials) => {
    return apiCall('/login', {
      method: 'POST',
      body: credentials
    })
  }

  const register = (userData) => {
    return apiCall('/register', {
      method: 'POST',
      body: userData
    })
  }

  return {
    getTenants,
    getAllProducts,
    getTenantProducts,
    login,
    register
  }
}