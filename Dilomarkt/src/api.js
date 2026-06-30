const BASE = 'http://localhost:8000/api'

// ── Seller API (requires Bearer token) ───────────────────────────────────────
function sellerHeaders() {
  return {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': `Bearer ${localStorage.getItem('dilomarkt_token') || ''}`,
  }
}

async function sellerFetch(url, method = 'GET', payload = null) {
  const opts = { method, headers: sellerHeaders() }
  if (payload) opts.body = JSON.stringify(payload)
  const res = await fetch(url, opts)
  let data
  try { data = await res.json() } catch { data = {} }
  if (!res.ok) throw new Error(data.message || 'Fehler')
  return data
}

export const getSellerShop       = ()       => sellerFetch(`${BASE}/seller/shop`)
export const saveSellerShop      = (p)      => sellerFetch(`${BASE}/seller/shop`, 'POST', p)
export const addSellerProduct    = (p)      => sellerFetch(`${BASE}/seller/products`, 'POST', p)
export const updateSellerProduct = (id, p)  => sellerFetch(`${BASE}/seller/products/${id}`, 'PUT', p)
export const deleteSellerProduct = (id)     => sellerFetch(`${BASE}/seller/products/${id}`, 'DELETE')
export const getSellerMessages   = ()       => sellerFetch(`${BASE}/seller/messages`)
// ─────────────────────────────────────────────────────────────────────────────

export async function fetchProducts(params = {}) {
  const q = new URLSearchParams(params).toString()
  const res = await fetch(`${BASE}/products?${q}`)
  if (!res.ok) throw new Error('Fehler beim Laden der Produkte')
  return res.json()
}

export async function fetchProduct(id) {
  const res = await fetch(`${BASE}/products/${id}`)
  if (!res.ok) throw new Error('Produkt nicht gefunden')
  return res.json()
}

export async function fetchProviders() {
  const res = await fetch(`${BASE}/providers`)
  if (!res.ok) throw new Error('Fehler beim Laden der Anbieter')
  return res.json()
}

export async function fetchProvider(id) {
  const res = await fetch(`${BASE}/providers/${id}`)
  if (!res.ok) throw new Error('Anbieter nicht gefunden')
  return res.json()
}

export async function fetchMessages(productId, buyerId) {
  const res = await fetch(`${BASE}/messages?product_id=${productId}&buyer_id=${buyerId}`)
  if (!res.ok) throw new Error('Fehler beim Laden der Nachrichten')
  return res.json()
}

export async function sendMessage(payload) {
  const res = await fetch(`${BASE}/messages`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
  if (!res.ok) throw new Error('Nachricht konnte nicht gesendet werden')
  return res.json()
}

const AUTH_HEADERS = { 'Content-Type': 'application/json', 'Accept': 'application/json' }

async function authFetch(url, payload) {
  const res = await fetch(url, { method: 'POST', headers: AUTH_HEADERS, body: JSON.stringify(payload) })
  let data
  try { data = await res.json() } catch { data = {} }
  if (!res.ok) throw new Error(data.message || 'Ein Fehler ist aufgetreten')
  return data
}

export const registerUser   = (p) => authFetch(`${BASE}/auth/register`, p)
export const verifyUser     = (p) => authFetch(`${BASE}/auth/verify`, p)
export const loginUser      = (p) => authFetch(`${BASE}/auth/login`, p)
export const forgotPassword = (p) => authFetch(`${BASE}/auth/forgot-password`, p)
export const resetPassword  = (p) => authFetch(`${BASE}/auth/reset-password`, p)
