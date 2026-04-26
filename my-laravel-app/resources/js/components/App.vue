<template>
  <v-app id="simple-app">


<!-----Header----->

    <v-app-bar class="site-header" elevation="4">
      <v-toolbar-title class="header-logo" @click="currentPage = 'home'" style="cursor:pointer;">TaskForge</v-toolbar-title>
      <v-spacer></v-spacer>

      <div class="navigation">
        <v-menu>
          <template #activator="{ props }">
            <v-btn text v-bind="props" :class="{ active: ['pomodoro','taskboard','matrix','calendar'].includes(currentPage) }">
              Rīki <v-icon size="16" class="ml-1">mdi-chevron-down</v-icon>
            </v-btn>
          </template>
          <v-list density="compact">
            <v-list-item prepend-icon="mdi-timer" @click="currentPage = 'pomodoro'">
              <v-list-item-title>Pomodoro</v-list-item-title>
            </v-list-item>
            <v-list-item prepend-icon="mdi-format-list-checks" @click="currentPage = 'taskboard'">
              <v-list-item-title>Uzdevumu Dēlis</v-list-item-title>
            </v-list-item>
            <v-list-item prepend-icon="mdi-grid" @click="currentPage = 'matrix'">
              <v-list-item-title>Eizenhauera Matrica</v-list-item-title>
            </v-list-item>
            <v-list-item prepend-icon="mdi-calendar" @click="currentPage = 'calendar'">
              <v-list-item-title>Kalendārs</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
        <v-btn text @click="currentPage = 'forum'" :class="{ active: currentPage === 'forum' }">Forums</v-btn>
        <v-btn text @click="currentPage = 'about'" :class="{ active: currentPage === 'about' }">Par Mums</v-btn>
        <v-btn text @click="currentPage = 'contact'" :class="{ active: currentPage === 'contact' }">Kontakti</v-btn>
        <v-btn icon @click="toggleTheme" :title="darkMode ? 'Gaisma' : 'Tumsa'">
          <v-icon>{{ darkMode ? 'mdi-weather-sunny' : 'mdi-moon-waning-crescent' }}</v-icon>
        </v-btn>
        <template v-if="currentUser">
          <span class="nav-user">{{ currentUser.name }} {{ currentUser.surname }}</span>
          <v-btn text @click="logout">Iziet</v-btn>
        </template>
        <v-btn v-else text @click="showLoginWindow = true">Pieslēgties</v-btn>
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
        <v-row align="stretch">
          <v-col cols="12" sm="6" md="3">
            <v-card class="card" id="pomodoro-card" @click="currentPage = 'pomodoro'">
              <v-card-text class="card-in-card">
                <h3>Pomodoro Pulkstenis</h3>
                <p>Strādājiet un atpūtieites pēc noteikta laika grafika.</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card class="card" id="task-card" @click="currentPage = 'taskboard'">
              <v-card-text class="card-in-card">
                <h3>Mērķu Plānotājs</h3>
                <p>Organizējiet savus mērķus ilgtermiņā.</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card class="card" id="eisenhower-card" @click="currentPage = 'matrix'">
              <v-card-text class="card-in-card">
                <h3>Eizenhauera Matrica</h3>
                <p>Nosakidrojiet kādus mērķus sasniegt pirms citiem.</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card class="card" id="calendar-card" @click="currentPage = 'calendar'">
              <v-card-text class="card-in-card">
                <h3>Uzdevumu Kalendārs</h3>
                <p>Lieciet atgādinājumus notikumiem vai termiņiem.</p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>


