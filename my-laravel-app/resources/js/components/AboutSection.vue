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
                <p>Projekta autors: Daniels Šabovičs DP3-1</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6">
            <v-card class="feature-card">
              <v-card-text>
                <h3>{{ s.aboutProject }}</h3>
                <p>Projekts sniedz lietotājam uzreiz vairākas metodes sava darbalaika plānošanai un organizēšanai, lai izvairītos no prokrastinācijas, izdeguma un citiem pārslodzes rezultātiem. Šobrīd neesmu atradis vietni, kas atbilstu šādām prasībām, tāpēc liecinu ka tādas platformas izveide ir lietderīga un aktuāla. Ar tās palīdzību cilvēki spēs ne tikai, attīstīt savas administratīvās prasmes un veikt vairāk darba īsākā laika sprīdī, bet arī cerams pašizglītoties šajā jomā no citiem un palīdzēt citiem ar līdzīgiem trūkumiem. Datu bāzē tiks glabāta informācija par tādiem elementiem kā lietotāji, forumi, publicējumi, komentāri un pulkstenis, matrica, kalendārs, uzdevumi, uzdevumu dēlis. </p>
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
