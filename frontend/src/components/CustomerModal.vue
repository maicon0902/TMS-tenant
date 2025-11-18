<template>
  <div class="modal-overlay" @click.self="close">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Customers - Detail</h3>
        <div>
          <button class="btn btn-secondary" @click="close" style="margin-right: 10px;">Back</button>
          <button class="btn btn-primary" @click="save">Save</button>
        </div>
      </div>

      <div class="modal-body">
        <!-- General Section -->
        <div class="section">
          <div class="section-title">General</div>
          <div class="form-group">
            <label>Name</label>
            <input type="text" v-model="form.name" />
          </div>
          <div class="form-group">
            <label>Reference</label>
            <input type="text" v-model="form.reference" />
          </div>
          <div class="form-group">
            <label>Category</label>
            <select v-model="form.customer_category_id">
              <option value="">[...Select...]</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Details Section -->
        <div class="section">
          <div class="section-title">Details</div>
          <div class="form-group">
            <label>Start Date</label>
            <input type="date" v-model="form.start_date" />
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea v-model="form.description"></textarea>
          </div>
        </div>

        <!-- Contacts Section - Only show when customer is saved -->
        <div class="section" v-if="isCustomerSaved">
          <div class="section-header">
            <div class="section-title">Contacts</div>
            <button class="btn btn-primary" @click="openContactModal()">Create</button>
          </div>
          <table class="table">
            <thead>
              <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="contact in contacts" :key="contact.id">
                <td>{{ contact.first_name }}</td>
                <td>{{ contact.last_name || '-' }}</td>
                <td>
                  <a class="link" @click="openContactModal(contact)">Edit</a> |
                  <a class="link" @click="confirmDeleteContact(contact)">Delete</a>
                </td>
              </tr>
              <tr v-if="contacts.length === 0">
                <td colspan="3" style="text-align: center; padding: 20px;">
                  No contacts found
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Contact Modal -->
      <ContactModal
        v-if="showContactModal"
        :contact="selectedContact"
        @close="closeContactModal"
        @saved="handleContactSaved"
      />

      <!-- Delete Contact Confirmation Modal -->
      <ConfirmModal
        v-if="showDeleteContactModal"
        title="Delete Contact"
        :message="`Are you sure you want to delete contact '${contactToDelete?.first_name} ${contactToDelete?.last_name}'?`"
        @confirm="deleteContact"
        @cancel="closeDeleteContactModal"
      />
    </div>
  </div>
</template>

<script>
import api from '../services/api'
import ContactModal from './ContactModal.vue'
import ConfirmModal from './ConfirmModal.vue'

export default {
  name: 'CustomerModal',
  components: {
    ContactModal,
    ConfirmModal
  },
  props: {
    customer: {
      type: Object,
      default: null
    },
    categories: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      form: {
        name: '',
        reference: '',
        customer_category_id: '',
        start_date: '',
        description: ''
      },
      contacts: [],
      currentCustomerId: null,
      showContactModal: false,
      selectedContact: null,
      showDeleteContactModal: false,
      contactToDelete: null
    }
  },
  computed: {
    isCustomerSaved() {
      // Customer is saved if it has an ID (either from currentCustomerId or customer.id)
      return !!(this.currentCustomerId || this.customer?.id)
    }
  },
  watch: {
    customer: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          // Convert date from m/d/Y to Y-m-d format for date input
          let startDate = ''
          if (newVal.start_date) {
            const dateParts = newVal.start_date.split('/')
            if (dateParts.length === 3) {
              startDate = `${dateParts[2]}-${dateParts[0].padStart(2, '0')}-${dateParts[1].padStart(2, '0')}`
            } else {
              startDate = newVal.start_date
            }
          }
          
          this.form = {
            name: newVal.name || '',
            reference: newVal.reference || '',
            customer_category_id: newVal.category_id || newVal.customer_category_id || '',
            start_date: startDate,
            description: newVal.description || ''
          }
          this.currentCustomerId = newVal.id
          this.loadContacts(newVal.id)
        } else {
          this.form = {
            name: '',
            reference: '',
            customer_category_id: '',
            start_date: '',
            description: ''
          }
          this.currentCustomerId = null
          this.contacts = []
        }
      }
    }
  },
  methods: {
    async loadContacts(customerId) {
      if (!customerId) return
      try {
        const response = await api.getContacts(customerId)
        this.contacts = response.data
      } catch (error) {
        console.error('Error loading contacts:', error)
      }
    },
    async save() {
      try {
        let customerId = this.currentCustomerId
        if (this.customer) {
          const response = await api.updateCustomer(this.customer.id, this.form)
          customerId = this.customer.id
        } else {
          const response = await api.createCustomer(this.form)
          customerId = response.data.id
          this.currentCustomerId = customerId
          await this.loadContacts(customerId)
        }
        this.$emit('saved')
      } catch (error) {
        console.error('Error saving customer:', error)
        const message = error.response?.data?.message || 'Error saving customer'
        alert(message)
      }
    },
    close() {
      this.$emit('close')
    },
    openContactModal(contact = null) {
      // If editing existing contact, allow it
      if (contact) {
        this.selectedContact = contact
        this.showContactModal = true
        return
      }

      // This should only be called when customer is already saved
      // (because the Contacts section is hidden when customer is not saved)
      if (!this.isCustomerSaved) {
        alert('Please save the customer first before adding contacts')
        return
      }

      this.selectedContact = contact
      this.showContactModal = true
    },
    closeContactModal() {
      this.showContactModal = false
      this.selectedContact = null
    },
    async handleContactSaved() {
      this.closeContactModal()
      const customerId = this.currentCustomerId || this.customer?.id
      if (customerId) {
        await this.loadContacts(customerId)
        // Emit event to parent to refresh customers list
        this.$emit('contact-updated')
      }
    },
    confirmDeleteContact(contact) {
      this.contactToDelete = contact
      this.showDeleteContactModal = true
    },
    closeDeleteContactModal() {
      this.showDeleteContactModal = false
      this.contactToDelete = null
    },
    async deleteContact() {
      try {
        await api.deleteContact(this.contactToDelete.id)
        this.closeDeleteContactModal()
        const customerId = this.currentCustomerId || this.customer?.id
        if (customerId) {
          await this.loadContacts(customerId)
          // Emit event to parent to refresh customers list
          this.$emit('contact-updated')
        }
      } catch (error) {
        console.error('Error deleting contact:', error)
        alert('Error deleting contact')
      }
    }
  }
}
</script>

<style scoped>
.section {
  margin-bottom: 20px;
}
</style>

