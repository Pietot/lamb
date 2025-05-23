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
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
          </svg>
          Enregistrer une livraison
        </button>
      </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="stats-section">
      <div class="stat-card stat-pending">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="1" y="3" width="15" height="13"/>
            <polygon points="16,3 19,7 19,13 16,13"/>
            <circle cx="5.5" cy="18.5" r="2.5"/>
            <circle cx="18.5" cy="18.5" r="2.5"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3 class="stat-title">Livraisons à réceptionner</h3>
          <p class="stat-value">3</p>
        </div>
      </div>
      
      <div class="stat-card stat-success">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <polyline points="20,6 9,17 4,12"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3 class="stat-title">Réceptionnées ce mois</h3>
          <p class="stat-value">12</p>
        </div>
      </div>
      
      <div class="stat-card stat-warning">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3 class="stat-title">Problèmes signalés</h3>
          <p class="stat-value">1</p>
        </div>
      </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
      <div class="filters-group">
        <div class="search-container">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher une livraison..."
            class="search-input"
          />
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
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
        <h3 class="section-title">Livraisons à réceptionner</h3>
        
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
                <td class="supplier-name">
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
                <td class="actions-cell">
                  <div class="action-buttons">
                    <button 
                      v-if="delivery.status === 'En attente'"
                      class="action-button receive-button"
                      @click="receiveDelivery(delivery.id)"
                      title="Réceptionner"
                    >
                      Réceptionner
                    </button>
                    <button 
                      v-if="delivery.status === 'Problème'"
                      class="action-button report-button"
                      @click="reportProblem(delivery.id)"
                      title="Signaler"
                    >
                      Signaler
                    </button>
                    <button 
                      v-if="delivery.status === 'Problème'"
                      class="action-button details-button"
                      @click="viewDetails(delivery.id)"
                      title="Détails"
                    >
                      Détails
                    </button>
                  </div>
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
        <h3 class="section-title">Historique des livraisons</h3>
        
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
                <td class="supplier-name">{{ item.supplier }}</td>
                <td class="delivery-date">{{ item.receptionDate }}</td>
                <td class="order-reference">{{ item.orderRef }}</td>
                <td class="received-by">{{ item.receivedBy }}</td>
                <td>
                  <span class="status-badge" :class="getStatusClass(item.status)">
                    {{ item.status }}
                  </span>
                </td>
                <td class="actions-cell">
                  <button 
                    class="action-button view-button"
                    @click="viewDeliveryDetails(item.id)"
                    title="Voir détails"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
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
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
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

    // Données des livraisons selon la maquette
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

    // Historique des livraisons
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

      // Filtre par fournisseur
      if (filters.value.supplier) {
        result = result.filter(delivery => delivery.supplier === filters.value.supplier)
      }

      // Filtre par statut
      if (filters.value.status) {
        result = result.filter(delivery => delivery.status === filters.value.status)
      }

      // Filtre par recherche
      if (searchQuery.value) {
        result = result.filter(delivery =>
          delivery.id.includes(searchQuery.value) ||
          delivery.supplier.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          delivery.orderRef.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
      }

      return result
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
      // Logique pour réceptionner une livraison
    }

    const reportProblem = (deliveryId) => {
      console.log('Signaler problème:', deliveryId)
      // Logique pour signaler un problème
    }

    const viewDetails = (deliveryId) => {
      console.log('Voir détails:', deliveryId)
      // Logique pour voir les détails
    }

    const viewDeliveryDetails = (deliveryId) => {
      console.log('Voir détails livraison:', deliveryId)
      // Logique pour voir les détails de livraison
    }

    return {
      searchQuery,
      showNewDeliveryModal,
      filters,
      deliveries,
      deliveryHistory,
      filteredDeliveries,
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
  background: #059669;
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
  box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
}

.new-delivery-button:hover {
  background: #047857;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
}

.new-delivery-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* STATISTIQUES */
.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon svg {
  width: 24px;
  height: 24px;
}

.stat-pending .stat-icon {
  background: #EFF6FF;
  color: #2563EB;
}

.stat-success .stat-icon {
  background: #F0FDF4;
  color: #059669;
}

.stat-warning .stat-icon {
  background: #FFFBEB;
  color: #D97706;
}

.stat-title {
  font-size: 14px;
  font-weight: 500;
  color: #64748B;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
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

.filters-group {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.search-container {
  position: relative;
  flex: 1;
  min-width: 250px;
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
  min-width: 160px;
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
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1.5rem 0;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #F1F5F9;
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
  padding: 0.75rem 1rem;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #E2E8F0;
  white-space: nowrap;
}

.deliveries-table td,
.history-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
  vertical-align: middle;
}

.deliveries-table tr:hover,
.history-table tr:hover {
  background: #F8FAFC;
}

/* CELLULES SPÉCIFIQUES */
.delivery-id {
  font-weight: 600;
  color: #0F172A;
}

.supplier-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  min-width: 140px;
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

.supplier-name {
  font-weight: 500;
  color: #334155;
}

.delivery-date {
  color: #64748B;
  font-size: 13px;
}

.order-reference {
  font-weight: 500;
  color: #3B82F6;
}

.articles-count {
  font-weight: 600;
  color: #0F172A;
  text-align: center;
}

.received-by {
  color: #64748B;
  font-size: 13px;
}

/* BADGES DE STATUT */
.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
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
.actions-cell {
  min-width: 120px;
}

.action-buttons {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.action-button {
  border: none;
  border-radius: 6px;
  padding: 0.5rem 0.75rem;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.receive-button {
  background: #059669;
  color: white;
}

.receive-button:hover {
  background: #047857;
  transform: translateY(-1px);
}

.report-button {
  background: #DC2626;
  color: white;
}

.report-button:hover {
  background: #B91C1C;
  transform: translateY(-1px);
}

.details-button {
  background: #3B82F6;
  color: white;
}

.details-button:hover {
  background: #2563EB;
  transform: translateY(-1px);
}

.view-button {
  background: none;
  border: 1px solid #E2E8F0;
  color: #64748B;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.view-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.view-button svg {
  width: 14px;
  height: 14px;
  stroke-width: 2;
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
  
  .filters-group {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-container {
    min-width: auto;
  }
  
  .stats-section {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .stats-section {
    grid-template-columns: 1fr;
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
  
  .supplier-info {
    min-width: 100px;
  }
  
  .action-buttons {
    flex-direction: column;
    gap: 0.25rem;
  }
}

@media (max-width: 640px) {
  .deliveries-table th:nth-child(3),
  .deliveries-table td:nth-child(3),
  .deliveries-table th:nth-child(4),
  .deliveries-table td:nth-child(4),
  .history-table th:nth-child(3),
  .history-table td:nth-child(3),
  .history-table th:nth-child(4),
  .history-table td:nth-child(4) {
    display: none;
  }
}
</style>
