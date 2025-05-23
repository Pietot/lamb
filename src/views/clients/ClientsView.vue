<template>
  <div class="clients-page">
    <!-- En-tête avec bouton Nouveau client -->
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Gestion des clients</h1>
      </div>
      <div class="header-right">
        <button class="new-client-button" @click="showNewClientModal = true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
          </svg>
          Nouveau client
        </button>
      </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="filters-section">
      <div class="filters-group">
        <div class="search-container">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher un client..."
            class="search-input"
          />
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
        </div>
        
        <select v-model="filters.type" class="filter-select">
          <option value="">Tous les types</option>
          <option value="Entreprise">Entreprise</option>
          <option value="Particulier">Particulier</option>
        </select>
        
        <select v-model="filters.sort" class="filter-select">
          <option value="name">Trier par nom</option>
          <option value="ca">Trier par CA</option>
          <option value="orders">Trier par commandes</option>
          <option value="date">Trier par date</option>
        </select>
        
        <button class="filter-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
          </svg>
          Filtrer
        </button>
        
        <button class="export-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7,10 12,15 17,10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Exporter
        </button>
      </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="stats-section">
      <div class="stat-card">
        <div class="stat-content">
          <h3 class="stat-title">Actif clients</h3>
          <p class="stat-value">156</p>
          <p class="stat-subtitle">+7% ce mois</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-content">
          <h3 class="stat-title">Inactif clients</h3>
          <p class="stat-value">112</p>
          <p class="stat-subtitle">+2% ce mois</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-content">
          <h3 class="stat-title">Particuliers</h3>
          <p class="stat-value">44</p>
          <p class="stat-subtitle">28% du total</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-content">
          <h3 class="stat-title">Chiffre d'affaires</h3>
          <p class="stat-value">245 390 €</p>
          <p class="stat-subtitle">+15% ce mois</p>
        </div>
      </div>
    </div>

    <!-- Tableau des clients -->
    <div class="table-section">
      <div class="table-card">
        <div class="table-container">
          <table class="clients-table">
            <thead>
              <tr>
                <th>Client</th>
                <th>Type</th>
                <th>Contact</th>
                <th>Commandes</th>
                <th>CA Total</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="client in filteredClients" :key="client.id">
                <td class="client-info">
                  <div class="client-avatar">
                    {{ getClientInitials(client.name) }}
                  </div>
                  <div class="client-details">
                    <p class="client-name">{{ client.name }}</p>
                    <p class="client-id">ID #{{ client.id }}</p>
                  </div>
                </td>
                <td>
                  <span class="type-badge" :class="getTypeClass(client.type)">
                    {{ client.type }}
                  </span>
                </td>
                <td class="contact-info">
                  <p class="contact-email">{{ client.contact }}</p>
                  <p class="contact-phone">{{ client.phone }}</p>
                </td>
                <td class="orders-count">{{ client.orders }}</td>
                <td class="ca-amount">{{ client.caTotal }}</td>
                <td>
                  <span class="status-badge" :class="getStatusClass(client.status)">
                    {{ client.status }}
                  </span>
                </td>
                <td class="actions-cell">
                  <div class="action-buttons">
                    <button class="action-button view-button" @click="viewClient(client.id)" title="Voir">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                      </svg>
                    </button>
                    <button class="action-button edit-button" @click="editClient(client.id)" title="Modifier">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Nouveau client (placeholder) -->
    <div v-if="showNewClientModal" class="modal-overlay" @click="showNewClientModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Nouveau client</h3>
          <button @click="showNewClientModal = false" class="modal-close">
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
  name: 'ClientsView',
  setup() {
    const searchQuery = ref('')
    const showNewClientModal = ref(false)
    
    const filters = ref({
      type: '',
      sort: 'name'
    })

    // Données des clients selon la maquette
    const clients = ref([
      {
        id: '1001',
        name: 'Dupont SAS',
        type: 'Entreprise',
        contact: 'contact@dupont-sas.fr',
        phone: '01 23 45 67 89',
        orders: 12,
        caTotal: '24 950 €',
        status: 'Actif'
      },
      {
        id: '1002',
        name: 'Martin & Cie',
        type: 'Entreprise',
        contact: 'info@martin-cie.fr',
        phone: '01 34 56 78 90',
        orders: 8,
        caTotal: '18 320 €',
        status: 'Actif'
      },
      {
        id: '1003',
        name: 'Bernard FR',
        type: 'Entreprise',
        contact: 'contact@bernard-fr.com',
        phone: '01 45 67 89 01',
        orders: 15,
        caTotal: '32 760 €',
        status: 'Actif'
      },
      {
        id: '1004',
        name: 'Artist DEF',
        type: 'Particulier',
        contact: 'artist@email.com',
        phone: '01 56 78 90 12',
        orders: 3,
        caTotal: '1 450 €',
        status: 'Inactif'
      },
      {
        id: '1005',
        name: 'Durand SA',
        type: 'Entreprise',
        contact: 'contact@durand-sa.fr',
        phone: '01 67 89 01 23',
        orders: 22,
        caTotal: '45 680 €',
        status: 'Actif'
      }
    ])

    const filteredClients = computed(() => {
      let result = clients.value

      // Filtre par type
      if (filters.value.type) {
        result = result.filter(client => client.type === filters.value.type)
      }

      // Filtre par recherche
      if (searchQuery.value) {
        result = result.filter(client =>
          client.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          client.contact.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          client.id.includes(searchQuery.value)
        )
      }

      // Tri
      switch (filters.value.sort) {
        case 'name':
          result.sort((a, b) => a.name.localeCompare(b.name))
          break
        case 'ca':
          result.sort((a, b) => parseFloat(b.caTotal.replace(/[€\s]/g, '').replace(',', '.')) - parseFloat(a.caTotal.replace(/[€\s]/g, '').replace(',', '.')))
          break
        case 'orders':
          result.sort((a, b) => b.orders - a.orders)
          break
      }

      return result
    })

    const getClientInitials = (name) => {
      return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
    }

    const getTypeClass = (type) => {
      return type === 'Entreprise' ? 'type-company' : 'type-individual'
    }

    const getStatusClass = (status) => {
      return status === 'Actif' ? 'status-active' : 'status-inactive'
    }

    const viewClient = (clientId) => {
      console.log('Voir client:', clientId)
      // Logique pour voir le détail du client
    }

    const editClient = (clientId) => {
      console.log('Modifier client:', clientId)
      // Logique pour modifier le client
    }

    return {
      searchQuery,
      showNewClientModal,
      filters,
      clients,
      filteredClients,
      getClientInitials,
      getTypeClass,
      getStatusClass,
      viewClient,
      editClient
    }
  }
}
</script>

