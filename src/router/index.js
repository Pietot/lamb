import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = createRouter({
  history: createWebHistory('/fashionchic/'),
  routes: [
    {
      path: "/login",
      name: "Login",
      component: () => import("@/views/auth/LoginView.vue"),
      meta: { requiresGuest: true },
    },
    {
      path: "/",
      name: "Dashboard",
      component: () => import("@/views/dashboard/DashboardView.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    {
      path: "/stocks",
      name: "Stocks",
      component: () => import("@/views/stocks/StocksView.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    {
      path: "/orders",
      name: "Orders",
      component: () => import("@/views/orders/OrdersView.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    {
      path: "/prepare",
      name: "Prepare",
      component: () => import("@/views/prepare/PrepareView.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    {
      path: "/reception",
      name: "Reception",
      component: () => import("@/views/reception/ReceptionView.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    {
      path: "/clients",
      name: "Clients",
      component: () => import("@/views/clients/ClientsView.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    {
      path: "/agenda",
      name: "Agenda",
      component: () => import("@/views/agenda/AgendaView.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    {
      path: "/suppliers",
      name: "Suppliers",
      component: () => import("@/views/suppliers/SuppliersView.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    {
      path: "/suppliers/:id",
      name: "SupplierDetail",
      component: () => import("@/views/suppliers/SuppliersViewDetail.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    {
      path: "/admin",
      name: "Admin",
      component: () => import("@/views/admin/AdminView.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    {
      path: "/errors",
      name: "NotFound",
      component: () => import("@/views/errors/NotFound.vue"),
    },
    {
      path: "/blackjack",
      name: "Blackjack",
      component: () => import("@/views/blackjack/BlackjackView.vue"),
      meta: { layout: "main", requiresKonami: true },
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  await authStore.initAuth();
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next("/login");
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next("/");
  } else if (
    to.meta.requiresKonami &&
    localStorage.getItem("konamiUnlocked") !== "true"
  ) {
    next("/errors");
  } else if (to.matched.length === 0) {
    next("/errors");
  } else {
    next();
  }
});

export default router;
