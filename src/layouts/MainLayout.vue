<template>
  <div
    class="app-layout"
    @touchstart="handleTouchStart"
    @touchmove="handleTouchMove"
    @touchend="handleTouchEnd"
  >
    <!-- Overlay pour mobile -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="closeSidebar"></div>

    <!-- Sidebar -->
    <aside
      class="sidebar"
      :class="{ 'sidebar-open': sidebarOpen, 'sidebar-dragging': isDragging }"
      :style="`translateX(${sidebarTransform}px)`"
    >
      <!-- Logo LAMB Solutions -->
      <div class="sidebar-header">
        <div class="logo-container">
          <div class="logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M12 2L2 7l10 5 10-5-10-5z" />
              <path d="M2 17l10 5 10-5" />
              <path d="M2 12l10 5 10-5" />
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
          v-for="item in filteredMenuItems"
          :key="item.name"
          :to="item.to"
          class="nav-link"
          :class="{ 'nav-link-active': $route.name === item.name }"
          @click="closeSidebar"
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
            <p class="user-name">{{ userName }}</p>
            <p class="user-role">{{ userRoleLabel }}</p>
          </div>
          <button
            @click="handleLogout"
            class="logout-button"
            title="Se déconnecter"
            role="button"
            aria-label="Se déconnecter"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
              <polyline points="16,17 21,12 16,7"></polyline>
              <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content" :class="{ 'main-content-dimmed': sidebarOpen }">
      <!-- Top Header -->
      <header class="main-header">
        <div class="header-content">
          <h1 class="page-title">{{ pageTitle }}</h1>
          <!-- Menu Burger Icon for Mobile to display / hide sidebar -->
          <button
            class="menu-toggle"
            @click="toggleSidebar"
            title="Menu"
            aria-label="Menu"
            role="button"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="3" y1="6" x2="21" y2="6" />
              <line x1="3" y1="12" x2="21" y2="12" />
              <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
          </button>
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
  import { computed, ref } from "vue";
  import { useRouter, useRoute } from "vue-router";
  import { useAuthStore } from "@/stores/auth";
  import { ADMIN, COMMERCIAL, PREPARATEUR } from "@/constants/constants.js";

  // Icônes SVG comme composants
  const DashboardIcon = {
    template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
      <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
    </svg>
  `,
  };

  const StocksIcon = {
    template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
      <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
      <line x1="12" y1="22.08" x2="12" y2="12"/>
    </svg>
  `,
  };

  const OrdersIcon = {
    template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <circle cx="9" cy="21" r="1"/>
      <circle cx="20" cy="21" r="1"/>
      <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
    </svg>
  `,
  };

  const PrepareIcon = {
    template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
      <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
      <path d="M9 14l2 2 4-4"/>
    </svg>
  `,
  };

  const ReceptionIcon = {
    template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <rect x="1" y="3" width="15" height="13"/>
      <polygon points="16,3 19,7 19,13 16,13"/>
      <circle cx="5.5" cy="18.5" r="2.5"/>
      <circle cx="18.5" cy="18.5" r="2.5"/>
    </svg>
  `,
  };

  const ClientsIcon = {
    template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
      <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
  `,
  };

  const AgendaIcon = {
    template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
      <line x1="16" y1="2" x2="16" y2="6"/>
      <line x1="8" y1="2" x2="8" y2="6"/>
      <line x1="3" y1="10" x2="21" y2="10"/>
    </svg>
  `,
  };

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
  `,
  };

  const AdminIcon = {
    template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
      <circle cx="12" cy="12" r="3"/>
    </svg>
  `,
  };

  export default {
    name: "MainLayout",
    components: {
      DashboardIcon,
      StocksIcon,
      OrdersIcon,
      PrepareIcon,
      ReceptionIcon,
      ClientsIcon,
      AgendaIcon,
      SuppliersIcon,
      AdminIcon,
    },
    setup() {
      const router = useRouter();
      const route = useRoute();
      const authStore = useAuthStore();
      const sidebarOpen = ref(false);
      const sidebarTransform = ref(0);
      const isDragging = ref(false);
      const startX = ref(0);
      const currentX = ref(0);

      // Tous les éléments du menu avec leurs rôles autorisés
      const allMenuItems = [
        {
          name: "Dashboard",
          label: "Tableau de bord",
          to: "/",
          icon: "DashboardIcon",
          allowedRoles: [ADMIN, COMMERCIAL, PREPARATEUR],
        },
        {
          name: "Stocks",
          label: "Gestion des stocks",
          to: "/stocks",
          icon: "StocksIcon",
          allowedRoles: [ADMIN, COMMERCIAL],
        },
        {
          name: "Lot",
          label: "Gestion des lots",
          to: "/lot",
          icon: "StocksIcon",
          allowedRoles: [ADMIN, COMMERCIAL],
        },
        {
          name: "Orders",
          label: "Gestion des commandes",
          to: "/orders",
          icon: "OrdersIcon",
          allowedRoles: [ADMIN, COMMERCIAL],
        },
        {
          name: "Prepare",
          label: "Commandes à préparer",
          to: "/prepare",
          icon: "PrepareIcon",
          allowedRoles: [ADMIN, COMMERCIAL, PREPARATEUR],
        },
        {
          name: "Reception",
          label: "Réception fournisseur",
          to: "/reception",
          icon: "ReceptionIcon",
          allowedRoles: [ADMIN, COMMERCIAL, PREPARATEUR],
        },
        {
          name: "Agenda",
          label: "Agenda",
          to: "/agenda",
          icon: "AgendaIcon",
          allowedRoles: [ADMIN, COMMERCIAL, PREPARATEUR],
        },
        {
          name: "Clients",
          label: "Clients",
          to: "/clients",
          icon: "ClientsIcon",
          allowedRoles: [ADMIN, COMMERCIAL],
        },
        {
          name: "Suppliers",
          label: "Fournisseurs",
          to: "/suppliers",
          icon: "SuppliersIcon",
          allowedRoles: [ADMIN, COMMERCIAL],
        },
        {
          name: "Admin",
          label: "Admin",
          to: "/admin",
          icon: "AdminIcon",
          allowedRoles: [ADMIN],
        },
      ];

      // Filtrer les éléments du menu selon le rôle de l'utilisateur
      const filteredMenuItems = computed(() => {
        const userRole = authStore.user?.role;

        // Si pas de rôle, ne rien afficher
        if (!userRole) return [];

        return allMenuItems.filter(item => {
          // Si pas de restriction de rôle, afficher pour tous
          if (!item.allowedRoles) return true;
          // Sinon, vérifier si le rôle de l'utilisateur est autorisé
          return item.allowedRoles.includes(userRole);
        });
      });

      const pageTitle = computed(() => {
        const currentItem = allMenuItems.find(item => item.name === route.name);
        return currentItem?.label || "Tableau de bord";
      });

      const userInitials = computed(() => {
        const name = authStore.user?.username || "User";
        return name
          .split(" ")
          .map(n => n[0])
          .join("")
          .toUpperCase();
      });

      const userName = computed(() => {
        return authStore.user?.username || "Utilisateur";
      });

      const userRoleLabel = computed(() => {
        const role = authStore.user?.role || 4;
        const roleLabels = {
          1: "Administrateur",
          2: "Commercial",
          3: "Préparateur",
          4: "Utilisateur",
        };
        return roleLabels[role];
      });

      const handleLogout = () => {
        authStore.logout();
        router.push("/login");
      };

      const toggleSidebar = () => {
        sidebarOpen.value = !sidebarOpen.value;
      };

      const closeSidebar = () => {
        sidebarOpen.value = false;
        sidebarTransform.value = 0;
        isDragging.value = false;
      };

      // Gestion des gestes tactiles
      const handleTouchStart = e => {
        if (window.innerWidth > 768) return;

        startX.value = e.touches[0].clientX;
        currentX.value = startX.value;
        isDragging.value = true;
      };

      const handleTouchMove = e => {
        if (!isDragging.value || window.innerWidth > 768) return;

        currentX.value = e.touches[0].clientX;
        const deltaX = currentX.value - startX.value;

        if (sidebarOpen.value) {
          // Sidebar ouverte, on peut la fermer en swipant vers la gauche
          if (deltaX < 0) {
            const progress = Math.max(0, Math.min(1, Math.abs(deltaX) / 260));
            sidebarTransform.value = -progress * 280;
          }
        } else {
          // Sidebar fermée, on peut l'ouvrir en swipant vers la droite depuis le bord gauche
          if (startX.value < window.innerWidth / 3 && deltaX > 0) {
            const progress = Math.max(0, Math.min(1, deltaX / 260));
            sidebarTransform.value = progress * 280 - 280;
          }
        }
      };

      const handleTouchEnd = () => {
        if (!isDragging.value || window.innerWidth > 768) return;

        const deltaX = currentX.value - startX.value;
        // Seuil pour déclencher l'ouverture/fermeture
        const threshold = 30;

        if (sidebarOpen.value) {
          // Sidebar ouverte
          if (Math.abs(deltaX) > threshold && deltaX < 0) {
            // Fermer la sidebar
            closeSidebar();
          } else {
            // Revenir à l'état ouvert
            sidebarTransform.value = 0;
          }
        } else {
          // Sidebar fermée
          if (startX.value < window.innerWidth / 6 && deltaX > threshold) {
            // Ouvrir la sidebar
            sidebarOpen.value = true;
            sidebarTransform.value = 0;
          } else {
            // Revenir à l'état fermé
            sidebarTransform.value = 0;
          }
        }

        isDragging.value = false;
      };

      return {
        authStore,
        filteredMenuItems,
        pageTitle,
        userInitials,
        userName,
        userRoleLabel,
        handleLogout,
        toggleSidebar,
        closeSidebar,
        sidebarOpen,
        sidebarTransform,
        isDragging,
        handleTouchStart,
        handleTouchMove,
        handleTouchEnd,
      };
    },
  };
</script>

<style scoped>
  .app-layout {
    display: flex;
    height: 100vh;
    background: #f8fafc;
    font-family: "Inter", sans-serif;
    position: relative;
  }

  /* OVERLAY MOBILE */
  .sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 40;
    display: none;
  }

  /* SIDEBAR */
  .sidebar {
    width: 260px;
    background: #0a2540;
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
    background: #00b8d4;
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
    color: #00b8d4;
    border-right-color: #00b8d4;
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
    width: 25px;
    height: 25px;
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
    background: #00b8d4;
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
    color: white;
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
    transition: all 0.3s ease;
  }

  .main-content-dimmed {
    pointer-events: none;
    filter: brightness(0.7);
  }

  .main-header {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  }

  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    height: 64px;
  }

  .page-title {
    font-size: 20px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
  }

  .menu-toggle {
    background: none;
    border: none;
    color: #0f172a;
    cursor: pointer;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    display: none;
    transition: all 0.2s ease;
  }

  .menu-toggle:hover {
    background: rgba(15, 23, 42, 0.1);
  }

  .menu-toggle svg {
    width: 18px;
    height: 18px;
    stroke-width: 1.5;
  }

  .page-content {
    flex: 1;
    overflow: auto;
    padding: 1.5rem;
    background: #f8fafc;
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
      left: -280px;
      width: 260px;
      height: 100%;
      z-index: 50;
      transition: transform 0.3s ease;
      border-radius: 0 1rem 1rem 0;
      box-shadow: 3px 0px 15px rgba(0, 0, 0, 0.2);
    }

    .sidebar-open {
      transform: translateX(280px);
    }

    .sidebar-dragging {
      transition: none;
    }

    .sidebar-overlay {
      display: block;
    }

    .main-content {
      margin-left: 0;
      width: 100%;
    }

    .page-content {
      padding: 1rem;
    }

    .menu-toggle {
      display: flex;
    }
  }
</style>
