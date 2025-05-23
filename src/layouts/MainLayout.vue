<template>
  <div class="app-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
      <!-- Logo LAMB Solutions -->
      <div class="sidebar-header">
        <div class="logo-container">
          <div class="logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M12 2L2 7l10 5 10-5-10-5z"/>
              <path d="M2 17l10 5 10-5"/>
              <path d="M2 12l10 5 10-5"/>
            </svg>
          </div>
          <div class="logo-text">
            <h1>LAMB Solutions</h1>
          </div>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="sidebar-nav">
        <router-link
          v-for="item in menuItems"
          :key="item.name"
          :to="item.to"
          class="nav-link"
          :class="{ 'nav-link-active': $route.name === item.name }"
        >
          <div class="nav-icon">
            <component :is="item.icon" />
          </div>
          <span class="nav-label">{{ item.label }}</span>
        </router-link>
      </nav>

      <!-- User Profile -->
      <div class="sidebar-footer">
        <div class="user-profile">
          <div class="user-avatar">
            <span>{{ userInitials }}</span>
          </div>
          <div class="user-info">
            <p class="user-name">{{ authStore.user?.name }}</p>
            <p class="user-role">{{ userRoleLabel }}</p>
          </div>
          <button @click="handleLogout" class="logout-button" title="Se déconnecter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16,17 21,12 16,7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      <!-- Top Header -->
      <header class="main-header">
        <div class="header-content">
          <h1 class="page-title">{{ pageTitle }}</h1>
          <div class="header-actions">
            <button class="header-action-btn" title="Notifications">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
              </svg>
            </button>
            <button class="header-action-btn" title="Paramètres">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="3"/>
                <path d="M12 1v6m0 6v6m11-7h-6m-6 0H1"/>
              </svg>
            </button>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <div class="page-content">
        <slot />
      </div>
    </main>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Icônes SVG comme composants
const DashboardIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
      <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
    </svg>
  `
}

const StocksIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
      <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
      <line x1="12" y1="22.08" x2="12" y2="12"/>
    </svg>
  `
}

const OrdersIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <circle cx="9" cy="21" r="1"/>
      <circle cx="20" cy="21" r="1"/>
      <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
    </svg>
  `
}

const PrepareIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
      <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
      <path d="M9 14l2 2 4-4"/>
    </svg>
  `
}

const ReceptionIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <rect x="1" y="3" width="15" height="13"/>
      <polygon points="16,3 19,7 19,13 16,13"/>
      <circle cx="5.5" cy="18.5" r="2.5"/>
      <circle cx="18.5" cy="18.5" r="2.5"/>
    </svg>
  `
}

const ClientsIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
      <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
  `
}

const SuppliersIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
      <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
      <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
      <path d="M10 6h4"/>
      <path d="M10 10h4"/>
      <path d="M10 14h4"/>
      <path d="M10 18h4"/>
    </svg>
  `
}

const AdminIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
      <circle cx="12" cy="12" r="3"/>
    </svg>
  `
}

