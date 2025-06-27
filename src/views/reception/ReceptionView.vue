<template>
  <div class="reception-page">
    <!-- En-tête avec bouton -->
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Réception des livraisons</h1>
      </div>
      <div class="header-right">
        <button class="new-delivery-button" @click="showNewDeliveryModal = true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="16" />
            <line x1="8" y1="12" x2="16" y2="12" />
          </svg>
          <span>Enregistrer une livraison</span>
        </button>
      </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="stats-section">
      <div class="stat-card">
        <div class="stat-icon truck-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="1" y="3" width="15" height="13" />
            <polygon points="16,3 19,7 19,13 16,13" />
            <circle cx="5.5" cy="18.5" r="2.5" />
            <circle cx="18.5" cy="18.5" r="2.5" />
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Livraisons<br>à réceptionner</p>
          <p class="stat-value">3</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon check-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Réceptionnées<br>ce mois</p>
          <p class="stat-value">12</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon warning-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z" />
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Problèmes<br>signalés</p>
          <p class="stat-value">1</p>
        </div>
      </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
      <div class="filter-group">
        <div class="search-container">
          <input v-model="searchQuery" type="text" placeholder="Rechercher une livraison..." class="search-input" />
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </div>

        <select v-model="filters.supplier" class="filter-select">
          <option value="">Tous les fournisseurs</option>
          <option value="Durand SA">Durand SA</option>
          <option value="Bernard FR">Bernard FR</option>
          <option value="Martin SA">Martin SA</option>
        </select>

        <select v-model="filters.status" class="filter-select">
          <option value="">Tous les statuts</option>
          <option value="En attente">En attente</option>
          <option value="Problème">Problème</option>
          <option value="Réceptionnée">Réceptionnée</option>
        </select>
      </div>
    </div>

    <!-- Livraisons à réceptionner -->
    <div class="deliveries-section">
      <div class="section-card">
        <div class="section-header">
          <h3 class="section-title">Livraisons à réceptionner</h3>
          <div class="section-stats">
            <span class="stat-item">
              <span class="stat-label">En attente:</span>
              <span class="stat-value">{{ pendingCount }}</span>
            </span>
            <span class="stat-item">
              <span class="stat-label">Problèmes:</span>
              <span class="stat-value problem">{{ problemCount }}</span>
            </span>
          </div>
        </div>

        <div class="table-container">
          <table class="deliveries-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Fournisseur</th>
                <th>Date prévue</th>
                <th>Commande liée</th>
                <th>Nb. articles</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="delivery in filteredDeliveries" :key="delivery.id">
                <td class="delivery-id">#{{ delivery.id }}</td>
                <td>
                  <div class="supplier-info">
                    <div class="supplier-avatar">{{ getSupplierInitials(delivery.supplier) }}</div>
                    <span>{{ delivery.supplier }}</span>
                  </div>
                </td>
                <td class="delivery-date">{{ delivery.expectedDate }}</td>
                <td class="order-reference">{{ delivery.orderRef }}</td>
                <td class="articles-count">{{ delivery.articlesCount }}</td>
                <td>
                  <span class="status-badge" :class="getStatusClass(delivery.status)">
                    {{ delivery.status }}
                  </span>
                </td>
                <td class="actions">
                  <button v-if="delivery.status === 'En attente'" class="action-btn primary"
                    @click="receiveDelivery(delivery.id)">
                    Réceptionner
                  </button>
                  <button v-if="delivery.status === 'Problème'" class="action-btn danger"
                    @click="reportProblem(delivery.id)">
                    Signaler
                  </button>
                  <button class="action-btn secondary" @click="viewDetails(delivery.id)" title="Voir détails">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Historique des livraisons -->
    <div class="history-section">
      <div class="section-card">
        <div class="section-header">
          <h3 class="section-title">Historique des livraisons</h3>
          <div class="section-stats">
            <span class="stat-item">
              <span class="stat-label">Total:</span>
              <span class="stat-value">{{ deliveryHistory.length }}</span>
            </span>
          </div>
        </div>

        <div class="table-container">
          <table class="history-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Fournisseur</th>
                <th>Date réception</th>
                <th>Commande liée</th>
                <th>Réceptionnée par</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in deliveryHistory" :key="item.id">
                <td class="delivery-id">#{{ item.id }}</td>
                <td>{{ item.supplier }}</td>
                <td class="delivery-date">{{ item.receptionDate }}</td>
                <td class="order-reference">{{ item.orderRef }}</td>
                <td>{{ item.receivedBy }}</td>
                <td>
                  <span class="status-badge status-received">
                    {{ item.status }}
                  </span>
                </td>
                <td class="actions">
                  <button class="action-btn secondary" @click="viewDeliveryDetails(item.id)" title="Voir détails">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Nouvelle livraison (placeholder) -->
    <div v-if="showNewDeliveryModal" class="modal-overlay" @click="showNewDeliveryModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Enregistrer une livraison</h3>
          <button @click="showNewDeliveryModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <p>Fonctionnalité en cours de développement</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'

