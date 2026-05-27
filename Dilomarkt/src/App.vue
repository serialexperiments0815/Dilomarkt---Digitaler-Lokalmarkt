<template>
  <div class="dilomarkt-app">
    <!-- 1. LANDING & SEARCH VIEW -->
    <div v-if="currentView === 'landing'" class="view-container">
      <header class="hero-section">
        <h1>Lokale Bau- und Heimwerkermärkte in deiner Nähe</h1>
        <p>Finde Materialien, Werkzeug und Restposten von regionalen Anbietern – direkt zum Abholen.</p>
        <div class="search-container">
          <input type="text" v-model="searchQuery" placeholder="z. B. Klinker, Bohrmaschine, Holzlatten..." />
          <input type="text" v-model="locationQuery" placeholder="PLZ / Umkreis (z.B. 42103)" class="location-input" />
          <button @click="triggerSearch" class="btn-primary">Q Suchen</button>
        </div>
      </header>

      <div class="marketplace-body">
        <aside class="filter-sidebar">
          <h3>UMKREIS</h3>
          <label><input type="radio" v-model="filters.radius" value="5" /> 5 km</label>
          <label><input type="radio" v-model="filters.radius" value="10" /> 10 km</label>
          <label><input type="radio" v-model="filters.radius" value="20" /> 20 km</label>
          <h3>ANBIETERTYP</h3>
          <label><input type="checkbox" v-model="filters.providerType" value="Fachhandel" /> Fachhandel</label>
          <label><input type="checkbox" v-model="filters.providerType" value="Baumarkt" /> Baumarkt</label>
        </aside>

        <main class="product-feed">
          <div class="feed-header"><span><strong>{{ filteredProducts.length }} Angebote</strong> gefunden</span></div>
          <div class="products-grid">
            <div v-for="product in filteredProducts" :key="product.id" class="product-card" @click="openProductDetails(product)">
              <div class="card-image-placeholder"><span class="icon">{{ product.icon }}</span></div>
              <div class="card-details">
                <span class="price">{{ product.price }} €</span>
                <h4>{{ product.title }}</h4>
                <p class="provider">{{ product.provider }}</p>
                <div class="card-footer"><span>📍 {{ product.distance }} km</span><span class="badge">Abholung</span></div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>

    <!-- 2. PRODUCT DETAILS VIEW -->
    <div v-if="currentView === 'product-detail' && selectedProduct" class="view-container detail-page">
      <button class="back-link" @click="setView('landing')">← Zurück zur Startseite</button>
      <div class="detail-layout">
        <div class="detail-main">
          <div class="large-image-placeholder"><span class="large-icon">{{ selectedProduct.icon }}</span></div>
          <h2>{{ selectedProduct.title }}</h2>
          <p class="detail-price">{{ selectedProduct.price }} €</p>
          <p class="description">Ca. 200 Stück verfügbar. Geeignet für Außenwände und Gartenmauern. Lokaler Anbieter verifiziert.</p>
          <div class="comparison-box">
            <h3>Vergleich mit anderen Anbietern</h3>
            <div class="comparison-row active"><span>{{ selectedProduct.provider }}</span><strong>{{ selectedProduct.price }} €</strong></div>
            <div class="comparison-row"><span>Baustoff Werner</span><strong>104 €</strong></div>
          </div>
        </div>
        <div class="detail-sidebar">
          <div class="seller-card" @click="openProviderProfile(selectedProduct.provider)">
            <div class="seller-avatar">BM</div>
            <h3>{{ selectedProduct.provider }}</h3>
            <p>📍 {{ selectedProduct.distance }} km entfernt</p>
            <button class="btn-action" @click.stop="requestPickup">✉️ Abholung anfragen</button>
          </div>
        </div>
      </div>
    </div>

    <!-- 3. PROVIDER PROFILE VIEW -->
    <div v-if="currentView === 'provider-profile'" class="view-container profile-page">
      <button class="back-link" @click="setView('landing')">← Zurück zur Startseite</button>
      <div class="profile-header-card">
        <div class="avatar-large">BM</div>
        <div class="profile-title-info">
          <h2>{{ currentProviderName }}</h2>
          <p>Musterstraße 12, Wuppertal | Verifizierter Fachhandel seit 1998</p>
        </div>
      </div>
      <div class="profile-content-grid">
        <div class="profile-main-listing">
          <h3>Aktuelle Angebote von diesem Anbieter</h3>
          <div class="products-grid">
            <div v-for="product in providerProducts" :key="product.id" class="product-card">
              <div class="card-image-placeholder"><span class="icon">{{ product.icon }}</span></div>
              <div class="card-details">
                <span class="price">{{ product.price }} €</span>
                <h4>{{ product.title }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MOBILE BOTTOM TRUCK NAVIGATION -->
    <nav class="mobile-bottom-nav">
      <button :class="{ active: currentView === 'landing' }" @click="setView('landing')">🏠<br>Start</button>
      <button :class="{ active: currentView === 'landing' }" @click="setView('landing')">🔍<br>Suche</button>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

const currentView = ref<'landing' | 'product-detail' | 'provider-profile'>('landing');
const searchQuery = ref('');
const locationQuery = ref('42103');
const selectedProduct = ref<any>(null);
const currentProviderName = ref('');
const filters = ref({ radius: '10', providerType: [] as string[] });

const products = ref([
  { id: 1, title: 'Klinker-Restposten', price: 89, provider: 'Baumarkt Müller', distance: 2.3, icon: '🧱' },
  { id: 2, title: 'Bohrmaschine 18V', price: 64, provider: 'Profi-Werkzeug GmbH', distance: 4.1, icon: '⚙️' },
  { id: 3, title: 'Zaunlatten 180 cm', price: 3.20, provider: 'Holzfachhandel Koch', distance: 7.8, icon: '🪵' },
  { id: 4, title: 'Wandfarbe Weiß 10L', price: 22, provider: 'Farbenwelt Lange', distance: 3.5, icon: '💧' }
]);

const filteredProducts = computed(() => {
  return products.value.filter(p => {
    const matchesSearch = p.title.toLowerCase().includes(searchQuery.value.toLowerCase());
    return matchesSearch && p.distance <= parseFloat(filters.value.radius);
  });
});

const providerProducts = computed(() => products.value.filter(p => p.provider === currentProviderName.value));
function setView(view: any) { currentView.value = view; window.scrollTo({ top: 0 }); }
function openProductDetails(product: any) { selectedProduct.value = product; setView('product-detail'); }
function openProviderProfile(name: string) { currentProviderName.value = name; setView('provider-profile'); }
function triggerSearch() {}
function requestPickup() { alert('Anfrage gesendet!'); }
</script>
<style>
:root {
  --bg-dark-card: #1e1e1e;
  --bg-app-dark: #121212;
  --text-main: #e0e0e0;
  --primary-accent: #2196f3;
  --border-color: #333333;
}
body {
  margin: 0; background-color: var(--bg-app-dark); color: var(--text-main);
  font-family: -apple-system, BlinkMacSystemFont, sans-serif;
}
.view-container { max-width: 1200px; margin: 0 auto; padding: 20px; padding-bottom: 90px; }
.hero-section { text-align: center; padding: 20px 10px; }
.search-container {
  display: flex; gap: 10px; max-width: 700px; margin: 20px auto;
  background: var(--bg-dark-card); padding: 10px; border-radius: 8px; border: 1px solid var(--border-color);
}
.search-container input { flex: 2; background: transparent; border: none; color: white; padding: 10px; }
.search-container input.location-input { flex: 1; border-left: 1px solid var(--border-color); }
.marketplace-body { display: flex; gap: 20px; }
.filter-sidebar { width: 220px; flex-shrink: 0; background: var(--bg-dark-card); padding: 20px; border-radius: 8px; }
.filter-sidebar label { display: block; margin: 10px 0; }
.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
.product-card { background: var(--bg-dark-card); border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); cursor: pointer; }
.card-image-placeholder { background: #2a2a2a; height: 120px; display: flex; align-items: center; justify-content: center; font-size: 40px; }
.card-details { padding: 15px; }
.price { font-size: 20px; font-weight: bold; color: var(--primary-accent); }
.card-footer { display: flex; justify-content: space-between; font-size: 12px; color: #777; margin-top: 10px; }
.badge { background: #2e7d32; color: white; padding: 2px 6px; border-radius: 4px; }
.back-link { background: transparent; border: none; color: var(--primary-accent); cursor: pointer; margin-bottom: 20px; }
.detail-layout, .profile-content-grid { display: flex; gap: 30px; margin-top: 20px; }
.detail-main { flex: 2; }
.detail-sidebar { flex: 1; }
.large-image-placeholder { background: #2a2a2a; height: 250px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 60px; }
.detail-price { font-size: 28px; font-weight: bold; color: var(--primary-accent); }
.seller-card, .profile-header-card { background: var(--bg-dark-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; }
.btn-primary, .btn-action { background: var(--primary-accent); color: white; border: none; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer; }
.btn-action { width: 100%; margin-top: 10px; }
.comparison-box { margin-top: 20px; background: var(--bg-dark-card); padding: 15px; border-radius: 8px; }
.comparison-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color); }
.comparison-row.active { color: var(--primary-accent); font-weight: bold; }
.mobile-bottom-nav {
  position: fixed; bottom: 0; left: 0; right: 0; height: 60px; background: #1a1a1a;
  border-top: 1px solid var(--border-color); display: flex; justify-content: space-around; align-items: center;
}
.mobile-bottom-nav button { background: transparent; border: none; color: #888; font-size: 12px; cursor: pointer; }
.mobile-bottom-nav button.active { color: var(--primary-accent); }

@media (max-width: 768px) {
  .filter-sidebar { display: none; }
  .search-container { flex-direction: column; }
  .search-container input.location-input { border-left: none; border-top: 1px solid var(--border-color); }
  .detail-layout, .profile-content-grid { flex-direction: column; }
  .products-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
}
</style>
