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
          <span>Nouvelle commande</span>
        </button>
      </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
      <div class="filter-group">
        <select v-model="filters.clientId" class="filter-select">
          <option value="">Tous les clients</option>
          <option v-for="client in uniqueClients" :key="client.id_client" :value="client.id_client">
            {{ client.prénom }} {{ client.nom }}
          </option>
        </select>
        
        <select v-model="filters.status" class="filter-select">
          <option value="">Tous les statuts</option>
          <option value="attente">En attente</option>
          <option value="preparation">En préparation</option>
          <option value="expedie">Expédiée</option>
        </select>
        
        <div class="search-container">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Rechercher par ID, client ou montant..."
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
        <div class="table-header">
          <h3 class="table-title">Liste des commandes</h3>
          <div class="table-stats">
            <span class="stat-item">
              <span class="stat-label">Total:</span>
              <span class="stat-value">{{ filteredOrders.length }}</span>
            </span>
            <span class="stat-item">
              <span class="stat-label">En attente:</span>
              <span class="stat-value pending">{{ pendingCount }}</span>
            </span>
            <span class="stat-item">
              <span class="stat-label">En préparation:</span>
              <span class="stat-value progress">{{ inProgressCount }}</span>
            </span>
          </div>
        </div>
        
        <div v-if="loading" class="loading-container">
          <div class="loader"></div>
          <p>Chargement des commandes...</p>
        </div>
        
        <div v-else-if="error" class="error-container">
          <p class="error-message">{{ error }}</p>
          <button @click="fetchData" class="retry-button">Réessayer</button>
        </div>
        
        <div v-else class="table-container">
          <table class="orders-table">
            <thead>
              <tr>
                <th>Commande</th>
                <th>Client</th>
                <th>Date</th>
                <th>Montant</th>
                <th>État</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in paginatedOrders" :key="order.id_commande">
                <td class="order-id">#{{ String(order.id_commande).padStart(5, '0') }}</td>
                <td class="client-name">{{ getClientName(order.id_client) }}</td>
                <td class="order-date">{{ formatDate(order.date_commande) }}</td>
                <td class="order-amount">{{ formatCurrency(order.montant_total) }}</td>
                <td>
                  <span class="status-badge" :class="getStatusClass(order.statut)">
                    {{ getStatusLabel(order.statut) }}
                  </span>
                </td>
                <td class="actions">
                  <button 
                    class="action-btn"
                    @click="viewOrder(order)"
                    title="Voir les détails"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>
                  <button 
                    class="action-btn"
                    @click="editOrder(order.id_commande)"
                    title="Modifier"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button 
                    class="action-btn"
                    @click="printOrder(order.id_commande)"
                    title="Imprimer"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <polyline points="6 9 6 2 18 2 18 9"/>
                      <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                      <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                  </button>
                </td>
              </tr>
              <tr v-if="paginatedOrders.length === 0">
                <td colspan="6" class="empty-message">Aucune commande trouvée</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="!loading && !error && totalPages > 1" class="pagination-container">
          <div class="pagination-info">
            Affichage {{ startIndex + 1 }}-{{ endIndex }} sur {{ filteredOrders.length }} commandes
          </div>
          <div class="pagination-controls">
            <button 
              class="pagination-btn" 
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polyline points="15 18 9 12 15 6"/>
              </svg>
            </button>
            
            <div class="page-numbers">
              <button
                v-for="page in visiblePages"
                :key="page"
                class="page-number"
                :class="{ 'active': page === currentPage }"
                @click="goToPage(page)"
                :disabled="page === '...'"
              >
                {{ page }}
              </button>
            </div>
            
            <button 
              class="pagination-btn" 
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === totalPages"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polyline points="9 18 15 12 9 6"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Voir détails -->
    <div v-if="showDetailsModal" class="modal-overlay" @click="showDetailsModal = false">
      <div class="modal-content modal-details" @click.stop>
        <div class="modal-header">
          <h3>Détails de la commande</h3>
          <button @click="showDetailsModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="selectedOrder">
          <div class="details-grid">
            <div class="details-section">
              <h4 class="section-subtitle">Informations générales</h4>
              <div class="detail-row">
                <span class="detail-label">N° Commande:</span>
                <span class="detail-value">#{{ String(selectedOrder.id_commande).padStart(5, '0') }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ formatDate(selectedOrder.date_commande) }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Statut:</span>
                <span class="detail-value">
                  <span class="status-badge" :class="getStatusClass(selectedOrder.statut)">
                    {{ getStatusLabel(selectedOrder.statut) }}
                  </span>
                </span>
              </div>
            </div>

            <div class="details-section">
              <h4 class="section-subtitle">Informations client</h4>
              <div class="detail-row">
                <span class="detail-label">Nom:</span>
                <span class="detail-value">{{ getClientName(selectedOrder.id_client) }}</span>
              </div>
              <div v-if="getClient(selectedOrder.id_client)" class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ getClient(selectedOrder.id_client).email }}</span>
              </div>
              <div v-if="getClient(selectedOrder.id_client)" class="detail-row">
                <span class="detail-label">Téléphone:</span>
                <span class="detail-value">{{ formatPhone(getClient(selectedOrder.id_client).telephone) }}</span>
              </div>
            </div>
            
            <div class="details-section full-width">
              <h4 class="section-subtitle">Informations financières</h4>
              <div class="detail-row">
                <span class="detail-label">Montant total:</span>
                <span class="detail-value amount">{{ formatCurrency(selectedOrder.montant_total) }}</span>
              </div>
            </div>
          </div>
          
          <div class="modal-actions">
            <button class="modal-btn secondary" @click="showDetailsModal = false">
              Fermer
            </button>
            <button class="modal-btn primary" @click="editOrder(selectedOrder.id_commande)">
              Modifier la commande
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'OrdersView',
  setup() {
    const router = useRouter()
    const searchQuery = ref('')
    const orders = ref([])
    const clients = ref([])
    const loading = ref(true)
    const error = ref(null)
    const currentPage = ref(1)
    const itemsPerPage = 10
    const showDetailsModal = ref(false)
    const selectedOrder = ref(null)
    
    const filters = ref({
      clientId: '',
      status: ''
    })

    // Fonction pour récupérer les commandes et les clients
    const fetchData = async () => {
      loading.value = true
      error.value = null
      
      try {
        // Récupérer les commandes
        const ordersResponse = await fetch(import.meta.env.VITE_API_URL + "get_table?table=commande", {
          method: "GET",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            Authorization: `Bearer ${import.meta.env.VITE_API_KEY}`,
          },
        })

        // Récupérer les clients
        const clientsResponse = await fetch(import.meta.env.VITE_API_URL + "get_table?table=client", {
          method: "GET",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            Authorization: `Bearer ${import.meta.env.VITE_API_KEY}`,
          },
        })

        if (!ordersResponse.ok || !clientsResponse.ok) {
          throw new Error('Erreur lors du chargement des données')
        }

        const ordersData = await ordersResponse.json()
        const clientsData = await clientsResponse.json()
        
        if (ordersData.success && ordersData.data) {
          // Trier les commandes par date décroissante
          orders.value = ordersData.data.sort((a, b) => 
            new Date(b.date_commande) - new Date(a.date_commande)
          )
        }

        if (clientsData.success && clientsData.data) {
          clients.value = clientsData.data
        }
      } catch (err) {
        console.error('Erreur lors du chargement:', err)
        error.value = 'Impossible de charger les données. Veuillez réessayer.'
      } finally {
        loading.value = false
      }
    }

    // Computed properties
    const uniqueClients = computed(() => {
      return clients.value.sort((a, b) => a.nom.localeCompare(b.nom))
    })

    const filteredOrders = computed(() => {
      let result = orders.value

      // Filtre par client
      if (filters.value.clientId) {
        result = result.filter(order => 
          order.id_client == filters.value.clientId
        )
      }

      // Filtre par statut
      if (filters.value.status) {
        result = result.filter(order => order.statut === filters.value.status)
      }

      // Filtre par recherche
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        result = result.filter(order => {
          const client = getClient(order.id_client)
          const clientName = client ? `${client.prénom} ${client.nom}`.toLowerCase() : ''
          return (
            order.id_commande.toString().includes(query) ||
            clientName.includes(query) ||
            order.montant_total.toString().includes(query)
          )
        })
      }

      return result
    })

    // Pagination computed properties
    const totalPages = computed(() => {
      return Math.ceil(filteredOrders.value.length / itemsPerPage)
    })

    const startIndex = computed(() => {
      return (currentPage.value - 1) * itemsPerPage
    })

    const endIndex = computed(() => {
      return Math.min(startIndex.value + itemsPerPage, filteredOrders.value.length)
    })

    const paginatedOrders = computed(() => {
      return filteredOrders.value.slice(startIndex.value, endIndex.value)
    })

    const visiblePages = computed(() => {
      const pages = []
      const total = totalPages.value
      const current = currentPage.value

      if (total <= 7) {
        for (let i = 1; i <= total; i++) {
          pages.push(i)
        }
      } else {
        pages.push(1)
        if (current > 3) pages.push('...')
        
        const start = Math.max(2, current - 1)
        const end = Math.min(total - 1, current + 1)
        
        for (let i = start; i <= end; i++) {
          pages.push(i)
        }
        
        if (current < total - 2) pages.push('...')
        pages.push(total)
      }

      return pages
    })

    const pendingCount = computed(() => {
      return orders.value.filter(order => order.statut === 'attente').length
    })

    const inProgressCount = computed(() => {
      return orders.value.filter(order => order.statut === 'preparation').length
    })

    // Watchers
    watch([searchQuery, filters], () => {
      currentPage.value = 1
    })

    // Fonctions utilitaires
    const getClient = (clientId) => {
      return clients.value.find(client => client.id_client === clientId)
    }

    const getClientName = (clientId) => {
      const client = getClient(clientId)
      return client ? `${client.prénom} ${client.nom}` : `Client #${clientId}`
    }

    const formatDate = (dateString) => {
      const date = new Date(dateString)
      const months = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.']
      return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }

    const formatPhone = (phone) => {
      if (!phone || phone === '32767') return 'Non renseigné'
      
      // Formater le numéro de téléphone français
      const cleaned = phone.toString().replace(/\D/g, '')
      if (cleaned.length === 10) {
        return cleaned.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5')
      }
      return phone
    }

    const getStatusClass = (status) => {
      const statusClasses = {
        'attente': 'status-pending',
        'preparation': 'status-progress',
        'expedie': 'status-shipped'
      }
      return statusClasses[status] || 'status-default'
    }

    const getStatusLabel = (status) => {
      const statusLabels = {
        'attente': 'En attente',
        'preparation': 'En préparation',
        'expedie': 'Expédiée'
      }
      return statusLabels[status] || status
    }

    const goToPage = (page) => {
      if (page !== '...' && page >= 1 && page <= totalPages.value) {
        currentPage.value = page
      }
    }

    const goToNewOrder = () => {
      router.push('/orders/new')
    }

    const viewOrder = (order) => {
      selectedOrder.value = order
      showDetailsModal.value = true
    }

    const editOrder = (orderId) => {
      showDetailsModal.value = false
      router.push(`/orders/${orderId}/edit`)
    }

    const printOrder = (orderId) => {
      console.log('Imprimer la commande:', orderId)
      // TODO: Implémenter la fonction d'impression
    }

    // Charger les données au montage
    onMounted(() => {
      fetchData()
    })

    return {
      searchQuery,
      filters,
      orders,
      clients,
      loading,
      error,
      currentPage,
      showDetailsModal,
      selectedOrder,
      uniqueClients,
      filteredOrders,
      paginatedOrders,
      totalPages,
      startIndex,
      endIndex,
      visiblePages,
      pendingCount,
      inProgressCount,
      getClient,
      getClientName,
      formatDate,
      formatCurrency,
      formatPhone,
      getStatusClass,
      getStatusLabel,
      goToPage,
      goToNewOrder,
      viewOrder,
      editOrder,
      printOrder,
      fetchData
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

.create-button:hover {
  background: #0891A6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
}

.create-button svg {
  width: 18px;
  height: 18px;
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

/* LOADING & ERROR */
.loading-container,
.error-container {
  text-align: center;
  padding: 3rem;
  color: #64748B;
}

.loader {
  width: 40px;
  height: 40px;
  border: 3px solid #F1F5F9;
  border-top-color: #00B8D4;
  border-radius: 50%;
  margin: 0 auto 1rem;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-message {
  color: #DC2626;
  margin-bottom: 1rem;
}

.retry-button {
  background: #00B8D4;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.retry-button:hover {
  background: #0891A6;
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

.table-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #E2E8F0;
}

.table-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.table-stats {
  display: flex;
  gap: 2rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 14px;
}

.stat-label {
  color: #64748B;
}

.stat-value {
  font-weight: 600;
  color: #0F172A;
}

.stat-value.pending {
  color: #F59E0B;
}

.stat-value.progress {
  color: #3B82F6;
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

.orders-table tbody tr:hover {
  background: #F8FAFC;
}

.orders-table tbody tr:last-child td {
  border-bottom: none;
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

.order-amount {
  font-weight: 600;
  color: #0F172A;
}

.empty-message {
  text-align: center;
  color: #64748B;
  font-style: italic;
  padding: 2rem;
}

/* BADGES DE STATUT */
.status-badge {
  display: inline-flex;
  align-items: center;
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

.status-default {
  background: #F1F5F9;
  color: #64748B;
}

/* ACTIONS */
.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  background: none;
  border: 1px solid #E2E8F0;
  border-radius: 6px;
  padding: 6px;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.action-btn svg {
  width: 16px;
  height: 16px;
  stroke-width: 1.5;
}

/* PAGINATION */
.pagination-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  border-top: 1px solid #E2E8F0;
  background: #F8FAFC;
}

.pagination-info {
  font-size: 14px;
  color: #64748B;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.pagination-btn {
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 6px;
  padding: 6px 10px;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pagination-btn:hover:not(:disabled) {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-btn svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

.page-numbers {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.page-number {
  min-width: 32px;
  height: 32px;
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 6px;
  color: #64748B;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-number:hover:not(:disabled):not(.active) {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.page-number.active {
  background: #00B8D4;
  border-color: #00B8D4;
  color: white;
}

.page-number:disabled {
  cursor: default;
  color: #CBD5E1;
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
  max-width: 600px;
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
  color: #64748B;
  overflow-y: auto;
  max-height: calc(90vh - 80px);
}

/* MODAL DÉTAILS */
.details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-bottom: 2rem;
}

.details-section {
  background: #F8FAFC;
  padding: 1.5rem;
  border-radius: 8px;
}

.details-section.full-width {
  grid-column: 1 / -1;
}

.section-subtitle {
  font-size: 14px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1rem 0;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #E2E8F0;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #E2E8F0;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-size: 13px;
  color: #64748B;
  font-weight: 500;
}

.detail-value {
  font-size: 14px;
  color: #0F172A;
  font-weight: 600;
  text-align: right;
}

.detail-value.amount {
  font-size: 16px;
  color: #00B8D4;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding-top: 1.5rem;
  border-top: 1px solid #E2E8F0;
}

.modal-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.modal-btn.primary {
  background: #00B8D4;
  color: white;
}

.modal-btn.primary:hover {
  background: #0891A6;
}

.modal-btn.secondary {
  background: #F1F5F9;
  color: #64748B;
}

.modal-btn.secondary:hover {
  background: #E2E8F0;
  color: #334155;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .create-button {
    width: 100%;
    justify-content: center;
  }
  
  .filter-group {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-select,
  .search-container {
    max-width: none;
  }
  
  .table-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .table-stats {
    width: 100%;
    justify-content: space-between;
  }
  
  .details-grid {
    grid-template-columns: 1fr;
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
  
  .actions {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .action-btn {
    padding: 4px;
  }
  
  .action-btn svg {
    width: 14px;
    height: 14px;
  }
  
  .pagination-container {
    flex-direction: column;
    gap: 1rem;
  }
  
  .modal-actions {
    flex-direction: column;
  }
  
  .modal-btn {
    width: 100%;
  }
}

@media (max-width: 640px) {
  .orders-table th:nth-child(4),
  .orders-table td:nth-child(4) {
    display: none;
  }
  
  .orders-table th:last-child,
  .orders-table td:last-child {
    display: none;
  }
}
</style>