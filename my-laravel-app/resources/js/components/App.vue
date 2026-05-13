<template>
  <v-app id="simple-app">

    <a href="#main-content" class="skip-link">{{ s.skipToContent }}</a>

<!-----Header----->

    <v-app-bar class="site-header" elevation="0">
      <v-toolbar-title class="header-logo" @click="currentPage = 'home'; navDrawer = false" style="cursor:pointer; flex:0 0 auto; max-width:fit-content;">TaskForge</v-toolbar-title>
      <v-spacer></v-spacer>

      <!-- Desktop navigation -->
      <div class="desktop-nav">
        <v-menu v-model="toolsMenuOpen" :close-on-content-click="true" location="bottom start">
          <template v-slot:activator="{ props }">
            <v-btn variant="text" class="desktop-nav-btn" v-bind="props">
              Rīki <span class="riki-arrow">{{ toolsMenuOpen ? '▴' : '▾' }}</span>
            </v-btn>
          </template>
          <v-list class="tools-dropdown" density="compact">
            <v-list-item prepend-icon="mdi-timer" @click="currentPage = 'pomodoro'" :active="currentPage === 'pomodoro'">
              <v-list-item-title>Pomodoro</v-list-item-title>
            </v-list-item>
            <v-list-item prepend-icon="mdi-grid" @click="currentPage = 'matrix'" :active="currentPage === 'matrix'">
              <v-list-item-title>Eizenhauera Matrica</v-list-item-title>
            </v-list-item>
            <v-list-item prepend-icon="mdi-calendar" @click="currentPage = 'calendar'" :active="currentPage === 'calendar'">
              <v-list-item-title>Kalendārs</v-list-item-title>
            </v-list-item>
            <v-list-item prepend-icon="mdi-format-list-checks" @click="currentPage = 'taskboard'" :active="currentPage === 'taskboard'">
              <v-list-item-title>Uzdevumu Pārvaldnieks</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>

        <v-btn variant="text" class="desktop-nav-btn" :class="{ 'desktop-nav-active': currentPage === 'forum' }" @click="currentPage = 'forum'">{{ s.navForum }}</v-btn>
        <v-btn variant="text" class="desktop-nav-btn" :class="{ 'desktop-nav-active': currentPage === 'about' }" @click="currentPage = 'about'">{{ s.navAbout }}</v-btn>
        <v-btn variant="text" class="desktop-nav-btn" :class="{ 'desktop-nav-active': currentPage === 'contact' }" @click="currentPage = 'contact'">{{ s.navContact }}</v-btn>
        <v-btn v-if="currentUser && currentUser.role === 'admin'" variant="text" class="desktop-nav-btn admin-nav-btn" :class="{ 'desktop-nav-active': currentPage === 'admin' }" @click="currentPage = 'admin'">{{ s.navAdmin }}</v-btn>

        <v-menu v-model="a11yMenuOpen" :close-on-content-click="false" location="bottom end">
          <template v-slot:activator="{ props }">
            <v-btn variant="text" class="desktop-nav-btn" v-bind="props">
              {{ s.navA11y }} <span class="riki-arrow">{{ a11yMenuOpen ? '▴' : '▾' }}</span>
            </v-btn>
          </template>
          <v-list class="tools-dropdown" density="compact">
            <v-list-item @click="toggleTheme">
              <template v-slot:prepend>
                <span class="theme-icon" :class="darkMode ? 'theme-sun' : 'theme-moon'" style="margin-right:18px;"></span>
              </template>
              <v-list-item-title>{{ darkMode ? s.lightTheme : s.darkTheme }}</v-list-item-title>
            </v-list-item>
            <v-list-item @click="toggleLanguage">
              <template v-slot:prepend>
                <span class="lang-flag">{{ language === 'lv' ? '🇬🇧' : '🇱🇻' }}</span>
              </template>
              <v-list-item-title>{{ language === 'lv' ? s.switchToEn : s.switchToLv }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>

        <template v-if="currentUser">
          <span class="desktop-nav-username" style="cursor:pointer" @click="showProfileDialog = true">
            <img v-if="currentUser.avatar_url" :src="currentUser.avatar_url" class="nav-avatar" :alt="currentUser.name">
            <span v-else class="nav-avatar-initials">{{ currentUser.name[0] }}{{ currentUser.surname ? currentUser.surname[0] : '' }}</span>
            {{ currentUser.name }}
          </span>
          <v-btn variant="text" class="desktop-nav-btn" @click="logout()">{{ s.navLogout }}</v-btn>
        </template>
        <template v-else>
          <v-btn color="primary" variant="elevated" size="small" class="desktop-nav-login" @click="showLoginWindow = true">{{ s.navLogin }}</v-btn>
        </template>
      </div>

      <!-- Mobile burger -->
      <v-btn icon variant="text" class="nav-burger-btn" @click="navDrawer = !navDrawer">
        <span class="burger-icon">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </v-btn>
    </v-app-bar>

    <div v-if="navDrawer" class="mobile-nav-overlay" @click="navDrawer = false"></div>

    <v-navigation-drawer v-model="navDrawer" location="right" temporary width="280" class="nav-drawer">
      <div class="drawer-header">
        <span class="drawer-logo">TaskForge</span>
        <v-btn icon variant="text" @click="navDrawer = false" color="#d4a851">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </div>

      <div class="drawer-section-label">{{ s.navTools }}</div>
      <v-list density="compact" nav>
        <v-list-item prepend-icon="mdi-timer" @click="currentPage = 'pomodoro'; navDrawer = false" :active="currentPage === 'pomodoro'">
          <v-list-item-title>{{ s.toolPomodoro }}</v-list-item-title>
        </v-list-item>
        <v-list-item prepend-icon="mdi-format-list-checks" @click="currentPage = 'taskboard'; navDrawer = false" :active="currentPage === 'taskboard'">
          <v-list-item-title>{{ s.toolTaskboard }}</v-list-item-title>
        </v-list-item>
        <v-list-item prepend-icon="mdi-grid" @click="currentPage = 'matrix'; navDrawer = false" :active="currentPage === 'matrix'">
          <v-list-item-title>{{ s.toolMatrix }}</v-list-item-title>
        </v-list-item>
        <v-list-item prepend-icon="mdi-calendar" @click="currentPage = 'calendar'; navDrawer = false" :active="currentPage === 'calendar'">
          <v-list-item-title>{{ s.toolCalendar }}</v-list-item-title>
        </v-list-item>
      </v-list>

      <v-divider style="border-color:#8B6E43;opacity:.3;"></v-divider>

      <v-list density="compact" nav>
        <v-list-item prepend-icon="mdi-forum-outline" @click="currentPage = 'forum'; navDrawer = false" :active="currentPage === 'forum'">
          <v-list-item-title>{{ s.navForum }}</v-list-item-title>
        </v-list-item>
        <v-list-item prepend-icon="mdi-information-outline" @click="currentPage = 'about'; navDrawer = false" :active="currentPage === 'about'">
          <v-list-item-title>{{ s.navAbout }}</v-list-item-title>
        </v-list-item>
        <v-list-item prepend-icon="mdi-email-outline" @click="currentPage = 'contact'; navDrawer = false" :active="currentPage === 'contact'">
          <v-list-item-title>{{ s.navContact }}</v-list-item-title>
        </v-list-item>
        <v-list-item v-if="currentUser && currentUser.role === 'admin'" prepend-icon="mdi-shield-crown-outline" @click="currentPage = 'admin'; navDrawer = false" :active="currentPage === 'admin'" class="admin-drawer-item">
          <v-list-item-title>{{ s.navAdmin }}</v-list-item-title>
        </v-list-item>
      </v-list>

      <v-divider style="border-color:#8B6E43;opacity:.3;"></v-divider>

      <div class="drawer-section-label">{{ s.navA11y }}</div>
      <v-list density="compact" nav>
        <v-list-item @click="toggleTheme">
          <template v-slot:prepend>
            <span class="theme-icon theme-icon-drawer" :class="darkMode ? 'theme-sun' : 'theme-moon'"></span>
          </template>
          <v-list-item-title>{{ darkMode ? s.lightTheme : s.darkTheme }}</v-list-item-title>
        </v-list-item>
        <v-list-item @click="toggleLanguage; navDrawer = false">
          <template v-slot:prepend>
            <span class="lang-flag lang-flag-drawer">{{ language === 'lv' ? '🇬🇧' : '🇱🇻' }}</span>
          </template>
          <v-list-item-title>{{ language === 'lv' ? s.switchToEn : s.switchToLv }}</v-list-item-title>
        </v-list-item>
      </v-list>

      <template v-if="currentUser">
        <v-divider style="border-color:#8B6E43;opacity:.3;"></v-divider>
        <div class="drawer-user">
          <v-icon color="#8B6E43">mdi-account-circle</v-icon>
          <span>{{ currentUser.name }} {{ currentUser.surname }}</span>
        </div>
        <v-list density="compact" nav>
          <v-list-item prepend-icon="mdi-logout" @click="logout(); navDrawer = false">
            <v-list-item-title>{{ s.navLogout }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </template>
      <template v-else>
        <v-divider style="border-color:#8B6E43;opacity:.3;"></v-divider>
        <div class="drawer-login-btn">
          <v-btn color="primary" variant="elevated" block @click="showLoginWindow = true; navDrawer = false">
            {{ s.navLogin }}
          </v-btn>
        </div>
      </template>
    </v-navigation-drawer>


<!-----Basics----->
<!-- Homepage -->

    <div id="main-content" tabindex="-1" style="position:absolute;top:0;left:0;width:1px;height:1px;overflow:hidden;"></div>

    <v-main v-if="currentPage === 'home'" class="main-content">
      <v-container fluid class="hero-section">
        <h1 class="site-title">{{ s.homeTitle }}</h1>
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
        <v-row align="stretch" class="home-cards-row">
          <v-col cols="12" sm="6" md="3" class="home-card-col">
            <v-card class="card" id="pomodoro-card" @click="currentPage = 'pomodoro'">
              <v-card-text class="card-in-card">
                <h3>{{ s.card1Title }}</h3>
                <p>{{ s.card1Desc }}</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3" class="home-card-col">
            <v-card class="card" id="task-card" @click="currentPage = 'taskboard'">
              <v-card-text class="card-in-card">
                <h3>{{ s.card2Title }}</h3>
                <p>{{ s.card2Desc }}</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3" class="home-card-col">
            <v-card class="card" id="eisenhower-card" @click="currentPage = 'matrix'">
              <v-card-text class="card-in-card">
                <h3>{{ s.card3Title }}</h3>
                <p>{{ s.card3Desc }}</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3" class="home-card-col">
            <v-card class="card" id="calendar-card" @click="currentPage = 'calendar'">
              <v-card-text class="card-in-card">
                <h3>{{ s.card4Title }}</h3>
                <p>{{ s.card4Desc }}</p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>

    <ForumSection v-else-if="currentPage === 'forum'"
      :current-user="currentUser" :s="s" :dark-mode="darkMode"
      @show-login="showLoginWindow = true" @snackbar="showSnackbar($event)"
    />

    <AboutSection v-else-if="currentPage === 'about'" :s="s" />

    <ContactSection v-else-if="currentPage === 'contact'" :s="s" :current-user="currentUser" @snackbar="showSnackbar($event)" />

    <PomodoroTool v-else-if="currentPage === 'pomodoro'"
      :tasks="tasks" :current-user="currentUser" :s="s" :dark-mode="darkMode"
      :open-task-dialog="openTaskDialog"
      @show-login="showLoginWindow = true" @snackbar="showSnackbar($event)" @navigate="currentPage = $event"
    />

    <TaskboardTool v-else-if="currentPage === 'taskboard'"
      :tasks="tasks" :columns="columns" :current-user="currentUser" :s="s" :dark-mode="darkMode"
      :open-task-dialog="openTaskDialog" :delete-task="deleteTask"
      :save-column="saveColumn" :delete-column="deleteColumn"
      @show-login="showLoginWindow = true" @snackbar="showSnackbar($event)" @navigate="currentPage = $event"
      @open-column-dialog="showColumnDialog = true"
    />

    <MatrixTool v-else-if="currentPage === 'matrix'"
      :tasks="tasks" :current-user="currentUser" :s="s" :dark-mode="darkMode"
      :open-task-dialog="openTaskDialog" :delete-task="deleteTask"
      @show-login="showLoginWindow = true" @snackbar="showSnackbar($event)" @navigate="currentPage = $event"
    />

    <CalendarTool v-else-if="currentPage === 'calendar'"
      :tasks="tasks" :current-user="currentUser" :s="s" :dark-mode="darkMode"
      :open-task-dialog="openTaskDialog"
      @show-login="showLoginWindow = true" @snackbar="showSnackbar($event)" @navigate="currentPage = $event"
    />

    <AdminSection v-else-if="currentPage === 'admin'"
      :s="s"
      @snackbar="showSnackbar"
    />

<!-- Footer -->

    <v-footer class="d-flex justify-center" id="footer-section">
      <span>&copy; 2026 TaskForge - D. Šabovičs. All rights reserved.</span>
    </v-footer>

<!-- Task Dialog -->

    <v-dialog v-model="showTaskDialog" max-width="500px">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ editingTask ? s.taskDialogEdit : s.taskDialogNew }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showTaskDialog = false"></v-btn>
          </div>
        </v-card-title>
        <v-card-text class="pt-4">
          <v-text-field v-model="taskForm.title" :label="s.taskTitle" required class="mb-3"></v-text-field>
          <v-textarea v-model="taskForm.description" :label="s.taskDesc" rows="2" class="mb-3"></v-textarea>
          <v-select v-model="taskForm.status" :items="statusOptions" item-title="title" item-value="value" :label="s.taskStatus" class="mb-3"></v-select>
          <v-text-field v-model="taskForm.due_date" :label="s.taskDueDate" type="date" class="mb-3"></v-text-field>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-btn v-if="editingTask" color="error" variant="text" @click="deleteTask(editingTask.id); showTaskDialog = false">{{ s.taskDelete }}</v-btn>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showTaskDialog = false">{{ s.taskCancel }}</v-btn>
          <v-btn color="primary" variant="elevated" @click="saveTask()">{{ s.taskSave }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

<!-- Column Dialog -->

    <v-dialog v-model="showColumnDialog" max-width="420px">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ s.colDialogTitle }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showColumnDialog = false"></v-btn>
          </div>
        </v-card-title>
        <v-card-text class="pt-4">
          <v-text-field v-model="columnForm.name" :label="s.colName" required class="mb-3"></v-text-field>
          <v-textarea v-model="columnForm.description" :label="s.colDesc" rows="2" class="mb-3"></v-textarea>
          <v-text-field :model-value="s.colCreatedDate()" :label="s.colCreated" readonly disabled class="mb-3"></v-text-field>
          <div class="mb-1 text-body-2 text-medium-emphasis">{{ s.colColor }}</div>
          <div class="col-color-picker">
            <div v-for="c in columnColors" :key="c"
                 class="col-color-swatch"
                 :style="{ background: c, outline: columnForm.color === c ? '3px solid #333' : 'none' }"
                 @click="columnForm.color = c">
            </div>
          </div>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showColumnDialog = false">{{ s.colCancel }}</v-btn>
          <v-btn color="primary" variant="elevated" @click="saveColumn()">{{ s.colCreate }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

<!-- Login Sequence -->

    <v-dialog v-model="showLoginWindow" persistent max-width="400px">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ s.loginTitle }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showLoginWindow = false; authError = ''"></v-btn>
          </div>
        </v-card-title>

        <v-card-text class="pt-6">
          <v-alert v-if="authError" type="error" variant="tonal" class="mb-4">{{ authError }}</v-alert>
          <v-form @submit.prevent="handleLogin">
            <v-text-field v-model="loginForm.email" :label="s.loginEmail" type="email" required class="mb-4"></v-text-field>
            <v-text-field v-model="loginForm.password" :label="s.loginPass" type="password" required class="mb-4"></v-text-field>
            <v-btn type="submit" color="primary" variant="elevated" block class="mb-4" :loading="authLoading">{{ s.loginSubmit }}</v-btn>
          </v-form>

          <p class="text-center">
            {{ s.loginNoAccount }}
            <v-btn text color="primary" @click="showLoginWindow = false; showRegisterWindow = true">{{ s.loginRegister }}</v-btn>
          </p>
        </v-card-text>
      </v-card>
    </v-dialog>


<!-- Register Sequence -->

    <v-dialog v-model="showRegisterWindow" persistent max-width="400px">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ s.regTitle }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showRegisterWindow = false; authError = ''"></v-btn>
          </div>
        </v-card-title>

        <v-card-text class="pt-6">
          <v-alert v-if="authError" type="error" variant="tonal" class="mb-4">{{ authError }}</v-alert>
          <v-form @submit.prevent="handleRegister">
            <v-text-field v-model="registerForm.username" :label="s.regUsername" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.name" :label="s.regName" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.surname" :label="s.regSurname" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.email" :label="s.regEmail" type="email" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.password" :label="s.regPass" type="password" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.password_confirmation" :label="s.regConfirm" type="password" required class="mb-4"></v-text-field>
            <v-btn type="submit" color="primary" variant="elevated" block class="mb-4" :loading="authLoading">{{ s.regSubmit }}</v-btn>
          </v-form>

          <p class="text-center">
            {{ s.regHasAccount }}
            <v-btn text color="primary" @click="showRegisterWindow = false; showLoginWindow = true">{{ s.regLogin }}</v-btn>
          </p>
        </v-card-text>
      </v-card>
    </v-dialog>

