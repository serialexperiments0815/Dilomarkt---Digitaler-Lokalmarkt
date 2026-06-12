const BASE = 'http://localhost:8000/api'

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