<!-- Forum -->

    <v-main v-else-if="currentPage === 'forum'" class="main-content">
      <v-container fluid class="py-8 forum-container">

        <!-- ── View: Category list ── -->
        <template v-if="forumView === 'categories'">
          <div class="forum-header mb-6">
            <h2 class="forum-title">Forums</h2>
            <p class="forum-subtitle">Izvēlies kategoriju, lai sāktu vai lasītu diskusijas</p>
          </div>

          <div v-if="forumLoading" class="text-center py-10">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
          </div>

          <v-row v-else>
            <v-col v-for="cat in forumCategories" :key="cat.id" cols="12" sm="6">
              <v-card class="forum-cat-card" :style="{ borderLeftColor: cat.color }" @click="openForumCategory(cat)">
                <v-card-text class="d-flex align-center gap-3">
                  <v-icon :color="cat.color" size="36">{{ cat.icon }}</v-icon>
                  <div style="flex:1; min-width:0">
                    <div class="forum-cat-name">{{ cat.name }}</div>
                    <div class="forum-cat-desc">{{ cat.description }}</div>
                  </div>
                  <v-chip size="small" variant="tonal" style="white-space:nowrap">{{ cat.posts_count }}</v-chip>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </template>

        <!-- ── View: Posts in category ── -->
        <template v-else-if="forumView === 'posts'">
          <v-btn variant="text" prepend-icon="mdi-arrow-left" class="mb-3" @click="forumView = 'categories'">Visi forumi</v-btn>

          <div class="forum-cat-header mb-4">
            <div>
              <h2 class="forum-title">
                <v-icon :color="forumCategory.color" class="mr-2">{{ forumCategory.icon }}</v-icon>
                {{ forumCategory.name }}
              </h2>
              <p class="forum-subtitle">{{ forumCategory.description }}</p>
            </div>
            <v-btn v-if="currentUser" color="primary" variant="elevated" prepend-icon="mdi-pencil-outline" @click="showNewPostDialog = true">
              Jauns ieraksts
            </v-btn>
            <v-btn v-else variant="tonal" @click="showLoginWindow = true">Piesakies, lai rakstītu</v-btn>
          </div>

          <div v-if="forumLoading" class="text-center py-10">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
          </div>
          <div v-else-if="forumPosts.length === 0" class="forum-empty-state">
            Nav ierakstu šajā kategorijā. Esi pirmais!
          </div>

          <v-card v-for="post in forumPosts" :key="post.id"
                  class="forum-post-item mb-2"
                  @click="openForumPost(post)">
            <v-card-text>
              <div class="forum-post-title">{{ post.title }}</div>
              <div class="forum-post-meta">
                <v-icon size="13" class="mr-1">mdi-account-outline</v-icon>{{ post.user.name }}
                <span class="mx-2">·</span>
                <v-icon size="13" class="mr-1">mdi-clock-outline</v-icon>{{ timeAgo(post.created_at) }}
                <span class="mx-2">·</span>
                <v-icon size="13" class="mr-1">mdi-comment-outline</v-icon>{{ post.comments_count }}
              </div>
            </v-card-text>
          </v-card>
        </template>

        <!-- ── View: Post detail ── -->
        <template v-else-if="forumView === 'post'">
          <v-btn variant="text" prepend-icon="mdi-arrow-left" class="mb-3"
                 @click="forumView = 'posts'">Atpakaļ uz {{ forumCategory?.name }}</v-btn>

          <div v-if="forumLoading" class="text-center py-10">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
          </div>

          <template v-else-if="forumPostData">
            <!-- Post body -->
            <v-card class="mb-4">
              <v-card-text>
                <h2 class="forum-post-detail-title">{{ forumPostData.post.title }}</h2>
                <div class="forum-post-detail-meta mb-4">
                  <v-icon size="14" class="mr-1">mdi-account-outline</v-icon>{{ forumPostData.post.user.name }}
                  <span class="mx-2">·</span>
                  <v-icon size="14" class="mr-1">mdi-clock-outline</v-icon>{{ timeAgo(forumPostData.post.created_at) }}
                </div>
                <div class="forum-post-body">{{ forumPostData.post.body }}</div>
              </v-card-text>
            </v-card>

            <!-- Add comment -->
            <v-card class="mb-5">
              <v-card-text v-if="currentUser">
                <div class="forum-comment-form-label mb-2">Komentē kā <strong>{{ currentUser.name }}</strong></div>
                <v-textarea v-model="forumNewComment" variant="outlined" density="compact" rows="3"
                            placeholder="Raksti komentāru..." hide-details class="mb-3"></v-textarea>
                <div class="d-flex justify-end">
                  <v-btn color="primary" variant="elevated"
                         :disabled="!forumNewComment.trim()"
                         @click="submitForumComment">Publicēt</v-btn>
                </div>
              </v-card-text>
              <v-card-text v-else class="text-center py-4">
                <v-btn variant="tonal" @click="showLoginWindow = true">Piesakies, lai komentētu</v-btn>
              </v-card-text>
            </v-card>

            <!-- Comments -->
            <div class="forum-comments-header mb-3">
              Komentāri ({{ forumPostData.comments.length }})
            </div>

            <div v-if="forumPostData.comments.length === 0" class="forum-empty-state mb-4">
              Nav komentāru. Esi pirmais!
            </div>

            <v-card v-for="comment in forumPostData.comments" :key="comment.id" class="forum-comment-card mb-2">
              <v-card-text>
                <div class="forum-comment-header">
                  <v-icon size="16" class="mr-1">mdi-account-circle-outline</v-icon>
                  <span class="forum-comment-author">{{ comment.user.name }}</span>
                  <span class="forum-comment-time">{{ timeAgo(comment.created_at) }}</span>
                </div>
                <div class="forum-comment-body">{{ comment.body }}</div>
                <div class="forum-comment-votes mt-3">
                  <button class="forum-vote-btn forum-vote-up"
                          :class="{ active: comment.user_vote === 1 }"
                          @click="castVote(comment, 1)">
                    <v-icon size="15">mdi-arrow-up-bold</v-icon>
                    <span>{{ comment.upvotes }}</span>
                  </button>
                  <button class="forum-vote-btn forum-vote-down"
                          :class="{ active: comment.user_vote === -1 }"
                          @click="castVote(comment, -1)">
                    <v-icon size="15">mdi-arrow-down-bold</v-icon>
                    <span>{{ comment.downvotes }}</span>
                  </button>
                </div>
              </v-card-text>
            </v-card>
          </template>
        </template>

      </v-container>

      <!-- New post dialog -->
      <v-dialog v-model="showNewPostDialog" max-width="620">
        <v-card>
          <v-card-title class="bg-primary text-white">
            <div class="d-flex justify-space-between align-center">
              <span>Jauns ieraksts — {{ forumCategory?.name }}</span>
              <v-btn icon="mdi-close" variant="text" @click="showNewPostDialog = false"></v-btn>
            </div>
          </v-card-title>
          <v-card-text class="pt-4">
            <v-text-field v-model="forumNewPost.title" label="Virsraksts" variant="outlined" density="compact" class="mb-3"></v-text-field>
            <v-textarea v-model="forumNewPost.body" label="Saturs" variant="outlined" rows="7" hide-details></v-textarea>
          </v-card-text>
          <v-card-actions class="pa-4 pt-0">
            <v-spacer></v-spacer>
            <v-btn variant="text" @click="showNewPostDialog = false">Atcelt</v-btn>
            <v-btn color="primary" variant="elevated"
                   :disabled="!forumNewPost.title.trim() || !forumNewPost.body.trim()"
                   @click="submitForumPost">Publicēt</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
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
      <v-container fluid class="py-8 pomo-container">
        <div class="pomo-layout">

          <!-- LEFT: Timer card -->
          <v-card class="pomo-card site-section" :class="`pomo-phase-${currentPhase}`">
            <v-card-text class="pomo-card-body">

              <!-- Top row: mode switcher + gear -->
              <div class="pomo-top-row">
                <div class="pomo-mode-switcher">
                  <button class="pomo-mode-btn" :class="{ active: pomodoroMode === 'cycle' }" @click="setMode('cycle')">Ciklu</button>
                  <button class="pomo-mode-btn" :class="{ active: pomodoroMode === 'queue' }" @click="setMode('queue')">Sesija</button>
                </div>
                <button class="pomo-gear-btn" @click="showPomodoroSettings = true" title="Iestatījumi">
                  <v-icon size="20">mdi-cog-outline</v-icon>
                </button>
              </div>

              <!-- Cycle mode: phase tabs -->
              <div v-if="pomodoroMode === 'cycle'" class="pomo-tabs">
                <button class="pomo-tab" :class="{ active: currentPhase === 'work' }"      @click="switchPhase('work')">Pomodoro</button>
                <button class="pomo-tab" :class="{ active: currentPhase === 'break' }"     @click="switchPhase('break')">Īsā pauze</button>
                <button class="pomo-tab" :class="{ active: currentPhase === 'longBreak' }" @click="switchPhase('longBreak')">Garā pauze</button>
              </div>

              <!-- Queue mode: block indicator -->
              <div v-else class="pomo-queue-indicator">
                <template v-if="sessionQueue.length > 0 && currentQueueIndex < sessionQueue.length">
                  <v-icon size="14" class="mr-1">{{ currentPhase === 'work' ? 'mdi-timer' : 'mdi-coffee' }}</v-icon>
                  {{ sessionQueue[currentQueueIndex].label }}
                  <span class="pomo-queue-pos"> · {{ currentQueueIndex + 1 }}/{{ sessionQueue.length }}</span>
                </template>
                <span v-else-if="currentQueueIndex >= sessionQueue.length && sessionQueue.length > 0">✓ Sesija pabeigta!</span>
                <span v-else class="pomo-queue-hint">Pievieno blokus →</span>
              </div>

              <!-- Focused task label -->
              <div class="pomo-focus-label" v-if="focusedTask">
                <v-icon size="14" class="mr-1">mdi-target</v-icon>
                {{ focusedTask.title }}
              </div>

              <!-- Timer -->
              <div class="pomo-timer">{{ formatTime(pomodoroTime) }}</div>

              <!-- Controls -->
              <div class="pomo-controls">
                <v-btn v-if="!isPomodoroRunning" class="pomo-start-btn" variant="elevated" size="large"
                       @click="startPomodoro"
                       :disabled="pomodoroMode === 'queue' && (sessionQueue.length === 0 || currentQueueIndex >= sessionQueue.length)">
                  START
                </v-btn>
                <v-btn v-else class="pomo-start-btn" variant="elevated" size="large" @click="pausePomodoro">
                  PAUZE
                </v-btn>
                <v-btn v-if="isPomodoroRunning" icon variant="text" class="pomo-icon-btn" @click="skipPhase" title="Izlaist">
                  <v-icon>mdi-skip-next</v-icon>
                </v-btn>
                <v-btn icon variant="text" class="pomo-icon-btn" @click="resetPomodoro" title="Atiestatīt">
                  <v-icon>mdi-restart</v-icon>
                </v-btn>
              </div>

              <!-- Meta row -->
              <div class="pomo-meta-row">
                <span v-if="pomodoroMode === 'cycle'" class="pomo-session-label">#{{ sessionNumber }} · {{ cyclesCompleted }} cikli</span>
                <span v-else class="pomo-session-label"></span>
                <v-switch v-model="pomodoroAutoStart" label="Auto-start" density="compact" hide-details color="white" class="pomo-autostart"></v-switch>
              </div>

            </v-card-text>
          </v-card>

          <!-- RIGHT: tasks + queue panel -->
          <div class="pomo-right-panel">
            <v-tabs v-model="rightPanelTab" color="primary" density="compact" class="pomo-panel-tabs mb-2">
              <v-tab value="tasks">Uzdevumi</v-tab>
              <v-tab value="queue">Sesijas plāns</v-tab>
            </v-tabs>

            <!-- Tasks tab -->
            <v-card v-if="rightPanelTab === 'tasks'" class="pomo-tasks-card site-section">
              <v-card-text>
                <div class="pomo-tasks-header">
                  <span class="pomo-tasks-title">Uzdevumi</span>
                  <v-btn size="small" variant="tonal" prepend-icon="mdi-plus" @click="openTaskDialog()">Pievienot</v-btn>
                </div>
                <v-divider class="my-3"></v-divider>
                <div v-if="tasks.length === 0" class="pomo-empty">Nav uzdevumu. Pievieno pirmo!</div>
                <div v-for="task in tasks" :key="task.id"
                     class="pomo-task-row"
                     :class="{ 'pomo-task-focused': focusedTaskId === task.id, 'pomo-task-done': task.status === 'completed' }">
                  <v-btn icon size="x-small" variant="text" @click="focusedTaskId = (focusedTaskId === task.id ? null : task.id)">
                    <v-icon size="18">{{ focusedTaskId === task.id ? 'mdi-radiobox-marked' : 'mdi-radiobox-blank' }}</v-icon>
                  </v-btn>
                  <span class="pomo-task-name" @click="openTaskDialog(task)">{{ task.title }}</span>
                  <span class="pomo-task-count">
                    <v-icon size="13" class="mr-1" style="color:#e74c3c">mdi-timer</v-icon>
                    {{ task.done_pomodoros || 0 }} / {{ task.est_pomodoros || 1 }}
                  </span>
                  <v-btn icon size="x-small" variant="text" @click="toggleTaskDone(task)">
                    <v-icon size="18" :color="task.status === 'completed' ? 'success' : ''">
                      {{ task.status === 'completed' ? 'mdi-check-circle' : 'mdi-circle-outline' }}
                    </v-icon>
                  </v-btn>
                </div>
              </v-card-text>
            </v-card>

            <!-- Queue tab -->
            <v-card v-else class="pomo-tasks-card site-section">
              <v-card-text>
                <div class="pomo-tasks-header">
                  <span class="pomo-tasks-title">Sesijas plāns</span>
                  <v-btn size="small" variant="tonal" prepend-icon="mdi-plus" @click="showQueueDialog = true">Pievienot bloku</v-btn>
                </div>
                <v-divider class="my-3"></v-divider>

                <div v-if="sessionQueue.length === 0" class="pomo-empty">
                  Nav bloku. Pievieno darba vai pauzes blokus!
                </div>

                <div v-for="(block, idx) in sessionQueue" :key="block.id"
                     class="pomo-queue-row"
                     :class="{ 'pomo-queue-active': pomodoroMode === 'queue' && idx === currentQueueIndex,
                               'pomo-queue-done':   pomodoroMode === 'queue' && idx < currentQueueIndex }">
                  <v-icon size="16" :style="{ color: block.type === 'work' ? '#c0392b' : '#27ae60' }" class="mr-1">
                    {{ block.type === 'work' ? 'mdi-timer' : 'mdi-coffee' }}
                  </v-icon>
                  <span class="pomo-queue-label">{{ block.label }}</span>
                  <span class="pomo-queue-dur">{{ block.minutes }}min</span>
                  <v-btn icon size="x-small" variant="text" :disabled="idx === 0" @click="moveQueueBlock(idx, -1)">
                    <v-icon size="14">mdi-arrow-up</v-icon>
                  </v-btn>
                  <v-btn icon size="x-small" variant="text" :disabled="idx === sessionQueue.length - 1" @click="moveQueueBlock(idx, 1)">
                    <v-icon size="14">mdi-arrow-down</v-icon>
                  </v-btn>
                  <v-btn icon size="x-small" variant="text" color="error" @click="removeQueueBlock(idx)">
                    <v-icon size="14">mdi-close</v-icon>
                  </v-btn>
                </div>

                <div v-if="sessionQueue.length > 0" class="pomo-queue-total mt-3">
                  Kopā: {{ sessionQueueTotal }} min
                </div>
              </v-card-text>
            </v-card>
          </div>
        </div>

        <div class="tool-bottom-nav mt-4">
          <v-btn variant="tonal" prepend-icon="mdi-arrow-left" @click="currentPage = 'home'">Atpakaļ</v-btn>
          <div class="tool-links">
            <v-btn variant="text" size="small" prepend-icon="mdi-format-list-checks" @click="currentPage = 'taskboard'">Uzdevumu Dēlis</v-btn>
            <v-btn variant="text" size="small" prepend-icon="mdi-grid" @click="currentPage = 'matrix'">Matrica</v-btn>
            <v-btn variant="text" size="small" prepend-icon="mdi-calendar" @click="currentPage = 'calendar'">Kalendārs</v-btn>
          </div>
        </div>
      </v-container>

      <!-- Settings dialog -->
      <v-dialog v-model="showPomodoroSettings" max-width="360">
        <v-card>
          <v-card-title class="bg-primary text-white">
            <div class="d-flex justify-space-between align-center">
              <span>Laika iestatījumi</span>
              <v-btn icon="mdi-close" variant="text" @click="showPomodoroSettings = false"></v-btn>
            </div>
          </v-card-title>
          <v-card-text class="pt-5">
            <v-text-field v-model.number="settingsForm.pomo"  label="Pomodoro (min)" type="number" min="1" max="60" variant="outlined" density="compact" class="mb-3"></v-text-field>
            <v-text-field v-model.number="settingsForm.short" label="Īsā pauze (min)" type="number" min="1" max="30" variant="outlined" density="compact" class="mb-3"></v-text-field>
            <v-text-field v-model.number="settingsForm.long"  label="Garā pauze (min)" type="number" min="1" max="60" variant="outlined" density="compact"></v-text-field>
          </v-card-text>
          <v-card-actions class="pa-4 pt-0">
            <v-spacer></v-spacer>
            <v-btn variant="text" @click="showPomodoroSettings = false">Atcelt</v-btn>
            <v-btn color="primary" variant="elevated" @click="applyPomodoroSettings">Saglabāt</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Queue block dialog -->
      <v-dialog v-model="showQueueDialog" max-width="360">
        <v-card>
          <v-card-title class="bg-primary text-white">
            <div class="d-flex justify-space-between align-center">
              <span>Pievienot bloku</span>
              <v-btn icon="mdi-close" variant="text" @click="showQueueDialog = false"></v-btn>
            </div>
          </v-card-title>
          <v-card-text class="pt-5">
            <div class="mb-3 text-body-2 text-medium-emphasis">Bloka tips</div>
            <v-btn-toggle v-model="queueForm.type" mandatory class="mb-4" color="primary" variant="outlined" density="compact" style="width:100%">
              <v-btn value="work" style="flex:1">🍅 Darbs</v-btn>
              <v-btn value="break" style="flex:1">☕ Pauze</v-btn>
              <v-btn value="longBreak" style="flex:1">🛋 Garā pauze</v-btn>
            </v-btn-toggle>
            <v-text-field v-model.number="queueForm.minutes" label="Ilgums (min)" type="number" min="1" max="120" variant="outlined" density="compact" class="mb-3"></v-text-field>
            <v-select v-if="queueForm.type === 'work'"
                      v-model="queueForm.taskId"
                      :items="tasks.map(t => ({ title: t.title, value: t.id }))"
                      label="Saistīt ar uzdevumu (neobligāti)"
                      clearable variant="outlined" density="compact"></v-select>
          </v-card-text>
          <v-card-actions class="pa-4 pt-0">
            <v-spacer></v-spacer>
            <v-btn variant="text" @click="showQueueDialog = false">Atcelt</v-btn>
            <v-btn color="primary" variant="elevated" @click="addQueueBlock">Pievienot</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-main>


