<template>
  <v-app id="simple-app">


<!-----Header----->

    <v-app-bar class="site-header" elevation="4">
      <v-toolbar-title class="site-title">TaskForge</v-toolbar-title>
      <v-spacer></v-spacer>
      
      <div class="navigation">
        <v-btn text @click="currentPage = 'home'" :class="{ active: currentPage === 'home' }">Sākums</v-btn>
        <v-btn text @click="currentPage = 'forum'" :class="{ active: currentPage === 'forum' }">Forums</v-btn>
        <v-btn text @click="currentPage = 'about'" :class="{ active: currentPage === 'about' }">Par Mums</v-btn>
        <v-btn text @click="currentPage = 'contact'" :class="{ active: currentPage === 'contact' }">Kontakti</v-btn>
        <v-btn icon @click="toggleTheme" :title="darkMode ? 'Gaisma' : 'Tumsa'">
          <v-icon>{{ darkMode ? 'mdi-weather-sunny' : 'mdi-moon-waning-crescent' }}</v-icon>
        </v-btn>
        <v-btn text @click="showLoginWindow = true">Pieslēgties</v-btn>
      </div>
    </v-app-bar>


<!-----Basics----->
<!-- Homepage -->

    <v-main v-if="currentPage === 'home'" class="main-content">
      <v-container fluid class="hero-section">
        <h1 class="site-title">Galvenā lapa</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Necessitatibus doloremque sequi nulla inventore blanditiis! Omnis alias doloribus possimus nemo? Blanditiis amet quam libero cum, reprehenderit laudantium corrupti obcaecati modi ad.</p>
      </v-container>

      <v-container fluid class="py-8">
        <v-card class="description site-section">
          <v-card-text>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis odit in quibusdam, enim cumque dolor laudantium sit quod impedit quo, aliquam et quis voluptatem delectus. Quasi facere architecto tenetur adipisci.</p>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dolorem sit quam reiciendis numquam, dolore necessitatibus excepturi. Exercitationem atque non, ipsum veniam, est laudantium, porro fugiat nulla totam quia asperiores eveniet.</p>
          </v-card-text>
        </v-card>
      </v-container>


<!-- Cards -->

      <v-container fluid class="py-8">
        <v-row>
          <v-col cols="12" sm="6" md="3">
            <v-card class="card" id="pomodoro-card" @click="currentPage = 'pomodoro'" style="cursor: pointer;">
              <v-card-text class="card-in-card">
                <h3>Pomodoro Pulkstenis</h3>
                <p>Strādājiet un atpūtieites pēc noteikta laika grafika.</p>
              </v-card-text>
              <v-card-actions class="card-button">
                <v-btn variant="elevated" color="primary" block>Pāriet</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card class="card" id="task-card" @click="currentPage = 'taskboard'" style="cursor: pointer;">
              <v-card-text class="card-in-card">
                <h3>Mērķu Plānotājs</h3>
                <p>Organizējiet savus mērķus ilgtermiņā.</p>
              </v-card-text>
              <v-card-actions class="card-button">
                <v-btn variant="elevated" color="primary" block>Pāriet</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card class="card" id="eisenhower-card" @click="currentPage = 'matrix'" style="cursor: pointer;">
              <v-card-text class="card-in-card">
                <h3>Eizenhauera Matrica</h3>
                <p>Nosakidrojiet kādus mērķus sasniegt pirms citiem.</p>
              </v-card-text>
              <v-card-actions class="card-button">
                <v-btn variant="elevated" color="primary" block>Pāriet</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card class="card" id="calendar-card" @click="currentPage = 'calendar'" style="cursor: pointer;">
              <v-card-text class="card-in-card">
                <h3>Uzdevumu Kalendārs</h3>
                <p>Lieciet atgādinājumus notikumiem vai termiņiem.</p>
              </v-card-text>
              <v-card-actions class="card-button">
                <v-btn variant="elevated" color="primary" block>Pāriet</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>


