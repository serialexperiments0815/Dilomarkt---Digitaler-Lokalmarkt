<template>
  <div class="view-container auth-shell">
    <div class="auth-card">
      <button class="back-link" @click="router.back()">← Zurück</button>
      <h2>Registrieren</h2>
      <p class="auth-subtitle">Erstelle dein Konto als Käufer oder Verkäufer.</p>

      <form @submit.prevent="submitRegister" class="auth-form">
        <div class="auth-row">
          <div class="field-block">
            <input v-model="form.first_name" placeholder="Vorname" @blur="validateField('first_name')" />
            <small v-if="errors.first_name" class="error-text">{{ errors.first_name }}</small>
          </div>
          <div class="field-block">
            <input v-model="form.last_name" placeholder="Nachname" @blur="validateField('last_name')" />
            <small v-if="errors.last_name" class="error-text">{{ errors.last_name }}</small>
          </div>
        </div>

        <div class="field-block">
          <input v-model="form.email" type="email" placeholder="E-Mail" @blur="validateField('email')" />
          <small v-if="errors.email" class="error-text">{{ errors.email }}</small>
        </div>

        <div class="field-block">
          <div class="password-field">
            <input :type="showPassword ? 'text' : 'password'" v-model="form.password" placeholder="Passwort" @blur="validateField('password')" />
            <button type="button" class="eye-btn" @click="showPassword = !showPassword">{{ showPassword ? '🙈' : '👁️' }}</button>
          </div>
          <small v-if="errors.password" class="error-text">{{ errors.password }}</small>
          <small v-else class="helper-text">Mindestens 8 Zeichen, inklusive Zahl und Sonderzeichen.</small>
        </div>

        <div class="field-block">
          <div class="password-field">
            <input :type="showConfirm ? 'text' : 'password'" v-model="form.password_confirmation" placeholder="Passwort wiederholen" @blur="validateField('password_confirmation')" />
            <button type="button" class="eye-btn" @click="showConfirm = !showConfirm">{{ showConfirm ? '🙈' : '👁️' }}</button>
          </div>
          <small v-if="errors.password_confirmation" class="error-text">{{ errors.password_confirmation }}</small>
        </div>

        <label class="role-choice">
          <input type="radio" value="buyer" v-model="form.role" /> Käufer
          <input type="radio" value="seller" v-model="form.role" /> Verkäufer
        </label>

        <button class="btn-primary auth-submit" type="submit">Jetzt registrieren</button>
      </form>

      <p v-if="message" class="status-message">{{ message }}</p>
      <p class="auth-footer">Schon ein Konto? <router-link to="/login">Anmelden</router-link></p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { registerUser } from '@/api.js'

const router = useRouter()
const form = ref({ first_name: '', last_name: '', email: '', password: '', password_confirmation: '', role: 'buyer' })
const message = ref('')
const errors = ref({})
const showPassword = ref(false)
const showConfirm = ref(false)

function validateField(field) {
  const next = { ...errors.value }
  if (field === 'first_name') {
    if (!form.value.first_name.trim()) next.first_name = 'Vorname ist erforderlich.'
    else delete next.first_name
  } else if (field === 'last_name') {
    if (!form.value.last_name.trim()) next.last_name = 'Nachname ist erforderlich.'
    else delete next.last_name
  } else if (field === 'email') {
    if (!form.value.email.trim()) next.email = 'E-Mail ist erforderlich.'
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) next.email = 'Bitte eine gültige E-Mail eingeben.'
    else delete next.email
  } else if (field === 'password') {
    if (!form.value.password) next.password = 'Passwort ist erforderlich.'
    else if (form.value.password.length < 8) next.password = 'Mindestens 8 Zeichen.'
    else if (!/\d/.test(form.value.password) || !/[^A-Za-z0-9]/.test(form.value.password)) next.password = 'Bitte Zahl und Sonderzeichen verwenden.'
    else delete next.password
  } else if (field === 'password_confirmation') {
    if (!form.value.password_confirmation) next.password_confirmation = 'Bitte Passwort wiederholen.'
    else if (form.value.password_confirmation !== form.value.password) next.password_confirmation = 'Passwörter stimmen nicht überein.'
    else delete next.password_confirmation
  }
  errors.value = next
}

function validateForm() {
  errors.value = {}  // clear any stale errors before a full run
  validateField('first_name')
  validateField('last_name')
  validateField('email')
  validateField('password')
  validateField('password_confirmation')
  return Object.keys(errors.value).length === 0
}

async function submitRegister() {
  message.value = ''
  if (!validateForm()) return
  try {
    const data = await registerUser(form.value)
    message.value = data.message
    if (data.status === 'verification_sent') {
      router.push({ path: '/verify', query: { email: form.value.email } })
    }
  } catch (e) {
    message.value = e.message
  }
}
</script>
