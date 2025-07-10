<template>
  <div class="suppliers-page">
    <!-- En-tête avec bouton Nouveau fournisseur -->
    <div class="page-header">
      <div class="header-left">
        <h2 class="page-title">Gestion des fournisseurs</h2>
      </div>
      <div class="header-right">
        <button class="new-supplier-button" @click="showNewSupplierModal = true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="16" />
            <line x1="8" y1="12" x2="16" y2="12" />
          </svg>
          <span>Nouveau fournisseur</span>
        </button>
      </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="stats-section">
      <div class="stat-card">
        <div class="stat-icon active-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z" />
            <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2" />
            <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2" />
            <path d="M10 6h4" />
            <path d="M10 10h4" />
            <path d="M10 14h4" />
            <path d="M10 18h4" />
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Fournisseurs actifs</p>
          <p class="stat-value">{{ activeSuppliers }}</p>
          <p class="stat-trend">{{ percentageActive }}% du total</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon products-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"
            />
            <polyline points="3.27,6.96 12,12.01 20.73,6.96" />
            <line x1="12" y1="22.08" x2="12" y2="12" />
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Total fournisseurs</p>
          <p class="stat-value">{{ totalSuppliers }}</p>
          <p class="stat-trend">Base fournisseurs</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon quality-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <polygon
              points="12,2 15,8.5 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 9,8.5"
            />
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Note moyenne</p>
          <p class="stat-value">{{ averageRating }}/5</p>
          <p class="stat-trend">Qualité globale</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon delivery-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="1" y="3" width="15" height="13" />
            <polygon points="16,3 19,7 19,13 16,13" />
            <circle cx="5.5" cy="18.5" r="2.5" />
            <circle cx="18.5" cy="18.5" r="2.5" />
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Délai moyen</p>
          <p class="stat-value">{{ averageDelivery }}</p>
          <p class="stat-trend">jours de livraison</p>
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
            placeholder="Rechercher par ID, fournisseur, contact ou localisation"
            class="search-input"
          />
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </div>
        <div class="filter-wrapper">
          <select v-model="filters.status" class="filter-select">
            <option value="">Tous les statuts</option>
            <option value="active">Actif</option>
            <option value="inactive">Inactif</option>
          </select>
          <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>

        <div class="filter-wrapper">
          <select v-model="filters.sort" class="filter-select">
            <option value="name">Trier par nom</option>
            <option value="rating">Trier par note</option>
            <option value="recent">Plus récents</option>
          </select>
          <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>

        <button class="action-button" @click="resetFilters">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="1 4 1 10 7 10" />
            <polyline points="23 20 23 14 17 14" />
            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15" />
          </svg>
          Réinitialiser
        </button>

        <button class="export-button" @click="exportSuppliers">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="7,10 12,15 17,10" />
            <line x1="12" y1="15" x2="12" y2="3" />
          </svg>
          Exporter
        </button>
      </div>
    </div>

    <!-- Tableau des fournisseurs -->
    <div class="table-section">
      <div class="table-card">
        <div class="table-header">
          <h3 class="table-title">Liste des fournisseurs</h3>
          <div class="table-stats">
            <span class="stat-item">
              <span class="stat-label">Affichés :</span>
              <span class="stat-value">{{ filteredSuppliers.length }}</span>
            </span>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="loader"></div>
          <p>Chargement des fournisseurs...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-container">
          <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
          <p class="error-message">{{ error }}</p>
          <button @click="fetchSuppliers" class="retry-button">Réessayer</button>
        </div>

        <!-- Table Content -->
        <div v-else class="table-container">
          <table class="suppliers-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Fournisseur</th>
                <th>Contact</th>
                <th>Localisation</th>
                <th>Note qualité</th>
                <th>Délai livraison</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="supplier in filteredSuppliers" :key="supplier.id_fournisseur">
                <td class="supplier-id">
                  <p class="supplier-id-text">{{ supplier.id_fournisseur }}</p>
                </td>
                <td class="supplier-info">
                  <div class="supplier-profile">
                    <div class="supplier-avatar-container">
                      <div
                        class="supplier-avatar"
                        :style="{
                          backgroundColor: getAvatarColor(supplier.id_fournisseur),
                        }"
                      >
                        {{ getSupplierInitials(supplier) }}
                      </div>
                    </div>
                    <div class="supplier-details">
                      <p class="supplier-name">{{ supplier.nom }}</p>
                    </div>
                  </div>
                </td>
                <td class="contact-info">
                  <p class="contact-person">
                    {{ supplier.contact_prenom }} {{ supplier.contact_nom }}
                  </p>
                  <p class="contact-phone">
                    {{ formatPhone(supplier.telephone) }}
                  </p>
                </td>
                <td class="location-info">
                  <p class="city">{{ supplier.ville }}</p>
                  <p class="country">{{ supplier.pays }}</p>
                </td>
                <td class="rating-info">
                  <div class="rating-display">
                    <span class="rating-value">{{ supplier.note_qualite || "N/A" }}</span>
                    <div v-if="supplier.note_qualite" class="stars-mini">
                      <span
                        v-for="i in 5"
                        :key="i"
                        class="star-container"
                        :style="{
                          '--fill-width': getStarFillWidth(i, parseFloat(supplier.note_qualite)),
                        }"
                      >
                        <span class="star-background">★</span>
                        <span class="star-fill">★</span>
                      </span>
                    </div>
                  </div>
                </td>
                <td class="delivery-info">{{ supplier.delai_livraison }} jours</td>
                <td>
                  <span
                    class="status-badge"
                    :class="supplier.actif ? 'status-active' : 'status-inactive'"
                  >
                    {{ supplier.actif ? "Actif" : "Inactif" }}
                  </span>
                </td>
                <td class="actions">
                  <button
                    class="action-btn secondary"
                    @click="viewSupplier(supplier.id_fournisseur)"
                    title="Voir détails"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                  <button
                    class="action-btn secondary"
                    @click="editSupplier(supplier.id_fournisseur)"
                    title="Modifier"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                  </button>
                </td>
              </tr>
              <tr v-if="filteredSuppliers.length === 0 && !loading">
                <td colspan="100%" class="empty-message">Aucun fournisseur trouvé</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Nouveau fournisseur -->
    <div v-if="showNewSupplierModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content modal-large" @click.stop>
        <div class="modal-header">
          <h3>Nouveau fournisseur</h3>
          <button @click="closeModal" class="modal-close" :disabled="isSubmitting">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitNewSupplier" class="supplier-form">
          <div class="modal-body">
            <!-- Section Entreprise -->
            <div class="form-section">
              <h4 class="section-title">Informations de l'entreprise</h4>
              <div class="form-grid">
                <div class="form-group">
                  <label for="nom" class="form-label required">Nom de l'entreprise</label>
                  <input
                    id="nom"
                    v-model="newSupplier.nom"
                    type="text"
                    class="form-input"
                    placeholder="Ex: Durand SA"
                    required
                    maxlength="50"
                  />
                </div>

                <div class="form-group">
                  <label for="email" class="form-label required">Email professionnel</label>
                  <input
                    id="email"
                    v-model="newSupplier.email"
                    type="email"
                    class="form-input"
                    placeholder="contact@entreprise.com"
                    required
                  />
                  <span v-if="errors.email" class="error-text">{{ errors.email }}</span>
                </div>

                <div class="form-group">
                  <label for="telephone" class="form-label required">Téléphone</label>
                  <input
                    id="telephone"
                    v-model="newSupplier.telephone"
                    type="tel"
                    class="form-input"
                    placeholder="0123456789"
                    pattern="0[1-9][0-9]{8}"
                    required
                  />
                  <span v-if="errors.telephone" class="error-text">{{ errors.telephone }}</span>
                  <span v-else class="help-text">Format: 10 chiffres sans espaces</span>
                </div>

                <div class="form-group">
                  <label for="site_web" class="form-label">Site web</label>
                  <input
                    id="site_web"
                    v-model="newSupplier.site_web"
                    type="url"
                    class="form-input"
                    placeholder="https://www.exemple.com"
                  />
                </div>
              </div>
            </div>

            <!-- Section Adresse -->
            <div class="form-section">
              <h4 class="section-title">Adresse</h4>
              <div class="form-grid">
                <div class="form-group col-span-2">
                  <label for="adresse" class="form-label required">Adresse</label>
                  <input
                    id="adresse"
                    v-model="newSupplier.adresse"
                    type="text"
                    class="form-input"
                    placeholder="123 rue de la Paix"
                    required
                    maxlength="100"
                  />
                </div>

                <div class="form-group">
                  <label for="code_postal" class="form-label required">Code postal</label>
                  <input
                    id="code_postal"
                    v-model="newSupplier.code_postal"
                    type="text"
                    class="form-input"
                    placeholder="75001"
                    required
                    pattern="[0-9]{5}"
                  />
                  <span v-if="errors.code_postal" class="error-text">{{ errors.code_postal }}</span>
                </div>

                <div class="form-group">
                  <label for="ville" class="form-label required">Ville</label>
                  <input
                    id="ville"
                    v-model="newSupplier.ville"
                    type="text"
                    class="form-input"
                    placeholder="Paris"
                    required
                  />
                </div>

                <div class="form-group">
                  <label for="pays" class="form-label required">Pays</label>
                  <div class="filter-wrapper" style="width: fit-content">
                    <select id="pays" class="filter-select" v-model="newSupplier.pays" required>
                      <option value="">Sélectionner un pays</option>
                      <option value="France">France</option>
                      <option value="Belgique">Belgique</option>
                      <option value="Suisse">Suisse</option>
                      <option value="Luxembourg">Luxembourg</option>
                      <option value="Allemagne">Allemagne</option>
                      <option value="Espagne">Espagne</option>
                      <option value="Italie">Italie</option>
                    </select>
                    <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M6 9l6 6 6-6" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Section Contact -->
            <div class="form-section">
              <h4 class="section-title">Contact principal</h4>
              <div class="form-grid">
                <div class="form-group">
                  <label for="contact_prenom" class="form-label required">Prénom du contact</label>
                  <input
                    id="contact_prenom"
                    v-model="newSupplier.contact_prenom"
                    type="text"
                    class="form-input"
                    placeholder="Marie"
                    required
                    maxlength="50"
                  />
                </div>

                <div class="form-group">
                  <label for="contact_nom" class="form-label required">Nom du contact</label>
                  <input
                    id="contact_nom"
                    v-model="newSupplier.contact_nom"
                    type="text"
                    class="form-input"
                    placeholder="Dupont"
                    required
                    maxlength="50"
                  />
                </div>
              </div>
            </div>

            <!-- Section Commerciale -->
            <div class="form-section">
              <h4 class="section-title">Informations commerciales</h4>
              <div class="form-grid">
                <div class="form-group">
                  <label for="delai_livraison" class="form-label">Délai de livraison (jours)</label>
                  <input
                    id="delai_livraison"
                    v-model.number="newSupplier.delai_livraison"
                    type="number"
                    class="form-input"
                    placeholder="5"
                    min="1"
                    max="365"
                  />
                </div>

                <div class="form-group">
                  <label for="conditions_paiement" class="form-label">Conditions de paiement</label>
                  <div class="filter-wrapper" style="width: fit-content">
                    <select
                      id="conditions_paiement"
                      v-model="newSupplier.conditions_paiement"
                      class="filter-select"
                      required
                    >
                      <option value="">Sélectionner</option>
                      <option value="Comptant">Comptant</option>
                      <option value="30 jours">30 jours</option>
                      <option value="45 jours">45 jours</option>
                      <option value="60 jours">60 jours</option>
                      <option value="90 jours">90 jours</option>
                      <option value="Autre">Autre</option>
                    </select>
                    <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M6 9l6 6 6-6" />
                    </svg>
                  </div>
                </div>

                <div class="form-group">
                  <label for="note_qualite" class="form-label">Note qualité initiale</label>
                  <div class="filter-wrapper" style="width: fit-content">
                    <select
                      id="note_qualite"
                      v-model="newSupplier.note_qualite"
                      class="filter-select"
                      required
                    >
                      <option value="">Non évalué</option>
                      <option value="1">1 - Médiocre</option>
                      <option value="2">2 - Faible</option>
                      <option value="3">3 - Correct</option>
                      <option value="4">4 - Bon</option>
                      <option value="5">5 - Excellent</option>
                    </select>
                    <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M6 9l6 6 6-6" />
                    </svg>
                  </div>
                </div>

                <div class="form-group">
                  <label class="checkbox-label">
                    <input v-model="newSupplier.actif" type="checkbox" class="checkbox-input" />
                    <span>Fournisseur actif</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Messages d'erreur -->
            <div v-if="globalError" class="error-banner">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
              </svg>
              <span>{{ globalError }}</span>
            </div>

            <!-- Message de succès -->
            <div v-if="successMessage" class="success-banner">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
              <span>{{ successMessage }}</span>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn-cancel" :disabled="isSubmitting">
              Annuler
            </button>
            <button type="submit" class="btn-submit" :disabled="isSubmitting">
              <span v-if="!isSubmitting">Créer le fournisseur</span>
              <span v-else class="loading-text">
                <svg class="spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-dashoffset="32">
                    <animate
                      attributeName="stroke-dashoffset"
                      dur="1s"
                      repeatCount="indefinite"
                      from="32"
                      to="0"
                    />
                  </circle>
                </svg>
                Création en cours...
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted } from "vue";
  import { useRouter } from "vue-router";
  import { VITE_API_URL } from "@/constants/constants.js";

  export default {
    name: "SuppliersView",
    metaInfo: {
      meta: [
        {
          name: "description",
          content:
            "Gérez vos fournisseurs avec notre interface intuitive. Ajoutez, modifiez et consultez les informations de vos fournisseurs facilement.",
        },
      ],
    },
    setup() {
      const router = useRouter();

      // États réactifs
      const suppliers = ref([]);
      const loading = ref(true);
      const error = ref(null);
      const searchQuery = ref("");
      const showNewSupplierModal = ref(false);
      const isSubmitting = ref(false);
      const globalError = ref("");
      const successMessage = ref("");
      const errors = ref({});

      // Formulaire nouveau fournisseur
      const newSupplier = ref({
        nom: "",
        email: "",
        telephone: "",
        adresse: "",
        code_postal: "",
        ville: "",
        pays: "France",
        site_web: "",
        contact_prenom: "",
        contact_nom: "",
        conditions_paiement: "",
        delai_livraison: "",
        note_qualite: "",
        actif: true,
      });

      const filters = ref({
        status: "",
        sort: "name",
      });

      // Fonction pour récupérer les fournisseurs depuis l'API
      const fetchSuppliers = async () => {
        loading.value = true;
        error.value = null;

        try {
          const response = await fetch(VITE_API_URL + "get_table?table=fournisseur", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
          }

          const data = await response.json();

          if (data.success && data.data) {
            suppliers.value = data.data;
          } else {
            throw new Error("Format de données invalide");
          }
        } catch (err) {
          console.error("Erreur lors du chargement des fournisseurs:", err);
          error.value = "Impossible de charger les fournisseurs. Veuillez réessayer.";
        } finally {
          loading.value = false;
        }
      };

      // Computed properties pour les statistiques
      const totalSuppliers = computed(() => suppliers.value.length);

      const activeSuppliers = computed(() => {
        return suppliers.value.filter(s => s.actif).length;
      });

      const percentageActive = computed(() => {
        return totalSuppliers.value > 0
          ? Math.round((activeSuppliers.value / totalSuppliers.value) * 100)
          : 0;
      });

      const averageRating = computed(() => {
        const withRating = suppliers.value.filter(s => s.note_qualite);
        if (withRating.length === 0) return "N/A";
        const sum = withRating.reduce((acc, s) => acc + parseFloat(s.note_qualite), 0);
        return (sum / withRating.length).toFixed(1);
      });

      const averageDelivery = computed(() => {
        const withDelivery = suppliers.value.filter(s => s.delai_livraison);
        if (withDelivery.length === 0) return "N/A";
        const sum = withDelivery.reduce((acc, s) => acc + parseInt(s.delai_livraison), 0);
        return Math.round(sum / withDelivery.length);
      });

      // Fournisseurs filtrés
      const filteredSuppliers = computed(() => {
        let result = [...suppliers.value];

        // Filtre par statut
        if (filters.value.status) {
          result = result.filter(supplier => {
            if (filters.value.status === "active") return supplier.actif;
            if (filters.value.status === "inactive") return !supplier.actif;
            return true;
          });
        }

        // Filtre par recherche
        if (searchQuery.value) {
          const fullName = supplier => {
            return `${supplier.contact_prenom} ${supplier.contact_nom}`.toLowerCase();
          };
          const query = searchQuery.value.toLowerCase();
          result = result.filter(
            supplier =>
              supplier.id_fournisseur.toString().includes(query) ||
              fullName(supplier).includes(query) ||
              supplier.nom.toLowerCase().includes(query) ||
              supplier.ville.toLowerCase().includes(query) ||
              supplier.pays.toLowerCase().includes(query),
          );
        }

        // Tri
        switch (filters.value.sort) {
          case "name":
            result.sort((a, b) => a.nom.localeCompare(b.nom));
            break;
          case "rating":
            result.sort((a, b) => {
              const ratingA = parseFloat(a.note_qualite) || 0;
              const ratingB = parseFloat(b.note_qualite) || 0;
              return ratingB - ratingA;
            });
            break;
          case "recent":
            result.sort((a, b) => new Date(b.date_creation) - new Date(a.date_creation));
            break;
        }

        return result;
      });

      // Fonctions utilitaires
      const getSupplierInitials = supplier => {
        const words = supplier.nom.split(/[\s\-&]+/);
        return words
          .map(w => w[0])
          .join("")
          .toUpperCase()
          .substring(0, 2);
      };

      const formatPhone = phone => {
        if (!phone) return "Non renseigné";
        const cleaned = phone.toString().replace(/\D/g, "");
        if (cleaned.length === 10) {
          return cleaned.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, "$1 $2 $3 $4 $5");
        }
        return phone;
      };

      const getAvatarColor = id => {
        const colors = ["#00B8D4", "#2563EB", "#059669", "#D97706", "#7C3AED", "#DC2626"];
        return colors[id % colors.length];
      };

      const resetFilters = () => {
        searchQuery.value = "";
        filters.value.status = "";
        filters.value.sort = "name";
      };

      const exportSuppliers = () => {
        const csv = [
          [
            "ID",
            "Nom",
            "Contact",
            "Ville",
            "Pays",
            "Téléphone",
            "Email",
            "Note qualité",
            "Délai livraison",
            "Statut",
          ],
          ...suppliers.value.map(s => [
            s.id_fournisseur,
            s.nom,
            `${s.contact_prenom} ${s.contact_nom}`,
            s.ville,
            s.pays,
            s.telephone,
            s.email,
            s.note_qualite || "N/A",
            s.delai_livraison,
            s.actif ? "Actif" : "Inactif",
          ]),
        ]
          .map(row => row.join(","))
          .join("\n");

        const blob = new Blob([csv], { type: "text/csv" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "fournisseurs.csv";
        a.click();
        URL.revokeObjectURL(url);
      };

      const viewSupplier = supplierId => {
        router.push(`/suppliers/${supplierId}`);
      };

      const editSupplier = supplierId => {
        console.log("Modifier fournisseur:", supplierId);
        // TODO: Redirection vers la page d'édition
      };

      // Charger les données au montage
      onMounted(() => {
        fetchSuppliers();
      });

      // Fonction pour réinitialiser le formulaire
      const resetForm = () => {
        newSupplier.value = {
          nom: "",
          email: "",
          telephone: "",
          adresse: "",
          code_postal: "",
          ville: "",
          pays: "France",
          site_web: "",
          contact_prenom: "",
          contact_nom: "",
          conditions_paiement: "",
          delai_livraison: "",
          note_qualite: "",
          actif: true,
        };
        errors.value = {};
        globalError.value = "";
        successMessage.value = "";
      };

      // Fermer la modale
      const closeModal = () => {
        if (!isSubmitting.value) {
          showNewSupplierModal.value = false;
          resetForm();
        }
      };

      // Valider le formulaire
      const validateForm = () => {
        const newErrors = {};

        // Validation du téléphone
        const phoneRegex = /^0[1-9][0-9]{8}$/;
        if (!phoneRegex.test(newSupplier.value.telephone)) {
          newErrors.telephone = "Le numéro de téléphone doit être au format 0123456789";
        }

        // Validation email
        if (!newSupplier.value.email.includes("@")) {
          newErrors.email = "Email invalide";
        }

        // Validation code postal
        const postalCodeRegex = /^[0-9]{5}$/;
        if (!postalCodeRegex.test(newSupplier.value.code_postal)) {
          newErrors.code_postal = "Le code postal doit contenir 5 chiffres";
        }

        errors.value = newErrors;
        return Object.keys(newErrors).length === 0;
      };

      // Soumettre le nouveau fournisseur
      const submitNewSupplier = async () => {
        globalError.value = "";
        successMessage.value = "";

        if (!validateForm()) {
          globalError.value = "Veuillez corriger les erreurs dans le formulaire";
          return;
        }

        isSubmitting.value = true;

        try {
          // Créer un FormData pour l'envoi
          const formData = new FormData();

          // Envoyer tous les champs selon la structure de la BDD
          formData.append("nom", newSupplier.value.nom);
          formData.append("contact_nom", newSupplier.value.contact_nom);
          formData.append("contact_prenom", newSupplier.value.contact_prenom);
          formData.append("email", newSupplier.value.email);
          formData.append("telephone", newSupplier.value.telephone);
          formData.append("adresse", newSupplier.value.adresse);
          formData.append("ville", newSupplier.value.ville);
          formData.append("code_postal", newSupplier.value.code_postal);
          formData.append("pays", newSupplier.value.pays);

          // Champs optionnels
          if (newSupplier.value.site_web) {
            formData.append("site_web", newSupplier.value.site_web);
          }
          if (newSupplier.value.conditions_paiement) {
            formData.append("conditions_paiement", newSupplier.value.conditions_paiement);
          }
          if (newSupplier.value.delai_livraison) {
            formData.append("delai_livraison", newSupplier.value.delai_livraison);
          }
          if (newSupplier.value.note_qualite) {
            formData.append("note_qualite", newSupplier.value.note_qualite);
          }
          formData.append("actif", newSupplier.value.actif ? "true" : "false");

          const response = await fetch(VITE_API_URL + "new_fournisseur", {
            method: "POST",
            body: formData,
            credentials: "include",
          });

          const data = await response.json();

          if (!response.ok) {
            // Gérer les erreurs spécifiques
            if (response.status === 409) {
              globalError.value = "Un fournisseur avec cet email existe déjà";
            } else if (response.status === 400) {
              globalError.value = data.message || "Données invalides";
            } else {
              throw new Error(data.message || "Erreur lors de la création");
            }
            return;
          }

          if (data.success) {
            const successMsg = data.id
              ? `Fournisseur créé avec succès ! (ID : ${String(data.id)})`
              : "Fournisseur créé avec succès !";
            successMessage.value = successMsg;

            // Attendre un peu avant de fermer et recharger
            setTimeout(() => {
              closeModal();
              fetchSuppliers(); // Recharger la liste
            }, 1500);
          } else {
            throw new Error(data.message || "Erreur lors de la création");
          }
        } catch (err) {
          console.error("Erreur lors de la création:", err);
          globalError.value = err.message || "Une erreur est survenue lors de la création";
        } finally {
          isSubmitting.value = false;
        }
      };

      return {
        suppliers,
        loading,
        error,
        searchQuery,
        showNewSupplierModal,
        isSubmitting,
        globalError,
        successMessage,
        errors,
        newSupplier,
        filters,
        totalSuppliers,
        activeSuppliers,
        percentageActive,
        averageRating,
        averageDelivery,
        filteredSuppliers,
        fetchSuppliers,
        getSupplierInitials,
        formatPhone,
        getAvatarColor,
        resetFilters,
        exportSuppliers,
        viewSupplier,
        editSupplier,
        resetForm,
        closeModal,
        validateForm,
        submitNewSupplier,
      };
    },
    methods: {
      getStarFillWidth(starIndex, rating) {
        const fillPercentage = Math.min(Math.max(rating - (starIndex - 1), 0), 1);
        return `${fillPercentage * 100}%`;
      },
    },
  };
</script>

<style scoped>
  .suppliers-page {
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

  .new-supplier-button {
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

  .new-supplier-button:hover {
    background: #0891a6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
  }

  .new-supplier-button svg {
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
    border: 1px solid #f1f5f9;
    transition: all 0.2s ease;
  }

  .stat-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }

  .stat-content .stat-value {
    font-size: 28px;
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
    background: #f0fdf4;
    color: #059669;
  }

  .products-icon {
    background: #eff6ff;
    color: #2563eb;
  }

  .quality-icon {
    background: #fef3c7;
    color: #d97706;
  }

  .delivery-icon {
    background: #f3e8ff;
    color: #7c3aed;
  }

  .stat-content {
    flex: 1;
  }

  .stat-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
  }

  .stat-value {
    font-weight: 700;
    color: #0f172a;
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

  .search-button,
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

  .search-button:hover,
  .export-button:hover {
    background: #2563eb;
    transform: translateY(-1px);
  }

  .search-button svg,
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

  .table-stats .stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 14px;
  }

  .table-stats .stat-label {
    color: #64748b;
  }

  .table-stats .stat-value {
    font-weight: 600;
    color: #0f172a;
  }

  .table-container {
    overflow-x: auto;
  }

  .suppliers-table {
    width: 100%;
    min-width: max-content;
    text-align: center;
  }

  .suppliers-table th {
    background: #f8fafc;
    padding: 1rem;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #e2e8f0;
  }

  .suppliers-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: black;
    vertical-align: middle;
  }

  .suppliers-table tbody tr:hover {
    background: #f8fafc;
  }

  .suppliers-table tbody tr:last-child td {
    border-bottom: none;
  }

  /* SUPPLIER INFO */
  .supplier-profile {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
  }

  .supplier-avatar-container {
    display: flex;
    width: 35%;
    justify-content: flex-end;
  }

  .supplier-avatar {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
  }

  .supplier-avatar.large {
    width: 64px;
    height: 64px;
    font-size: 20px;
  }

  .supplier-details {
    width: 65%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }

  .supplier-name {
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 0.25rem 0;
    line-height: 1.2;
  }

  .supplier-id {
    font-size: 12px;
    color: #64748b;
    margin: 0;
  }

  /* CONTACT INFO */
  .contact-person {
    font-weight: 500;
    color: black;
    margin: 0 0 0.25rem 0;
  }

  .contact-phone {
    font-size: 12px;
    color: #64748b;
    margin: 0;
  }

  /* LOCATION INFO */
  .city {
    font-weight: 500;
    color: black;
    margin: 0 0 0.25rem 0;
  }

  .country {
    font-size: 12px;
    color: #64748b;
    margin: 0;
  }

  /* RATING */
  .rating-display {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
  }

  .rating-value {
    font-weight: 600;
    color: #0f172a;
  }

  .stars-mini {
    display: flex;
    gap: 1px;
  }

  .star-container {
    position: relative;
    display: inline-block;
    font-size: 12px;
  }

  .star-container span {
    font-size: 1rem;
    -webkit-user-drag: none;
    -moz-user-drag: none;
    -ms-user-drag: none;
  }

  .star-background {
    color: #e2e8f0;
  }

  .star-fill {
    position: absolute;
    top: 0;
    left: 0;
    color: #f59e0b;
    overflow: hidden;
    width: var(--fill-width, 0%);
  }

  /* DELIVERY INFO */
  .delivery-info {
    font-weight: 600;
    color: #0f172a;
    text-align: center;
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
    background: #d1fae5;
    color: #047857;
  }

  .status-inactive {
    background: #fef2f2;
    color: #dc2626;
  }

  /* ACTIONS */
  .actions {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
  }

  .action-button,
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

  .action-button:hover,
  .export-button:hover {
    background: #2563eb;
    transform: translateY(-1px);
  }

  .action-button svg,
  .export-button svg {
    width: 16px;
    height: 16px;
    stroke-width: 2;
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

  .empty-message {
    text-align: center;
    color: #64748b;
    font-style: italic;
    padding: 2rem;
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

  .modal-large {
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

  .modal-close:hover:not(:disabled) {
    background: #f1f5f9;
    color: black;
  }

  .modal-close:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .modal-close svg {
    width: 20px;
    height: 20px;
    stroke-width: 2;
  }

  .modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    max-height: calc(90vh - 180px);
  }

  /* FORMULAIRE */
  .supplier-form {
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .form-section {
    margin-bottom: 2rem;
  }

  .form-section:last-child {
    margin-bottom: 0;
  }

  .section-title {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .form-group.col-span-2 {
    grid-column: span 2;
  }

  .form-label {
    font-size: 14px;
    font-weight: 500;
    color: black;
  }

  .form-label.required::after {
    content: " *";
    color: #dc2626;
  }

  .form-input {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 14px;
    color: #0f172a;
    transition: all 0.2s ease;
  }

  .form-input:focus {
    outline: none;
    border-color: #00b8d4;
    box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
  }

  .form-input::placeholder {
    color: #94a3b8;
  }

  .form-input:invalid:not(:focus) {
    border-color: #dc2626;
  }

  .help-text {
    font-size: 12px;
    color: #64748b;
    margin-top: -0.25rem;
  }

  .error-text {
    font-size: 12px;
    color: #dc2626;
    margin-top: -0.25rem;
    font-weight: 500;
  }

  .checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 14px;
    color: black;
  }

  .checkbox-input {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }

  /* MESSAGES */
  .error-banner,
  .success-banner {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
    font-size: 14px;
  }

  .error-banner {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
  }

  .success-banner {
    background: #f0fdf4;
    color: #059669;
    border: 1px solid #a7f3d0;
  }

  .error-banner svg,
  .success-banner svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
  }

  /* FOOTER */
  .modal-footer {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding: 1.5rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
  }

  .btn-cancel,
  .btn-submit {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .btn-cancel {
    background: white;
    color: #64748b;
    border: 1px solid #e2e8f0;
  }

  .btn-cancel:hover:not(:disabled) {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: black;
  }

  .btn-submit {
    background: #00b8d4;
    color: white;
    box-shadow: 0 2px 8px rgba(0, 184, 212, 0.3);
  }

  .btn-submit:hover:not(:disabled) {
    background: #0891a6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
  }

  .btn-cancel:disabled,
  .btn-submit:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
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

  input:placeholder-shown {
    text-overflow: ellipsis;
  }

  input:focus::placeholder {
    color: transparent;
  }

  /* RESPONSIVE */
  @media (max-width: 768px) {
    .modal-content {
      width: 95%;
      margin: 1rem;
    }

    .form-grid {
      grid-template-columns: 1fr;
    }

    .form-group.col-span-2 {
      grid-column: span 1;
    }

    .modal-footer {
      flex-direction: column-reverse;
    }

    .btn-cancel,
    .btn-submit {
      width: 100%;
      justify-content: center;
    }
  }

  /* RESPONSIVE */
  @media (max-width: 1024px) {
    .page-header {
      flex-direction: column;
      gap: 1rem;
      align-items: stretch;
    }

    .new-supplier-button {
      width: 100%;
      justify-content: center;
    }

    .stats-section {
      grid-template-columns: repeat(2, 1fr);
    }

    .filter-group {
      flex-direction: column;
      align-items: stretch;
    }

    .search-container,
    .filter-select,
    .action-button,
    .export-button {
      width: 100%;
      max-width: none;
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

    .suppliers-table {
      font-size: 12px;
    }

    .actions {
      gap: 0.5rem;
    }
  }
</style>