export default {
  name: 'MainLayout',
  components: {
    DashboardIcon,
    StocksIcon,
    OrdersIcon,
    PrepareIcon,
    ReceptionIcon,
    ClientsIcon,
    SuppliersIcon,
    AdminIcon
  },
  setup() {
    const router = useRouter()
    const route = useRoute()
    const authStore = useAuthStore()

    const menuItems = [
      {
        name: 'Dashboard',
        label: 'Tableau de bord',
        to: '/',
        icon: 'DashboardIcon'
      },
      {
        name: 'Stocks',
        label: 'Gestion des stocks',
        to: '/stocks',
        icon: 'StocksIcon'
      },
      {
        name: 'Orders',
        label: 'Gestion des commandes',
        to: '/orders',
        icon: 'OrdersIcon'
      },
      {
        name: 'Prepare',
        label: 'Commandes à préparer',
        to: '/prepare',
        icon: 'PrepareIcon'
      },
      {
        name: 'Reception',
        label: 'Réception fournisseur',
        to: '/reception',
        icon: 'ReceptionIcon'
      },
      {
        name: 'Clients',
        label: 'Clients',
        to: '/clients',
        icon: 'ClientsIcon'
      },
      {
        name: 'Suppliers',
        label: 'Fournisseurs',
        to: '/suppliers',
        icon: 'SuppliersIcon'
      },
      {
        name: 'Admin',
        label: 'Admin',
        to: '/admin',
        icon: 'AdminIcon'
      }
    ]

    const pageTitle = computed(() => {
      const currentItem = menuItems.find(item => item.name === route.name)
      return currentItem?.label || 'Tableau de bord'
    })

    const userInitials = computed(() => {
      const name = authStore.user?.name || 'User'
      return name.split(' ').map(n => n[0]).join('').toUpperCase()
    })

    const userRoleLabel = computed(() => {
      const role = authStore.user?.role || 'user'
      const roleLabels = {
        admin: 'Administrateur',
        manager: 'Gestionnaire',
        commercial: 'Commercial',
        support: 'Support'
      }
      return roleLabels[role] || 'Utilisateur'
    })

    const handleLogout = () => {
      authStore.logout()
      router.push('/login')
    }

    return {
      authStore,
      menuItems,
      pageTitle,
      userInitials,
      userRoleLabel,
      handleLogout
    }
  }
}
</script>

<style scoped>
.app-layout {
  display: flex;
  height: 100vh;
  background: #F8FAFC;
  font-family: 'Inter', sans-serif;
}

/* SIDEBAR */
.sidebar {
  width: 260px;
  background: #0A2540;
  display: flex;
  flex-direction: column;
  position: relative;
  z-index: 10;
}

/* HEADER SIDEBAR */
.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-icon {
  width: 40px;
  height: 40px;
  background: #00B8D4;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.logo-icon svg {
  width: 20px;
  height: 20px;
  color: white;
  stroke-width: 1.5;
}

.logo-text h1 {
  color: white;
  font-size: 18px;
  font-weight: 600;
  margin: 0;
  line-height: 1.2;
}

/* NAVIGATION */
.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
  overflow-y: auto;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 24px;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
  border-right: 3px solid transparent;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.05);
  color: rgba(255, 255, 255, 0.9);
}

.nav-link-active {
  background: rgba(0, 184, 212, 0.1);
  color: #00B8D4;
  border-right-color: #00B8D4;
}

.nav-icon {
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.nav-icon svg {
  width: 18px;
  height: 18px;
  stroke-width: 1.5;
}

.nav-label {
  flex: 1;
}

/* FOOTER SIDEBAR */
.sidebar-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  background: #00B8D4;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.user-avatar span {
  color: white;
  font-weight: 600;
  font-size: 14px;
}

.user-info {
  flex: 1;
  min-width: 0;
}

.user-name {
  color: white;
  font-size: 14px;
  font-weight: 600;
  margin: 0 0 2px 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  color: rgba(255, 255, 255, 0.6);
  font-size: 12px;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.logout-button {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.6);
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logout-button:hover {
  background: rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.9);
}

.logout-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 1.5;
}

/* MAIN CONTENT */
.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.main-header {
  background: white;
  border-bottom: 1px solid #E2E8F0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
}

.page-title {
  font-size: 20px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.header-action-btn {
  background: none;
  border: none;
  color: #64748B;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.header-action-btn:hover {
  background: #F1F5F9;
  color: #334155;
}

.header-action-btn svg {
  width: 18px;
  height: 18px;
  stroke-width: 1.5;
}

.page-content {
  flex: 1;
  overflow: auto;
  padding: 1.5rem;
  background: #F8FAFC;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .sidebar {
    width: 240px;
  }
}

@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    left: -260px;
    width: 260px;
    z-index: 50;
    transition: left 0.3s ease;
  }
  
  .main-content {
    margin-left: 0;
  }
  
  .page-content {
    padding: 1rem;
  }
}
</style>