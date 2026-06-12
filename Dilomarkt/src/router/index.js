import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import SearchView from '@/views/SearchView.vue'
import ProductView from '@/views/ProductView.vue'
import ProviderView from '@/views/ProviderView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior: () => ({ top: 0 }),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/suche', name: 'search', component: SearchView },
    { path: '/produkt/:id', name: 'product', component: ProductView },
    { path: '/anbieter/:id', name: 'provider', component: ProviderView },
  ],
})

export default router
