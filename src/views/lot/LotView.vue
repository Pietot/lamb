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
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="16" />
            <line x1="8" y1="12" x2="16" y2="12" />
          </svg>
          <span>Nouveau lot</span>
        </button>
      </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="filters-section">
      <div class="filter-group">
        <div class="search-container">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher par lot ou date de création"
            class="search-input"
          />
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </div>

        <div class="filter-wrapper">
          <label for="stocks-filter" class="filter-label">Stocks :</label>
          <select id="stocks-filter" v-model="filters.stockStatus" class="filter-select">
            <option value="">Tous les stocks</option>
            <option value="normal">Stock normal</option>
            <option value="low">Stock faible</option>
          </select>
          <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>

        <button
          role="button"
          aria-label="Exporter les lots"
          class="export-button"
          @click="exportLots"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="7,10 12,15 17,10" />
            <line x1="12" y1="15" x2="12" y2="3" />
          </svg>
          Exporter
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

        <!-- Error State -->
        <div v-else-if="error" class="error-container">
          <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="13" />
            <line x1="12" y1="16" x2="12" y2="17" />
          </svg>
          <p class="error-message">{{ error }}</p>
          <button @click="fetchData" class="retry-button">Réessayer</button>
        </div>

        <!-- Table Content -->
        <div v-else class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>Lot</th>
                <th>Articles</th>
                <th>Stock</th>
                <th>Seuil d'alerte</th>
                <th>Date création</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="bundle in paginatedBundles" :key="bundle.id_lot">
                <td>
                  <div class="bundle-info">
                    <div
                      class="bundle-icon"
                      :style="{ backgroundColor: getBundleColor(bundle.id_lot) }"
                    >
                      <svg viewBox="0 0 24 24" fill="currentColor">
                        <rect x="2" y="2" width="9" height="9" rx="1" />
                        <rect x="13" y="2" width="9" height="9" rx="1" />
                        <rect x="2" y="13" width="9" height="9" rx="1" />
                        <rect x="13" y="13" width="9" height="9" rx="1" />
                      </svg>
                    </div>
                    <div class="bundle-details">
                      <p class="bundle-name">{{ bundle.nom }}</p>
                      <p class="bundle-code">{{ bundle.description }}</p>
                    </div>
                  </div>
                </td>
                <td class="products-count">
                  <div class="products-preview">
                    <span class="count">{{ getBundleProductsCount(bundle.id_lot) }}</span>
                    <span class="label">articles</span>
                  </div>
                </td>
                <td class="stock">
                  <span
                    class="stock-value"
                    :class="getStockClass(bundle.quantite_stock, bundle.seuil_alerte)"
                  >
                    {{ bundle.quantite_stock }}
                  </span>
                </td>
                <td class="threshold">
                  <span class="threshold-value">{{ bundle.seuil_alerte }}</span>
                </td>
                <td class="date">{{ formatDate(bundle.date_creation) }}</td>
                <td>
                  <span
                    class="status-badge"
                    :class="getStatusClass(bundle.quantite_stock, bundle.seuil_alerte)"
                  >
                    {{ getStatusLabel(bundle.quantite_stock, bundle.seuil_alerte) }}
                  </span>
                </td>
                <td class="actions">
                  <button class="action-btn" @click="viewBundle(bundle)" title="Voir détails">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                  <button class="action-btn" @click="editBundle(bundle)" title="Modifier">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                  </button>
                </td>
              </tr>
              <tr v-if="filteredBundles.length === 0 && !loading">
                <td colspan="7" class="empty-message">Aucun lot trouvé</td>
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
            <button class="pagination-btn" @click="currentPage--" :disabled="currentPage === 1">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polyline points="15 18 9 12 15 6" />
              </svg>
            </button>
            <span class="page-info">Page {{ currentPage }} / {{ totalPages }}</span>
            <button
              class="pagination-btn"
              @click="currentPage++"
              :disabled="currentPage === totalPages"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polyline points="9 18 15 12 9 6" />
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
          <h3>{{ isEditing ? "Modifier le lot" : "Nouveau lot" }}</h3>
          <button @click="closeBundleModal" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <!-- Messages de succès/erreur -->
          <div v-if="successMessage" class="success-message">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path
                d="M20 6L9 17l-5-5"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            {{ successMessage }}
          </div>
          <div v-if="errorMessage" class="error-message-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            {{ errorMessage }}
          </div>

          <form @submit.prevent="saveBundle" class="bundle-form">
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">Nom du lot</label>
                <input
                  v-model="bundleForm.nom"
                  type="text"
                  class="form-input"
                  placeholder="Ex: Lot Printemps"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label">Description</label>
                <input
                  v-model="bundleForm.description"
                  type="text"
                  class="form-input"
                  placeholder="Ex: Collection Printemps 2025"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label">Stock initial</label>
                <input
                  v-model.number="bundleForm.quantite_stock"
                  type="number"
                  min="0"
                  class="form-input"
                  placeholder="0"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label">Seuil d'alerte</label>
                <input
                  v-model.number="bundleForm.seuil_alerte"
                  type="number"
                  min="0"
                  class="form-input"
                  placeholder="10"
                  required
                />
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
                      :key="product.id_article"
                      :value="product.id_article"
                    >
                      {{ product.nom }} - {{ product.reference }} -
                      {{ formatCurrency(product.prix_achat) }}
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
                      <line x1="12" y1="5" x2="12" y2="19" />
                      <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                  </button>
                </div>

                <div class="selected-products" v-if="bundleForm.products.length > 0">
                  <div
                    v-for="(item, index) in bundleForm.products"
                    :key="item.id_article"
                    class="product-item"
                  >
                    <span class="product-name">{{ getProductName(item.id_article) }}</span>
                    <span class="product-quantity">x{{ item.quantite_article }}</span>
                    <span class="product-price">{{
                      formatCurrency(getProductPrice(item.id_article) * item.quantite_article)
                    }}</span>
                    <button
                      type="button"
                      @click="removeProductFromBundle(index)"
                      class="remove-btn"
                    >
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                      </svg>
                    </button>
                  </div>
                </div>

                <div v-else class="no-products">Aucun article ajouté au lot</div>
              </div>

              <div class="price-summary">
                <div class="summary-row">
                  <span>Valeur totale des articles:</span>
                  <span>{{ formatCurrency(calculateTotalValue()) }}</span>
                </div>
              </div>
            </div>

            <div class="modal-actions">
              <button type="button" class="modal-btn secondary" @click="closeBundleModal">
                Annuler
              </button>
              <button type="submit" class="modal-btn primary" :disabled="!isFormValid || saving">
                <span v-if="saving">Enregistrement...</span>
                <span v-else>{{ isEditing ? "Mettre à jour" : "Créer le lot" }}</span>
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
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="selectedBundle">
          <div class="details-grid">
            <div class="details-section">
              <h4 class="section-subtitle">Informations générales</h4>
              <div class="detail-row">
                <span class="detail-label">Nom:</span>
                <span class="detail-value">{{ selectedBundle.nom }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span class="detail-value">{{ selectedBundle.description }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Date création:</span>
                <span class="detail-value">{{ formatDate(selectedBundle.date_creation) }}</span>
              </div>
            </div>

            <div class="details-section">
              <h4 class="section-subtitle">Stock</h4>
              <div class="detail-row">
                <span class="detail-label">Quantité en stock:</span>
                <span class="detail-value">{{ selectedBundle.quantite_stock }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Seuil d'alerte:</span>
                <span class="detail-value">{{ selectedBundle.seuil_alerte }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Statut:</span>
                <span class="detail-value">
                  <span
                    class="status-badge"
                    :class="
                      getStatusClass(selectedBundle.quantite_stock, selectedBundle.seuil_alerte)
                    "
                  >
                    {{ getStatusLabel(selectedBundle.quantite_stock, selectedBundle.seuil_alerte) }}
                  </span>
                </span>
              </div>
            </div>

            <div class="details-section full-width">
              <h4 class="section-subtitle">
                Articles inclus ({{ getBundleProducts(selectedBundle.id_lot).length }})
              </h4>
              <div class="products-list">
                <div
                  v-for="item in getBundleProducts(selectedBundle.id_lot)"
                  :key="item.id_article"
                  class="product-detail"
                >
                  <span class="product-info"
                    >{{ getProductName(item.id_article) }} -
                    {{ getProductReference(item.id_article) }}</span
                  >
                  <span class="product-qty">x{{ item.quantite_article }}</span>
                  <span class="product-subtotal">{{
                    formatCurrency(getProductPrice(item.id_article) * item.quantite_article)
                  }}</span>
                </div>
                <div
                  v-if="getBundleProducts(selectedBundle.id_lot).length === 0"
                  class="no-products"
                >
                  Aucun article dans ce lot
                </div>
              </div>
              <div class="total-value">
                <span>Valeur totale:</span>
                <span class="amount">{{
                  formatCurrency(calculateBundleTotalValue(selectedBundle.id_lot))
                }}</span>
              </div>
            </div>
          </div>

          <div class="modal-actions">
            <button class="modal-btn secondary" @click="closeDetailsModal">Fermer</button>
            <button
              class="modal-btn primary"
              @click="
                () => {
                  closeDetailsModal();
                  editBundle(selectedBundle);
                }
              "
            >
              Modifier le lot
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted } from "vue";
  import { VITE_API_URL } from "@/constants/constants";

  export default {
    name: "ProductBundlesView",
    setup() {
      // États réactifs
      const bundles = ref([]);
      const products = ref([]);
      const bundleProducts = ref([]);
      const loading = ref(true);
      const error = ref(null);
      const searchQuery = ref("");
      const currentPage = ref(1);
      const itemsPerPage = 10;
      const showBundleModal = ref(false);
      const showDetailsModal = ref(false);
      const selectedBundle = ref(null);
      const isEditing = ref(false);
      const selectedProduct = ref("");
      const productQuantity = ref(1);
      const saving = ref(false);
      const successMessage = ref("");
      const errorMessage = ref("");

      const filters = ref({
        stockStatus: "",
        sort: "name",
      });

      const bundleForm = ref({
        nom: "",
        description: "",
        quantite_stock: 0,
        seuil_alerte: 10,
        products: [],
      });

      // Fonction pour récupérer les données depuis l'API
      const fetchData = async () => {
        loading.value = true;
        error.value = null;

        try {
          // Récupérer les lots
          const lotsResponse = await fetch(VITE_API_URL + "get_table?table=lot", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          // Récupérer les articles
          const articlesResponse = await fetch(VITE_API_URL + "get_table?table=article", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          // Récupérer les associations articles-lots
          const articleLotsResponse = await fetch(VITE_API_URL + "get_table?table=article_lot", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          if (!lotsResponse.ok || !articlesResponse.ok || !articleLotsResponse.ok) {
            throw new Error("Erreur lors du chargement des données");
          }

          const lotsData = await lotsResponse.json();
          const articlesData = await articlesResponse.json();
          const articleLotsData = await articleLotsResponse.json();

          if (lotsData.success && lotsData.data) {
            bundles.value = lotsData.data;
          }

          if (articlesData.success && articlesData.data) {
            products.value = articlesData.data;
          }

          if (articleLotsData.success && articleLotsData.data) {
            bundleProducts.value = articleLotsData.data;
          }
        } catch (err) {
          console.error("Erreur lors du chargement:", err);
          error.value = "Impossible de charger les données. Veuillez réessayer.";
        } finally {
          loading.value = false;
        }
      };

      // Computed properties
      const availableProducts = computed(() => {
        return products.value.filter(product => {
          return !bundleForm.value.products.some(item => item.id_article === product.id_article);
        });
      });

      const filteredBundles = computed(() => {
        let result = [...bundles.value];

        // Filtre par statut de stock
        if (filters.value.stockStatus === "low") {
          result = result.filter(
            bundle => Number(bundle.quantite_stock) <= Number(bundle.seuil_alerte),
          );
        } else if (filters.value.stockStatus === "normal") {
          result = result.filter(
            bundle => Number(bundle.quantite_stock) > Number(bundle.seuil_alerte),
          );
        }

        // Filtre par recherche
        if (searchQuery.value) {
          const query = searchQuery.value.toLowerCase();
          result = result.filter(
            bundle =>
              bundle.nom.toLowerCase().includes(query) ||
              bundle.description.toLowerCase().includes(query),
          );
        }

        // Tri
        switch (filters.value.sort) {
          case "name":
            result.sort((a, b) => a.nom.localeCompare(b.nom));
            break;
          case "date":
            result.sort((a, b) => new Date(b.date_creation) - new Date(a.date_creation));
            break;
          case "stock":
            result.sort((a, b) => Number(a.quantite_stock) - Number(b.quantite_stock));
            break;
        }

        return result;
      });

      const totalPages = computed(() => {
        return Math.ceil(filteredBundles.value.length / itemsPerPage);
      });

      const startIndex = computed(() => {
        return (currentPage.value - 1) * itemsPerPage;
      });

      const endIndex = computed(() => {
        return Math.min(startIndex.value + itemsPerPage, filteredBundles.value.length);
      });

      const paginatedBundles = computed(() => {
        return filteredBundles.value.slice(startIndex.value, endIndex.value);
      });

      const isFormValid = computed(() => {
        return (
          bundleForm.value.nom &&
          bundleForm.value.description &&
          bundleForm.value.quantite_stock >= 0 &&
          bundleForm.value.seuil_alerte >= 0
        );
      });

      // Fonctions
      const formatCurrency = amount => {
        return new Intl.NumberFormat("fr-FR", {
          style: "currency",
          currency: "EUR",
        }).format(amount);
      };

      const formatDate = dateString => {
        const date = new Date(dateString);
        return date.toLocaleDateString("fr-FR");
      };

      const getBundleColor = id => {
        const colors = ["#00B8D4", "#2563EB", "#059669", "#D97706", "#7C3AED", "#DC2626"];
        return colors[id % colors.length];
      };

      const getStatusClass = (stock, threshold) => {
        return Number(stock) <= Number(threshold) ? "status-alert" : "status-normal";
      };

      const getStatusLabel = (stock, threshold) => {
        return Number(stock) <= Number(threshold) ? "Stock faible" : "Stock normal";
      };

      const getStockClass = (stock, threshold) => {
        return Number(stock) <= Number(threshold) ? "stock-low" : "stock-high";
      };

      const getBundleProductsCount = bundleId => {
        return bundleProducts.value.filter(item => Number(item.id_lot) === Number(bundleId)).length;
      };

      const getBundleProducts = bundleId => {
        return bundleProducts.value.filter(item => Number(item.id_lot) === Number(bundleId));
      };

      const getProductName = productId => {
        const product = products.value.find(p => Number(p.id_article) === Number(productId));
        return product ? product.nom : `Article #${productId}`;
      };

      const getProductReference = productId => {
        const product = products.value.find(p => Number(p.id_article) === Number(productId));
        return product ? product.reference : "";
      };

      const getProductPrice = productId => {
        const product = products.value.find(p => Number(p.id_article) === Number(productId));
        return product ? Number(product.prix_achat) : 0;
      };

      const calculateTotalValue = () => {
        return bundleForm.value.products.reduce((total, item) => {
          return total + getProductPrice(item.id_article) * Number(item.quantite_article);
        }, 0);
      };

      const calculateBundleTotalValue = bundleId => {
        const products = getBundleProducts(bundleId);
        return products.reduce((total, item) => {
          return total + getProductPrice(item.id_article) * Number(item.quantite_article);
        }, 0);
      };

      const addProductToBundle = () => {
        if (selectedProduct.value && productQuantity.value > 0) {
          bundleForm.value.products.push({
            id_article: Number(selectedProduct.value),
            quantite_article: productQuantity.value,
          });
          selectedProduct.value = "";
          productQuantity.value = 1;
        }
      };

      const removeProductFromBundle = index => {
        bundleForm.value.products.splice(index, 1);
      };

      const resetFilters = () => {
        searchQuery.value = "";
        filters.value.stockStatus = "";
        filters.value.sort = "name";
        currentPage.value = 1;
      };

      const openNewBundleModal = () => {
        isEditing.value = false;
        bundleForm.value = {
          nom: "",
          description: "",
          quantite_stock: 0,
          seuil_alerte: 10,
          products: [],
        };
        successMessage.value = "";
        errorMessage.value = "";
        showBundleModal.value = true;
      };

      const editBundle = bundle => {
        isEditing.value = true;
        bundleForm.value = {
          id_lot: bundle.id_lot,
          nom: bundle.nom,
          description: bundle.description,
          quantite_stock: Number(bundle.quantite_stock),
          seuil_alerte: Number(bundle.seuil_alerte),
          products: getBundleProducts(bundle.id_lot).map(item => ({
            id_article: Number(item.id_article),
            quantite_article: Number(item.quantite_article),
          })),
        };
        successMessage.value = "";
        errorMessage.value = "";
        showBundleModal.value = true;
      };

      const viewBundle = bundle => {
        selectedBundle.value = bundle;
        showDetailsModal.value = true;
      };

      const saveBundle = async () => {
        if (saving.value) return;

        saving.value = true;
        successMessage.value = "";
        errorMessage.value = "";

        try {
          // Préparer les données pour l'API avec URLSearchParams
          const formData = new URLSearchParams();

          if (isEditing.value) {
            // Pour la mise à jour, ajouter l'ID du lot
            formData.append("id_lot", bundleForm.value.id_lot.toString());
          }

          formData.append("nom", bundleForm.value.nom);
          formData.append("description", bundleForm.value.description);
          formData.append("date_creation", new Date().toISOString().split("T")[0]); // Format YYYY-MM-DD
          formData.append("quantite_stock", bundleForm.value.quantite_stock.toString());
          formData.append("seuil_alerte", bundleForm.value.seuil_alerte.toString());

          // Appel API pour créer ou mettre à jour le lot
          const endpoint = isEditing.value ? "update_lot" : "new_lot";
          const response = await fetch(VITE_API_URL + endpoint, {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
            body: formData.toString(),
          });

          if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
          }

          const result = await response.json();
          console.log(`Réponse de ${endpoint}:`, result);

          if (result.success) {
            // Récupérer l'ID du lot (créé ou mis à jour)
            const lotId = isEditing.value ? bundleForm.value.id_lot : result.id_lot;
            console.log("ID du lot:", lotId);

            // Si mise à jour, d'abord supprimer les anciennes associations
            if (isEditing.value && lotId) {
              console.log("Suppression des anciennes associations article_lot...");

              // Récupérer les associations existantes
              const existingProducts = getBundleProducts(lotId);

              // Pour chaque association existante, la supprimer
              for (const existing of existingProducts) {
                try {
                  const deleteData = new URLSearchParams();
                  deleteData.append("id_article", existing.id_article.toString());
                  deleteData.append("id_lot", lotId.toString());

                  // Appel à un endpoint delete_article_lot (à créer si nécessaire)
                  const deleteResponse = await fetch(VITE_API_URL + "delete_article_lot", {
                    method: "POST",
                    headers: {
                      "Content-Type": "application/x-www-form-urlencoded",
                    },
                    credentials: "include",
                    body: deleteData.toString(),
                  });

                  if (!deleteResponse.ok) {
                    console.error("Erreur lors de la suppression de l'association");
                  }
                } catch (error) {
                  console.error("Erreur:", error);
                }
              }
            }

            // Créer les (nouvelles) associations article_lot
            if (bundleForm.value.products.length > 0 && lotId) {
              console.log("Création des associations article_lot...");

              for (const product of bundleForm.value.products) {
                try {
                  const articleLotData = new URLSearchParams();
                  articleLotData.append("id_article", product.id_article.toString());
                  articleLotData.append("id_lot", lotId.toString());
                  articleLotData.append("quantite", product.quantite_article.toString());

                  console.log("Envoi:", {
                    id_article: product.id_article.toString(),
                    id_lot: lotId.toString(),
                    quantite: product.quantite_article.toString(),
                  });

                  const articleResponse = await fetch(VITE_API_URL + "new_article_lot", {
                    method: "POST",
                    headers: {
                      "Content-Type": "application/x-www-form-urlencoded",
                    },
                    credentials: "include",
                    body: articleLotData.toString(),
                  });

                  const responseText = await articleResponse.text();

                  try {
                    const articleResult = JSON.parse(responseText);

                    if (!articleResponse.ok || !articleResult.success) {
                      console.error(
                        `Erreur lors de l'association de l'article ${product.id_article}:`,
                        articleResult.message || "Erreur inconnue",
                      );
                    }
                  } catch (parseError) {
                    console.error("Erreur de parsing JSON:", parseError);
                    console.error("Réponse non-JSON:", responseText);
                  }
                } catch (error) {
                  console.error(
                    `Erreur lors de l'association de l'article ${product.id_article}:`,
                    error,
                  );
                }
              }
            }

            // Afficher le message de succès
            successMessage.value = isEditing.value
              ? "Lot modifié avec succès!"
              : "Lot créé avec succès!";

            // Attendre 2 secondes puis fermer la modal et recharger
            setTimeout(async () => {
              closeBundleModal();
              await fetchData();
            }, 2000);
          } else {
            throw new Error(result.message || "Erreur lors de la sauvegarde du lot");
          }
        } catch (error) {
          console.error("Erreur lors de la sauvegarde:", error);
          errorMessage.value = "Erreur lors de la sauvegarde: " + error.message;
        } finally {
          saving.value = false;
        }
      };

      const closeBundleModal = () => {
        showBundleModal.value = false;
        selectedProduct.value = "";
        productQuantity.value = 1;
        saving.value = false;
        successMessage.value = "";
        errorMessage.value = "";
      };

      const closeDetailsModal = () => {
        showDetailsModal.value = false;
        selectedBundle.value = null;
      };

      // Charger les données au montage
      onMounted(() => {
        fetchData();
      });

      return {
        bundles,
        products,
        bundleProducts,
        loading,
        error,
        searchQuery,
        currentPage,
        showBundleModal,
        showDetailsModal,
        selectedBundle,
        isEditing,
        selectedProduct,
        productQuantity,
        saving,
        successMessage,
        errorMessage,
        filters,
        bundleForm,
        availableProducts,
        filteredBundles,
        totalPages,
        startIndex,
        endIndex,
        paginatedBundles,
        isFormValid,
        formatCurrency,
        formatDate,
        getBundleColor,
        getStatusClass,
        getStatusLabel,
        getStockClass,
        getBundleProductsCount,
        getBundleProducts,
        getProductName,
        getProductReference,
        getProductPrice,
        calculateTotalValue,
        calculateBundleTotalValue,
        addProductToBundle,
        removeProductFromBundle,
        resetFilters,
        openNewBundleModal,
        editBundle,
        viewBundle,
        saveBundle,
        closeBundleModal,
        closeDetailsModal,
        fetchData,
      };
    },
  };
</script>

<style scoped>
  .bundles-page {
    padding: 0;
    font-family: "Inter", sans-serif;
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
    color: #0f172a;
    margin: 0;
  }

  .new-bundle-button {
    background: #5500ff;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(85, 0, 255, 0.2);
    font-size: 14px;
    font-weight: 500;
  }

  .new-bundle-button:hover {
    background: #5500cc;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(85, 0, 255, 0.3);
  }

  .new-bundle-button svg {
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
    gap: 2rem 1rem;
    flex-wrap: wrap;
  }

  .search-container {
    position: relative;
    flex: 1;
    min-width: 315px;
  }

  .search-input {
    width: 100%;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    font-size: 14px;
    color: #334155;
    transition: all 0.2s ease;
  }

  .search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    color: #94a3b8;
    stroke-width: 2;
    pointer-events: none;
  }

  .filter-wrapper {
    position: relative;
    display: inline-block;
  }

  .filter-wrapper label {
    position: absolute;
    top: -25px;
    left: 3px;
    font-size: 14px;
  }

  .filter-select {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding-top: 0.75rem;
    padding-right: 2.5rem;
    padding-bottom: 0.75rem;
    padding-left: 1rem;
    font-size: 14px;
    color: #64748b;
    min-width: 140px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .filter-select:hover,
  .search-input:hover {
    box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
  }

  .filter-select:focus,
  .search-input:focus {
    outline: none;
    border-color: #00b8d4;
    box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
  }

  .filter-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
  }

  .filter-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translate(25%, -50%);
    pointer-events: none;
    height: 20px;
  }

  .export-button {
    background: #0062ff;
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

  .export-button:hover {
    background: #0056cc;
    transform: translateY(-1px);
  }

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
    color: #64748b;
  }

  .loader {
    width: 40px;
    height: 40px;
    border: 3px solid #f1f5f9;
    border-top-color: #00b8d4;
    border-radius: 50%;
    margin: 0 auto 1rem;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  .error-icon {
    width: 48px;
    height: 48px;
    color: #dc2626;
    margin: 0 auto 1rem;
  }

  .error-message {
    color: #dc2626;
    margin-bottom: 1rem;
    font-weight: 500;
  }

  .retry-button {
    background: #00b8d4;
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
    background: #0891a6;
  }

  /* TABLEAU */
  .table-section {
    margin-bottom: 2rem;
  }

  .table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
    overflow: hidden;
  }

  .table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .table-title {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
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
    color: #64748b;
  }

  .stat-value {
    font-weight: 600;
    color: #0f172a;
  }

  .table-container {
    overflow-x: auto;
  }

  .data-table {
    width: 100%;
    min-width: max-content;
    text-align: center;
  }

  .data-table th {
    background: #f8fafc;
    padding: 1rem;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #e2e8f0;
  }

  .data-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: #334155;
    vertical-align: middle;
  }

  .data-table tbody tr:hover {
    background: #f8fafc;
  }

  .data-table tbody tr:last-child td {
    border-bottom: none;
  }

  /* BUNDLE INFO */
  .bundle-info {
    text-align: left;
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
    color: #0f172a;
    margin: 0 0 0.25rem 0;
    line-height: 1.2;
  }

  .bundle-code {
    font-size: 12px;
    color: #64748b;
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
    color: #0f172a;
  }

  .products-preview .label {
    font-size: 12px;
    color: #64748b;
  }

  /* STOCK */
  .stock-value,
  .threshold-value {
    font-weight: 600;
  }

  .stock-low {
    color: #dc2626;
  }

  .stock-high {
    color: #059669;
  }

  .threshold-value {
    color: #f59e0b;
  }

  .date {
    color: #64748b;
    font-size: 13px;
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

  .status-normal {
    background: #d1fae5;
    color: #047857;
  }

  .status-alert {
    background: #fef2f2;
    color: #dc2626;
  }

  /* ACTIONS */
  .actions {
    display: flex;
    gap: 1.5rem;
  }

  .action-btn {
    background: none;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 6px;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .action-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155;
  }

  .action-btn svg {
    width: 16px;
    height: 16px;
    stroke-width: 1.5;
  }

  .empty-message {
    text-align: center;
    color: #64748b;
    font-style: italic;
    padding: 2rem;
  }

  /* PAGINATION */
  .pagination-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
  }

  .pagination-info {
    font-size: 14px;
    color: #64748b;
  }

  .pagination-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .pagination-btn {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 6px 10px;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .pagination-btn:hover:not(:disabled) {
    background: #f8fafc;
    border-color: #cbd5e1;
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
    color: #64748b;
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
    border-bottom: 1px solid #e2e8f0;
  }

  .modal-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
  }

  .modal-close {
    background: none;
    border: none;
    color: #64748b;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
  }

  .modal-close:hover {
    background: #f1f5f9;
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

  /* MESSAGES DE SUCCÈS/ERREUR */
  .success-message,
  .error-message-box {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-size: 14px;
    font-weight: 500;
  }

  .success-message {
    background: #d1fae5;
    color: #047857;
    border: 1px solid #a7f3d0;
  }

  .success-message svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
  }

  .error-message-box {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
  }

  .error-message-box svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
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
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    color: #0f172a;
    background: white;
    transition: all 0.2s ease;
  }

  .form-input:focus,
  .form-select:focus {
    outline: none;
    border-color: #00b8d4;
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
    color: #0f172a;
    margin: 0;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
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
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    text-align: center;
  }

  .add-btn {
    background: #00b8d4;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .add-btn:hover:not(:disabled) {
    background: #0891a6;
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
    background: #f8fafc;
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
    color: #0f172a;
  }

  .product-quantity {
    font-weight: 600;
    color: #64748b;
  }

  .product-price {
    font-weight: 600;
    color: #00b8d4;
  }

  .remove-btn {
    background: none;
    border: none;
    color: #dc2626;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
  }

  .remove-btn:hover {
    background: #fef2f2;
  }

  .remove-btn svg {
    width: 16px;
    height: 16px;
    stroke-width: 2;
  }

  .no-products {
    text-align: center;
    color: #64748b;
    font-style: italic;
    padding: 2rem;
  }

  .price-summary {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
  }

  .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    font-size: 14px;
    color: #64748b;
  }

  .modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
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
    background: #00b8d4;
    color: white;
  }

  .modal-btn.primary:hover:not(:disabled) {
    background: #0891a6;
  }

  .modal-btn.primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .modal-btn.secondary {
    background: #f1f5f9;
    color: #64748b;
  }

  .modal-btn.secondary:hover {
    background: #e2e8f0;
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
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
  }

  .details-section.full-width {
    grid-column: 1 / -1;
  }

  .section-subtitle {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 1rem 0;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;
  }

  .detail-row:last-child {
    border-bottom: none;
  }

  .detail-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
  }

  .detail-value {
    font-size: 14px;
    color: #0f172a;
    font-weight: 600;
    text-align: right;
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
    border-bottom: 1px solid #e2e8f0;
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
    color: #64748b;
    margin-right: 2rem;
  }

  .product-subtotal {
    font-weight: 600;
    color: #0f172a;
  }

  .total-value {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
    font-weight: 600;
    font-size: 16px;
    color: #0f172a;
  }

  .total-value .amount {
    color: #00b8d4;
  }

  /* RESPONSIVE */
  @media (max-width: 1024px) {
    .page-header {
      flex-direction: column;
      gap: 1rem;
      align-items: stretch;
    }

    .new-bundle-button {
      width: 100%;
      justify-content: center;
    }

    .form-grid {
      grid-template-columns: 1fr;
    }

    .details-grid {
      grid-template-columns: 1fr;
    }
  }

  /* RESPONSIVE */
  @media (max-width: 768px) {
    .search-container {
      min-width: auto;
    }

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

    .actions {
      gap: 0.5rem;
    }

    .pagination-container {
      flex-direction: column;
      gap: 1rem;
    }

    .page-numbers {
      flex-wrap: wrap;
      justify-content: center;
    }

    .form-grid {
      grid-template-columns: 1fr;
    }

    .details-grid {
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }

    .form-actions {
      flex-direction: column-reverse;
    }

    .btn-secondary,
    .btn-primary {
      width: 100%;
    }
  }

  @media (max-width: 425px) {
    .search-container > input:placeholder-shown {
      text-overflow: ellipsis;
    }

    .search-container > input:focus::placeholder {
      color: transparent;
    }
  }
</style>
