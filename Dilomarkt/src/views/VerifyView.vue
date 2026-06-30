<template>
  <div class="view-container auth-shell">
    <div class="auth-card">
      <h2>Account verifizieren</h2>
      <p class="auth-subtitle">Gib den 6-stelligen Code aus deiner E-Mail ein.</p>
      <form @submit.prevent="submitVerify" class="auth-form">
        <input v-model="code" maxlength="6" placeholder="6-stelliger Code" required />
        <button class="btn-primary auth-submit" type="submit">Verifizieren</button>
      </form>
      <p v-if="message" class="status-message">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { verifyUser } from '@/api.js'

const route = useRoute()
const router = useRouter()
const code = ref('')
const message = ref('')

async function submitVerify() {
  try {
    const data = await verifyUser({ email: route.query.email, code: code.value })
    message.value = data.message
    if (data.status === 'verified') {
      router.push('/login')
    }
  } catch (e) {
    message.value = e.message
  }
}
</script>
