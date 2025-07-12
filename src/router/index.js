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
      meta: { requiresAuth: true, layout: "main", title: "Tableau de bord - Fashion Chic" },
    },
    {
      path: "/stocks",
      name: "Stocks",
      component: () => import("@/views/stocks/StocksView.vue"),
      meta: { requiresAuth: true, layout: "main", title: "Gestion des stocks - Fashion Chic" },
    },
    {
      path: "/orders",
      name: "Orders",
      component: () => import("@/views/orders/OrdersView.vue"),
      meta: { requiresAuth: true, layout: "main", title: "Gestion des commandes - Fashion Chic" },
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
      meta: { requiresAuth: true, layout: "main", title: "Réception des commandes - Fashion Chic" },
    },
    {
      path: "/clients",
      name: "Clients",
      component: () => import("@/views/clients/ClientsView.vue"),
      meta: { requiresAuth: true, layout: "main", title: "Gestion des clients - Fashion Chic" },
    },
    {
      path: "/agenda",
      name: "Agenda",
      component: () => import("@/views/agenda/AgendaView.vue"),
      meta: { requiresAuth: true, layout: "main", title: "Agenda - Fashion Chic" },
    },
    {
      path: "/suppliers",
      name: "Suppliers",
      component: () => import("@/views/suppliers/SuppliersView.vue"),
      meta: {
        requiresAuth: true,
        layout: "main",
        title: "Gestion des fournisseurs - Fashion Chic",
      },
    },
    {
      path: "/suppliers/:id",
      name: "SupplierDetail",
      component: () => import("@/views/suppliers/SuppliersViewDetail.vue"),
      meta: { requiresAuth: true, layout: "main", title: "Détails du fournisseur - Fashion Chic" },
    },
    {
      path: "/admin",
      name: "Admin",
      component: () => import("@/views/admin/AdminView.vue"),
      meta: { requiresAuth: true, layout: "main", title: "Administration - Fashion Chic" },
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
      meta: { layout: "main", requiresKonami: true, title: "Blackjack - Fashion Chic" },
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  await authStore.initAuth();
  document.title = to.meta.title || "Fashion Chic";
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next("/login");
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next("/");
  } else if (to.meta.requiresKonami && localStorage.getItem("konamiUnlocked") !== "true") {
    next("/errors");
  } else if (to.matched.length === 0) {
    next("/errors");
  } else {
    next();
  }
});

export default router;
