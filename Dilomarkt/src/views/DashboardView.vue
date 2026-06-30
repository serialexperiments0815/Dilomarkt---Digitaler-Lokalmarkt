<template>
  <div class="view-container">
    <div class="dashboard-header">
      <div>
        <h2>Willkommen zurück</h2>
        <p class="auth-subtitle">{{ userName }}</p>
      </div>
      <button class="btn-primary" @click="logout">Abmelden</button>
    </div>

    <div class="dashboard-grid">
      <section class="dashboard-card">
        <h3>Profil</h3>
        <p><strong>Vorname:</strong> {{ profile.first_name }}</p>
        <p><strong>Nachname:</strong> {{ profile.last_name }}</p>
        <p><strong>E-Mail:</strong> {{ profile.email }}</p>
        <p><strong>Rolle:</strong> {{ profile.role === 'seller' ? 'Verkäufer' : 'Käufer' }}</p>
      </section>

      <section class="dashboard-card">
        <h3>Quick Actions</h3>
        <div class="dashboard-actions">
          <router-link class="btn-primary" to="/suche">Zur Suche</router-link>
          <router-link class="btn-primary" to="/" style="background:#2a2a2a;border:1px solid var(--border-color)">Zum Start</router-link>
        </div>
      </section>

      <section class="dashboard-card">
        <h3>{{ profile.role === 'seller' ? 'Verkäuferbereich' : 'Käuferbereich' }}</h3>
        <p v-if="profile.role === 'seller'">Sie können Ihre Angebote verwalten und neue Produkte einstellen.</p>
        <p v-else>Sie können Angebote vergleichen, Anfragen senden und regionale Anbieter entdecken.</p>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const profile = computed(() => {
  const raw = localStorage.getItem('dilomarkt_user')
  return raw ? JSON.parse(raw) : { first_name: '', last_name: '', email: '', role: 'buyer' }
})
const userName = computed(() => `${profile.value.first_name} ${profile.value.last_name}`.trim() || 'Nutzer')

onMounted(() => {
  if (!localStorage.getItem('dilomarkt_user')) {
    router.push('/login')
  }
})

function logout() {
  localStorage.removeItem('dilomarkt_user')
  localStorage.removeItem('dilomarkt_token')
  router.push('/login')
}
</script>
