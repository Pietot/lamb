<template>
  <div class="prepare-page">
    <!-- En-tête -->
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Commandes à préparer</h1>
        <p class="page-subtitle">File d'attente de préparation</p>
      </div>
      <div class="header-right">
        <button class="mark-prepared-button" :disabled="selectedOrders.length === 0">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="9,11 12,14 22,4"/>
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
          </svg>
          <span>Marquer comme préparées ({{ selectedOrders.length }})</span>
        </button>
      </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="stats-section">
      <div class="stat-card urgent">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value">3</span>
          <span class="stat-label">Urgentes</span>
        </div>
      </div>
      <div class="stat-card normal">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10 9 9 9 8 9"/>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value">9</span>
          <span class="stat-label">Normales</span>
        </div>
      </div>
      <div class="stat-card time">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value">{{ averageTime }}min</span>
          <span class="stat-label">Temps moyen</span>
        </div>
      </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
      <div class="filter-group">
        <select v-model="filters.priority" class="filter-select">
          <option value="">Toutes les priorités</option>
          <option value="Urgente">Urgente</option>
          <option value="Normale">Normale</option>
        </select>
        
        <select v-model="filters.status" class="filter-select">
          <option value="">Tous les statuts</option>
          <option value="En attente">En attente</option>
          <option value="En cours">En cours</option>
        </select>
        
        <div class="search-container">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Rechercher une commande..."
            class="search-input"
          />
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
        </div>

        <button class="refresh-button" @click="refreshData">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="1 4 1 10 7 10"/>
            <polyline points="23 20 23 14 17 14"/>
            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
          </svg>
          Actualiser
        </button>
      </div>
    </div>

    <!-- Liste des commandes -->
    <div class="orders-section">
      <div class="orders-card">
        <div class="orders-header">
          <h3 class="orders-title">File de préparation ({{ filteredOrders.length }})</h3>
          <button 
            class="select-all-button"
            @click="toggleSelectAll"
          >
            {{ selectedOrders.length === filteredOrders.length ? 'Tout désélectionner' : 'Tout sélectionner' }}
          </button>
        </div>

        <div class="orders-list">
          <div
            v-for="order in filteredOrders"
            :key="order.id"
            class="order-item"
            :class="{ 'selected': selectedOrders.includes(order.id) }"
          >
            <div class="order-select">
              <input
                type="checkbox"
                :value="order.id"
                v-model="selectedOrders"
                class="checkbox"
              />
            </div>

            <div class="order-content">
              <div class="order-header-info">
                <div class="order-primary">
                  <span class="order-id">#{{ order.id }}</span>
                  <span class="order-client">{{ order.client }}</span>
                  <span class="priority-badge" :class="`priority-${order.priority.toLowerCase()}`">
                    {{ order.priority }}
                  </span>
                  <span class="status-badge" :class="getStatusClass(order.status)">
                    {{ order.status }}
                  </span>
                </div>
                <div class="order-dates">
                  <span class="order-date">Commandé le {{ order.orderDate }}</span>
                  <span class="delivery-date">Livraison: {{ order.deliveryDate }}</span>
                </div>
              </div>

              <div class="order-details">
                <div class="order-stats">
                  <span class="stat-item">
                    <span class="stat-label">Articles:</span>
                    <span class="stat-value">{{ order.itemsCount }}</span>
                  </span>
                  <span class="stat-item">
                    <span class="stat-label">Total:</span>
                    <span class="stat-value">{{ formatCurrency(order.total) }}</span>
                  </span>
                  <span class="stat-item">
                    <span class="stat-label">Temps estimé:</span>
                    <span class="stat-value">{{ order.estimatedTime }}min</span>
                  </span>
                </div>
              </div>

              <div v-if="order.status === 'En cours'" class="progress-bar">
                <div class="progress-fill" :style="{ width: order.progress + '%' }"></div>
                <span class="progress-text">{{ order.progress }}%</span>
              </div>
            </div>

            <div class="order-actions">
              <button 
                v-if="order.status === 'En attente'"
                class="action-btn primary"
                @click="startPreparation(order)"
              >
                Commencer
              </button>
              
              <button 
                v-else-if="order.status === 'En cours'"
                class="action-btn success"
                @click="completePreparation(order)"
              >
                Terminer
              </button>

              <button 
                class="action-btn secondary"
                @click="viewOrder(order.id)"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
          </div>

          <div v-if="filteredOrders.length === 0" class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
              <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
              <path d="M12 11l-2 2 2 2M16 11l2 2-2 2"/>
            </svg>
            <h3>Aucune commande à préparer</h3>
            <p>Toutes les commandes sont préparées ou il n'y a pas de nouvelles commandes.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'PrepareView',
  setup() {
    const router = useRouter()
    const searchQuery = ref('')
    const selectedOrders = ref([])
    
    const filters = ref({
      priority: '',
      status: ''
    })

    const orders = ref([
      {
        id: '00012',
        client: 'Boutique Mode Paris',
        orderDate: '15 mai 2023',
        deliveryDate: '18 mai 2023',
        priority: 'Urgente',
        status: 'En attente',
        estimatedTime: 25,
        itemsCount: 8,
        total: 1850.00,
        progress: 0
      },
      {
        id: '00011',
        client: 'Fashion Store Lyon',
        orderDate: '14 mai 2023',
        deliveryDate: '19 mai 2023',
        priority: 'Normale',
        status: 'En cours',
        estimatedTime: 35,
        itemsCount: 12,
        total: 2340.00,
        progress: 65
      },
      {
        id: '00010',
        client: 'Dupont SAS',
        orderDate: '13 mai 2023',
        deliveryDate: '17 mai 2023',
        priority: 'Urgente',
        status: 'En cours',
        estimatedTime: 20,
        itemsCount: 6,
        total: 980.00,
        progress: 40
      },
      {
        id: '00009',
        client: 'Martin & Cie',
        orderDate: '12 mai 2023',
        deliveryDate: '20 mai 2023',
        priority: 'Normale',
        status: 'En attente',
        estimatedTime: 40,
        itemsCount: 15,
        total: 3200.00,
        progress: 0
      }
    ])

    const filteredOrders = computed(() => {
      let result = orders.value

      if (filters.value.priority) {
        result = result.filter(order => order.priority === filters.value.priority)
      }

      if (filters.value.status) {
        result = result.filter(order => order.status === filters.value.status)
      }

      if (searchQuery.value) {
        result = result.filter(order => 
          order.id.includes(searchQuery.value) ||
          order.client.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
      }

      return result.sort((a, b) => {
        const priorityOrder = { 'Urgente': 0, 'Normale': 1 }
        if (priorityOrder[a.priority] !== priorityOrder[b.priority]) {
          return priorityOrder[a.priority] - priorityOrder[b.priority]
        }
        return new Date(a.deliveryDate) - new Date(b.deliveryDate)
      })
    })

    const averageTime = computed(() => {
      const total = orders.value.reduce((sum, o) => sum + o.estimatedTime, 0)
      return Math.round(total / orders.value.length)
    })

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }

    const getStatusClass = (status) => {
      const statusClasses = {
        'En attente': 'status-waiting',
        'En cours': 'status-progress'
      }
      return statusClasses[status] || 'status-default'
    }

    const toggleSelectAll = () => {
      if (selectedOrders.value.length === filteredOrders.value.length) {
        selectedOrders.value = []
      } else {
        selectedOrders.value = filteredOrders.value.map(o => o.id)
      }
    }

    const startPreparation = (order) => {
      const index = orders.value.findIndex(o => o.id === order.id)
      if (index !== -1) {
        orders.value[index].status = 'En cours'
        orders.value[index].progress = 10
      }
    }

    const completePreparation = (order) => {
      const index = orders.value.findIndex(o => o.id === order.id)
      if (index !== -1) {
        orders.value.splice(index, 1)
      }
    }

    const viewOrder = (orderId) => {
      router.push(`/orders/${orderId}`)
    }

    const refreshData = () => {
      console.log('Actualisation des données...')
    }

    return {
      searchQuery,
      selectedOrders,
      filters,
      orders,
      filteredOrders,
      averageTime,
      formatCurrency,
      getStatusClass,
      toggleSelectAll,
      startPreparation,
      completePreparation,
      viewOrder,
      refreshData
    }
  }
}
</script>

