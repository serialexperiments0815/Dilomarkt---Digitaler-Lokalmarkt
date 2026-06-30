import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import SearchView from '@/views/SearchView.vue'
import ProductView from '@/views/ProductView.vue'
import ProviderView from '@/views/ProviderView.vue'
import RegisterView from '@/views/RegisterView.vue'
import VerifyView from '@/views/VerifyView.vue'
import LoginView from '@/views/LoginView.vue'
import ResetPasswordView from '@/views/ResetPasswordView.vue'
import DashboardView from '@/views/DashboardView.vue'
import ProfileView from '@/views/ProfileView.vue'
import OrdersView from '@/views/OrdersView.vue'
import SellerView from '@/views/SellerView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior: () => ({ top: 0 }),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/suche', name: 'search', component: SearchView },
    { path: '/produkt/:id', name: 'product', component: ProductView },
    { path: '/anbieter/:id', name: 'provider', component: ProviderView },
    { path: '/register', name: 'register', component: RegisterView },
    { path: '/verify', name: 'verify', component: VerifyView },
    { path: '/login', name: 'login', component: LoginView },
    { path: '/reset-password/:token', name: 'reset-password', component: ResetPasswordView },
    { path: '/dashboard', name: 'dashboard', component: DashboardView },
    { path: '/profil', name: 'profile', component: ProfileView },
    { path: '/bestellungen', name: 'orders', component: OrdersView },
    { path: '/mein-shop', name: 'seller', component: SellerView },
    { path: '/chats', name: 'my-chats', component: () => import('../views/ChatOverviewView.vue') },
    
  ],
})

export default router
