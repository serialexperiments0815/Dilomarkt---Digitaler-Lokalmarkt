<template>
  <div style="max-width:700px;margin:0 auto;padding:20px">
    <h2 style="margin-bottom:16px">Meine Chats</h2>
    <div v-if="conversations.length === 0" style="color:#666">Noch keine Chats.</div>
    <div v-for="c in conversations" :key="c.product_id + '-' + c.buyer_id"
         @click="active = c"
         style="background:#1e1e1e;border:1px solid #333;border-radius:8px;padding:12px;margin-bottom:8px;cursor:pointer">
      <div style="display:flex;justify-content:space-between">
        <strong>{{ c.product_title }}</strong>
        <small style="color:#666">{{ formatDate(c.last_at) }}</small>
      </div>
      <div style="color:#aaa;font-size:13px">{{ c.provider_name }}</div>
      <div style="color:#ccc;font-size:13px;margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
        {{ c.last_message }}
      </div>
    </div>

    <ChatModal v-if="active" open :product-id="active.product_id"
           :product-title="active.product_title" :provider-id="active.provider_id"
           :buyer-id="active.buyer_id" @close="active = null" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { fetchMyConversations } from '@/api.js'
import { useAuth } from '@/useAuth.js'
import ChatModal from './ChatModal.vue'

const { user } = useAuth()
const conversations = ref([])
const active = ref(null)

async function load() {
  const role = user.value?.role === 'seller' ? 'seller' : 'buyer'
  conversations.value = await fetchMyConversations(role)
}
function formatDate(d) { return d ? new Date(d).toLocaleString('de-DE') : '' }

onMounted(load)
</script>