<!-- Profile Dialog -->

    <v-dialog v-model="showProfileDialog" max-width="460">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ s.profileTitle }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showProfileDialog = false"></v-btn>
          </div>
        </v-card-title>
        <v-card-text class="pt-4" v-if="currentUser">
          <!-- Avatar -->
          <div class="profile-avatar-wrap mb-4 mx-auto">
            <img v-if="currentUser.avatar_url" :src="currentUser.avatar_url" class="profile-avatar-img" :alt="currentUser.name">
            <div v-else class="profile-avatar-placeholder">{{ currentUser.name[0] }}{{ currentUser.surname ? currentUser.surname[0] : '' }}</div>
            <label class="profile-avatar-edit" :title="s.changeAvatar">
              <v-icon size="18">mdi-camera</v-icon>
              <input type="file" accept="image/*" style="display:none" @change="uploadAvatar">
            </label>
          </div>
          <v-progress-linear v-if="avatarUploading" indeterminate color="primary" class="mb-3"></v-progress-linear>

          <v-divider class="mb-4"></v-divider>

          <!-- Profile edit form -->
          <v-form @submit.prevent="saveProfile">
            <v-row dense>
              <v-col cols="6">
                <v-text-field v-model="profileForm.name" :label="s.regName" variant="outlined" density="compact"></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field v-model="profileForm.surname" :label="s.regSurname" variant="outlined" density="compact"></v-text-field>
              </v-col>
            </v-row>
            <v-text-field v-model="profileForm.username" :label="s.regUsername" variant="outlined" density="compact" class="mb-1" prefix="@"></v-text-field>
            <v-alert v-if="profileError" type="error" variant="tonal" density="compact" class="mb-2">{{ profileError }}</v-alert>
            <v-btn type="submit" color="primary" variant="elevated" block :loading="profileSaving">{{ s.profileSave }}</v-btn>
          </v-form>

          <v-divider class="my-4"></v-divider>

          <!-- Password change -->
          <v-form @submit.prevent="savePassword">
            <div class="text-body-2 font-weight-bold mb-2">{{ s.profileChangePass }}</div>
            <v-text-field v-model="passwordForm.current_password" :label="s.profileCurrentPass" type="password" variant="outlined" density="compact" class="mb-2"></v-text-field>
            <v-text-field v-model="passwordForm.password" :label="s.regPass" type="password" variant="outlined" density="compact" class="mb-2"></v-text-field>
            <v-text-field v-model="passwordForm.password_confirmation" :label="s.regConfirm" type="password" variant="outlined" density="compact" class="mb-1"></v-text-field>
            <v-alert v-if="passwordError" type="error" variant="tonal" density="compact" class="mb-2">{{ passwordError }}</v-alert>
            <v-btn type="submit" color="secondary" variant="elevated" block :loading="passwordSaving">{{ s.profileSavePass }}</v-btn>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="4000" location="bottom">
      {{ snackbar.text }}
      <template #actions>
        <v-btn variant="text" icon="mdi-close" @click="snackbar.show = false"></v-btn>
      </template>
    </v-snackbar>

  </v-app>
</template>


<!--scripts-->

