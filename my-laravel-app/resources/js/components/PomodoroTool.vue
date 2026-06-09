<template>
  <v-main class="main-content">
    <v-container fluid class="py-8 pomo-container">
      <div class="pomo-layout">

        <!-- LEFT: Timer card -->
        <v-card class="pomo-card site-section" :class="`pomo-phase-${currentPhase}`">
          <v-card-text class="pomo-card-body">

            <!-- Top row: mode switcher + gear -->
            <div class="pomo-top-row">
              <div class="pomo-mode-switcher">
                <button class="pomo-mode-btn" :class="{ active: pomodoroMode === 'cycle' }" @click="setMode('cycle')">{{ s.pomoCycle }}</button>
                <button class="pomo-mode-btn" :class="{ active: pomodoroMode === 'queue' }" @click="setMode('queue')">{{ s.pomoSession }}</button>
              </div>
              <button class="pomo-gear-btn" @click="showPomodoroSettings = true" :title="s.pomoSettingsTitle">
                {{ s.pomoSettingsTitle }}
              </button>
            </div>

            <!-- Cycle mode: phase tabs -->
            <div v-if="pomodoroMode === 'cycle'" class="pomo-tabs">
              <button class="pomo-tab" :class="{ active: currentPhase === 'work' }"      @click="switchPhase('work')">{{ s.pomoWork }}</button>
              <button class="pomo-tab" :class="{ active: currentPhase === 'break' }"     @click="switchPhase('break')">{{ s.pomoShortBreak }}</button>
              <button class="pomo-tab" :class="{ active: currentPhase === 'longBreak' }" @click="switchPhase('longBreak')">{{ s.pomoLongBreak }}</button>
            </div>

            <!-- Queue mode: block indicator -->
            <div v-else class="pomo-queue-indicator">
              <template v-if="sessionQueue.length > 0 && currentQueueIndex < sessionQueue.length">
                <v-icon size="14" class="mr-1">{{ currentPhase === 'work' ? 'mdi-timer' : 'mdi-coffee' }}</v-icon>
                {{ sessionQueue[currentQueueIndex].label }}
                <span class="pomo-queue-pos"> · {{ currentQueueIndex + 1 }}/{{ sessionQueue.length }}</span>
              </template>
              <span v-else-if="currentQueueIndex >= sessionQueue.length && sessionQueue.length > 0">{{ s.pomoSessionDone }}</span>
              <span v-else class="pomo-queue-hint">{{ s.pomoAddBlocksHint }}</span>
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
                {{ s.pomoStart }}
              </v-btn>
              <v-btn v-else class="pomo-start-btn" variant="elevated" size="large" @click="pausePomodoro">
                {{ s.pomoPause }}
              </v-btn>
              <v-btn v-if="isPomodoroRunning" icon variant="text" class="pomo-icon-btn" @click="skipPhase" :title="s.pomoSkip" :aria-label="s.pomoSkip">
                <v-icon>mdi-skip-next</v-icon>
              </v-btn>
              <v-btn icon variant="text" class="pomo-icon-btn" @click="resetPomodoro" :title="s.pomoReset" :aria-label="s.pomoReset">
                <v-icon>mdi-restart</v-icon>
              </v-btn>
            </div>

            <!-- Meta row -->
            <div class="pomo-meta-row">
              <span v-if="pomodoroMode === 'cycle'" class="pomo-session-label">#{{ sessionNumber }} · {{ cyclesCompleted }} {{ s.pomoCycles }}</span>
              <span v-else class="pomo-session-label"></span>
              <v-switch v-model="pomodoroAutoStart" :label="s.pomoAutoStart" density="compact" hide-details color="white" class="pomo-autostart"></v-switch>
            </div>

          </v-card-text>
        </v-card>

        <!-- RIGHT: tasks + queue panel -->
        <div class="pomo-right-panel">
          <v-tabs v-model="rightPanelTab" color="primary" density="compact" class="pomo-panel-tabs mb-2">
            <v-tab value="tasks">{{ s.pomoTasks }}</v-tab>
            <v-tab value="queue">{{ s.pomoQueue }}</v-tab>
          </v-tabs>

          <!-- Tasks tab -->
          <v-card v-if="rightPanelTab === 'tasks'" class="pomo-tasks-card site-section">
            <v-card-text>
              <div class="pomo-tasks-header">
                <span class="pomo-tasks-title">{{ s.pomoTasks }}</span>
                <v-btn size="small" variant="tonal" prepend-icon="mdi-plus" @click="openTaskDialog()">{{ s.pomoAdd }}</v-btn>
              </div>
              <v-divider class="my-3"></v-divider>
              <div v-if="tasks.length === 0" class="pomo-empty">{{ s.pomoNoTasks }}</div>
              <div v-for="task in tasks" :key="task.id"
                   class="pomo-task-row"
                   :class="{ 'pomo-task-focused': focusedTaskId === task.id, 'pomo-task-done': task.status === 'completed' }">
                <button class="pomo-task-btn"
                        :class="{ 'pomo-task-btn--active': focusedTaskId === task.id }"
                        :aria-label="focusedTaskId === task.id ? s.pomoUnfocusTask : s.pomoFocusTask"
                        @click="focusedTaskId = (focusedTaskId === task.id ? null : task.id)">
                  ●
                </button>
                <span class="pomo-task-name" @click="openTaskDialog(task)">{{ task.title }}</span>
                <span class="pomo-task-count" aria-hidden="true">
                  {{ task.done_pomodoros || 0 }} / {{ task.est_pomodoros || 1 }}
                </span>
                <button class="pomo-task-btn"
                        :class="{ 'pomo-task-btn--done': task.status === 'completed' }"
                        :aria-label="s.pomoCompleteTask"
                        @click="toggleTaskDone(task)">
                  {{ task.status === 'completed' ? '✓' : '○' }}
                </button>
              </div>
            </v-card-text>
          </v-card>

          <!-- Queue tab -->
          <v-card v-else class="pomo-tasks-card site-section">
            <v-card-text>
              <div class="pomo-tasks-header">
                <span class="pomo-tasks-title">{{ s.pomoQueue }}</span>
                <v-btn size="small" variant="tonal" prepend-icon="mdi-plus" @click="showQueueDialog = true">{{ s.pomoAddBlock }}</v-btn>
              </div>
              <v-divider class="my-3"></v-divider>

              <div v-if="sessionQueue.length === 0" class="pomo-empty">
                {{ s.pomoNoBlocks }}
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
                <button class="pomo-queue-btn" :disabled="idx === 0" :aria-label="s.pomoMoveUp" @click="moveQueueBlock(idx, -1)">▲</button>
                <button class="pomo-queue-btn" :disabled="idx === sessionQueue.length - 1" :aria-label="s.pomoMoveDown" @click="moveQueueBlock(idx, 1)">▼</button>
                <button class="pomo-queue-btn pomo-queue-btn--danger" :aria-label="s.pomoRemoveBlock" @click="removeQueueBlock(idx)">✕</button>
              </div>

              <div v-if="sessionQueue.length > 0" class="pomo-queue-total mt-3">
                {{ s.pomoTotal }} {{ sessionQueueTotal }} min
              </div>
            </v-card-text>
          </v-card>
        </div>
      </div>

      <div class="tool-bottom-nav mt-4">
        <v-btn variant="tonal" prepend-icon="mdi-arrow-left" @click="$emit('navigate', 'home')">{{ s.back }}</v-btn>
        <div class="tool-links">
          <v-btn variant="text" size="small" prepend-icon="mdi-format-list-checks" @click="$emit('navigate', 'taskboard')">{{ s.toolTaskboard }}</v-btn>
          <v-btn variant="text" size="small" prepend-icon="mdi-grid" @click="$emit('navigate', 'matrix')">{{ s.toolMatrix }}</v-btn>
          <v-btn variant="text" size="small" prepend-icon="mdi-calendar" @click="$emit('navigate', 'calendar')">{{ s.toolCalendar }}</v-btn>
        </div>
      </div>
    </v-container>

    <!-- Settings dialog -->
    <v-dialog v-model="showPomodoroSettings" max-width="360">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ s.pomoSettingsTitle }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showPomodoroSettings = false"></v-btn>
          </div>
        </v-card-title>
        <v-card-text class="pt-5">
          <v-text-field v-model.number="settingsForm.pomo"  :label="s.pomoPomoMin"  type="number" min="1" max="60" variant="outlined" density="compact" class="mb-3"></v-text-field>
          <v-text-field v-model.number="settingsForm.short" :label="s.pomoShortMin" type="number" min="1" max="30" variant="outlined" density="compact" class="mb-3"></v-text-field>
          <v-text-field v-model.number="settingsForm.long"  :label="s.pomoLongMin"  type="number" min="1" max="60" variant="outlined" density="compact"></v-text-field>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showPomodoroSettings = false">{{ s.pomoCancel }}</v-btn>
          <v-btn color="primary" variant="elevated" @click="applyPomodoroSettings">{{ s.pomoSave }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Queue block dialog -->
    <v-dialog v-model="showQueueDialog" max-width="360">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ s.pomoAddBlockTitle }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showQueueDialog = false"></v-btn>
          </div>
        </v-card-title>
        <v-card-text class="pt-5">
          <div class="mb-3 text-body-2 text-medium-emphasis">{{ s.pomoBlockType }}</div>
          <v-btn-toggle v-model="queueForm.type" mandatory class="mb-4" color="primary" variant="outlined" density="compact" style="width:100%">
            <v-btn value="work" style="flex:1">{{ s.pomoBlockWork }}</v-btn>
            <v-btn value="break" style="flex:1">{{ s.pomoBlockBreak }}</v-btn>
            <v-btn value="longBreak" style="flex:1">{{ s.pomoBlockLong }}</v-btn>
          </v-btn-toggle>
          <v-text-field v-model.number="queueForm.minutes" :label="s.pomoBlockDur" type="number" min="1" max="120" variant="outlined" density="compact" class="mb-3"></v-text-field>
          <v-select v-if="queueForm.type === 'work'"
                    v-model="queueForm.taskId"
                    :items="tasks.map(t => ({ title: t.title, value: t.id }))"
                    :label="s.pomoBlockTask"
                    clearable variant="outlined" density="compact"></v-select>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showQueueDialog = false">{{ s.pomoCancel }}</v-btn>
          <v-btn color="primary" variant="elevated" @click="addQueueBlock">{{ s.pomoAdd }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-main>
</template>

<script>
import { apiFetch } from '../api.js'

export default {
  name: 'PomodoroTool',
  props: ['tasks', 'currentUser', 's', 'darkMode', 'openTaskDialog'],
  emits: ['show-login', 'snackbar', 'navigate'],

  data() {
    return {
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
  },

  methods: {
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
            this.notify(this.currentPhase === 'work' ? this.s.pomoNotifyWorkBlock : this.s.pomoNotifyBreak)
            if (this.pomodoroAutoStart) this.startPomodoro()
          } else {
            this.notify(this.s.pomoNotifySessionDone)
          }
        } else {
          if (this.currentPhase === 'work') {
            this.cyclesCompleted += 1
            this.sessionNumber   += 1
            this.creditFocusedTask()
            const next = this.cyclesCompleted % 4 === 0 ? 'longBreak' : 'break'
            this.currentPhase  = next
            this.pomodoroTime  = next === 'longBreak' ? this.longBreakDuration : this.shortBreakDuration
            this.notify(next === 'longBreak' ? this.s.pomoNotifyLongBreak : this.s.pomoNotifyShortBreak)
          } else {
            this.currentPhase = 'work'
            this.pomodoroTime = this.pomodoroDuration
            this.notify(this.s.pomoNotifyWorkStart)
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
        if (this.currentUser) apiFetch(`/api/tasks/${this.focusedTaskId}`, { method: 'PUT', body: JSON.stringify({ done_pomodoros: updated.done_pomodoros }) })
      }
    },

    addQueueBlock() {
      if (!this.queueForm.minutes || this.queueForm.minutes < 1) return
      const labels = { work: this.s.pomoWork, break: this.s.pomoShortBreak, longBreak: this.s.pomoLongBreak }
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
      localStorage.setItem('pomo_settings', JSON.stringify(this.settingsForm))
      if (this.currentUser) {
        apiFetch('/api/pomodoro/settings', { method: 'POST', body: JSON.stringify(this.settingsForm) })
      }
    },

    async toggleTaskDone(task) {
      const newStatus = task.status === 'completed' ? 'pending' : 'completed'
      const idx = this.tasks.findIndex(t => t.id === task.id)
      if (idx !== -1) {
        this.tasks.splice(idx, 1, { ...this.tasks[idx], status: newStatus })
        if (this.currentUser) apiFetch(`/api/tasks/${task.id}`, { method: 'PUT', body: JSON.stringify({ status: newStatus }) })
      }
    },

    notify(message) {
      if (window.Notification && Notification.permission === 'granted') {
        new Notification(message)
      } else {
        console.log(message)
      }
    },
  },
}
</script>
