<template>
  <div class="view-container">
    <h2>Meine Bestellungen</h2>
    <div class="orders-empty">
      <div class="orders-icon">📦</div>
      <p>Du hast noch keine Bestellungen aufgegeben.</p>
      <button class="btn-primary" @click="router.push('/suche')">Jetzt stöbern</button>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/useAuth.js'

const router = useRouter()
const { user } = useAuth()
onMounted(() => { if (!user.value) router.push('/login') })
</script>

<style scoped>
.orders-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 40vh; gap: 12px; color: #888; }
.orders-icon { font-size: 60px; }
</style>
