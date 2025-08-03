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

  const getTenantProducts = (page = 1) => {
    return apiCall(`/tenant/products?page=${page}`)
  }

  const createTenantProduct = (productData) => {
    return apiCall('/tenant/products', {
      method: 'POST',
      body: productData
    })
  }

  const updateTenantProduct = (productId, productData) => {
    return apiCall(`/tenant/products/${productId}`, {
      method: 'PUT',
      body: productData
    })
  }

  const deleteTenantProduct = (productId) => {
    return apiCall(`/tenant/products/${productId}`, {
      method: 'DELETE'
    })
  }

  const getTenantProduct = (productId) => {
    return apiCall(`/tenant/products/${productId}`)
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

  const removeFromCart = (productId, tenantId) => {
    return apiCall(`/cart/${productId}?is_delete=1&tenant_id=${tenantId}`, {
      method: 'DELETE'
    })
  }

  const updateCartQuantity = (productId, quantity, tenantId) => {
    return apiCall(`/cart/${productId}`, {
      method: 'POST',
      body: { 
        quantity,
        tenant_id: tenantId
      }
    })
  }

  const checkoutCart = () => {
    return apiCall(`/checkout`, {
      method: 'POST'
    })
  }

  return {
    getTenants,
    getAllProducts,
    getTenantProducts,
    createTenantProduct,
    updateTenantProduct,
    deleteTenantProduct,
    getTenantProduct,
    getAllProductsByTenant,
    login,
    register,
    addToCart,
    getCart,
    removeFromCart,
    updateCartQuantity,
    checkoutCart
  }
}