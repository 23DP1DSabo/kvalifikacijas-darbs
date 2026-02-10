<template>
  <div id="simple-app">
    <header class="site-header">
        <h1 class="site-title">TaskForge</h1>
      <nav class="navigation">
        <a href="/" :class="{ active: currentPage === 'home' }">Sākums</a>
        <a href="/forum" :class="{ active: currentPage === 'forum' }">Forums</a>
        <a href="/about" :class="{ active: currentPage === 'about' }">Par projektu</a>
        <a href="/contact" :class="{ active: currentPage === 'contact' }">Kontakti</a>
        <button class="nav-btn" @click="toggleTheme">'{{ darkMode ? 'light' : 'dark' }}' tēma</button>
      </nav>
    </header>

    <main class="main-content" v-if="currentPage === 'home'">
      <section class="hero-section">
      <h1 class="site-title">Galvenā lapa</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Necessitatibus doloremque sequi nulla inventore blanditiis! Omnis alias doloribus possimus nemo? Blanditiis amet quam libero cum, reprehenderit laudantium corrupti obcaecati modi ad.</p>
      </section>
      <section class="description, site-section">
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis odit in quibusdam, enim cumque dolor laudantium sit quod impedit quo, aliquam et quis voluptatem delectus. Quasi facere architecto tenetur adipisci.</p>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dolorem sit quam reiciendis numquam, dolore necessitatibus excepturi. Exercitationem atque non, ipsum veniam, est laudantium, porro fugiat nulla totam quia asperiores eveniet.</p>
      </section>
    </main>

    <main class="main-content" v-else-if="currentPage === 'forum'">
      <section class="forum-section site-section">
        <h2>Forums</h2>
        <p>Join discussions.</p>
        <div class="features-grid">
          <div class="feature-card">
            <h3>General Discussion</h3>
            <p>Talk about anything related to web development, programming, and technology.</p>
            <button class="action-btn">Browse Topics</button>
          </div>
          <div class="feature-card">
            <h3>Help & Support</h3>
            <p>Get help with coding problems, debugging, and technical issues.</p>
            <button class="action-btn">Get Help</button>
          </div>
        </div>
      </section>
    </main>

    <main class="main-content" v-else-if="currentPage === 'about'">
      <section class="about-section site-section">
        <h2>Par projektu</h2>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Facilis dolorum, aliquid minus inventore velit nulla ipsam omnis temporibus fugiat? Sit velit, excepturi dolor consectetur alias quam dolorem perspiciatis fugit assumenda!</p>
        <div class="features-grid">
          <div class="feature-card">
            <h3>Modern Tech Stack</h3>
            <p>Vue.js, Laravel, and modern development tools</p>
          </div>
          <div class="feature-card">
            <h3>User Focused</h3>
            <p>Creating intuitive and accessible web experiences</p>
          </div>
        </div>
      </section>
    </main>

    <main class="main-content" v-else-if="currentPage === 'contact'">
      <section class="contact-section site-section">
        <h2>Kontakti</h2>
        <div class="contact-content">
          <div class="contact-info">
            <h3>Get In Touch</h3>
            <p><strong>Email:</strong> a230158ds@rvt.lv</p>
            <p><strong>Phone:</strong> (+371) 28 105 787</p>
          </div>
          <div class="contact-form">
            <h3>Send a Message</h3>
            <form @submit.prevent="submitContact">
              <input type="text" placeholder="Your Name" v-model="contactForm.name" required>
              <input type="email" placeholder="Your Email" v-model="contactForm.email" required>
              <textarea placeholder="Your Message" v-model="contactForm.message" required></textarea>
              <button type="submit" class="action-btn">Send Message</button>
            </form>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      // Get page data from Laravel (passed via window.pageData)
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
    incrementCounter() {
      this.clickCount++
    },

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
</script>