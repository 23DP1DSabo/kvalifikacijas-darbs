<template>
  <v-main class="main-content">
    <v-container fluid class="py-8">
      <v-card class="contact-section site-section">
        <v-card-text>
          <h2>{{ s.contactTitle }}</h2>
        </v-card-text>

        <v-row class="contact-content">
          <v-col cols="12" sm="6">
            <v-card class="contact-info">
              <v-card-text>
                <h3>{{ s.contactAuthorLabel }}</h3>
                <p><strong>{{ s.contactEmail }}:</strong> a230158ds@rvt.lv</p>
                <p><strong>{{ s.contactPhone }}:</strong> (+371) 28 105 787</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6">
            <v-card class="contact-form">
              <v-card-text>
                <h3>{{ s.contactSendMsg }}</h3>
                <v-alert v-if="contactSent" type="success" variant="tonal" class="mb-4">
                  {{ s.contactSentMsg }}
                </v-alert>
                <v-form v-else @submit.prevent="submitContact">
                  <v-text-field v-model="contactForm.name" :label="s.contactName" required class="mb-4"></v-text-field>
                  <v-text-field v-model="contactForm.email" :label="s.contactEmailField" type="email" required class="mb-4"></v-text-field>
                  <v-textarea v-model="contactForm.message" :label="s.contactMessage" required class="mb-4"></v-textarea>
                  <v-btn type="submit" color="primary" variant="elevated" block :loading="contactLoading">{{ s.contactSend }}</v-btn>
                </v-form>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-card>
    </v-container>
  </v-main>
</template>

<script>
import { apiFetch } from '../api.js'

export default {
  name: 'ContactSection',
  props: ['s', 'currentUser'],
  emits: ['snackbar'],

  data() {
    return {
      contactForm: { name: '', email: '', message: '' },
      contactLoading: false,
      contactSent: false,
    }
  },

  methods: {
    async submitContact() {
      this.contactLoading = true
      try {
        const res = await apiFetch('/api/contact', {
          method: 'POST',
          body: JSON.stringify(this.contactForm),
        })
        if (!res.ok) throw new Error()
        this.contactSent = true
        this.contactForm = { name: '', email: '', message: '' }
      } catch {
        this.$emit('snackbar', this.s.errSendMessage)
      } finally {
        this.contactLoading = false
      }
    },
  },
}
</script>