<script>
  import { useTheme } from 'vuetify'
  import { apiFetch } from '../api.js'
  import PomodoroTool    from './PomodoroTool.vue'
  import TaskboardTool   from './TaskboardTool.vue'
  import MatrixTool      from './MatrixTool.vue'
  import CalendarTool    from './CalendarTool.vue'
  import ForumSection    from './ForumSection.vue'
  import AboutSection    from './AboutSection.vue'
  import ContactSection  from './ContactSection.vue'
  import AdminSection    from './AdminSection.vue'

  export default {
    name: 'App',
    components: { PomodoroTool, TaskboardTool, MatrixTool, CalendarTool, ForumSection, AboutSection, ContactSection, AdminSection },
    setup() {
      const theme = useTheme()
      return { theme }
    },
    data() {
      return {
        currentPage: window.pageData?.currentPage || 'home',
        pageTitle: window.pageData?.title || 'Homepage',
        navDrawer: false,
        toolsMenuOpen: false,
        a11yMenuOpen: false,
        language: 'lv',
        darkMode: false,
        showLoginWindow: false,
        showRegisterWindow: false,
        showProfileDialog: false,
        avatarUploading: false,
        verificationResending: false,
        profileForm: { name: '', surname: '', username: '' },
        profileError: '',
        profileSaving: false,
        passwordForm: { current_password: '', password: '', password_confirmation: '' },
        passwordError: '',
        passwordSaving: false,
        currentUser: null,
        authError: '',
        authLoading: false,
        snackbar: { show: false, text: '', color: 'error' },
        loginForm: {
          email: '',
          password: ''
        },
        registerForm: {
          username: '',
          name: '',
          surname: '',
          email: '',
          password: '',
          password_confirmation: ''
        },
        tasks: JSON.parse(localStorage.getItem('tf_tasks') || '[]'),
        showTaskDialog: false,
        editingTask: null,
        taskForm: { title: '', description: '', status: 'pending', due_date: '', priority: 'medium', est_pomodoros: 1 },
        columns: JSON.parse(localStorage.getItem('tf_columns') || 'null') || [
          { id: 'default', name: 'Iesūtne', description: 'Noklusējuma kolonna', createdAt: new Date().toISOString() }
        ],
        showColumnDialog: false,
        columnForm: { name: '', description: '', color: '#8B6E43' },
        columnColors: ['#8B6E43','#5a8a6e','#ef5350','#66bb6a','#ffa726','#26c6da','#ec407a','#78909c'],
      }
    },

    computed: {
      s() {
        const lv = {
          navTools:'Rīki', navForum:'Forums', navAbout:'Par Mums', navContact:'Kontakti',
          navA11y:'Pieejamība', navLogin:'Pieslēgties', navLogout:'Iziet',
          darkTheme:'Tumšā tema', lightTheme:'Gaišā tema',
          switchToEn:'Switch to English', switchToLv:'Pārslēgties uz latviešu',
          toolPomodoro:'Pomodoro', toolMatrix:'Eizenhauera Matrica',
          toolCalendar:'Kalendārs', toolTaskboard:'Uzdevumu Pārvaldnieks',
          homeTitle:'Galvenā lapa',
          card1Title:'Pomodoro Pulkstenis', card1Desc:'Strādājiet un atpūtieites pēc noteikta laika grafika.',
          card2Title:'Mērķu Plānotājs',     card2Desc:'Organizējiet savus mērķus ilgtermiņā.',
          card3Title:'Eizenhauera Matrica', card3Desc:'Nosakidrojiet kādus mērķus sasniegt pirms citiem.',
          card4Title:'Uzdevumu Kalendārs',  card4Desc:'Lieciet atgādinājumus notikumiem vai termiņiem.',
          forumTitle:'Forums', forumSubtitle:'Izvēlies kategoriju, lai sāktu vai lasītu diskusijas',
          forumAllForums:'Visi forumi', forumNewPost:'Jauns ieraksts',
          forumSignInToWrite:'Piesakies, lai rakstītu', forumSignInToComment:'Piesakies, lai komentētu',
          forumNoPosts:'Nav ierakstu šajā kategorijā. Esi pirmais!',
          forumComments:'Komentāri', forumNoComments:'Nav komentāru. Esi pirmais!',
          forumPublish:'Publicēt', forumCancel:'Atcelt',
          forumCommentAs:'Komentē kā', forumCommentPlaceholder:'Raksti komentāru...',
          forumNewPostLabel:'Jauns ieraksts —', forumPostTitle:'Virsraksts', forumPostBody:'Saturs',
          forumBackTo:'Atpakaļ uz',
          forumSearch:'Meklēt ierakstus...', forumSortLatest:'Jaunākie', forumSortOldest:'Vecākie', forumSortPopular:'Populārākie',
          forumFavorites:'Izlase', forumFavorite:'Pievienot izlasei', forumUnfavorite:'Noņemt no izlases',
          forumNoFavorites:'Nav izlases ierakstu šajā kategorijā.', forumEditPost:'Rediģēt ierakstu',
          aboutTitle:'Par Mums', aboutAuthor:'Par autoru', aboutProject:'Par projektu',
          contactTitle:'Kontakti', contactAuthorLabel:'Sazinies ar autoru!',
          contactEmail:'Epasts', contactPhone:'Tālrunis', contactSendMsg:'Nosūti ziņojumu!',
          contactSentMsg:'Paldies! Jūsu ziņojums ir nosūtīts.',
          contactName:'Jūsu vārds un uzvārds', contactEmailField:'Jūsu epasts',
          contactMessage:'Jūsu ziņojums', contactSend:'Sūtīt',
          pomoCycle:'Ciklu', pomoSession:'Sesija',
          pomoWork:'Pomodoro', pomoShortBreak:'Īsā pauze', pomoLongBreak:'Garā pauze',
          pomoStart:'START', pomoPause:'PAUZE', pomoSkip:'Izlaist', pomoReset:'Atiestatīt',
          pomoCycles:'cikli', pomoAutoStart:'Auto-start',
          pomoTasks:'Uzdevumi', pomoQueue:'Sesijas plāns',
          pomoAdd:'Pievienot', pomoAddBlock:'Pievienot bloku',
          pomoNoTasks:'Nav uzdevumu. Pievieno pirmo!',
          pomoNoBlocks:'Nav bloku. Pievieno darba vai pauzes blokus!',
          pomoTotal:'Kopā:', pomoSessionDone:'✓ Sesija pabeigta!', pomoAddBlocksHint:'Pievieno blokus →',
          pomoSettingsTitle:'Laika iestatījumi', pomoPomoMin:'Pomodoro (min)',
          pomoShortMin:'Īsā pauze (min)', pomoLongMin:'Garā pauze (min)',
          pomoSave:'Saglabāt', pomoCancel:'Atcelt',
          pomoAddBlockTitle:'Pievienot bloku', pomoBlockType:'Bloka tips',
          pomoBlockWork:'🍅 Darbs', pomoBlockBreak:'☕ Pauze', pomoBlockLong:'🛋 Garā pauze',
          pomoBlockDur:'Ilgums (min)', pomoBlockTask:'Saistīt ar uzdevumu (neobligāti)',
          taskboardTitle:'Uzdevumu Dēlis', taskboardSearch:'Meklēt...',
          taskboardNewTask:'Jauns uzdevums', taskboardNewCol:'Pievienot kolonnu',
          taskboardNoTasks:'Nav uzdevumu', taskboardDragHint:'Velc šurp vai pievieno zemāk',
          taskboardAddTask:'Pievienot uzdevumu',
          taskboardExpand:'Izvērst', taskboardCollapse:'Sakļaut', taskboardDeleteCol:'Dzēst kolonnu', taskboardEditCol:'Rediģēt kolonnu', taskboardEditColTitle:'Rediģēt kolonnu',
          taskboardPriHigh:'↑ Augsta', taskboardPriMed:'→ Vidēja', taskboardPriLow:'↓ Zema',
          taskboardDone:'Pabeigts', taskboardEdit:'Rediģēt', taskboardDelete:'Dzēst',
          taskboardTasks:'uzdevumi',
          matrixTitle:'Eizenhauera Matrica', matrixClear:'Notīrīt matricu',
          matrixDoNow:'DARI TAGAD', matrixDoSub:'Steidzami + Svarīgi',
          matrixSchedule:'IEPLĀNO', matrixScheduleSub:'Ne-steidzami + Svarīgi',
          matrixDelegate:'DELEĢĒ', matrixDelegateSub:'Steidzami + Nesvarīgi',
          matrixEliminate:'ATMET', matrixEliminateSub:'Ne-steidzami + Nesvarīgi',
          matrixUrgency:'↑ Steidzamība', matrixImportance:'→ Svarīgums',
          matrixUnassigned:'Nepievienotie uzdevumi', matrixAllAssigned:'Visi uzdevumi ir pievienoti matricai',
          calTitle:'Kalendārs', calUnscheduled:'Neieplānotie uzdevumi',
          calNoUnscheduled:'Nav neieplānotu uzdevumu',
          calDays:['P','O','T','C','P','S','Sv'],
          taskDialogEdit:'Rediģēt uzdevumu', taskDialogNew:'Jauns uzdevums',
          taskTitle:'Nosaukums', taskDesc:'Apraksts', taskStatus:'Statuss', taskPriority:'Prioritāte',
          taskDueDate:'Izpildes datums', taskPomodoros:'Paredzamie pomodori',
          taskDelete:'Dzēst', taskCancel:'Atcelt', taskSave:'Saglabāt',
          colDialogTitle:'Jauna kolonna', colName:'Nosaukums', colDesc:'Apraksts',
          colCreated:'Izveidošanas datums', colColor:'Kolonnas krāsa',
          colCancel:'Atcelt', colCreate:'Izveidot',
          statusPending:'Gaida', statusInProgress:'Procesā', statusDone:'Pabeigts',
          priNone:'— Nav',
          loginTitle:'Pieslēgties', loginEmail:'Epasts', loginPass:'Parole',
          loginSubmit:'Pieslēgties', loginNoAccount:'Nav konta?', loginRegister:'Reģistrēties',
          regTitle:'Reģistrēties', regUsername:'Lietotājvārds', regName:'Vārds',
          regSurname:'Uzvārds', regEmail:'Epasts', regPass:'Parole',
          regConfirm:'Apstiprināt paroli', regSubmit:'Reģistrēties',
          regHasAccount:'Jau ir konts?', regLogin:'Pieslēgties',
          back:'Atpakaļ', newTask:'+ Jauns uzdevums',
          colCreatedDate: (d) => new Date(d || Date.now()).toLocaleDateString('lv-LV'),
          skipToContent:'Pāriet uz saturu',
          calPrevMonth:'Iepriekšējais mēnesis', calNextMonth:'Nākamais mēnesis', calPrevYear:'Iepriekšējais gads', calNextYear:'Nākamais gads', calToday:'Šodien',
          pomoFocusTask:'Fokusēt uzdevumu', pomoUnfocusTask:'Noņemt fokusu',
          pomoCompleteTask:'Atzīmēt kā pabeigtu',
          pomoMoveUp:'Pārvietot augšup', pomoMoveDown:'Pārvietot lejup',
          pomoRemoveBlock:'Noņemt bloku',
          quickAddConfirm:'Pievienot uzdevumu',
          addColumnBtn:'Pievienot jaunu kolonnu',
          uploadAvatar: 'Augšupielādēt foto',
          changeAvatar: 'Mainīt foto',
          avatarSaved: 'Foto saglabāts',
          emailUnverified: 'Lūdzu verificē savu e-pastu pirms rakstīšanas forumā.',
          resendVerification: 'Atsūtīt verificēšanas e-pastu',
          verificationSent: 'Verificēšanas e-pasts nosūtīts!',
          myProfile: 'Mans profils',
          profileTitle: 'Profila iestatījumi',
          profileSave: 'Saglabāt profilu', profileSaved: 'Profils saglabāts!',
          profileChangePass: 'Mainīt paroli', profileCurrentPass: 'Pašreizējā parole',
          profileSavePass: 'Mainīt paroli', profilePassSaved: 'Parole mainīta!',
          navAdmin: 'Administrācija',
          adminTitle: 'Administrācijas panelis',
          adminSubtitle: 'Pārvaldīt lietotājus, ierakstus un komentārus',
          adminUsers: 'Lietotāji',
          adminPosts: 'Ieraksti',
          adminComments: 'Komentāri',
          adminSearch: 'Meklēt...',
          adminBan: 'Bloķēt',
          adminUnban: 'Atbloķēt',
          adminBanned: 'Bloķēts',
          adminDelete: 'Dzēst',
          adminNoResults: 'Nav rezultātu.',
          adminInPost: 'Ierakstā',
          adminCategories: 'Kategorijas', adminAddCategory: 'Pievienot kategoriju', adminEditCategory: 'Rediģēt kategoriju', adminEdit: 'Rediģēt',
        }
        const en = {
          navTools:'Tools', navForum:'Forum', navAbout:'About Us', navContact:'Contact',
          navA11y:'Accessibility', navLogin:'Log In', navLogout:'Log Out',
          darkTheme:'Dark mode', lightTheme:'Light mode',
          switchToEn:'Switch to English', switchToLv:'Switch to Latvian',
          toolPomodoro:'Pomodoro', toolMatrix:'Eisenhower Matrix',
          toolCalendar:'Calendar', toolTaskboard:'Task Manager',
          homeTitle:'Home',
          card1Title:'Pomodoro Timer',      card1Desc:'Work and rest on a structured time schedule.',
          card2Title:'Goal Planner',        card2Desc:'Organise your long-term goals.',
          card3Title:'Eisenhower Matrix',   card3Desc:'Prioritise which goals to tackle first.',
          card4Title:'Task Calendar',       card4Desc:'Set reminders for events or deadlines.',
          forumTitle:'Forum', forumSubtitle:'Choose a category to start or read discussions',
          forumAllForums:'All forums', forumNewPost:'New post',
          forumSignInToWrite:'Sign in to write', forumSignInToComment:'Sign in to comment',
          forumNoPosts:'No posts in this category. Be the first!',
          forumComments:'Comments', forumNoComments:'No comments yet. Be the first!',
          forumPublish:'Publish', forumCancel:'Cancel',
          forumCommentAs:'Comment as', forumCommentPlaceholder:'Write a comment...',
          forumNewPostLabel:'New post —', forumPostTitle:'Title', forumPostBody:'Content',
          forumBackTo:'Back to',
          forumSearch:'Search posts...', forumSortLatest:'Latest', forumSortOldest:'Oldest', forumSortPopular:'Popular',
          forumFavorites:'Favorites', forumFavorite:'Add to favorites', forumUnfavorite:'Remove from favorites',
          forumNoFavorites:'No favorited posts in this category.', forumEditPost:'Edit post',
          aboutTitle:'About Us', aboutAuthor:'About the author', aboutProject:'About the project',
          contactTitle:'Contact', contactAuthorLabel:'Get in touch!',
          contactEmail:'Email', contactPhone:'Phone', contactSendMsg:'Send a message!',
          contactSentMsg:'Thank you! Your message has been sent.',
          contactName:'Your full name', contactEmailField:'Your email',
          contactMessage:'Your message', contactSend:'Send',
          pomoCycle:'Cycle', pomoSession:'Session',
          pomoWork:'Pomodoro', pomoShortBreak:'Short break', pomoLongBreak:'Long break',
          pomoStart:'START', pomoPause:'PAUSE', pomoSkip:'Skip', pomoReset:'Reset',
          pomoCycles:'cycles', pomoAutoStart:'Auto-start',
          pomoTasks:'Tasks', pomoQueue:'Session plan',
          pomoAdd:'Add', pomoAddBlock:'Add block',
          pomoNoTasks:'No tasks. Add your first!',
          pomoNoBlocks:'No blocks. Add work or break blocks!',
          pomoTotal:'Total:', pomoSessionDone:'✓ Session complete!', pomoAddBlocksHint:'Add blocks →',
          pomoSettingsTitle:'Timer settings', pomoPomoMin:'Pomodoro (min)',
          pomoShortMin:'Short break (min)', pomoLongMin:'Long break (min)',
          pomoSave:'Save', pomoCancel:'Cancel',
          pomoAddBlockTitle:'Add block', pomoBlockType:'Block type',
          pomoBlockWork:'🍅 Work', pomoBlockBreak:'☕ Break', pomoBlockLong:'🛋 Long break',
          pomoBlockDur:'Duration (min)', pomoBlockTask:'Link to task (optional)',
          taskboardTitle:'Task Board', taskboardSearch:'Search...',
          taskboardNewTask:'New task', taskboardNewCol:'Add column',
          taskboardNoTasks:'No tasks', taskboardDragHint:'Drag here or add below',
          taskboardAddTask:'Add task',
          taskboardExpand:'Expand', taskboardCollapse:'Collapse', taskboardDeleteCol:'Delete column', taskboardEditCol:'Edit column', taskboardEditColTitle:'Edit column',
          taskboardPriHigh:'↑ High', taskboardPriMed:'→ Medium', taskboardPriLow:'↓ Low',
          taskboardDone:'Done', taskboardEdit:'Edit', taskboardDelete:'Delete',
          taskboardTasks:'tasks',
          matrixTitle:'Eisenhower Matrix', matrixClear:'Clear matrix',
          matrixDoNow:'DO NOW',    matrixDoSub:'Urgent + Important',
          matrixSchedule:'SCHEDULE', matrixScheduleSub:'Not urgent + Important',
          matrixDelegate:'DELEGATE', matrixDelegateSub:'Urgent + Not important',
          matrixEliminate:'ELIMINATE', matrixEliminateSub:'Not urgent + Not important',
          matrixUrgency:'↑ Urgency', matrixImportance:'→ Importance',
          matrixUnassigned:'Unassigned tasks', matrixAllAssigned:'All tasks are assigned to the matrix',
          calTitle:'Calendar', calUnscheduled:'Unscheduled tasks',
          calNoUnscheduled:'No unscheduled tasks',
          calDays:['Mo','Tu','We','Th','Fr','Sa','Su'],
          taskDialogEdit:'Edit task', taskDialogNew:'New task',
          taskTitle:'Title', taskDesc:'Description', taskStatus:'Status', taskPriority:'Priority',
          taskDueDate:'Due date', taskPomodoros:'Estimated pomodoros',
          taskDelete:'Delete', taskCancel:'Cancel', taskSave:'Save',
          colDialogTitle:'New column', colName:'Name', colDesc:'Description',
          colCreated:'Created date', colColor:'Column colour',
          colCancel:'Cancel', colCreate:'Create',
          statusPending:'Pending', statusInProgress:'In progress', statusDone:'Done',
          priNone:'— None',
          loginTitle:'Log In', loginEmail:'Email', loginPass:'Password',
          loginSubmit:'Log In', loginNoAccount:"Don't have an account?", loginRegister:'Register',
          regTitle:'Register', regUsername:'Username', regName:'First name',
          regSurname:'Last name', regEmail:'Email', regPass:'Password',
          regConfirm:'Confirm password', regSubmit:'Register',
          regHasAccount:'Already have an account?', regLogin:'Log In',
          back:'Back', newTask:'+ New task',
          colCreatedDate: (d) => new Date(d || Date.now()).toLocaleDateString('en-US'),
          skipToContent:'Skip to content',
          calPrevMonth:'Previous month', calNextMonth:'Next month', calPrevYear:'Previous year', calNextYear:'Next year', calToday:'Today',
          pomoFocusTask:'Focus task', pomoUnfocusTask:'Remove focus',
          pomoCompleteTask:'Mark as complete',
          pomoMoveUp:'Move up', pomoMoveDown:'Move down',
          pomoRemoveBlock:'Remove block',
          quickAddConfirm:'Add task',
          addColumnBtn:'Add new column',
          uploadAvatar: 'Upload photo',
          changeAvatar: 'Change photo',
          avatarSaved: 'Photo saved',
          emailUnverified: 'Please verify your email before posting in the forum.',
          resendVerification: 'Resend verification email',
          verificationSent: 'Verification email sent!',
          myProfile: 'My profile',
          profileTitle: 'Profile settings',
          profileSave: 'Save profile', profileSaved: 'Profile saved!',
          profileChangePass: 'Change password', profileCurrentPass: 'Current password',
          profileSavePass: 'Change password', profilePassSaved: 'Password changed!',
          navAdmin: 'Admin',
          adminTitle: 'Admin Panel',
          adminSubtitle: 'Manage users, posts and comments',
          adminUsers: 'Users',
          adminPosts: 'Posts',
          adminComments: 'Comments',
          adminSearch: 'Search...',
          adminBan: 'Ban',
          adminUnban: 'Unban',
          adminBanned: 'Banned',
          adminDelete: 'Delete',
          adminNoResults: 'No results.',
          adminInPost: 'In post',
          adminCategories: 'Categories', adminAddCategory: 'Add category', adminEditCategory: 'Edit category', adminEdit: 'Edit',
        }
        return this.language === 'en' ? en : lv
      },

      statusOptions() {
        return [
          { title: this.s.statusPending,    value: 'pending' },
          { title: this.s.statusInProgress, value: 'in_progress' },
          { title: this.s.statusDone,       value: 'completed' },
        ]
      },
      priorityOptions() {
        return [
          { title: this.s.taskboardPriHigh, value: 'high' },
          { title: this.s.taskboardPriMed,  value: 'medium' },
          { title: this.s.taskboardPriLow,  value: 'low' },
          { title: this.s.priNone,          value: 'none' },
        ]
      },
    },

    watch: {
      tasks: {
        deep: true,
        handler(val) {
          localStorage.setItem('tf_tasks', JSON.stringify(val))
        }
      },
      columns: {
        deep: true,
        handler(val) {
          localStorage.setItem('tf_columns', JSON.stringify(val))
        }
      },
      language(val) {
        document.documentElement.lang = val === ' en' ? 'en' : 'lv'
      },
      showProfileDialog(open) {
        if (open && this.currentUser) {
          this.profileForm = { name: this.currentUser.name, surname: this.currentUser.surname, username: this.currentUser.username }
          this.profileError = ''
          this.passwordForm = { current_password: '', password: '', password_confirmation: '' }
          this.passwordError = ''
        }
      },
    },

    methods: {
      toggleLanguage() {
        this.language = this.language === 'lv' ? 'en' : 'lv'
        localStorage.setItem('language', this.language)
      },

      toggleTheme() {
        this.darkMode = !this.darkMode
        this.theme.global.name.value = this.darkMode ? 'dark' : 'light'
        localStorage.setItem('darkMode', this.darkMode)
      },

      showSnackbar(text, color = 'error') {
        this.snackbar = { show: true, text, color }
      },

      async handleLogin() {
        this.authError = ''
        this.authLoading = true
        try {
          const res = await fetch('/api/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(this.loginForm)
          })
          const data = await res.json()
          if (!res.ok) {
            this.authError = data.errors?.email?.[0] || data.message || 'Pieslēgšanās neizdevās.'
            return
          }
          this.currentUser = data.user
          this.loginForm = { email: '', password: '' }
          this.showLoginWindow = false
        } catch {
          this.authError = 'Servera kļūda. Mēģiniet vēlreiz.'
        } finally {
          this.authLoading = false
        }
      },

      async handleRegister() {
        this.authError = ''
        this.authLoading = true
        try {
          const res = await fetch('/api/auth/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(this.registerForm)
          })
          const data = await res.json()
          if (!res.ok) {
            const errors = data.errors || {}
            this.authError = Object.values(errors).flat()[0] || data.message || 'Reģistrācija neizdevās.'
            return
          }
          this.currentUser = data.user
          this.registerForm = { username: '', name: '', surname: '', email: '', password: '', password_confirmation: '' }
          this.showRegisterWindow = false
        } catch {
          this.authError = 'Servera kļūda. Mēģiniet vēlreiz.'
        } finally {
          this.authLoading = false
        }
      },

      openTaskDialog(task = null, due_date = '') {
        this.editingTask = task || null
        this.taskForm = task
          ? { title: task.title, description: task.description || '', status: task.status, due_date: task.due_date || '', priority: task.priority || 'medium', est_pomodoros: task.est_pomodoros || 1 }
          : { title: '', description: '', status: 'pending', due_date: due_date || '', priority: 'medium', est_pomodoros: 1 }
        this.showTaskDialog = true
      },

      async saveTask() {
        if (!this.taskForm.title.trim()) return
        try {
          if (this.currentUser) {
            if (this.editingTask) {
              const res = await this.apiFetch(`/api/tasks/${this.editingTask.id}`, {
                method: 'PUT',
                body: JSON.stringify({ ...this.taskForm, column_id: this.editingTask.columnId }),
              })
              if (!res.ok) throw new Error()
              const idx = this.tasks.findIndex(t => t.id === this.editingTask.id)
              if (idx !== -1) this.tasks.splice(idx, 1, { ...this.tasks[idx], ...this.taskForm })
            } else {
              const defaultColId = this.columns[0]?.id ?? null
              const res = await this.apiFetch('/api/tasks', {
                method: 'POST',
                body: JSON.stringify({ ...this.taskForm, column_id: defaultColId }),
              })
              if (!res.ok) throw new Error()
              const created = await res.json()
              this.tasks.push({ id: created.id, quadrant: null, x_pos: null, y_pos: null, columnId: defaultColId, ...this.taskForm })
            }
          } else {
            if (this.editingTask) {
              const idx = this.tasks.findIndex(t => t.id === this.editingTask.id)
              if (idx !== -1) this.tasks.splice(idx, 1, { ...this.tasks[idx], ...this.taskForm })
            } else {
              this.tasks.push({ id: Date.now(), quadrant: null, x_pos: null, y_pos: null, columnId: 'default', ...this.taskForm })
            }
          }
          this.showTaskDialog = false
        } catch {
          this.showSnackbar('Neizdevās saglabāt uzdevumu. Mēģiniet vēlreiz.')
        }
      },

      async deleteTask(id) {
        try {
          if (this.currentUser) {
            const res = await this.apiFetch(`/api/tasks/${id}`, { method: 'DELETE' })
            if (!res.ok) throw new Error()
          }
          const idx = this.tasks.findIndex(t => t.id !== id)
          const delIdx = this.tasks.findIndex(t => t.id === id)
          if (delIdx !== -1) this.tasks.splice(delIdx, 1)
        } catch {
          this.showSnackbar('Neizdevās dzēst uzdevumu. Mēģiniet vēlreiz.')
        }
      },

      async saveColumn() {
        if (!this.columnForm.name.trim()) return
        if (this.currentUser) {
          const res = await this.apiFetch('/api/columns', {
            method: 'POST',
            body: JSON.stringify(this.columnForm),
          })
          const created = await res.json()
          this.columns.push({
            id: created.id, name: created.name,
            description: created.description || '',
            color: created.color || '#8B6E43',
            createdAt: created.created_at,
          })
        } else {
          this.columns.push({
            id: Date.now(), name: this.columnForm.name,
            description: this.columnForm.description,
            createdAt: new Date().toISOString(),
          })
        }
        this.columnForm = { name: '', description: '', color: '#8B6E43' }
        this.showColumnDialog = false
      },

      async deleteColumn(id) {
        if (this.currentUser) {
          await this.apiFetch(`/api/columns/${id}`, { method: 'DELETE' })
          await this.loadFromApi()
        } else {
          this.tasks.forEach((t, i) => {
            if (t.columnId === id) this.tasks.splice(i, 1, { ...t, columnId: 'default' })
          })
          const colIdx = this.columns.findIndex(c => c.id === id)
          if (colIdx !== -1) this.columns.splice(colIdx, 1)
        }
      },

      async logout() {
        await fetch('/api/auth/logout', {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
          credentials: 'include'
        })
        this.currentUser = null
        const localTasks = JSON.parse(localStorage.getItem('tf_tasks') || '[]')
        const localCols  = JSON.parse(localStorage.getItem('tf_columns') || 'null') || [{ id: 'default', name: 'Iesūtne', description: 'Noklusējuma kolonna', createdAt: new Date().toISOString() }]
        this.tasks.splice(0, this.tasks.length, ...localTasks)
        this.columns.splice(0, this.columns.length, ...localCols)
      },

      apiFetch(url, options = {}) {
        return apiFetch(url, options)
      },

      async uploadAvatar(event) {
        const file = event.target.files?.[0]
        if (!file) return
        this.avatarUploading = true
        try {
          const form = new FormData()
          form.append('avatar', file)
          const res = await fetch('/api/user/avatar', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            credentials: 'include',
            body: form,
          })
          if (!res.ok) throw new Error()
          const data = await res.json()
          this.currentUser = { ...this.currentUser, avatar_url: data.avatar_url }
          this.showSnackbar(this.s.avatarSaved, 'success')
        } catch {
          this.showSnackbar('Neizdevās saglabāt foto.')
        } finally {
          this.avatarUploading = false
        }
      },

      async saveProfile() {
        this.profileError = ''
        this.profileSaving = true
        try {
          const res = await this.apiFetch('/api/user/profile', {
            method: 'PUT',
            body: JSON.stringify(this.profileForm),
          })
          const data = await res.json()
          if (!res.ok) {
            this.profileError = Object.values(data.errors || {}).flat()[0] || data.message || 'Kļūda.'
            return
          }
          this.currentUser = { ...this.currentUser, ...data.user }
          this.showSnackbar(this.s.profileSaved, 'success')
        } catch {
          this.profileError = 'Servera kļūda.'
        } finally {
          this.profileSaving = false
        }
      },

      async savePassword() {
        this.passwordError = ''
        this.passwordSaving = true
        try {
          const res = await this.apiFetch('/api/user/password', {
            method: 'PUT',
            body: JSON.stringify(this.passwordForm),
          })
          const data = await res.json()
          if (!res.ok) {
            this.passwordError = Object.values(data.errors || {}).flat()[0] || data.message || 'Kļūda.'
            return
          }
          this.passwordForm = { current_password: '', password: '', password_confirmation: '' }
          this.showSnackbar(this.s.profilePassSaved, 'success')
        } catch {
          this.passwordError = 'Servera kļūda.'
        } finally {
          this.passwordSaving = false
        }
      },

      async resendVerification() {
        // verification disabled
      },

      async loadFromApi() {
        try {
          const [colRes, taskRes] = await Promise.all([
            this.apiFetch('/api/columns'),
            this.apiFetch('/api/tasks'),
          ])
          if (!colRes.ok || !taskRes.ok) throw new Error()
          const cols  = await colRes.json()
          const tasks = await taskRes.json()

          const mappedCols = cols.map(c => ({
            id:          c.id,
            name:        c.name,
            description: c.description || '',
            color:       c.color || '#8B6E43',
            createdAt:   c.created_at,
          }))
          this.columns.splice(0, this.columns.length, ...mappedCols)

          const defaultId = this.columns[0]?.id ?? null

          const mappedTasks = tasks.map(t => ({
            id:          t.id,
            title:       t.title,
            description: t.description || '',
            status:      t.status,
            priority:    t.priority || 'medium',
            due_date:       t.due_date,
            quadrant:       t.quadrant,
            x_pos:          t.x_pos != null ? parseFloat(t.x_pos) : null,
            y_pos:          t.y_pos != null ? parseFloat(t.y_pos) : null,
            columnId:       t.column_id ?? defaultId,
            est_pomodoros:  t.est_pomodoros  ?? 1,
            done_pomodoros: t.done_pomodoros ?? 0,
          }))
          this.tasks.splice(0, this.tasks.length, ...mappedTasks)
        } catch {
          this.showSnackbar('Neizdevās ielādēt datus no servera.')
        }
      },
    },

    async mounted() {
      const savedLang = localStorage.getItem('language')
      if (savedLang === 'en' || savedLang === 'lv') this.language = savedLang

      const saved = localStorage.getItem('darkMode')
      if (saved === 'true') {
        this.darkMode = true
        this.theme.global.name.value = 'dark'
      } else {
        this.darkMode = false
        this.theme.global.name.value = 'light'
      }

      if (window.Notification && Notification.permission !== 'granted') {
        Notification.requestPermission().then((permission) => {
          if (permission !== 'granted') {
            console.log('Notification permission denied')
          }
        })
      }

      // Load pomodoro settings from localStorage
      const savedSettings = localStorage.getItem('pomo_settings')
      if (savedSettings) {
        try {
          const ps = JSON.parse(savedSettings)
          // PomodoroTool manages its own state; just store for reference
          // Settings will be applied by PomodoroTool itself via its own data
        } catch {}
      }

      try {
        const res = await fetch('/api/auth/user', {
          headers: { 'Accept': 'application/json' },
          credentials: 'include'
        })
        const data = await res.json()
        if (data.user) {
          this.currentUser = data.user
          await this.loadFromApi()

          // Load server-side pomodoro settings (PomodoroTool will pick these up via localStorage)
          try {
            const sr = await this.apiFetch('/api/pomodoro/settings')
            if (sr.ok) {
              const ps = await sr.json()
              localStorage.setItem('pomo_settings', JSON.stringify(ps))
            }
          } catch {}
        }
      } catch {
        // not logged in, silently ignore
      }
    }
  }
