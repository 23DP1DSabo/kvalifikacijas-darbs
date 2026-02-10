import { createApp } from 'vue'
import App from './components/App.vue'

import { createVuetify } from 'vuetify'
import 'vuetify/styles'

const vuetify = createVuetify({
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        colors: {
          primary: '#1976D2',
          secondary: '#424242',
          accent: '#82B1FF',
        }
      }
    }
  }
})

createApp(App)
  .use(vuetify)
  .mount('#app')

// Apply persisted dark mode before Vue mounts so all pages match
const saved = localStorage.getItem('darkMode')
if (saved === 'true') {
  document.body.classList.add('dark-theme')
}