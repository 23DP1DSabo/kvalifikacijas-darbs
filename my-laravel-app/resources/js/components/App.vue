<template>
  <div id="simple-app">


    <header class="site-header">
        <h1 class="site-title">TaskForge</h1>
      <nav class="navigation">
        <a href="/" :class="{ active: currentPage === 'home' }">Sākums</a>
        <a href="/forum" :class="{ active: currentPage === 'forum' }">Forums</a>
        <a href="/about" :class="{ active: currentPage === 'about' }">Par Mums</a>
        <a href="/contact" :class="{ active: currentPage === 'contact' }">Kontakti</a>
        <button class="nav-btn" @click="toggleTheme">'{{ darkMode ? 'light' : 'dark' }}' tēma</button>
      </nav>
    </header>


    <main class="main-content" v-if="currentPage === 'home'">
      <section class="hero-section site-section">
      <h1 class="site-title">Galvenā lapa</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Necessitatibus doloremque sequi nulla inventore blanditiis! Omnis alias doloribus possimus nemo? Blanditiis amet quam libero cum, reprehenderit laudantium corrupti obcaecati modi ad.</p>
      </section>
      <section class="description site-section">
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis odit in quibusdam, enim cumque dolor laudantium sit quod impedit quo, aliquam et quis voluptatem delectus. Quasi facere architecto tenetur adipisci.</p>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dolorem sit quam reiciendis numquam, dolore necessitatibus excepturi. Exercitationem atque non, ipsum veniam, est laudantium, porro fugiat nulla totam quia asperiores eveniet.</p>
      </section>
      <section id="cards">
        <div class="card" id="pomodoro-card">
          <div class="card-in-card">
            <h2>Pomodoro pulkstenis</h2>
            <p>Strādājiet un atpūtieites pēc noteikta laika grafika.</p>
          </div>
          <a class="card-button" href="...">Pāriet</a>
        </div>

        <div class="card" id="task-card">
          <div class="card-in-card">
            <h2>Mērķu plānotājs</h2>
            <p>Organizējiet savus mērķus ilgtermiņā.</p>
          </div>
          <a class="card-button" id="plan-button" href="...">Pāriet</a>
        </div>

        <div class="card" id="eisenhower-card">
          <div class="card-in-card">
            <h2>Eizenhauera matrica</h2>
            <p>Nosakidrojiet kādus mērķus sasniegt pirms citiem.</p>
          </div>
          <a class="card-button" href="...">Pāriet</a>
        </div>

        <div class="card" id="calendar-card">
          <div class="card-in-card">
            <h2>Uzdevumu ķēdes kalendārs</h2>
            <p>Lieciet atgādinājumus notikumiem vai termiņiem.</p>
          </div>
          <a class="card-button" href="...">Pāriet</a>
        </div>
      </section>
    </main>

    <main class="main-content" v-else-if="currentPage === 'forum'">
      <section class="forum-section site-section">
        <h2>Forums</h2>
        <p>Pievienojies diskusijām.</p>
        <div class="features-grid">
          <div class="feature-card">
            <h3>Galvenā diskusija.</h3>
            <button class="action-btn">Pārlūkot tēmas</button>
          </div>
          <div class="feature-card">
            <h3>Tehniskais atbalsts</h3>
            <button class="action-btn">Palīdzība</button>
          </div>
        </div>
      </section>
    </main>

    <main class="main-content" v-else-if="currentPage === 'about'">
      <section class="about-section site-section">
        <h2>Par Mums</h2>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Facilis dolorum, aliquid minus inventore velit nulla ipsam omnis temporibus fugiat? Sit velit, excepturi dolor consectetur alias quam dolorem perspiciatis fugit assumenda!</p>
        <div class="features-grid">
          <div class="feature-card">
            <h3>Par autoru</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia laborum cumque voluptates, ea aliquam voluptas ad nisi. Esse quod nisi odit beatae. Optio est accusantium ipsa praesentium, non mollitia excepturi.</p>
          </div>
          <div class="feature-card">
            <h3>Par projektu</h3>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Accusantium nobis nam, dolorum adipisci dolor rem dolorem commodi, quae earum expedita cumque. Molestias officiis delectus eius vitae ea adipisci, rem repellendus?</p>
          </div>
        </div>
      </section>
    </main>

    <main class="main-content" v-else-if="currentPage === 'contact'">
      <section class="contact-section site-section">
        <h2>Kontakti</h2>
        <div class="contact-content">
          <div class="contact-info">
            <h3>Sazinies ar autoru!</h3>
            <p><strong>Epasts:</strong> a230158ds@rvt.lv</p>
            <p><strong>Tālrunis:</strong> (+371) 28 105 787</p>
          </div>
          <div class="contact-form">
            <h3>Nosūti ziņojumu!</h3>
            <form @submit.prevent="submitContact">
              <input type="text" placeholder="Your Name" v-model="contactForm.name" required>
              <input type="email" placeholder="Your Email" v-model="contactForm.email" required>
              <textarea placeholder="Your Message" v-model="contactForm.message" required></textarea>
              <button type="submit" class="action-btn">Sūtīt</button>
            </form>
          </div>
        </div>
      </section>
    </main>


    <footer id="footer-section">
      <p>&copy; 2026 TaskForge - D. Sabovics. All rights reserved.</p>
    </footer>


  </div>
</template>


<script>
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
      localStorage.setItem('darkMode', this.darkMode)
    },

    submitContact() {
      alert(`Thank you ${this.contactForm.name}! Your message has been sent.`)
      this.contactForm = { name: '', email: '', message: '' }
    }
  },

  mounted() {
    const saved = localStorage.getItem('darkMode')
    if (saved === 'true') {
      this.darkMode = true
      document.body.classList.add('dark-theme')
    } else {
      this.darkMode = false
      document.body.classList.remove('dark-theme')
    }
  }
}
</script>
