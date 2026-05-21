<template>
  <v-main class="main-content">
    <v-container fluid class="py-8 forum-container">

      <!-- ── View: Category list ── -->
      <template v-if="forumView === 'categories'">
        <div class="forum-header mb-6">
          <h2 class="forum-title">{{ s.forumTitle }}</h2>
          <p class="forum-subtitle">{{ s.forumSubtitle }}</p>
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
        <v-btn variant="text" prepend-icon="mdi-arrow-left" class="mb-3" @click="forumView = 'categories'">{{ s.forumAllForums }}</v-btn>

        <div class="forum-cat-header mb-4">
          <div>
            <h2 class="forum-title">
              <v-icon :color="forumCategory.color" class="mr-2">{{ forumCategory.icon }}</v-icon>
              {{ forumCategory.name }}
            </h2>
            <p class="forum-subtitle">{{ forumCategory.description }}</p>
          </div>
          <v-btn v-if="currentUser" color="primary" variant="elevated" prepend-icon="mdi-pencil-outline" @click="showNewPostDialog = true">
            {{ s.forumNewPost }}
          </v-btn>
          <v-btn v-else variant="tonal" @click="$emit('show-login')">{{ s.forumSignInToWrite }}</v-btn>
        </div>

        <!-- Search + Sort + Favorites toolbar -->
        <div class="forum-toolbar mb-4">
          <v-text-field
            v-model="forumSearch"
            :placeholder="s.forumSearch"
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            density="compact"
            hide-details
            clearable
            class="forum-search-field"
          ></v-text-field>

          <div class="forum-sort-btns">
            <v-btn size="small" :variant="forumSort === 'latest' ? 'elevated' : 'tonal'"
                   :color="forumSort === 'latest' ? 'primary' : undefined"
                   @click="forumSort = 'latest'">{{ s.forumSortLatest }}</v-btn>
            <v-btn size="small" :variant="forumSort === 'oldest' ? 'elevated' : 'tonal'"
                   :color="forumSort === 'oldest' ? 'primary' : undefined"
                   @click="forumSort = 'oldest'">{{ s.forumSortOldest }}</v-btn>
            <v-btn size="small" :variant="forumSort === 'popular' ? 'elevated' : 'tonal'"
                   :color="forumSort === 'popular' ? 'primary' : undefined"
                   @click="forumSort = 'popular'">{{ s.forumSortPopular }}</v-btn>
          </div>

          <v-btn v-if="currentUser"
                 size="small"
                 :variant="forumShowFavorites ? 'elevated' : 'outlined'"
                 color="warning"
                 @click="forumShowFavorites = !forumShowFavorites">{{ s.forumFavorites }}</v-btn>
        </div>

        <div v-if="forumLoading" class="text-center py-10">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </div>
        <div v-else-if="forumPosts.length === 0" class="forum-empty-state">
          {{ forumShowFavorites ? s.forumNoFavorites : s.forumNoPosts }}
        </div>

        <v-card v-for="post in forumPosts" :key="post.id"
                class="forum-post-item mb-2"
                @click="openForumPost(post)">
          <v-card-text class="d-flex align-start">
            <div style="flex:1; min-width:0;">
              <div class="forum-post-title">{{ post.title }}</div>
              <div class="forum-post-meta">
                <img v-if="post.user.avatar_url" :src="post.user.avatar_url" class="forum-user-avatar" :alt="post.user.name">
                <v-icon v-else size="13" class="mr-1">mdi-account-outline</v-icon>{{ post.user.name }}<span v-if="post.user.role === 'admin'" class="forum-admin-badge">ADMIN</span>
                <span class="mx-2">·</span>
                <v-icon size="13" class="mr-1">mdi-clock-outline</v-icon>{{ timeAgo(post.created_at) }}
                <span class="mx-2">·</span>
                <v-icon size="13" class="mr-1">mdi-comment-outline</v-icon>{{ post.comments_count }}
              </div>
            </div>
            <button v-if="currentUser"
                    class="forum-fav-btn"
                    :class="{ active: post.is_favorited }"
                    :aria-label="post.is_favorited ? s.forumUnfavorite : s.forumFavorite"
                    @click="toggleForumFavorite(post, $event)">
              <v-icon size="18">{{ post.is_favorited ? 'mdi-star' : 'mdi-star-outline' }}</v-icon>
            </button>
          </v-card-text>
        </v-card>
      </template>

      <!-- ── View: Post detail ── -->
      <template v-else-if="forumView === 'post'">
        <v-btn variant="text" prepend-icon="mdi-arrow-left" class="mb-3"
               @click="forumView = 'posts'">{{ s.forumBackTo }} {{ forumCategory?.name }}</v-btn>

        <div v-if="forumLoading" class="text-center py-10">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </div>

        <template v-else-if="forumPostData">
          <!-- Post body -->
          <v-card class="mb-4">
            <v-card-text>
              <div class="d-flex align-start">
                <div style="flex:1; min-width:0;">
                  <h2 class="forum-post-detail-title">{{ forumPostData.post.title }}</h2>
                  <div class="forum-post-detail-meta mb-4">
                    <img v-if="forumPostData.post.user.avatar_url" :src="forumPostData.post.user.avatar_url" class="forum-user-avatar" :alt="forumPostData.post.user.name">
                    <v-icon v-else size="14" class="mr-1">mdi-account-outline</v-icon>{{ forumPostData.post.user.name }}<span v-if="forumPostData.post.user.role === 'admin'" class="forum-admin-badge">ADMIN</span>
                    <span class="mx-2">·</span>
                    <v-icon size="14" class="mr-1">mdi-clock-outline</v-icon>{{ timeAgo(forumPostData.post.created_at) }}
                  </div>
                </div>
                <div class="d-flex align-center gap-1">
                  <template v-if="currentUser && currentUser.id === forumPostData.post.user_id">
                    <v-btn icon size="x-small" variant="text" @click="openEditPost">
                      <v-icon size="15">mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn icon size="x-small" variant="text" color="error" @click="deleteOwnPost">
                      <v-icon size="15">mdi-delete</v-icon>
                    </v-btn>
                  </template>
                  <button v-if="currentUser"
                          class="forum-fav-btn"
                          :class="{ active: forumPostData.post.is_favorited }"
                          :aria-label="forumPostData.post.is_favorited ? s.forumUnfavorite : s.forumFavorite"
                          @click="toggleForumFavorite(forumPostData.post, $event)">
                    <v-icon size="20">{{ forumPostData.post.is_favorited ? 'mdi-star' : 'mdi-star-outline' }}</v-icon>
                  </button>
                </div>
              </div>
              <div class="forum-post-body">{{ forumPostData.post.body }}</div>
            </v-card-text>
          </v-card>

          <!-- Add comment -->
          <v-card class="mb-5">
            <v-card-text v-if="currentUser">
              <div class="forum-comment-form-label mb-2">{{ s.forumCommentAs }} <strong>{{ currentUser.name }}</strong></div>
              <v-textarea v-model="forumNewComment" variant="outlined" density="compact" rows="3"
                          :placeholder="s.forumCommentPlaceholder" hide-details class="mb-3"></v-textarea>
              <div class="d-flex justify-end">
                <v-btn color="primary" variant="elevated"
                       :disabled="!forumNewComment.trim()"
                       @click="submitForumComment">{{ s.forumPublish }}</v-btn>
              </div>
            </v-card-text>
            <v-card-text v-else class="text-center py-4">
              <v-btn variant="tonal" @click="$emit('show-login')">{{ s.forumSignInToComment }}</v-btn>
            </v-card-text>
          </v-card>

          <!-- Comments -->
          <div class="forum-comments-header mb-3">
            {{ s.forumComments }} ({{ forumPostData.comments.length }})
          </div>

          <div v-if="forumPostData.comments.length === 0" class="forum-empty-state mb-4">
            {{ s.forumNoComments }}
          </div>

          <v-card v-for="comment in forumPostData.comments" :key="comment.id" class="forum-comment-card mb-2">
            <v-card-text>
              <div class="forum-comment-header">
                <img v-if="comment.user.avatar_url" :src="comment.user.avatar_url" class="forum-user-avatar" :alt="comment.user.name">
                <v-icon v-else size="16" class="mr-1">mdi-account-circle-outline</v-icon>
                <span class="forum-comment-author">{{ comment.user.name }}</span><span v-if="comment.user.role === 'admin'" class="forum-admin-badge">ADMIN</span>
                <span class="forum-comment-time">{{ timeAgo(comment.created_at) }}</span>
                <div v-if="currentUser && currentUser.id === comment.user_id" class="forum-comment-own-actions">
                  <v-btn icon size="x-small" variant="text" @click="openEditComment(comment)"><v-icon size="13">mdi-pencil</v-icon></v-btn>
                  <v-btn icon size="x-small" variant="text" color="error" @click="deleteOwnComment(comment)"><v-icon size="13">mdi-delete</v-icon></v-btn>
                </div>
              </div>
              <div v-if="editingCommentId === comment.id" class="mt-2">
                <v-textarea v-model="editCommentBody" variant="outlined" density="compact" rows="2" hide-details class="mb-2"></v-textarea>
                <div class="d-flex justify-end gap-2">
                  <v-btn size="small" variant="text" @click="editingCommentId = null">{{ s.forumCancel }}</v-btn>
                  <v-btn size="small" color="primary" variant="elevated" @click="saveEditComment(comment)">{{ s.taskSave }}</v-btn>
                </div>
              </div>
              <div v-else class="forum-comment-body">{{ comment.body }}</div>
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

    <!-- Edit post dialog -->
    <v-dialog v-model="showEditPostDialog" max-width="620">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ s.forumEditPost }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showEditPostDialog = false"></v-btn>
          </div>
        </v-card-title>
        <v-card-text class="pt-4">
          <v-text-field v-model="editPostForm.title" :label="s.forumPostTitle" variant="outlined" density="compact" class="mb-3"></v-text-field>
          <v-textarea v-model="editPostForm.body" :label="s.forumPostBody" variant="outlined" rows="7" hide-details></v-textarea>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showEditPostDialog = false">{{ s.forumCancel }}</v-btn>
          <v-btn color="primary" variant="elevated"
                 :disabled="!editPostForm.title.trim() || !editPostForm.body.trim()"
                 @click="saveEditPost">{{ s.taskSave }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- New post dialog -->
    <v-dialog v-model="showNewPostDialog" max-width="620">
      <v-card>
        <v-card-title class="bg-primary text-white">
          <div class="d-flex justify-space-between align-center">
            <span>{{ s.forumNewPostLabel }} {{ forumCategory?.name }}</span>
            <v-btn icon="mdi-close" variant="text" @click="showNewPostDialog = false"></v-btn>
          </div>
        </v-card-title>
        <v-card-text class="pt-4">
          <v-text-field v-model="forumNewPost.title" :label="s.forumPostTitle" variant="outlined" density="compact" class="mb-3"></v-text-field>
          <v-textarea v-model="forumNewPost.body" :label="s.forumPostBody" variant="outlined" rows="7" hide-details></v-textarea>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showNewPostDialog = false">{{ s.forumCancel }}</v-btn>
          <v-btn color="primary" variant="elevated"
                 :disabled="!forumNewPost.title.trim() || !forumNewPost.body.trim()"
                 @click="submitForumPost">{{ s.forumPublish }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-main>
</template>

<script>
import { apiFetch } from '../api.js'

export default {
  name: 'ForumSection',
  props: ['currentUser', 's', 'darkMode'],
  emits: ['show-login', 'snackbar'],

  data() {
    return {
      forumView: 'categories',
      forumCategory: null,
      forumCategories: [],
      forumPosts: [],
      forumPostData: null,
      forumNewPost: { title: '', body: '' },
      forumNewComment: '',
      forumLoading: false,
      forumSearch: '',
      forumSort: 'latest',
      forumShowFavorites: false,
      forumSearchTimer: null,
      showNewPostDialog: false,
      showEditPostDialog: false,
      editPostForm: { title: '', body: '' },
      editingCommentId: null,
      editCommentBody: '',
    }
  },

  mounted() {
    this.loadForumCategories()
  },

  watch: {
    forumSearch() {
      clearTimeout(this.forumSearchTimer)
      this.forumSearchTimer = setTimeout(() => this.loadForumPosts(), 400)
    },
    forumSort() {
      this.loadForumPosts()
    },
    forumShowFavorites() {
      this.loadForumPosts()
    },
  },

  methods: {
    async loadForumCategories() {
      this.forumLoading = true
      try {
        const res = await apiFetch('/api/forum/categories')
        if (!res.ok) throw new Error()
        this.forumCategories = await res.json()
      } catch {
        this.$emit('snackbar', this.s.errLoadForumCats)
      } finally {
        this.forumLoading = false
      }
    },

    async openForumCategory(cat) {
      this.forumCategory = cat
      this.forumView = 'posts'
      this.forumSearch = ''
      this.forumSort = 'latest'
      this.forumShowFavorites = false
      await this.loadForumPosts()
    },

    async loadForumPosts() {
      if (!this.forumCategory) return
      this.forumLoading = true
      try {
        const params = new URLSearchParams()
        if (this.forumSearch.trim()) params.set('search', this.forumSearch.trim())
        if (this.forumSort !== 'latest') params.set('sort', this.forumSort)
        if (this.forumShowFavorites) params.set('favorites', '1')
        const qs = params.toString()
        const res = await apiFetch(`/api/forum/categories/${this.forumCategory.id}/posts${qs ? '?' + qs : ''}`)
        if (!res.ok) throw new Error()
        this.forumPosts = await res.json()
      } catch {
        this.$emit('snackbar', this.s.errLoadPosts)
      } finally {
        this.forumLoading = false
      }
    },

    async toggleForumFavorite(post, event) {
      event?.stopPropagation()
      if (!this.currentUser) { this.$emit('show-login'); return }
      try {
        const res = await apiFetch(`/api/forum/posts/${post.id}/favorite`, { method: 'POST' })
        if (!res.ok) throw new Error()
        const data = await res.json()
        post.is_favorited = data.is_favorited
        if (this.forumShowFavorites && !data.is_favorited) {
          this.forumPosts = this.forumPosts.filter(p => p.id !== post.id)
        }
      } catch {
        this.$emit('snackbar', this.s.errChangeFav)
      }
    },

    async openForumPost(post) {
      this.forumView = 'post'
      this.forumPostData = null
      this.forumLoading = true
      this.forumNewComment = ''
      try {
        const res = await apiFetch(`/api/forum/posts/${post.id}`)
        if (!res.ok) throw new Error()
        this.forumPostData = await res.json()
      } catch {
        this.$emit('snackbar', this.s.errLoadPost)
      } finally {
        this.forumLoading = false
      }
    },

    async submitForumPost() {
      if (!this.forumNewPost.title.trim() || !this.forumNewPost.body.trim()) return
      try {
        const res = await apiFetch(`/api/forum/categories/${this.forumCategory.id}/posts`, {
          method: 'POST',
          body: JSON.stringify(this.forumNewPost),
        })
        if (!res.ok) throw new Error()
        const created = await res.json()
        this.forumPosts.unshift(created)
        this.forumNewPost = { title: '', body: '' }
        this.showNewPostDialog = false
        if (this.forumCategory) this.forumCategory.posts_count = (this.forumCategory.posts_count || 0) + 1
      } catch {
        this.$emit('snackbar', this.s.errPublishPost)
      }
    },

    async submitForumComment() {
      if (!this.forumNewComment.trim() || !this.forumPostData) return
      try {
        const res = await apiFetch(`/api/forum/posts/${this.forumPostData.post.id}/comments`, {
          method: 'POST',
          body: JSON.stringify({ body: this.forumNewComment }),
        })
        if (!res.ok) throw new Error()
        const created = await res.json()
        this.forumPostData.comments.unshift(created)
        this.forumNewComment = ''
      } catch {
        this.$emit('snackbar', this.s.errPublishComment)
      }
    },

    async castVote(comment, value) {
      if (!this.currentUser) { this.$emit('show-login'); return }
      try {
        const res = await apiFetch(`/api/forum/comments/${comment.id}/vote`, {
          method: 'POST',
          body: JSON.stringify({ value }),
        })
        if (!res.ok) throw new Error()
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
      } catch {
        this.$emit('snackbar', this.s.errVote)
      }
    },

    openEditPost() {
      this.editPostForm = { title: this.forumPostData.post.title, body: this.forumPostData.post.body }
      this.showEditPostDialog = true
    },

    async saveEditPost() {
      if (!this.forumPostData) return
      try {
        const res = await apiFetch(`/api/forum/posts/${this.forumPostData.post.id}`, {
          method: 'PUT',
          body: JSON.stringify(this.editPostForm),
        })
        if (!res.ok) throw new Error()
        this.forumPostData.post.title = this.editPostForm.title
        this.forumPostData.post.body  = this.editPostForm.body
        this.showEditPostDialog = false
      } catch {
        this.$emit('snackbar', this.s.errSavePost)
      }
    },

    async deleteOwnPost() {
      if (!this.forumPostData) return
      try {
        const res = await apiFetch(`/api/forum/posts/${this.forumPostData.post.id}`, { method: 'DELETE' })
        if (!res.ok) throw new Error()
        this.forumView = 'posts'
        this.forumPosts = this.forumPosts.filter(p => p.id !== this.forumPostData.post.id)
        this.forumPostData = null
      } catch {
        this.$emit('snackbar', this.s.errDeletePost)
      }
    },

    openEditComment(comment) {
      this.editingCommentId = comment.id
      this.editCommentBody  = comment.body
    },

    async saveEditComment(comment) {
      try {
        const res = await apiFetch(`/api/forum/comments/${comment.id}`, {
          method: 'PUT',
          body: JSON.stringify({ body: this.editCommentBody }),
        })
        if (!res.ok) throw new Error()
        const idx = this.forumPostData.comments.findIndex(c => c.id === comment.id)
        if (idx !== -1) this.forumPostData.comments.splice(idx, 1, { ...this.forumPostData.comments[idx], body: this.editCommentBody })
        this.editingCommentId = null
      } catch {
        this.$emit('snackbar', this.s.errSaveComment)
      }
    },

    async deleteOwnComment(comment) {
      try {
        const res = await apiFetch(`/api/forum/comments/${comment.id}`, { method: 'DELETE' })
        if (!res.ok) throw new Error()
        const idx = this.forumPostData.comments.findIndex(c => c.id === comment.id)
        if (idx !== -1) this.forumPostData.comments.splice(idx, 1)
      } catch {
        this.$emit('snackbar', this.s.errDeleteComment)
      }
    },

    timeAgo(dateStr) {
      const diff = Date.now() - new Date(dateStr).getTime()
      const mins  = Math.floor(diff / 60000)
      const hours = Math.floor(diff / 3600000)
      const days  = Math.floor(diff / 86400000)
      const isEn  = this.s.calDays && this.s.calDays[0] === 'Mo'
      if (mins  < 1)  return this.s.timeJustNow
      if (mins  < 60) return this.s.timeMinAgo(mins)
      if (hours < 24) return this.s.timeHourAgo(hours)
      if (days  < 7)  return this.s.timeDayAgo(days)
      return new Date(dateStr).toLocaleDateString(isEn ? 'en-US' : 'lv-LV', { day: 'numeric', month: 'short' })
    },
  },
}
</script>
