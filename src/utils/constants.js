export const ROUTES = {
  LOGIN: '/login',
  DASHBOARD: '/',
  STOCKS: '/stocks',
  ORDERS: '/orders',
  ORDERS_NEW: '/orders/new',
  ORDERS_DETAIL: '/orders/:id',
  CLIENTS: '/clients',
  SUPPLIERS: '/suppliers',
  SUPPLIERS_DETAIL: '/suppliers/:id',
  SUPPLIER_RECEPTION: '/suppliers/reception',
  ADMIN: '/admin',
  REPORTS: '/reports',
}

export const ORDER_STATUSES = {
  PENDING: 'pending',
  PREPARING: 'preparing',
  SHIPPED: 'shipped',
  DELIVERED: 'delivered',
}

export const USER_ROLES = {
  ADMIN: 'admin',
  MANAGER: 'manager',
  COMMERCIAL: 'commercial',
  SUPPORT: 'support',
}