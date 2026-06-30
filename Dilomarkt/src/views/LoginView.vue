<template>
  <div class="view-container auth-shell">
    <div class="auth-card">
      <button class="back-link" @click="router.back()">← Zurück</button>
      <h2>Anmelden</h2>
      <p class="auth-subtitle">Melde dich mit deiner E-Mail an.</p>

      <form @submit.prevent="submitLogin" class="auth-form">
        <div class="field-block">
          <input v-model="form.email" type="email" placeholder="E-Mail" @blur="validateEmail()" />
          <small v-if="errors.email" class="error-text">{{ errors.email }}</small>
        </div>
        <div class="field-block">
          <div class="password-field">
            <input :type="showPassword ? 'text' : 'password'" v-model="form.password" placeholder="Passwort" @blur="validatePassword()" />
            <button type="button" class="eye-btn" @click="showPassword = !showPassword">{{ showPassword ? '🙈' : '👁️' }}</button>
          </div>
          <small v-if="errors.password" class="error-text">{{ errors.password }}</small>
        </div>
        <button class="btn-primary auth-submit" type="submit">Anmelden</button>
      </form>

      <p v-if="message" class="status-message">{{ message }}</p>
      <p class="auth-footer"><a href="#" @click.prevent="forgot">Passwort vergessen?</a></p>
      <p class="auth-footer">Noch kein Konto? <router-link to="/register">Registrieren</router-link></p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { forgotPassword, loginUser } from '@/api.js'
import { useAuth } from '@/useAuth.js'

const router = useRouter()
const { setUser, setToken } = useAuth()
const form = ref({ email: '', password: '' })
const message = ref('')
const errors = ref({})
const showPassword = ref(false)

function validateEmail() {
  const next = { ...errors.value }
  if (!form.value.email.trim()) next.email = 'E-Mail ist erforderlich.'
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) next.email = 'Bitte eine gültige E-Mail eingeben.'
  else delete next.email
  errors.value = next
}

function validatePassword() {
  const next = { ...errors.value }
  if (!form.value.password) next.password = 'Passwort ist erforderlich.'
  else delete next.password
  errors.value = next
}

async function submitLogin() {
  message.value = ''
  validateEmail()
  validatePassword()
  if (Object.keys(errors.value).length) return
  try {
    const data = await loginUser(form.value)
    message.value = 'Erfolgreich angemeldet.'
    setUser(data.user)
    setToken(data.token)
    router.push('/')
  } catch (e) {
    message.value = e.message
  }
}

async function forgot() {
  if (!form.value.email) {
    message.value = 'Bitte gib zuerst deine E-Mail ein.'
    return
  }
  try {
    const data = await forgotPassword({ email: form.value.email })
    message.value = data.message
    router.push({ path: '/reset-password', query: { email: form.value.email } })
  } catch (e) {
    message.value = e.message
  }
}
</script>
