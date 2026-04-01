<template>
  <div id="simple-app">

<!-----Header----->
    <header class="site-header">
        <h1 class="site-title">TaskForge</h1>
      <nav class="navigation">
        <a href="/" :class="{ active: currentPage === 'home' }">Sākums</a>
        <a href="/forum" :class="{ active: currentPage === 'forum' }">Forums</a>
        <a href="/about" :class="{ active: currentPage === 'about' }">Par Mums</a>
        <a href="/contact" :class="{ active: currentPage === 'contact' }">Kontakti</a>
        <button class="nav-btn" @click="toggleTheme">'{{ darkMode ? 'light' : 'dark' }}' tēma</button>
        <button class="nav-btn" @click="showLoginwindow = true">Pieslēgties</button>
      </nav>
    </header>


<!-----Basics----->
<!--Homepage-->
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
            <h2>Pomodoro Pulkstenis</h2>
            <p>Strādājiet un atpūtieites pēc noteikta laika grafika.</p>
          </div>
          <a class="card-button" href="/pomodoro" :class="{ active: currentPage === 'pomodoro' }">Pāriet</a>
        </div>


<!--Cards-->
        <div class="card" id="task-card">
          <div class="card-in-card">
            <h2>Mērķu Plānotājs</h2>
            <p>Organizējiet savus mērķus ilgtermiņā.</p>
          </div>
          <a class="card-button" id="plan-button" href="/taskboard" :class="{ active: currentPage === 'taskboard' }">Pāriet</a>
        </div>

        <div class="card" id="eisenhower-card">
          <div class="card-in-card">
            <h2>Eizenhauera Matrica</h2>
            <p>Nosakidrojiet kādus mērķus sasniegt pirms citiem.</p>
          </div>
          <a class="card-button" href="/matrix" :class="{ active: currentPage === 'matrix' }">Pāriet</a>
        </div>

        <div class="card" id="calendar-card">
          <div class="card-in-card">
            <h2>Uzdevumu Kalendārs</h2>
            <p>Lieciet atgādinājumus notikumiem vai termiņiem.</p>
          </div>
          <a class="card-button" href="/calendar" :class="{ active: currentPage === 'calendar' }">Pāriet</a>
        </div>
      </section>
    </main>


<!--Forum-->
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


<!--About-page-->
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


<!--Contacts-->
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
              <input type="text" placeholder="Jūsu vārds un uzvārds" v-model="contactForm.name" required>
              <input type="email" placeholder="Jūsu epasts" v-model="contactForm.email" required>
              <textarea placeholder="Jūsu ziņojums" v-model="contactForm.message" required></textarea>
              <button type="submit" class="action-btn">Sūtīt</button>
            </form>
          </div>
        </div>
      </section>
    </main>


<!-----Interactive tools----->
<!--Pomodoro clock-->
    <main class="main-content" v-else-if="currentPage === 'pomodoro'">
      <section class="pomodoro-section site-section">
        <h2>Pomodoro Pulkstenis</h2>
        <div class="pomodoro-content">
          <div class="timer-display">{{ formatTime(pomodoroTime) }}</div>
          <button @click="startPomodoro" class="action-btn">Start</button>
          <button @click="pausePomodoro" class="action-btn">Pause</button>
          <button @click="resetPomodoro" class="action-btn">Reset</button>
        </div>
      </section>
    </main>


<!--Task board-->
    <main class="main-content" v-else-if="currentPage === 'taskboard'">
      <section class="taskboard-section site-section">
        <h2>Uzdevumu Dēlis</h2>
        <div class="contact-content">

        </div>
      </section>
  </main>


<!--Eisenhower matrix-->
    <main class="main-content" v-else-if="currentPage === 'matrix'">
      <section class="matrix-section site-section">
        <h2>Eizenhauera Matrica</h2>
        <div class="contact-content">
      
        </div>
      </section>
    </main>

    
