<template>
  <div class="stocks-page">
    <!-- En-tête avec bouton Ajouter -->
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Stock</h1>
      </div>
      <div class="header-right">
        <button class="add-button" @click="showAddModal = true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
          </svg>
          <span>Ajouter un article</span>
        </button>
      </div>
    </div>

    <!-- Filtres de recherche -->
    <div class="filters-section">
      <div class="filter-group">
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Rechercher par nom ou description..."
          class="search-input"
        />
        
        <select v-model="filters.category" class="filter-select">
          <option value="">Toutes les catégories</option>
          <option value="1">Vêtements Homme</option>
          <option value="2">Vêtements Femme</option>
          <option value="3">Accessoires</option>
          <option value="4">Enfants</option>
          <option value="5">Chaussures</option>
        </select>
        
        <select v-model="filters.stockStatus" class="filter-select">
          <option value="">Tous les stocks</option>
          <option value="low">Stock faible</option>
          <option value="normal">Stock normal</option>
        </select>
        
        <button class="search-button" @click="applyFilters">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          Rechercher
        </button>
      </div>
    </div>

    <!-- Tableau des stocks -->
    <div class="table-section">
      <div class="table-card">
        <div class="table-header">
          <h3 class="table-title">Articles en stock</h3>
          <div class="table-stats">
            <span class="stat-item">
              <span class="stat-label">Total articles:</span>
              <span class="stat-value">{{ totalArticles }}</span>
            </span>
            <span class="stat-item">
              <span class="stat-label">Stock faible:</span>
              <span class="stat-value low">{{ lowStockCount }}</span>
            </span>
          </div>
        </div>
        <div v-if="loading" class="loading-container">
          <div class="loader"></div>
          <p>Chargement des articles...</p>
        </div>
        <div v-else-if="error" class="error-container">
          <p class="error-message">{{ error }}</p>
          <button @click="fetchArticles" class="retry-button">Réessayer</button>
        </div>
        <div v-else class="table-container">
          <table class="stocks-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Catégorie</th>
                <th>En stock</th>
                <th>Seuil</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in paginatedArticles" :key="item.id_article" :class="{ 'low-stock': item.quantite_stock <= item.seuil_alerte }">
                <td class="reference">#{{ item.id_article }}</td>
                <td class="product-name">{{ item.nom }}</td>
                <td class="description">{{ item.description }}</td>
                <td>{{ getCategoryName(item.id_categorie) }}</td>
                <td class="stock-value">
                  <span class="stock-badge" :class="{ 'low': item.quantite_stock <= item.seuil_alerte }">
                    {{ item.quantite_stock }}
                  </span>
                </td>
                <td class="threshold-value">{{ item.seuil_alerte }}</td>
                <td class="actions">
                  <button class="action-btn" @click="editArticle(item)" title="Modifier">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button class="action-btn" @click="viewArticle(item)" title="Voir détails">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>
                </td>
              </tr>
              <tr v-if="paginatedArticles.length === 0">
                <td colspan="7" class="empty-message">Aucun article trouvé</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="!loading && !error && totalPages > 1" class="pagination-container">
          <div class="pagination-info">
            Affichage {{ startIndex + 1 }}-{{ endIndex }} sur {{ filteredArticles.length }} articles
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

    <!-- Modal Ajouter un article (placeholder) -->
    <div v-if="showAddModal" class="modal-overlay" @click="showAddModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Ajouter un article</h3>
          <button @click="showAddModal = false" class="modal-close">
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

    <!-- Modal Voir détails -->
    <div v-if="showDetailsModal" class="modal-overlay" @click="showDetailsModal = false">
      <div class="modal-content modal-details" @click.stop>
        <div class="modal-header">
          <h3>Détails de l'article</h3>
          <button @click="showDetailsModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="selectedArticle">
          <div class="details-grid">
            <div class="details-section">
              <h4 class="section-subtitle">Informations générales</h4>
              <div class="detail-row">
                <span class="detail-label">ID Article:</span>
                <span class="detail-value">#{{ selectedArticle.id_article }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Nom:</span>
                <span class="detail-value">{{ selectedArticle.nom }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span class="detail-value">{{ selectedArticle.description }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Catégorie:</span>
                <span class="detail-value">{{ getCategoryName(selectedArticle.id_categorie) }}</span>
              </div>
            </div>

            <div class="details-section">
              <h4 class="section-subtitle">Informations de stock</h4>
              <div class="detail-row">
                <span class="detail-label">Quantité en stock:</span>
                <span class="detail-value">
                  <span class="stock-badge" :class="{ 'low': selectedArticle.quantite_stock <= selectedArticle.seuil_alerte }">
                    {{ selectedArticle.quantite_stock }}
                  </span>
                </span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Seuil d'alerte:</span>
                <span class="detail-value">{{ selectedArticle.seuil_alerte }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Statut:</span>
                <span class="detail-value">
                  <span v-if="selectedArticle.quantite_stock <= selectedArticle.seuil_alerte" class="status-badge status-critical">
                    Stock critique
                  </span>
                  <span v-else class="status-badge status-ok">
                    Stock normal
                  </span>
                </span>
              </div>
            </div>

            <div class="details-section full-width" v-if="selectedArticle.prix_unitaire">
              <h4 class="section-subtitle">Informations commerciales</h4>
              <div class="detail-row">
                <span class="detail-label">Prix unitaire:</span>
                <span class="detail-value">{{ formatCurrency(selectedArticle.prix_unitaire) }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Valeur du stock:</span>
                <span class="detail-value">{{ formatCurrency(selectedArticle.prix_unitaire * selectedArticle.quantite_stock) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Modifier (TODO) -->
    <div v-if="showEditModal" class="modal-overlay" @click="showEditModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Modifier l'article</h3>
          <button @click="showEditModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="todo-container">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="todo-icon">
              <path d="M12 2L2 7l10 5 10-5-10-5z"/>
              <path d="M2 17l10 5 10-5"/>
              <path d="M2 12l10 5 10-5"/>
            </svg>
            <h4>Fonctionnalité à venir</h4>
            <p>La modification des articles sera bientôt disponible.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'

export default {
  name: 'StocksView',
  setup() {
    const showAddModal = ref(false)
    const showDetailsModal = ref(false)
    const showEditModal = ref(false)
    const selectedArticle = ref(null)
    const articles = ref([])
    const loading = ref(true)
    const error = ref(null)
    const searchQuery = ref('')
    const currentPage = ref(1)
    const itemsPerPage = 10
    
    const filters = reactive({
      category: '',
      stockStatus: ''
    })

    // Mapping des catégories
    const categories = {
      1: 'Vêtements Homme',
      2: 'Vêtements Femme',
      3: 'Accessoires',
      4: 'Enfants',
      5: 'Chaussures'
    }

    // Fonction pour récupérer les articles
    const fetchArticles = async () => {
      loading.value = true
      error.value = null
      
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
        error.value = 'Impossible de charger les articles. Veuillez réessayer.'
      } finally {
        loading.value = false
      }
    }

    // Computed properties
    const filteredArticles = computed(() => {
      let result = articles.value

      // Filtre par recherche
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        result = result.filter(article => 
          article.nom.toLowerCase().includes(query) ||
          article.description.toLowerCase().includes(query) ||
          article.id_article.toString().includes(query)
        )
      }

      // Filtre par catégorie
      if (filters.category) {
        result = result.filter(article => article.id_categorie == filters.category)
      }

      // Filtre par statut de stock
      if (filters.stockStatus === 'low') {
        result = result.filter(article => article.quantite_stock <= article.seuil_alerte)
      } else if (filters.stockStatus === 'normal') {
        result = result.filter(article => article.quantite_stock > article.seuil_alerte)
      }

      return result
    })

    // Pagination computed properties
    const totalPages = computed(() => {
      return Math.ceil(filteredArticles.value.length / itemsPerPage)
    })

    const startIndex = computed(() => {
      return (currentPage.value - 1) * itemsPerPage
    })

    const endIndex = computed(() => {
      return Math.min(startIndex.value + itemsPerPage, filteredArticles.value.length)
    })

    const paginatedArticles = computed(() => {
      return filteredArticles.value.slice(startIndex.value, endIndex.value)
    })

    const visiblePages = computed(() => {
      const pages = []
      const total = totalPages.value
      const current = currentPage.value

      if (total <= 7) {
        // Si 7 pages ou moins, afficher toutes les pages
        for (let i = 1; i <= total; i++) {
          pages.push(i)
        }
      } else {
        // Toujours afficher la première page
        pages.push(1)

        if (current > 3) {
          pages.push('...')
        }

        // Pages autour de la page courante
        const start = Math.max(2, current - 1)
        const end = Math.min(total - 1, current + 1)

        for (let i = start; i <= end; i++) {
          pages.push(i)
        }

        if (current < total - 2) {
          pages.push('...')
        }

        // Toujours afficher la dernière page
        pages.push(total)
      }

      return pages
    })

    const totalArticles = computed(() => filteredArticles.value.length)

    const lowStockCount = computed(() => {
      return articles.value.filter(item => item.quantite_stock <= item.seuil_alerte).length
    })

    // Watchers
    watch([searchQuery, filters], () => {
      // Réinitialiser à la première page lors du changement de filtres
      currentPage.value = 1
    })

    // Fonctions utilitaires
    const getCategoryName = (categoryId) => {
      return categories[categoryId] || `Catégorie ${categoryId}`
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount || 0)
    }

    const applyFilters = () => {
      // Les filtres sont déjà appliqués via computed property
      // Cette fonction peut être utilisée pour des actions supplémentaires
      currentPage.value = 1
    }

    const goToPage = (page) => {
      if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page
      }
    }

    const editArticle = (article) => {
      selectedArticle.value = article
      showEditModal.value = true
    }

    const viewArticle = (article) => {
      selectedArticle.value = article
      showDetailsModal.value = true
    }

    // Charger les données au montage
    onMounted(() => {
      fetchArticles()
    })

    return {
      showAddModal,
      showDetailsModal,
      showEditModal,
      selectedArticle,
      articles,
      loading,
      error,
      searchQuery,
      filters,
      filteredArticles,
      paginatedArticles,
      totalArticles,
      lowStockCount,
      currentPage,
      totalPages,
      startIndex,
      endIndex,
      visiblePages,
      fetchArticles,
      getCategoryName,
      formatCurrency,
      applyFilters,
      goToPage,
      editArticle,
      viewArticle
    }
  }
}
</script>

<style scoped>
.stocks-page {
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

.add-button {
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

.add-button:hover {
  background: #0891A6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
}

.add-button svg {
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

.search-input {
  flex: 1;
  min-width: 250px;
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-size: 14px;
  color: #334155;
  transition: all 0.2s ease;
}

.search-input:focus {
  outline: none;
  border-color: #00B8D4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
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

.search-button {
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

.search-button:hover {
  background: #2563EB;
  transform: translateY(-1px);
}

.search-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
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

.stat-value.low {
  color: #DC2626;
}

.table-container {
  overflow-x: auto;
}

.stocks-table {
  width: 100%;
  border-collapse: collapse;
}

.stocks-table th {
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

.stocks-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
}

.stocks-table tbody tr:hover {
  background: #F8FAFC;
}

.stocks-table tbody tr:last-child td {
  border-bottom: none;
}

.stocks-table tr.low-stock {
  background: #FEF2F2;
}

.stocks-table tr.low-stock:hover {
  background: #FEE2E2;
}

.reference {
  font-weight: 600;
  color: #0F172A;
}

.product-name {
  font-weight: 600;
  color: #0F172A;
}

.description {
  font-size: 13px;
  color: #64748B;
  max-width: 250px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.stock-value {
  text-align: center;
}

.stock-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 40px;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 600;
  background: #F0FDF4;
  color: #166534;
}

.stock-badge.low {
  background: #FEE2E2;
  color: #991B1B;
}

.threshold-value {
  font-weight: 600;
  text-align: center;
  color: #64748B;
}

.empty-message {
  text-align: center;
  color: #64748B;
  font-style: italic;
  padding: 2rem;
}

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
  max-width: 500px;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-content.modal-details {
  max-width: 700px;
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

.status-badge {
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 500;
}

.status-critical {
  background: #FEE2E2;
  color: #991B1B;
}

.status-ok {
  background: #F0FDF4;
  color: #166534;
}

/* MODAL TODO */
.todo-container {
  text-align: center;
  padding: 2rem 0;
}

.todo-icon {
  width: 64px;
  height: 64px;
  color: #00B8D4;
  margin-bottom: 1rem;
}

.todo-container h4 {
  font-size: 18px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.5rem 0;
}

.todo-container p {
  color: #64748B;
  margin: 0;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .add-button {
    width: 100%;
    justify-content: center;
  }
  
  .filter-group {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-input,
  .filter-select,
  .search-button {
    width: 100%;
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
  
  .stocks-table {
    font-size: 12px;
  }
  
  .stocks-table th,
  .stocks-table td {
    padding: 0.75rem 0.5rem;
  }
  
  .stocks-table th:nth-child(3),
  .stocks-table td:nth-child(3) {
    display: none;
  }
  
  .actions {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .stocks-table th:last-child,
  .stocks-table td:last-child {
    display: none;
  }

  .pagination-container {
    flex-direction: column;
    gap: 1rem;
  }

  .page-numbers {
    flex-wrap: wrap;
    justify-content: center;
  }
  
  .details-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}
</style>