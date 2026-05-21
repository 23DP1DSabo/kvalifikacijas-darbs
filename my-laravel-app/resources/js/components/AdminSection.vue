<template>
  <v-main class="main-content">
    <v-container fluid class="py-8 admin-container">

      <div class="forum-header mb-6">
        <h2 class="forum-title">{{ s.adminTitle }}</h2>
        <p class="forum-subtitle">{{ s.adminSubtitle }}</p>
      </div>

      <v-tabs v-model="adminTab" color="primary" class="mb-6">
        <v-tab value="users">{{ s.adminUsers }}</v-tab>
        <v-tab value="posts">{{ s.adminPosts }}</v-tab>
        <v-tab value="comments">{{ s.adminComments }}</v-tab>
        <v-tab value="categories">{{ s.adminCategories }}</v-tab>
      </v-tabs>

      <!-- Search bar -->
      <v-text-field
        v-model="adminSearch"
        :placeholder="s.adminSearch"
        prepend-inner-icon="mdi-magnify"
        variant="outlined"
        density="compact"
        hide-details
        clearable
        class="mb-5"
        style="max-width:480px"
      ></v-text-field>

      <div v-if="adminLoading" class="text-center py-10">
        <v-progress-circular indeterminate color="primary"></v-progress-circular>
      </div>

      <!-- ── Tab: Users ── -->
      <template v-if="adminTab === 'users' && !adminLoading">
        <v-card v-for="user in filteredUsers" :key="user.id" class="admin-row mb-2">
          <v-card-text class="d-flex align-center gap-3 flex-wrap">
            <div style="flex:1; min-width:0;">
              <span class="admin-username">{{ user.name }} {{ user.surname }}</span>
              <span class="mx-1 text-medium-emphasis">·</span>
              <span class="text-medium-emphasis">@{{ user.username }}</span>
              <span class="mx-1 text-medium-emphasis">·</span>
              <span class="text-medium-emphasis text-caption">{{ user.email }}</span>
            </div>
            <v-chip v-if="user.role === 'admin'" color="blue" size="x-small" class="mr-1">ADMIN</v-chip>
            <v-chip v-if="user.banned_at" color="error" size="x-small" class="mr-2">{{ s.adminBanned }}</v-chip>
            <v-btn v-if="user.role !== 'admin'"
                   size="small"
                   :color="user.banned_at ? 'success' : 'error'"
                   variant="tonal"
                   :loading="togglingBan === user.id"
                   @click="toggleBan(user)">
              {{ user.banned_at ? s.adminUnban : s.adminBan }}
            </v-btn>
          </v-card-text>
        </v-card>
        <div v-if="filteredUsers.length === 0" class="forum-empty-state">{{ s.adminNoResults }}</div>
      </template>

      <!-- ── Tab: Posts ── -->
      <template v-if="adminTab === 'posts' && !adminLoading">
        <v-card v-for="post in filteredPosts" :key="post.id" class="admin-row mb-2">
          <v-card-text class="d-flex align-start gap-3 flex-wrap">
            <div style="flex:1; min-width:0;">
              <div class="admin-post-title">{{ post.title }}</div>
              <div class="text-caption text-medium-emphasis mt-1">
                <span>{{ post.category?.name }}</span>
                <span class="mx-1">·</span>
                <span>@{{ post.user?.username }}</span>
                <span class="mx-1">·</span>
                <span>{{ post.comments_count }} {{ s.forumComments }}</span>
              </div>
            </div>
            <v-btn size="small" color="error" variant="tonal"
                   :loading="deletingPost === post.id"
                   @click="deletePost(post)">
              {{ s.adminDelete }}
            </v-btn>
          </v-card-text>
        </v-card>
        <div v-if="filteredPosts.length === 0" class="forum-empty-state">{{ s.adminNoResults }}</div>
      </template>

      <!-- ── Tab: Categories ── -->
      <template v-if="adminTab === 'categories' && !adminLoading">
        <v-btn color="primary" variant="elevated" prepend-icon="mdi-plus" class="mb-4" @click="openNewCatDialog">{{ s.adminAddCategory }}</v-btn>

        <v-card v-for="cat in adminCategories" :key="cat.id" class="admin-row mb-2">
          <v-card-text class="d-flex align-center gap-3 flex-wrap">
            <v-icon :color="cat.color" size="22" class="mr-1">{{ cat.icon }}</v-icon>
            <div style="flex:1; min-width:0;">
              <span class="admin-username">{{ cat.name }}</span>
              <span class="mx-1 text-medium-emphasis">·</span>
              <span class="text-caption text-medium-emphasis">{{ cat.slug }}</span>
              <span class="mx-1 text-medium-emphasis">·</span>
              <span class="text-caption text-medium-emphasis">{{ cat.posts_count }} posts</span>
            </div>
            <v-btn size="small" variant="tonal" @click="openEditCatDialog(cat)">{{ s.adminEdit }}</v-btn>
            <v-btn size="small" color="error" variant="tonal"
                   :loading="deletingCat === cat.id"
                   @click="deleteCategory(cat)">{{ s.adminDelete }}</v-btn>
          </v-card-text>
        </v-card>
        <div v-if="adminCategories.length === 0" class="forum-empty-state">{{ s.adminNoResults }}</div>
      </template>

      <!-- ── Tab: Comments ── -->
      <template v-if="adminTab === 'comments' && !adminLoading">
        <v-card v-for="comment in filteredComments" :key="comment.id" class="admin-row mb-2">
          <v-card-text class="d-flex align-start gap-3 flex-wrap">
            <div style="flex:1; min-width:0;">
              <div class="admin-comment-body">{{ comment.body }}</div>
              <div class="text-caption text-medium-emphasis mt-1">
                <span>@{{ comment.user?.username }}</span>
                <span class="mx-1">·</span>
                <span>{{ s.adminInPost }}: {{ comment.post?.title }}</span>
              </div>
            </div>
            <v-btn size="small" color="error" variant="tonal"
                   :loading="deletingComment === comment.id"
                   @click="deleteComment(comment)">
              {{ s.adminDelete }}
            </v-btn>
          </v-card-text>
        </v-card>
        <div v-if="filteredComments.length === 0" class="forum-empty-state">{{ s.adminNoResults }}</div>
      </template>

    </v-container>

    <!-- Category create/edit dialog -->
    <v-dialog v-model="showCatDialog" max-width="500">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ editingCat ? s.adminEditCategory : s.adminAddCategory }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showCatDialog = false"></v-btn>
          </div>
        </v-card-title>
        <v-card-text class="pt-4">
          <v-text-field v-model="catForm.name" :label="s.colName" variant="outlined" density="compact" class="mb-3"></v-text-field>
          <v-text-field v-model="catForm.slug" :label="s.adminCatSlug" variant="outlined" density="compact" class="mb-3" :hint="s.adminCatSlugHint"></v-text-field>
          <v-textarea v-model="catForm.description" :label="s.taskDesc" variant="outlined" rows="2" class="mb-3" hide-details></v-textarea>
          <v-text-field v-model="catForm.icon" :label="s.adminCatIcon" variant="outlined" density="compact" class="mb-3" :hint="s.adminCatIconHint"></v-text-field>
          <v-text-field v-model="catForm.color" :label="s.colColor" variant="outlined" density="compact" type="color"></v-text-field>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showCatDialog = false">{{ s.forumCancel }}</v-btn>
          <v-btn color="primary" variant="elevated" :loading="savingCat" @click="saveCategory">{{ s.adminSave }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-main>
</template>

<script>
import { apiFetch } from '../api.js'

export default {
  name: 'AdminSection',
  props: ['s'],
  emits: ['snackbar'],

  data() {
    return {
      adminTab: 'users',
      adminSearch: '',
      adminLoading: false,
      adminUsers: [],
      adminPosts: [],
      adminComments: [],
      togglingBan: null,
      deletingPost: null,
      deletingComment: null,
      adminCategories: [],
      showCatDialog: false,
      editingCat: null,
      catForm: { name: '', slug: '', description: '', icon: 'mdi-chat-outline', color: '#8B6E43' },
      savingCat: false,
      deletingCat: null,
    }
  },

  computed: {
    filteredUsers() {
      const q = this.adminSearch.toLowerCase().trim()
      if (!q) return this.adminUsers
      return this.adminUsers.filter(u =>
        u.name.toLowerCase().includes(q) ||
        u.surname.toLowerCase().includes(q) ||
        u.username.toLowerCase().includes(q) ||
        u.email.toLowerCase().includes(q)
      )
    },
    filteredPosts() {
      const q = this.adminSearch.toLowerCase().trim()
      if (!q) return this.adminPosts
      return this.adminPosts.filter(p =>
        p.title.toLowerCase().includes(q) ||
        (p.user?.username || '').toLowerCase().includes(q)
      )
    },
    filteredComments() {
      const q = this.adminSearch.toLowerCase().trim()
      if (!q) return this.adminComments
      return this.adminComments.filter(c =>
        c.body.toLowerCase().includes(q) ||
        (c.user?.username || '').toLowerCase().includes(q)
      )
    },
  },

  watch: {
    adminTab(val) {
      if (val === 'users'      && this.adminUsers.length === 0)      this.loadUsers()
      if (val === 'posts'      && this.adminPosts.length === 0)      this.loadPosts()
      if (val === 'comments'   && this.adminComments.length === 0)   this.loadComments()
      if (val === 'categories' && this.adminCategories.length === 0) this.loadCategories()
    },
  },

  mounted() {
    this.loadUsers()
  },

  methods: {
    async loadUsers() {
      this.adminLoading = true
      try {
        const res = await apiFetch('/api/admin/users')
        if (!res.ok) throw new Error()
        this.adminUsers = await res.json()
      } catch {
        this.$emit('snackbar', this.s.errLoadUsers)
      } finally {
        this.adminLoading = false
      }
    },

    async loadPosts() {
      this.adminLoading = true
      try {
        const res = await apiFetch('/api/admin/posts')
        if (!res.ok) throw new Error()
        this.adminPosts = await res.json()
      } catch {
        this.$emit('snackbar', this.s.errLoadPosts)
      } finally {
        this.adminLoading = false
      }
    },

    async loadComments() {
      this.adminLoading = true
      try {
        const res = await apiFetch('/api/admin/comments')
        if (!res.ok) throw new Error()
        this.adminComments = await res.json()
      } catch {
        this.$emit('snackbar', this.s.errLoadComments)
      } finally {
        this.adminLoading = false
      }
    },

    async toggleBan(user) {
      this.togglingBan = user.id
      try {
        const res = await apiFetch(`/api/admin/users/${user.id}/toggle-ban`, { method: 'POST' })
        if (!res.ok) throw new Error()
        const data = await res.json()
        user.banned_at = data.banned_at
      } catch {
        this.$emit('snackbar', this.s.errBanStatus)
      } finally {
        this.togglingBan = null
      }
    },

    async deletePost(post) {
      this.deletingPost = post.id
      try {
        const res = await apiFetch(`/api/admin/posts/${post.id}`, { method: 'DELETE' })
        if (!res.ok) throw new Error()
        this.adminPosts = this.adminPosts.filter(p => p.id !== post.id)
        this.$emit('snackbar', this.s.successPostDeleted, 'success')
      } catch {
        this.$emit('snackbar', this.s.errDeletePost)
      } finally {
        this.deletingPost = null
      }
    },

    async loadCategories() {
      this.adminLoading = true
      try {
        const res = await apiFetch('/api/admin/categories')
        if (!res.ok) throw new Error()
        this.adminCategories = await res.json()
      } catch {
        this.$emit('snackbar', this.s.errLoadCategories)
      } finally {
        this.adminLoading = false
      }
    },

    openNewCatDialog() {
      this.editingCat = null
      this.catForm = { name: '', slug: '', description: '', icon: 'mdi-chat-outline', color: '#8B6E43' }
      this.showCatDialog = true
    },

    openEditCatDialog(cat) {
      this.editingCat = cat
      this.catForm = { name: cat.name, slug: cat.slug, description: cat.description || '', icon: cat.icon || 'mdi-chat-outline', color: cat.color || '#8B6E43' }
      this.showCatDialog = true
    },

    async saveCategory() {
      this.savingCat = true
      try {
        const url    = this.editingCat ? `/api/admin/categories/${this.editingCat.id}` : '/api/admin/categories'
        const method = this.editingCat ? 'PUT' : 'POST'
        const res    = await apiFetch(url, { method, body: JSON.stringify(this.catForm) })
        if (!res.ok) throw new Error()
        const saved = await res.json()
        if (this.editingCat) {
          const idx = this.adminCategories.findIndex(c => c.id === this.editingCat.id)
          if (idx !== -1) this.adminCategories.splice(idx, 1, { ...this.adminCategories[idx], ...saved })
        } else {
          this.adminCategories.push({ ...saved, posts_count: 0 })
        }
        this.showCatDialog = false
      } catch {
        this.$emit('snackbar', this.s.errSaveCategory)
      } finally {
        this.savingCat = false
      }
    },

    async deleteCategory(cat) {
      this.deletingCat = cat.id
      try {
        const res = await apiFetch(`/api/admin/categories/${cat.id}`, { method: 'DELETE' })
        if (!res.ok) throw new Error()
        this.adminCategories = this.adminCategories.filter(c => c.id !== cat.id)
      } catch {
        this.$emit('snackbar', this.s.errDeleteCategory)
      } finally {
        this.deletingCat = null
      }
    },

    async deleteComment(comment) {
      this.deletingComment = comment.id
      try {
        const res = await apiFetch(`/api/admin/comments/${comment.id}`, { method: 'DELETE' })
        if (!res.ok) throw new Error()
        this.adminComments = this.adminComments.filter(c => c.id !== comment.id)
        this.$emit('snackbar', this.s.successCommentDeleted, 'success')
      } catch {
        this.$emit('snackbar', this.s.errDeleteComment)
      } finally {
        this.deletingComment = null
      }
    },
  },
}
</script>
