<template>
  <div class="supplier-detail-page">
    <!-- En-tête avec boutons -->
    <div class="page-header">
      <div class="header-left">
        <button class="back-button" @click="goBack">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="15,18 9,12 15,6" />
          </svg>
          <span>Retour</span>
        </button>
        <h1 class="page-title">Détail du fournisseur</h1>
      </div>
      <div class="header-actions">
        <button class="print-button" @click="printSupplier">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="6,9 6,2 18,2 18,9" />
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
            <rect x="6" y="14" width="12" height="8" />
          </svg>
          <span>Imprimer</span>
        </button>
        <button class="edit-button" @click="editSupplier">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
          </svg>
          <span>Modifier</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loader"></div>
      <p>Chargement des informations...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <p class="error-message">{{ error }}</p>
      <button @click="fetchSupplier" class="retry-button">Réessayer</button>
    </div>

    <!-- Content -->
    <template v-else-if="supplier">
      <!-- Informations principales -->
      <div class="info-section">
        <div class="info-card">
          <div class="supplier-header">
            <div class="supplier-logo" :style="{ backgroundColor: getSupplierColor() }">
              {{ getSupplierInitials() }}
            </div>
            <div class="supplier-main">
              <h2 class="supplier-name">{{ supplier.nom }}</h2>
              <p class="supplier-since">Fournisseur actif depuis {{ getYearFromDate(supplier.date_creation) }}</p>
              <span v-if="supplier.actif" class="status-badge active">Actif</span>
              <span v-else class="status-badge inactive">Inactif</span>
            </div>
          </div>

          <div class="info-grid">
            <div class="info-item">
              <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                <polyline points="22,6 12,13 2,6" />
              </svg>
              <div class="info-content">
                <span class="info-label">EMAIL</span>
                <span class="info-value">{{ supplier.email }}</span>
              </div>
            </div>

            <div class="info-item">
              <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path
                  d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
              </svg>
              <div class="info-content">
                <span class="info-label">TÉLÉPHONE</span>
                <span class="info-value">{{ formatPhone(supplier.telephone) }}</span>
              </div>
            </div>

            <div class="info-item">
              <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                <circle cx="12" cy="10" r="3" />
              </svg>
              <div class="info-content">
                <span class="info-label">ADRESSE</span>
                <span class="info-value">
                  {{ supplier.adresse }}<br>
                  {{ supplier.code_postal }} {{ supplier.ville }}, {{ supplier.pays }}
                </span>
              </div>
            </div>

            <div v-if="supplier.site_web" class="info-item">
              <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10"/>
                <line x1="2" y1="12" x2="22" y2="12"/>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
              </svg>
              <div class="info-content">
                <span class="info-label">SITE WEB</span>
                <a :href="supplier.site_web" target="_blank" class="info-value link">{{ supplier.site_web }}</a>
              </div>
            </div>
          </div>

          <div class="contact-section">
            <h3 class="section-title">Contact principal</h3>
            <div class="contact-person">
              <div class="contact-avatar">{{ getContactInitials() }}</div>
              <div class="contact-info">
                <p class="contact-name">{{ supplier.contact_prenom }} {{ supplier.contact_nom }}</p>
                <p class="contact-role">Contact commercial</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-card">
          <h3 class="card-title">Informations commerciales</h3>

          <div class="stats-grid">
            <div class="stat-item">
              <p class="stat-label">Note qualité</p>
              <div class="rating-display">
                <p class="stat-value">{{ supplier.note_qualite || 'N/A' }}</p>
                <div v-if="supplier.note_qualite" class="stars">
                  <span v-for="i in 5" :key="i" class="star"
                    :class="{ 'filled': i <= Math.floor(parseFloat(supplier.note_qualite)) }">★</span>
                </div>
              </div>
            </div>

            <div class="stat-item">
              <p class="stat-label">Délai de livraison</p>
              <p class="stat-value">{{ supplier.delai_livraison }} <span class="unit">jours</span></p>
            </div>

            <div class="stat-item">
              <p class="stat-label">ID Fournisseur</p>
              <p class="stat-value">#{{ String(supplier.id_fournisseur).padStart(4, '0') }}</p>
            </div>
          </div>

          <div class="metrics-section">
            <div class="metric-item">
              <span class="metric-label">Conditions de paiement</span>
              <span class="metric-value">{{ supplier.conditions_paiement || 'Non spécifiées' }}</span>
            </div>

            <div class="metric-item">
              <span class="metric-label">Date de création</span>
              <span class="metric-value">{{ formatDate(supplier.date_creation) }}</span>
            </div>

            <div class="metric-item">
              <span class="metric-label">Dernière modification</span>
              <span class="metric-value">{{ formatDate(supplier.date_modification) }}</span>
            </div>
          </div>

          <!-- Stats supplémentaires (simulées pour l'instant) -->
          <div class="additional-stats">
            <h4 class="subsection-title">Statistiques d'activité</h4>
            <div class="mini-stats">
              <div class="mini-stat">
                <span class="mini-label">Commandes en cours</span>
                <span class="mini-value">{{ Math.floor(Math.random() * 10) + 1 }}</span>
              </div>
              <div class="mini-stat">
                <span class="mini-label">Articles fournis</span>
                <span class="mini-value">{{ Math.floor(Math.random() * 50) + 10 }}</span>
              </div>
              <div class="mini-stat">
                <span class="mini-label">CA annuel</span>
                <span class="mini-value">{{ formatCurrency(Math.random() * 100000 + 10000) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Onglets -->
      <div class="tabs-section">
        <div class="tabs-nav">
          <button v-for="tab in tabs" :key="tab.id" class="tab-button" :class="{ 'active': activeTab === tab.id }"
            @click="activeTab = tab.id">
            {{ tab.label }}
          </button>
        </div>

        <!-- Contenu des onglets -->
        <div class="tab-content">
          <!-- Onglet Produits -->
          <div v-if="activeTab === 'products'" class="products-section">
            <div class="table-header">
              <h3 class="table-title">Produits fournis</h3>
              <button class="export-button" @click="exportProducts">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                  <polyline points="7,10 12,15 17,10" />
                  <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Exporter
              </button>
            </div>

            <div v-if="loadingProducts" class="loading-mini">
              <div class="loader-mini"></div>
              <p>Chargement des produits...</p>
            </div>

            <div v-else-if="products.length === 0" class="empty-state">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                <line x1="12" y1="22.08" x2="12" y2="12"/>
              </svg>
              <p>Aucun produit fourni par ce fournisseur</p>
            </div>

            <div v-else class="table-container">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>RÉFÉRENCE</th>
                    <th>PRODUIT</th>
                    <th>CATÉGORIE</th>
                    <th>PRIX UNITAIRE</th>
                    <th>EN STOCK</th>
                    <th>SEUIL</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="product in products" :key="product.id_article">
                    <td class="reference">{{ product.reference || `ART${product.id_article}` }}</td>
                    <td>{{ product.nom }}</td>
                    <td>{{ product.categorie || 'Non catégorisé' }}</td>
                    <td class="price">{{ formatCurrency(product.prix) }}</td>
                    <td class="stock" :class="{ 'low-stock': product.quantite_stock <= product.seuil_alerte }">
                      {{ product.quantite_stock }}
                    </td>
                    <td class="threshold">{{ product.seuil_alerte }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Onglet Commandes -->
          <div v-else-if="activeTab === 'orders'" class="placeholder-section">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
              <polyline points="14,2 14,8 20,8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            <h4>Commandes</h4>
            <p>Section des commandes en cours de développement</p>
          </div>

          <!-- Onglet Livraisons -->
          <div v-else-if="activeTab === 'deliveries'" class="placeholder-section">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <rect x="1" y="3" width="15" height="13"/>
              <polygon points="16,3 19,7 19,13 16,13"/>
              <circle cx="5.5" cy="18.5" r="2.5"/>
              <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
            <h4>Livraisons</h4>
            <p>Section des livraisons en cours de développement</p>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

export default {
  name: 'SupplierDetailView',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const activeTab = ref('products')
    const supplier = ref(null)
    const products = ref([])
    const loading = ref(true)
    const loadingProducts = ref(true)
    const error = ref(null)

    const tabs = ref([
      { id: 'products', label: 'Produits' },
      { id: 'orders', label: 'Commandes' },
      { id: 'deliveries', label: 'Livraisons' }
    ])

    // Récupérer l'ID du fournisseur depuis l'URL
    const supplierId = computed(() => {
      // Essayer différentes façons de récupérer l'ID
      return route.params.id || route.params.supplierId || route.query.id
    })

    // Fonction pour récupérer les informations du fournisseur
    const fetchSupplier = async () => {
      console.log('fetchSupplier appelé avec ID:', supplierId.value)
      loading.value = true
      error.value = null

      try {
        console.log('API URL:', import.meta.env.VITE_API_URL + "get_table?table=fournisseur")
        const response = await fetch(import.meta.env.VITE_API_URL + "get_table?table=fournisseur", {
          method: "GET",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          credentials: 'include',
        })

        console.log('Response status:', response.status)
        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`)
        }

        const data = await response.json()
        console.log('Data reçue:', data)
        
        if (data.success && data.data) {
          // Trouver le fournisseur par ID
          const foundSupplier = data.data.find(f => f.id_fournisseur == supplierId.value)
          if (foundSupplier) {
            supplier.value = foundSupplier
            console.log('Fournisseur trouvé:', foundSupplier)
          } else {
            throw new Error('Fournisseur introuvable')
          }
        } else {
          throw new Error('Format de données invalide')
        }
      } catch (err) {
        console.error('Erreur lors du chargement du fournisseur:', err)
        error.value = 'Impossible de charger les informations du fournisseur.'
      } finally {
        loading.value = false
      }
    }

    // Fonction pour récupérer les produits du fournisseur
    const fetchProducts = async () => {
      loadingProducts.value = true

      try {
        const response = await fetch(import.meta.env.VITE_API_URL + "get_table?table=article", {
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
          // Filtrer les articles par fournisseur (à adapter selon votre structure)
          // Pour l'instant, on simule avec des données aléatoires
          products.value = data.data.slice(0, 5)
        }
      } catch (err) {
        console.error('Erreur lors du chargement des produits:', err)
      } finally {
        loadingProducts.value = false
      }
    }

    // Fonctions utilitaires
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount || 0)
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return date.toLocaleDateString('fr-FR')
    }

    const formatPhone = (phone) => {
      if (!phone) return 'Non renseigné'
      const cleaned = phone.toString().replace(/\D/g, '')
      if (cleaned.length === 10) {
        return cleaned.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5')
      }
      return phone
    }

    const getSupplierInitials = () => {
      if (!supplier.value) return ''
      const words = supplier.value.nom.split(' ')
      return words.map(w => w[0]).join('').toUpperCase().substring(0, 2)
    }

    const getContactInitials = () => {
      if (!supplier.value) return ''
      const firstInitial = supplier.value.contact_prenom ? supplier.value.contact_prenom[0] : ''
      const lastInitial = supplier.value.contact_nom ? supplier.value.contact_nom[0] : ''
      return (firstInitial + lastInitial).toUpperCase()
    }

    const getSupplierColor = () => {
      const colors = ['#3B82F6', '#00B8D4', '#059669', '#D97706', '#7C3AED', '#DC2626']
      const id = supplier.value?.id_fournisseur || 0
      return colors[id % colors.length]
    }

    const getYearFromDate = (dateString) => {
      if (!dateString) return 'N/A'
      return new Date(dateString).getFullYear()
    }

    const goBack = () => {
      router.push('/suppliers')
    }

    const printSupplier = () => {
      window.print()
    }

    const editSupplier = () => {
      router.push(`/suppliers/${supplierId.value}/edit`)
    }

    const exportProducts = () => {
      if (!products.value.length) return

      const csv = [
        ['Référence', 'Produit', 'Catégorie', 'Prix unitaire', 'Stock', 'Seuil'],
        ...products.value.map(p => [
          p.reference || `ART${p.id_article}`,
          p.nom,
          p.categorie || 'Non catégorisé',
          p.prix,
          p.quantite_stock,
          p.seuil_alerte
        ])
      ].map(row => row.join(',')).join('\n')
      
      const blob = new Blob([csv], { type: 'text/csv' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `produits_${supplier.value.nom.replace(/\s+/g, '_')}.csv`
      a.click()
      URL.revokeObjectURL(url)
    }

    // Charger les données au montage
    onMounted(() => {
      console.log('Component mounted')
      console.log('Route params:', route.params)
      console.log('Route query:', route.query)
      console.log('Supplier ID:', supplierId.value)
      
      // Pour le développement, on peut charger le premier fournisseur si pas d'ID
      if (!supplierId.value) {
        console.warn('Pas d\'ID fournisseur trouvé, chargement du premier fournisseur')
        // Charger quand même les données pour récupérer le premier fournisseur
        fetchAllSuppliers()
      } else {
        fetchSupplier()
        fetchProducts()
      }
    })

    // Fonction pour récupérer tous les fournisseurs et sélectionner le premier
    const fetchAllSuppliers = async () => {
      loading.value = true
      error.value = null

      try {
        const response = await fetch(import.meta.env.VITE_API_URL + "get_table?table=fournisseur", {
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
        console.log('API Response:', data)
        
        if (data.success && data.data && data.data.length > 0) {
          // Prendre le premier fournisseur
          supplier.value = data.data[0]
          console.log('Premier fournisseur chargé:', supplier.value)
          // Charger aussi les produits
          fetchProducts()
        } else {
          throw new Error('Aucun fournisseur trouvé')
        }
      } catch (err) {
        console.error('Erreur lors du chargement des fournisseurs:', err)
        error.value = 'Impossible de charger les informations du fournisseur.'
      } finally {
        loading.value = false
      }
    }

    return {
      activeTab,
      supplier,
      products,
      loading,
      loadingProducts,
      error,
      tabs,
      supplierId,
      fetchSupplier,
      fetchProducts,
      fetchAllSuppliers,
      formatCurrency,
      formatDate,
      formatPhone,
      getSupplierInitials,
      getContactInitials,
      getSupplierColor,
      getYearFromDate,
      goBack,
      printSupplier,
      editSupplier,
      exportProducts
    }
  }
}
</script>

<style scoped>
.supplier-detail-page {
  padding: 0;
  font-family: 'Inter', sans-serif;
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

.loader-mini {
  width: 24px;
  height: 24px;
  border: 2px solid #F1F5F9;
  border-top-color: #00B8D4;
  border-radius: 50%;
  margin: 0 auto 0.5rem;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-mini {
  text-align: center;
  padding: 2rem;
  color: #64748B;
  font-size: 14px;
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

/* EN-TÊTE */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  gap: 2rem;
  flex-wrap: wrap;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.back-button {
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

.back-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.back-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 0.75rem;
}

.print-button,
.edit-button {
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

.print-button {
  background: white;
  color: #64748B;
  border: 1px solid #E2E8F0;
}

.print-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.edit-button {
  background: #00B8D4;
  color: white;
  box-shadow: 0 2px 8px rgba(0, 184, 212, 0.3);
}

.edit-button:hover {
  background: #0891A6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
}

.print-button svg,
.edit-button svg {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

/* SECTION INFO */
.info-section {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
  margin-bottom: 2rem;
}

.info-card,
.stats-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
}

.supplier-header {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 2rem;
}

.supplier-logo {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 24px;
  font-weight: 700;
  flex-shrink: 0;
}

.supplier-main {
  flex: 1;
}

.supplier-name {
  font-size: 20px;
  font-weight: 700;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
}

.supplier-since {
  font-size: 14px;
  color: #64748B;
  margin: 0 0 0.75rem 0;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.active {
  background: #D1FAE5;
  color: #047857;
}

.status-badge.inactive {
  background: #FEF2F2;
  color: #DC2626;
}

.info-grid {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  margin-bottom: 2rem;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
}

.info-icon {
  width: 18px;
  height: 18px;
  color: #64748B;
  stroke-width: 2;
  margin-top: 2px;
  flex-shrink: 0;
}

.info-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.info-label {
  font-size: 12px;
  color: #64748B;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-value {
  font-size: 14px;
  color: #334155;
  font-weight: 500;
  line-height: 1.4;
}

.info-value.link {
  color: #00B8D4;
  text-decoration: none;
  transition: color 0.2s ease;
}

.info-value.link:hover {
  color: #0891A6;
  text-decoration: underline;
}

/* CONTACT */
.contact-section {
  border-top: 1px solid #E2E8F0;
  padding-top: 1.5rem;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1rem 0;
}

.contact-person {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.contact-avatar {
  width: 40px;
  height: 40px;
  background: #00B8D4;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 14px;
  flex-shrink: 0;
}

.contact-name {
  font-size: 14px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
}

.contact-role {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

/* STATISTIQUES */
.card-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1.5rem 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.25rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #E2E8F0;
}

.stat-item {
  text-align: center;
}

.stat-label {
  font-size: 12px;
  color: #64748B;
  font-weight: 500;
  margin: 0 0 0.5rem 0;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
  line-height: 1;
}

.stat-value .unit {
  font-size: 16px;
  font-weight: 500;
  color: #64748B;
}

.rating-display {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.stars {
  display: flex;
  gap: 1px;
}

.star {
  color: #E2E8F0;
  font-size: 14px;
}

.star.filled {
  color: #F59E0B;
}

.metrics-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.metric-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #F8FAFC;
}

.metric-item:last-child {
  border-bottom: none;
}

.metric-label {
  font-size: 14px;
  color: #64748B;
  font-weight: 500;
}

.metric-value {
  font-size: 14px;
  color: #0F172A;
  font-weight: 600;
  text-align: right;
}

/* STATS SUPPLÉMENTAIRES */
.additional-stats {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #E2E8F0;
}

.subsection-title {
  font-size: 14px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1rem 0;
}

.mini-stats {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.75rem;
}

.mini-stat {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
}

.mini-label {
  font-size: 13px;
  color: #64748B;
}

.mini-value {
  font-size: 14px;
  font-weight: 600;
  color: #0F172A;
}

/* ONGLETS */
.tabs-section {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  overflow: hidden;
}

.tabs-nav {
  display: flex;
  border-bottom: 1px solid #E2E8F0;
  background: #F8FAFC;
}

.tab-button {
  background: none;
  border: none;
  padding: 1rem 1.5rem;
  font-size: 14px;
  font-weight: 500;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s ease;
  border-bottom: 3px solid transparent;
}

.tab-button:hover {
  color: #334155;
  background: #F1F5F9;
}

.tab-button.active {
  color: #00B8D4;
  background: white;
  border-bottom-color: #00B8D4;
}

.tab-content {
  padding: 1.5rem;
}

/* TABLE */
.table-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.table-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

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

.export-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.export-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
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

.data-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
}

.data-table tbody tr:hover {
  background: #F8FAFC;
}

.data-table tbody tr:last-child td {
  border-bottom: none;
}

.reference {
  font-weight: 600;
  color: #0F172A;
}

.price,
.stock,
.threshold {
  font-weight: 600;
  text-align: center;
}

.stock.low-stock {
  color: #DC2626;
  background: #FEF2F2;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #64748B;
}

.empty-state svg {
  width: 48px;
  height: 48px;
  margin: 0 auto 1rem;
  color: #CBD5E1;
  stroke-width: 1.5;
}

.placeholder-section {
  text-align: center;
  padding: 3rem;
  color: #64748B;
}

.placeholder-section svg {
  width: 48px;
  height: 48px;
  margin: 0 auto 1rem;
  color: #CBD5E1;
  stroke-width: 1.5;
}

.placeholder-section h4 {
  font-size: 18px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.5rem 0;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .info-section {
    grid-template-columns: 1fr;
  }

  .stats-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: stretch;
  }

  .header-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }

  .header-actions {
    justify-content: space-between;
  }

  .supplier-header {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .tabs-nav {
    flex-wrap: wrap;
  }

  .tab-button {
    flex: 1;
    min-width: 100px;
  }

  .data-table {
    font-size: 12px;
  }

  .data-table th,
  .data-table td {
    padding: 0.75rem 0.5rem;
  }
}

@media (max-width: 640px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .data-table th:nth-child(3),
  .data-table td:nth-child(3),
  .data-table th:nth-child(4),
  .data-table td:nth-child(4) {
    display: none;
  }
}

@media print {
  .back-button,
  .print-button,
  .edit-button,
  .export-button,
  .tabs-nav {
    display: none;
  }
  
  .tabs-section {
    box-shadow: none;
    border: 1px solid #E2E8F0;
    margin-top: 2rem;
    page-break-inside: avoid;
  }
  
  .tab-content {
    padding: 0;
  }
}
</style>