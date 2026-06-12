<template>
  <div class="view-container" v-if="product">
    <button class="back-link" @click="router.back()">← Zurück</button>
    <div class="detail-layout">
      <div class="detail-main">
        <div class="large-image-placeholder"><span>{{ product.icon }}</span></div>
        <div style="display:flex;gap:8px;align-items:center;margin-top:12px">
          <span class="badge-cat">{{ product.category }}</span>
          <span style="font-size:13px;color:#888">Lagerbestand: {{ product.stock }} Stück</span>
        </div>
        <h2 style="margin:10px 0 4px">{{ product.title }}</h2>
        <p class="detail-price">{{ product.price }} €</p>
        <p style="color:#aaa;line-height:1.6">{{ product.description }}</p>
        <div class="comparison-box">
          <h3 style="margin-top:0">Preisvergleich</h3>
          <div class="comparison-row active">
            <span>{{ product.provider }}</span><strong>{{ product.price }} €</strong>
          </div>
          <div class="comparison-row" v-for="a in alternatives" :key="a.id">
            <span style="cursor:pointer;color:var(--primary-accent)" @click="router.push(`/produkt/${a.id}`)">{{ a.provider }}</span>
            <strong>{{ a.price }} €</strong>
          </div>
          <div v-if="!alternatives.length" style="color:#666;font-size:13px;padding-top:8px">Keine weiteren Angebote.</div>
        </div>
      </div>
      <div class="detail-sidebar">
        <div class="seller-card" v-if="product">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
            <div class="seller-avatar">{{ product.initials }}</div>
            <div>
              <h3 style="margin:0;cursor:pointer;color:var(--primary-accent)" @click="router.push(`/anbieter/${product.provider_id}`)">{{ product.provider }}</h3>
              <p style="margin:4px 0 0;font-size:13px;color:#aaa">{{ product.provider_type }}</p>
            </div>
          </div>
          <p style="font-size:13px;color:#aaa">📦 {{ product.address }}, {{ product.city }}</p>
          <div v-if="product.verified" style="font-size:12px;color:#4caf50;margin:8px 0">✅ Verifizierter Anbieter</div>
          <button class="btn-action" @click="alert('Anfrage gesendet!')">✉️ Abholung anfragen</button>
          <button class="btn-action" style="background:#333;margin-top:8px" @click="router.push(`/anbieter/${product.provider_id}`)">Alle Angebote ansehen</button>
        </div>
      </div>
    </div>
  </div>
  <div v-else-if="loading" class="view-container" style="color:#aaa">Lädt...</div>
  <div v-else class="view-container" style="color:#f44336">{{ error || 'Produkt nicht gefunden.' }}</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { fetchProduct } from '@/api.js'

const route = useRoute()
const router = useRouter()
const product = ref(null)
const alternatives = ref([])
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const data = await fetchProduct(route.params.id)
    product.value = data.product
    alternatives.value = data.alternatives
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
})
</script>
