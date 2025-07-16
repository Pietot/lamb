<template>
  <div class="reception-page">
    <!-- En-tête avec bouton -->
    <div class="page-header">
      <div class="header-left">
        <h2 class="page-title">Réception des livraisons</h2>
      </div>
      <div class="header-right">
        <button
          role="button"
          aria-label="Nouvelle livraison"
          class="new-delivery-button"
          @click="showNewDeliveryModal = true"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="16" />
            <line x1="8" y1="12" x2="16" y2="12" />
          </svg>
          <span>Enregistrer une livraison</span>
        </button>
      </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="stats-section">
      <div class="stat-card">
        <div class="stat-icon truck-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="1" y="3" width="15" height="13" />
            <polygon points="16,3 19,7 19,13 16,13" />
            <circle cx="5.5" cy="18.5" r="2.5" />
            <circle cx="18.5" cy="18.5" r="2.5" />
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Total livraisons</p>
          <p class="stat-value">{{ totalDeliveries }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon check-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"
            />
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Complétées</p>
          <p class="stat-value">{{ completedCount }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon warning-icon">
          <svg viewBox="0 0 512 512">
            <path
              fill="currentColor"
              d="M34.5 420.4c-1.6 2.8-2.5 6-2.5 9.3c0 10.2 8.2 18.4 18.4 18.4l411.2 0c10.2 0 18.4-8.2 18.4-18.4c0-3.3-.9-6.4-2.5-9.3L276.5 75.8C272.2 68.5 264.4 64 256 64s-16.2 4.5-20.5 11.8L34.5 420.4zM6.9 404.2l201-344.6C217.9 42.5 236.2 32 256 32s38.1 10.5 48.1 27.6l201 344.6c4.5 7.7 6.9 16.5 6.9 25.4c0 27.8-22.6 50.4-50.4 50.4L50.4 480C22.6 480 0 457.4 0 429.6c0-8.9 2.4-17.7 6.9-25.4zM256 160c8.8 0 16 7.2 16 16l0 128c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zM232 384a24 24 0 1 1 48 0 24 24 0 1 1 -48 0z"
            />
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">En attente</p>
          <p class="stat-value">{{ pendingCount }}</p>
        </div>
      </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
      <div class="filter-group">
        <div class="search-container">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher par numéro, fournisseur, date de livraison, bon de livraison ou notes"
            class="search-input"
          />
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </div>

        <div class="filter-wrapper">
          <label for="supplier-filter" class="filter-label">Fournisseur :</label>
          <select id="supplier-filter" v-model="filters.supplier" class="filter-select">
            <option value="">Tous les fournisseurs</option>
            <option
              v-for="supplier in uniqueSuppliers"
              :key="supplier.id_fournisseur"
              :value="supplier.id_fournisseur"
            >
              {{ supplier.nom }}
            </option>
          </select>
          <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>

        <div class="filter-wrapper">
          <label for="status-filter" class="filter-label">Statut :</label>
          <select id="status-filter" v-model="filters.status" class="filter-select">
            <option value="">Tous les statuts</option>
            <option value="completee">Complétée</option>
            <option value="en_attente">En attente</option>
            <option value="partielle">Partielle</option>
            <option value="annulee">Annulée</option>
          </select>
          <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>

        <button
          role="button"
          aria-label="Exporter les réceptions"
          class="export-button"
          @click="exportDelivery"
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

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loader"></div>
      <p>Chargement des livraisons...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <circle cx="12" cy="12" r="10" />
        <line x1="12" y1="8" x2="12" y2="12" />
        <line x1="12" y1="16" x2="12.01" y2="16" />
      </svg>
      <p class="error-message">{{ error }}</p>
      <button @click="fetchData" class="retry-button">Réessayer</button>
    </div>

    <!-- Livraisons -->
    <div v-else class="deliveries-section">
      <div class="section-card">
        <div class="section-header">
          <h3 class="section-title">Liste des livraisons</h3>
          <div class="table-stats">
            <span class="stat-item">
              <span class="stat-label">Total :</span>
              <span class="stat-value">{{ filteredDeliveries.length }}</span>
            </span>
          </div>
        </div>

        <div class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>N° livraison</th>
                <th>Fournisseur</th>
                <th>Date livraison</th>
                <th>Bon de livraison</th>
                <th>Statut</th>
                <th>Notes</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="delivery in paginatedDeliveries" :key="delivery.id_livraison">
                <td class="delivery-id">{{ delivery.numero_livraison }}</td>
                <td class="supplier-info">
                  <div class="supplier-profile">
                    <div class="supplier-avatar-container">
                      <div
                        class="supplier-avatar"
                        :style="{ backgroundColor: getAvatarColor(delivery.id_fournisseur) }"
                      >
                        {{ getSupplierInitials(getSupplierName(delivery.id_fournisseur)) }}
                      </div>
                    </div>
                    <div class="supplier-details">
                      <p class="supplier-name">{{ getSupplierName(delivery.id_fournisseur) }}</p>
                    </div>
                  </div>
                </td>
                <td class="delivery-date">{{ formatDate(delivery.date_livraison) }}</td>
                <td class="order-reference">{{ delivery.numero_bon_livraison || "-" }}</td>
                <td>
                  <span class="status-badge" :class="getStatusClass(delivery.statut)">
                    {{ getStatusLabel(delivery.statut) }}
                  </span>
                </td>
                <td class="notes">{{ delivery.notes || "-" }}</td>
                <td class="actions">
                  <button
                    role="button"
                    aria-label="Voir détails"
                    class="action-btn secondary"
                    @click="viewDetails(delivery)"
                    title="Voir détails"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                  <button
                    v-if="delivery.statut === 'en_attente'"
                    role="button"
                    aria-label="Marquer comme complétée"
                    class="action-btn success"
                    @click="markAsCompleted(delivery.id_livraison)"
                  >
                    <svg viewBox="0 0 448 512">
                      <path
                        d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"
                        fill="currentColor"
                      />
                    </svg>
                  </button>
                  <!-- Bouton invisible pour l'alignement -->
                  <button
                    v-else
                    role="button"
                    aria-label="Marquer comme complétée"
                    class="action-btn"
                    style="border: none; background: transparent; cursor: default"
                  >
                    <svg viewBox="0 0 448 512"></svg>
                  </button>
                </td>
              </tr>
            </tbody>
            <tr v-if="filteredDeliveries.length === 0">
              <td colspan="7" class="empty-message">Aucune livraison trouvée</td>
            </tr>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="pagination-container">
          <div class="pagination-info">
            Affichage {{ startIndex + 1 }}-{{ endIndex }} sur
            {{ filteredDeliveries.length }} livraisons
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

    <!-- Modal Détails livraison -->
    <div v-if="showDetailsModal" class="modal-overlay" @click="showDetailsModal = false">
      <div class="modal-content modal-details" @click.stop>
        <div class="modal-header">
          <h3>Détails de la livraison</h3>
          <button @click="showDetailsModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="selectedDelivery">
          <div class="details-grid">
            <div class="details-section">
              <h4 class="section-subtitle">Informations générales</h4>
              <div class="detail-row">
                <span class="detail-label">Numéro livraison:</span>
                <span class="detail-value">{{ selectedDelivery.numero_livraison }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Bon de livraison:</span>
                <span class="detail-value">{{ selectedDelivery.numero_bon_livraison || "-" }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Date livraison:</span>
                <span class="detail-value">{{
                  formatDateTime(selectedDelivery.date_livraison)
                }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Statut:</span>
                <span class="detail-value">
                  <span class="status-badge" :class="getStatusClass(selectedDelivery.statut)">
                    {{ getStatusLabel(selectedDelivery.statut) }}
                  </span>
                </span>
              </div>
            </div>

            <div class="details-section">
              <h4 class="section-subtitle">Informations fournisseur</h4>
              <div class="detail-row">
                <span class="detail-label">Fournisseur:</span>
                <span class="detail-value">{{
                  getSupplierName(selectedDelivery.id_fournisseur)
                }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Date création:</span>
                <span class="detail-value">{{
                  formatDateTime(selectedDelivery.date_creation)
                }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Dernière modification:</span>
                <span class="detail-value">{{
                  formatDateTime(selectedDelivery.date_modification)
                }}</span>
              </div>
            </div>

            <div class="details-section full-width" v-if="selectedDelivery.notes">
              <h4 class="section-subtitle">Notes</h4>
              <div class="notes-content">
                {{ selectedDelivery.notes }}
              </div>
            </div>
          </div>

          <div class="modal-actions">
            <button class="modal-btn secondary" @click="showDetailsModal = false">Fermer</button>
            <button
              v-if="selectedDelivery.statut === 'en_attente'"
              class="modal-btn primary"
              @click="
                () => {
                  showDetailsModal = false;
                  markAsCompleted(selectedDelivery.id_livraison);
                }
              "
            >
              Marquer comme complétée
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Nouvelle livraison -->
    <div v-if="showNewDeliveryModal" class="modal-overlay" @click="showNewDeliveryModal = false">
      <div class="modal-content modal-form" @click.stop>
        <div class="modal-header">
          <h3>Enregistrer une nouvelle livraison</h3>
          <button
            role="button"
            aria-label="Fermer"
            @click="closeNewDeliveryModal"
            class="modal-close"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submitNewDelivery" class="delivery-form">
            <div class="form-grid">
              <div class="form-group">
                <label for="fournisseur" class="form-label required">Fournisseur</label>
                <select
                  id="fournisseur"
                  v-model="newDeliveryForm.id_fournisseur"
                  class="form-select"
                  required
                >
                  <option value="">Sélectionnez un fournisseur</option>
                  <option
                    v-for="supplier in fournisseurs"
                    :key="supplier.id_fournisseur"
                    :value="supplier.id_fournisseur"
                  >
                    {{ supplier.nom }}
                  </option>
                </select>
              </div>

              <div class="form-group">
                <label for="date_livraison" class="form-label required">Date de livraison</label>
                <input
                  id="date_livraison"
                  type="datetime-local"
                  v-model="newDeliveryForm.date_livraison"
                  class="form-input"
                  required
                />
              </div>

              <div class="form-group">
                <label for="bon_livraison" class="form-label">N° Bon de livraison</label>
                <input
                  id="bon_livraison"
                  type="text"
                  v-model="newDeliveryForm.numero_bon_livraison"
                  placeholder="Ex: BL-2025-001 (auto-généré si vide)"
                  class="form-input"
                />
              </div>

              <div class="form-group">
                <label for="statut" class="form-label required">Statut</label>
                <select id="statut" v-model="newDeliveryForm.statut" class="form-select" required>
                  <option value="">Sélectionnez un statut</option>
                  <option value="en_attente">En attente</option>
                  <option value="completee">Complétée</option>
                  <option value="partielle">Partielle</option>
                  <option value="annulee">Annulée</option>
                </select>
              </div>

              <div class="form-group full-width">
                <label for="notes" class="form-label">Notes</label>
                <textarea
                  id="notes"
                  v-model="newDeliveryForm.notes"
                  rows="3"
                  placeholder="Informations complémentaires..."
                  class="form-textarea"
                ></textarea>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" @click="closeNewDeliveryModal" class="btn-cancel">
                Annuler
              </button>
              <button type="submit" class="btn-submit" :disabled="submitting">
                <span v-if="submitting">Enregistrement...</span>
                <span v-else>Enregistrer la livraison</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Confirmation -->
    <div v-if="showConfirmModal" class="modal-overlay" @click="showConfirmModal = false">
      <div class="modal-content modal-confirm" @click.stop>
        <div class="modal-header">
          <h3>Confirmer la réception</h3>
          <button @click="showConfirmModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="deliveryToComplete">
          <div class="confirm-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
          </div>
          <div class="confirm-content">
            <h4>Marquer cette livraison comme complétée ?</h4>
            <p>
              Livraison <strong>{{ deliveryToComplete.numero_livraison }}</strong>
            </p>
            <p class="confirm-supplier">
              Fournisseur : {{ getSupplierName(deliveryToComplete.id_fournisseur) }}
            </p>
            <p class="confirm-warning">
              Cette action confirmera la réception complète de la livraison.
            </p>
          </div>

          <div class="modal-actions">
            <button
              class="modal-btn secondary"
              @click="showConfirmModal = false"
              :disabled="updating"
            >
              Annuler
            </button>
            <button class="modal-btn success" @click="confirmMarkAsCompleted" :disabled="updating">
              <span v-if="updating">Mise à jour...</span>
              <span v-else>Confirmer la réception</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted, watch } from "vue";
  import { VITE_API_URL } from "@/constants/constants";

  export default {
    name: "ReceptionView",
    metaInfo: {
      meta: [
        {
          name: "description",
          content:
            "Gérez les réceptions de livraisons de Fashion Chic, visualisez les livraisons en attente, l'historique des réceptions et les statistiques.",
        },
      ],
    },
    setup() {
      // États réactifs
      const searchQuery = ref("");
      const showNewDeliveryModal = ref(false);
      const showDetailsModal = ref(false);
      const showConfirmModal = ref(false);
      const selectedDelivery = ref(null);
      const deliveryToComplete = ref(null);
      const livraisons = ref([]);
      const fournisseurs = ref([]);
      const loading = ref(true);
      const error = ref(null);
      const currentPage = ref(1);
      const itemsPerPage = 10;
      const submitting = ref(false);
      const updating = ref(false);

      // Formulaire nouvelle livraison
      const newDeliveryForm = ref({
        id_fournisseur: "",
        date_livraison: "",
        numero_bon_livraison: "",
        statut: "en_attente",
        notes: "",
      });

      const filters = ref({
        supplier: "",
        status: "",
      });

      // Fonction pour récupérer les données
      const fetchData = async () => {
        loading.value = true;
        error.value = null;

        try {
          // Récupérer les livraisons
          const livraisonsResponse = await fetch(VITE_API_URL + "get_table?table=livraison", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          // Récupérer les fournisseurs
          const fournisseursResponse = await fetch(VITE_API_URL + "get_table?table=fournisseur", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          if (!livraisonsResponse.ok || !fournisseursResponse.ok) {
            throw new Error("Erreur lors du chargement des données");
          }

          const livraisonsData = await livraisonsResponse.json();
          const fournisseursData = await fournisseursResponse.json();

          if (livraisonsData.success && livraisonsData.data) {
            // Trier par date de création décroissante
            livraisons.value = livraisonsData.data.sort(
              (a, b) => new Date(b.date_creation) - new Date(a.date_creation),
            );
          }

          if (fournisseursData.success && fournisseursData.data) {
            fournisseurs.value = fournisseursData.data;
          }
        } catch (err) {
          console.error("Erreur lors du chargement:", err);
          error.value = "Impossible de charger les données. Veuillez réessayer.";
        } finally {
          loading.value = false;
        }
      };

      // Computed properties
      const uniqueSuppliers = computed(() => {
        return fournisseurs.value.sort((a, b) => a.nom.localeCompare(b.nom));
      });

      const totalDeliveries = computed(() => livraisons.value.length);

      const completedCount = computed(() => {
        return livraisons.value.filter(l => l.statut === "completee").length;
      });

      const pendingCount = computed(() => {
        return livraisons.value.filter(l => l.statut === "en_attente").length;
      });

      const filteredDeliveries = computed(() => {
        let result = livraisons.value;

        // Filtre par fournisseur
        if (filters.value.supplier) {
          result = result.filter(delivery => delivery.id_fournisseur == filters.value.supplier);
        }

        // Filtre par statut
        if (filters.value.status) {
          result = result.filter(delivery => delivery.statut === filters.value.status);
        }

        // Filtre par recherche
        if (searchQuery.value) {
          const query = searchQuery.value.toLowerCase();
          result = result.filter(delivery => {
            const numero = delivery.numero_livraison ? delivery.numero_livraison.toLowerCase() : "";
            const bon = delivery.numero_bon_livraison
              ? delivery.numero_bon_livraison.toLowerCase()
              : "";
            const fournisseurName = getSupplierName(delivery.id_fournisseur).toLowerCase();
            const notes = delivery.notes ? delivery.notes.toLowerCase() : "";
            const dateLivraison = delivery.date_livraison ? delivery.date_livraison.toLowerCase() : "";

            return (
              numero.includes(query) ||
              bon.includes(query) ||
              fournisseurName.includes(query) ||
              dateLivraison.includes(query) ||
              notes.includes(query)
            );
          });
        }

        return result;
      });

      // Pagination computed properties
      const totalPages = computed(() => {
        return Math.ceil(filteredDeliveries.value.length / itemsPerPage);
      });

      const startIndex = computed(() => {
        return (currentPage.value - 1) * itemsPerPage;
      });

      const endIndex = computed(() => {
        return Math.min(startIndex.value + itemsPerPage, filteredDeliveries.value.length);
      });

      const paginatedDeliveries = computed(() => {
        return filteredDeliveries.value.slice(startIndex.value, endIndex.value);
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

      // Fonctions utilitaires
      const getSupplierName = supplierId => {
        const supplier = fournisseurs.value.find(f => f.id_fournisseur === supplierId);
        return supplier ? supplier.nom : `Fournisseur #${supplierId}`;
      };

      const getSupplierInitials = supplier => {
        const words = supplier.split(/[\s\-&]+/);
        return words
          .map(w => w[0])
          .join("")
          .toUpperCase()
          .substring(0, 2);
      };

      const formatDate = dateString => {
        if (!dateString) return "-";
        const date = new Date(dateString);
        const months = [
          "janv.",
          "févr.",
          "mars",
          "avr.",
          "mai",
          "juin",
          "juil.",
          "août",
          "sept.",
          "oct.",
          "nov.",
          "déc.",
        ];
        return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
      };

      const formatDateTime = dateString => {
        if (!dateString) return "-";
        const date = new Date(dateString);
        return date.toLocaleString("fr-FR", {
          day: "2-digit",
          month: "2-digit",
          year: "numeric",
          hour: "2-digit",
          minute: "2-digit",
        });
      };

      const getStatusClass = status => {
        const statusClasses = {
          en_attente: "status-pending",
          completee: "status-received",
          partielle: "status-partial",
          annulee: "status-cancelled",
        };
        return statusClasses[status] || "status-default";
      };

      const getStatusLabel = status => {
        const statusLabels = {
          en_attente: "En attente",
          completee: "Complétée",
          partielle: "Partielle",
          annulee: "Annulée",
        };
        return statusLabels[status] || status;
      };

      const goToPage = page => {
        if (page !== "..." && page >= 1 && page <= totalPages.value) {
          currentPage.value = page;
        }
      };

      const getAvatarColor = id => {
        const colors = ["#00B8D4", "#2563EB", "#059669", "#D97706", "#7C3AED", "#DC2626"];
        return colors[id % colors.length];
      };

      const exportDelivery = () => {
        const data = [
          ["Numéro", "Fournisseur", "Date livraison", "Bon de livraison", "Statut", "Notes"],
          ...filteredDeliveries.value.map(d => [
            d.numero_livraison,
            getSupplierName(d.id_fournisseur),
            formatDate(d.date_livraison),
            d.numero_bon_livraison || "-",
            getStatusLabel(d.statut),
            d.notes || "-",
          ]),
        ];

        if (!data[1]) {
          console.log("Aucune livraison à exporter");
          return;
        }

        const csvString = data.map(row => row.join(",")).join("\n");
        const blob = new Blob([csvString], { type: "text/csv;charset=utf-8;" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "livraisons.csv";
        a.click();
        URL.revokeObjectURL(url);
      };

      const viewDetails = delivery => {
        selectedDelivery.value = delivery;
        showDetailsModal.value = true;
      };

      const markAsCompleted = deliveryId => {
        const delivery = livraisons.value.find(l => l.id_livraison === deliveryId);
        if (delivery) {
          deliveryToComplete.value = delivery;
          showConfirmModal.value = true;
        }
      };

      const confirmMarkAsCompleted = async () => {
        if (!deliveryToComplete.value) return;

        updating.value = true;

        try {
          const formData = new URLSearchParams();
          formData.append("id_livraison", deliveryToComplete.value.id_livraison);
          formData.append("statut", "completee");

          const response = await fetch(VITE_API_URL + "update_livraison", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
            body: formData.toString(),
          });

          if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `Erreur HTTP: ${response.status}`);
          }

          const data = await response.json();

          if (data.success) {
            showConfirmModal.value = false;
            deliveryToComplete.value = null;
            // Recharger les données
            await fetchData();
          } else {
            throw new Error(data.message || "Erreur lors de la mise à jour");
          }
        } catch (err) {
          console.error("Erreur lors de la mise à jour:", err);
          alert(`Erreur: ${err.message}`);
        } finally {
          updating.value = false;
        }
      };

      // Fonction pour fermer la modal et réinitialiser le formulaire
      const closeNewDeliveryModal = () => {
        showNewDeliveryModal.value = false;
        // Réinitialiser le formulaire
        newDeliveryForm.value = {
          id_fournisseur: "",
          date_livraison: "",
          numero_bon_livraison: "",
          statut: "en_attente",
          notes: "",
        };
      };

      // Fonction pour créer une nouvelle livraison
      const submitNewDelivery = async () => {
        submitting.value = true;

        try {
          // Préparer les données du formulaire
          const formData = new URLSearchParams();
          formData.append("id_fournisseur", newDeliveryForm.value.id_fournisseur);

          // Convertir la date au format MySQL si elle existe
          if (newDeliveryForm.value.date_livraison) {
            const dateFormatted = newDeliveryForm.value.date_livraison.replace("T", " ") + ":00";
            formData.append("date_livraison", dateFormatted);
          } else {
            // Date par défaut si non renseignée
            formData.append(
              "date_livraison",
              new Date().toISOString().slice(0, 19).replace("T", " "),
            );
          }

          // Champs obligatoires avec valeurs par défaut
          formData.append(
            "numero_bon_livraison",
            newDeliveryForm.value.numero_bon_livraison || "BL-" + Date.now(),
          );
          formData.append("statut", newDeliveryForm.value.statut);
          formData.append("notes", newDeliveryForm.value.notes || "Aucune note");

          // Ne pas envoyer id_commande_fournisseur ou l'envoyer vide
          // formData.append('id_commande_fournisseur', '');

          // ID utilisateur connecté (à remplacer par l'ID réel de l'utilisateur)
          formData.append("id_utilisateur_reception", "1");

          // Ajouter numero_livraison (sera remplacé par l'API mais doit être présent)
          formData.append("numero_livraison", "TEMP-" + Date.now());

          // Dates au format MySQL
          const now = new Date().toISOString().slice(0, 19).replace("T", " ");
          formData.append("date_creation", now);
          formData.append("date_modification", now);

          // Appel API
          const response = await fetch(VITE_API_URL + "new_livraison", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
            body: formData.toString(),
          });

          if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `Erreur HTTP: ${response.status}`);
          }

          const data = await response.json();

          if (data.success) {
            closeNewDeliveryModal();
            // Recharger les données
            await fetchData();
          } else {
            throw new Error(data.message || "Erreur lors de la création");
          }
        } catch (err) {
          console.error("Erreur lors de la création:", err);
          // Afficher l'erreur dans la console avec plus de détails
          console.error("Détails de l'erreur:", err.message);
          alert(`Erreur: ${err.message}`);
        } finally {
          submitting.value = false;
        }
      };

      // Réinitialiser la page quand les filtres changent
      watch(
        [() => filters.value, searchQuery],
        () => {
          currentPage.value = 1;
        },
        { deep: true },
      );

      // Charger les données au montage
      onMounted(() => {
        fetchData();
      });

      return {
        searchQuery,
        showNewDeliveryModal,
        showDetailsModal,
        showConfirmModal,
        selectedDelivery,
        deliveryToComplete,
        filters,
        livraisons,
        fournisseurs,
        loading,
        error,
        currentPage,
        submitting,
        updating,
        newDeliveryForm,
        totalDeliveries,
        completedCount,
        pendingCount,
        uniqueSuppliers,
        filteredDeliveries,
        paginatedDeliveries,
        totalPages,
        startIndex,
        endIndex,
        visiblePages,
        fetchData,
        getSupplierName,
        getSupplierInitials,
        formatDate,
        formatDateTime,
        getStatusClass,
        getStatusLabel,
        goToPage,
        getAvatarColor,
        exportDelivery,
        viewDetails,
        markAsCompleted,
        confirmMarkAsCompleted,
        closeNewDeliveryModal,
        submitNewDelivery,
      };
    },
  };
</script>

<style scoped>
  .reception-page {
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

  .new-delivery-button {
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

  .new-delivery-button:hover {
    background: #5500cc;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(85, 0, 255, 0.3);
  }

  .new-delivery-button svg {
    width: 18px;
    height: 18px;
    stroke-width: 2;
  }

  /* STATISTIQUES */
  .stats-section {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
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
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1;
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

  .truck-icon {
    background: #f0fdf4;
    color: #059669;
  }

  .check-icon {
    background: #eff6ff;
    color: #2563eb;
  }

  .warning-icon {
    background: #fffbeb;
    color: #d97706;
  }

  .stat-content {
    flex: 1;
  }

  .stat-label {
    font-size: 13px;
    color: #64748b;
    line-height: 1.3;
    font-weight: 500;
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
    min-width: 590px;
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
    color: #64748b;
  }

  .filter-select {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem 2.5rem 0.75rem 1rem;
    font-size: 14px;
    color: #64748b;
    min-width: 140px;
    cursor: pointer;
    transition: all 0.2s ease;
    appearance: none;
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

  .filter-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translate(25%, -50%);
    pointer-events: none;
    height: 20px;
    color: #94a3b8;
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

  /* SECTIONS */
  .deliveries-section {
    margin-bottom: 2rem;
  }

  .section-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
    overflow: hidden;
  }

  .section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .section-title {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
  }

  /* TABLEAUX */
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

  .delivery-id {
    font-weight: 600;
    color: #0f172a;
  }

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
    border-radius: 50%;
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
  }

  .delivery-date {
    color: #64748b;
    font-size: 13px;
  }

  .order-reference {
    font-weight: 500;
    color: #334155;
  }

  .notes {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #64748b;
    font-size: 13px;
  }

  .empty-message {
    text-align: center;
    color: #64748b;
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
    white-space: nowrap;
  }

  .status-pending {
    background: #fef3c7;
    color: #92400e;
  }

  .status-received {
    background: #d1fae5;
    color: #047857;
  }

  .status-partial {
    background: #dbeafe;
    color: #1e40af;
  }

  .status-cancelled {
    background: #fef2f2;
    color: #dc2626;
  }

  .status-default {
    background: #f1f5f9;
    color: #64748b;
  }

  /* ACTIONS */
  .actions {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
  }

  .action-btn {
    border: solid 1px;
    border-radius: 6px;
    padding: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .action-btn.success {
    background: none;
    border-color: #10b981;
    color: #10b981;
  }

  .action-btn.success:hover {
    background: #10b981;
    color: white;
    transform: translateY(-2px);
  }

  .action-btn.secondary {
    background: none;
    border-color: #e2e8f0;
    color: #64748b;
  }

  .action-btn.secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
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
    color: #334155;
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

  .modal-details {
    max-width: 700px;
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
    text-align: center;
    color: #64748b;
    overflow-y: auto;
    max-height: calc(90vh - 120px);
  }

  /* MODAL DÉTAILS */
  .details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    text-align: left;
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

  .notes-content {
    padding: 1rem;
    background: white;
    border-radius: 6px;
    color: #334155;
    line-height: 1.5;
    font-size: 14px;
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

  .modal-btn.primary:hover {
    background: #0891a6;
  }

  .modal-btn.secondary {
    background: #f1f5f9;
    color: #64748b;
  }

  .modal-btn.secondary:hover {
    background: #e2e8f0;
    color: #334155;
  }

  .modal-btn.success {
    background: #059669;
    color: white;
  }

  .modal-btn.success:hover:not(:disabled) {
    background: #047857;
  }

  .modal-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  /* MODAL CONFIRMATION */
  .modal-confirm {
    max-width: 450px;
  }

  .confirm-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1.5rem;
    color: #059669;
  }

  .confirm-icon svg {
    width: 100%;
    height: 100%;
    stroke-width: 1.5;
  }

  .confirm-content {
    text-align: center;
    margin-bottom: 2rem;
  }

  .confirm-content h4 {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 1rem 0;
  }

  .confirm-content p {
    margin: 0.5rem 0;
    color: #64748b;
    font-size: 14px;
  }

  .confirm-content strong {
    color: #0f172a;
    font-weight: 600;
  }

  .confirm-supplier {
    color: #334155;
  }

  .confirm-warning {
    margin-top: 1rem;
    padding: 0.75rem;
    background: #fef3c7;
    color: #92400e;
    border-radius: 6px;
    font-size: 13px;
  }

  /* FORMULAIRE */
  .modal-form {
    max-width: 600px;
  }

  .delivery-form {
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
    color: #334155;
  }

  .form-label.required::after {
    content: " *";
    color: #dc2626;
  }

  .form-input,
  .form-select,
  .form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    color: #334155;
    background-color: white;
    transition: all 0.2s ease;
  }

  .form-input:focus,
  .form-select:focus,
  .form-textarea:focus {
    outline: none;
    border-color: #00b8d4;
    box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
  }

  .form-textarea {
    resize: vertical;
    min-height: 80px;
  }

  .form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1rem;
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
  }

  .btn-cancel {
    background: #f1f5f9;
    color: #64748b;
  }

  .btn-cancel:hover {
    background: #e2e8f0;
    color: #334155;
  }

  .btn-submit {
    background: #00b8d4;
    color: white;
  }

  .btn-submit:hover:not(:disabled) {
    background: #0891a6;
  }

  .btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  /* RESPONSIVE */
  @media (max-width: 1024px) {
    .page-header {
      flex-direction: column;
      gap: 1rem;
      align-items: stretch;
    }

    .new-delivery-button {
      width: 100%;
      justify-content: center;
    }

    .stats-section {
      grid-template-columns: 1fr;
    }

    .filter-group {
      flex-direction: column;
      align-items: stretch;
    }

    .search-container,
    .filter-select {
      width: 100%;
      max-width: none;
    }

    .details-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 768px) {
    .search-container {
      min-width: auto;
    }

    .section-header {
      flex-direction: column;
      gap: 1rem;
      align-items: flex-start;
    }

    .data-table {
      font-size: 12px;
    }

    .data-table th,
    .data-table td {
      padding: 0.75rem 0.5rem;
    }

    .actions {
      gap: 0.25rem;
    }

    .action-btn {
      padding: 4px;
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

    .form-grid {
      grid-template-columns: 1fr;
    }

    .form-actions {
      flex-direction: column;
    }

    .btn-cancel,
    .btn-submit {
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