<!-- Forum -->

    <v-main v-else-if="currentPage === 'forum'" class="main-content">
      <v-container fluid class="py-8">
        <v-card class="forum-section site-section">
          <v-card-text>
            <h2>Forums</h2>
            <p>Pievienojies diskusijām.</p>
          </v-card-text>

          <v-row class="features-grid">
            <v-col cols="12" sm="6">
              <v-card class="feature-card">
                <v-card-text>
                  <h3>Galvenā diskusija.</h3>
                </v-card-text>
                <v-card-actions>
                  <v-btn color="primary" variant="elevated" block>Pārlūkot tēmas</v-btn>
                </v-card-actions>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6">
              <v-card class="feature-card">
                <v-card-text>
                  <h3>Tehniskais atbalsts</h3>
                </v-card-text>
                <v-card-actions>
                  <v-btn color="primary" variant="elevated" block>Palīdzība</v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </v-card>
      </v-container>
    </v-main>


<!-- About page -->

    <v-main v-else-if="currentPage === 'about'" class="main-content">
      <v-container fluid class="py-8">
        <v-card class="about-section site-section">
          <v-card-text>
            <h2>Par Mums</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Facilis dolorum, aliquid minus inventore velit nulla ipsam omnis temporibus fugiat? Sit velit, excepturi dolor consectetur alias quam dolorem perspiciatis fugit assumenda!</p>
          </v-card-text>

          <v-row class="features-grid">
            <v-col cols="12" sm="6">
              <v-card class="feature-card">
                <v-card-text>
                  <h3>Par autoru</h3>
                  <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia laborum cumque voluptates, ea aliquam voluptas ad nisi. Esse quod nisi odit beatae. Optio est accusantium ipsa praesentium, non mollitia excepturi.</p>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6">
              <v-card class="feature-card">
                <v-card-text>
                  <h3>Par projektu</h3>
                  <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Accusantium nobis nam, dolorum adipisci dolor rem dolorem commodi, quae earum expedita cumque. Molestias officiis delectus eius vitae ea adipisci, rem repellendus?</p>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-card>
      </v-container>
    </v-main>


<!-- Contacts -->

    <v-main v-else-if="currentPage === 'contact'" class="main-content">
      <v-container fluid class="py-8">
        <v-card class="contact-section site-section">
          <v-card-text>
            <h2>Kontakti</h2>
          </v-card-text>

          <v-row class="contact-content">
            <v-col cols="12" sm="6">
              <v-card class="contact-info">
                <v-card-text>
                  <h3>Sazinies ar autoru!</h3>
                  <p><strong>Epasts:</strong> a230158ds@rvt.lv</p>
                  <p><strong>Tālrunis:</strong> (+371) 28 105 787</p>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6">
              <v-card class="contact-form">
                <v-card-text>
                  <h3>Nosūti ziņojumu!</h3>
                  <v-form @submit.prevent="submitContact">
                    <v-text-field v-model="contactForm.name" label="Jūsu vārds un uzvārds" required class="mb-4"></v-text-field>
                    <v-text-field v-model="contactForm.email" label="Jūsu epasts" type="email" required class="mb-4"></v-text-field>
                    <v-textarea v-model="contactForm.message" label="Jūsu ziņojums" required class="mb-4"></v-textarea>
                    <v-btn type="submit" color="primary" variant="elevated" block>Sūtīt</v-btn>
                  </v-form>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-card>
      </v-container>
    </v-main>


