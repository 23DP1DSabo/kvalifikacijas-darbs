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

  export default {
    name: 'App',
    data() {
      return {
        currentPage: window.pageData?.currentPage || 'home',
        pageTitle: window.pageData?.title || 'Homepage',
        clickCount: 0,
        darkMode: false,
        contactForm: {
          name: '',
          email: '',
          message: ''
        }
      }
    },
  
    methods: {
  
      toggleTheme() {
        this.darkMode = !this.darkMode
        document.body.classList.toggle('dark-theme', this.darkMode)
      },
  
      submitContact() {
        alert(`Thank you ${this.contactForm.name}! Your message has been sent.`)
        // Reset form
        this.contactForm = { name: '', email: '', message: '' }
      }
    }
  }