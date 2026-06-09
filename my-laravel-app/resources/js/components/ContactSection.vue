<template>
  <v-main class="main-content">
    <v-container fluid class="py-8">
      <v-card class="contact-section site-section">
        <v-card-text class="pa-6">
          <h2>{{ s.contactTitle }}</h2>
        </v-card-text>

        <v-row class="px-6 pb-6" no-gutters>
          <v-col cols="12" md="5" class="pr-md-4 pb-4 pb-md-0">
            <v-card class="contact-info" height="100%">
              <v-card-text class="pa-6">
                <h3 class="mb-4">{{ s.contactAuthorLabel }}</h3>
                <p class="mb-2"><strong>{{ s.contactEmail }}:</strong> a230158ds@rvt.lv</p>
                <p><strong>{{ s.contactPhone }}:</strong> (+371) 28 105 787</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" md="7" class="pl-md-4">
            <v-card class="contact-form">
              <v-card-text class="pa-6">
                <h3 class="mb-4">{{ s.contactSendMsg }}</h3>
                <v-alert v-if="contactSent" type="success" variant="tonal" class="mb-4">
                  {{ s.contactSentMsg }}
                </v-alert>
                <v-form v-else @submit.prevent="submitContact">
                  <v-text-field
                    v-model="contactForm.name"
                    :label="s.contactName"
                    variant="outlined"
                    density="comfortable"
                    required
                    class="mb-3"
                  ></v-text-field>
                  <v-text-field
                    v-model="contactForm.email"
                    :label="s.contactEmailField"
                    type="email"
                    variant="outlined"
                    density="comfortable"
                    required
                    class="mb-3"
                  ></v-text-field>
                  <v-textarea
                    v-model="contactForm.message"
                    :label="s.contactMessage"
                    variant="outlined"
                    rows="5"
                    required
                    class="mb-4"
                  ></v-textarea>
                  <v-btn type="submit" color="primary" variant="elevated" block :loading="contactLoading">
                    {{ s.contactSend }}
                  </v-btn>
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
