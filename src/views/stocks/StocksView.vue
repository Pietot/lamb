<template>
  <div class="stocks-page">
    <!-- En-tête avec bouton Ajouter -->
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Stock</h1>
      </div>
      <div class="header-right">
        <button class="add-button" @click="openAddModal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="16" />
            <line x1="8" y1="12" x2="16" y2="12" />
          </svg>
          <span>Ajouter un article</span>
        </button>
      </div>
    </div>

    <!-- Filtres de recherche -->
    <div class="filters-section">
      <div class="filter-group">
        <div class="search-container">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher par ID, référence, nom ou description"
            class="search-input"
          />
          <svg
            class="search-icon"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
          >
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </div>

        <div class="filter-wrapper">
          <select v-model="filters.category" class="filter-select">
            <option value="">Toutes les catégories</option>
            <option value="1">Vêtements Homme</option>
            <option value="2">Vêtements Femme</option>
            <option value="3">Accessoires</option>
            <option value="4">Enfants</option>
            <option value="5">Chaussures</option>
          </select>
          <svg
            class="filter-icon"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
          >
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>

        <div class="filter-wrapper">
          <select v-model="filters.stockStatus" class="filter-select">
            <option value="">Tous les stocks</option>
            <option value="low">Stock faible</option>
            <option value="normal">Stock normal</option>
          </select>
          <svg
            class="filter-icon"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
          >
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>

        <button class="search-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
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
              <span class="stat-label">Total articles :</span>
              <span class="stat-value">{{ totalArticles }}</span>
            </span>
            <span class="stat-item">
              <span class="stat-label">Stock faible :</span>
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
                <th>Référence</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Catégorie</th>
                <th>En stock</th>
                <th>Seuil</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="item in paginatedArticles"
                :key="item.id_article"
                :class="{
                  'low-stock': item.quantite_stock <= item.seuil_alerte,
                }"
              >
                <td class="id">{{ item.id_article }}</td>
                <td class="reference">
                  {{ item.reference || "#" + item.id_article }}
                </td>
                <td class="product-name">{{ item.nom }}</td>
                <td class="description">{{ item.description }}</td>
                <td>{{ getCategoryName(item.id_categorie) }}</td>
                <td class="stock-value">
                  <span
                    class="stock-badge"
                    :class="{ low: item.quantite_stock <= item.seuil_alerte }"
                  >
                    {{ item.quantite_stock }}
                  </span>
                </td>
                <td class="threshold-value">{{ item.seuil_alerte }}</td>
                <td class="actions">
                  <button
                    class="action-btn"
                    @click="editArticle(item)"
                    title="Modifier"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path
                        d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"
                      />
                      <path
                        d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"
                      />
                    </svg>
                  </button>
                  <button
                    class="action-btn"
                    @click="viewArticle(item)"
                    title="Voir détails"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                </td>
              </tr>
              <tr v-if="paginatedArticles.length === 0">
                <td colspan="100%" class="empty-message">
                  Aucun article trouvé
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          v-if="!loading && !error && totalPages > 1"
          class="pagination-container"
        >
          <div class="pagination-info">
            Affichage {{ startIndex + 1 }}-{{ endIndex }} sur
            {{ filteredArticles.length }} articles
          </div>
          <div class="pagination-controls">
            <button
              class="pagination-btn"
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polyline points="15 18 9 12 15 6" />
              </svg>
            </button>

            <div class="page-numbers">
              <button
                v-for="page in visiblePages"
                :key="page"
                class="page-number"
                :class="{ active: page === currentPage }"
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
                <polyline points="9 18 15 12 9 6" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ajouter un article -->
    <div v-if="showAddModal" class="modal-overlay" @click="closeAddModal">
      <div class="modal-content modal-form" @click.stop>
        <div class="modal-header">
          <h3>Ajouter un article</h3>
          <button @click="closeAddModal" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submitNewArticle" class="article-form">
            <div class="form-grid">
              <div class="form-group">
                <label for="reference" class="form-label"
                  >Référence<span class="required-indicator">*</span></label
                >
                <input
                  v-model="newArticle.reference"
                  type="text"
                  id="reference"
                  class="form-input"
                  :class="{ error: formErrors.reference }"
                  maxlength="20"
                  required
                />
                <span v-if="formErrors.reference" class="error-text">{{
                  formErrors.reference
                }}</span>
              </div>

              <div class="form-group">
                <label for="nom" class="form-label"
                  >Nom<span class="required-indicator">*</span></label
                >
                <input
                  v-model="newArticle.nom"
                  type="text"
                  id="nom"
                  class="form-input"
                  :class="{ error: formErrors.nom }"
                  maxlength="50"
                  required
                />
                <span v-if="formErrors.nom" class="error-text">{{
                  formErrors.nom
                }}</span>
              </div>

              <div class="form-group full-width">
                <label for="description" class="form-label"
                  >Description<span class="required-indicator">*</span></label
                >
                <textarea
                  v-model="newArticle.description"
                  id="description"
                  class="form-input form-textarea"
                  :class="{ error: formErrors.description }"
                  maxlength="200"
                  rows="3"
                  required
                ></textarea>
                <span v-if="formErrors.description" class="error-text">{{
                  formErrors.description
                }}</span>
              </div>

              <div class="form-group">
                <label for="quantite" class="form-label"
                  >Quantité initiale<span class="required-indicator"
                    >*</span
                  ></label
                >
                <input
                  v-model.number="newArticle.quantite"
                  type="number"
                  id="quantite"
                  class="form-input"
                  :class="{ error: formErrors.quantite }"
                  min="0"
                  required
                />
                <span v-if="formErrors.quantite" class="error-text">{{
                  formErrors.quantite
                }}</span>
              </div>

              <div class="form-group">
                <label for="seuil_alerte" class="form-label"
                  >Seuil d'alerte<span class="required-indicator"
                    >*</span
                  ></label
                >
                <input
                  v-model.number="newArticle.seuil_alerte"
                  type="number"
                  id="seuil_alerte"
                  class="form-input"
                  :class="{ error: formErrors.seuil_alerte }"
                  min="0"
                  required
                />
                <span v-if="formErrors.seuil_alerte" class="error-text">{{
                  formErrors.seuil_alerte
                }}</span>
              </div>

              <div class="form-group">
                <label for="id_categorie" class="form-label"
                  >Catégorie<span class="required-indicator">*</span></label
                >
                <select
                  v-model="newArticle.id_categorie"
                  id="id_categorie"
                  class="form-input"
                  :class="{ error: formErrors.id_categorie }"
                  required
                >
                  <option value="">Sélectionner une catégorie</option>
                  <option value="1">Vêtements Homme</option>
                  <option value="2">Vêtements Femme</option>
                  <option value="3">Accessoires</option>
                  <option value="4">Enfants</option>
                  <option value="5">Chaussures</option>
                </select>
                <span v-if="formErrors.id_categorie" class="error-text">{{
                  formErrors.id_categorie
                }}</span>
              </div>

              <div class="form-group">
                <label for="id_fournisseur" class="form-label"
                  >Fournisseur principal<span class="required-indicator"
                    >*</span
                  ></label
                >
                <select
                  v-model="newArticle.id_fournisseur"
                  id="id_fournisseur"
                  class="form-input"
                  :class="{ error: formErrors.id_fournisseur }"
                  required
                >
                  <option value="">Sélectionner un fournisseur</option>
                  <option
                    v-for="supplier in suppliers"
                    :key="supplier.id_fournisseur"
                    :value="supplier.id_fournisseur"
                  >
                    {{ supplier.nom || supplier.raison_sociale }}
                  </option>
                </select>
                <span v-if="formErrors.id_fournisseur" class="error-text">{{
                  formErrors.id_fournisseur
                }}</span>
              </div>

              <div class="form-group">
                <label for="reference_fournisseur" class="form-label"
                  >Référence fournisseur<span class="required-indicator"
                    >*</span
                  ></label
                >
                <input
                  v-model="newArticle.reference_fournisseur"
                  type="text"
                  id="reference_fournisseur"
                  class="form-input"
                  :class="{ error: formErrors.reference_fournisseur }"
                  maxlength="20"
                  required
                />
                <span
                  v-if="formErrors.reference_fournisseur"
                  class="error-text"
                  >{{ formErrors.reference_fournisseur }}</span
                >
              </div>

              <div class="form-group">
                <label for="prix_achat" class="form-label"
                  >Prix d'achat (€)<span class="required-indicator"
                    >*</span
                  ></label
                >
                <input
                  v-model.number="newArticle.prix_achat"
                  type="number"
                  id="prix_achat"
                  class="form-input"
                  :class="{ error: formErrors.prix_achat }"
                  min="0"
                  step="0.01"
                  required
                />
                <span v-if="formErrors.prix_achat" class="error-text">{{
                  formErrors.prix_achat
                }}</span>
              </div>
            </div>

            <div v-if="submitError" class="alert alert-error">
              {{ submitError }}
            </div>

            <div v-if="submitSuccess" class="alert alert-success">
              Article créé avec succès !
            </div>

            <div class="form-actions">
              <button
                type="button"
                @click="closeAddModal"
                class="btn-secondary"
              >
                Annuler
              </button>
              <button type="submit" class="btn-primary" :disabled="submitting">
                <span v-if="submitting">Création en cours...</span>
                <span v-else>Créer l'article</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Voir détails -->
    <div
      v-if="showDetailsModal"
      class="modal-overlay"
      @click="showDetailsModal = false"
    >
      <div class="modal-content modal-details" @click.stop>
        <div class="modal-header">
          <h3>Détails de l'article</h3>
          <button @click="showDetailsModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="selectedArticle">
          <div class="details-grid">
            <div class="details-section">
              <h4 class="section-subtitle">Informations générales</h4>
              <div class="detail-row">
                <span class="detail-label">Référence :</span>
                <span class="detail-value">{{
                  selectedArticle.reference || "#" + selectedArticle.id_article
                }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Nom :</span>
                <span class="detail-value">{{ selectedArticle.nom }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Description :</span>
                <span class="detail-value">{{
                  selectedArticle.description
                }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Catégorie :</span>
                <span class="detail-value">{{
                  getCategoryName(selectedArticle.id_categorie)
                }}</span>
              </div>
            </div>

            <div class="details-section">
              <h4 class="section-subtitle">Informations de stock</h4>
              <div class="detail-row">
                <span class="detail-label">Quantité en stock :</span>
                <span class="detail-value">
                  <span class="stock-badge">
                    {{ selectedArticle.quantite_stock }}
                  </span>
                </span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Seuil d'alerte :</span>
                <span class="detail-value">{{
                  selectedArticle.seuil_alerte
                }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Statut :</span>
                <span class="detail-value">
                  <span
                    v-if="
                      selectedArticle.quantite_stock <=
                      selectedArticle.seuil_alerte
                    "
                    class="status-badge status-critical"
                  >
                    Stock critique
                  </span>
                  <span v-else class="status-badge status-ok">
                    Stock normal
                  </span>
                </span>
              </div>
            </div>

            <div class="details-section full-width">
              <h4 class="section-subtitle">Informations commerciales</h4>
              <div class="detail-row">
                <span class="detail-label">Prix d'achat :</span>
                <span class="detail-value">{{
                  formatCurrency(selectedArticle.prix_achat)
                }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Valeur du stock :</span>
                <span class="detail-value">{{
                  formatCurrency(
                    selectedArticle.prix_achat * selectedArticle.quantite_stock
                  )
                }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Fournisseur principal :</span>
                <span class="detail-value">{{
                  getSupplierName(selectedArticle.id_fournisseur_principal)
                }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Référence fournisseur :</span>
                <span class="detail-value">{{
                  selectedArticle.reference_fournisseur || "N/A"
                }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Modifier (TODO) -->
    <div
      v-if="showEditModal"
      class="modal-overlay"
      @click="showEditModal = false"
    >
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Modifier l'article</h3>
          <button @click="showEditModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="todo-container">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              class="todo-icon"
            >
              <path d="M12 2L2 7l10 5 10-5-10-5z" />
              <path d="M2 17l10 5 10-5" />
              <path d="M2 12l10 5 10-5" />
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
import { ref, reactive, computed, onMounted, watch } from "vue";

export default {
  name: "StocksView",
  setup() {
    const showAddModal = ref(false);
    const showDetailsModal = ref(false);
    const showEditModal = ref(false);
    const selectedArticle = ref(null);
    const articles = ref([]);
    const suppliers = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const searchQuery = ref("");
    const currentPage = ref(1);
    const itemsPerPage = 10;
    const submitting = ref(false);
    const submitError = ref("");
    const submitSuccess = ref(false);

    const filters = reactive({
      category: "",
      stockStatus: "",
    });

    // Formulaire nouvel article
    const newArticle = reactive({
      reference: "",
      description: "",
      nom: "",
      quantite: "",
      seuil_alerte: "",
      id_categorie: "",
      id_fournisseur: "",
      reference_fournisseur: "",
      prix_achat: "",
    });

    const formErrors = reactive({
      reference: "",
      description: "",
      nom: "",
      quantite: "",
      seuil_alerte: "",
      id_categorie: "",
      id_fournisseur: "",
      reference_fournisseur: "",
      prix_achat: "",
    });

    // Mapping des catégories
    const categories = {
      1: "Vêtements Homme",
      2: "Vêtements Femme",
      3: "Accessoires",
      4: "Enfants",
      5: "Chaussures",
    };

    // Fonction pour récupérer les articles
    const fetchArticles = async () => {
      loading.value = true;
      error.value = null;

      try {
        const response = await fetch(
          import.meta.env.VITE_API_URL + "get_table?table=article",
          {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          }
        );

        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const data = await response.json();

        if (data.success && data.data) {
          articles.value = data.data;
        } else {
          throw new Error("Format de données invalide");
        }
      } catch (err) {
        console.error("Erreur lors du chargement des articles:", err);
        error.value = "Impossible de charger les articles. Veuillez réessayer.";
      } finally {
        loading.value = false;
      }
    };

    // Fonction pour récupérer les fournisseurs
    const fetchSuppliers = async () => {
      try {
        const response = await fetch(
          import.meta.env.VITE_API_URL + "get_table?table=fournisseur",
          {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          }
        );

        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const data = await response.json();

        if (data.success && data.data) {
          suppliers.value = data.data;
        }
      } catch (err) {
        console.error("Erreur lors du chargement des fournisseurs:", err);
      }
    };

    // Computed properties
    const filteredArticles = computed(() => {
      let result = articles.value;

      // Filtre par recherche
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
          (article) =>
            article.nom.toLowerCase().includes(query) ||
            article.description.toLowerCase().includes(query) ||
            (article.reference &&
              article.reference.toLowerCase().includes(query)) ||
            article.id_article.toString().includes(query)
        );
      }

      // Filtre par catégorie
      if (filters.category) {
        result = result.filter(
          (article) => article.id_categorie == filters.category
        );
      }

      // Filtre par statut de stock
      if (filters.stockStatus === "low") {
        result = result.filter(
          (article) => article.quantite_stock <= article.seuil_alerte
        );
      } else if (filters.stockStatus === "normal") {
        result = result.filter(
          (article) => article.quantite_stock > article.seuil_alerte
        );
      }

      return result;
    });

    // Pagination computed properties
    const totalPages = computed(() => {
      return Math.ceil(filteredArticles.value.length / itemsPerPage);
    });

    const startIndex = computed(() => {
      return (currentPage.value - 1) * itemsPerPage;
    });

    const endIndex = computed(() => {
      return Math.min(
        startIndex.value + itemsPerPage,
        filteredArticles.value.length
      );
    });

    const paginatedArticles = computed(() => {
      return filteredArticles.value.slice(startIndex.value, endIndex.value);
    });

    const visiblePages = computed(() => {
      const pages = [];
      const total = totalPages.value;
      const current = currentPage.value;

      if (total <= 7) {
        for (let i = 1; i <= total; i++) {
          pages.push(i);
        }
      } else {
        pages.push(1);
        if (current > 3) pages.push("...");

        const start = Math.max(2, current - 1);
        const end = Math.min(total - 1, current + 1);

        for (let i = start; i <= end; i++) {
          pages.push(i);
        }

        if (current < total - 2) pages.push("...");
        pages.push(total);
      }

      return pages;
    });

    const totalArticles = computed(() => filteredArticles.value.length);

    const lowStockCount = computed(() => {
      return filteredArticles.value.filter(
        (item) => item.quantite_stock <= item.seuil_alerte
      ).length;
    });

    // Watchers
    watch([searchQuery, filters], () => {
      currentPage.value = 1;
    });

    // Fonctions utilitaires
    const getCategoryName = (categoryId) => {
      return categories[categoryId] || `Catégorie ${categoryId}`;
    };

    const getSupplierName = (supplierId) => {
      const supplier = suppliers.value.find(
        (s) => s.id_fournisseur === supplierId
      );
      if (supplier) {
        return (
          supplier.nom ||
          supplier.raison_sociale ||
          `Fournisseur #${supplierId}`
        );
      }
      return `Fournisseur #${supplierId}`;
    };

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat("fr-FR", {
        style: "currency",
        currency: "EUR",
      }).format(amount || 0);
    };

    // Validation du formulaire
    const validateForm = () => {
      let isValid = true;

      // Réinitialiser les erreurs
      Object.keys(formErrors).forEach((key) => {
        formErrors[key] = "";
      });

      // Valider la référence
      if (!newArticle.reference) {
        formErrors.reference = "La référence est requise";
        isValid = false;
      } else if (newArticle.reference.length > 20) {
        formErrors.reference =
          "La référence ne doit pas dépasser 20 caractères";
        isValid = false;
      }

      // Valider le nom
      if (!newArticle.nom) {
        formErrors.nom = "Le nom est requis";
        isValid = false;
      } else if (newArticle.nom.length > 50) {
        formErrors.nom = "Le nom ne doit pas dépasser 50 caractères";
        isValid = false;
      }

      // Valider la description
      if (!newArticle.description) {
        formErrors.description = "La description est requise";
        isValid = false;
      } else if (newArticle.description.length > 200) {
        formErrors.description =
          "La description ne doit pas dépasser 200 caractères";
        isValid = false;
      }

      // Valider la quantité
      if (newArticle.quantite === "" || newArticle.quantite < 0) {
        formErrors.quantite = "La quantité doit être un nombre positif";
        isValid = false;
      }

      // Valider le seuil d'alerte
      if (newArticle.seuil_alerte === "" || newArticle.seuil_alerte < 0) {
        formErrors.seuil_alerte =
          "Le seuil d'alerte doit être un nombre positif";
        isValid = false;
      }

      // Valider la catégorie
      if (!newArticle.id_categorie) {
        formErrors.id_categorie = "Veuillez sélectionner une catégorie";
        isValid = false;
      }

      // Valider le fournisseur
      if (!newArticle.id_fournisseur) {
        formErrors.id_fournisseur = "Veuillez sélectionner un fournisseur";
        isValid = false;
      }

      // Valider la référence fournisseur
      if (!newArticle.reference_fournisseur) {
        formErrors.reference_fournisseur =
          "La référence fournisseur est requise";
        isValid = false;
      } else if (newArticle.reference_fournisseur.length > 20) {
        formErrors.reference_fournisseur =
          "La référence fournisseur ne doit pas dépasser 20 caractères";
        isValid = false;
      }

      // Valider le prix d'achat
      if (newArticle.prix_achat === "" || newArticle.prix_achat < 0) {
        formErrors.prix_achat = "Le prix d'achat doit être un nombre positif";
        isValid = false;
      }

      return isValid;
    };

    // Soumettre le nouvel article
    const submitNewArticle = async () => {
      if (!validateForm()) {
        return;
      }

      submitting.value = true;
      submitError.value = "";
      submitSuccess.value = false;

      const formData = new FormData();
      formData.append("reference", newArticle.reference);
      formData.append("description", newArticle.description);
      formData.append("nom", newArticle.nom);
      formData.append("quantite", newArticle.quantite);
      formData.append("seuil_alerte", newArticle.seuil_alerte);
      formData.append("id_categorie", newArticle.id_categorie);
      formData.append("id_fournisseur", newArticle.id_fournisseur);
      formData.append(
        "reference_fournisseur",
        newArticle.reference_fournisseur
      );
      formData.append("prix_achat", newArticle.prix_achat);

      try {
        const response = await fetch(
          import.meta.env.VITE_API_URL + "new_article",
          {
            method: "POST",
            body: formData,
            credentials: "include",
          }
        );

        const data = await response.json();

        if (response.ok && data.success) {
          submitSuccess.value = true;
          // Recharger les articles
          await fetchArticles();
          // Fermer la modale après un délai
          setTimeout(() => {
            closeAddModal();
          }, 1500);
        } else {
          submitError.value =
            data.message || "Erreur lors de la création de l'article";
        }
      } catch (err) {
        console.error("Erreur lors de la création de l'article:", err);
        submitError.value = "Erreur de connexion au serveur";
      } finally {
        submitting.value = false;
      }
    };

    const openAddModal = () => {
      showAddModal.value = true;
      fetchSuppliers();
    };

    const closeAddModal = () => {
      showAddModal.value = false;
      // Réinitialiser le formulaire
      Object.keys(newArticle).forEach((key) => {
        newArticle[key] = "";
      });
      Object.keys(formErrors).forEach((key) => {
        formErrors[key] = "";
      });
      submitError.value = "";
      submitSuccess.value = false;
    };

    const goToPage = (page) => {
      if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
      }
    };

    const editArticle = (article) => {
      selectedArticle.value = article;
      showEditModal.value = true;
    };

    const viewArticle = (article) => {
      selectedArticle.value = article;
      showDetailsModal.value = true;
      fetchSuppliers();
    };

    // Charger les données au montage
    onMounted(() => {
      fetchArticles();
    });

    return {
      showAddModal,
      showDetailsModal,
      showEditModal,
      selectedArticle,
      articles,
      suppliers,
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
      newArticle,
      formErrors,
      submitting,
      submitError,
      submitSuccess,
      fetchArticles,
      getCategoryName,
      getSupplierName,
      formatCurrency,
      goToPage,
      editArticle,
      viewArticle,
      openAddModal,
      closeAddModal,
      submitNewArticle,
    };
  },
};
</script>

<style scoped>
.stocks-page {
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

.add-button {
  background: #00b8d4;
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
  background: #0891a6;
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

.search-container {
  position: relative;
  flex: 1;
}

.search-input {
  width: 100%;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  font-size: 14px;
  color: black;
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

.search-button {
  background: #3b82f6;
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
  background: #2563eb;
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

.error-message {
  color: #dc2626;
  margin-bottom: 1rem;
}

.retry-button {
  background: #00b8d4;
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
  text-align: center;
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

.stat-value.low {
  color: #dc2626;
}

.table-container {
  overflow-x: auto;
}

.stocks-table {
  width: 100%;
  min-width: max-content;
}

.stocks-table th {
  background: #f8fafc;
  padding: 1rem;
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #e2e8f0;
}

.stocks-table td {
  padding: 1rem;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
  color: black;
}

.stocks-table tbody tr:hover {
  background: #f8fafc;
}

.stocks-table tbody tr:last-child td {
  border-bottom: none;
}

.stocks-table tr.low-stock {
  background: #fef2f2;
}

.stocks-table tr.low-stock:hover {
  background: #fee2e2;
}

.reference {
  font-weight: 600;
  color: #0f172a;
}

.product-name {
  font-weight: 600;
  color: #0f172a;
}

.description {
  font-size: 13px;
  color: #64748b;
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
  background: #f0fdf4;
  color: #166534;
}

.stock-badge.low {
  background: #fee2e2;
  color: #991b1b;
}

.threshold-value {
  font-weight: 600;
  text-align: center;
  color: #64748b;
}

.empty-message {
  text-align: center;
  color: #64748b;
  font-style: italic;
  padding: 2rem;
}

.actions {
  display: flex;
  justify-content: center;
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
  color: black;
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
  gap: 0.5rem;
}

.pagination-btn {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 6px 10px;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pagination-btn:hover:not(:disabled) {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: black;
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
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  color: #64748b;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-number:hover:not(:disabled):not(.active) {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: black;
}

.page-number.active {
  background: #00b8d4;
  border-color: #00b8d4;
  color: white;
}

.page-number:disabled {
  cursor: default;
  color: #cbd5e1;
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

.modal-content.modal-form {
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
  color: black;
}

.modal-close svg {
  width: 20px;
  height: 20px;
  stroke-width: 2;
}

.modal-body {
  padding: 1.5rem;
  color: #64748b;
  overflow-y: auto;
  max-height: calc(90vh - 80px);
}

/* FORMULAIRE */
.article-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-label {
  font-size: 14px;
  font-weight: 500;
  color: black;
}

.form-input {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 0.75rem;
  font-size: 14px;
  color: black;
  transition: all 0.2s ease;
}

.form-input:focus {
  outline: none;
  border-color: #00b8d4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

.form-input.error {
  border-color: #dc2626;
}

.form-input.error:focus {
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

.error-text {
  font-size: 12px;
  color: #dc2626;
  margin-top: -0.25rem;
}

.alert {
  padding: 1rem;
  border-radius: 6px;
  font-size: 14px;
  text-align: center;
}

.alert-error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.alert-success {
  background: #d1fae5;
  color: #047857;
  border: 1px solid #a7f3d0;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding-top: 1rem;
  border-top: 1px solid #e2e8f0;
}

.btn-secondary,
.btn-primary {
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.btn-secondary {
  background: #f1f5f9;
  color: #64748b;
}

.btn-secondary:hover {
  background: #e2e8f0;
  color: black;
}

.btn-primary {
  background: #00b8d4;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #0891a6;
}

.btn-primary:disabled {
  background: #cbd5e1;
  cursor: not-allowed;
}

.required-indicator {
  color: #dc2626;
}

/* MODAL DÉTAILS */
.details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
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

.status-badge {
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 500;
}

.status-critical {
  background: #fee2e2;
  color: #991b1b;
}

.status-ok {
  background: #f0fdf4;
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
  color: #00b8d4;
  margin-bottom: 1rem;
}

.todo-container h4 {
  font-size: 18px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 0.5rem 0;
}

.todo-container p {
  color: #64748b;
  margin: 0;
}

input:placeholder-shown {
  text-overflow: ellipsis;
}

input:focus::placeholder {
  color: transparent;
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
</style>