<!-- Task board -->

    <v-main v-else-if="currentPage === 'taskboard'" class="main-content">
      <v-container fluid class="py-8">
        <v-card class="taskboard-section site-section">
          <v-card-text>

            <!-- Board toolbar -->
            <div class="board-toolbar mb-4">
              <div class="d-flex align-center gap-2">
                <h2 class="board-title">Uzdevumu Dēlis</h2>
                <v-chip size="small" variant="tonal" color="primary">
                  {{ tasks.length }} uzdevumi
                </v-chip>
              </div>
              <div class="d-flex align-center gap-2 flex-wrap">
                <v-text-field
                  v-model="boardFilter"
                  placeholder="Meklēt..."
                  prepend-inner-icon="mdi-magnify"
                  variant="outlined"
                  density="compact"
                  clearable
                  hide-details
                  style="max-width:220px"
                ></v-text-field>
                <v-btn color="primary" variant="elevated" prepend-icon="mdi-plus" @click="openTaskDialog()">
                  Jauns uzdevums
                </v-btn>
              </div>
            </div>

            <!-- Board -->
            <div class="trello-board">
              <div v-for="col in columns" :key="col.id"
                   class="trello-column"
                   :class="{ 'drag-over': dragOverColumn === col.id, 'col-collapsed': collapsedColumns.includes(col.id) }"
                   @dragover.prevent="dragOverColumn = col.id"
                   @dragleave.self="dragOverColumn = null"
                   @drop="onDropToColumn(col.id); dragOverColumn = null">

                <!-- Column header -->
                <div class="trello-col-header" :style="{ borderBottomColor: col.color || '#764ba2' }">
                  <div class="trello-col-meta">
                    <div class="d-flex align-center gap-2">
                      <span class="trello-col-name">{{ col.name }}</span>
                      <span class="trello-col-count" :style="{ background: col.color || '#764ba2' }">
                        {{ tasksByColumn(col.id).length }}
                      </span>
                    </div>
                    <span v-if="col.description && !collapsedColumns.includes(col.id)" class="trello-col-desc">
                      {{ col.description }}
                    </span>
                    <span v-if="!collapsedColumns.includes(col.id)" class="trello-col-time">
                      {{ formatColDate(col.createdAt) }}
                    </span>
                  </div>
                  <div class="d-flex" style="flex-shrink:0">
                    <v-btn icon size="x-small" variant="text" @click="toggleCollapse(col.id)"
                           :title="collapsedColumns.includes(col.id) ? 'Izvērst' : 'Sakļaut'">
                      <v-icon size="14">{{ collapsedColumns.includes(col.id) ? 'mdi-chevron-down' : 'mdi-chevron-up' }}</v-icon>
                    </v-btn>
                    <v-btn v-if="col.id !== 'default'" icon size="x-small" variant="text" color="error"
                           title="Dzēst kolonnu" @click="deleteColumn(col.id)">
                      <v-icon size="14">mdi-close</v-icon>
                    </v-btn>
                  </div>
                </div>

                <!-- Column body (hidden when collapsed) -->
                <template v-if="!collapsedColumns.includes(col.id)">
                  <div class="trello-col-body">
                    <!-- Empty state -->
                    <div v-if="tasksByColumn(col.id).length === 0" class="trello-empty">
                      <v-icon size="32" color="grey-lighten-1">mdi-inbox-outline</v-icon>
                      <p>Nav uzdevumu</p>
                      <p class="trello-empty-hint">Velc šurp vai pievieno zemāk</p>
                    </div>

                    <!-- Task cards -->
                    <v-card v-for="task in tasksByColumn(col.id)" :key="task.id"
                            class="trello-task-card mb-2" elevation="1"
                            draggable="true" @dragstart="onDragStart($event, task.id)"
                            :style="{ borderLeftColor: priorityColor(task.priority) }">
                      <v-card-text class="py-2 px-3">
                        <!-- Priority + actions row -->
                        <div class="d-flex justify-space-between align-center mb-1">
                          <v-chip v-if="task.priority && task.priority !== 'none'"
                                  size="x-small" variant="tonal"
                                  :color="{ high: 'error', medium: 'warning', low: 'success' }[task.priority]">
                            {{ { high: '↑ Augsta', medium: '→ Vidēja', low: '↓ Zema' }[task.priority] }}
                          </v-chip>
                          <span v-else></span>
                          <div class="task-actions">
                            <v-btn icon size="x-small" variant="text" title="Rediģēt"
                                   @click.stop="openTaskDialog(task)">
                              <v-icon size="13">mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon size="x-small" variant="text" color="error" title="Dzēst"
                                   @click.stop="deleteTask(task.id)">
                              <v-icon size="13">mdi-delete</v-icon>
                            </v-btn>
                          </div>
                        </div>

                        <span class="trello-task-title">{{ task.title }}</span>
                        <p v-if="task.description" class="trello-task-desc">{{ task.description }}</p>

                        <!-- Footer chips -->
                        <div class="d-flex gap-1 flex-wrap mt-1">
                          <v-chip v-if="task.due_date" size="x-small" variant="tonal"
                                  :color="isOverdue(task.due_date) ? 'error' : 'primary'"
                                  prepend-icon="mdi-calendar">
                            {{ task.due_date }}
                          </v-chip>
                          <v-chip v-if="task.status === 'completed'" size="x-small"
                                  variant="tonal" color="success" prepend-icon="mdi-check">
                            Pabeigts
                          </v-chip>
                        </div>
                      </v-card-text>
                    </v-card>
                  </div>

                  <!-- Quick-add row -->
                  <div class="col-quick-add">
                    <template v-if="quickAddCol === col.id">
                      <v-text-field
                        v-model="quickAddTitle"
                        placeholder="Uzdevuma nosaukums..."
                        variant="outlined"
                        density="compact"
                        autofocus
                        hide-details
                        @keyup.enter="quickAddTask(col.id)"
                        @keyup.esc="quickAddCol = null"
                        @blur="quickAddCol = null">
                        <template #append-inner>
                          <v-btn icon size="x-small" variant="text" color="primary"
                                 @mousedown.prevent="quickAddTask(col.id)">
                            <v-icon size="16">mdi-check</v-icon>
                          </v-btn>
                        </template>
                      </v-text-field>
                    </template>
                    <v-btn v-else variant="text" size="small" prepend-icon="mdi-plus"
                           block class="quick-add-btn"
                           @click="quickAddCol = col.id; quickAddTitle = ''">
                      Pievienot uzdevumu
                    </v-btn>
                  </div>
                </template>
              </div>

              <!-- Add column button -->
              <div class="trello-add-col" @click="showColumnDialog = true">
                <v-icon size="28">mdi-plus</v-icon>
                <span>Pievienot kolonnu</span>
              </div>
            </div>

          </v-card-text>
        </v-card>
        <div class="tool-bottom-nav mt-4">
          <v-btn variant="tonal" prepend-icon="mdi-arrow-left" @click="currentPage = 'home'">Atpakaļ</v-btn>
          <div class="tool-links">
            <v-btn variant="text" size="small" prepend-icon="mdi-timer" @click="currentPage = 'pomodoro'">Pomodoro</v-btn>
            <v-btn variant="text" size="small" prepend-icon="mdi-grid" @click="currentPage = 'matrix'">Matrica</v-btn>
            <v-btn variant="text" size="small" prepend-icon="mdi-calendar" @click="currentPage = 'calendar'">Kalendārs</v-btn>
          </div>
        </div>
      </v-container>
    </v-main>