<!--Calendar-->
    <main class="main-content" v-else-if="currentPage === 'calendar'">
      <section class="calendar-section site-section">
        <h2>Kalendārs</h2>
        <div class="contact-content">
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Autem suscipit alias adipisci beatae recusandae aperiam ratione ab facere amet, quo consectetur perferendis iure numquam nostrum, quos corrupti maxime delectus dolorum.
        </div>
        <VCalendar :attributes="calendarAttributes" />
      </section>
    </main>


<!-----Footer----->
    <footer id="footer-section">
      <p>&copy; 2026 TaskForge - D. Sabovics. All rights reserved.</p>
    </footer>

<!-- Login Window -->
    <div v-if="showLoginWindow" class="window-overlay" @click="showLoginWindow = false">
      <div class="window-content" @click.stop>
        <div class="window-header">
          <h2>Pieslēgties</h2>
          <button class="close-btn" @click="showLoginWindow = false">&times;</button>
        </div>
        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label for="email">Epasts:</label>
            <input type="email" id="email" v-model="loginForm.email" required>
          </div>
          <div class="form-group">
            <label for="password">Parole:</label>
            <input type="password" id="password" v-model="loginForm.password" required>
          </div>
          <button type="submit" class="login-btn">Pieslēgties</button>
        </form>
        <p class="register-link">Nav konta? <a href="#" @click.prevent="showRegisterWindow = true; showLoginWindow = false">Reģistrēties</a></p>
      </div>
    </div>

    <!-- Register window -->
    <div v-if="showRegisterWindow" class="window-overlay" @click="showRegisterWindow = false">
      <div class="window-content" @click.stop>
        <div class="window-header">
          <h2>Reģistrēties</h2>
          <button class="close-btn" @click="showRegisterWindow = false">&times;</button>
        </div>
        <form @submit.prevent="handleRegister" class="login-form">
          <div class="form-group">
            <label for="reg-name">Vārds:</label>
            <input type="text" id="reg-name" v-model="registerForm.name" required>
          </div>
          <div class="form-group">
            <label for="reg-email">Epasts:</label>
            <input type="email" id="reg-email" v-model="registerForm.email" required>
          </div>
          <div class="form-group">
            <label for="reg-password">Parole:</label>
            <input type="password" id="reg-password" v-model="registerForm.password" required>
          </div>
          <div class="form-group">
            <label for="reg-password-confirm">Apstiprināt paroli:</label>
            <input type="password" id="reg-password-confirm" v-model="registerForm.password_confirmation" required>
          </div>
          <button type="submit" class="login-btn">Reģistrēties</button>
        </form>
        <p class="register-link">Jau ir konts? <a href="#" @click.prevent="showLoginWindow = true; showRegisterWindow = false">Pieslēgties</a></p>
      </div>
    </div>

  </div>
</template>

<!--scripts-->
<script>
  export default {
    name: 'App',
    data() {
      return {
        currentPage: window.pageData?.currentPage || 'home',
        pageTitle: window.pageData?.title || 'Homepage',
        clickCount: 0,
        darkMode: false,
        showLoginWindow: false,
        showRegisterWindow: false,
        contactForm: {
          name: '',
          email: '',
          message: ''
        },
        loginForm: {
          email: '',
          password: ''
        },
        registerForm: {
          name: '',
          email: '',
          password: '',
          password_confirmation: ''
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
      },

      handleLogin() {
        // Here you would typically send the login data to your backend
        alert(`Logging in with email: ${this.loginForm.email}`)
        // Reset form and close window
        this.loginForm = { email: '', password: '' }
        this.showLoginwindow = false
      },

      handleRegister() {
        // Here you would typically send the register data to your backend
        alert(`Registering user: ${this.registerForm.name}`)
        // Reset form and close window
        this.registerForm = { name: '', email: '', password: '', password_confirmation: '' }
        this.showRegisterwindow = false
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