export default {
  name: 'ReceptionView',
  setup() {
    const searchQuery = ref('')
    const showNewDeliveryModal = ref(false)

    const filters = ref({
      supplier: '',
      status: ''
    })

    const deliveries = ref([
      {
        id: '5003',
        supplier: 'Durand SA',
        expectedDate: '6 mai 2023',
        orderRef: 'CMD-1205',
        articlesCount: 8,
        status: 'En attente'
      },
      {
        id: '5002',
        supplier: 'Bernard FR',
        expectedDate: '6 mai 2023',
        orderRef: 'CMD-1273',
        articlesCount: 5,
        status: 'En attente'
      },
      {
        id: '5001',
        supplier: 'Bernard FR',
        expectedDate: '3 mai 2023',
        orderRef: 'CMD-1254',
        articlesCount: 3,
        status: 'Problème'
      }
    ])

    const deliveryHistory = ref([
      {
        id: '4998',
        supplier: 'Martin SA',
        receptionDate: '2 mai 2023',
        orderRef: 'CMD-1201',
        receivedBy: 'Jean Dupont',
        status: 'Réceptionnée'
      },
      {
        id: '4997',
        supplier: 'Durand SA',
        receptionDate: '1 mai 2023',
        orderRef: 'CMD-1198',
        receivedBy: 'Marie Martin',
        status: 'Réceptionnée'
      },
      {
        id: '4996',
        supplier: 'Bernard FR',
        receptionDate: '29 avr 2023',
        orderRef: 'CMD-1195',
        receivedBy: 'Pierre Leroy',
        status: 'Réceptionnée'
      }
    ])

    const filteredDeliveries = computed(() => {
      let result = deliveries.value

      if (filters.value.supplier) {
        result = result.filter(delivery => delivery.supplier === filters.value.supplier)
      }

      if (filters.value.status) {
        result = result.filter(delivery => delivery.status === filters.value.status)
      }

      if (searchQuery.value) {
        result = result.filter(delivery =>
          delivery.id.includes(searchQuery.value) ||
          delivery.supplier.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          delivery.orderRef.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
      }

      return result
    })

    const pendingCount = computed(() => {
      return deliveries.value.filter(d => d.status === 'En attente').length
    })

    const problemCount = computed(() => {
      return deliveries.value.filter(d => d.status === 'Problème').length
    })

    const getSupplierInitials = (name) => {
      return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
    }

    const getStatusClass = (status) => {
      const statusClasses = {
        'En attente': 'status-pending',
        'Problème': 'status-problem',
        'Réceptionnée': 'status-received'
      }
      return statusClasses[status] || 'status-default'
    }

    const receiveDelivery = (deliveryId) => {
      console.log('Réceptionner livraison:', deliveryId)
    }

    const reportProblem = (deliveryId) => {
      console.log('Signaler problème:', deliveryId)
    }

    const viewDetails = (deliveryId) => {
      console.log('Voir détails:', deliveryId)
    }

    const viewDeliveryDetails = (deliveryId) => {
      console.log('Voir détails livraison:', deliveryId)
    }

    return {
      searchQuery,
      showNewDeliveryModal,
      filters,
      deliveries,
      deliveryHistory,
      filteredDeliveries,
      pendingCount,
      problemCount,
      getSupplierInitials,
      getStatusClass,
      receiveDelivery,
      reportProblem,
      viewDetails,
      viewDeliveryDetails
    }
  }
}
</script>

<style scoped>
.reception-page {
  padding: 0;
  font-family: 'Inter', sans-serif;
}

/* EN-TÊTE */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.new-delivery-button {
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

.new-delivery-button:hover {
  background: #0891A6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
}

.new-delivery-button svg {
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

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon svg {
  width: 24px;
  height: 24px;
}

.truck-icon {
  background: #F0FDF4;
  color: #059669;
}

.check-icon {
  background: #EFF6FF;
  color: #2563EB;
}

.warning-icon {
  background: #FFFBEB;
  color: #D97706;
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 13px;
  color: #64748B;
  line-height: 1.3;
  font-weight: 500;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
  line-height: 1;
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

/* SECTIONS */
.deliveries-section,
.history-section {
  margin-bottom: 2rem;
}

.section-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  overflow: hidden;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #E2E8F0;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.section-stats {
  display: flex;
  gap: 2rem;
}

.section-stats .stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 14px;
}

.section-stats .stat-label {
  color: #64748B;
}

.section-stats .stat-value {
  font-weight: 600;
  color: #0F172A;
}

.section-stats .stat-value.problem {
  color: #DC2626;
}

/* TABLEAUX */
.table-container {
  overflow-x: auto;
}

.deliveries-table,
.history-table {
  width: 100%;
  border-collapse: collapse;
}

.deliveries-table th,
.history-table th {
  background: #F8FAFC;
  text-align: left;
  padding: 1rem;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #E2E8F0;
}

.deliveries-table td,
.history-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
  vertical-align: middle;
}

.deliveries-table tbody tr:hover,
.history-table tbody tr:hover {
  background: #F8FAFC;
}

.deliveries-table tbody tr:last-child td,
.history-table tbody tr:last-child td {
  border-bottom: none;
}

.delivery-id {
  font-weight: 600;
  color: #0F172A;
}

.supplier-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.supplier-avatar {
  width: 32px;
  height: 32px;
  background: #3B82F6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 12px;
  flex-shrink: 0;
}

.delivery-date {
  color: #64748B;
  font-size: 13px;
}

.order-reference {
  font-weight: 500;
  color: #3B82F6;
  cursor: pointer;
}

.order-reference:hover {
  text-decoration: underline;
}

.articles-count {
  font-weight: 600;
  color: #0F172A;
  text-align: center;
}

/* BADGES DE STATUT */
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.status-pending {
  background: #FEF3C7;
  color: #92400E;
}

.status-problem {
  background: #FEF2F2;
  color: #DC2626;
}

.status-received {
  background: #D1FAE5;
  color: #047857;
}

/* ACTIONS */
.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  border: none;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn.primary {
  background: #00B8D4;
  color: white;
}

.action-btn.primary:hover {
  background: #0891A6;
}

.action-btn.danger {
  background: #DC2626;
  color: white;
}

.action-btn.danger:hover {
  background: #B91C1C;
}

.action-btn.secondary {
  background: none;
  border: 1px solid #E2E8F0;
  color: #64748B;
  padding: 6px;
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

/* MODAL */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #E2E8F0;
}

.modal-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  color: #64748B;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background: #F1F5F9;
  color: #334155;
}

.modal-close svg {
  width: 20px;
  height: 20px;
  stroke-width: 2;
}

.modal-body {
  padding: 1.5rem;
  text-align: center;
  color: #64748B;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .new-delivery-button {
    width: 100%;
    justify-content: center;
  }

  .stats-section {
    grid-template-columns: 1fr;
  }

  .filter-group {
    flex-direction: column;
  }

  .search-container,
  .filter-select {
    width: 100%;
    max-width: none;
  }
}

@media (max-width: 768px) {
  .section-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }

  .section-stats {
    width: 100%;
    justify-content: space-between;
  }

  .deliveries-table,
  .history-table {
    font-size: 12px;
  }

  .deliveries-table th,
  .deliveries-table td,
  .history-table th,
  .history-table td {
    padding: 0.75rem 0.5rem;
  }

  .actions {
    flex-direction: column;
    gap: 0.25rem;
  }

  .action-btn {
    padding: 0.375rem 0.75rem;
    font-size: 11px;
  }
}
</style>