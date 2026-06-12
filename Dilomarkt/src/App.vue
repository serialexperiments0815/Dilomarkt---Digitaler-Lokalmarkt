<template>
  <div class="dilomarkt-app">
    <RouterView />
    <nav class="mobile-bottom-nav">
      <button :class="{ active: route.name === 'home' }" @click="router.push('/')">🏠<br>Start</button>
      <button :class="{ active: route.name === 'search' }" @click="router.push('/suche')">🔍<br>Suche</button>
    </nav>
  </div>
</template>
