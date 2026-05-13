import { createApp } from 'vue'
import App from './components/App.vue'

import { createVuetify } from 'vuetify'
import 'vuetify/styles'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { aliases, mdi } from 'vuetify/iconsets/mdi'

const vuetify = createVuetify({
  components,
  directives,
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        dark: false,
        colors: {
          primary: '#8B6E43',
          secondary: '#6b4f2a',
          accent: '#d4a851',
          surface: '#faf6ef',
          background: '#e4dccf',
        }
      },
      dark: {
        dark: true,
        colors: {
          primary: '#d4a851',
          secondary: '#8B6E43',
          accent: '#d4a851',
          surface: '#1e1e1e',
          background: '#121212',
        }
      }
    }
  }
})

createApp(App)
  .use(vuetify)
  .mount('#app')