<!-- Eisenhower matrix -->

    <v-main v-else-if="currentPage === 'matrix'" class="main-content">
      <v-container fluid class="py-8">
        <v-card class="matrix-section site-section">
          <v-card-text>
            <div class="d-flex justify-space-between align-center mb-4">
              <h2>Eizenhauera Matrica</h2>
              <div class="d-flex ga-2">
                <v-btn color="primary" variant="elevated" @click="openTaskDialog()">+ Jauns uzdevums</v-btn>
                <v-btn color="error" variant="outlined" @click="clearMatrix()">Notīrīt matricu</v-btn>
              </div>
            </div>

            <div class="matrix-axis-row">
              <div class="matrix-axis-label-y">↑ Steidzamība</div>
              <div class="matrix-grid">
                <div class="matrix-quadrant q-do"
                     @dragover.prevent @drop="onDropToQuadrant('do')">
                  <div class="q-label">DARI TAGAD<br><small>Steidzami + Svarīgi</small></div>
                  <div class="q-tasks">
                    <div v-for="task in tasksByQuadrant('do')" :key="task.id"
                         class="q-chip" draggable="true" @dragstart="onDragStart($event, task.id)"
                         @click="openTaskDialog(task)">
                      {{ task.title }}<span class="chip-del" @click.stop="deleteTask(task.id)">×</span>
                    </div>
                  </div>
                </div>
                <div class="matrix-quadrant q-schedule"
                     @dragover.prevent @drop="onDropToQuadrant('schedule')">
                  <div class="q-label">IEPLĀNO<br><small>Ne-steidzami + Svarīgi</small></div>
                  <div class="q-tasks">
                    <div v-for="task in tasksByQuadrant('schedule')" :key="task.id"
                         class="q-chip" draggable="true" @dragstart="onDragStart($event, task.id)"
                         @click="openTaskDialog(task)">
                      {{ task.title }}<span class="chip-del" @click.stop="deleteTask(task.id)">×</span>
                    </div>
                  </div>
                </div>
                <div class="matrix-quadrant q-delegate"
                     @dragover.prevent @drop="onDropToQuadrant('delegate')">
                  <div class="q-label">DELEĢĒ<br><small>Steidzami + Nesvarīgi</small></div>
                  <div class="q-tasks">
                    <div v-for="task in tasksByQuadrant('delegate')" :key="task.id"
                         class="q-chip" draggable="true" @dragstart="onDragStart($event, task.id)"
                         @click="openTaskDialog(task)">
                      {{ task.title }}<span class="chip-del" @click.stop="deleteTask(task.id)">×</span>
                    </div>
                  </div>
                </div>
                <div class="matrix-quadrant q-eliminate"
                     @dragover.prevent @drop="onDropToQuadrant('eliminate')">
                  <div class="q-label">ATMET<br><small>Ne-steidzami + Nesvarīgi</small></div>
                  <div class="q-tasks">
                    <div v-for="task in tasksByQuadrant('eliminate')" :key="task.id"
                         class="q-chip" draggable="true" @dragstart="onDragStart($event, task.id)"
                         @click="openTaskDialog(task)">
                      {{ task.title }}<span class="chip-del" @click.stop="deleteTask(task.id)">×</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="matrix-axis-label-x">→ Svarīgums</div>

            <h3 class="mt-6 mb-3">Nepievienotie uzdevumi</h3>
            <div class="task-pool" @dragover.prevent @drop="onDropToQuadrant(null)">
              <div v-if="unassignedTasks.length === 0" class="pool-empty">Visi uzdevumi ir pievienoti matricai</div>
              <div v-for="task in unassignedTasks" :key="task.id"
                   class="pool-chip" draggable="true" @dragstart="onDragStart($event, task.id)"
                   @click="openTaskDialog(task)">
                {{ task.title }}<span class="chip-del" @click.stop="deleteTask(task.id)">×</span>
              </div>
            </div>
          </v-card-text>
        </v-card>
        <div class="tool-bottom-nav mt-4">
          <v-btn variant="tonal" prepend-icon="mdi-arrow-left" @click="currentPage = 'home'">Atpakaļ</v-btn>
          <div class="tool-links">
            <v-btn variant="text" size="small" prepend-icon="mdi-timer" @click="currentPage = 'pomodoro'">Pomodoro</v-btn>
            <v-btn variant="text" size="small" prepend-icon="mdi-format-list-checks" @click="currentPage = 'taskboard'">Uzdevumu Dēlis</v-btn>
            <v-btn variant="text" size="small" prepend-icon="mdi-calendar" @click="currentPage = 'calendar'">Kalendārs</v-btn>
          </div>
        </div>
      </v-container>
    </v-main>

<!-- Calendar -->

    <v-main v-else-if="currentPage === 'calendar'" class="main-content">
      <v-container fluid class="py-8">
        <v-card class="calendar-section site-section">
          <v-card-text>
            <div class="d-flex justify-space-between align-center mb-4">
              <h2>Kalendārs</h2>
              <v-btn color="primary" variant="elevated" @click="openTaskDialog()">+ Jauns uzdevums</v-btn>
            </div>

            <div class="cal-nav">
              <v-btn icon variant="text" @click="changeMonth(-1)"><v-icon>mdi-chevron-left</v-icon></v-btn>
              <span class="cal-month-label">{{ calendarMonthLabel }}</span>
              <v-btn icon variant="text" @click="changeMonth(1)"><v-icon>mdi-chevron-right</v-icon></v-btn>
            </div>

            <div class="cal-grid">
              <div class="cal-day-header" v-for="d in ['P','O','T','C','P','S','Sv']" :key="d">{{ d }}</div>
              <div v-for="(day, i) in calendarDays" :key="i"
                   class="cal-day-cell"
                   :class="{ 'cal-other': !day.currentMonth, 'cal-today': day.isToday }"
                   @dragover.prevent
                   @drop="day.currentMonth && onDropToCalendarDay(day.dateStr)"
                   @click="day.currentMonth && openTaskDialog(null, day.dateStr)">
                <span class="cal-day-num">{{ day.day }}</span>
                <div v-for="task in tasksForDay(day.dateStr)" :key="task.id"
                     class="cal-task-block"
                     draggable="true"
                     @dragstart.stop="onDragStart($event, task.id)"
                     @click.stop="openTaskDialog(task)">
                  {{ task.title }}
                </div>
              </div>
            </div>

            <h3 class="mt-6 mb-3">Neieplānotie uzdevumi</h3>
            <div class="task-pool" @dragover.prevent @drop="onDropToCalendarDay(null)">
              <div v-if="tasksWithoutDate.length === 0" class="pool-empty">Nav neieplānotu uzdevumu</div>
              <div v-for="task in tasksWithoutDate" :key="task.id"
                   class="pool-chip" draggable="true" @dragstart="onDragStart($event, task.id)">
                {{ task.title }}
              </div>
            </div>
          </v-card-text>
        </v-card>
        <div class="tool-bottom-nav mt-4">
          <v-btn variant="tonal" prepend-icon="mdi-arrow-left" @click="currentPage = 'home'">Atpakaļ</v-btn>
          <div class="tool-links">
            <v-btn variant="text" size="small" prepend-icon="mdi-timer" @click="currentPage = 'pomodoro'">Pomodoro</v-btn>
            <v-btn variant="text" size="small" prepend-icon="mdi-format-list-checks" @click="currentPage = 'taskboard'">Uzdevumu Dēlis</v-btn>
            <v-btn variant="text" size="small" prepend-icon="mdi-grid" @click="currentPage = 'matrix'">Matrica</v-btn>
          </div>
        </div>
      </v-container>
    </v-main>


