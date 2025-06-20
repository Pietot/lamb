<template>
  <div class="dashboard">
    <!-- Cartes KPI -->
    <div class="kpi-section">
      <div class="kpi-card kpi-alert">
        <div class="kpi-icon alert-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Alertes<br>de stock</p>
          <p class="kpi-value">{{ stockAlerts }}</p>
        </div>
      </div>

      <div class="kpi-card kpi-orders">
        <div class="kpi-icon orders-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
            <polyline points="14,2 14,8 20,8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10,9 9,9 8,9"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Commandes<br>en préparation</p>
          <p class="kpi-value">{{ ordersInPreparation }}</p>
        </div>
      </div>

      <div class="kpi-card kpi-delivery">
        <div class="kpi-icon delivery-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="1" y="3" width="15" height="13"/>
            <polygon points="16,3 19,7 19,13 16,13"/>
            <circle cx="5.5" cy="18.5" r="2.5"/>
            <circle cx="18.5" cy="18.5" r="2.5"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Livraisons<br>à réceptionner</p>
          <p class="kpi-value">{{ deliveriesToReceive }}</p>
        </div>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="dashboard-content">
      <!-- Commandes récentes - Pleine largeur -->
      <div class="section-card full-width">
        <h3 class="section-title">Commandes récentes</h3>
        <div v-if="loadingOrders || loadingClients" class="loading-container">
          <div class="loader"></div>
          <p>Chargement des commandes...</p>
        </div>
        <div v-else-if="ordersError" class="error-container">
          <p class="error-message">{{ ordersError }}</p>
          <button @click="fetchOrders" class="retry-button">Réessayer</button>
        </div>
        <div v-else class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Date</th>
                <th>Montant</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in recentOrders" :key="order.id_commande">
                <td class="order-id">#{{ order.id_commande }}</td>
                <td>{{ getClientName(order.id_client) }}</td>
                <td>{{ formatDate(order.date_commande) }}</td>
                <td>{{ formatCurrency(order.montant_total) }}</td>
                <td>
                  <span class="status" :class="getStatusClass(order.statut)">
                    {{ getStatusLabel(order.statut) }}
                  </span>
                </td>
              </tr>
              <tr v-if="recentOrders.length === 0">
                <td colspan="5" class="empty-message">Aucune commande récente</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Section inférieure -->
      <div class="dashboard-bottom">
        <!-- Articles en alerte -->
        <div class="section-card">
          <h3 class="section-title">Articles en alerte</h3>
          <div class="alerts-container">
            <div v-for="item in lowStockItems" :key="item.id_article" class="alert-item">
              <div class="alert-icon-container">
                <div class="alert-dot stock-alert"></div>
              </div>
              <div class="alert-content">
                <p class="alert-title">{{ item.nom }}</p>
                <p class="alert-description">Stock: {{ item.quantite_stock }} / Seuil: {{ item.seuil_alerte }}</p>
              </div>
            </div>
            <div v-if="lowStockItems.length === 0" class="empty-message">
              Aucune alerte de stock
            </div>
          </div>
        </div>

        <!-- Livraisons à réceptionner -->
        <div class="section-card">
          <h3 class="section-title">Livraisons à réceptionner</h3>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Fournisseur</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="delivery in deliveries" :key="delivery.id">
                  <td class="delivery-id">{{ delivery.id }}</td>
                  <td>{{ delivery.supplier }}</td>
                  <td>{{ delivery.date }}</td>
                </tr>
                <tr v-if="deliveries.length === 0">
                  <td colspan="3" class="empty-message">Aucune livraison en attente</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'DashboardView',
  setup() {
    // États réactifs
    const orders = ref([])
    const clients = ref([])
    const articles = ref([])
    const deliveries = ref([])
    const loadingOrders = ref(true)
    const loadingClients = ref(true)
    const loadingArticles = ref(true)
    const ordersError = ref(null)
    const clientsError = ref(null)
    const articlesError = ref(null)

    // Données mockées pour les livraisons (à remplacer par vos endpoints)
    const mockDeliveries = [
      { id: '5003', supplier: 'Durand SA', date: '6 mai 2023' },
      { id: '5002', supplier: 'Bernard FR', date: '6 mai 2023' },
      { id: '5001', supplier: 'Bernard FR', date: '3 mai 2023' }
    ]

    // Fonction pour récupérer les commandes
    const fetchOrders = async () => {
      loadingOrders.value = true
      ordersError.value = null
      
      try {
        const response = await fetch(import.meta.env.VITE_API_URL + "get_table?table=commande", {
          method: "GET",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            Authorization: `Bearer ${import.meta.env.VITE_API_KEY}`,
          },
        })

        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`)
        }

        const data = await response.json()
        
        if (data.success && data.articles) {
          orders.value = data.articles
        } else {
          throw new Error('Format de données invalide')
        }
      } catch (err) {
        console.error('Erreur lors du chargement des commandes:', err)
        ordersError.value = 'Impossible de charger les commandes.'
      } finally {
        loadingOrders.value = false
      }
    }

    // Fonction pour récupérer les clients
    const fetchClients = async () => {
      loadingClients.value = true
      clientsError.value = null
      
      try {
        const response = await fetch(import.meta.env.VITE_API_URL + "get_table?table=client", {
          method: "GET",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            Authorization: `Bearer ${import.meta.env.VITE_API_KEY}`,
          },
        })

        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`)
        }

        const data = await response.json()
        
        if (data.success && data.articles) {
          clients.value = data.articles
        } else {
          throw new Error('Format de données invalide')
        }
      } catch (err) {
        console.error('Erreur lors du chargement des clients:', err)
        clientsError.value = 'Impossible de charger les clients.'
      } finally {
        loadingClients.value = false
      }
    }

    // Fonction pour récupérer les articles (pour les alertes de stock)
    const fetchArticles = async () => {
      loadingArticles.value = true
      articlesError.value = null
      
      try {
        const response = await fetch(import.meta.env.VITE_API_URL + "get_table?table=article", {
          method: "GET",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            Authorization: `Bearer ${import.meta.env.VITE_API_KEY}`,
          },
        })

        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`)
        }

        const data = await response.json()
        
        if (data.success && data.articles) {
          articles.value = data.articles
        } else {
          throw new Error('Format de données invalide')
        }
      } catch (err) {
        console.error('Erreur lors du chargement des articles:', err)
        articlesError.value = 'Impossible de charger les articles.'
      } finally {
        loadingArticles.value = false
      }
    }

    // Computed properties
    const recentOrders = computed(() => {
      // Trier par date décroissante et prendre les 5 dernières
      return orders.value
        .sort((a, b) => new Date(b.date_commande) - new Date(a.date_commande))
        .slice(0, 5)
    })

    const ordersInPreparation = computed(() => {
      return orders.value.filter(order => order.statut === 'preparation').length
    })

    const deliveriesToReceive = computed(() => {
      return deliveries.value.length
    })

    const stockAlerts = computed(() => {
      return articles.value.filter(article => article.quantite_stock <= article.seuil_alerte).length
    })

    const lowStockItems = computed(() => {
      // Récupérer les 5 premiers articles en alerte
      return articles.value
        .filter(article => article.quantite_stock <= article.seuil_alerte)
        .slice(0, 5)
    })

    // Fonction pour obtenir le nom du client
    const getClientName = (clientId) => {
      const client = clients.value.find(c => c.id_client === clientId)
      if (client) {
        return `${client.prénom} ${client.nom}`
      }
      return `Client #${clientId}`
    }

    // Fonctions utilitaires
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

    const getStatusClass = (status) => {
      const statusClasses = {
        'preparation': 'status-preparing',
        'expedie': 'status-shipped',
        'attente': 'status-wait',
        'annule': 'status-cancelled'
      }
      return statusClasses[status] || 'status-default'
    }

    const getStatusLabel = (status) => {
      const statusLabels = {
        'preparation': 'En préparation',
        'expedie': 'Expédiée',
        'attente': 'En attente',
        'annule': 'Annulée'
      }
      return statusLabels[status] || status
    }

    // Charger les données au montage du composant
    onMounted(() => {
      fetchOrders()
      fetchClients()
      fetchArticles()
      // Charger les données mockées
      deliveries.value = mockDeliveries
    })

    return {
      orders,
      clients,
      articles,
      deliveries,
      loadingOrders,
      loadingClients,
      loadingArticles,
      ordersError,
      clientsError,
      articlesError,
      recentOrders,
      ordersInPreparation,
      deliveriesToReceive,
      stockAlerts,
      lowStockItems,
      fetchOrders,
      fetchClients,
      fetchArticles,
      getClientName,
      formatDate,
      formatCurrency,
      getStatusClass,
      getStatusLabel
    }
  }
}
</script>

