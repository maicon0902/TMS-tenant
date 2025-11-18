<template>
  <div class="modal-overlay" @click.self="close">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Contacts - Detail</h3>
        <div>
          <button class="btn btn-secondary" @click="close" style="margin-right: 10px;">Back</button>
          <button class="btn btn-primary" @click="save">Save</button>
        </div>
      </div>

      <div class="modal-body">
        <div class="section">
          <div class="section-title">General</div>
          <div class="form-group">
            <label>First Name <span class="required">*</span></label>
            <input type="text" v-model="form.first_name" />
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input type="text" v-model="form.last_name" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'ContactModal',
  props: {
    contact: {
      type: Object,
      default: null
    },
    customerId: {
      type: Number,
      default: null
    }
  },
  data() {
    return {
      form: {
        first_name: '',
        last_name: ''
      }
    }
  },
  watch: {
    contact: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.form = {
            first_name: newVal.first_name || '',
            last_name: newVal.last_name || ''
          }
        } else {
          this.form = {
            first_name: '',
            last_name: ''
          }
        }
      }
    }
  },
  methods: {
    async save() {
      if (!this.form.first_name) {
        alert('First Name is required')
        return
      }

      try {
        let customerId = this.customerId
        if (!customerId && this.$parent) {
          customerId = this.$parent.currentCustomerId || this.$parent.customer?.id
        }
        if (!customerId) {
          alert('Customer ID is required')
          return
        }

        if (this.contact) {
          await api.updateContact(this.contact.id, this.form)
        } else {
          await api.createContact(customerId, this.form)
        }
        this.$emit('saved')
      } catch (error) {
        console.error('Error saving contact:', error)
        const message = error.response?.data?.message || 'Error saving contact'
        alert(message)
      }
    },
    close() {
      this.$emit('close')
    }
  }
}
</script>

