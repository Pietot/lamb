<template>
  <div class="prepare-page">
    <!-- En-tête avec statistiques -->
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Commandes à préparer</h1>
        <p class="page-subtitle">File d'attente de préparation</p>
      </div>
      <div class="header-stats">
        <div class="stat-card urgent">
          <div class="stat-icon">🔥</div>
          <div class="stat-content">
            <span class="stat-value">3</span>
            <span class="stat-label">Urgentes</span>
          </div>
        </div>
        <div class="stat-card normal">
          <div class="stat-icon">📋</div>
          <div class="stat-content">
            <span class="stat-value">9</span>
            <span class="stat-label">Normales</span>
          </div>
        </div>
        <div class="stat-card progress">
          <div class="stat-icon">⏱️</div>
          <div class="stat-content">
            <span class="stat-value">{{ averageTime }}min</span>
            <span class="stat-label">Temps moyen</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtres et actions -->
    <div class="filters-section">
      <div class="filter-group">
        <select v-model="filters.priority" class="filter-select">
          <option value="">Toutes les priorités</option>
          <option value="Urgente">Urgente</option>
          <option value="Normale">Normale</option>
          <option value="Faible">Faible</option>
        </select>
        
        <select v-model="filters.status" class="filter-select">
          <option value="">Tous les statuts</option>
          <option value="En attente">En attente</option>
          <option value="En cours">En cours de préparation</option>
          <option value="Prête">Prête à expédier</option>
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
      </div>
      
      <div class="action-buttons">
        <button class="refresh-button" @click="refreshData">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="23,4 23,10 17,10"/>
            <polyline points="1,20 1,14 7,14"/>
            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
          </svg>
          Actualiser
        </button>
        <button class="bulk-action-button" :disabled="selectedOrders.length === 0">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="9,11 12,14 22,4"/>
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
          </svg>
          Marquer comme préparées ({{ selectedOrders.length }})
        </button>
      </div>
    </div>

    <!-- File d'attente de préparation -->
    <div class="queue-section">
      <div class="queue-card">
        <div class="queue-header">
          <h3 class="queue-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M9 11H5a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h4"/>
              <path d="M15 11h4a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-4"/>
              <path d="M12 4v14"/>
              <polyline points="8,8 12,4 16,8"/>
            </svg>
            File de préparation ({{ filteredOrders.length }})
          </h3>
          <div class="queue-actions">
            <button 
              class="select-all-button"
              @click="toggleSelectAll"
            >
              {{ selectedOrders.length === filteredOrders.length ? 'Tout désélectionner' : 'Tout sélectionner' }}
            </button>
          </div>
        </div>

        <!-- Liste des commandes à préparer -->
        <div class="orders-list">
          <div
            v-for="order in filteredOrders"
            :key="order.id"
            class="order-item"
            :class="[
              `priority-${order.priority.toLowerCase()}`,
              `status-${order.status.replace(' ', '-').toLowerCase()}`,
              { 'selected': selectedOrders.includes(order.id) }
            ]"
          >
            <!-- Sélection -->
            <div class="order-checkbox">
              <input
                type="checkbox"
                :value="order.id"
                v-model="selectedOrders"
                class="checkbox"
              />
            </div>

            <!-- Info commande -->
            <div class="order-info">
              <div class="order-header">
                <div class="order-main">
                  <span class="order-id">#{{ order.id }}</span>
                  <span class="order-client">{{ order.client }}</span>
                  <div class="order-badges">
                    <span class="priority-badge" :class="`priority-${order.priority.toLowerCase()}`">
                      {{ order.priority }}
                    </span>
                    <span class="status-badge" :class="getStatusClass(order.status)">
                      {{ order.status }}
                    </span>
                  </div>
                </div>
                <div class="order-meta">
                  <span class="order-date">{{ order.orderDate }}</span>
                  <span class="delivery-date">Livraison: {{ order.deliveryDate }}</span>
                  <span class="prep-time">⏱️ {{ order.estimatedTime }}min</span>
                </div>
              </div>

              <!-- Détails articles -->
              <div class="order-details">
                <div class="articles-summary">
                  <span class="articles-count">{{ order.itemsCount }} articles</span>
                  <span class="articles-total">{{ formatCurrency(order.total) }}</span>
                </div>
                <div class="articles-preview">
                  <span 
                    v-for="item in order.items.slice(0, 3)" 
                    :key="item.reference"
                    class="article-tag"
                  >
                    {{ item.reference }} ({{ item.quantity }})
                  </span>
                  <span v-if="order.items.length > 3" class="more-articles">
                    +{{ order.items.length - 3 }} autres
                  </span>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="order-actions">
              <button 
                class="action-button start-button"
                @click="startPreparation(order)"
                v-if="order.status === 'En attente'"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <polygon points="5,3 19,12 5,21"/>
                </svg>
                Commencer
              </button>
              
              <button 
                class="action-button continue-button"
                @click="continuePreparation(order)"
                v-else-if="order.status === 'En cours'"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12,6 12,12 16,14"/>
                </svg>
                Continuer
              </button>
              
              <button 
                class="action-button complete-button"
                @click="completePreparation(order)"
                v-else-if="order.status === 'En cours'"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <polyline points="9,11 12,14 22,4"/>
                  <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
                Terminer
              </button>

              <button 
                class="action-button view-button"
                @click="viewOrder(order.id)"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                Voir
              </button>
            </div>

            <!-- Barre de progression -->
            <div v-if="order.status === 'En cours'" class="progress-bar">
              <div class="progress-fill" :style="{ width: order.progress + '%' }"></div>
            </div>
          </div>

          <!-- Message si aucune commande -->
          <div v-if="filteredOrders.length === 0" class="empty-queue">
            <div class="empty-icon">📭</div>
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

    // Données des commandes à préparer
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
        progress: 0,
        items: [
          { reference: 'A1A', quantity: 3 },
          { reference: 'A2A', quantity: 2 },
          { reference: 'A5A', quantity: 1 },
          { reference: 'A8A', quantity: 2 }
        ]
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
        progress: 65,
        items: [
          { reference: 'A3A', quantity: 5 },
          { reference: 'A6A', quantity: 3 },
          { reference: 'A9A', quantity: 4 }
        ]
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
        progress: 40,
        items: [
          { reference: 'A1A', quantity: 2 },
          { reference: 'A4A', quantity: 4 }
        ]
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
        progress: 0,
        items: [
          { reference: 'A2A', quantity: 6 },
          { reference: 'A7A', quantity: 4 },
          { reference: 'A8A', quantity: 3 },
          { reference: 'A9A', quantity: 2 }
        ]
      },
      {
        id: '00008',
        client: 'Style Boutique',
        orderDate: '12 mai 2023',
        deliveryDate: '22 mai 2023',
        priority: 'Faible',
        status: 'En attente',
        estimatedTime: 30,
        itemsCount: 10,
        total: 1540.00,
        progress: 0,
        items: [
          { reference: 'A5A', quantity: 8 },
          { reference: 'A6A', quantity: 2 }
        ]
      }
    ])

    const filteredOrders = computed(() => {
      let result = orders.value

      // Filtre par priorité
      if (filters.value.priority) {
        result = result.filter(order => order.priority === filters.value.priority)
      }

      // Filtre par statut
      if (filters.value.status) {
        result = result.filter(order => order.status === filters.value.status)
      }

      // Filtre par recherche
      if (searchQuery.value) {
        result = result.filter(order => 
          order.id.includes(searchQuery.value) ||
          order.client.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
      }

      // Tri par priorité et date
      return result.sort((a, b) => {
        const priorityOrder = { 'Urgente': 0, 'Normale': 1, 'Faible': 2 }
        if (priorityOrder[a.priority] !== priorityOrder[b.priority]) {
          return priorityOrder[a.priority] - priorityOrder[b.priority]
        }
        return new Date(a.deliveryDate) - new Date(b.deliveryDate)
      })
    })

    const averageTime = computed(() => {
      const inProgress = orders.value.filter(o => o.status === 'En cours')
      if (inProgress.length === 0) return 0
      const total = inProgress.reduce((sum, o) => sum + o.estimatedTime, 0)
      return Math.round(total / inProgress.length)
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
        'En cours': 'status-progress',
        'Prête': 'status-ready'
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

    const continuePreparation = (order) => {
      router.push(`/orders/${order.id}`)
    }

    const completePreparation = (order) => {
      const index = orders.value.findIndex(o => o.id === order.id)
      if (index !== -1) {
        orders.value[index].status = 'Prête'
        orders.value[index].progress = 100
      }
    }

    const viewOrder = (orderId) => {
      router.push(`/orders/${orderId}`)
    }

    const refreshData = () => {
      // Simulation refresh
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
      continuePreparation,
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

.header-stats {
  display: flex;
  gap: 1rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1rem 1.25rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border-left: 4px solid;
  min-width: 140px;
}

.stat-card.urgent {
  border-left-color: #EF4444;
}

.stat-card.normal {
  border-left-color: #3B82F6;
}

.stat-card.progress {
  border-left-color: #F59E0B;
}

.stat-icon {
  font-size: 1.5rem;
}

.stat-value {
  font-size: 20px;
  font-weight: 700;
  color: #0F172A;
  display: block;
  line-height: 1;
}

.stat-label {
  font-size: 12px;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* FILTRES */
.filters-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  gap: 1rem;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
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

.action-buttons {
  display: flex;
  gap: 0.75rem;
}

.refresh-button,
.bulk-action-button {
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-size: 14px;
  font-weight: 500;
  color: #64748B;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.refresh-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.bulk-action-button {
  background: #059669;
  color: white;
  border-color: #059669;
}

.bulk-action-button:hover:not(:disabled) {
  background: #047857;
}

.bulk-action-button:disabled {
  background: #94A3B8;
  border-color: #94A3B8;
  cursor: not-allowed;
}

.refresh-button svg,
.bulk-action-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* FILE D'ATTENTE */
.queue-section {
  margin-bottom: 2rem;
}

.queue-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  overflow: hidden;
}

.queue-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #F1F5F9;
  background: #F8FAFC;
}

.queue-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.queue-title svg {
  width: 20px;
  height: 20px;
  color: #00B8D4;
  stroke-width: 2;
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

/* LISTE DES COMMANDES */
.orders-list {
  max-height: 800px;
  overflow-y: auto;
}

.order-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1.5rem;
  border-bottom: 1px solid #F1F5F9;
  transition: all 0.2s ease;
  position: relative;
}

.order-item:hover {
  background: #F8FAFC;
}

.order-item.selected {
  background: #F0F9FF;
  border-left: 4px solid #00B8D4;
}

.order-item.priority-urgente {
  border-left: 4px solid #EF4444;
}

.order-checkbox {
  margin-top: 0.25rem;
}

.checkbox {
  width: 18px;
  height: 18px;
  accent-color: #00B8D4;
  cursor: pointer;
}

.order-info {
  flex: 1;
  min-width: 0;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
  gap: 1rem;
}

.order-main {
  display: flex;
  align-items: center;
  gap: 1rem;
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

.order-badges {
  display: flex;
  gap: 0.5rem;
}

.priority-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.priority-badge.priority-urgente {
  background: #FEF2F2;
  color: #991B1B;
}

.priority-badge.priority-normale {
  background: #EFF6FF;
  color: #1E40AF;
}

.priority-badge.priority-faible {
  background: #F7FEE7;
  color: #365314;
}

.status-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 10px;
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

.status-ready {
  background: #D1FAE5;
  color: #047857;
}

.order-meta {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  align-items: flex-end;
  font-size: 12px;
  color: #64748B;
}

.order-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.articles-summary {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.articles-count {
  font-size: 14px;
  color: #64748B;
}

.articles-total {
  font-weight: 600;
  color: #0F172A;
  font-size: 16px;
}

.articles-preview {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.article-tag {
  background: #F1F5F9;
  color: #64748B;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 500;
}

.more-articles {
  color: #00B8D4;
  font-size: 11px;
  font-weight: 500;
}

.order-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  min-width: 120px;
}

.action-button {
  border: none;
  border-radius: 6px;
  padding: 0.5rem 0.75rem;
  font-size: 12px;
  font-weight: 500;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.25rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.start-button {
  background: #059669;
  color: white;
}

.start-button:hover {
  background: #047857;
}

.continue-button {
  background: #3B82F6;
  color: white;
}

.continue-button:hover {
  background: #2563EB;
}

.complete-button {
  background: #F59E0B;
  color: white;
}

.complete-button:hover {
  background: #D97706;
}

.view-button {
  background: #F8FAFC;
  color: #64748B;
  border: 1px solid #E2E8F0;
}

.view-button:hover {
  background: #E2E8F0;
  color: #334155;
}

.action-button svg {
  width: 14px;
  height: 14px;
  stroke-width: 2;
}

.progress-bar {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: #F1F5F9;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(to right, #00B8D4, #0891A6);
  border-radius: 0 3px 0 0;
  transition: width 0.3s ease;
}

.empty-queue {
  text-align: center;
  padding: 3rem 2rem;
  color: #64748B;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.empty-queue h3 {
  color: #334155;
  margin-bottom: 0.5rem;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .page-header {
    flex-direction: column;
    align-items: stretch;
  }
  
  .header-stats {
    justify-content: space-between;
  }
  
  .stat-card {
    min-width: auto;
    flex: 1;
  }
}

@media (max-width: 768px) {
  .filters-section {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-group {
    flex-direction: column;
  }
  
  .action-buttons {
    flex-direction: column;
  }
  
  .order-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .order-meta {
    align-items: flex-start;
  }
  
  .order-details {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .order-actions {
    flex-direction: row;
    min-width: auto;
  }
}
</style>