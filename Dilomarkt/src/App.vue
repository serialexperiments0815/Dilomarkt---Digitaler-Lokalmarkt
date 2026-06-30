<template>
  <div class="dilomarkt-app">

    <!-- Top header -->
    <header class="top-header">
      <span class="top-logo" @click="router.push('/')">Dilomarkt</span>
      <div class="top-right">
        <!-- Not logged in --> 
        <template v-if="!user">
          <button class="btn-primary top-btn" @click="router.push('/login')">Anmelden</button>
          <button class="top-btn-outline" @click="router.push('/register')">Registrieren</button>
        </template>
        <!-- Logged in: avatar -->
        <div v-else class="avatar-wrapper" ref="avatarRef">
          <button class="avatar-btn" @click="toggleDropdown">{{ initials }}</button>
          <div v-if="dropdownOpen" class="avatar-dropdown">
            <div class="dropdown-name">{{ user.first_name }} {{ user.last_name }}</div>
            <div class="dropdown-role">{{ user.role === 'seller' ? 'Verkäufer' : 'Käufer' }}</div>
            <hr class="dropdown-divider" />
<button @click="goTo('/chats')">💬 Meine Chats</button>
<button @click="goTo('/profil')">👤 Mein Profil</button>
<button v-if="user.role === 'seller'" @click="goTo('/mein-shop')">🏪 Mein Shop</button>
<button @click="goTo('/bestellungen')">📦 Meine Bestellungen</button>
<button class="dropdown-logout" @click="logout">🚪 Abmelden</button>
          </div>
        </div>
      </div>
    </header>

    <RouterView />

    <nav class="mobile-bottom-nav">
      <button :class="{ active: route.name === 'home' }" @click="router.push('/')">🏠<br>Start</button>
      <button :class="{ active: route.name === 'search' }" @click="router.push('/suche')">🔍<br>Suche</button>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '@/useAuth.js'

const route = useRoute()
const router = useRouter()
const dropdownOpen = ref(false)
const avatarRef = ref<HTMLElement | null>(null)

const { user, setUser } = useAuth()

const initials = computed(() => {
  if (!user.value) return ''
  const f = (user.value.first_name ?? '').charAt(0).toUpperCase()
  const l = (user.value.last_name ?? '').charAt(0).toUpperCase()
  return f + l
})

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value
}

function goTo(path: string) {
  dropdownOpen.value = false
  router.push(path)
}

function logout() {
  dropdownOpen.value = false
  setUser(null)
  router.push('/')
}

