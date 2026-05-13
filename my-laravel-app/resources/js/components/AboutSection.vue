<template>
  <v-main class="main-content">
    <v-container fluid class="py-8">
      <v-card class="about-section site-section">
        <v-card-text>
          <h2>{{ s.aboutTitle }}</h2>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Facilis dolorum, aliquid minus inventore velit nulla ipsam omnis temporibus fugiat? Sit velit, excepturi dolor consectetur alias quam dolorem perspiciatis fugit assumenda!</p>
        </v-card-text>

        <v-row class="features-grid">
          <v-col cols="12" sm="6">
            <v-card class="feature-card">
              <v-card-text>
                <h3>{{ s.aboutAuthor }}</h3>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia laborum cumque voluptates, ea aliquam voluptas ad nisi. Esse quod nisi odit beatae. Optio est accusantium ipsa praesentium, non mollitia excepturi.</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6">
            <v-card class="feature-card">
              <v-card-text>
                <h3>{{ s.aboutProject }}</h3>
                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Accusantium nobis nam, dolorum adipisci dolor rem dolorem commodi, quae earum expedita cumque. Molestias officiis delectus eius vitae ea adipisci, rem repellendus?</p>
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
