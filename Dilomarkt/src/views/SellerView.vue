<template>
  <div class="view-container">
    <h2>Mein Shop</h2>

    <div v-if="loading" style="color:#aaa;padding:20px">Lädt...</div>
    <p v-if="pageMsg" :class="msgIsError ? 'msg-error' : 'msg-ok'">{{ pageMsg }}</p>

    <!-- ── SHOP SECTION ────────────────────────────────── -->
    <section class="seller-section">
      <div class="section-head">
        <h3>Shop-Daten</h3>
        <button v-if="shop && !showShopForm" class="btn-sm" @click="showShopForm = true">✏️ Bearbeiten</button>
      </div>

      <!-- Info card (view mode) -->
      <div v-if="shop && !showShopForm" class="shop-info-card">
        <div class="shop-initials">{{ shop.initials }}</div>
        <div>
          <strong style="font-size:17px">{{ shop.name }}</strong>
          <div class="meta">{{ shop.type }} · {{ shop.address }}, {{ shop.zip }} {{ shop.city }}</div>
          <span v-if="shop.verified" class="badge-ok">✓ Verifiziert</span>
          <span v-else class="badge-pending">⏳ Noch nicht verifiziert</span>
        </div>
      </div>

      <!-- Form (create or edit) -->
      <form v-if="(!shop && !loading) || showShopForm" @submit.prevent="submitShop" class="seller-form">
        <p v-if="!shop" style="color:#aaa;margin-bottom:16px">
          Richte deinen Shop ein, damit deine Produkte auf der Plattform erscheinen.
        </p>
        <div class="form-grid">
          <div class="field-block">
            <label>Shopname *</label>
            <input v-model="shopForm.name" placeholder="z. B. Profi-Werkzeug GmbH" required />
          </div>
          <div class="field-block">
            <label>Anbietertyp *</label>
            <select v-model="shopForm.type">
              <option>Fachhandel</option>
              <option>Baumarkt</option>
            </select>
          </div>
          <div class="field-block">
            <label>Adresse *</label>
            <input v-model="shopForm.address" placeholder="Musterstraße 12" required />
          </div>
          <div class="field-block">
            <label>Stadt *</label>
            <input v-model="shopForm.city" placeholder="Wuppertal" required />
          </div>
          <div class="field-block">
            <label>Postleitzahl *</label>
            <input v-model="shopForm.zip" placeholder="42103" required />
          </div>
        </div>
        <div class="form-actions">
          <button class="btn-primary" type="submit" :disabled="saving">
            {{ saving ? 'Speichern…' : 'Shop speichern' }}
          </button>
          <button v-if="shop" type="button" class="btn-cancel" @click="showShopForm = false">Abbrechen</button>
        </div>
      </form>
    </section>

    <!-- ── PRODUCTS SECTION ────────────────────────────── -->
    <section v-if="shop" class="seller-section">
      <div class="section-head">
        <h3>Produkte <span class="count-badge">{{ products.length }}</span></h3>
        <button v-if="!showProductForm" class="btn-primary btn-sm" @click="openAddProduct">+ Neues Produkt</button>
      </div>

      <!-- Add / Edit product form -->
      <form v-if="showProductForm" @submit.prevent="submitProduct" class="seller-form product-form">
        <h4 style="margin:0 0 16px">{{ editingProductId ? 'Produkt bearbeiten' : 'Neues Produkt' }}</h4>
        <div class="form-grid">
          <div class="field-block">
            <label>Titel *</label>
            <input v-model="productForm.title" placeholder="z. B. Bohrmaschine 18V" required />
          </div>
          <div class="field-block">
            <label>Preis (€) *</label>
            <input v-model="productForm.price" type="number" step="0.01" min="0" required />
          </div>
          <div class="field-block">
            <label>Kategorie *</label>
            <select v-model="productForm.category">
              <option v-for="c in CATEGORIES" :key="c">{{ c }}</option>
            </select>
          </div>
          <div class="field-block">
            <label>Lagerbestand *</label>
            <input v-model="productForm.stock" type="number" min="0" required />
          </div>
          <div class="field-block">
            <label>Icon (Emoji)</label>
            <input v-model="productForm.icon" placeholder="📦" maxlength="4" required />
          </div>
        </div>
        <div class="field-block" style="margin-top:12px">
          <label>Beschreibung *</label>
          <textarea v-model="productForm.description" rows="3" placeholder="Details zum Angebot…" required></textarea>
        </div>
        <div class="form-actions">
          <button class="btn-primary" type="submit" :disabled="saving">
            {{ saving ? 'Speichern…' : 'Speichern' }}
          </button>
          <button type="button" class="btn-cancel" @click="closeProductForm">Abbrechen</button>
        </div>
      </form>

      <!-- Empty state -->
      <div v-if="products.length === 0 && !showProductForm" class="empty-hint">
        Noch keine Produkte. Füge dein erstes Angebot hinzu.
      </div>

      <!-- Product rows -->
      <div v-for="p in products" :key="p.id" class="product-row">
        <span class="product-icon-cell">{{ p.icon }}</span>
        <div class="product-info">
          <strong>{{ p.title }}</strong>
          <span class="meta">{{ p.category }} · {{ p.stock }} Stk. · {{ p.price }} €</span>
        </div>
        <div class="row-actions">
          <button class="btn-sm" @click="openEditProduct(p)">✏️</button>
          <button class="btn-sm btn-danger" @click="removeProduct(p.id)">🗑️</button>
        </div>
      </div>
    </section>

    <!-- ── MESSAGES SECTION ──────────────────────────── -->
    <section v-if="shop" class="seller-section">
      <div class="section-head">
        <h3>Nachrichten <span class="count-badge">{{ conversations.length }}</span></h3>
        <button v-if="activeConv" class="btn-sm" @click="activeConv = null">← Zurück</button>
      </div>

      <!-- Conversation list -->
      <template v-if="!activeConv">
        <div v-if="conversations.length === 0" class="empty-hint">Noch keine Nachrichten von Käufern.</div>
        <div v-for="c in conversations" :key="`${c.product_id}_${c.buyer_id}`"
             class="conv-row" @click="openConversation(c)">
          <div class="conv-info">
            <strong>{{ c.buyer_name }}</strong>
            <div class="meta">{{ c.product_title }}</div>
            <div class="conv-preview">{{ c.last_message }}</div>
          </div>
          <span style="color:#555;font-size:20px">›</span>
        </div>
      </template>

      <!-- Thread view -->
      <template v-else>
        <div class="thread-header">
          <strong>{{ activeConv.buyer_name }}</strong>
          <div class="meta">{{ activeConv.product_title }}</div>
        </div>
        <div v-if="threadLoading" style="color:#aaa;padding:12px">Lädt...</div>
        <div v-else class="thread-messages" ref="threadEl">
          <div v-if="threadMessages.length === 0" class="empty-hint">Keine Nachrichten.</div>
          <div v-for="m in threadMessages" :key="m.id"
               :class="['bubble', m.sender === 'buyer' ? 'bubble-buyer' : 'bubble-seller']">
            <span>{{ m.body }}</span>
            <small>{{ new Date(m.created_at).toLocaleString('de-DE') }}</small>
          </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:12px">
          <textarea v-model="replyDraft" rows="2" placeholder="Antwort schreiben…" class="reply-input"
                    @keydown.enter.exact.prevent="sendReply" />
          <button class="btn-primary" @click="sendReply"
                  :disabled="replySending || !replyDraft.trim()"
                  style="align-self:flex-end;padding:10px 16px">
            {{ replySending ? '…' : '➤' }}
          </button>
        </div>
        <small style="color:#555;font-size:11px">Enter zum Senden</small>
      </template>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/useAuth.js'
