<template>
  <div class="customers-page">
    <div class="page-header">
      <h2>Customers</h2>
      <button class="btn btn-primary" @click="openCustomerModal()">Create</button>
    </div>

    <!-- Search and Filter -->
    <div class="search-filter">
      <div class="search-filter-row">
        <div class="form-group">
          <label>Search</label>
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Search customers..."
            @keyup.enter="loadCustomers"
          />
        </div>
        <div class="form-group">
          <label>Category</label>
          <select v-model="selectedCategory">
            <option value="">[...Select...]</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        <div class="search-filter-actions">
          <button class="btn btn-secondary" @click="clearFilters">Clear</button>
          <button class="btn btn-primary" @click="loadCustomers">Apply</button>
        </div>
      </div>
    </div>

    <!-- Customers Table -->
    <div class="section">
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Reference</th>
            <th>Category</th>
            <th>No of Contacts</th>
            <th>Edit | Delete</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="customer in customers" :key="customer.id">
            <td>{{ customer.name }}</td>
            <td>{{ customer.reference }}</td>
            <td>{{ customer.category || '-' }}</td>
            <td>{{ customer.contacts_count }}</td>
            <td>
              <a class="link" @click="openCustomerModal(customer)">Edit</a> |
              <a class="link" @click="confirmDeleteCustomer(customer)">Delete</a>
            </td>
          </tr>
          <tr v-if="customers.length === 0">
            <td colspan="5" style="text-align: center; padding: 20px;">
              No customers found
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Customer Modal -->
    <CustomerModal
      v-if="showCustomerModal"
      :customer="selectedCustomer"
      :categories="categories"
      @close="closeCustomerModal"
      @saved="handleCustomerSaved"
      @contact-updated="loadCustomers"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      v-if="showDeleteModal"
      title="Delete Customer"
      :message="`Are you sure you want to delete customer '${customerToDelete?.name}'?`"
      @confirm="deleteCustomer"
      @cancel="closeDeleteModal"
    />
  </div>
</template>

<script>
import api from '../services/api'
import CustomerModal from '../components/CustomerModal.vue'
import ConfirmModal from '../components/ConfirmModal.vue'

export default {
  name: 'Customers',
  components: {
    CustomerModal,
    ConfirmModal
  },
  data() {
    return {
      customers: [],
      categories: [],
      searchQuery: '',
      selectedCategory: '',
      showCustomerModal: false,
      selectedCustomer: null,
      showDeleteModal: false,
      customerToDelete: null
    }
  },
  mounted() {
    this.loadCategories()
    this.loadCustomers()
  },
  methods: {
    async loadCategories() {
      try {
        const response = await api.getCustomerCategories()
        this.categories = response.data
      } catch (error) {
        console.error('Error loading categories:', error)
        // Don't show alert for categories, just log the error
      }
    },
    async loadCustomers() {
      try {
        const params = {}
        if (this.searchQuery) {
          params.search = this.searchQuery
        }
        if (this.selectedCategory) {
          params.category = this.selectedCategory
        }
        const response = await api.getCustomers(params)
        this.customers = response.data
      } catch (error) {
        console.error('Error loading customers:', error)
        const errorMessage = error.response?.data?.message || 
                            error.message || 
                            'Error loading customers. Please check if the API is running on http://localhost:8081'
        alert(errorMessage)
      }
    },
    clearFilters() {
      this.searchQuery = ''
      this.selectedCategory = ''
      this.loadCustomers()
    },
    async openCustomerModal(customer = null) {
      if (customer) {
        // Fetch full customer details including contacts
        try {
          const response = await api.getCustomer(customer.id)
          this.selectedCustomer = response.data
        } catch (error) {
          console.error('Error loading customer details:', error)
          this.selectedCustomer = customer
        }
      } else {
        this.selectedCustomer = null
      }
      this.showCustomerModal = true
    },
    closeCustomerModal() {
      this.showCustomerModal = false
      this.selectedCustomer = null
    },
    handleCustomerSaved() {
      this.closeCustomerModal()
      this.loadCustomers()
    },
    confirmDeleteCustomer(customer) {
      this.customerToDelete = customer
      this.showDeleteModal = true
    },
    closeDeleteModal() {
      this.showDeleteModal = false
      this.customerToDelete = null
    },
    async deleteCustomer() {
      try {
        await api.deleteCustomer(this.customerToDelete.id)
        this.closeDeleteModal()
        this.loadCustomers()
      } catch (error) {
        console.error('Error deleting customer:', error)
        alert('Error deleting customer')
      }
    }
  }
}
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.page-header h2 {
  font-size: 24px;
  font-weight: 600;
}
</style>

