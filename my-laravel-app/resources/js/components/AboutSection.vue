<template>
  <v-main class="main-content">
    <v-container fluid class="py-8">
      <v-card class="about-section site-section">
        <v-card-text>
          <h2>{{ s.aboutTitle }}</h2>
          <p>TaskForge</p>
        </v-card-text>

        <v-row class="features-grid">
          <v-col cols="12" sm="6">
            <v-card class="feature-card">
              <v-card-text>
                <h3>{{ s.aboutAuthor }}</h3>
                <p>{{ s.aboutAuthorCredit }}</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6">
            <v-card class="feature-card">
              <v-card-text>
                <h3>{{ s.aboutProject }}</h3>
                <p>{{ s.aboutProjectDesc }}</p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
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