<style scoped>
.prepare-page {
  padding: 0;
  font-family: 'Inter', sans-serif;
}

/* EN-TÊTE */
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 2rem;
  gap: 2rem;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
}

.page-subtitle {
  color: #64748B;
  margin: 0;
  font-size: 14px;
}

.mark-prepared-button {
  background: #00B8D4;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.75rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 8px rgba(0, 184, 212, 0.3);
  font-size: 14px;
  font-weight: 500;
}

.mark-prepared-button:hover:not(:disabled) {
  background: #0891A6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
}

.mark-prepared-button:disabled {
  background: #94A3B8;
  cursor: not-allowed;
  box-shadow: none;
}

.mark-prepared-button svg {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

/* STATISTIQUES */
.stats-section {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
}

.stat-card.urgent {
  border-left: 4px solid #EF4444;
}

.stat-card.normal {
  border-left: 4px solid #3B82F6;
}

.stat-card.time {
  border-left: 4px solid #F59E0B;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-card.urgent .stat-icon {
  background: #FEF2F2;
  color: #DC2626;
}

.stat-card.normal .stat-icon {
  background: #EFF6FF;
  color: #2563EB;
}

.stat-card.time .stat-icon {
  background: #FEF3C7;
  color: #D97706;
}

.stat-icon svg {
  width: 24px;
  height: 24px;
  stroke-width: 1.5;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #0F172A;
  display: block;
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 13px;
  color: #64748B;
  font-weight: 500;
}

/* FILTRES */
.filters-section {
  margin-bottom: 2rem;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.filter-select {
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-size: 14px;
  color: #64748B;
  min-width: 140px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #00B8D4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

.search-container {
  position: relative;
  flex: 1;
  max-width: 300px;
}

.search-input {
  width: 100%;
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  font-size: 14px;
  color: #334155;
  transition: all 0.2s ease;
}

.search-input:focus {
  outline: none;
  border-color: #00B8D4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  color: #94A3B8;
  stroke-width: 2;
  pointer-events: none;
}

.refresh-button {
  background: #3B82F6;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.75rem 1.5rem;
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.refresh-button:hover {
  background: #2563EB;
  transform: translateY(-1px);
}

.refresh-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* LISTE DES COMMANDES */
.orders-section {
  margin-bottom: 2rem;
}

.orders-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  overflow: hidden;
}

.orders-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #E2E8F0;
}

.orders-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.select-all-button {
  background: none;
  border: 1px solid #E2E8F0;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  font-size: 12px;
  font-weight: 500;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s ease;
}

.select-all-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.orders-list {
  max-height: 600px;
  overflow-y: auto;
}

.order-item {
  display: flex;
  align-items: stretch;
  gap: 1rem;
  padding: 1.5rem;
  border-bottom: 1px solid #F1F5F9;
  transition: all 0.2s ease;
}

.order-item:hover {
  background: #F8FAFC;
}

.order-item.selected {
  background: #F0F9FF;
}

.order-item:last-child {
  border-bottom: none;
}

.order-select {
  display: flex;
  align-items: center;
}

.checkbox {
  width: 18px;
  height: 18px;
  accent-color: #00B8D4;
  cursor: pointer;
}

.order-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.order-header-info {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.order-primary {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.order-id {
  font-weight: 700;
  color: #0F172A;
  font-size: 16px;
}

.order-client {
  font-weight: 500;
  color: #334155;
}

.priority-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.priority-urgente {
  background: #FEF2F2;
  color: #991B1B;
}

.priority-normale {
  background: #EFF6FF;
  color: #1E40AF;
}

.status-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 500;
}

.status-waiting {
  background: #FEF3C7;
  color: #92400E;
}

.status-progress {
  background: #DBEAFE;
  color: #1E40AF;
}

.order-dates {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  font-size: 12px;
  color: #64748B;
  text-align: right;
}

.order-stats {
  display: flex;
  gap: 2rem;
}

.order-stats .stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 14px;
}

.order-stats .stat-label {
  color: #64748B;
}

.order-stats .stat-value {
  font-weight: 600;
  color: #0F172A;
}

.progress-bar {
  position: relative;
  height: 8px;
  background: #F1F5F9;
  border-radius: 4px;
  overflow: hidden;
  margin-top: 0.5rem;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(to right, #00B8D4, #0891A6);
  border-radius: 4px;
  transition: width 0.3s ease;
}

.progress-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 10px;
  font-weight: 600;
  color: white;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.order-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  justify-content: center;
}

.action-btn {
  border: none;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  min-width: 100px;
}

.action-btn.primary {
  background: #00B8D4;
  color: white;
}

.action-btn.primary:hover {
  background: #0891A6;
}

.action-btn.success {
  background: #059669;
  color: white;
}

.action-btn.success:hover {
  background: #047857;
}

.action-btn.secondary {
  background: none;
  border: 1px solid #E2E8F0;
  color: #64748B;
  padding: 6px;
  min-width: auto;
}

.action-btn.secondary:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.action-btn.secondary svg {
  width: 16px;
  height: 16px;
  stroke-width: 1.5;
}

.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  color: #64748B;
}

.empty-state svg {
  width: 48px;
  height: 48px;
  margin-bottom: 1rem;
  stroke-width: 1.5;
}

.empty-state h3 {
  color: #334155;
  margin-bottom: 0.5rem;
  font-size: 18px;
}

.empty-state p {
  margin: 0;
  font-size: 14px;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .page-header {
    flex-direction: column;
    align-items: stretch;
  }
  
  .stats-section {
    grid-template-columns: 1fr;
  }
  
  .filter-group {
    flex-direction: column;
  }
  
  .filter-select,
  .search-container,
  .refresh-button {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .order-header-info {
    flex-direction: column;
  }
  
  .order-dates {
    text-align: left;
  }
  
  .order-stats {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .order-actions {
    flex-direction: row;
  }
  
  .action-btn {
    padding: 0.375rem 0.75rem;
    font-size: 11px;
  }
}
</style>