<style scoped>
.dashboard {
  padding: 0;
  font-family: 'Inter', sans-serif;
}

/* SECTION KPI */
.kpi-section {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.kpi-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  transition: all 0.2s ease;
}

.kpi-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.kpi-icon svg {
  width: 24px;
  height: 24px;
}

.alert-icon {
  background: #FEF2F2;
  color: #DC2626;
}

.orders-icon {
  background: #EFF6FF;
  color: #2563EB;
}

.delivery-icon {
  background: #F0FDF4;
  color: #059669;
}

.kpi-content {
  flex: 1;
}

.kpi-label {
  font-size: 13px;
  color: #64748B;
  margin: 0 0 4px 0;
  line-height: 1.3;
  font-weight: 500;
}

.kpi-value {
  font-size: 28px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
  line-height: 1;
}

/* CONTENU DASHBOARD */
.dashboard-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.dashboard-bottom {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.section-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
}

.section-card.full-width {
  width: 100%;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1.5rem 0;
}

/* LOADING & ERROR */
.loading-container,
.error-container {
  text-align: center;
  padding: 2rem;
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

/* TABLEAUX */
.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  text-align: left;
  padding: 8px 12px;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #E2E8F0;
}

.data-table td {
  padding: 12px;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
}

.data-table tbody tr:hover {
  background-color: #F8FAFC;
}

