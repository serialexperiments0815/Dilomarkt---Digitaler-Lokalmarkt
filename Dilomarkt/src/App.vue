<template>
  <div class="dilomarkt-app">
    <RouterView />
    
    <nav class="mobile-bottom-nav">
      <button :class="{ active: route.name === 'home' }" @click="router.push('/')">🏠<br>Start</button>
      <button :class="{ active: route.name === 'search' }" @click="router.push('/suche')">🔍<br>Suche</button>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'
const route = useRoute()
const router = useRouter()
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
  margin: 0; 
  background-color: var(--bg-app-dark); 
  color: var(--text-main); 
  font-family: -apple-system, BlinkMacSystemFont, sans-serif; 
}
.view-container { max-width: 1200px; margin: 0 auto; padding: 20px; padding-bottom: 90px; }
.hero-section { text-align: center; padding: 20px 10px; }
.search-container { display: flex; gap: 10px; max-width: 700px; margin: 20px auto; background: var(--bg-dark-card); padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); }
.search-container input { flex: 2; background: transparent; border: none; color: white; padding: 10px; font-size: 14px; }
.search-container input.location-input { flex: 1; border-left: 1px solid var(--border-color); }
.search-container select { background: #2a2a2a; border: none; border-left: 1px solid var(--border-color); color: var(--text-main); padding: 10px; cursor: pointer; }
.marketplace-body { display: flex; gap: 20px; }
.filter-sidebar { width: 220px; flex-shrink: 0; background: var(--bg-dark-card); padding: 20px; border-radius: 8px; height: fit-content; }
.filter-sidebar h3 { font-size: 11px; color: #888; letter-spacing: 1px; margin: 16px 0 8px; }
.filter-sidebar label { display: block; margin: 8px 0; font-size: 14px; cursor: pointer; }
.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
.product-card { background: var(--bg-dark-card); border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); cursor: pointer; transition: border-color 0.2s; }
.product-card:hover { border-color: var(--primary-accent); }
.card-image-placeholder { background: #2a2a2a; height: 120px; display: flex; align-items: center; justify-content: center; font-size: 40px; }
.card-details { padding: 15px; }
.price { font-size: 20px; font-weight: bold; color: var(--primary-accent); }
.card-footer { display: flex; justify-content: space-between; font-size: 12px; color: #777; margin-top: 10px; }
.badge { background: #2e7d32; color: white; padding: 2px 6px; border-radius: 4px; }
.badge-cat { background: #37474f; color: #ccc; padding: 2px 6px; border-radius: 4px; font-size: 11px; }
.back-link { background: transparent; border: none; color: var(--primary-accent); cursor: pointer; margin-bottom: 20px; font-size: 14px; padding: 0; }
.detail-layout { display: flex; gap: 30px; margin-top: 20px; }
.detail-main { flex: 2; }
.detail-sidebar { flex: 1; }
.large-image-placeholder { background: #2a2a2a; height: 250px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 60px; }
.detail-price { font-size: 28px; font-weight: bold; color: var(--primary-accent); }
.seller-card { background: var(--bg-dark-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; }
.seller-avatar, .avatar-large { width: 50px; height: 50px; border-radius: 50%; background: var(--primary-accent); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; }
.avatar-large { width: 70px; height: 70px; font-size: 22px; flex-shrink: 0; }
.btn-primary, .btn-action { background: var(--primary-accent); color: white; border: none; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer; }
.btn-action { width: 100%; margin-top: 10px; }
.comparison-box { margin-top: 20px; background: var(--bg-dark-card); padding: 15px; border-radius: 8px; }
.comparison-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color); }
.comparison-row.active { color: var(--primary-accent); font-weight: bold; }
.profile-header-card { background: var(--bg-dark-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; display: flex; gap: 20px; align-items: center; }
.profile-stats { display: flex; gap: 20px; margin-top: 20px; }
.stat-box { background: var(--bg-dark-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; text-align: center; flex: 1; }
.stat-box .num { font-size: 28px; font-weight: bold; color: var(--primary-accent); }
.stat-box .label { font-size: 12px; color: #888; margin-top: 4px; }
.category-chips { display: flex; flex-wrap: wrap; gap: 8px; margin: 16px 0; }
.chip { background: #2a2a2a; border: 1px solid var(--border-color); border-radius: 20px; padding: 6px 14px; font-size: 13px; cursor: pointer; transition: all 0.2s; }
.chip:hover, .chip.active { background: var(--primary-accent); border-color: var(--primary-accent); color: white; }
.mobile-bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; height: 60px; background: #1a1a1a; border-top: 1px solid var(--border-color); display: flex; justify-content: space-around; align-items: center; z-index: 100; }
.mobile-bottom-nav button { background: transparent; border: none; color: #888; font-size: 12px; cursor: pointer; }
.mobile-bottom-nav button.active { color: var(--primary-accent); }
.feed-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.tag { font-size: 11px; color: #aaa; }

@media (max-width: 768px) {
  .filter-sidebar { display: none; }
  .search-container { flex-direction: column; }
  .search-container input.location-input, .search-container select { border-left: none; border-top: 1px solid var(--border-color); }
  .detail-layout { flex-direction: column; }
  .products-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
  .profile-stats { flex-wrap: wrap; }
}
</style>