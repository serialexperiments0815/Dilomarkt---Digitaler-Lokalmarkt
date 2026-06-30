<template>
  <div class="view-container">
    <h2>Mein Profil</h2>
    <div class="profile-info-card">
      <div class="profile-avatar-lg">{{ initials }}</div>
      <div class="profile-details">
        <div class="profile-field">
          <label>Vorname</label>
          <span>{{ user.first_name }}</span>
        </div>
        <div class="profile-field">
          <label>Nachname</label>
          <span>{{ user.last_name }}</span>
        </div>
        <div class="profile-field">
          <label>E-Mail</label>
          <span>{{ user.email }}</span>
        </div>
        <div class="profile-field">
          <label>Konto-Typ</label>
          <span class="role-badge">{{ user.role === 'seller' ? '🏪 Verkäufer' : '🛒 Käufer' }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/useAuth.js'

const router = useRouter()
const { user } = useAuth()
const initials = computed(() => {
  if (!user.value) return '?'
  return (user.value.first_name?.charAt(0) ?? '') + (user.value.last_name?.charAt(0) ?? '')
})

onMounted(() => { if (!user.value) router.push('/login') })
</script>

<style scoped>
.profile-info-card { background: var(--bg-dark-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 28px; display: flex; gap: 28px; align-items: flex-start; }
.profile-avatar-lg { width: 72px; height: 72px; border-radius: 50%; background: var(--primary-accent); display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: bold; color: white; flex-shrink: 0; text-transform: uppercase; }
.profile-details { flex: 1; display: flex; flex-direction: column; gap: 16px; }
.profile-field { display: flex; flex-direction: column; gap: 4px; }
.profile-field label { font-size: 11px; color: #888; letter-spacing: 0.8px; text-transform: uppercase; }
.profile-field span { font-size: 15px; color: var(--text-main); }
.role-badge { display: inline-block; background: #2a2a2a; border: 1px solid var(--border-color); border-radius: 20px; padding: 4px 12px; font-size: 13px; }
@media (max-width: 480px) { .profile-info-card { flex-direction: column; align-items: center; text-align: center; } }
</style>
