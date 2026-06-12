<template>
  <div class="view-container" v-if="provider">
    <button class="back-link" @click="router.back()">← Zurück</button>
    <div class="profile-header-card">
      <div class="avatar-large">{{ provider.initials }}</div>
      <div>
        <h2 style="margin:0">{{ provider.name }}</h2>
        <p style="margin:4px 0;color:#aaa;font-size:14px">{{ provider.address }}, {{ provider.zip }} {{ provider.city }}</p>
        <p style="margin:0;font-size:13px;color:#888">{{ provider.type }} · seit {{ provider.since }}</p>
        <span v-if="provider.verified" style="font-size:12px;color:#4caf50">✅ Verifiziert</span>
      </div>
    </div>
    <div class="profile-stats">
      <div class="stat-box"><div class="num">{{ stats.product_count }}</div><div class="label">Aktive Angebote</div></div>
      <div class="stat-box"><div class="num">{{ stats.total_stock }}</div><div class="label">Einheiten gesamt</div></div>
      <div class="stat-box"><div class="num">{{ stats.min_price }} €</div><div class="label">Ab Preis</div></div>
    </div>
    <h3 style="margin-top:28px">Kategorien</h3>
    <div class="category-chips">
      <span :class="['chip', activeCat==='' && 'active']" @click="activeCat=''">Alle</span>
      <span v-for="c in categories" :key="c" :class="['chip', activeCat===c && 'active']" @click="activeCat=c">{{ c }}</span>
    </div>
    <h3>Angebote</h3>
    <div class="products-grid">
      <div v-for="p in filteredProducts" :key="p.id" class="product-card" @click="router.push(`/produkt/${p.id}`)">
        <div class="card-image-placeholder"><span>{{ p.icon }}</span></div>
        <div class="card-details">
          <span class="price">{{ p.price }} €</span>
          <h4 style="margin:6px 0 4px">{{ p.title }}</h4>
          <div class="card-footer">
            <span class="badge-cat">{{ p.category }}</span>
            <span style="font-size:12px;color:#888">{{ p.stock }} Stk.</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else-if="loading" class="view-container" style="color:#aaa">Lädt...</div>
  <div v-else class="view-container" style="color:#f44336">{{ error }}</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { fetchProvider } from '@/api.js'

const route = useRoute()
const router = useRouter()
const provider = ref(null)
const products = ref([])
const stats = ref({})
const activeCat = ref('')
const loading = ref(true)
const error = ref('')

const categories = computed(() => [...new Set(products.value.map(p => p.category))])
const filteredProducts = computed(() => activeCat.value ? products.value.filter(p => p.category === activeCat.value) : products.value)

onMounted(async () => {
  try {
    const data = await fetchProvider(route.params.id)
    provider.value = data.provider
    products.value = data.products
    stats.value = data.stats
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
})
</script>
