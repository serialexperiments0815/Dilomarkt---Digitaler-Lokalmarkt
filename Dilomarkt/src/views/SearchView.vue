<template>
  <div class="view-container">
    <div class="search-container" style="max-width:100%;margin-bottom:16px">
      <input v-model="q" @keyup.enter="load" placeholder="Suchen..." />
      <select v-model="cat">
        <option value="">Alle Kategorien</option>
        <option v-for="c in CATEGORIES" :key="c">{{ c }}</option>
      </select>
      <input v-model="plz" class="location-input" placeholder="PLZ" @change="load" @keyup.enter="load" />
      <button class="btn-primary" @click="load">Suchen</button>
    </div>

    <div class="category-chips">
      <span :class="['chip', !cat && 'active']" @click="cat=''">Alle</span>
      <span v-for="c in CATEGORIES" :key="c" :class="['chip', cat===c && 'active']" @click="cat=c">{{ c }}</span>
    </div>

    <div class="marketplace-body">
      <aside class="filter-sidebar">
        <h3>UMKREIS</h3>
        <label v-for="r in [5,10,20,50]" :key="r"><input type="radio" v-model="radius" :value="r" /> {{ r }} km</label>
        <h3>ANBIETERTYP</h3>
        <label><input type="checkbox" v-model="types" value="Fachhandel" /> Fachhandel</label>
        <label><input type="checkbox" v-model="types" value="Baumarkt" /> Baumarkt</label>
        <h3>PREIS BIS</h3>
        <input type="range" v-model="maxPrice" min="1" max="500" style="width:100%" />
        <span style="font-size:13px">max. {{ maxPrice }} €</span>
      </aside>
      <main style="flex:1">
        <div class="feed-header">
          <strong>{{ products.length }} Ergebnisse</strong>
          <span class="tag" v-if="q"> für „{{ q }}"</span>
          <span class="tag"> · {{ radius }} km um {{ plz }}</span>
        </div>
        <div v-if="loading" style="color:#aaa;padding:20px">Lädt...</div>
        <div v-if="error" style="color:#f44336;padding:20px">{{ error }}</div>
        <div v-if="!loading && products.length === 0" style="color:#888;margin-top:40px;text-align:center">Keine Ergebnisse gefunden.</div>
        <div class="products-grid">
          <div v-for="p in products" :key="p.id" class="product-card" @click="router.push(`/produkt/${p.id}`)">
            <div class="card-image-placeholder"><span>{{ p.icon }}</span></div>
            <div class="card-details">
              <span class="price">{{ p.price }} €</span>
              <h4 style="margin:6px 0 4px">{{ p.title }}</h4>
              <p style="margin:0;font-size:12px;color:#aaa">{{ p.provider }}</p>
              <div class="card-footer">
                <span>📍 {{ p.distance }} km</span>
                <span class="badge-cat">{{ p.category }}</span>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { fetchProducts } from '@/api.js'

const CATEGORIES = ['Baumaterial','Werkzeug','Holz & Platten','Farben & Lacke','Sanitär','Elektro']
const route = useRoute()
const router = useRouter()

const q        = ref(route.query.q   ?? '')
const cat      = ref(route.query.cat ?? '')
const plz      = ref(route.query.plz ?? '42103')
const radius   = ref(20)
const maxPrice = ref(500)
const types    = ref([])
const allProducts = ref([])
const loading  = ref(false)
const error    = ref('')

// All filtering is client-side — instant, no network round-trip per filter change
const products = computed(() => {
  let list = allProducts.value
  list = list.filter(p => p.distance <= Number(radius.value))
  if (cat.value)          list = list.filter(p => p.category === cat.value)
  if (types.value.length) list = list.filter(p => types.value.includes(p.provider_type))
  if (Number(maxPrice.value) < 500) list = list.filter(p => p.price <= Number(maxPrice.value))
  if (q.value.trim()) {
    const sq = q.value.trim().toLowerCase()
    list = list.filter(p =>
      p.title?.toLowerCase().includes(sq) ||
      p.provider?.toLowerCase().includes(sq)
    )
  }
  return list
})

async function load() {
  loading.value = true
  error.value = ''
  try {
    // Fetch broad dataset once; client-side computed handles the rest
    allProducts.value = await fetchProducts({ plz: plz.value, radius: 50 })
    router.replace({ query: { q: q.value, cat: cat.value, plz: plz.value } })
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

// Sync q/cat from route params (no reload — computed handles it)
watch(() => route.query, (newQ) => {
  q.value   = newQ.q   ?? ''
  cat.value = newQ.cat ?? ''
})

onMounted(load)
</script>
