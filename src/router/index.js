import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = createRouter({
  history: createWebHistory(),
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
      path: "/suppliers",
      name: "Suppliers",
      component: () => import("@/views/suppliers/SuppliersView.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    {
      path: "/admin",
      name: "Admin",
      component: () => import("@/views/admin/AdminView.vue"),
      meta: { requiresAuth: true, layout: "main" },
    },
    // {
    //   path: "/orders/:id",
    //   name: "OrderDetail",
    //   component: () => import("@/views/orders/OrderDetailView.vue"),
    //   meta: { requiresAuth: true, layout: "main" },
    // },
    // {
    //   path: "/orders/new",
    //   name: "NewOrder",
    //   component: () => import("@/views/orders/NewOrderView.vue"),
    //   meta: { requiresAuth: true, layout: "main" },
    // },
  ],
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  await authStore.initAuth();
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next("/login");
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next("/");
  } else {
    next();
  }
});

export default router;
