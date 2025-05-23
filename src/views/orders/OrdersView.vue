<template>
  <div class="orders-page">
    <!-- En-tête avec bouton Créer -->
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Gestion des commandes</h1>
      </div>
      <div class="header-right">
        <button class="create-button" @click="goToNewOrder">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
          </svg>
          Créer
        </button>
      </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
      <div class="filter-group">
        <select v-model="filters.supplier" class="filter-select">
          <option value="">Fournisseur</option>
          <option value="Bernard FR">Bernard FR</option>
          <option value="Durand SA">Durand SA</option>
          <option value="Martin SA">Martin SA</option>
          <option value="Artist DEF">Artist DEF</option>
          <option value="Dupont SAS">Dupont SAS</option>
        </select>
        
        <select v-model="filters.status" class="filter-select">
          <option value="">Statut</option>
          <option value="En attente">En attente</option>
          <option value="En cours">En cours</option>
          <option value="Expédiée">Expédiée</option>
          <option value="En préparation">En préparation</option>
        </select>
        
        <div class="search-container">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Rechercher..."
            class="search-input"
          />
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- Tableau des commandes -->
    <div class="table-section">
      <div class="table-card">
        <div class="table-container">
          <table class="orders-table">
            <thead>
              <tr>
                <th>Commande</th>
                <th>Client</th>
                <th>Date</th>
                <th>État</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in filteredOrders" :key="order.id">
                <td class="order-id">#{{ order.id }}</td>
                <td class="client-name">{{ order.client }}</td>
                <td class="order-date">{{ order.date }}</td>
                <td>
                  <span class="status-badge" :class="getStatusClass(order.status)">
                    {{ order.status }}
                  </span>
                </td>
                <td>
                  <button 
                    class="action-button"
                    @click="viewOrder(order.id)"
                    title="Voir les détails"
                  >
                    Voir
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'OrdersView',
  setup() {
    const router = useRouter()
    const searchQuery = ref('')
    
    const filters = ref({
      supplier: '',
      status: ''
    })

    // Données des commandes selon la maquette
    const orders = ref([
      {
        id: '00008',
        client: 'Artist DEF',
        date: '1 janv. 2023',
        status: 'En attente'
      },
      {
        id: '00007',
        client: 'Bernard FR',
        date: '31 mai 2023',
        status: 'Expédiée'
      },
      {
        id: '00006',
        client: 'Dupont SAS',
        date: '30 mai 2023',
        status: 'En cours'
      },
      {
        id: '00005',
        client: 'Martin SA',
        date: '7 mai 2023',
        status: 'En attente'
      },
      {
        id: '00004',
        client: 'Bernard FR',
        date: '27 mai 2023',
        status: 'Expédiée'
      },
      {
        id: '00003',
        client: 'Dupont SAS',
        date: '23 mai 2023',
        status: 'En cours'
      },
      {
        id: '00002',
        client: 'Martin SA',
        date: '11 mai 2023',
        status: 'En cours'
      },
      {
        id: '00001',
        client: 'Martin SA',
        date: '30 janv. 2023',
        status: 'En cours'
      }
    ])

    const filteredOrders = computed(() => {
      let result = orders.value

      // Filtre par fournisseur (ici on utilise client car c'est ce qu'on a)
      if (filters.value.supplier) {
        result = result.filter(order => 
          order.client.toLowerCase().includes(filters.value.supplier.toLowerCase())
        )
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

      return result
    })

    const getStatusClass = (status) => {
      const statusClasses = {
        'En attente': 'status-pending',
        'En cours': 'status-progress',
        'Expédiée': 'status-shipped',
        'En préparation': 'status-preparing'
      }
      return statusClasses[status] || 'status-default'
    }

    const goToNewOrder = () => {
      router.push('/orders/new')
    }

    const viewOrder = (orderId) => {
      router.push(`/orders/${orderId}`)
    }

    return {
      searchQuery,
      filters,
      orders,
      filteredOrders,
      getStatusClass,
      goToNewOrder,
      viewOrder
    }
  }
}
</script>

<style scoped>
.orders-page {
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

.create-button {
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
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.create-button:hover {
  background: #2563EB;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.create-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
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

.search-input::placeholder {
  color: #94A3B8;
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

/* TABLEAU */
.table-section {
  margin-bottom: 2rem;
}

.table-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  overflow: hidden;
}

.table-container {
  overflow-x: auto;
}

.orders-table {
  width: 100%;
  border-collapse: collapse;
}

.orders-table th {
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

.orders-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
  vertical-align: middle;
}

.orders-table tr:hover {
  background: #F8FAFC;
}

.order-id {
  font-weight: 600;
  color: #0F172A;
}

.client-name {
  font-weight: 500;
  color: #334155;
}

.order-date {
  color: #64748B;
  font-size: 13px;
}

/* BADGES DE STATUT */
.status-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 500;
  text-transform: capitalize;
  white-space: nowrap;
}

.status-pending {
  background: #FEF3C7;
  color: #92400E;
}

.status-progress {
  background: #DBEAFE;
  color: #1E40AF;
}

.status-shipped {
  background: #D1FAE5;
  color: #047857;
}

.status-preparing {
  background: #E0E7FF;
  color: #4338CA;
}

.status-default {
  background: #F1F5F9;
  color: #64748B;
}

/* BOUTON ACTION */
.action-button {
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

.action-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .filter-group {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-select,
  .search-container {
    max-width: none;
  }
}

@media (max-width: 768px) {
  .orders-table {
    font-size: 12px;
  }
  
  .orders-table th,
  .orders-table td {
    padding: 0.75rem 0.5rem;
  }
  
  .status-badge {
    font-size: 10px;
    padding: 0.25rem 0.5rem;
  }
  
  .action-button {
    font-size: 10px;
    padding: 0.375rem 0.75rem;
  }
}

@media (max-width: 640px) {
  .orders-table th:nth-child(3),
  .orders-table td:nth-child(3) {
    display: none;
  }
}
</style>