<!-- Footer -->

    <v-footer class="d-flex justify-center" id="footer-section">
      <span>&copy; 2026 TaskForge - D. Šabovičs. All rights reserved.</span>
    </v-footer>

<!-- Task Dialog -->

    <v-dialog v-model="showTaskDialog" max-width="500px">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ editingTask ? 'Rediģēt uzdevumu' : 'Jauns uzdevums' }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showTaskDialog = false"></v-btn>
          </div>
        </v-card-title>
        <v-card-text class="pt-4">
          <v-text-field v-model="taskForm.title" label="Nosaukums" required class="mb-3"></v-text-field>
          <v-textarea v-model="taskForm.description" label="Apraksts" rows="2" class="mb-3"></v-textarea>
          <v-select v-model="taskForm.status" :items="statusOptions" item-title="title" item-value="value" label="Statuss" class="mb-3"></v-select>
          <v-select v-model="taskForm.priority" :items="priorityOptions" item-title="title" item-value="value" label="Prioritāte" class="mb-3"></v-select>
          <v-text-field v-model="taskForm.due_date" label="Izpildes datums" type="date" class="mb-3"></v-text-field>
          <v-text-field v-model.number="taskForm.est_pomodoros" label="Paredzamie pomodori" type="number" min="1" max="99" class="mb-3"></v-text-field>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-btn v-if="editingTask" color="error" variant="text" @click="deleteTask(editingTask.id); showTaskDialog = false">Dzēst</v-btn>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showTaskDialog = false">Atcelt</v-btn>
          <v-btn color="primary" variant="elevated" @click="saveTask()">Saglabāt</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

<!-- Column Dialog -->

    <v-dialog v-model="showColumnDialog" max-width="420px">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>Jauna kolonna</span>
            <v-btn icon="mdi-close" variant="text" @click="showColumnDialog = false"></v-btn>
          </div>
        </v-card-title>
        <v-card-text class="pt-4">
          <v-text-field v-model="columnForm.name" label="Nosaukums" required class="mb-3"></v-text-field>
          <v-textarea v-model="columnForm.description" label="Apraksts" rows="2" class="mb-3"></v-textarea>
          <v-text-field :model-value="new Date().toLocaleDateString('lv-LV')" label="Izveidošanas datums" readonly disabled class="mb-3"></v-text-field>
          <div class="mb-1 text-body-2 text-medium-emphasis">Kolonnas krāsa</div>
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
          <v-btn variant="text" @click="showColumnDialog = false">Atcelt</v-btn>
          <v-btn color="primary" variant="elevated" @click="saveColumn()">Izveidot</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

