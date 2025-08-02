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

  const getAllProductsByTenant = (tenantId) => {
    return apiCall(`/products?tenant=${tenantId}`)
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

  const addToCart = (productId, tenantId) => {
    return apiCall(`/cart/${productId}`, {
      method: 'POST',
      body: {
        tenant_id: tenantId
      }
    })
  }

  const getCart = () => {
    return apiCall('/cart')
  }

  const removeFromCart = (productId) => {
    return apiCall(`/cart/${productId}`, {
      method: 'DELETE'
    })
  }

  const updateCartQuantity = (productId, quantity) => {
    return apiCall(`/cart/${productId}`, {
      method: 'PUT',
      body: { quantity }
    })
  }

  return {
    getTenants,
    getAllProducts,
    getTenantProducts,
    getAllProductsByTenant,
    login,
    register,
    addToCart,
    getCart,
    removeFromCart,
    updateCartQuantity
  }
}