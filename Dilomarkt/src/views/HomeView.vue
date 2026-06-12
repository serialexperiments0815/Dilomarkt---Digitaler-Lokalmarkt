<template>
  <div class="view-container">
    <header class="hero-section">
      <h1>Lokale Bau- und Heimwerkermärkte in deiner Nähe</h1>
      <p>Finde Materialien, Werkzeug und Restposten von regionalen Anbietern – direkt zum Abholen.</p>
      <div class="search-container">
        <input v-model="q" @keyup.enter="doSearch" placeholder="z. B. Klinker, Bohrmaschine..." />
        <input v-model="plz" class="location-input" placeholder="PLZ (z.B. 42103)" />
        <select v-model="cat">
          <option value="">Alle Kategorien</option>
          <option v-for="c in CATEGORIES" :key="c">{{ c }}</option>
        </select>
        <button class="btn-primary" @click="doSearch">Suchen</button>
      </div>
    </header>

    <div class="category-chips">
      <span v-for="c in CATEGORIES" :key="c" class="chip" @click="goCategory(c)">{{ categoryIcon(c) }} {{ c }}</span>
    </div>

    <div class="marketplace-body">
      <aside class="filter-sidebar">
        <h3>UMKREIS</h3>
        <label v-for="r in [5,10,20]" :key="r"><input type="radio" v-model="radius" :value="r" /> {{ r }} km</label>
        <h3>ANBIETERTYP</h3>
        <label><input type="checkbox" v-model="types" value="Fachhandel" /> Fachhandel</label>
        <label><input type="checkbox" v-model="types" value="Baumarkt" /> Baumarkt</label>
      </aside>
      <main style="flex:1">
        <div class="feed-header"><strong>{{ products.length }} Angebote</strong></div>
        <div v-if="loading" style="color:#aaa;padding:20px">Lädt...</div>
        <div v-if="error" style="color:#f44336;padding:20px">{{ error }}</div>
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
                <span class="badge">Abholung</span>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { fetchProducts } from '@/api.js'

const CATEGORIES = ['Baumaterial','Werkzeug','Holz & Platten','Farben & Lacke','Sanitär','Elektro']
const router = useRouter()
const q = ref('')
const plz = ref('42103')
const cat = ref('')
const radius = ref(10)
const types = ref([])
const products = ref([])
const loading = ref(false)
const error = ref('')

async function load() {
  loading.value = true
  error.value = ''
  try {
    products.value = await fetchProducts({ plz: plz.value, radius: radius.value, q: q.value, category: cat.value })
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

watch([radius, cat], load)
onMounted(load)

function doSearch() {
  router.push({ path: '/suche', query: { q: q.value, cat: cat.value, plz: plz.value } })
}
function goCategory(c) {
  router.push({ path: '/suche', query: { cat: c } })
}
function categoryIcon(c) {
  const m = { 'Baumaterial':'🧱','Werkzeug':'🔧','Holz & Platten':'🪵','Farben & Lacke':'💧','Sanitär':'🚿','Elektro':'💡' }
  return m[c] ?? '📦'
}
</script>