</script>

<style lang="css">
  /* Skip-to-content link — visible only on keyboard focus */
  .skip-link {
    position: fixed;
    top: -100%;
    left: 8px;
    background: #8B6E43;
    color: #fff;
    padding: 8px 18px;
    z-index: 9999;
    text-decoration: none;
    font-weight: 600;
    border-radius: 0 0 4px 4px;
    transition: top 0.15s;
  }
  .skip-link:focus { top: 0; outline: 3px solid #d4a851; outline-offset: 2px; }

  #simple-app {
    font-family: Bahnschrift, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #e4dccf;
    background-image:
      repeating-linear-gradient(88deg, transparent 0, transparent 5px, rgba(90,60,20,.04) 5px, rgba(90,60,20,.04) 6px),
      repeating-linear-gradient(92deg, transparent 0, transparent 14px, rgba(65,45,15,.03) 14px, rgba(65,45,15,.03) 15px),
      linear-gradient(176deg, #d8cfbe 0%, #e4dccf 50%, #d8cfbe 100%);
    min-height: calc(100vh + 20px);
  }

  .site-header {
    background-color: #1c1c1c !important;
    border-bottom: 2px solid #8B6E43 !important;
    z-index: 1000;
  }

  .site-header .v-toolbar-title { overflow: visible; }

  .header-logo {
    font-size: 1.8rem;
    font-weight: bolder;
    font-family: Bahnschrift, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    white-space: nowrap;
    color: #d4a851;
    letter-spacing: 0.06em;
  }

  .nav-burger-btn { color: #d4a851 !important; }

  /* ── Navigation Drawer ── */
  .nav-drawer { background: #1c1c1c !important; }

  .drawer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 20px 14px;
    border-bottom: 1px solid rgba(139,110,67,.4);
  }

  .drawer-logo {
    font-size: 1.4rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    color: #d4a851;
    font-family: Bahnschrift, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .drawer-section-label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #8B6E43;
    padding: 14px 20px 4px;
  }

  .nav-drawer .v-list-item-title { color: #d4cbbf; }
  .nav-drawer .v-list-item:hover > .v-list-item__overlay { opacity: 0.12; background: #8B6E43; }
  .nav-drawer .v-list-item--active > .v-list-item__overlay { background: #8B6E43; opacity: 0.2; }
  .nav-drawer .v-list-item__prepend .v-icon { color: #8B6E43 !important; }

  .drawer-user {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 20px;
    color: #d4a851;
    font-weight: 600;
    font-size: 0.9rem;
  }

  .drawer-login-btn { padding: 16px 20px; }

  .site-title {
    margin: 0;
    font-size: 2.5rem;
    font-weight: bolder;
    font-family: Bahnschrift, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    text-align: center;
    letter-spacing: 0.04em;
    color: #1c1c1c;
  }

  .main-content { display: block; height: auto; }

  .site-section {
    padding: 20px;
    background-color: #faf6ef;
    margin-bottom: 20px;
    border-radius: 0;
    border-left: 3px solid #8B6E43;
    margin-top: 0;
  }

  .hero-section {
    background-color: #faf6ef;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    min-height: 80vh;
    gap: 20px;
    padding: 60px 40px;
    border-bottom: 2px solid #c8b89a;
  }

  .hero-section p { max-width: 720px; text-align: center; color: #444; }

  .description { background-color: #faf6ef; border-radius: 0; }


/* Cards styling */

  .card {
    display: flex;
    flex-direction: column;
    background-color: #faf6ef;
    color: #1c1c1c;
    margin: 10px;
    transition: transform 0.25s, box-shadow 0.25s;
    border: 2px solid #8B6E43;
    border-radius: 0;
    box-sizing: border-box;
    width: auto;
    min-height: 300px;
    height: 100%;
    cursor: pointer;
  }

  .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(100,72,20,0.28);
    z-index: 2;
  }

  .card-in-card {
    background-color: #f0e8d8;
    padding: 24px 16px;
    margin: 16px;
    border-radius: 0;
    text-align: center;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10px;
  }

  .card-in-card h3 { margin: 0; font-size: 1.1rem; }

  #pomodoro-card, #task-card, #eisenhower-card, #calendar-card {
    background-color: #faf6ef;
    color: #1c1c1c;
  }

  .features-grid { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px; }

  .feature-card {
    flex: 1 1 calc(50% - 20px);
    padding: 15px;
    border: 1px solid #c8b89a;
    background-color: #faf6ef;
    border-radius: 0;
  }

  .contact-content { display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap; }

  .contact-info,
  .contact-form {
    flex: 1;
    min-width: 250px;
    padding: 15px;
    background-color: #faf6ef;
    border-radius: 0;
  }

  .contact-form .v-text-field,
  .contact-form .v-textarea { background-color: #f0e8d8; }


  /* ── Pomodoro ─────────────────────────────────────── */

  .pomo-container { max-width: 1100px !important; }

  .pomo-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: start;
  }

  /* Timer card */
  .pomo-card { text-align: center; transition: background 0.4s; }
  .pomo-phase-work      { background: linear-gradient(135deg, #7b2d2d 0%, #4a2010 100%) !important; color: #fff !important; }
  .pomo-phase-break     { background: linear-gradient(135deg, #1a5c35 0%, #2d7a4f 100%) !important; color: #fff !important; }
  .pomo-phase-longBreak { background: linear-gradient(135deg, #1a3a5c 0%, #2d5a7a 100%) !important; color: #fff !important; }
  .pomo-card-body { padding: 28px 24px 20px !important; }

  /* Top row: mode switcher + gear */
  .pomo-top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
  }
  .pomo-mode-switcher {
    display: flex;
    background: rgba(0,0,0,0.3);
    border-radius: 2px;
    padding: 3px;
    gap: 2px;
  }
  .pomo-mode-btn {
    background: transparent;
    border: none;
    border-radius: 1px;
    color: rgba(255,255,255,0.75);
    cursor: pointer;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 4px 14px;
    transition: background 0.2s, color 0.2s;
    letter-spacing: 0.03em;
  }
  .pomo-mode-btn.active { background: rgba(255,255,255,0.25); color: #fff; }
  .pomo-mode-btn:hover  { color: #fff; }

  .pomo-gear-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: 0;
    color: #fff;
    cursor: pointer;
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.2s;
  }
  .pomo-gear-btn:hover { background: rgba(255,255,255,0.35); }

  /* Phase tabs */
  .pomo-tabs {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-bottom: 12px;
  }
  .pomo-tab {
    background: rgba(255,255,255,0.15);
    border: none; border-radius: 2px;
    color: rgba(255,255,255,0.8);
    cursor: pointer; font-size: 0.82rem; font-weight: 600;
    padding: 5px 16px;
    transition: background 0.2s;
  }
  .pomo-tab.active, .pomo-tab:hover { background: rgba(255,255,255,0.32); color: #fff; }

  /* Queue indicator */
  .pomo-queue-indicator {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.9);
    margin-bottom: 12px;
    min-height: 24px;
    display: flex; align-items: center; justify-content: center;
  }
  .pomo-queue-pos  { opacity: 0.7; }
  .pomo-queue-hint { opacity: 0.6; font-style: italic; }

  /* Focus label */
  .pomo-focus-label {
    font-size: 0.85rem;
    opacity: 0.85;
    margin-bottom: 4px;
    display: flex; align-items: center; justify-content: center;
  }

  /* Timer */
  .pomo-timer {
    font-size: 88px;
    font-weight: 700;
    letter-spacing: -2px;
    line-height: 1;
    color: #fff;
    margin: 12px 0 20px;
    font-variant-numeric: tabular-nums;
  }

  /* Controls */
  .pomo-controls { display: flex; justify-content: center; align-items: center; gap: 10px; margin-bottom: 16px; }
  .pomo-start-btn .v-btn__content { font-size: 0.95rem; font-weight: 700; letter-spacing: 3px; }
  .pomo-start-btn { min-width: 130px !important; background: rgba(255,255,255,0.92) !important; color: #1c1c1c !important; border-radius: 0 !important; }
  .pomo-icon-btn  { color: rgba(255,255,255,0.8) !important; }

  /* Meta row */
  .pomo-meta-row { display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; opacity: 0.85; }
  .pomo-session-label { font-size: 0.8rem; }
  .pomo-autostart .v-label        { font-size: 0.78rem; color: rgba(255,255,255,0.85); }
  .pomo-autostart .v-switch__track{ opacity: 0.6; }

  /* Right panel */
  .pomo-right-panel { display: flex; flex-direction: column; }
  .pomo-panel-tabs  { background: transparent !important; }

  /* Task list card */
  .pomo-tasks-card   { background: #faf6ef !important; border-radius: 0 !important; }
  .pomo-tasks-header { display: flex; justify-content: space-between; align-items: center; }
  .pomo-tasks-title  { font-size: 0.9rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #555; }
  .pomo-empty        { text-align: center; color: #aaa; padding: 20px 0; font-size: 0.88rem; }

  .pomo-task-row {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 6px; border-radius: 0;
    transition: background 0.15s;
  }
  .pomo-task-row:hover           { background: #f0e8d8; }
  .pomo-task-focused             { background: #f0e8d8 !important; border-left: 3px solid #8B6E43; }
  .pomo-task-done .pomo-task-name{ text-decoration: line-through; color: #aaa; }

  .pomo-task-name {
    flex: 1; font-size: 0.92rem; cursor: pointer;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .pomo-task-name:hover { text-decoration: underline; }
  .pomo-task-count { font-size: 0.78rem; color: #888; white-space: nowrap; display: flex; align-items: center; }

  /* Queue rows */
  .pomo-queue-row {
    display: flex; align-items: center; gap: 6px;
    padding: 7px 6px; border-radius: 0;
    transition: background 0.15s;
  }
  .pomo-queue-row:hover  { background: #f0e8d8; }
  .pomo-queue-active     { background: #f0e8d8 !important; border-left: 3px solid #8B6E43; font-weight: 600; }
  .pomo-queue-done       { opacity: 0.4; text-decoration: line-through; }
  .pomo-queue-label      { flex: 1; font-size: 0.92rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .pomo-queue-dur        { font-size: 0.8rem; color: #888; white-space: nowrap; margin-right: 4px; }
  .pomo-queue-total      { font-size: 0.82rem; color: #888; text-align: right; border-top: 1px solid #e0d8cc; padding-top: 8px; }

  @media (max-width: 700px) {
    .pomo-layout  { grid-template-columns: 1fr; }
    .pomo-timer   { font-size: 72px; }
  }

  /* Footer */
  #footer-section {
    background-color: #1c1c1c;
    color: #8B6E43;
    text-align: center;
    padding: 20px;
  }

  /* ── Board toolbar ── */
  .board-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
  }
  .board-title {
    font-size: 1.4rem;
    font-weight: 700;
  }

  /* ── Trello Board ── */
  .trello-board {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding-bottom: 12px;
    align-items: flex-start;
  }
  .trello-column {
    min-width: 260px;
    max-width: 260px;
    background: #f0e8d8;
    border-radius: 0;
    border: 1px solid #c8b89a;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
  }
  .trello-col-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 12px 10px 10px;
    border-bottom: 3px solid #8B6E43;
    border-radius: 0;
    background: #e8dcc8;
  }
  .trello-col-meta {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
    min-width: 0;
  }
  .trello-col-name {
    font-weight: 700;
    font-size: 0.92rem;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .trello-col-desc {
    font-size: 0.76rem;
    color: #555;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .trello-col-time {
    font-size: 0.7rem;
    color: #aaa;
  }
  .trello-col-body {
    padding: 10px;
    min-height: 220px;
    flex: 1;
  }
  .trello-task-card {
    border-left: 3px solid #8B6E43 !important;
    border-radius: 0 !important;
    background: #faf6ef !important;
    cursor: grab;
  }
  .trello-task-card:active { cursor: grabbing; }
  .trello-task-title {
    font-size: 0.86rem;
    font-weight: 600;
    flex: 1;
    line-height: 1.3;
  }
  .trello-task-desc {
    font-size: 0.76rem;
    color: #888;
    margin: 3px 0 0;
  }
  .trello-empty {
    text-align: center;
    color: #bbb;
    padding: 28px 0;
    font-size: 0.82rem;
  }
  .trello-add-col {
    min-width: 260px;
    max-width: 260px;
    min-height: 80px;
    border: 2px dashed #c8b89a;
    border-radius: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    cursor: pointer;
    color: #bbb;
    flex-shrink: 0;
    transition: border-color 0.2s, color 0.2s;
    font-size: 0.85rem;
  }
  .trello-add-col:hover { border-color: #8B6E43; color: #8B6E43; }

  .trello-column.drag-over { outline: 2px dashed #8B6E43; background: #f0e8d8; }

  .trello-column.col-collapsed { min-height: unset; }
  .trello-column.col-collapsed .trello-col-header { border-radius: 0; }

  .trello-col-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    border-radius: 0;
    padding: 0 6px;
    font-size: 0.7rem;
    font-weight: 700;
    color: #fff;
  }

  /* Empty state */
  .trello-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24px 8px 16px;
    color: #ccc;
    font-size: 0.82rem;
    gap: 4px;
    text-align: center;
  }
  .trello-empty p { margin: 0; }
  .trello-empty-hint { font-size: 0.74rem; color: #ddd; }

  /* Task actions fade in on hover */
  .task-actions { opacity: 1; }
  .chip-del {
    margin-left: 6px; opacity: 0.5; cursor: pointer; font-weight: bold; font-size: 0.9em;
    background: none; border: none; padding: 0; line-height: inherit; color: inherit;
  }
  .chip-del:hover { opacity: 1; color: #ef5350; }

  /* Quick-add */
  .col-quick-add {
    padding: 6px 10px 10px;
  }
  .quick-add-btn {
    color: #aaa !important;
    font-size: 0.82rem;
    justify-content: flex-start !important;
    letter-spacing: 0;
  }
  .quick-add-btn:hover { color: #8B6E43 !important; }

  /* Column color picker in dialog */
  .col-color-picker {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 4px;
  }
  .col-color-swatch {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    cursor: pointer;
    transition: transform 0.15s, outline-offset 0.15s;
    outline-offset: 2px;
  }
  .col-color-swatch:hover { transform: scale(1.2); }

  /* ── Eisenhower Matrix (free-position canvas) ── */
  .mfree-wrap {
    display: flex;
    align-items: stretch;
    gap: 8px;
  }
  .mfree-y-label {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    font-weight: 600;
    font-size: 0.8rem;
    color: #8B6E43;
    letter-spacing: 1px;
    text-align: center;
    min-width: 20px;
    flex-shrink: 0;
  }
  .mfree-canvas-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
  }
  .mfree-x-label {
    text-align: center;
    font-weight: 600;
    font-size: 0.8rem;
    color: #8B6E43;
    letter-spacing: 1px;
    margin-top: 6px;
  }
  .mfree-canvas {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    border: 2px solid #8B6E43;
    overflow: hidden;
    cursor: default;
    user-select: none;
  }
  /* Quadrant backgrounds — each covers exactly one quarter */
  .mfree-q {
    position: absolute;
    width: 50%; height: 50%;
    display: flex;
    pointer-events: none;
  }
  .mfree-q-do       { top:0; left:0;   background: rgba(192, 57, 43, 0.07);  align-items: flex-start;    justify-content: flex-start; }
  .mfree-q-schedule { top:0; right:0;  background: rgba(90, 154, 74, 0.07);  align-items: flex-start;    justify-content: flex-end;   }
  .mfree-q-delegate { bottom:0; left:0;  background: rgba(200,125, 42, 0.07); align-items: flex-end;      justify-content: flex-start; }
  .mfree-q-eliminate{ bottom:0; right:0; background: rgba(139,110, 67, 0.06); align-items: flex-end;      justify-content: flex-end;   }
  .mfree-q-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    opacity: 0.35;
    line-height: 1.35;
    padding: 6px 8px;
    letter-spacing: 0.04em;
  }
  .mfree-q-label-right { text-align: right; }
  /* Divider lines */
  .mfree-vline {
    position: absolute;
    left: 50%; top: 0; bottom: 0;
    width: 1px;
    background: rgba(139,110,67,0.3);
    pointer-events: none;
  }
  .mfree-hline {
    position: absolute;
    top: 50%; left: 0; right: 0;
    height: 1px;
    background: rgba(139,110,67,0.3);
    pointer-events: none;
  }
  /* Task chips — positioned absolutely, centered on their coordinate */
  .mfree-chip {
    position: absolute;
    transform: translate(-50%, -50%);
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(255,255,255,0.92);
    border: 1px solid #8B6E43;
    border-radius: 12px;
    padding: 3px 6px 3px 9px;
    font-size: 0.76rem;
    max-width: 130px;
    cursor: grab;
    user-select: none;
    z-index: 10;
    box-shadow: 0 1px 5px rgba(0,0,0,0.13);
    white-space: nowrap;
    transition: box-shadow 0.1s;
  }
  .mfree-chip:hover { box-shadow: 0 3px 10px rgba(0,0,0,0.2); z-index: 11; }
  .mfree-chip-dragging { cursor: grabbing; box-shadow: 0 6px 18px rgba(0,0,0,0.28); z-index: 20; transition: none; }
  .mfree-chip-title { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100px; }
  .mfree-chip-del {
    flex-shrink: 0;
    background: none;
    border: none;
    cursor: pointer;
    color: #999;
    font-size: 1rem;
    line-height: 1;
    padding: 0 2px;
    border-radius: 50%;
    transition: color 0.15s;
  }
  .mfree-chip-del:hover { color: #c0392b; }

  /* ── Shared task pool ── */
  .task-pool {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 56px;
    padding: 12px;
    background: #ede4d4;
    border-radius: 0;
    border: 2px dashed #c8b89a;
    align-content: flex-start;
  }
  .pool-empty { width: 100%; text-align: center; color: #aaa; font-size: 0.85rem; }
  .pool-chip {
    background: #8B6E43;
    color: #fff;
    border-radius: 0;
    padding: 5px 14px;
    font-size: 0.82rem;
    cursor: grab;
    user-select: none;
    box-shadow: 0 1px 4px rgba(100,72,20,0.3);
  }
  .pool-chip:active { cursor: grabbing; opacity: 0.7; }

  /* ── Calendar ── */
  .cal-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }
  .cal-nav-group {
    display: flex;
    align-items: center;
    gap: 2px;
  }
  .cal-month-label {
    font-size: 1.1rem;
    font-weight: 600;
    min-width: 120px;
    text-align: center;
    text-transform: capitalize;
  }
  .cal-year-label {
    font-size: 1.1rem;
    font-weight: 600;
    min-width: 56px;
    text-align: center;
  }
  .cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
  }
  .cal-day-header {
    text-align: center;
    font-weight: 700;
    padding: 6px 0;
    font-size: 0.8rem;
    color: #8B6E43;
  }
  .cal-day-cell {
    min-height: 120px;
    border: 1px solid #d8ccba;
    border-radius: 0;
    padding: 5px;
    background: #faf6ef;
    cursor: pointer;
    transition: background 0.15s;
  }
  .cal-day-cell:hover { background: #f0e8d8; }
  .cal-other { background: #f5f0e8; cursor: default; }
  .cal-other:hover { background: #f5f0e8; }
  .cal-today { border-color: #8B6E43 !important; border-width: 2px !important; }
  .cal-day-num {
    display: inline-block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #444;
    margin-bottom: 3px;
    width: 22px;
    text-align: center;
  }
  .cal-today .cal-day-num {
    background: #8B6E43;
    color: #fff;
    border-radius: 0;
    line-height: 22px;
    height: 22px;
  }
  .cal-other .cal-day-num { color: #ccc; }
  .cal-task-block {
    background: #8B6E43;
    color: #fff;
    border-radius: 0;
    padding: 1px 5px;
    font-size: 0.72rem;
    margin-bottom: 2px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
    user-select: none;
  }
  .cal-task-block:hover { background: #6b4f2a; }

  /* ── Tool bottom navigation ── */
  .tool-bottom-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    padding: 12px 4px;
    border-top: 1px solid rgba(139, 110, 67, 0.2);
  }
  .tool-links {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
  }

  @media (max-width: 768px) {
    .site-title { font-size: 1.5rem; }
    .feature-card { flex: 1 1 100%; }
    .contact-info, .contact-form { flex: 1 1 100%; }
    .timer-display { font-size: 36px; }
    .hero-section { padding: 40px 20px; min-height: 60vh; }
    .cal-day-cell { min-height: 60px; }
    .mfree-canvas { aspect-ratio: 1 / 1; }
    .trello-board { padding-bottom: 80px; }
  }

  /* ── Forum ─────────────────────────────────────────────── */

  .forum-container { max-width: 900px !important; }

  .forum-header { text-align: center; }
  .forum-title  { font-size: 1.6rem; font-weight: 700; margin: 0 0 4px; }
  .forum-subtitle { color: #555; font-size: 0.95rem; margin: 0; }

  .forum-cat-card {
    border-left: 5px solid #8B6E43;
    cursor: pointer;
    border-radius: 0 !important;
    background: #faf6ef !important;
    transition: box-shadow 0.2s, transform 0.15s;
  }
  .forum-cat-card:hover { box-shadow: 0 4px 18px rgba(100,72,20,0.18); transform: translateY(-2px); }
  .forum-cat-name { font-weight: 700; font-size: 0.97rem; margin-bottom: 2px; }
  .forum-cat-desc { font-size: 0.82rem; color: #666; }

  .forum-cat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 10px;
  }

  .forum-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
  }
  .forum-search-field { flex: 1 1 200px; min-width: 160px; }
  .forum-sort-btns { display: flex; gap: 6px; flex-wrap: wrap; }

  .forum-fav-btn {
    flex-shrink: 0;
    background: none;
    border: none;
    cursor: pointer;
    color: #bbb;
    padding: 4px;
    border-radius: 4px;
    line-height: 1;
    transition: color 0.15s;
  }
  .forum-fav-btn:hover { color: #d4a851; }
  .forum-fav-btn.active { color: #d4a851; }

  .forum-post-item {
    cursor: pointer;
    border-radius: 0 !important;
    background: #faf6ef !important;
    transition: box-shadow 0.15s;
  }
  .forum-post-item:hover { box-shadow: 0 2px 12px rgba(100,72,20,0.14); }
  .forum-post-title { font-weight: 600; font-size: 0.97rem; margin-bottom: 5px; }
  .forum-post-meta  { font-size: 0.78rem; color: #666; display: flex; align-items: center; flex-wrap: wrap; }

  .forum-empty-state {
    text-align: center;
    color: #aaa;
    padding: 32px 0;
    font-size: 0.9rem;
  }

  .forum-post-detail-title { font-size: 1.35rem; font-weight: 700; margin: 0 0 6px; }
  .forum-post-detail-meta  { font-size: 0.82rem; color: #999; display: flex; align-items: center; }
  .forum-post-body         { white-space: pre-wrap; line-height: 1.65; font-size: 0.96rem; }

  .forum-comment-form-label { font-size: 0.85rem; color: #777; }

  .forum-comments-header { font-weight: 700; font-size: 1rem; color: #555; }

  .forum-comment-header {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 6px;
  }
  .forum-comment-author { font-weight: 600; font-size: 0.88rem; }
  .forum-comment-time   { font-size: 0.78rem; color: #aaa; margin-left: 8px; }
  .forum-comment-body   { white-space: pre-wrap; font-size: 0.93rem; line-height: 1.6; }

  .forum-comment-votes { display: flex; align-items: center; gap: 8px; }

  .forum-comment-card { border-radius: 0 !important; background: #faf6ef !important; }

  .forum-vote-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border: 1px solid #d4c0a0;
    border-radius: 0;
    background: transparent;
    cursor: pointer;
    font-size: 0.82rem;
    color: #888;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
  }
  .forum-vote-btn:hover { background: #f0e8d8; }
  .forum-vote-up.active  { border-color: #43a047; background: #e8f5e9; color: #2e7d32; }
  .forum-vote-down.active{ border-color: #e53935; background: #ffebee; color: #c62828; }

  /* Forum user avatar */
  .forum-user-avatar { width: 16px; height: 16px; border-radius: 50%; object-fit: cover; vertical-align: middle; margin-right: 4px; }

  .forum-comment-own-actions { margin-left: auto; display: flex; gap: 2px; }
  .forum-comment-header { display: flex; align-items: center; flex-wrap: wrap; gap: 4px; margin-bottom: 4px; }

  /* ── Desktop navigation ── */
  .desktop-nav {
    display: flex;
    align-items: center;
    gap: 2px;
    padding-right: 14px;
  }

  .desktop-nav-btn {
    color: #d4cbbf !important;
    font-size: 0.88rem !important;
    letter-spacing: 0.02em;
    text-transform: none !important;
    min-width: unset !important;
  }

  .desktop-nav-btn:hover { color: #d4a851 !important; }

  .desktop-nav-active { color: #d4a851 !important; border-bottom: 2px solid #d4a851; border-radius: 0 !important; }

  .desktop-nav-username {
    color: #d4a851;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 0 8px;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
  }

  .desktop-nav-login { margin-left: 4px; border-radius: 0 !important; }

  .riki-arrow {
    font-size: 0.7rem;
    opacity: 0.75;
    margin-left: 3px;
    line-height: 1;
  }

  .tools-dropdown {
    background: #1c1c1c !important;
    border: 1px solid #8B6E43;
    border-radius: 0 !important;
    min-width: 210px;
  }

  .tools-dropdown .v-list-item-title { color: #d4cbbf; font-size: 0.88rem; }
  .tools-dropdown .v-list-item__prepend .v-icon { color: #8B6E43 !important; }
  .tools-dropdown .v-list-item:hover > .v-list-item__overlay { opacity: 0.12; background: #8B6E43; }
  .tools-dropdown .v-list-item--active > .v-list-item__overlay { background: #8B6E43; opacity: 0.25; }

  /* Mobile-only burger, hidden on desktop */
  .nav-burger-btn { display: none !important; }

  @media (max-width: 768px) {
    .desktop-nav { display: none; }
    .nav-burger-btn { display: inline-flex !important; }
  }

  /* ── Burger icon (3 lines) ── */
  .burger-icon {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    width: 22px;
    height: 22px;
  }

  .burger-icon span {
    display: block;
    height: 2px;
    width: 100%;
    background: #d4a851;
    border-radius: 1px;
  }

  /* ── Mobile drawer covers full height (over header) ── */
  .nav-drawer { z-index: 1200 !important; top: 0 !important; height: 100% !important; }

  /* ── Mobile overlay behind drawer ── */
  .mobile-nav-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 1100;
  }
  @media (max-width: 768px) {
    .mobile-nav-overlay { display: block; }
  }

  /* ── Mobile: horizontal-scroll card row ── */
  @media (max-width: 599px) {
    .home-cards-row {
      flex-wrap: nowrap !important;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      -webkit-overflow-scrolling: touch;
      padding-bottom: 12px;
      gap: 0;
    }
    .home-card-col {
      flex: 0 0 82vw !important;
      max-width: 82vw !important;
      width: 82vw !important;
      scroll-snap-align: center;
    }
  }

  /* ── CSS theme toggle icons ── */
  .theme-toggle-btn {
    background: transparent;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    transition: background 0.2s;
    padding: 0;
  }
  .theme-toggle-btn:hover { background: rgba(212,168,81,0.12); }

  /* Crescent moon: gold circle with a "bite" matching the header background */
  .theme-moon {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #d4a851;
    box-shadow: -5px 3px 0 0 #1c1c1c;
  }

  /* Sun: glowing gold circle */
  .theme-sun {
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: #d4a851;
    box-shadow:
      0 0 0 2px rgba(212,168,81,0.25),
      0 -8px 0 -3px #d4a851,
      0  8px 0 -3px #d4a851,
      -8px 0 0 -3px #d4a851,
       8px 0 0 -3px #d4a851,
      -6px -6px 0 -3.5px #d4a851,
       6px -6px 0 -3.5px #d4a851,
      -6px  6px 0 -3.5px #d4a851,
       6px  6px 0 -3.5px #d4a851;
  }

  /* Drawer icon: slightly larger, with left margin matching prepend slot */
  .theme-icon-drawer {
    margin-right: 20px;
    margin-left: 4px;
  }

  .lang-flag {
    font-size: 1.1rem;
    line-height: 1;
    margin-right: 10px;
  }
  .lang-flag-drawer {
    margin-right: 20px;
    margin-left: 4px;
    font-size: 1.2rem;
  }

  /* ── Profile / Avatar ── */
  .nav-avatar { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; vertical-align: middle; margin-right: 4px; }
  .nav-avatar-initials { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: #8B6E43; color: #fff; font-size: 11px; font-weight: 700; vertical-align: middle; margin-right: 4px; }
  .profile-avatar-wrap { position: relative; display: inline-block; }
  .profile-avatar-img { width: 96px; height: 96px; border-radius: 50%; object-fit: cover; display: block; margin: 0 auto; }
  .profile-avatar-placeholder { width: 96px; height: 96px; border-radius: 50%; background: #8B6E43; color: #fff; font-size: 2.2rem; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto; }
  .profile-avatar-edit { position: absolute; bottom: 2px; right: 2px; background: #fff; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 1px 4px rgba(0,0,0,.2); }
</style>

<!-- Non-scoped: dark mode overrides keyed off Vuetify's own .v-theme--dark class,
     which the v-app root element reliably carries when dark mode is active. -->
<style lang="css">
  #simple-app.v-theme--dark,
  #simple-app.v-theme--dark .v-main {
    background-color: #1a1816 !important;
    background-image: none !important;
    color: #e0d8cc !important;
  }

  /* App-level background */
  .v-theme--dark .v-application__wrap { background-color: #1a1816; }

  /* Typography */
  .v-theme--dark .site-title  { color: #e0d8cc !important; }
  .v-theme--dark .hero-section { background-color: #201e1a !important; border-bottom-color: #3a3020 !important; }
  .v-theme--dark .hero-section p { color: #9a8878; }

  /* Generic sections / cards */
  .v-theme--dark .site-section  { background-color: #252220 !important; }
  .v-theme--dark .description   { background-color: #252220 !important; }
  .v-theme--dark .card          { background-color: #252220 !important; color: #e0d8cc !important; border-color: #6b4f2a !important; }
  .v-theme--dark .card-in-card  { background-color: #2e2a26 !important; color: #e0d8cc !important; }
  .v-theme--dark .card-in-card h3 { color: #e0d8cc; }
  .v-theme--dark #pomodoro-card,
  .v-theme--dark #task-card,
  .v-theme--dark #eisenhower-card,
  .v-theme--dark #calendar-card { background-color: #252220 !important; color: #e0d8cc !important; }
  .v-theme--dark .feature-card  { background-color: #252220 !important; border-color: #3a3020 !important; color: #e0d8cc; }
  .v-theme--dark .contact-info,
  .v-theme--dark .contact-form  { background-color: #252220 !important; color: #e0d8cc; }
  .v-theme--dark .about-section { background-color: #252220 !important; }

  /* Contact form inputs have a hardcoded light background in scoped CSS — override it */
  .v-theme--dark .contact-form .v-text-field,
  .v-theme--dark .contact-form .v-textarea { background-color: transparent !important; }

  /* Pomodoro */
  .v-theme--dark .pomo-tasks-card   { background: #252220 !important; }
  .v-theme--dark .pomo-tasks-title  { color: #9a8878 !important; }
  .v-theme--dark .pomo-task-name    { color: #e0d8cc !important; }
  .v-theme--dark .pomo-task-row:hover     { background: #2e2a26 !important; }
  .v-theme--dark .pomo-task-focused       { background: #2e2a26 !important; }
  .v-theme--dark .pomo-queue-row:hover    { background: #2e2a26 !important; }
  .v-theme--dark .pomo-queue-active       { background: #2e2a26 !important; }
  .v-theme--dark .pomo-queue-total  { border-top-color: #3a3020 !important; color: #9a8878 !important; }
  .v-theme--dark .pomo-queue-dur    { color: #9a8878 !important; }
  .v-theme--dark .pomo-empty        { color: #6a5a4a !important; }

  /* Taskboard */
  .v-theme--dark .board-title          { color: #e0d8cc !important; }
  .v-theme--dark .trello-column        { background: #2a2520 !important; border-color: #3a3020 !important; }
  .v-theme--dark .trello-col-header    { background: #201e18 !important; }
  .v-theme--dark .trello-col-name      { color: #e0d8cc !important; }
  .v-theme--dark .trello-col-desc      { color: #9a8878 !important; }
  .v-theme--dark .trello-col-time      { color: #5a5040 !important; }
  .v-theme--dark .trello-task-card     { background: #252220 !important; }
  .v-theme--dark .trello-task-title    { color: #e0d8cc !important; }
  .v-theme--dark .trello-task-desc     { color: #9a8878 !important; }
  .v-theme--dark .trello-empty         { color: #4a4030 !important; }
  .v-theme--dark .trello-empty-hint    { color: #3a3020 !important; }
  .v-theme--dark .trello-add-col       { border-color: #3a3020 !important; color: #6a5a4a !important; }
  .v-theme--dark .trello-add-col:hover { border-color: #8B6E43 !important; color: #8B6E43 !important; }
  .v-theme--dark .quick-add-btn        { color: #6a5a4a !important; }
  .v-theme--dark .trello-column.drag-over { background: #2e2a26 !important; }

  /* Eisenhower Matrix */
  .v-theme--dark .mfree-canvas   { border-color: #6b5030 !important; }
  .v-theme--dark .mfree-q-do     { background: rgba(180,60,40,0.12) !important; }
  .v-theme--dark .mfree-q-schedule { background: rgba(70,140,60,0.10) !important; }
  .v-theme--dark .mfree-q-delegate { background: rgba(190,120,40,0.10) !important; }
  .v-theme--dark .mfree-q-eliminate { background: rgba(90,70,40,0.08) !important; }
  .v-theme--dark .mfree-chip     { background: rgba(40,35,28,0.92) !important; border-color: #6b5030 !important; color: #e0d8cc !important; }

  /* Calendar */
  .v-theme--dark .cal-day-cell        { background: #252220 !important; border-color: #3a3020 !important; }
  .v-theme--dark .cal-day-cell:hover  { background: #2e2a26 !important; }
  .v-theme--dark .cal-other           { background: #1c1a18 !important; }
  .v-theme--dark .cal-other:hover     { background: #1c1a18 !important; }
  .v-theme--dark .cal-day-num         { color: #a09080 !important; }
  .v-theme--dark .cal-other .cal-day-num { color: #4a4a4a !important; }
  .v-theme--dark .task-pool           { background: #201e18 !important; border-color: #3a3020 !important; }
  .v-theme--dark .pool-empty          { color: #5a5040 !important; }

  /* Forum */
  .v-theme--dark .forum-fav-btn        { color: #5a4a3a !important; }
  .v-theme--dark .forum-fav-btn:hover  { color: #d4a851 !important; }
  .v-theme--dark .forum-fav-btn.active { color: #d4a851 !important; }
  .v-theme--dark .forum-subtitle      { color: #9a8878 !important; }
  .v-theme--dark .forum-cat-card      { background: #252220 !important; }
  .v-theme--dark .forum-cat-name      { color: #e0d8cc !important; }
  .v-theme--dark .forum-cat-desc      { color: #9a8878 !important; }
  .v-theme--dark .forum-post-item     { background: #252220 !important; }
  .v-theme--dark .forum-post-title    { color: #e0d8cc !important; }
  .v-theme--dark .forum-post-meta     { color: #6a5a4a !important; }
  .v-theme--dark .forum-post-body     { color: #d0c8bc !important; }
  .v-theme--dark .forum-post-detail-title { color: #e0d8cc !important; }
  .v-theme--dark .forum-comment-card  { background: #252220 !important; }
  .v-theme--dark .forum-comment-body  { color: #d0c8bc !important; }
  .v-theme--dark .forum-comment-time  { color: #6a5a4a !important; }
  .v-theme--dark .forum-comment-author { color: #e0d8cc !important; }
  .v-theme--dark .forum-comments-header { color: #9a8878 !important; }
  .v-theme--dark .forum-empty-state   { color: #6a5a4a !important; }
  .v-theme--dark .forum-vote-btn      { border-color: #3a3020 !important; color: #9a8878 !important; }
  .v-theme--dark .forum-vote-btn:hover { background: #2e2a26 !important; }
  .v-theme--dark .forum-comment-form-label { color: #9a8878 !important; }

  /* ── Admin badge ── */
  .forum-admin-badge {
    display: inline-block;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    color: #1565c0;
    background: #e3f0ff;
    border: 1px solid #90caf9;
    border-radius: 3px;
    padding: 0px 5px;
    margin-left: 5px;
    vertical-align: middle;
    line-height: 1.6;
  }

  .v-theme--dark .forum-admin-badge {
    color: #90caf9;
    background: #0d2a4a;
    border-color: #1565c0;
  }

  /* ── Admin nav button ── */
  .admin-nav-btn { color: #2196f3 !important; font-weight: 700 !important; }
  .admin-nav-btn:hover { color: #90caf9 !important; }

  .admin-drawer-item .v-list-item-title { color: #90caf9 !important; }
  .admin-drawer-item .v-list-item__prepend .v-icon { color: #2196f3 !important; }

  /* ── Admin panel ── */
  .admin-container { max-width: 900px !important; }

  .admin-row { border-left: 3px solid #2196f3 !important; }

  .admin-username { font-weight: 600; }
  .admin-post-title { font-weight: 600; font-size: 0.95rem; }
  .admin-comment-body { font-size: 0.9rem; white-space: pre-wrap; word-break: break-word; }

  .v-theme--dark .admin-row { background: #1e2a38 !important; }
</style>
