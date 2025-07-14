<template>
  <div class="bundles-page">
    <!-- En-tête avec bouton Nouveau lot -->
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Conception de lots</h1>
      </div>
      <div class="header-right">
        <button class="new-bundle-button" @click="openNewBundleModal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
          </svg>
          <span>Nouveau lot</span>
        </button>
      </div>
    </div>

    <!-- Cartes KPI -->
    <div class="kpi-section">
      <div class="kpi-card">
        <div class="kpi-icon bundles-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="2" y="2" width="9" height="9" rx="1"/>
            <rect x="13" y="2" width="9" height="9" rx="1"/>
            <rect x="2" y="13" width="9" height="9" rx="1"/>
            <rect x="13" y="13" width="9" height="9" rx="1"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Lots actifs</p>
          <p class="kpi-value">{{ activeBundles }}</p>
          <p class="kpi-trend">{{ bundleTrend }}% ce mois</p>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-icon revenue-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <line x1="12" y1="1" x2="12" y2="23"/>
            <polyline points="17 6 12 1 7 6"/>
            <polyline points="17 18 12 23 7 18"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Valeur totale</p>
          <p class="kpi-value">{{ formatCurrency(totalValue) }}</p>
          <p class="kpi-trend">Stock lots</p>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-icon products-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
            <line x1="12" y1="22.08" x2="12" y2="12"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Articles en lots</p>
          <p class="kpi-value">{{ totalProductsInBundles }}</p>
          <p class="kpi-trend">Produits uniques</p>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-icon savings-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <circle cx="8" cy="21" r="1"/>
            <circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            <path d="M9 11l3-3 3 3"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Économie moyenne</p>
          <p class="kpi-value">{{ averageSavings }}%</p>
          <p class="kpi-trend">Sur les lots</p>
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
            placeholder="Rechercher un lot..."
            class="search-input"
          />
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
        </div>
        
        <select v-model="filters.status" class="filter-select">
          <option value="">Tous les statuts</option>
          <option value="active">Actif</option>
          <option value="inactive">Inactif</option>
          <option value="draft">Brouillon</option>
        </select>
        
        <select v-model="filters.sort" class="filter-select">
          <option value="name">Trier par nom</option>
          <option value="date">Plus récents</option>
          <option value="price">Prix croissant</option>
          <option value="savings">Économies</option>
        </select>
        
        <button class="action-button" @click="resetFilters">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="1 4 1 10 7 10"/>
            <polyline points="23 20 23 14 17 14"/>
            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
          </svg>
          Réinitialiser
        </button>
      </div>
    </div>

    <!-- Tableau des lots -->
    <div class="table-section">
      <div class="table-card">
        <div class="table-header">
          <h3 class="table-title">Liste des lots</h3>
          <div class="table-stats">
            <span class="stat-item">
              <span class="stat-label">Total:</span>
              <span class="stat-value">{{ filteredBundles.length }}</span>
            </span>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="loader"></div>
          <p>Chargement des lots...</p>
        </div>

        <!-- Table Content -->
        <div v-else class="table-container">
          <table class="bundles-table">
            <thead>
              <tr>
                <th>Lot</th>
                <th>Articles</th>
                <th>Prix unitaire</th>
                <th>Prix du lot</th>
                <th>Économie</th>
                <th>Stock</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="bundle in paginatedBundles" :key="bundle.id">
                <td>
                  <div class="bundle-info">
                    <div class="bundle-icon" :style="{ backgroundColor: getBundleColor(bundle.id) }">
                      <svg viewBox="0 0 24 24" fill="currentColor">
                        <rect x="2" y="2" width="9" height="9" rx="1"/>
                        <rect x="13" y="2" width="9" height="9" rx="1"/>
                        <rect x="2" y="13" width="9" height="9" rx="1"/>
                        <rect x="13" y="13" width="9" height="9" rx="1"/>
                      </svg>
                    </div>
                    <div class="bundle-details">
                      <p class="bundle-name">{{ bundle.name }}</p>
                      <p class="bundle-code">{{ bundle.code }}</p>
                    </div>
                  </div>
                </td>
                <td class="products-count">
                  <div class="products-preview">
                    <span class="count">{{ bundle.products.length }}</span>
                    <span class="label">articles</span>
                  </div>
                </td>
                <td class="price-unit">{{ formatCurrency(bundle.originalPrice) }}</td>
                <td class="price-bundle">
                  <span class="bundle-price">{{ formatCurrency(bundle.bundlePrice) }}</span>
                </td>
                <td class="savings">
                  <span class="savings-badge">
                    -{{ bundle.savingsPercent }}%
                  </span>
                </td>
                <td class="stock">
                  <span class="stock-value" :class="getStockClass(bundle.stock)">
                    {{ bundle.stock }}
                  </span>
                </td>
                <td>
                  <span class="status-badge" :class="getStatusClass(bundle.status)">
                    {{ getStatusLabel(bundle.status) }}
                  </span>
                </td>
                <td class="actions">
                  <button class="action-btn" @click="viewBundle(bundle)" title="Voir détails">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>
                  <button class="action-btn" @click="editBundle(bundle)" title="Modifier">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button class="action-btn danger" @click="deleteBundle(bundle)" title="Supprimer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <polyline points="3 6 5 6 21 6"/>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                  </button>
                </td>
              </tr>
              <tr v-if="filteredBundles.length === 0 && !loading">
                <td colspan="8" class="empty-message">
                  Aucun lot trouvé
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="pagination-container">
          <div class="pagination-info">
            Affichage {{ startIndex + 1 }}-{{ endIndex }} sur {{ filteredBundles.length }}
          </div>
          <div class="pagination-controls">
            <button 
              class="pagination-btn" 
              @click="currentPage--"
              :disabled="currentPage === 1"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polyline points="15 18 9 12 15 6"/>
              </svg>
            </button>
            <span class="page-info">Page {{ currentPage }} / {{ totalPages }}</span>
            <button 
              class="pagination-btn" 
              @click="currentPage++"
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

    <!-- Modal Nouveau/Éditer lot -->
    <div v-if="showBundleModal" class="modal-overlay" @click="closeBundleModal">
      <div class="modal-content modal-large" @click.stop>
        <div class="modal-header">
          <h3>{{ isEditing ? 'Modifier le lot' : 'Nouveau lot' }}</h3>
          <button @click="closeBundleModal" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveBundle" class="bundle-form">
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">Nom du lot</label>
                <input 
                  v-model="bundleForm.name"
                  type="text" 
                  class="form-input"
                  placeholder="Ex: Pack Été 2025"
                  required
                />
              </div>
              
              <div class="form-group">
                <label class="form-label">Code</label>
                <input 
                  v-model="bundleForm.code"
                  type="text" 
                  class="form-input"
                  placeholder="Ex: PACK-ETE-2025"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label">Prix du lot</label>
                <input 
                  v-model.number="bundleForm.bundlePrice"
                  type="number" 
                  step="0.01"
                  class="form-input"
                  placeholder="0.00"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label">Statut</label>
                <select v-model="bundleForm.status" class="form-select">
                  <option value="active">Actif</option>
                  <option value="inactive">Inactif</option>
                  <option value="draft">Brouillon</option>
                </select>
              </div>
            </div>

            <div class="form-section">
              <h4 class="section-title">Articles du lot</h4>
              
              <div class="products-selection">
                <div class="add-product-row">
                  <select v-model="selectedProduct" class="form-select flex-1">
                    <option value="">Sélectionner un article</option>
                    <option 
                      v-for="product in availableProducts" 
                      :key="product.id"
                      :value="product.id"
                    >
                      {{ product.name }} - {{ formatCurrency(product.price) }}
                    </option>
                  </select>
                  <input 
                    v-model.number="productQuantity"
                    type="number" 
                    min="1"
                    placeholder="Qté"
                    class="quantity-input"
                  />
                  <button 
                    type="button"
                    @click="addProductToBundle"
                    class="add-btn"
                    :disabled="!selectedProduct || !productQuantity"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <line x1="12" y1="5" x2="12" y2="19"/>
                      <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                  </button>
                </div>

                <div class="selected-products" v-if="bundleForm.products.length > 0">
                  <div 
                    v-for="(item, index) in bundleForm.products" 
                    :key="item.productId"
                    class="product-item"
                  >
                    <span class="product-name">{{ getProductName(item.productId) }}</span>
                    <span class="product-quantity">x{{ item.quantity }}</span>
                    <span class="product-price">{{ formatCurrency(getProductPrice(item.productId) * item.quantity) }}</span>
                    <button 
                      type="button"
                      @click="removeProductFromBundle(index)"
                      class="remove-btn"
                    >
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                      </svg>
                    </button>
                  </div>
                </div>

                <div v-else class="no-products">
                  Aucun article ajouté au lot
                </div>
              </div>

              <div class="price-summary">
                <div class="summary-row">
                  <span>Prix total des articles:</span>
                  <span>{{ formatCurrency(calculateOriginalPrice()) }}</span>
                </div>
                <div class="summary-row">
                  <span>Prix du lot:</span>
                  <span>{{ formatCurrency(bundleForm.bundlePrice || 0) }}</span>
                </div>
                <div class="summary-row highlight">
                  <span>Économie:</span>
                  <span>{{ calculateSavings() }}</span>
                </div>
              </div>
            </div>

            <div class="modal-actions">
              <button type="button" class="modal-btn secondary" @click="closeBundleModal">
                Annuler
              </button>
              <button type="submit" class="modal-btn primary" :disabled="!isFormValid">
                {{ isEditing ? 'Mettre à jour' : 'Créer le lot' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Détails du lot -->
    <div v-if="showDetailsModal" class="modal-overlay" @click="closeDetailsModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Détails du lot</h3>
          <button @click="closeDetailsModal" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="selectedBundle">
          <div class="details-grid">
            <div class="details-section">
              <h4 class="section-subtitle">Informations générales</h4>
              <div class="detail-row">
                <span class="detail-label">Nom:</span>
                <span class="detail-value">{{ selectedBundle.name }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Code:</span>
                <span class="detail-value">{{ selectedBundle.code }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Statut:</span>
                <span class="detail-value">
                  <span class="status-badge" :class="getStatusClass(selectedBundle.status)">
                    {{ getStatusLabel(selectedBundle.status) }}
                  </span>
                </span>
              </div>
            </div>

            <div class="details-section">
              <h4 class="section-subtitle">Détails financiers</h4>
              <div class="detail-row">
                <span class="detail-label">Prix unitaire:</span>
                <span class="detail-value">{{ formatCurrency(selectedBundle.originalPrice) }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Prix du lot:</span>
                <span class="detail-value amount">{{ formatCurrency(selectedBundle.bundlePrice) }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Économie:</span>
                <span class="detail-value savings">-{{ selectedBundle.savingsPercent }}%</span>
              </div>
            </div>
            
            <div class="details-section full-width">
              <h4 class="section-subtitle">Articles inclus ({{ selectedBundle.products.length }})</h4>
              <div class="products-list">
                <div v-for="item in selectedBundle.products" :key="item.productId" class="product-detail">
                  <span class="product-info">{{ getProductName(item.productId) }}</span>
                  <span class="product-qty">x{{ item.quantity }}</span>
                  <span class="product-subtotal">{{ formatCurrency(getProductPrice(item.productId) * item.quantity) }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="modal-actions">
            <button class="modal-btn secondary" @click="closeDetailsModal">
              Fermer
            </button>
            <button class="modal-btn primary" @click="() => { closeDetailsModal(); editBundle(selectedBundle); }">
              Modifier le lot
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'ProductBundlesView',
  setup() {
    // États réactifs
    const bundles = ref([])
    const products = ref([])
    const loading = ref(false)
    const searchQuery = ref('')
    const currentPage = ref(1)
    const itemsPerPage = 10
    const showBundleModal = ref(false)
    const showDetailsModal = ref(false)
    const selectedBundle = ref(null)
    const isEditing = ref(false)
    const selectedProduct = ref('')
    const productQuantity = ref(1)
    
    const filters = ref({
      status: '',
      sort: 'name'
    })

    const bundleForm = ref({
      name: '',
      code: '',
      bundlePrice: 0,
      status: 'active',
      products: []
    })

    // Données mockées
    const mockProducts = [
      { id: 1, name: 'Robe d\'été Flora', price: 89.90 },
      { id: 2, name: 'Chemisier Soie', price: 129.90 },
      { id: 3, name: 'Pantalon Lin', price: 99.90 },
      { id: 4, name: 'Jupe Plissée', price: 79.90 },
      { id: 5, name: 'Top Dentelle', price: 59.90 },
      { id: 6, name: 'Cardigan Mohair', price: 149.90 },
      { id: 7, name: 'Short Denim', price: 69.90 },
      { id: 8, name: 'Blouse Fleurie', price: 84.90 }
    ]

    const mockBundles = [
      {
        id: 1,
        name: 'Collection Printemps',
        code: 'SPRING-2025',
        products: [
          { productId: 1, quantity: 1 },
          { productId: 2, quantity: 1 },
          { productId: 4, quantity: 1 }
        ],
        originalPrice: 299.70,
        bundlePrice: 239.90,
        savingsPercent: 20,
        stock: 15,
        status: 'active'
      },
      {
        id: 2,
        name: 'Pack Été Complet',
        code: 'SUMMER-FULL',
        products: [
          { productId: 1, quantity: 2 },
          { productId: 3, quantity: 1 },
          { productId: 7, quantity: 2 }
        ],
        originalPrice: 429.50,
        bundlePrice: 349.90,
        savingsPercent: 19,
        stock: 8,
        status: 'active'
      },
      {
        id: 3,
        name: 'Duo Élégance',
        code: 'DUO-ELEG',
        products: [
          { productId: 2, quantity: 1 },
          { productId: 6, quantity: 1 }
        ],
        originalPrice: 279.80,
        bundlePrice: 249.90,
        savingsPercent: 11,
        stock: 22,
        status: 'active'
      }
    ]

    // Computed properties
    const activeBundles = computed(() => {
      return bundles.value.filter(b => b.status === 'active').length
    })

    const bundleTrend = computed(() => '+15')

    const totalValue = computed(() => {
      return bundles.value.reduce((total, bundle) => {
        return total + (bundle.bundlePrice * bundle.stock)
      }, 0)
    })

    const totalProductsInBundles = computed(() => {
      const uniqueProducts = new Set()
      bundles.value.forEach(bundle => {
        bundle.products.forEach(item => {
          uniqueProducts.add(item.productId)
        })
      })
      return uniqueProducts.size
    })

    const averageSavings = computed(() => {
      if (bundles.value.length === 0) return 0
      const totalSavings = bundles.value.reduce((sum, bundle) => sum + bundle.savingsPercent, 0)
      return Math.round(totalSavings / bundles.value.length)
    })

    const availableProducts = computed(() => {
      return products.value.filter(product => {
        return !bundleForm.value.products.some(item => item.productId === product.id)
      })
    })

    const filteredBundles = computed(() => {
      let result = [...bundles.value]

      // Filtre par statut
      if (filters.value.status) {
        result = result.filter(bundle => bundle.status === filters.value.status)
      }

      // Filtre par recherche
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        result = result.filter(bundle =>
          bundle.name.toLowerCase().includes(query) ||
          bundle.code.toLowerCase().includes(query)
        )
      }

      // Tri
      switch (filters.value.sort) {
        case 'name':
          result.sort((a, b) => a.name.localeCompare(b.name))
          break
        case 'date':
          result.sort((a, b) => b.id - a.id)
          break
        case 'price':
          result.sort((a, b) => a.bundlePrice - b.bundlePrice)
          break
        case 'savings':
          result.sort((a, b) => b.savingsPercent - a.savingsPercent)
          break
      }

      return result
    })

    const totalPages = computed(() => {
      return Math.ceil(filteredBundles.value.length / itemsPerPage)
    })

    const startIndex = computed(() => {
      return (currentPage.value - 1) * itemsPerPage
    })

    const endIndex = computed(() => {
      return Math.min(startIndex.value + itemsPerPage, filteredBundles.value.length)
    })

    const paginatedBundles = computed(() => {
      return filteredBundles.value.slice(startIndex.value, endIndex.value)
    })

    const isFormValid = computed(() => {
      return bundleForm.value.name && 
             bundleForm.value.code && 
             bundleForm.value.bundlePrice > 0 && 
             bundleForm.value.products.length > 0
    })

    // Fonctions
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }

    const getBundleColor = (id) => {
      const colors = ['#00B8D4', '#2563EB', '#059669', '#D97706', '#7C3AED', '#DC2626']
      return colors[id % colors.length]
    }

    const getStatusClass = (status) => {
      const classes = {
        'active': 'status-active',
        'inactive': 'status-inactive',
        'draft': 'status-draft'
      }
      return classes[status] || 'status-default'
    }

    const getStatusLabel = (status) => {
      const labels = {
        'active': 'Actif',
        'inactive': 'Inactif',
        'draft': 'Brouillon'
      }
      return labels[status] || status
    }

    const getStockClass = (stock) => {
      if (stock <= 5) return 'stock-low'
      if (stock <= 20) return 'stock-medium'
      return 'stock-high'
    }

    const getProductName = (productId) => {
      const product = products.value.find(p => p.id === productId)
      return product ? product.name : `Produit #${productId}`
    }

    const getProductPrice = (productId) => {
      const product = products.value.find(p => p.id === productId)
      return product ? product.price : 0
    }

    const calculateOriginalPrice = () => {
      return bundleForm.value.products.reduce((total, item) => {
        return total + (getProductPrice(item.productId) * item.quantity)
      }, 0)
    }

    const calculateSavings = () => {
      const original = calculateOriginalPrice()
      const bundle = bundleForm.value.bundlePrice || 0
      if (original === 0 || bundle >= original) return '0%'
      const savings = ((original - bundle) / original) * 100
      return `-${savings.toFixed(0)}%`
    }

    const addProductToBundle = () => {
      if (selectedProduct.value && productQuantity.value > 0) {
        bundleForm.value.products.push({
          productId: Number(selectedProduct.value),
          quantity: productQuantity.value
        })
        selectedProduct.value = ''
        productQuantity.value = 1
      }
    }

    const removeProductFromBundle = (index) => {
      bundleForm.value.products.splice(index, 1)
    }

    const resetFilters = () => {
      searchQuery.value = ''
      filters.value.status = ''
      filters.value.sort = 'name'
      currentPage.value = 1
    }

    const openNewBundleModal = () => {
      isEditing.value = false
      bundleForm.value = {
        name: '',
        code: '',
        bundlePrice: 0,
        status: 'active',
        products: []
      }
      showBundleModal.value = true
    }

    const editBundle = (bundle) => {
      isEditing.value = true
      bundleForm.value = {
        id: bundle.id,
        name: bundle.name,
        code: bundle.code,
        bundlePrice: bundle.bundlePrice,
        status: bundle.status,
        products: [...bundle.products]
      }
      showBundleModal.value = true
    }

    const viewBundle = (bundle) => {
      selectedBundle.value = bundle
      showDetailsModal.value = true
    }

    const saveBundle = () => {
      // Calculer les prix et économies
      const originalPrice = calculateOriginalPrice()
      const savingsPercent = Math.round(((originalPrice - bundleForm.value.bundlePrice) / originalPrice) * 100)
      
      const newBundle = {
        ...bundleForm.value,
        originalPrice,
        savingsPercent,
        stock: 10 // Stock par défaut
      }

      if (isEditing.value) {
        const index = bundles.value.findIndex(b => b.id === bundleForm.value.id)
        if (index !== -1) {
          bundles.value[index] = newBundle
        }
      } else {
        newBundle.id = bundles.value.length + 1
        bundles.value.push(newBundle)
      }

      closeBundleModal()
    }

    const deleteBundle = (bundle) => {
      if (confirm(`Êtes-vous sûr de vouloir supprimer le lot "${bundle.name}" ?`)) {
        const index = bundles.value.findIndex(b => b.id === bundle.id)
        if (index !== -1) {
          bundles.value.splice(index, 1)
        }
      }
    }

    const closeBundleModal = () => {
      showBundleModal.value = false
      selectedProduct.value = ''
      productQuantity.value = 1
    }

    const closeDetailsModal = () => {
      showDetailsModal.value = false
      selectedBundle.value = null
    }

    // Charger les données au montage
    onMounted(() => {
      products.value = mockProducts
      bundles.value = mockBundles
    })

    return {
      bundles,
      products,
      loading,
      searchQuery,
      currentPage,
      showBundleModal,
      showDetailsModal,
      selectedBundle,
      isEditing,
      selectedProduct,
      productQuantity,
      filters,
      bundleForm,
      activeBundles,
      bundleTrend,
      totalValue,
      totalProductsInBundles,
      averageSavings,
      availableProducts,
      filteredBundles,
      totalPages,
      startIndex,
      endIndex,
      paginatedBundles,
      isFormValid,
      formatCurrency,
      getBundleColor,
      getStatusClass,
      getStatusLabel,
      getStockClass,
      getProductName,
      getProductPrice,
      calculateOriginalPrice,
      calculateSavings,
      addProductToBundle,
      removeProductFromBundle,
      resetFilters,
      openNewBundleModal,
      editBundle,
      viewBundle,
      saveBundle,
      deleteBundle,
      closeBundleModal,
      closeDetailsModal
    }
  }
}
</script>

<style scoped>
.bundles-page {
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

.new-bundle-button {
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

.new-bundle-button:hover {
  background: #0891A6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
}

.new-bundle-button svg {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

/* SECTION KPI */
.kpi-section {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
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

.bundles-icon {
  background: #F3E8FF;
  color: #7C3AED;
}

.revenue-icon {
  background: #EFF6FF;
  color: #2563EB;
}

.products-icon {
  background: #F0FDF4;
  color: #059669;
}

.savings-icon {
  background: #FEF3C7;
  color: #D97706;
}

.kpi-content {
  flex: 1;
}

.kpi-label {
  font-size: 13px;
  color: #64748B;
  margin: 0 0 4px 0;
  font-weight: 500;
}

.kpi-value {
  font-size: 28px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
  line-height: 1;
}

.kpi-trend {
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

.action-button {
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

.action-button:hover {
  background: #2563EB;
  transform: translateY(-1px);
}

.action-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* LOADING */
.loading-container {
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

.table-container {
  overflow-x: auto;
}

.bundles-table {
  width: 100%;
  border-collapse: collapse;
}

.bundles-table th {
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

.bundles-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
  vertical-align: middle;
}

.bundles-table tbody tr:hover {
  background: #F8FAFC;
}

.bundles-table tbody tr:last-child td {
  border-bottom: none;
}

/* BUNDLE INFO */
.bundle-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.bundle-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.bundle-icon svg {
  width: 20px;
  height: 20px;
}

.bundle-name {
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
  line-height: 1.2;
}

.bundle-code {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

/* PRODUCTS COUNT */
.products-preview {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.25rem;
}

.products-preview .count {
  font-size: 18px;
  font-weight: 600;
  color: #0F172A;
}

.products-preview .label {
  font-size: 12px;
  color: #64748B;
}

/* PRICES */
.price-unit,
.price-bundle {
  font-weight: 600;
  color: #0F172A;
}

.bundle-price {
  font-size: 16px;
  color: #00B8D4;
}

/* SAVINGS */
.savings-badge {
  background: #D1FAE5;
  color: #047857;
  padding: 0.375rem 0.75rem;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 600;
}

/* STOCK */
.stock-value {
  font-weight: 600;
}

.stock-low {
  color: #DC2626;
}

.stock-medium {
  color: #F59E0B;
}

.stock-high {
  color: #059669;
}

/* STATUS */
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border-radius: 16px;
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

.status-draft {
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

.action-btn.danger:hover {
  background: #FEF2F2;
  border-color: #DC2626;
  color: #DC2626;
}

.action-btn svg {
  width: 16px;
  height: 16px;
  stroke-width: 1.5;
}

.empty-message {
  text-align: center;
  color: #64748B;
  font-style: italic;
  padding: 2rem;
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
  gap: 1rem;
}

.pagination-btn {
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 6px;
  padding: 6px 10px;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s ease;
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

.page-info {
  font-size: 14px;
  color: #64748B;
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
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
}

.modal-content.modal-large {
  max-width: 800px;
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
  overflow-y: auto;
  flex: 1;
}

/* FORMULAIRE */
.bundle-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-size: 13px;
  font-weight: 500;
  color: #334155;
}

.form-input,
.form-select {
  padding: 0.75rem;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  font-size: 14px;
  color: #0F172A;
  background: white;
  transition: all 0.2s ease;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #00B8D4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #E2E8F0;
}

.products-selection {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.add-product-row {
  display: flex;
  gap: 0.75rem;
  align-items: flex-end;
}

.flex-1 {
  flex: 1;
}

.quantity-input {
  width: 80px;
  padding: 0.75rem;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  font-size: 14px;
  text-align: center;
}

.add-btn {
  background: #00B8D4;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.75rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.add-btn:hover:not(:disabled) {
  background: #0891A6;
}

.add-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.add-btn svg {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

.selected-products {
  background: #F8FAFC;
  border-radius: 8px;
  padding: 1rem;
}

.product-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  background: white;
  border-radius: 6px;
  margin-bottom: 0.5rem;
}

.product-item:last-child {
  margin-bottom: 0;
}

.product-name {
  flex: 1;
  font-weight: 500;
  color: #0F172A;
}

.product-quantity {
  font-weight: 600;
  color: #64748B;
}

.product-price {
  font-weight: 600;
  color: #00B8D4;
}

.remove-btn {
  background: none;
  border: none;
  color: #DC2626;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.remove-btn:hover {
  background: #FEF2F2;
}

.remove-btn svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

.no-products {
  text-align: center;
  color: #64748B;
  font-style: italic;
  padding: 2rem;
}

.price-summary {
  background: #F8FAFC;
  border-radius: 8px;
  padding: 1rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  font-size: 14px;
  color: #64748B;
}

.summary-row.highlight {
  font-weight: 600;
  font-size: 16px;
  color: #059669;
  padding-top: 0.75rem;
  border-top: 1px solid #E2E8F0;
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

.modal-btn.primary:hover:not(:disabled) {
  background: #0891A6;
}

.modal-btn.primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-btn.secondary {
  background: #F1F5F9;
  color: #64748B;
}

.modal-btn.secondary:hover {
  background: #E2E8F0;
  color: #334155;
}

/* MODAL DÉTAILS */
.details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
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

.detail-value.savings {
  color: #059669;
}

.products-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.product-detail {
  display: flex;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid #E2E8F0;
}

.product-detail:last-child {
  border-bottom: none;
}

.product-info {
  flex: 1;
  font-size: 14px;
  color: #334155;
}

.product-qty {
  font-weight: 600;
  color: #64748B;
  margin-right: 2rem;
}

.product-subtotal {
  font-weight: 600;
  color: #0F172A;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .kpi-section {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .new-bundle-button {
    width: 100%;
    justify-content: center;
  }
  
  .filter-group {
    flex-direction: column;
  }
  
  .search-container,
  .filter-select,
  .action-button {
    width: 100%;
    max-width: none;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .details-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .kpi-section {
    grid-template-columns: 1fr;
  }
  
  .table-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .bundles-table {
    font-size: 12px;
  }
  
  .bundles-table th,
  .bundles-table td {
    padding: 0.75rem 0.5rem;
  }
  
  .actions {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .modal-content {
    width: 100%;
    height: 100vh;
    max-height: 100vh;
    border-radius: 0;
  }
  
  .add-product-row {
    flex-direction: column;
  }
  
  .quantity-input {
    width: 100%;
  }
  
  .modal-actions {
    flex-direction: column;
  }
  
  .modal-btn {
    width: 100%;
  }
}

@media (max-width: 640px) {
  .bundles-table th:nth-child(3),
  .bundles-table td:nth-child(3),
  .bundles-table th:nth-child(4),
  .bundles-table td:nth-child(4),
  .bundles-table th:nth-child(5),
  .bundles-table td:nth-child(5) {
    display: none;
  }
}
</style>