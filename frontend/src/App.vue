<template>
  <div id="app">
    <header class="app-header">
      <div class="header-content">
        <div class="logo">
          <h1>slipstream</h1>
          <span class="tenant">TMS Tenant</span>
        </div>
        <nav class="main-nav">
          <router-link to="/customers" class="nav-link">Customers</router-link>
        </nav>
      </div>
    </header>
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style scoped>
.app-header {
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h1 {
  font-size: 24px;
  color: #333;
  margin-bottom: 5px;
}

.tenant {
  color: #666;
  font-size: 14px;
}

.main-nav {
  display: flex;
  gap: 20px;
}

.nav-link {
  color: #007bff;
  text-decoration: none;
  font-weight: 500;
  padding: 8px 16px;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.nav-link:hover,
.nav-link.router-link-active {
  background-color: #e7f3ff;
}

.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px 20px;
}
</style>

