<template>
  <v-main class="main-content">
    <v-container fluid class="py-8">
      <v-card class="calendar-section site-section">
        <v-card-text>
          <div class="d-flex justify-space-between align-center mb-4">
            <h2>{{ s.calTitle }}</h2>
            <v-btn color="primary" variant="elevated" @click="openTaskDialog()">{{ s.newTask }}</v-btn>
          </div>

          <div class="cal-nav">
            <div class="cal-nav-group">
              <v-btn icon variant="text" size="small" :aria-label="s.calPrevMonth" @click="changeMonth(-1)"><v-icon>mdi-chevron-left</v-icon></v-btn>
              <span class="cal-month-label" aria-live="polite">{{ calMonthName }}</span>
              <v-btn icon variant="text" size="small" :aria-label="s.calNextMonth" @click="changeMonth(1)"><v-icon>mdi-chevron-right</v-icon></v-btn>
            </div>
            <div class="cal-nav-group">
              <v-btn icon variant="text" size="small" :aria-label="s.calPrevYear" @click="changeYear(-1)"><v-icon>mdi-chevron-left</v-icon></v-btn>
              <span class="cal-year-label">{{ calendarYear }}</span>
              <v-btn icon variant="text" size="small" :aria-label="s.calNextYear" @click="changeYear(1)"><v-icon>mdi-chevron-right</v-icon></v-btn>
            </div>
            <v-btn size="x-small" variant="tonal" @click="goToToday">{{ s.calToday }}</v-btn>
          </div>

          <div class="cal-grid">
            <div class="cal-day-header" v-for="d in s.calDays" :key="d">{{ d }}</div>
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

          <h3 class="mt-6 mb-3">{{ s.calUnscheduled }}</h3>
          <div class="task-pool" @dragover.prevent @drop="onDropToCalendarDay(null)">
            <div v-if="tasksWithoutDate.length === 0" class="pool-empty">{{ s.calNoUnscheduled }}</div>
            <div v-for="task in tasksWithoutDate" :key="task.id"
                 class="pool-chip" draggable="true" @dragstart="onDragStart($event, task.id)"
                 @click="openTaskDialog(task)">
              {{ task.title }}
            </div>
          </div>
        </v-card-text>
      </v-card>
      <div class="tool-bottom-nav mt-4">
        <v-btn variant="tonal" prepend-icon="mdi-arrow-left" @click="$emit('navigate', 'home')">{{ s.back }}</v-btn>
        <div class="tool-links">
          <v-btn variant="text" size="small" prepend-icon="mdi-timer" @click="$emit('navigate', 'pomodoro')">{{ s.toolPomodoro }}</v-btn>
          <v-btn variant="text" size="small" prepend-icon="mdi-format-list-checks" @click="$emit('navigate', 'taskboard')">{{ s.toolTaskboard }}</v-btn>
          <v-btn variant="text" size="small" prepend-icon="mdi-grid" @click="$emit('navigate', 'matrix')">{{ s.toolMatrix }}</v-btn>
        </div>
      </div>
    </v-container>
  </v-main>
</template>

<script>
import { apiFetch } from '../api.js'

export default {
  name: 'CalendarTool',
  props: ['tasks', 'currentUser', 's', 'darkMode', 'openTaskDialog'],
  emits: ['show-login', 'snackbar', 'navigate'],

  data() {
    const saved = JSON.parse(localStorage.getItem('cal_view') || 'null')
    return {
      calendarYear:  saved?.year  ?? new Date().getFullYear(),
      calendarMonth: saved?.month ?? new Date().getMonth(),
      draggedTaskId: null,
    }
  },

  computed: {
    calMonthName() {
      return new Date(this.calendarYear, this.calendarMonth, 1)
        .toLocaleDateString(this.s.calDays[0] === 'Mo' ? 'en-US' : 'lv-LV', { month: 'long' })
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
    },
    tasksWithoutDate() {
      return this.tasks.filter(t => !t.due_date)
    },
  },

  methods: {
    saveView() {
      localStorage.setItem('cal_view', JSON.stringify({ year: this.calendarYear, month: this.calendarMonth }))
    },

    changeMonth(delta) {
      this.calendarMonth += delta
      if (this.calendarMonth > 11) { this.calendarMonth = 0; this.calendarYear++ }
      if (this.calendarMonth < 0)  { this.calendarMonth = 11; this.calendarYear-- }
      this.saveView()
    },

    changeYear(delta) {
      this.calendarYear += delta
      this.saveView()
    },

    goToToday() {
      this.calendarYear  = new Date().getFullYear()
      this.calendarMonth = new Date().getMonth()
      this.saveView()
    },

    tasksForDay(dateStr) {
      if (!dateStr) return []
      return this.tasks.filter(t => t.due_date === dateStr)
    },

    onDragStart(event, taskId) {
      this.draggedTaskId = taskId
      event.dataTransfer.setData('text/plain', String(taskId))
      event.dataTransfer.effectAllowed = 'move'
    },

    async onDropToCalendarDay(dateStr) {
      if (this.draggedTaskId === null) return
      const id = this.draggedTaskId
      this.draggedTaskId = null
      const idx = this.tasks.findIndex(t => t.id === id)
      if (idx === -1) return
      this.tasks[idx].due_date = dateStr
      if (this.currentUser) {
        try {
          const res = await apiFetch(`/api/tasks/${id}`, { method: 'PUT', body: JSON.stringify({ due_date: dateStr }) })
          if (!res.ok) throw new Error()
        } catch {
          this.$emit('snackbar', 'Neizdevās saglabāt kalendāra izmaiņas.')
        }
      }
    },
  },
}
</script>