import {
  getSellerShop, saveSellerShop,
  addSellerProduct, updateSellerProduct, deleteSellerProduct,
  getSellerMessages,
} from '@/api.js'
import { fetchMessages, sendMessage } from '@/api.js'

const CATEGORIES = ['Baumaterial', 'Werkzeug', 'Holz & Platten', 'Farben & Lacke', 'Sanitär', 'Elektro']
const CACHE_KEY = 'dilomarkt_seller_shop'

const router      = useRouter()
const { user }    = useAuth()
const loading     = ref(true)   // true by default — prevents setup-form flash
const saving      = ref(false)
const pageMsg     = ref('')
const msgIsError  = ref(false)
const shop        = ref(null)
const products    = ref([])

// Hydrate from cache synchronously before first render — eliminates flash on repeat visits
try {
  const raw = localStorage.getItem(CACHE_KEY)
  if (raw) {
    const c = JSON.parse(raw)
    shop.value     = c.shop     ?? null
    products.value = c.products ?? []
    loading.value  = false  // cached data is ready — no spinner needed
  }
} catch { localStorage.removeItem(CACHE_KEY) }

const showShopForm   = ref(false)
const shopForm       = ref({ name: '', type: 'Fachhandel', address: '', city: '', zip: '' })

const showProductForm    = ref(false)
const editingProductId   = ref(null)
const productForm        = ref({ title: '', price: '', category: 'Baumaterial', stock: 1, description: '', icon: '📦' })

