<template>
  <v-main class="main-content">
    <v-container fluid class="py-8 about-container">
      <v-card class="about-section site-section">
        <v-card-text class="pa-6 pb-2">
          <h2>{{ s.aboutTitle }}</h2>
        </v-card-text>

        <div class="about-cards-wrap pa-6 pt-4">
          <!-- Author card — on top -->
          <v-card class="feature-card mb-6">
            <v-card-text>
              <h3>{{ s.aboutAuthor }}</h3>
              <p>{{ s.aboutAuthorCredit }}</p>
            </v-card-text>
          </v-card>

          <!-- Project card — below, multi-paragraph -->
          <v-card class="feature-card">
            <v-card-text>
              <h3>{{ s.aboutProject }}</h3>
              <p>{{ s.aboutProjectDesc }}</p>
              <p class="mt-4">{{ s.aboutProjectDesc2 }}</p>
              <p class="mt-4">{{ s.aboutProjectDesc3 }}</p>
            </v-card-text>
          </v-card>
        </div>
      </v-card>
    </v-container>
  </v-main>
</template>

<script>
export default {
  name: 'AboutSection',
  props: ['s'],
}
</script>