<!-- Pomodoro clock -->

    <v-main v-else-if="currentPage === 'pomodoro'" class="main-content">
      <v-container fluid class="py-8">
        <v-card class="pomodoro-section site-section">
          <v-card-text>
            <h2>Pomodoro Pulkstenis</h2>
            <p>{{ currentPhase === 'work' ? 'Work session' : currentPhase === 'break' ? 'Short break' : 'Long break' }}</p>
            <div class="pomodoro-content">
              <div class="timer-display">{{ formatTime(pomodoroTime) }}</div>
              <v-btn color="primary" variant="elevated" @click="startPomodoro" class="ma-2" :disabled="isPomodoroRunning">Start</v-btn>
              <v-btn color="warning" variant="elevated" @click="pausePomodoro" class="ma-2" :disabled="!isPomodoroRunning">Pause</v-btn>
              <v-btn color="error" variant="elevated" @click="resetPomodoro" class="ma-2">Reset</v-btn>
            </div>
          </v-card-text>
        </v-card>
      </v-container>
    </v-main>


<!-- Task board -->

    <v-main v-else-if="currentPage === 'taskboard'" class="main-content">
      <v-container fluid class="py-8">
        <v-card class="taskboard-section site-section">
          <v-card-text>
            <h2>Uzdevumu Dēlis</h2>
            <div class="contact-content">
              
            </div>
          </v-card-text>
        </v-card>
      </v-container>
    </v-main>


<!-- Eisenhower matrix -->

    <v-main v-else-if="currentPage === 'matrix'" class="main-content">
      <v-container fluid class="py-8">
        <v-card class="matrix-section site-section">
          <v-card-text>
            <h2>Eizenhauera Matrica</h2>
            <div class="contact-content">
              
            </div>
          </v-card-text>
        </v-card>
      </v-container>
    </v-main>

<!-- Calendar -->

    <v-main v-else-if="currentPage === 'calendar'" class="main-content">
      <v-container fluid class="py-8">
        <v-card class="calendar-section site-section">
          <v-card-text>
            <h2>Kalendārs</h2>
            <div class="contact-content">
              Lorem ipsum dolor sit amet consectetur adipisicing elit. Autem suscipit alias adipisci beatae recusandae aperiam ratione ab facere amet, quo consectetur perferendis iure numquam nostrum, quos corrupti maxime delectus dolorum.
            </div>
            <VCalendar :attributes="calendarAttributes" />
          </v-card-text>
        </v-card>
      </v-container>
    </v-main>


<!-- Footer -->

    <v-footer app class="d-flex justify-center" id="footer-section">
      <span>&copy; 2026 TaskForge - D. Šabovičs. All rights reserved.</span>
    </v-footer>

<!-- Login Sequence -->

    <v-dialog v-model="showLoginWindow" persistent max-width="400px">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>Pieslēgties</span>
            <v-btn icon="mdi-close" variant="text" @click="showLoginWindow = false"></v-btn>
          </div>
        </v-card-title>

        <v-card-text class="pt-6">
          <v-form @submit.prevent="handleLogin">
            <v-text-field v-model="loginForm.email" label="Epasts" type="email" required class="mb-4"></v-text-field>
            <v-text-field v-model="loginForm.password" label="Parole" type="password" required class="mb-4"></v-text-field>
            <v-btn type="submit" color="primary" variant="elevated" block class="mb-4">Pieslēgties</v-btn>
          </v-form>

          <p class="text-center">
            Nav konta? 
            <v-btn text color="primary" @click="showLoginWindow = false; showRegisterWindow = true">Reģistrēties</v-btn>
          </p>
        </v-card-text>
      </v-card>
    </v-dialog>


<!-- Register Sequence -->

    <v-dialog v-model="showRegisterWindow" persistent max-width="400px">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>Reģistrēties</span>
            <v-btn icon="mdi-close" variant="text" @click="showRegisterWindow = false"></v-btn>
          </div>
        </v-card-title>

        <v-card-text class="pt-6">
          <v-form @submit.prevent="handleRegister">
            <v-text-field v-model="registerForm.name" label="Vārds" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.email" label="Epasts" type="email" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.password" label="Parole" type="password" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.password_confirmation" label="Apstiprināt paroli" type="password" required class="mb-4"></v-text-field>
            <v-btn type="submit" color="primary" variant="elevated" block class="mb-4">Reģistrēties</v-btn>
          </v-form>

          <p class="text-center">
            Jau ir konts? 
            <v-btn text color="primary" @click="showRegisterWindow = false; showLoginWindow = true">Pieslēgties</v-btn>
          </p>
        </v-card-text>
      </v-card>
    </v-dialog>

  </v-app>