// ── Messages ─────────────────────────────────────────────────────────────────
const conversations   = ref([])
const activeConv      = ref(null)
const threadMessages  = ref([])
const replyDraft      = ref('')
const threadLoading   = ref(false)
const replySending    = ref(false)
const threadEl        = ref(null)

onMounted(async () => {
  if (!user.value || user.value.role !== 'seller') { router.push('/'); return }
  await loadShop()
})

async function loadShop() {
  if (!shop.value) loading.value = true  // only spin if nothing to show yet
  try {
    const data  = await getSellerShop()
    shop.value     = data.shop
    products.value = data.products
    if (data.shop) populateShopForm(data.shop)
    // Persist to cache so next visit is instant
    localStorage.setItem(CACHE_KEY, JSON.stringify({ shop: data.shop, products: data.products }))
    if (data.shop) await loadConversations()
  } finally {
    loading.value = false
  }
}

function populateShopForm(s) {
  shopForm.value = { name: s.name, type: s.type, address: s.address, city: s.city, zip: s.zip }
}

async function submitShop() {
  saving.value = true; pageMsg.value = ''
  try {
    const data   = await saveSellerShop(shopForm.value)
    shop.value   = data.shop
    showShopForm.value = false
    showMsg('Shop gespeichert.', false)
  } catch (e) { showMsg(e.message, true) }
  finally { saving.value = false }
}

function openAddProduct() {
  editingProductId.value = null
  productForm.value = { title: '', price: '', category: 'Baumaterial', stock: 1, description: '', icon: '📦' }
  showProductForm.value = true
}

function openEditProduct(p) {
  editingProductId.value = p.id
  productForm.value = { title: p.title, price: p.price, category: p.category, stock: p.stock, description: p.description, icon: p.icon }
  showProductForm.value = true
}

function closeProductForm() {
  showProductForm.value  = false
  editingProductId.value = null
}

async function submitProduct() {
  saving.value = true; pageMsg.value = ''
  const isEdit = !!editingProductId.value
  try {
    if (isEdit) {
      const data = await updateSellerProduct(editingProductId.value, productForm.value)
      const idx  = products.value.findIndex(p => p.id === editingProductId.value)
      if (idx !== -1) products.value[idx] = data.product
    } else {
      const data = await addSellerProduct(productForm.value)
      products.value.push(data.product)
    }
    closeProductForm()
    showMsg(isEdit ? 'Produkt aktualisiert.' : 'Produkt hinzugefügt.', false)
  } catch (e) { showMsg(e.message, true) }
  finally { saving.value = false }
}

async function removeProduct(id) {
  if (!confirm('Dieses Produkt wirklich löschen?')) return
  try {
    await deleteSellerProduct(id)
    products.value = products.value.filter(p => p.id !== id)
    showMsg('Produkt gelöscht.', false)
  } catch (e) { showMsg(e.message, true) }
}

async function loadConversations() {
  try {
    const data = await getSellerMessages()
    conversations.value = data.conversations
  } catch { /* silent */ }
}

async function openConversation(c) {
  activeConv.value  = c
  replyDraft.value  = ''
  threadLoading.value = true
  try {
    threadMessages.value = await fetchMessages(c.product_id, c.buyer_id)
    await nextTick()
    if (threadEl.value) threadEl.value.scrollTop = threadEl.value.scrollHeight
  } finally {
    threadLoading.value = false
  }
}

async function sendReply() {
  if (!replyDraft.value.trim() || replySending.value) return
  replySending.value = true
  try {
    await sendMessage({
      product_id:  activeConv.value.product_id,
      provider_id: shop.value.id,
      buyer_id:    activeConv.value.buyer_id,
      sender:      'seller',
      body:        replyDraft.value.trim(),
    })
    replyDraft.value = ''
    threadMessages.value = await fetchMessages(activeConv.value.product_id, activeConv.value.buyer_id)
    await nextTick()
    if (threadEl.value) threadEl.value.scrollTop = threadEl.value.scrollHeight
  } finally {
    replySending.value = false
  }
}