<style scoped>
.clients-page {
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

.new-client-button {
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

.new-client-button:hover {
  background: #2563EB;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.new-client-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
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
  min-width: 140px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #00B8D4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

.filter-button,
.export-button {
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

.filter-button:hover,
.export-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.filter-button svg,
.export-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* STATISTIQUES */
.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-title {
  font-size: 14px;
  font-weight: 500;
  color: #64748B;
  margin: 0 0 0.5rem 0;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
  line-height: 1.2;
}

.stat-subtitle {
  font-size: 12px;
  color: #059669;
  margin: 0;
  font-weight: 500;
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

.clients-table {
  width: 100%;
  border-collapse: collapse;
}

.clients-table th {
  background: #F8FAFC;
  text-align: left;
  padding: 1rem;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #E2E8F0;
  white-space: nowrap;
}

.clients-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
  vertical-align: middle;
}

.clients-table tr:hover {
  background: #F8FAFC;
}

/* CLIENT INFO */
.client-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  min-width: 200px;
}

.client-avatar {
  width: 40px;
  height: 40px;
  background: #00B8D4;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 12px;
  flex-shrink: 0;
}

.client-name {
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
  line-height: 1.2;
}

.client-id {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

/* BADGES */
.type-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.type-company {
  background: #DBEAFE;
  color: #1E40AF;
}

.type-individual {
  background: #FEF3C7;
  color: #92400E;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.status-active {
  background: #D1FAE5;
  color: #047857;
}

.status-inactive {
  background: #FEF2F2;
  color: #DC2626;
}

/* CONTACT INFO */
.contact-info {
  min-width: 180px;
}

.contact-email {
  font-weight: 500;
  color: #334155;
  margin: 0 0 0.25rem 0;
}

.contact-phone {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

/* TOTAUX */
.orders-count,
.ca-amount {
  font-weight: 600;
  color: #0F172A;
  text-align: center;
}

/* ACTIONS */
.actions-cell {
  width: 100px;
}

.action-buttons {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  justify-content: center;
}

.action-button {
  background: none;
  border: 1px solid #E2E8F0;
  border-radius: 6px;
  padding: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.view-button {
  color: #3B82F6;
}

.view-button:hover {
  background: #EFF6FF;
  border-color: #3B82F6;
}

.edit-button {
  color: #059669;
}

.edit-button:hover {
  background: #ECFDF5;
  border-color: #059669;
}

.action-button svg {
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
  
  .clients-table {
    font-size: 12px;
  }
  
  .clients-table th,
  .clients-table td {
    padding: 0.75rem 0.5rem;
  }
  
  .client-info {
    min-width: 150px;
  }
  
  .contact-info {
    min-width: 120px;
  }
}

@media (max-width: 640px) {
  .clients-table th:nth-child(3),
  .clients-table td:nth-child(3),
  .clients-table th:nth-child(4),
  .clients-table td:nth-child(4) {
    display: none;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>
