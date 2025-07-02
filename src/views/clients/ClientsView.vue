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
          <span>Nouveau client</span>
        </button>
      </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="stats-section">
      <div class="stat-card">
        <div class="stat-icon active-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Total clients</p>
          <p class="stat-value">{{ totalClients }}</p>
          <p class="stat-trend">{{ percentageChange }}% ce mois</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon new-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
            <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Nouveaux ce mois</p>
          <p class="stat-value">{{ newThisMonth }}</p>
          <p class="stat-trend">Croissance active</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon location-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Villes desservies</p>
          <p class="stat-value">{{ uniqueCities }}</p>
          <p class="stat-trend">Présence nationale</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon email-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="m22 7-10 5L2 7"/>
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Emails valides</p>
          <p class="stat-value">{{ validEmails }}%</p>
          <p class="stat-trend">Base de données fiable</p>
        </div>
      </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="filters-section">
      <div class="filter-group">
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
        
        <select v-model="filters.city" class="filter-select">
          <option value="">Toutes les villes</option>
          <option v-for="city in availableCities" :key="city" :value="city">{{ city }}</option>
        </select>
        
        <select v-model="filters.sort" class="filter-select">
          <option value="name">Trier par nom</option>
          <option value="recent">Plus récents</option>
          <option value="id">Par ID</option>
        </select>
        
        <button class="action-button" @click="resetFilters">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="1 4 1 10 7 10"/>
            <polyline points="23 20 23 14 17 14"/>
            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
          </svg>
          Réinitialiser
        </button>
        
        <button class="export-button" @click="exportClients">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7,10 12,15 17,10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Exporter
        </button>
      </div>
    </div>

    <!-- Tableau des clients -->
    <div class="table-section">
      <div class="table-card">
        <div class="table-header">
          <h3 class="table-title">Liste des clients</h3>
          <div class="table-stats">
            <span class="stat-item">
              <span class="stat-label">Affichés:</span>
              <span class="stat-value">{{ filteredClients.length }}</span>
            </span>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="loader"></div>
          <p>Chargement des clients...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-container">
          <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <p class="error-message">{{ error }}</p>
          <button @click="fetchClients" class="retry-button">Réessayer</button>
        </div>

        <!-- Table Content -->
        <div v-else class="table-container">
          <table class="clients-table">
            <thead>
              <tr>
                <th>Client</th>
                <th>Contact</th>
                <th>Ville</th>
                <th>Téléphone</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="client in filteredClients" :key="client.id_client">
                <td>
                  <div class="client-info">
                    <div class="client-avatar" :style="{ backgroundColor: getAvatarColor(client.id_client) }">
                      {{ getClientInitials(client) }}
                    </div>
                    <div class="client-details">
                      <p class="client-name">{{ client.contact_principal || client.raison_sociale }}</p>
                      <p class="client-id">ID #{{ client.id_client }}</p>
                    </div>
                  </div>
                </td>
                <td class="contact-info">
                  <p class="contact-email">{{ client.email }}</p>
                </td>
                <td class="address-info">
                  {{ getCityFromAddress(client) }}
                </td>
                <td class="phone-info">
                  {{ formatPhone(client.telephone) }}
                </td>
                <td class="actions">
                  <button class="action-btn secondary" @click="viewClient(client.id_client)" title="Voir détails">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>
                  <button class="action-btn secondary" @click="editClient(client.id_client)" title="Modifier">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                </td>
              </tr>
              <tr v-if="filteredClients.length === 0 && !loading">
                <td colspan="5" class="empty-message">
                  Aucun client trouvé
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Nouveau client -->
    <div v-if="showNewClientModal" class="modal-overlay" @click="showNewClientModal = false">
      <div class="modal-content modal-form" @click.stop>
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
          <form @submit.prevent="handleSubmitNewClient" class="client-form">
            <div class="form-grid">
              <!-- Informations entreprise -->
              <div class="form-section">
                <h4 class="form-section-title">Informations entreprise</h4>
                
                <div class="form-group">
                  <label for="raison_sociale" class="form-label">Raison sociale <span class="required">*</span></label>
                  <input
                    id="raison_sociale"
                    v-model="newClientForm.raison_sociale"
                    type="text"
                    class="form-input"
                    placeholder="SARL Exemple"
                    required
                    maxlength="100"
                  />
                </div>

                <div class="form-group">
                  <label for="nom_commercial" class="form-label">Nom commercial</label>
                  <input
                    id="nom_commercial"
                    v-model="newClientForm.nom_commercial"
                    type="text"
                    class="form-input"
                    placeholder="Exemple Distribution"
                    maxlength="100"
                  />
                </div>

                <div class="form-group">
                  <label for="secteur_activite" class="form-label">Secteur d'activité</label>
                  <input
                    id="secteur_activite"
                    v-model="newClientForm.secteur_activite"
                    type="text"
                    class="form-input"
                    placeholder="Distribution textile"
                    maxlength="100"
                  />
                </div>

                <div class="form-group">
                  <label for="siret" class="form-label">SIRET <span class="required">*</span></label>
                  <input
                    id="siret"
                    v-model="newClientForm.siret"
                    type="text"
                    class="form-input"
                    placeholder="12345678901234"
                    pattern="\d{14}"
                    required
                    maxlength="14"
                  />
                  <p class="form-hint">14 chiffres</p>
                </div>
              </div>

              <!-- Informations contact -->
              <div class="form-section">
                <h4 class="form-section-title">Contact</h4>
                
                <div class="form-group">
                  <label for="contact_principal" class="form-label">Contact principal <span class="required">*</span></label>
                  <input
                    id="contact_principal"
                    v-model="newClientForm.contact_principal"
                    type="text"
                    class="form-input"
                    placeholder="Jean Dupont"
                    required
                    maxlength="100"
                  />
                </div>

                <div class="form-group">
                  <label for="email" class="form-label">Email <span class="required">*</span></label>
                  <input
                    id="email"
                    v-model="newClientForm.email"
                    type="email"
                    class="form-input"
                    placeholder="contact@entreprise.fr"
                    required
                  />
                </div>

                <div class="form-group">
                  <label for="telephone" class="form-label">Téléphone <span class="required">*</span></label>
                  <input
                    id="telephone"
                    v-model="newClientForm.telephone"
                    type="tel"
                    class="form-input"
                    placeholder="0123456789"
                    pattern="0[1-9][0-9]{8}"
                    required
                  />
                  <p class="form-hint">Format: 10 chiffres commençant par 0</p>
                </div>
              </div>

              <!-- Adresse -->
              <div class="form-section full-width">
                <h4 class="form-section-title">Adresse</h4>
                
                <div class="form-group">
                  <label for="adresse" class="form-label">Adresse <span class="required">*</span></label>
                  <input
                    id="adresse"
                    v-model="newClientForm.adresse"
                    type="text"
                    class="form-input"
                    placeholder="123 rue de la Paix"
                    required
                    maxlength="255"
                  />
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="code_postal" class="form-label">Code postal <span class="required">*</span></label>
                    <input
                      id="code_postal"
                      v-model="newClientForm.code_postal"
                      type="text"
                      class="form-input"
                      placeholder="75001"
                      pattern="\d{5}"
                      required
                      maxlength="5"
                    />
                    <p class="form-hint">5 chiffres</p>
                  </div>

                  <div class="form-group" style="flex: 2;">
                    <label for="ville" class="form-label">Ville <span class="required">*</span></label>
                    <input
                      id="ville"
                      v-model="newClientForm.ville"
                      type="text"
                      class="form-input"
                      placeholder="Paris"
                      required
                      maxlength="100"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Messages d'erreur -->
            <div v-if="formError" class="form-error">
              <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
              </svg>
              {{ formError }}
            </div>

            <!-- Actions -->
            <div class="modal-actions">
              <button type="button" class="modal-btn secondary" @click="showNewClientModal = false">
                Annuler
              </button>
              <button type="submit" class="modal-btn primary" :disabled="submitting">
                <span v-if="!submitting">Créer le client</span>
                <span v-else class="loading-text">
                  <svg class="spinner" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"/>
                    <path d="M12 2a10 10 0 0 1 0 20" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                  </svg>
                  Création...
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Détails client -->
    <div v-if="showDetailsModal" class="modal-overlay" @click="showDetailsModal = false">
      <div class="modal-content modal-details" @click.stop>
        <div class="modal-header">
          <h3>Détails du client</h3>
          <button @click="showDetailsModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="selectedClient">
          <div class="details-grid">
            <div class="details-section">
              <h4 class="section-subtitle">Informations personnelles</h4>
              <div class="detail-row">
                <span class="detail-label">ID Client:</span>
                <span class="detail-value">#{{ String(selectedClient.id_client).padStart(4, '0') }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Nom:</span>
                <span class="detail-value">{{ selectedClient.contact_principal }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Raison sociale:</span>
                <span class="detail-value">{{ selectedClient.raison_sociale }}</span>
              </div>
              <div class="detail-row" v-if="selectedClient.nom_commercial">
                <span class="detail-label">Nom commercial:</span>
                <span class="detail-value">{{ selectedClient.nom_commercial }}</span>
              </div>
              <div class="detail-row" v-if="selectedClient.secteur_activite">
                <span class="detail-label">Secteur d'activité:</span>
                <span class="detail-value">{{ selectedClient.secteur_activite }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Date d'inscription:</span>
                <span class="detail-value">{{ new Date().toLocaleDateString('fr-FR') }}</span>
              </div>
            </div>

            <div class="details-section">
              <h4 class="section-subtitle">Informations de contact</h4>
              <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ selectedClient.email }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Téléphone:</span>
                <span class="detail-value">{{ formatPhone(selectedClient.telephone) }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Adresse:</span>
                <span class="detail-value">{{ selectedClient.adresse }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Code postal / Ville:</span>
                <span class="detail-value">{{ selectedClient.code_postal }} {{ selectedClient.ville }}</span>
              </div>
            </div>
            
            <div class="details-section full-width">
              <h4 class="section-subtitle">Statistiques client</h4>
              <div class="detail-row">
                <span class="detail-label">SIRET:</span>
                <span class="detail-value">{{ selectedClient.siret }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Date de création:</span>
                <span class="detail-value">{{ selectedClient.date_creation ? new Date(selectedClient.date_creation).toLocaleDateString('fr-FR') : 'Non renseignée' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Nombre de commandes:</span>
                <span class="detail-value">{{ Math.floor(Math.random() * 20) + 1 }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Chiffre d'affaires total:</span>
                <span class="detail-value amount">{{ formatCurrency(Math.random() * 50000) }}</span>
              </div>
            </div>
          </div>
          
          <div class="modal-actions">
            <button class="modal-btn secondary" @click="showDetailsModal = false">
              Fermer
            </button>
            <button class="modal-btn primary" @click="() => { showDetailsModal = false; editClient(selectedClient.id_client); }">
              Modifier le client
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Modifier client -->
    <div v-if="showEditModal" class="modal-overlay" @click="showEditModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Modifier le client</h3>
          <button @click="showEditModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="todo-message">
            <svg class="todo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M12 2L2 7l10 5 10-5-10-5z"/>
              <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
            <h4>Fonctionnalité en cours de développement</h4>
            <p>La modification des informations client sera bientôt disponible.</p>
            <div class="client-preview" v-if="selectedClient">
              <p><strong>Client sélectionné:</strong> {{ selectedClient.raison_sociale }}</p>
              <p><strong>Contact:</strong> {{ selectedClient.contact_principal }}</p>
              <p><strong>Email:</strong> {{ selectedClient.email }}</p>
            </div>
          </div>
          <div class="modal-actions">
            <button class="modal-btn secondary" @click="showEditModal = false">
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'

export default {
  name: 'ClientsView',
  setup() {
    // États réactifs
    const clients = ref([])
    const loading = ref(true)
    const error = ref(null)
    const searchQuery = ref('')
    const showNewClientModal = ref(false)
    const showDetailsModal = ref(false)
    const showEditModal = ref(false)
    const selectedClient = ref(null)
    const submitting = ref(false)
    const formError = ref('')
    
    // Formulaire nouveau client
    const newClientForm = ref({
      raison_sociale: '',
      nom_commercial: '',
      email: '',
      telephone: '',
      adresse: '',
      code_postal: '',
      ville: '',
      siret: '',
      secteur_activite: '',
      contact_principal: ''
    })
    
    const filters = ref({
      city: '',
      sort: 'name'
    })

    // Fonction pour récupérer les clients depuis l'API
    const fetchClients = async () => {
      loading.value = true
      error.value = null
      
      try {
        const response = await fetch(import.meta.env.VITE_API_URL + "get_table?table=client", {
          method: "GET",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          credentials: 'include',  
        })

        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`)
        }

        const data = await response.json()
        
        if (data.success && data.data) {
          clients.value = data.data
        } else {
          throw new Error('Format de données invalide')
        }
      } catch (err) {
        console.error('Erreur lors du chargement des clients:', err)
        error.value = 'Impossible de charger les clients. Veuillez réessayer.'
      } finally {
        loading.value = false
      }
    }

    // Computed properties pour les statistiques
    const totalClients = computed(() => clients.value.length)
    
    const percentageChange = computed(() => {
      // Simulé pour l'instant
      return '+12'
    })
    
    const newThisMonth = computed(() => {
      // Simulé pour l'instant
      return Math.floor(clients.value.length * 0.15)
    })

    const uniqueCities = computed(() => {
      const cities = new Set(clients.value.map(client => client.ville).filter(ville => ville))
      return cities.size
    })

    const validEmails = computed(() => {
      const valid = clients.value.filter(client => client.email && client.email.includes('@')).length
      return clients.value.length > 0 ? Math.round((valid / clients.value.length) * 100) : 100
    })

    // Villes disponibles pour le filtre
    const availableCities = computed(() => {
      const cities = new Set(clients.value.map(client => client.ville).filter(ville => ville))
      return Array.from(cities).sort()
    })

    // Clients filtrés
    const filteredClients = computed(() => {
      let result = [...clients.value]

      // Filtre par ville
      if (filters.value.city) {
        result = result.filter(client => client.ville === filters.value.city)
      }

      // Filtre par recherche
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        result = result.filter(client => {
          const contactName = client.contact_principal ? client.contact_principal.toLowerCase() : ''
          const raisonSociale = client.raison_sociale ? client.raison_sociale.toLowerCase() : ''
          return (
            contactName.includes(query) ||
            raisonSociale.includes(query) ||
            client.email.toLowerCase().includes(query) ||
            client.id_client.toString().includes(query)
          )
        })
      }

      // Tri
      switch (filters.value.sort) {
        case 'name':
          result.sort((a, b) => (a.raison_sociale || '').localeCompare(b.raison_sociale || ''))
          break
        case 'recent':
          result.sort((a, b) => b.id_client - a.id_client)
          break
        case 'id':
          result.sort((a, b) => a.id_client - b.id_client)
          break
      }

      return result
    })

    // Fonctions utilitaires
    const getClientInitials = (client) => {
      if (client.contact_principal) {
        const parts = client.contact_principal.split(' ')
        const firstInitial = parts[0] ? parts[0][0].toUpperCase() : ''
        const lastInitial = parts.length > 1 && parts[parts.length - 1] ? parts[parts.length - 1][0].toUpperCase() : ''
        return firstInitial + lastInitial
      }
      return '??'
    }

    const getCityFromAddress = (client) => {
      return client.ville || 'Non renseigné'
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

    const getAvatarColor = (id) => {
      const colors = ['#00B8D4', '#2563EB', '#059669', '#D97706', '#7C3AED', '#DC2626']
      return colors[id % colors.length]
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }

    const resetFilters = () => {
      searchQuery.value = ''
      filters.value.city = ''
      filters.value.sort = 'name'
    }

    const exportClients = () => {
      // Fonction d'export
      console.log('Export des clients...')
      const csv = [
        ['ID', 'Raison sociale', 'Nom commercial', 'Contact principal', 'Email', 'Téléphone', 'Adresse', 'Code postal', 'Ville', 'SIRET', 'Secteur activité'],
        ...clients.value.map(c => [
          c.id_client,
          c.raison_sociale,
          c.nom_commercial || '',
          c.contact_principal,
          c.email,
          c.telephone,
          c.adresse,
          c.code_postal,
          c.ville,
          c.siret,
          c.secteur_activite || ''
        ])
      ].map(row => row.join(',')).join('\n')
      
      const blob = new Blob([csv], { type: 'text/csv' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = 'clients.csv'
      a.click()
      URL.revokeObjectURL(url)
    }

    const viewClient = (clientId) => {
      const client = clients.value.find(c => c.id_client === clientId)
      if (client) {
        selectedClient.value = client
        showDetailsModal.value = true
      }
    }

    const editClient = (clientId) => {
      const client = clients.value.find(c => c.id_client === clientId)
      if (client) {
        selectedClient.value = client
        showEditModal.value = true
      }
    }

    // Fonction pour créer un nouveau client
    const handleSubmitNewClient = async () => {
      formError.value = ''
      submitting.value = true

      try {
        // Créer les données du formulaire
        const formData = new URLSearchParams()
        Object.keys(newClientForm.value).forEach(key => {
          formData.append(key, newClientForm.value[key])
        })

        const response = await fetch(import.meta.env.VITE_API_URL + "new_client", {
          method: "POST",
          body: formData,
          credentials: 'include',
        })

        const data = await response.json()

        if (!response.ok) {
          if (response.status === 409) {
            throw new Error('Cet email est déjà utilisé')
          } else if (response.status === 400) {
            throw new Error(data.message || 'Veuillez vérifier tous les champs')
          } else {
            throw new Error(data.message || 'Une erreur est survenue')
          }
        }

        if (data.success) {
          // Réinitialiser le formulaire
          Object.keys(newClientForm.value).forEach(key => {
            newClientForm.value[key] = ''
          })
          
          // Fermer la modale
          showNewClientModal.value = false
          
          // Recharger la liste des clients
          await fetchClients()
          
          // Message de succès (vous pouvez implémenter un système de toast si vous voulez)
          console.log('Client créé avec succès')
        }
      } catch (err) {
        console.error('Erreur lors de la création du client:', err)
        formError.value = err.message || 'Impossible de créer le client. Veuillez réessayer.'
      } finally {
        submitting.value = false
      }
    }

    // Réinitialiser le formulaire quand on ferme la modale
    const resetNewClientForm = () => {
      Object.keys(newClientForm.value).forEach(key => {
        newClientForm.value[key] = ''
      })
      formError.value = ''
    }

    // Watcher pour réinitialiser le formulaire quand on ferme la modale
    watch(showNewClientModal, (newValue) => {
      if (!newValue) {
        resetNewClientForm()
      }
    })

    // Charger les données au montage
    onMounted(() => {
      fetchClients()
    })

    return {
      clients,
      loading,
      error,
      searchQuery,
      showNewClientModal,
      showDetailsModal,
      showEditModal,
      selectedClient,
      submitting,
      formError,
      newClientForm,
      filters,
      totalClients,
      percentageChange,
      newThisMonth,
      uniqueCities,
      validEmails,
      availableCities,
      filteredClients,
      fetchClients,
      getClientInitials,
      getCityFromAddress,
      formatPhone,
      formatCurrency,
      getAvatarColor,
      resetFilters,
      exportClients,
      viewClient,
      editClient,
      handleSubmitNewClient,
      resetNewClientForm
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

.new-client-button:hover {
  background: #0891A6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
}

.new-client-button svg {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

/* STATISTIQUES */
.stats-section {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
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
  transition: all 0.2s ease;
}

.stat-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
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

.active-icon {
  background: #F0FDF4;
  color: #059669;
}

.new-icon {
  background: #EFF6FF;
  color: #2563EB;
}

.location-icon {
  background: #FEF3C7;
  color: #D97706;
}

.email-icon {
  background: #F3E8FF;
  color: #7C3AED;
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 13px;
  color: #64748B;
  margin: 0 0 4px 0;
  font-weight: 500;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
  line-height: 1;
}

.stat-trend {
  font-size: 12px;
  color: #059669;
  margin: 4px 0 0 0;
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

.action-button,
.export-button {
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

.action-button:hover,
.export-button:hover {
  background: #2563EB;
  transform: translateY(-1px);
}

.action-button svg,
.export-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* LOADING & ERROR */
.loading-container,
.error-container {
  text-align: center;
  padding: 4rem 2rem;
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

.error-icon {
  width: 48px;
  height: 48px;
  color: #DC2626;
  margin: 0 auto 1rem;
}

.error-message {
  color: #DC2626;
  margin-bottom: 1rem;
  font-weight: 500;
}

.retry-button {
  background: #00B8D4;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 0.5rem 1.5rem;
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

.table-stats .stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 14px;
}

.table-stats .stat-label {
  color: #64748B;
}

.table-stats .stat-value {
  font-weight: 600;
  color: #0F172A;
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
}

.clients-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
  vertical-align: middle;
}

.clients-table tbody tr:hover {
  background: #F8FAFC;
}

.clients-table tbody tr:last-child td {
  border-bottom: none;
}

/* CLIENT INFO */
.client-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.client-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 14px;
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

/* CONTACT INFO */
.contact-email {
  font-weight: 500;
  color: #334155;
  margin: 0;
}

.address-info,
.phone-info {
  color: #64748B;
  font-size: 14px;
}

.empty-message {
  text-align: center;
  color: #64748B;
  font-style: italic;
  padding: 2rem;
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
  overflow-y: auto;
  max-height: calc(90vh - 120px);
}

/* MODAL DÉTAILS */
.modal-details {
  max-width: 700px;
}

.details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
  text-align: left;
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

/* TODO MESSAGE */
.todo-message {
  text-align: center;
  padding: 2rem;
}

.todo-icon {
  width: 64px;
  height: 64px;
  color: #00B8D4;
  margin: 0 auto 1.5rem;
  stroke-width: 1.5;
}

.todo-message h4 {
  font-size: 18px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.5rem 0;
}

.todo-message p {
  color: #64748B;
  margin: 0 0 2rem 0;
}

.client-preview {
  background: #F8FAFC;
  padding: 1rem;
  border-radius: 8px;
  text-align: left;
}

.client-preview p {
  margin: 0.5rem 0;
  font-size: 14px;
  color: #334155;
}

.client-preview strong {
  color: #0F172A;
}

/* FORMULAIRE */
.modal-form {
  max-width: 800px;
}

.client-form {
  width: 100%;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-bottom: 1.5rem;
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-section.full-width {
  grid-column: 1 / -1;
}

.form-section-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.5rem 0;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #E2E8F0;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-size: 14px;
  font-weight: 500;
  color: #334155;
}

.form-label .required {
  color: #DC2626;
}

.form-input {
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 6px;
  padding: 0.75rem 1rem;
  font-size: 14px;
  color: #0F172A;
  transition: all 0.2s ease;
  width: 100%;
}

.form-input:focus {
  outline: none;
  border-color: #00B8D4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

.form-input::placeholder {
  color: #94A3B8;
}

.form-hint {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

.form-error {
  background: #FEF2F2;
  color: #DC2626;
  padding: 1rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
  font-size: 14px;
}

.form-error .error-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.loading-text {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.spinner {
  width: 16px;
  height: 16px;
  animation: spin 1s linear infinite;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .new-client-button {
    width: 100%;
    justify-content: center;
  }
  
  .stats-section {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .filter-group {
    flex-direction: column;
  }
  
  .search-container,
  .filter-select,
  .action-button,
  .export-button {
    width: 100%;
    max-width: none;
  }
  
  .details-grid {
    grid-template-columns: 1fr;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .stats-section {
    grid-template-columns: 1fr;
  }
  
  .table-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .clients-table {
    font-size: 12px;
  }
  
  .clients-table th,
  .clients-table td {
    padding: 0.75rem 0.5rem;
  }
  
  .actions {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .modal-content {
    width: 95%;
    margin: 1rem;
  }
  
  .modal-actions {
    flex-direction: column;
  }
  
  .modal-btn {
    width: 100%;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .clients-table th:nth-child(3),
  .clients-table td:nth-child(3),
  .clients-table th:nth-child(4),
  .clients-table td:nth-child(4) {
    display: none;
  }
}
</style>