function handleOutsideClick(e: MouseEvent) {
  if (avatarRef.value && !avatarRef.value.contains(e.target as Node)) {
    dropdownOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', handleOutsideClick))
onBeforeUnmount(() => document.removeEventListener('click', handleOutsideClick))
</script>

<style>
:root {
  --bg-dark-card: #1e1e1e;
  --bg-app-dark: #121212;
  --text-main: #e0e0e0;
  --primary-accent: #2196f3;
  --border-color: #333333;
}
body { 
  margin: 0; 
  background-color: var(--bg-app-dark); 
  color: var(--text-main); 
  font-family: -apple-system, BlinkMacSystemFont, sans-serif; 
}
.view-container { max-width: 1200px; margin: 0 auto; padding: 20px; padding-bottom: 90px; }
.hero-section { text-align: center; padding: 20px 10px; }
.search-container { display: flex; gap: 10px; max-width: 700px; margin: 20px auto; background: var(--bg-dark-card); padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); }
.search-container input { flex: 2; background: transparent; border: none; color: white; padding: 10px; font-size: 14px; }
.search-container input.location-input { flex: 1; border-left: 1px solid var(--border-color); }
.search-container select { background: #2a2a2a; border: none; border-left: 1px solid var(--border-color); color: var(--text-main); padding: 10px; cursor: pointer; }
.marketplace-body { display: flex; gap: 20px; }
.filter-sidebar { width: 220px; flex-shrink: 0; background: var(--bg-dark-card); padding: 20px; border-radius: 8px; height: fit-content; }
.filter-sidebar h3 { font-size: 11px; color: #888; letter-spacing: 1px; margin: 16px 0 8px; }
.filter-sidebar label { display: block; margin: 8px 0; font-size: 14px; cursor: pointer; }
.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
.product-card { background: var(--bg-dark-card); border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); cursor: pointer; transition: border-color 0.2s; }
.product-card:hover { border-color: var(--primary-accent); }
.card-image-placeholder { background: #2a2a2a; height: 120px; display: flex; align-items: center; justify-content: center; font-size: 40px; }
.card-details { padding: 15px; }
.price { font-size: 20px; font-weight: bold; color: var(--primary-accent); }
.card-footer { display: flex; justify-content: space-between; font-size: 12px; color: #777; margin-top: 10px; }
.badge { background: #2e7d32; color: white; padding: 2px 6px; border-radius: 4px; }
.badge-cat { background: #37474f; color: #ccc; padding: 2px 6px; border-radius: 4px; font-size: 11px; }
.back-link { background: transparent; border: none; color: var(--primary-accent); cursor: pointer; margin-bottom: 20px; font-size: 14px; padding: 0; }
.detail-layout { display: flex; gap: 30px; margin-top: 20px; }
.detail-main { flex: 2; }
.detail-sidebar { flex: 1; }
.large-image-placeholder { background: #2a2a2a; height: 250px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 60px; }
.detail-price { font-size: 28px; font-weight: bold; color: var(--primary-accent); }
.seller-card { background: var(--bg-dark-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; }
.seller-avatar, .avatar-large { width: 50px; height: 50px; border-radius: 50%; background: var(--primary-accent); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; }
.avatar-large { width: 70px; height: 70px; font-size: 22px; flex-shrink: 0; }
.btn-primary, .btn-action { background: var(--primary-accent); color: white; border: none; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer; }
.btn-action { width: 100%; margin-top: 10px; }
.comparison-box { margin-top: 20px; background: var(--bg-dark-card); padding: 15px; border-radius: 8px; }
.comparison-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color); }
.comparison-row.active { color: var(--primary-accent); font-weight: bold; }
.profile-header-card { background: var(--bg-dark-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; display: flex; gap: 20px; align-items: center; }
.profile-stats { display: flex; gap: 20px; margin-top: 20px; }
.stat-box { background: var(--bg-dark-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; text-align: center; flex: 1; }
.stat-box .num { font-size: 28px; font-weight: bold; color: var(--primary-accent); }
.stat-box .label { font-size: 12px; color: #888; margin-top: 4px; }
.category-chips { display: flex; flex-wrap: wrap; gap: 8px; margin: 16px 0; }
.chip { background: #2a2a2a; border: 1px solid var(--border-color); border-radius: 20px; padding: 6px 14px; font-size: 13px; cursor: pointer; transition: all 0.2s; }
.chip:hover, .chip.active { background: var(--primary-accent); border-color: var(--primary-accent); color: white; }
.mobile-bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; height: 60px; background: #1a1a1a; border-top: 1px solid var(--border-color); display: flex; justify-content: space-around; align-items: center; z-index: 100; }
.mobile-bottom-nav button { background: transparent; border: none; color: #888; font-size: 12px; cursor: pointer; }
.mobile-bottom-nav button.active { color: var(--primary-accent); }
.feed-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.tag { font-size: 11px; color: #aaa; }

@media (max-width: 768px) {
  .filter-sidebar { display: none; }
  .search-container { flex-direction: column; }
  .search-container input.location-input, .search-container select { border-left: none; border-top: 1px solid var(--border-color); }
  .detail-layout { flex-direction: column; }
  .products-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
  .profile-stats { flex-wrap: wrap; }
}

.dashboard-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.dashboard-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:16px; }
.dashboard-card { background: var(--bg-dark-card); border: 1px solid var(--border-color); border-radius: 10px; padding: 18px; }
.dashboard-actions { display:flex; flex-wrap:wrap; gap:10px; }

/* Top header */
.top-header { position: sticky; top: 0; z-index: 200; display: flex; justify-content: space-between; align-items: center; padding: 0 20px; height: 56px; background: #1a1a1a; border-bottom: 1px solid var(--border-color); }
.top-logo { font-size: 18px; font-weight: bold; color: var(--primary-accent); cursor: pointer; letter-spacing: 0.5px; }
.top-right { display: flex; align-items: center; gap: 10px; }
.top-btn { padding: 7px 14px; font-size: 13px; }
.top-btn-outline { background: transparent; border: 1px solid var(--border-color); color: var(--text-main); padding: 7px 14px; border-radius: 6px; font-size: 13px; cursor: pointer; }
.top-btn-outline:hover { border-color: var(--primary-accent); color: var(--primary-accent); }

/* Avatar & dropdown */
.avatar-wrapper { position: relative; }
.avatar-btn { width: 38px; height: 38px; border-radius: 50%; background: var(--primary-accent); border: none; color: white; font-weight: bold; font-size: 14px; cursor: pointer; }
.avatar-dropdown { position: absolute; right: 0; top: 48px; background: #1e1e1e; border: 1px solid var(--border-color); border-radius: 10px; width: 210px; padding: 12px 0; box-shadow: 0 8px 24px rgba(0,0,0,.4); z-index: 300; }
.dropdown-name { padding: 4px 16px; font-weight: bold; font-size: 14px; }
.dropdown-role { padding: 2px 16px 8px; font-size: 12px; color: #888; }
.dropdown-divider { border: none; border-top: 1px solid var(--border-color); margin: 4px 0; }
.avatar-dropdown button { display: block; width: 100%; text-align: left; background: transparent; border: none; color: var(--text-main); padding: 10px 16px; font-size: 14px; cursor: pointer; }
.avatar-dropdown button:hover { background: #2a2a2a; }
.dropdown-logout { color: #ef5350 !important; }

.view-container { max-width: 1200px; margin: 0 auto; padding: 20px; padding-bottom: 90px; padding-top: 20px; }
.auth-shell { display: flex; justify-content: center; align-items: center; min-height: 80vh; }
.auth-card { background: var(--bg-dark-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; width: min(100%, 480px); box-shadow: 0 10px 30px rgba(0,0,0,.25); }
.auth-card h2 { margin-top: 0; }
.auth-subtitle { color: #aaa; margin-bottom: 16px; }
.auth-form { display: flex; flex-direction: column; gap: 12px; }
.auth-form input { width: 100%; box-sizing: border-box; background: #2a2a2a; border: 1px solid var(--border-color); color: white; padding: 12px; border-radius: 6px; }
.auth-row { display: flex; gap: 10px; }
.auth-row input { flex: 1; }
.password-field { position: relative; }
.password-field input { padding-right: 44px; }
.eye-btn { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #aaa; cursor: pointer; }
.role-choice { display: flex; gap: 16px; align-items: center; color: #ccc; }
.auth-submit { width: 100%; margin-top: 4px; }
.status-message { margin-top: 12px; color: var(--primary-accent); font-size: 14px; }
.field-block { display:flex; flex-direction:column; gap:4px; }
.error-text { color:#ff8a80; font-size:12px; }
.helper-text { color:#888; font-size:12px; }
.auth-footer { margin-top: 12px; font-size: 14px; color: #aaa; }
.auth-footer a, .auth-footer router-link { color: var(--primary-accent); text-decoration: none; }
</style>