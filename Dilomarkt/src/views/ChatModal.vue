<template>
  <div v-if="open" class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-box">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <h3 style="margin:0">Anfrage: {{ productTitle }}</h3>
        <button @click="$emit('close')" style="background:none;border:none;color:#aaa;font-size:20px;cursor:pointer">✕</button>
      </div>

      <div class="message-list" ref="listEl">
        <div v-if="loading" style="color:#aaa;font-size:13px">Lädt...</div>
        <div v-for="m in messages" :key="m.id" :class="['msg', m.sender === 'buyer' ? 'msg-buyer' : 'msg-seller']">
          <span>{{ m.body }}</span>
          <small>{{ m.created_at }}</small>
        </div>
        <div v-if="!loading && messages.length === 0" style="color:#666;font-size:13px;text-align:center;padding:20px">
          Noch keine Nachrichten. Schreib dem Anbieter!
        </div>
      </div>

      <div v-if="error" style="color:#f44336;font-size:12px;margin-bottom:8px">{{ error }}</div>

      <div style="display:flex;gap:8px;margin-top:12px">
        <textarea v-model="draft" placeholder="Deine Nachricht..." rows="2"
          style="flex:1;background:#2a2a2a;border:1px solid #333;color:#e0e0e0;border-radius:6px;padding:8px;resize:none;font-size:14px"
          @keydown.enter.exact.prevent="send" />
        <button class="btn-primary" @click="send" :disabled="sending || !draft.trim()"
          style="align-self:flex-end;padding:10px 16px">
          {{ sending ? '...' : '➤' }}
        </button>
      </div>
      <small style="color:#555;font-size:11px">Enter zum Senden</small>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue'
import { fetchMessages, sendMessage } from '@/api.js'
import { useAuth } from '@/useAuth.js'

const props = defineProps({
  open: Boolean,
  productId: [String, Number],
  productTitle: String,
  providerId: [String, Number],
})
defineEmits(['close'])

const { user } = useAuth()
const buyerId = () => user.value?.id ?? null

const messages = ref([])
const draft = ref('')
const loading = ref(false)
const sending = ref(false)
const error = ref('')
const listEl = ref(null)

let pollInterval = null

async function loadMessages() {
  if (!buyerId()) return
  loading.value = true
  try {
    messages.value = await fetchMessages(props.productId, buyerId())
    await nextTick()
    if (listEl.value) listEl.value.scrollTop = listEl.value.scrollHeight
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

async function send() {
  if (!draft.value.trim() || sending.value) return
  sending.value = true
  error.value = ''
  try {
    await sendMessage({
      product_id: props.productId,
      provider_id: props.providerId,
      buyer_id: buyerId(),
      sender: 'buyer',
      body: draft.value.trim(),
    })
    draft.value = ''
    await loadMessages()
  } catch (e) {
    error.value = e.message
  } finally {
    sending.value = false
  }
}

watch(() => props.open, (val) => {
  if (val) {
    loadMessages()
    pollInterval = setInterval(loadMessages, 5000)
  } else {
    clearInterval(pollInterval)
    messages.value = []
    error.value = ''
  }
})
</script>

<style scoped>
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.7);
  display: flex; align-items: center; justify-content: center; z-index: 200;
}
.modal-box {
  background: #1e1e1e; border: 1px solid #333; border-radius: 10px;
  padding: 20px; width: 90%; max-width: 480px; display: flex; flex-direction: column;
}
.message-list {
  min-height: 200px; max-height: 300px; overflow-y: auto;
  display: flex; flex-direction: column; gap: 8px; padding: 4px 0;
}
.msg { display: flex; flex-direction: column; max-width: 80%; padding: 8px 12px; border-radius: 8px; font-size: 14px; }
.msg small { font-size: 10px; color: #666; margin-top: 4px; }
.msg-buyer { align-self: flex-end; background: #1565c0; }
.msg-seller { align-self: flex-start; background: #2a2a2a; border: 1px solid #333; }
</style>