</template>


<!--scripts-->

<script>
  import { useTheme } from 'vuetify'

  export default {
    name: 'App',
    setup() {
      const theme = useTheme()
      return { theme }
    },
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
        },
        pomodoroTime: 25 * 60,
        pomodoroDuration: 25 * 60,
        shortBreakDuration: 5 * 60,
        longBreakDuration: 15 * 60,
        currentPhase: 'work',
        isPomodoroRunning: false,
        pomodoroTimerId: null,
        cyclesCompleted: 0,
        calendarAttributes: []
      }
    },

    methods: {
      toggleTheme() {
        this.darkMode = !this.darkMode
        this.theme.global.name.value = this.darkMode ? 'dark' : 'light'
        const appElement = document.getElementById('simple-app')
        if (this.darkMode) {
          appElement?.classList.add('dark-theme')
        } else {
          appElement?.classList.remove('dark-theme')
        }
        localStorage.setItem('darkMode', this.darkMode)
      },

      formatTime(seconds) {
        const mins = Math.floor(seconds / 60)
        const secs = seconds % 60
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
      },

      startPomodoro() {
        if (this.isPomodoroRunning) return

        this.isPomodoroRunning = true
        this.pomodoroTimerId = setInterval(() => {
          if (this.pomodoroTime > 0) {
            this.pomodoroTime -= 1
            return
          }

          // transition phase
          if (this.currentPhase === 'work') {
            this.cyclesCompleted += 1
            if (this.cyclesCompleted % 4 === 0) {
              this.currentPhase = 'longBreak'
              this.pomodoroTime = this.longBreakDuration
              this.notify('Long break starts!')
            } else {
              this.currentPhase = 'break'
              this.pomodoroTime = this.shortBreakDuration
              this.notify('Break starts!')
            }
          } else {
            this.currentPhase = 'work'
            this.pomodoroTime = this.pomodoroDuration
            this.notify('Work session starts!')
          }
        }, 1000)
      },

      pausePomodoro() {
        if (this.pomodoroTimerId) {
          clearInterval(this.pomodoroTimerId)
          this.pomodoroTimerId = null
        }
        this.isPomodoroRunning = false
      },

      resetPomodoro() {
        if (this.pomodoroTimerId) {
          clearInterval(this.pomodoroTimerId)
          this.pomodoroTimerId = null
        }
        this.isPomodoroRunning = false
        this.currentPhase = 'work'
        this.cyclesCompleted = 0
        this.pomodoroTime = this.pomodoroDuration
      },

      notify(message) {
        if (window.Notification && Notification.permission === 'granted') {
          new Notification(message)
        } else {
          console.log(message)
        }
      },

      submitContact() {
        alert(`Thank you ${this.contactForm.name}! Your message has been sent.`)
        this.contactForm = { name: '', email: '', message: '' }
      },

      handleLogin() {
        alert(`Logging in with email: ${this.loginForm.email}`)
        this.loginForm = { email: '', password: '' }
        this.showLoginWindow = false
      },

      handleRegister() {
        alert(`Registering user: ${this.registerForm.name}`)
        this.registerForm = { name: '', email: '', password: '', password_confirmation: '' }
        this.showRegisterWindow = false
      }
    },

    mounted() {
      const saved = localStorage.getItem('darkMode')
      const appElement = document.getElementById('simple-app')
      if (saved === 'true') {
        this.darkMode = true
        this.theme.global.name.value = 'dark'
        appElement?.classList.add('dark-theme')
      } else {
        this.darkMode = false
        this.theme.global.name.value = 'light'
        appElement?.classList.remove('dark-theme')
      }

      if (window.Notification && Notification.permission !== 'granted') {
        Notification.requestPermission().then((permission) => {
          if (permission !== 'granted') {
            console.log('Notification permission denied')
          }
        })
      }
    }
  }
