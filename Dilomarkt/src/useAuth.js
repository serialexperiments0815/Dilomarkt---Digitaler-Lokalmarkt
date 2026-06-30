import { ref } from 'vue'

// Module-level singleton — the same ref is shared across every component that imports this
const _user = ref(JSON.parse(localStorage.getItem('dilomarkt_user') || 'null'))

export function useAuth() {
  function setUser(u) {
    _user.value = u
    if (u) {
      localStorage.setItem('dilomarkt_user', JSON.stringify(u))
    } else {
      localStorage.removeItem('dilomarkt_user')
      localStorage.removeItem('dilomarkt_token')
      localStorage.removeItem('dilomarkt_seller_shop')  // clear seller cache on logout
    }
  }

  function setToken(token) {
    if (token) localStorage.setItem('dilomarkt_token', token)
    else localStorage.removeItem('dilomarkt_token')
  }

  return { user: _user, setUser, setToken }
}
