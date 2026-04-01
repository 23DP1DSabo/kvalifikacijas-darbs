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
          primary: '#764ba2',
          secondary: '#667eea',
          accent: '#82B1FF',
          surface: '#ffffff',
          background: '#f5f5f5',
        }
      },
      dark: {
        dark: true,
        colors: {
          primary: '#764ba2',
          secondary: '#667eea',
          accent: '#82B1FF',
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

const saved = localStorage.getItem('darkMode')
if (saved === 'true') {
  vuetify.theme.global.name.value = 'dark'
}