function showMsg(text, isError) {
  pageMsg.value   = text
  msgIsError.value = isError
  setTimeout(() => { pageMsg.value = '' }, 4000)
}
</script>

<style scoped>
.seller-section { background: var(--bg-dark-card); border: 1px solid var(--border-color); border-radius: 10px; padding: 20px; margin-bottom: 20px; }
.section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.section-head h3 { margin: 0; font-size: 15px; letter-spacing: 0.5px; }

.shop-info-card { display: flex; gap: 16px; align-items: center; }
.shop-initials { width: 52px; height: 52px; border-radius: 50%; background: var(--primary-accent); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0; }
.meta { font-size: 13px; color: #888; margin-top: 4px; }
.badge-ok { background: #1b5e20; color: #a5d6a7; font-size: 12px; padding: 2px 10px; border-radius: 20px; }
.badge-pending { background: #333; color: #aaa; font-size: 12px; padding: 2px 10px; border-radius: 20px; }

.seller-form { margin-top: 8px; }
.form-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; }
.field-block { display: flex; flex-direction: column; gap: 6px; }
.field-block label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.6px; }
.field-block input, .field-block select, .field-block textarea {
  background: #2a2a2a; border: 1px solid var(--border-color); border-radius: 6px;
  color: var(--text-main); padding: 9px 12px; font-size: 14px;
}
.field-block textarea { resize: vertical; font-family: inherit; }
.form-actions { display: flex; gap: 10px; margin-top: 18px; }
.btn-sm { background: #2a2a2a; border: 1px solid var(--border-color); color: var(--text-main); padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 13px; }
.btn-sm:hover { border-color: var(--primary-accent); }
.btn-cancel { background: transparent; border: 1px solid var(--border-color); color: #888; padding: 10px 18px; border-radius: 6px; cursor: pointer; }
.btn-danger { border-color: #b71c1c !important; color: #ef9a9a !important; }

.product-form { background: #1a1a1a; border: 1px solid var(--border-color); border-radius: 8px; padding: 18px; margin-bottom: 16px; }
.product-row { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid var(--border-color); }
.product-row:last-child { border-bottom: none; }
.product-icon-cell { font-size: 28px; width: 36px; text-align: center; flex-shrink: 0; }
.product-info { flex: 1; display: flex; flex-direction: column; gap: 3px; }
.row-actions { display: flex; gap: 8px; }
.count-badge { background: #2a2a2a; border: 1px solid var(--border-color); border-radius: 20px; padding: 1px 10px; font-size: 12px; font-weight: normal; }
.empty-hint { color: #666; padding: 20px 0; text-align: center; }

.msg-ok    { color: #81c784; padding: 10px 14px; background: #1b5e20; border-radius: 6px; margin-bottom: 12px; }
.msg-error { color: #ef9a9a; padding: 10px 14px; background: #b71c1c33; border-radius: 6px; margin-bottom: 12px; }

.conv-row { display: flex; align-items: center; gap: 12px; padding: 12px 4px; border-bottom: 1px solid var(--border-color); cursor: pointer; }
.conv-row:last-child { border-bottom: none; }
.conv-row:hover { background: #222; border-radius: 6px; }
.conv-info { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.conv-preview { font-size: 13px; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 400px; }

.thread-header { padding: 10px 0 14px; border-bottom: 1px solid var(--border-color); margin-bottom: 12px; }
.thread-messages { display: flex; flex-direction: column; gap: 8px; max-height: 320px; overflow-y: auto; padding: 4px 0; }
.bubble { display: flex; flex-direction: column; max-width: 75%; padding: 8px 12px; border-radius: 10px; font-size: 14px; }
.bubble small { font-size: 10px; color: #666; margin-top: 4px; }
.bubble-buyer { align-self: flex-start; background: #2a2a2a; border: 1px solid var(--border-color); }
.bubble-seller { align-self: flex-end; background: #1565c0; }
.reply-input { flex: 1; background: #2a2a2a; border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-main); padding: 8px 12px; font-size: 14px; resize: none; font-family: inherit; }
</style>
