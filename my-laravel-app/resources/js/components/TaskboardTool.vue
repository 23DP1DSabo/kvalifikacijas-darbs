<template>
  <v-main class="main-content">
    <v-container fluid class="py-8">
      <v-card class="taskboard-section site-section">
        <v-card-text>

          <!-- Board toolbar -->
          <div class="board-toolbar mb-2">
            <div class="d-flex align-center gap-2">
              <h2 class="board-title">{{ s.taskboardTitle }}</h2>
              <v-chip size="small" variant="tonal" color="primary">
                {{ tasks.length }} {{ s.taskboardTasks }}
              </v-chip>
            </div>
            <div class="d-flex align-center gap-2 flex-wrap">
              <v-text-field
                v-model="boardFilter"
                :placeholder="s.taskboardSearch"
                :aria-label="s.taskboardSearch"
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                clearable
                hide-details
                style="min-width:180px"
              ></v-text-field>
              <v-btn color="primary" variant="elevated" prepend-icon="mdi-plus" @click="openTaskDialog()">
                {{ s.taskboardNewTask }}
              </v-btn>
            </div>
          </div>

          <!-- Filter row -->
          <div class="d-flex align-center gap-2 flex-wrap mb-4">
            <v-select
              v-model="filterStatus"
              :items="statusFilterItems"
              :label="s.taskStatus"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              style="max-width:160px"
            ></v-select>
            <v-select
              v-model="filterPriority"
              :items="priorityFilterItems"
              :label="s.taskPriority"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              style="max-width:160px"
            ></v-select>
            <v-text-field
              v-model="filterDateFrom"
              :label="s.taskFilterFrom"
              type="date"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              style="max-width:190px"
            ></v-text-field>
            <v-text-field
              v-model="filterDateTo"
              :label="s.taskFilterTo"
              type="date"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              style="max-width:190px"
            ></v-text-field>
            <v-btn v-if="hasActiveFilters" size="small" variant="text" color="error" @click="clearFilters">
              {{ s.taskFilterClear }}
            </v-btn>
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
              <div class="trello-col-header" :style="{ borderBottomColor: col.color || '#8B6E43' }">
                <div class="trello-col-meta">
                  <div class="d-flex align-center gap-2">
                    <span class="trello-col-name">{{ col.name }}</span>
                    <span class="trello-col-count" :style="{ background: col.color || '#8B6E43' }">
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
                         :title="collapsedColumns.includes(col.id) ? s.taskboardExpand : s.taskboardCollapse"
                         :aria-label="collapsedColumns.includes(col.id) ? s.taskboardExpand : s.taskboardCollapse">
                    <v-icon size="14">{{ collapsedColumns.includes(col.id) ? 'mdi-chevron-down' : 'mdi-chevron-up' }}</v-icon>
                  </v-btn>
                  <v-btn icon size="x-small" variant="text"
                         :title="s.taskboardEditCol" :aria-label="s.taskboardEditCol"
                         @click="openEditColDialog(col)">
                    <v-icon size="14">mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn v-if="col.id !== 'default'" icon size="x-small" variant="text" color="error"
                         :title="s.taskboardDeleteCol" :aria-label="s.taskboardDeleteCol" @click="deleteColumn(col.id)">
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
                    <p>{{ s.taskboardNoTasks }}</p>
                    <p class="trello-empty-hint">{{ s.taskboardDragHint }}</p>
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
                          {{ { high: s.taskboardPriHigh, medium: s.taskboardPriMed, low: s.taskboardPriLow }[task.priority] }}
                        </v-chip>
                        <span v-else></span>
                        <div class="task-actions">
                          <v-btn icon size="x-small" variant="text" :title="s.taskboardEdit" :aria-label="s.taskboardEdit"
                                 @click.stop="openTaskDialog(task)">
                            <v-icon size="13">mdi-pencil</v-icon>
                          </v-btn>
                          <v-btn icon size="x-small" variant="text" color="error" :title="s.taskboardDelete" :aria-label="s.taskboardDelete"
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
                          {{ s.taskboardDone }}
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
                      :placeholder="s.taskTitle + '...'"
                      :aria-label="s.taskTitle"
                      variant="outlined"
                      density="compact"
                      autofocus
                      hide-details
                      @keyup.enter="quickAddTask(col.id)"
                      @keyup.esc="quickAddCol = null"
                      @blur="quickAddCol = null">
                      <template #append-inner>
                        <v-btn icon size="x-small" variant="text" color="primary"
                               :aria-label="s.quickAddConfirm"
                               @mousedown.prevent="quickAddTask(col.id)">
                          <v-icon size="16">mdi-check</v-icon>
                        </v-btn>
                      </template>
                    </v-text-field>
                  </template>
                  <v-btn v-else variant="text" size="small" prepend-icon="mdi-plus"
                         block class="quick-add-btn"
                         @click="quickAddCol = col.id; quickAddTitle = ''">
                    {{ s.taskboardAddTask }}
                  </v-btn>
                </div>
              </template>
            </div>

            <!-- Add column button -->
            <div class="trello-add-col"
                 role="button" tabindex="0"
                 :aria-label="s.addColumnBtn"
                 @click="$emit('open-column-dialog')"
                 @keydown.enter="$emit('open-column-dialog')"
                 @keydown.space.prevent="$emit('open-column-dialog')">
              <v-icon size="28" aria-hidden="true">mdi-plus</v-icon>
              <span>{{ s.taskboardNewCol }}</span>
            </div>
          </div>

        </v-card-text>
      </v-card>
      <div class="tool-bottom-nav mt-4">
        <v-btn variant="tonal" prepend-icon="mdi-arrow-left" @click="$emit('navigate', 'home')">{{ s.back }}</v-btn>
        <div class="tool-links">
          <v-btn variant="text" size="small" prepend-icon="mdi-timer" @click="$emit('navigate', 'pomodoro')">{{ s.toolPomodoro }}</v-btn>
          <v-btn variant="text" size="small" prepend-icon="mdi-grid" @click="$emit('navigate', 'matrix')">{{ s.toolMatrix }}</v-btn>
          <v-btn variant="text" size="small" prepend-icon="mdi-calendar" @click="$emit('navigate', 'calendar')">{{ s.toolCalendar }}</v-btn>
        </div>
      </div>
    </v-container>

    <!-- Edit column dialog -->
    <v-dialog v-model="showEditColDialog" max-width="420">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ s.taskboardEditColTitle }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showEditColDialog = false"></v-btn>
          </div>
        </v-card-title>
        <v-card-text class="pt-4">
          <v-text-field v-model="editColForm.name" :label="s.colName" variant="outlined" density="compact" class="mb-3"></v-text-field>
          <v-textarea v-model="editColForm.description" :label="s.colDesc" variant="outlined" rows="2" class="mb-3" hide-details></v-textarea>
          <div class="mb-1 text-body-2 text-medium-emphasis">{{ s.colColor }}</div>
          <div class="col-color-picker">
            <div v-for="c in columnColors" :key="c"
                 class="col-color-swatch"
                 :style="{ background: c, outline: editColForm.color === c ? '3px solid #333' : 'none' }"
                 @click="editColForm.color = c">
            </div>
          </div>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showEditColDialog = false">{{ s.colCancel }}</v-btn>
          <v-btn color="primary" variant="elevated" @click="saveEditCol">{{ s.taskSave }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-main>
</template>

<script>
import { apiFetch } from '../api.js'

export default {
  name: 'TaskboardTool',
  props: ['tasks', 'columns', 'currentUser', 's', 'darkMode', 'openTaskDialog', 'deleteTask', 'saveColumn', 'deleteColumn'],
  emits: ['show-login', 'snackbar', 'navigate', 'open-column-dialog'],

  data() {
    return {
      collapsedColumns: [],
      dragOverColumn: null,
      boardFilter: '',
      filterStatus: '',
      filterPriority: '',
      filterDateFrom: '',
      filterDateTo: '',
      quickAddCol: null,
      quickAddTitle: '',
      draggedTaskId: null,
      showEditColDialog: false,
      editingCol: null,
      editColForm: { name: '', description: '', color: '#8B6E43' },
      columnColors: ['#8B6E43','#5a8a6e','#ef5350','#66bb6a','#ffa726','#26c6da','#ec407a','#78909c'],
    }
  },

  computed: {
    hasActiveFilters() {
      return !!(this.boardFilter || this.filterStatus || this.filterPriority || this.filterDateFrom || this.filterDateTo)
    },
    statusFilterItems() {
      return [
        { title: this.s.statusPending,    value: 'pending' },
        { title: this.s.statusInProgress, value: 'in_progress' },
        { title: this.s.statusDone,       value: 'completed' },
      ]
    },
    priorityFilterItems() {
      return [
        { title: this.s.taskboardPriHigh, value: 'high' },
        { title: this.s.taskboardPriMed,  value: 'medium' },
        { title: this.s.taskboardPriLow,  value: 'low' },
        { title: this.s.priNone,          value: 'none' },
      ]
    },
  },

  methods: {
    tasksByColumn(columnId) {
      return this.tasks.filter(t => {
        if ((t.columnId || 'default') !== columnId) return false
        if (this.boardFilter) {
          const q = this.boardFilter.toLowerCase()
          if (!t.title.toLowerCase().includes(q) && !(t.description || '').toLowerCase().includes(q)) return false
        }
        if (this.filterStatus && t.status !== this.filterStatus) return false
        if (this.filterPriority && (t.priority || 'none') !== this.filterPriority) return false
        if (this.filterDateFrom && t.created_at && t.created_at.slice(0, 10) < this.filterDateFrom) return false
        if (this.filterDateTo   && t.created_at && t.created_at.slice(0, 10) > this.filterDateTo)   return false
        return true
      })
    },

    clearFilters() {
      this.boardFilter    = ''
      this.filterStatus   = ''
      this.filterPriority = ''
      this.filterDateFrom = ''
      this.filterDateTo   = ''
    },

    async quickAddTask(columnId) {
      if (!this.quickAddTitle.trim()) { this.quickAddCol = null; return }
      const newTask = {
        title: this.quickAddTitle.trim(), description: '',
        status: 'pending', priority: 'medium',
        due_date: null, quadrant: null, columnId,
      }
      if (this.currentUser) {
        const res = await apiFetch('/api/tasks', {
          method: 'POST',
          body: JSON.stringify({ ...newTask, column_id: columnId }),
        })
        if (!res.ok) { this.$emit('snackbar', 'Neizdevās pievienot uzdevumu. Mēģiniet vēlreiz.'); return }
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
      return { high: '#ef5350', medium: '#ffa726', low: '#66bb6a' }[p] || '#8B6E43'
    },

    isOverdue(dateStr) {
      if (!dateStr) return false
      return new Date(dateStr) < new Date(new Date().toDateString())
    },

    onDragStart(event, taskId) {
      this.draggedTaskId = taskId
      event.dataTransfer.setData('text/plain', String(taskId))
      event.dataTransfer.effectAllowed = 'move'
    },

    onDropToColumn(columnId) {
      if (this.draggedTaskId === null) return
      const idx = this.tasks.findIndex(t => t.id === this.draggedTaskId)
      if (idx !== -1) {
        this.tasks.splice(idx, 1, { ...this.tasks[idx], columnId })
        if (this.currentUser) apiFetch(`/api/tasks/${this.draggedTaskId}`, { method: 'PUT', body: JSON.stringify({ column_id: columnId }) })
      }
      this.draggedTaskId = null
    },

    formatColDate(iso) {
      return new Date(iso).toLocaleDateString('lv-LV', { day: 'numeric', month: 'short', year: 'numeric' })
    },

    openEditColDialog(col) {
      this.editingCol = col
      this.editColForm = { name: col.name, description: col.description || '', color: col.color || '#8B6E43' }
      this.showEditColDialog = true
    },

    async saveEditCol() {
      if (!this.editColForm.name.trim() || !this.editingCol) return
      if (this.currentUser) {
        try {
          const res = await apiFetch(`/api/columns/${this.editingCol.id}`, {
            method: 'PUT',
            body: JSON.stringify(this.editColForm),
          })
          if (!res.ok) throw new Error()
        } catch {
          this.$emit('snackbar', 'Neizdevās saglabāt kolonnu.')
          return
        }
      }
      const idx = this.columns.findIndex(c => c.id === this.editingCol.id)
      if (idx !== -1) {
        this.columns.splice(idx, 1, { ...this.columns[idx], ...this.editColForm })
      }
      this.showEditColDialog = false
    },
  },
}
</script>
