import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8081/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Add request interceptor for debugging
api.interceptors.request.use(
  config => {
    console.log('API Request:', config.method?.toUpperCase(), config.url)
    return config
  },
  error => {
    console.error('API Request Error:', error)
    return Promise.reject(error)
  }
)

// Add response interceptor for error handling
api.interceptors.response.use(
  response => {
    return response
  },
  error => {
    console.error('API Error:', error.response?.data || error.message)
    console.error('API Error Status:', error.response?.status)
    console.error('API Error URL:', error.config?.url)
    return Promise.reject(error)
  }
)

export default {
  // Customer Categories
  getCustomerCategories() {
    return api.get('/customer-categories')
  },

  // Customers
  getCustomers(params = {}) {
    return api.get('/customers', { params })
  },
  getCustomer(id) {
    return api.get(`/customers/${id}`)
  },
  createCustomer(data) {
    return api.post('/customers', data)
  },
  updateCustomer(id, data) {
    return api.put(`/customers/${id}`, data)
  },
  deleteCustomer(id) {
    return api.delete(`/customers/${id}`)
  },

  // Contacts
  getContacts(customerId) {
    return api.get(`/customers/${customerId}/contacts`)
  },
  createContact(customerId, data) {
    return api.post(`/customers/${customerId}/contacts`, data)
  },
  updateContact(contactId, data) {
    return api.put(`/contacts/${contactId}`, data)
  },
  deleteContact(contactId) {
    return api.delete(`/contacts/${contactId}`)
  }
}

