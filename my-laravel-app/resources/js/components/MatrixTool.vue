<template>
  <v-main class="main-content">
    <v-container fluid class="py-8">
      <v-card class="matrix-section site-section">
        <v-card-text>
          <div class="d-flex justify-space-between align-center mb-4 flex-wrap gap-2">
            <h2>{{ s.matrixTitle }}</h2>
            <div class="d-flex ga-2">
              <v-btn color="primary" variant="elevated" @click="openTaskDialog()" style="min-width:152px">{{ s.newTask }}</v-btn>
              <v-btn color="error" variant="outlined" @click="clearMatrix" style="min-width:148px">{{ s.matrixClear }}</v-btn>
            </div>
          </div>

          <!-- Axis and canvas -->
          <div class="mfree-wrap">
            <div class="mfree-y-label">{{ s.matrixUrgency }}</div>

            <div class="mfree-canvas-col">
              <div
                class="mfree-canvas"
                ref="canvas"
                @mousemove="onMouseMove"
                @mouseup="onMouseUp"
                @mouseleave="onMouseUp"
                @touchmove.prevent="onTouchMove"
                @touchend="onTouchEnd"
              >
                <!-- Quadrant backgrounds -->
                <div class="mfree-q mfree-q-do">
                  <span class="mfree-q-label">{{ s.matrixDoNow }}<br><small>{{ s.matrixDoSub }}</small></span>
                </div>
                <div class="mfree-q mfree-q-schedule">
                  <span class="mfree-q-label mfree-q-label-right">{{ s.matrixSchedule }}<br><small>{{ s.matrixScheduleSub }}</small></span>
                </div>
                <div class="mfree-q mfree-q-delegate">
                  <span class="mfree-q-label">{{ s.matrixDelegate }}<br><small>{{ s.matrixDelegateSub }}</small></span>
                </div>
                <div class="mfree-q mfree-q-eliminate">
                  <span class="mfree-q-label mfree-q-label-right">{{ s.matrixEliminate }}<br><small>{{ s.matrixEliminateSub }}</small></span>
                </div>

                <!-- Divider lines -->
                <div class="mfree-vline"></div>
                <div class="mfree-hline"></div>

                <!-- Task chips -->
                <div
                  v-for="task in placedTasks"
                  :key="task.id"
                  class="mfree-chip"
                  :class="{ 'mfree-chip-dragging': dragging && dragging.task.id === task.id }"
                  :style="{ left: task.x_pos + '%', top: task.y_pos + '%' }"
                  @mousedown.prevent="startDrag($event, task)"
                  @touchstart.prevent="startTouchDrag($event, task)"
                >
                  <span class="mfree-chip-title">{{ task.title }}</span>
                  <button class="mfree-chip-del" @mousedown.stop @click.stop="removeFromMatrix(task)">×</button>
                </div>
              </div>
              <div class="mfree-x-label">{{ s.matrixImportance }}</div>
            </div>
          </div>

          <!-- Unassigned area -->
          <h3 class="mt-6 mb-3">{{ s.matrixUnassigned }}</h3>
          <div class="task-pool">
            <div v-if="unassignedTasks.length === 0" class="pool-empty">{{ s.matrixAllAssigned }}</div>
            <div v-for="task in unassignedTasks" :key="task.id"
                 class="pool-chip"
                 @click="placeAtCenter(task)">
              {{ task.title }}
              <button class="chip-del" :aria-label="`${s.taskDelete}: ${task.title}`" @click.stop="deleteTask(task.id)">×</button>
            </div>
          </div>
        </v-card-text>
      </v-card>

      <div class="tool-bottom-nav mt-4">
        <v-btn variant="tonal" prepend-icon="mdi-arrow-left" @click="$emit('navigate', 'home')">{{ s.back }}</v-btn>
        <div class="tool-links">
          <v-btn variant="text" size="small" prepend-icon="mdi-timer" @click="$emit('navigate', 'pomodoro')">{{ s.toolPomodoro }}</v-btn>
          <v-btn variant="text" size="small" prepend-icon="mdi-format-list-checks" @click="$emit('navigate', 'taskboard')">{{ s.toolTaskboard }}</v-btn>
          <v-btn variant="text" size="small" prepend-icon="mdi-calendar" @click="$emit('navigate', 'calendar')">{{ s.toolCalendar }}</v-btn>
        </div>
      </div>
    </v-container>
  </v-main>
</template>

<script>
import { apiFetch } from '../api.js'

export default {
  name: 'MatrixTool',
  props: ['tasks', 'currentUser', 's', 'darkMode', 'openTaskDialog', 'deleteTask'],
  emits: ['show-login', 'snackbar', 'navigate'],

  data() {
    return {
      dragging: null,  
    }
  },

  computed: {
    placedTasks()    { return this.tasks.filter(t => t.x_pos != null) },
    unassignedTasks(){ return this.tasks.filter(t => t.x_pos == null) },
  },

  methods: {
    placeAtCenter(task) {
      task.x_pos = 50
      task.y_pos = 50
      this.persist(task)
    },

    removeFromMatrix(task) {
      task.x_pos = null
      task.y_pos = null
      this.persist(task)
    },

    clearMatrix() {
      this.tasks.forEach(t => { t.x_pos = null; t.y_pos = null })
    },

    startDrag(event, task) {
      const rect = this.$refs.canvas.getBoundingClientRect()
      this.dragging = {
        task,
        startClientX: event.clientX,
        startClientY: event.clientY,
        startXPos: task.x_pos,
        startYPos: task.y_pos,
        canvasW: rect.width,
        canvasH: rect.height,
        moved: false,
      }
    },

    startTouchDrag(event, task) {
      const t = event.touches[0]
      const rect = this.$refs.canvas.getBoundingClientRect()
      this.dragging = {
        task,
        startClientX: t.clientX,
        startClientY: t.clientY,
        startXPos: task.x_pos,
        startYPos: task.y_pos,
        canvasW: rect.width,
        canvasH: rect.height,
        moved: false,
      }
    },

    applyMove(clientX, clientY) {
      if (!this.dragging) return
      const dx = clientX - this.dragging.startClientX
      const dy = clientY - this.dragging.startClientY
      if (Math.abs(dx) > 3 || Math.abs(dy) > 3) this.dragging.moved = true
      this.dragging.task.x_pos = Math.min(98, Math.max(2, this.dragging.startXPos + (dx / this.dragging.canvasW) * 100))
      this.dragging.task.y_pos = Math.min(98, Math.max(2, this.dragging.startYPos + (dy / this.dragging.canvasH) * 100))
    },

    onMouseMove(event) { this.applyMove(event.clientX, event.clientY) },
    onTouchMove(event) { this.applyMove(event.touches[0].clientX, event.touches[0].clientY) },

    finishDrag() {
      if (!this.dragging) return
      const { task, moved } = this.dragging
      this.dragging = null
      if (moved) this.persist(task)
      else this.openTaskDialog(task)
    },

    onMouseUp()  { this.finishDrag() },
    onTouchEnd() { this.finishDrag() },

    persist(task) {
      if (!this.currentUser) return
      apiFetch(`/api/tasks/${task.id}`, {
        method: 'PUT',
        body: JSON.stringify({ x_pos: task.x_pos, y_pos: task.y_pos }),
      }).catch(() => this.$emit('snackbar', this.s.errSaveMatrix))
    },
  },
}
</script>