<!-- Login Sequence -->

    <v-dialog v-model="showLoginWindow" persistent max-width="400px">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>Pieslēgties</span>
            <v-btn icon="mdi-close" variant="text" @click="showLoginWindow = false; authError = ''"></v-btn>
          </div>
        </v-card-title>

        <v-card-text class="pt-6">
          <v-alert v-if="authError" type="error" variant="tonal" class="mb-4">{{ authError }}</v-alert>
          <v-form @submit.prevent="handleLogin">
            <v-text-field v-model="loginForm.email" label="Epasts" type="email" required class="mb-4"></v-text-field>
            <v-text-field v-model="loginForm.password" label="Parole" type="password" required class="mb-4"></v-text-field>
            <v-btn type="submit" color="primary" variant="elevated" block class="mb-4" :loading="authLoading">Pieslēgties</v-btn>
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
            <v-btn icon="mdi-close" variant="text" @click="showRegisterWindow = false; authError = ''"></v-btn>
          </div>
        </v-card-title>

        <v-card-text class="pt-6">
          <v-alert v-if="authError" type="error" variant="tonal" class="mb-4">{{ authError }}</v-alert>
          <v-form @submit.prevent="handleRegister">
            <v-text-field v-model="registerForm.username" label="Lietotājvārds" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.name" label="Vārds" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.surname" label="Uzvārds" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.email" label="Epasts" type="email" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.password" label="Parole" type="password" required class="mb-4"></v-text-field>
            <v-text-field v-model="registerForm.password_confirmation" label="Apstiprināt paroli" type="password" required class="mb-4"></v-text-field>
            <v-btn type="submit" color="primary" variant="elevated" block class="mb-4" :loading="authLoading">Reģistrēties</v-btn>
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
        currentUser: null,
        authError: '',
        authLoading: false,
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
          username: '',
          name: '',
          surname: '',
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
        sessionNumber: 1,
        focusedTaskId: null,
        pomodoroAutoStart: false,
        showPomodoroSettings: false,
        settingsForm: { pomo: 25, short: 5, long: 15 },
        pomodoroMode: 'cycle',
        rightPanelTab: 'tasks',
        sessionQueue: [],
        currentQueueIndex: 0,
        showQueueDialog: false,
        queueForm: { type: 'work', minutes: 25, taskId: null },
        tasks: JSON.parse(localStorage.getItem('tf_tasks') || '[]'),
        showTaskDialog: false,
        editingTask: null,
        taskForm: { title: '', description: '', status: 'pending', due_date: '', priority: 'medium', est_pomodoros: 1 },
        columns: JSON.parse(localStorage.getItem('tf_columns') || 'null') || [
          { id: 'default', name: 'Iesūtne', description: 'Noklusējuma kolonna', createdAt: new Date().toISOString() }
        ],
        showColumnDialog: false,
        columnForm: { name: '', description: '', color: '#764ba2' },
        collapsedColumns: [],
        dragOverColumn: null,
        boardFilter: '',
        quickAddCol: null,
        quickAddTitle: '',
        columnColors: ['#764ba2','#667eea','#ef5350','#66bb6a','#ffa726','#26c6da','#ec407a','#78909c'],
        statusOptions: [
          { title: 'Gaida',    value: 'pending' },
          { title: 'Procesā',  value: 'in_progress' },
          { title: 'Pabeigts', value: 'completed' }
        ],
        priorityOptions: [
          { title: '↑ Augsta', value: 'high' },
          { title: '→ Vidēja', value: 'medium' },
          { title: '↓ Zema',   value: 'low' },
          { title: '— Nav',    value: 'none' }
        ],
        draggedTaskId: null,
        calendarYear: new Date().getFullYear(),
        calendarMonth: new Date().getMonth(),

        // Forum
        forumView: 'categories',
        forumCategory: null,
        forumCategories: [],
        forumPosts: [],
        forumPostData: null,
        forumNewPost: { title: '', body: '' },
        forumNewComment: '',
        forumLoading: false,
        showNewPostDialog: false,
      }
    },

    computed: {
      focusedTask() {
        if (!this.focusedTaskId) return null
        return this.tasks.find(t => t.id === this.focusedTaskId) || null
      },
      currentQueueBlock() {
        return this.sessionQueue[this.currentQueueIndex] || null
      },
      sessionQueueTotal() {
        return this.sessionQueue.reduce((sum, b) => sum + (b.minutes || 0), 0)
      },
      unassignedTasks() {
        return this.tasks.filter(t => !t.quadrant)
      },
      tasksWithoutDate() {
        return this.tasks.filter(t => !t.due_date)
      },
      calendarMonthLabel() {
        return new Date(this.calendarYear, this.calendarMonth, 1)
          .toLocaleDateString('lv-LV', { month: 'long', year: 'numeric' })
      },
      calendarDays() {
        const year = this.calendarYear
        const month = this.calendarMonth
        const firstDay = new Date(year, month, 1)
        const daysInMonth = new Date(year, month + 1, 0).getDate()
        const today = new Date()
        const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`
        const startDow = (firstDay.getDay() + 6) % 7
        const days = []

        for (let i = startDow - 1; i >= 0; i--) {
          const d = new Date(year, month, -i)
          days.push({ day: d.getDate(), dateStr: null, currentMonth: false, isToday: false })
        }
        for (let d = 1; d <= daysInMonth; d++) {
          const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`
          days.push({ day: d, dateStr, currentMonth: true, isToday: dateStr === todayStr })
        }
        let next = 1
        while (days.length < 42) {
          days.push({ day: next++, dateStr: null, currentMonth: false, isToday: false })
        }
        return days
      }
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
      currentPage(val) {
        if (val === 'calendar') {
          this.calendarYear = new Date().getFullYear()
          this.calendarMonth = new Date().getMonth()
        }
        if (val === 'forum') {
          this.forumView = 'categories'
          this.loadForumCategories()
        }
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

        if (this.pomodoroMode === 'queue') {
          if (!this.currentQueueBlock) return
          this.pomodoroTime  = this.currentQueueBlock.minutes * 60
          this.currentPhase  = this.currentQueueBlock.type
          if (this.currentQueueBlock.taskId) this.focusedTaskId = this.currentQueueBlock.taskId
        }

        this.isPomodoroRunning = true
        this.pomodoroTimerId = setInterval(() => {
          if (this.pomodoroTime > 0) { this.pomodoroTime -= 1; return }

          clearInterval(this.pomodoroTimerId)
          this.pomodoroTimerId = null
          this.isPomodoroRunning = false

          if (this.pomodoroMode === 'queue') {
            if (this.currentPhase === 'work') this.creditFocusedTask()
            this.currentQueueIndex++
            if (this.currentQueueBlock) {
              this.pomodoroTime = this.currentQueueBlock.minutes * 60
              this.currentPhase = this.currentQueueBlock.type
              if (this.currentQueueBlock.taskId) this.focusedTaskId = this.currentQueueBlock.taskId
              this.notify(this.currentPhase === 'work' ? 'Darba bloks sākas!' : 'Pauze sākas!')
              if (this.pomodoroAutoStart) this.startPomodoro()
            } else {
              this.notify('Sesija pabeigta!')
            }
          } else {
            if (this.currentPhase === 'work') {
              this.cyclesCompleted += 1
              this.sessionNumber   += 1
              this.creditFocusedTask()
              const next = this.cyclesCompleted % 4 === 0 ? 'longBreak' : 'break'
              this.currentPhase  = next
              this.pomodoroTime  = next === 'longBreak' ? this.longBreakDuration : this.shortBreakDuration
              this.notify(next === 'longBreak' ? 'Garā pauze sākas!' : 'Īsā pauze sākas!')
            } else {
              this.currentPhase = 'work'
              this.pomodoroTime = this.pomodoroDuration
              this.notify('Darba sesija sākas!')
            }
            if (this.pomodoroAutoStart) this.startPomodoro()
          }
        }, 1000)
      },

      pausePomodoro() {
        if (this.pomodoroTimerId) { clearInterval(this.pomodoroTimerId); this.pomodoroTimerId = null }
        this.isPomodoroRunning = false
      },

      resetPomodoro() {
        if (this.pomodoroTimerId) { clearInterval(this.pomodoroTimerId); this.pomodoroTimerId = null }
        this.isPomodoroRunning = false
        if (this.pomodoroMode === 'queue') {
          this.currentQueueIndex = 0
          if (this.currentQueueBlock) {
            this.currentPhase = this.currentQueueBlock.type
            this.pomodoroTime = this.currentQueueBlock.minutes * 60
          }
        } else {
          this.currentPhase    = 'work'
          this.cyclesCompleted = 0
          this.sessionNumber   = 1
          this.pomodoroTime    = this.pomodoroDuration
        }
      },

      switchPhase(phase) {
        this.pausePomodoro()
        this.currentPhase = phase
        if (phase === 'work')       this.pomodoroTime = this.pomodoroDuration
        else if (phase === 'break') this.pomodoroTime = this.shortBreakDuration
        else                        this.pomodoroTime = this.longBreakDuration
      },

      skipPhase() {
        this.pausePomodoro()
        if (this.pomodoroMode === 'queue') {
          this.currentQueueIndex++
          if (this.currentQueueBlock) {
            this.currentPhase = this.currentQueueBlock.type
            this.pomodoroTime = this.currentQueueBlock.minutes * 60
            if (this.currentQueueBlock.taskId) this.focusedTaskId = this.currentQueueBlock.taskId
          }
        } else {
          if (this.currentPhase === 'work') {
            this.cyclesCompleted += 1
            this.switchPhase(this.cyclesCompleted % 4 === 0 ? 'longBreak' : 'break')
          } else {
            this.switchPhase('work')
          }
        }
      },

      setMode(mode) {
        this.pausePomodoro()
        this.pomodoroMode = mode
        if (mode === 'queue') {
          this.currentQueueIndex = 0
          if (this.currentQueueBlock) {
            this.currentPhase = this.currentQueueBlock.type
            this.pomodoroTime = this.currentQueueBlock.minutes * 60
          }
        } else {
          this.currentPhase = 'work'
          this.pomodoroTime = this.pomodoroDuration
        }
      },

      creditFocusedTask() {
        if (this.focusedTaskId === null) return
        const idx = this.tasks.findIndex(t => t.id === this.focusedTaskId)
        if (idx !== -1) {
          const updated = { ...this.tasks[idx], done_pomodoros: (this.tasks[idx].done_pomodoros || 0) + 1 }
          this.tasks.splice(idx, 1, updated)
          if (this.currentUser) this.apiFetch(`/api/tasks/${this.focusedTaskId}`, { method: 'PUT', body: JSON.stringify({ done_pomodoros: updated.done_pomodoros }) })
        }
      },

      addQueueBlock() {
        if (!this.queueForm.minutes || this.queueForm.minutes < 1) return
        const labels = { work: 'Darbs', break: 'Īsā pauze', longBreak: 'Garā pauze' }
        const linkedTask = this.queueForm.taskId ? this.tasks.find(t => t.id === this.queueForm.taskId) : null
        this.sessionQueue.push({
          id:      Date.now(),
          type:    this.queueForm.type,
          minutes: this.queueForm.minutes,
          taskId:  this.queueForm.taskId || null,
          label:   linkedTask ? linkedTask.title : labels[this.queueForm.type],
        })
        if (this.pomodoroMode === 'queue' && this.sessionQueue.length === 1) {
          this.currentQueueIndex = 0
          this.currentPhase  = this.queueForm.type
          this.pomodoroTime  = this.queueForm.minutes * 60
        }
        this.queueForm    = { type: 'work', minutes: 25, taskId: null }
        this.showQueueDialog = false
      },

      removeQueueBlock(idx) {
        this.sessionQueue.splice(idx, 1)
        if (this.currentQueueIndex >= this.sessionQueue.length) {
          this.currentQueueIndex = Math.max(0, this.sessionQueue.length - 1)
        }
      },

      moveQueueBlock(idx, dir) {
        const to = idx + dir
        if (to < 0 || to >= this.sessionQueue.length) return
        const item = this.sessionQueue.splice(idx, 1)[0]
        this.sessionQueue.splice(to, 0, item)
      },

      applyPomodoroSettings() {
        this.pomodoroDuration    = this.settingsForm.pomo  * 60
        this.shortBreakDuration  = this.settingsForm.short * 60
        this.longBreakDuration   = this.settingsForm.long  * 60
        if (!this.isPomodoroRunning) this.switchPhase(this.currentPhase)
        this.showPomodoroSettings = false
      },

      async toggleTaskDone(task) {
        const newStatus = task.status === 'completed' ? 'pending' : 'completed'
        const idx = this.tasks.findIndex(t => t.id === task.id)
        if (idx !== -1) {
          this.tasks.splice(idx, 1, { ...this.tasks[idx], status: newStatus })
          if (this.currentUser) this.apiFetch(`/api/tasks/${task.id}`, { method: 'PUT', body: JSON.stringify({ status: newStatus }) })
        }
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

      tasksByColumn(columnId) {
        return this.tasks.filter(t => {
          if ((t.columnId || 'default') !== columnId) return false
          if (this.boardFilter) {
            const q = this.boardFilter.toLowerCase()
            return t.title.toLowerCase().includes(q) ||
                   (t.description || '').toLowerCase().includes(q)
          }
          return true
        })
      },
      async quickAddTask(columnId) {
        if (!this.quickAddTitle.trim()) { this.quickAddCol = null; return }
        const newTask = {
          title: this.quickAddTitle.trim(), description: '',
          status: 'pending', priority: 'medium',
          due_date: null, quadrant: null, columnId,
        }
        if (this.currentUser) {
          const res = await this.apiFetch('/api/tasks', {
            method: 'POST',
            body: JSON.stringify({ ...newTask, column_id: columnId }),
          })
          const created = await res.json()
          this.tasks.push({ ...newTask, id: created.id })
        } else {
          this.tasks.push({ ...newTask, id: Date.now() })
        }
        this.quickAddTitle = ''
        this.quickAddCol = null
      },
      toggleCollapse(colId) {
        const i = this.collapsedColumns.indexOf(colId)
        if (i === -1) this.collapsedColumns.push(colId)
        else this.collapsedColumns.splice(i, 1)
      },
      priorityColor(p) {
        return { high: '#ef5350', medium: '#ffa726', low: '#66bb6a' }[p] || '#764ba2'
      },
      isOverdue(dateStr) {
        if (!dateStr) return false
        return new Date(dateStr) < new Date(new Date().toDateString())
      },
      onDropToColumn(columnId) {
        if (this.draggedTaskId === null) return
        const idx = this.tasks.findIndex(t => t.id === this.draggedTaskId)
        if (idx !== -1) {
          this.tasks.splice(idx, 1, { ...this.tasks[idx], columnId })
          if (this.currentUser) this.apiFetch(`/api/tasks/${this.draggedTaskId}`, { method: 'PUT', body: JSON.stringify({ column_id: columnId }) })
        }
        this.draggedTaskId = null
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
            color: created.color || '#764ba2',
            createdAt: created.created_at,
          })
        } else {
          this.columns.push({
            id: Date.now(), name: this.columnForm.name,
            description: this.columnForm.description,
            createdAt: new Date().toISOString(),
          })
        }
        this.columnForm = { name: '', description: '', color: '#764ba2' }
        this.showColumnDialog = false
      },
      async deleteColumn(id) {
        if (this.currentUser) {
          await this.apiFetch(`/api/columns/${id}`, { method: 'DELETE' })
          await this.loadFromApi()
        } else {
          this.tasks = this.tasks.map(t => t.columnId === id ? { ...t, columnId: 'default' } : t)
          this.columns = this.columns.filter(c => c.id !== id)
        }
      },
      formatColDate(iso) {
        return new Date(iso).toLocaleDateString('lv-LV', { day: 'numeric', month: 'short', year: 'numeric' })
      },

      tasksByStatus(status) {
        return this.tasks.filter(t => t.status === status)
      },
      tasksByQuadrant(quadrant) {
        return this.tasks.filter(t => t.quadrant === quadrant)
      },
      tasksForDay(dateStr) {
        if (!dateStr) return []
        return this.tasks.filter(t => t.due_date === dateStr)
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
        if (this.currentUser) {
          if (this.editingTask) {
            await this.apiFetch(`/api/tasks/${this.editingTask.id}`, {
              method: 'PUT',
              body: JSON.stringify({ ...this.taskForm, column_id: this.editingTask.columnId }),
            })
            const idx = this.tasks.findIndex(t => t.id === this.editingTask.id)
            if (idx !== -1) this.tasks.splice(idx, 1, { ...this.tasks[idx], ...this.taskForm })
          } else {
            const defaultColId = this.columns[0]?.id ?? null
            const res = await this.apiFetch('/api/tasks', {
              method: 'POST',
              body: JSON.stringify({ ...this.taskForm, column_id: defaultColId }),
            })
            const created = await res.json()
            this.tasks.push({ id: created.id, quadrant: null, columnId: defaultColId, ...this.taskForm })
          }
        } else {
          if (this.editingTask) {
            const idx = this.tasks.findIndex(t => t.id === this.editingTask.id)
            if (idx !== -1) this.tasks.splice(idx, 1, { ...this.tasks[idx], ...this.taskForm })
          } else {
            this.tasks.push({ id: Date.now(), quadrant: null, columnId: 'default', ...this.taskForm })
          }
        }
        this.showTaskDialog = false
      },
      async deleteTask(id) {
        if (this.currentUser) {
          await this.apiFetch(`/api/tasks/${id}`, { method: 'DELETE' })
        }
        this.tasks = this.tasks.filter(t => t.id !== id)
      },
      clearMatrix() {
        this.tasks = this.tasks.map(t => ({ ...t, quadrant: null }))
      },
      onDragStart(event, taskId) {
        this.draggedTaskId = taskId
        event.dataTransfer.setData('text/plain', String(taskId))
        event.dataTransfer.effectAllowed = 'move'
      },
      onDropToQuadrant(quadrant) {
        if (this.draggedTaskId === null) return
        const idx = this.tasks.findIndex(t => t.id === this.draggedTaskId)
        if (idx !== -1) {
          this.tasks.splice(idx, 1, { ...this.tasks[idx], quadrant })
          if (this.currentUser) this.apiFetch(`/api/tasks/${this.draggedTaskId}`, { method: 'PUT', body: JSON.stringify({ quadrant }) })
        }
        this.draggedTaskId = null
      },
      onDropToCalendarDay(dateStr) {
        if (this.draggedTaskId === null) return
        const idx = this.tasks.findIndex(t => t.id === this.draggedTaskId)
        if (idx !== -1) {
          this.tasks.splice(idx, 1, { ...this.tasks[idx], due_date: dateStr })
          if (this.currentUser) this.apiFetch(`/api/tasks/${this.draggedTaskId}`, { method: 'PUT', body: JSON.stringify({ due_date: dateStr }) })
        }
        this.draggedTaskId = null
      },
      changeMonth(delta) {
        this.calendarMonth += delta
        if (this.calendarMonth > 11) { this.calendarMonth = 0; this.calendarYear++ }
        if (this.calendarMonth < 0)  { this.calendarMonth = 11; this.calendarYear-- }
      },

      async logout() {
        await fetch('/api/auth/logout', {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
          credentials: 'include'
        })
        this.currentUser = null
        this.tasks = JSON.parse(localStorage.getItem('tf_tasks') || '[]')
        this.columns = JSON.parse(localStorage.getItem('tf_columns') || 'null') || [
          { id: 'default', name: 'Iesūtne', description: 'Noklusējuma kolonna', createdAt: new Date().toISOString() }
        ]
      },

      apiFetch(url, options = {}) {
        return fetch(url, {
          ...options,
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', ...(options.headers || {}) },
          credentials: 'include',
        })
      },

      // ── Forum ──────────────────────────────────────────────

      async loadForumCategories() {
        this.forumLoading = true
        try {
          const res = await this.apiFetch('/api/forum/categories')
          this.forumCategories = await res.json()
        } finally {
          this.forumLoading = false
        }
      },

      async openForumCategory(cat) {
        this.forumCategory = cat
        this.forumView = 'posts'
        this.forumLoading = true
        try {
          const res = await this.apiFetch(`/api/forum/categories/${cat.id}/posts`)
          this.forumPosts = await res.json()
        } finally {
          this.forumLoading = false
        }
      },

      async openForumPost(post) {
        this.forumView = 'post'
        this.forumPostData = null
        this.forumLoading = true
        this.forumNewComment = ''
        try {
          const res = await this.apiFetch(`/api/forum/posts/${post.id}`)
          this.forumPostData = await res.json()
        } finally {
          this.forumLoading = false
        }
      },

      async submitForumPost() {
        if (!this.forumNewPost.title.trim() || !this.forumNewPost.body.trim()) return
        const res = await this.apiFetch(`/api/forum/categories/${this.forumCategory.id}/posts`, {
          method: 'POST',
          body: JSON.stringify(this.forumNewPost),
        })
        if (res.ok) {
          const created = await res.json()
          this.forumPosts.unshift(created)
          this.forumNewPost = { title: '', body: '' }
          this.showNewPostDialog = false
          if (this.forumCategory) this.forumCategory.posts_count = (this.forumCategory.posts_count || 0) + 1
        }
      },

      async submitForumComment() {
        if (!this.forumNewComment.trim() || !this.forumPostData) return
        const res = await this.apiFetch(`/api/forum/posts/${this.forumPostData.post.id}/comments`, {
          method: 'POST',
          body: JSON.stringify({ body: this.forumNewComment }),
        })
        if (res.ok) {
          const created = await res.json()
          this.forumPostData.comments.unshift(created)
          this.forumNewComment = ''
        }
      },

      async castVote(comment, value) {
        if (!this.currentUser) { this.showLoginWindow = true; return }
        const res = await this.apiFetch(`/api/forum/comments/${comment.id}/vote`, {
          method: 'POST',
          body: JSON.stringify({ value }),
        })
        if (res.ok) {
          const data = await res.json()
          const idx = this.forumPostData.comments.findIndex(c => c.id === comment.id)
          if (idx !== -1) {
            this.forumPostData.comments.splice(idx, 1, {
              ...this.forumPostData.comments[idx],
              upvotes:   data.upvotes,
              downvotes: data.downvotes,
              user_vote: data.user_vote,
            })
          }
        }
      },

      timeAgo(dateStr) {
        const diff = Date.now() - new Date(dateStr).getTime()
        const mins  = Math.floor(diff / 60000)
        const hours = Math.floor(diff / 3600000)
        const days  = Math.floor(diff / 86400000)
        if (mins  < 1)  return 'tikko'
        if (mins  < 60) return `pirms ${mins} min`
        if (hours < 24) return `pirms ${hours} h`
        if (days  < 7)  return `pirms ${days} d`
        return new Date(dateStr).toLocaleDateString('lv-LV', { day: 'numeric', month: 'short' })
      },

      // ───────────────────────────────────────────────────────

      async loadFromApi() {
        const [colRes, taskRes] = await Promise.all([
          this.apiFetch('/api/columns'),
          this.apiFetch('/api/tasks'),
        ])
        const cols  = await colRes.json()
        const tasks = await taskRes.json()

        this.columns = cols.map(c => ({
          id:          c.id,
          name:        c.name,
          description: c.description || '',
          color:       c.color || '#764ba2',
          createdAt:   c.created_at,
        }))

        const defaultId = this.columns[0]?.id ?? null

        this.tasks = tasks.map(t => ({
          id:          t.id,
          title:       t.title,
          description: t.description || '',
          status:      t.status,
          priority:    t.priority || 'medium',
          due_date:       t.due_date,
          quadrant:       t.quadrant,
          columnId:       t.column_id ?? defaultId,
          est_pomodoros:  t.est_pomodoros  ?? 1,
          done_pomodoros: t.done_pomodoros ?? 0,
        }))
      },
    },

    async mounted() {
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

      try {
        const res = await fetch('/api/auth/user', {
          headers: { 'Accept': 'application/json' },
          credentials: 'include'
        })
        const data = await res.json()
        if (data.user) {
          this.currentUser = data.user
          await this.loadFromApi()
        }
      } catch {
        // not logged in, silently ignore
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

  .site-header :deep(.v-toolbar-title) {
    overflow: visible;
  }

  .header-logo {
    font-size: 1.8rem;
    font-weight: bolder;
    font-family: bahnschrift, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    white-space: nowrap;
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

  .nav-user {
    font-weight: 600;
    padding: 0 8px;
  }

  .main-content {
    display: block;
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
    min-height: 80vh;
    gap: 20px;
    padding: 60px 40px;
  }

  .hero-section p {
    max-width: 720px;
    text-align: center;
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
    transition: transform 0.3s, box-shadow 0.3s;
    border: 3px solid #764ba2;
    border-radius: 12px;
    box-sizing: border-box;
    width: auto;
    min-height: 300px;
    height: 100%;
    cursor: pointer;
  }

  .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 32px rgba(118, 75, 162, 0.35);
    z-index: 2;
  }

  .card-in-card {
    background-color: rgb(235, 235, 235);
    padding: 24px 16px;
    margin: 16px;
    border-radius: 8px;
    text-align: center;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10px;
  }

  .card-in-card h3 {
    margin: 0;
    font-size: 1.1rem;
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
  .pomo-phase-work      { background: linear-gradient(135deg, #c0392b 0%, #764ba2 100%) !important; color: #fff !important; }
  .pomo-phase-break     { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%) !important; color: #fff !important; }
  .pomo-phase-longBreak { background: linear-gradient(135deg, #2980b9 0%, #6dd5fa 100%) !important; color: #fff !important; }
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
    background: rgba(0,0,0,0.2);
    border-radius: 20px;
    padding: 3px;
    gap: 2px;
  }
  .pomo-mode-btn {
    background: transparent;
    border: none;
    border-radius: 16px;
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
    background: rgba(255,255,255,0.2);
    border: none;
    border-radius: 50%;
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
    background: rgba(255,255,255,0.18);
    border: none; border-radius: 20px;
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
  .pomo-start-btn :deep(.v-btn__content) { font-size: 0.95rem; font-weight: 700; letter-spacing: 3px; }
  .pomo-start-btn { min-width: 130px !important; background: rgba(255,255,255,0.95) !important; color: #333 !important; }
  .pomo-icon-btn  { color: rgba(255,255,255,0.8) !important; }

  /* Meta row */
  .pomo-meta-row { display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; opacity: 0.85; }
  .pomo-session-label { font-size: 0.8rem; }
  .pomo-autostart :deep(.v-label)        { font-size: 0.78rem; color: rgba(255,255,255,0.85); }
  .pomo-autostart :deep(.v-switch__track){ opacity: 0.6; }

  /* Right panel */
  .pomo-right-panel { display: flex; flex-direction: column; }
  .pomo-panel-tabs  { background: transparent !important; }

  /* Task list card */
  .pomo-tasks-card   { background: #fff !important; }
  .pomo-tasks-header { display: flex; justify-content: space-between; align-items: center; }
  .pomo-tasks-title  { font-size: 0.9rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #555; }
  .pomo-empty        { text-align: center; color: #aaa; padding: 20px 0; font-size: 0.88rem; }

  .pomo-task-row {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 6px; border-radius: 6px;
    transition: background 0.15s;
  }
  .pomo-task-row:hover           { background: #f5f5f5; }
  .pomo-task-focused             { background: #f0eafa !important; border-left: 3px solid #764ba2; }
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
    padding: 7px 6px; border-radius: 6px;
    transition: background 0.15s;
  }
  .pomo-queue-row:hover  { background: #f5f5f5; }
  .pomo-queue-active     { background: #f0eafa !important; border-left: 3px solid #764ba2; font-weight: 600; }
  .pomo-queue-done       { opacity: 0.4; text-decoration: line-through; }
  .pomo-queue-label      { flex: 1; font-size: 0.92rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .pomo-queue-dur        { font-size: 0.8rem; color: #888; white-space: nowrap; margin-right: 4px; }
  .pomo-queue-total      { font-size: 0.82rem; color: #888; text-align: right; border-top: 1px solid #eee; padding-top: 8px; }

  @media (max-width: 700px) {
    .pomo-layout  { grid-template-columns: 1fr; }
    .pomo-timer   { font-size: 72px; }
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
    background: #f4f4f4;
    border-radius: 10px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
  }
  .trello-col-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 12px 10px 10px;
    border-bottom: 3px solid #764ba2;
    border-radius: 10px 10px 0 0;
    background: #ede7f6;
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
    color: #666;
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
    border-left: 3px solid #764ba2 !important;
    border-radius: 6px !important;
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
    border: 2px dashed #ccc;
    border-radius: 10px;
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
  .trello-add-col:hover {
    border-color: #764ba2;
    color: #764ba2;
  }

  /* Column drag-over highlight */
  .trello-column.drag-over {
    outline: 2px dashed #764ba2;
    background: #ede7f6;
  }

  /* Collapsed column */
  .trello-column.col-collapsed {
    min-height: unset;
  }
  .trello-column.col-collapsed .trello-col-header {
    border-radius: 10px;
  }

  /* Count badge */
  .trello-col-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
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
  .chip-del { margin-left: 6px; opacity: 0.5; cursor: pointer; font-weight: bold; font-size: 0.9em; }
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
  .quick-add-btn:hover { color: #764ba2 !important; }

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

  /* ── Eisenhower Matrix ── */
  .matrix-axis-row {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .matrix-axis-label-y {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    font-weight: 600;
    font-size: 0.8rem;
    color: #764ba2;
    letter-spacing: 1px;
    text-align: center;
    min-width: 20px;
  }
  .matrix-axis-label-x {
    text-align: center;
    font-weight: 600;
    font-size: 0.8rem;
    color: #764ba2;
    letter-spacing: 1px;
    margin-top: 6px;
  }
  .matrix-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    flex: 1;
  }
  .matrix-quadrant {
    border-radius: 10px;
    padding: 14px;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    transition: filter 0.15s;
  }
  .matrix-quadrant[draggable-over] { filter: brightness(0.94); }
  .q-do       { background: #ffebee; border: 2px solid #ef5350; }
  .q-schedule { background: #e8f5e9; border: 2px solid #66bb6a; }
  .q-delegate { background: #fff3e0; border: 2px solid #ffa726; }
  .q-eliminate{ background: #f3e5f5; border: 2px solid #ab47bc; }
  .q-label {
    font-weight: 700;
    font-size: 0.8rem;
    text-transform: uppercase;
    margin-bottom: 10px;
    line-height: 1.4;
  }
  .q-tasks {
    flex: 1;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-content: flex-start;
  }
  .q-chip {
    background: rgba(255,255,255,0.85);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 0.82rem;
    cursor: grab;
    border: 1px solid rgba(0,0,0,0.12);
    user-select: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
  }
  .q-chip:active { cursor: grabbing; opacity: 0.7; }

  /* ── Shared task pool ── */
  .task-pool {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 56px;
    padding: 12px;
    background: #f0f0f0;
    border-radius: 8px;
    border: 2px dashed #ccc;
    align-content: flex-start;
  }
  .pool-empty {
    width: 100%;
    text-align: center;
    color: #aaa;
    font-size: 0.85rem;
  }
  .pool-chip {
    background: #764ba2;
    color: #fff;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 0.82rem;
    cursor: grab;
    user-select: none;
    box-shadow: 0 1px 4px rgba(118,75,162,0.3);
  }
  .pool-chip:active { cursor: grabbing; opacity: 0.7; }

  /* ── Calendar ── */
  .cal-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 14px;
  }
  .cal-month-label {
    font-size: 1.15rem;
    font-weight: 600;
    min-width: 220px;
    text-align: center;
    text-transform: capitalize;
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
    color: #764ba2;
  }
  .cal-day-cell {
    min-height: 120px;
    border: 1px solid #e8e8e8;
    border-radius: 5px;
    padding: 5px;
    background: #fff;
    cursor: pointer;
    transition: background 0.15s;
  }
  .cal-day-cell:hover { background: #f9f0ff; }
  .cal-other { background: #fafafa; cursor: default; }
  .cal-other:hover { background: #fafafa; }
  .cal-today { border-color: #764ba2 !important; }
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
    background: #764ba2;
    color: #fff;
    border-radius: 50%;
    line-height: 22px;
    height: 22px;
  }
  .cal-other .cal-day-num { color: #ccc; }
  .cal-task-block {
    background: #764ba2;
    color: #fff;
    border-radius: 3px;
    padding: 1px 5px;
    font-size: 0.72rem;
    margin-bottom: 2px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
    user-select: none;
  }
  .cal-task-block:hover { background: #5e3a87; }

  /* ── Tool bottom navigation ── */
  .tool-bottom-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    padding: 12px 4px;
    border-top: 1px solid rgba(118, 75, 162, 0.2);
  }
  .tool-links {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
  }

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

  /* ── Forum ─────────────────────────────────────────────── */

  .forum-container { max-width: 900px !important; }

  .forum-header { text-align: center; }
  .forum-title  { font-size: 1.6rem; font-weight: 700; margin: 0 0 4px; }
  .forum-subtitle { color: #777; font-size: 0.95rem; margin: 0; }

  .forum-cat-card {
    border-left: 5px solid #764ba2;
    cursor: pointer;
    transition: box-shadow 0.2s, transform 0.15s;
  }
  .forum-cat-card:hover { box-shadow: 0 4px 18px rgba(118,75,162,0.18); transform: translateY(-2px); }
  .forum-cat-name { font-weight: 700; font-size: 0.97rem; margin-bottom: 2px; }
  .forum-cat-desc { font-size: 0.82rem; color: #888; }

  .forum-cat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 10px;
  }

  .forum-post-item {
    cursor: pointer;
    transition: box-shadow 0.15s;
  }
  .forum-post-item:hover { box-shadow: 0 2px 12px rgba(118,75,162,0.14); }
  .forum-post-title { font-weight: 600; font-size: 0.97rem; margin-bottom: 5px; }
  .forum-post-meta  { font-size: 0.78rem; color: #999; display: flex; align-items: center; flex-wrap: wrap; }

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

  .forum-comment-card { }
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

  .forum-vote-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border: 1px solid #ddd;
    border-radius: 20px;
    background: transparent;
    cursor: pointer;
    font-size: 0.82rem;
    color: #888;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
  }
  .forum-vote-btn:hover { background: #f0f0f0; }
  .forum-vote-up.active  { border-color: #43a047; background: #e8f5e9; color: #2e7d32; }
  .forum-vote-down.active{ border-color: #e53935; background: #ffebee; color: #c62828; }
</style>