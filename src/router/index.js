import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = createRouter({
  history: createWebHistory("/fashionchic/"),
  routes: [
    {
      path: "/login",
      name: "Login",
      component: () => import("@/views/auth/LoginView.vue"),
      meta: { requiresGuest: true, title: "Connexion - Fashion Chic" },
    },
    {
      path: "/",
      name: "Dashboard",
      component: () => import("@/views/dashboard/DashboardView.vue"),
      meta: { 
        requiresAuth: true, 
        layout: "main", 
        title: "Tableau de bord - Fashion Chic" },
    },
    {
      path: "/stocks",
      name: "Stocks",
      component: () => import("@/views/stocks/StocksView.vue"),
      meta: { 
        requiresAuth: true, 
        layout: "main",
        roles: [1,2],
        title: "Gestion des stocks - Fashion Chic" },
    },
    {
      path: "/orders",
      name: "Orders",
      component: () => import("@/views/orders/OrdersView.vue"),
      meta: { 
        requiresAuth: true, 
        layout: "main", 
        roles: [1,2],
        title: "Gestion des commandes - Fashion Chic" },
    },
    {
      path: "/prepare",
      name: "Prepare",
      component: () => import("@/views/prepare/PrepareView.vue"),
      meta: {
        requiresAuth: true,
        layout: "main",
        title: "Préparation des commandes - Fashion Chic",
      },
    },
    {
      path: "/reception",
      name: "Reception",
      component: () => import("@/views/reception/ReceptionView.vue"),
      meta: { 
        requiresAuth: true, 
        layout: "main", 
        title: "Réception des commandes - Fashion Chic" },
    },
    {
      path: "/lot",
      name: "Lot",
      component: () => import("@/views/lot/LotView.vue"),
      meta: { 
        requiresAuth: 
        true, layout: "main", 
        roles: [1,2],
        title: "Gestion des lots  Fashion Chic" },
    },
    {
      path: "/clients",
      name: "Clients",
      component: () => import("@/views/clients/ClientsView.vue"),
      meta: { 
        requiresAuth: true, 
        layout: "main", 
        roles: [1,2],
        title: "Gestion des clients - Fashion Chic" },
    },
    {
      path: "/agenda",
      name: "Agenda",
      component: () => import("@/views/agenda/AgendaView.vue"),
      meta: { 
        requiresAuth: true, 
        layout: "main", 
        title: "Agenda - Fashion Chic" },
    },
    {
      path: "/suppliers",
      name: "Suppliers",
      component: () => import("@/views/suppliers/SuppliersView.vue"),
      meta: {
        requiresAuth: true,
        layout: "main",
        roles: [1,2],
        title: "Gestion des fournisseurs - Fashion Chic",
      },
    },
    {
      path: "/suppliers/:id",
      name: "SupplierDetail",
      component: () => import("@/views/suppliers/SuppliersViewDetail.vue"),
      meta: { 
        requiresAuth: true, 
        layout: "main", 
        roles: [1,2],
        title: "Détails du fournisseur - Fashion Chic" },
    },
    {
      path: "/admin",
      name: "Admin",
      component: () => import("@/views/admin/AdminView.vue"),
      meta: {
        requiresAuth: true,
        layout: "main",
        roles: [1],
        title: "Administration - Fashion Chic"
      },
    },
    {
      path: "/errors",
      name: "NotFound",
      component: () => import("@/views/errors/NotFound.vue"),
      meta: { title: "Page non trouvée - Fashion Chic" },
    },
    {
      path: "/blackjack",
      name: "Blackjack",
      component: () => import("@/views/blackjack/BlackjackView.vue"),
      meta: { 
        requiresAuth: true,
        layout: "main", 
        requiresKonami: true, 
        title: "Blackjack - Fashion Chic" },
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  await authStore.initAuth();
  document.title = to.meta.title || "Fashion Chic";
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return next({ name: "Login" });
  }
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    return next({ name: "Dashboard" });
  }
  if (to.meta.requiresKonami && localStorage.getItem("konamiUnlocked") !== "true") {
    return next({ name: "NotFound" });
  }
  if (to.meta.roles) {
    const userRole = authStore.user?.role;
    if (!userRole || !to.meta.roles.includes(userRole)) {
      return next({ name: "NotFound" });
    }
  }
  if (to.matched.length === 0) {
    return next({ name: "NotFound" });
  }
  next();
});


export default router;