.data-table tbody tr:last-child td {
  border-bottom: none;
}

.order-id, .delivery-id {
  font-weight: 600;
  color: #0F172A;
}

.empty-message {
  text-align: center;
  color: #64748B;
  font-style: italic;
  padding: 1rem;
}

.status {
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.status-preparing {
  background: #EFF6FF;
  color: #1D4ED8;
}

.status-wait {
  background: #FEF3C7;
  color: #92400E;
}

.status-shipped {
  background: #E0E7FF;
  color: #4338CA;
}

.status-cancelled {
  background: #FEF2F2;
  color: #DC2626;
}

.status-default {
  background: #F1F5F9;
  color: #64748B;
}

/* ALERTES */
.alerts-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-height: 300px;
  overflow-y: auto;
}

.alert-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #F8FAFC;
  border-radius: 8px;
  border: 1px solid #E2E8F0;
  transition: all 0.2s ease;
}

.alert-item:hover {
  background: #F1F5F9;
  border-color: #CBD5E1;
}

.alert-icon-container {
  flex-shrink: 0;
}

.alert-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.8;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.stock-alert {
  background: #EF4444;
}

.alert-content {
  flex: 1;
}

.alert-title {
  font-size: 14px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 2px 0;
}

.alert-description {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .kpi-section {
    grid-template-columns: 1fr;
  }
  
  .dashboard-bottom {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .kpi-card {
    padding: 1rem;
  }
  
  .section-card {
    padding: 1rem;
  }
  
  .data-table th,
  .data-table td {
    padding: 8px;
    font-size: 12px;
  }
}
</style>