</script>

<style scoped lang="css">
  #simple-app {
    font-family: Bahnschrift, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: calc(100vh + 20px);
  }

  .site-header {
    background-color: #ffffff;
    color: #000000;
    z-index: 1000;
  }

  .site-title {
    margin: 0;
    font-size: 2.5rem;
    font-weight: bolder;
    font-family: bahnschrift, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    text-align: center;
  }

  .navigation {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    text-align: center;
    gap: 10px;
    align-items: center;
  }

  .navigation :deep(.v-btn) {
    margin: 0 5px;
  }

  .main-content {
    display: block;
    margin-top: auto;
    padding: 20px;
    height: auto;
  }

  .site-section {
    padding: 20px;
    background-color: #f9f9f9;
    margin-bottom: 20px;
    border-radius: 8px;
    margin-top: 0;
  }

  .hero-section {
    background-color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    margin-top: 100px;
    height: 80vh;
    gap: 20px;
  }

  .description {
    background-color: #ffffff;
    border-radius: 8px;
  }


/* Cards styling */

  .card {
    display: flex;
    flex-direction: column;
    background-color: rgb(255, 255, 255);
    color: rgb(0, 0, 0);
    margin: 10px;
    transition: 0.5s;
    border: 3px solid #764ba2;
    border-radius: 8px;
    box-sizing: border-box;
    width: auto;
    height: 100%;
    cursor: pointer;
  }

  .card:hover {
    transform: scale(1.25);
    z-index: 2;
  }

  .card-in-card {
    background-color: rgb(220, 220, 220);
    backdrop-filter: blur(5px);
    padding: 10px;
    margin: 20px;
    border-radius: 8px;
    text-align: center;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .card-in-card h3 {
    margin: 10px 0;
  }

  .card-button {
    padding: 0 !important;
    border-color: #764ba2;
    background-color: #764ba2;
  }

  .card-button :deep(.v-btn) {
    background-color: #764ba2 !important;
    color: #ffffff;
  }

  .card-button :deep(.v-btn:hover) {
    background-color: #644087 !important;
  }

  #pomodoro-card,
  #task-card,
  #eisenhower-card,
  #calendar-card {
    background-color: rgb(255, 255, 255);
    color: #000000;
  }

  /* Features grid */
  .features-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
  }

  .feature-card {
    flex: 1 1 calc(50% - 20px);
    padding: 15px;
    border: 1px solid #cccccc;
    background-color: #ffffff;
    border-radius: 8px;
  }

  /* Contact form */
  .contact-content {
    display: flex;
    gap: 20px;
    margin-top: 20px;
    flex-wrap: wrap;
  }

  .contact-info,
  .contact-form {
    flex: 1;
    min-width: 250px;
    padding: 15px;
    background-color: #ffffff;
    border-radius: 8px;
  }

  .contact-form :deep(.v-text-field),
  .contact-form :deep(.v-textarea) {
    background-color: #f8f8f8;
  }


  /* Pomodoro */

  .pomodoro-content {
    display: flex;
    gap: 20px;
    margin-top: 20px;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
  }

  .timer-display {
    font-size: 48px;
    font-weight: bold;
    text-align: center;
    background-color: #ffffff;
    border-radius: 8px;
    padding: 20px;
    min-width: 200px;
  }

  /* Footer */
  #footer-section {
    background-color: #ffffff;
    text-align: center;
    padding: 20px;
  }

  /* Active navigation button */
  .navigation :deep(.active) {
    text-decoration: underline;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .site-title {
      font-size: 1.5rem;
    }

    .navigation {
      flex-direction: column;
      justify-content: center;
    }

    .feature-card {
      flex: 1 1 100%;
    }

    .contact-info,
    .contact-form {
      flex: 1 1 100%;
    }

    .timer-display {
      font-size: 36px;
    }
  }
</style>