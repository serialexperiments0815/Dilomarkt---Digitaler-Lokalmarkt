<template>
  <div class="view-container auth-shell">
    <div class="auth-card">
      <h2>Passwort zurücksetzen</h2>
      <p class="auth-subtitle">Lege ein neues Passwort fest.</p>
      <form @submit.prevent="submitReset" class="auth-form">
        <input v-model="form.password" type="password" placeholder="Neues Passwort" required />
        <input v-model="form.password_confirmation" type="password" placeholder="Passwort wiederholen" required />
        <button class="btn-primary auth-submit" type="submit">Passwort ändern</button>
      </form>
      <p v-if="message" class="status-message">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { resetPassword } from '@/api.js'

const route = useRoute()
const router = useRouter()
const form = ref({ password: '', password_confirmation: '' })
const message = ref('')

async function submitReset() {
  try {
    const data = await resetPassword({ token: route.params.token, password: form.value.password, password_confirmation: form.value.password_confirmation })
    message.value = data.message
    if (data.status === 'password_reset') router.push('/login')
  } catch (e) {
    message.value = e.message
  }
}
</script>
