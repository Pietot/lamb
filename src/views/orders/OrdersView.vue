<template>
  <div class="orders-page">
    <!-- En-tête avec bouton Créer -->
    <div class="page-header">
      <div class="header-left">
        <h2 class="page-title">Gestion des commandes</h2>
      </div>
      <div class="header-right">
        <button
          role="button"
          aria-label="Créer une nouvelle commande"
          class="create-button"
          @click="goToNewOrder"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="16" />
            <line x1="8" y1="12" x2="16" y2="12" />
          </svg>
          <span>Nouvelle commande</span>
        </button>
      </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
      <div class="filter-group">
        <div class="search-container">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher par numéro, client ou montant"
            class="search-input"
          />
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </div>

        <div class="filter-wrapper">
          <label for="client-filter" class="filter-label">Client :</label>
          <select id="client-filter" v-model="filters.clientId" class="filter-select">
            <option value="">Tous les clients</option>
            <option
              v-for="client in uniqueClients"
              :key="client.id_client"
              :value="client.id_client"
            >
              {{ getClientName(client.id_client) }}
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
            <option value="0">En attente</option>
            <option value="1">En préparation</option>
            <option value="2">Expédiée</option>
          </select>
          <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>

        <button
          role="button"
          aria-label="Exporter les commandes"
          class="export-button"
          @click="exportOrders"
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

    <!-- Tableau des commandes -->
    <div class="table-section">
      <div class="table-card">
        <div class="table-header">
          <h3 class="table-title">Liste des commandes</h3>
          <div class="table-stats">
            <span class="stat-item">
              <span class="stat-label">Total :</span>
              <span class="stat-value">{{ filteredOrders.length }}</span>
            </span>
            <span class="stat-item">
              <span class="stat-label">En attente :</span>
              <span class="stat-value pending">{{ pendingCount }}</span>
            </span>
            <span class="stat-item">
              <span class="stat-label">En préparation :</span>
              <span class="stat-value progress">{{ inProgressCount }}</span>
            </span>
          </div>
        </div>

        <div v-if="loading" class="loading-container">
          <div class="loader"></div>
          <p>Chargement des commandes...</p>
        </div>

        <div v-else-if="error" class="error-container">
          <p class="error-message">{{ error }}</p>
          <button role="button" aria-label="Réessayer" @click="fetchData" class="retry-button">
            Réessayer
          </button>
        </div>

        <div v-else class="table-container">
          <table class="orders-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>N° Commande</th>
                <th>Client</th>
                <th>Date</th>
                <th>Nb Lots</th>
                <th>Montant TTC</th>
                <th>État</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in paginatedOrders" :key="order.id_commande">
                <td class="order-id">
                  {{ order.id_commande }}
                </td>
                <td class="order-id">{{ order.numero_commande }}</td>
                <td class="client-name">
                  {{ getClientName(order.id_client) }}
                </td>
                <td class="order-date">
                  {{ formatDate(order.date_commande) }}
                </td>
                <td class="lots-count">
                  <span class="lots-badge">{{ getOrderLotsCount(order.id_commande) }}</span>
                </td>
                <td class="order-amount">
                  {{ formatCurrency(order.montant_ttc) }}
                </td>
                <td>
                  <span class="status-badge" :class="getStatusClass(order.statut)">
                    {{ getStatusLabel(order.statut) }}
                  </span>
                </td>
                <td class="actions">
                  <button
                    role="button"
                    aria-label="Voir les détails"
                    class="action-btn"
                    @click="viewOrder(order)"
                    title="Voir les détails"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                  <button
                    role="button"
                    aria-label="Modifier"
                    class="action-btn"
                    @click="openEditModal(order)"
                    title="Modifier"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                  </button>
                </td>
              </tr>
              <tr v-if="paginatedOrders.length === 0">
                <td colspan="100%" class="empty-message">Aucune commande trouvée</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="!loading && !error && totalPages > 1" class="pagination-container">
          <div class="pagination-info">
            Affichage {{ startIndex + 1 }}-{{ endIndex }} sur {{ filteredOrders.length }} commandes
          </div>
          <div class="pagination-controls">
            <button
              role="button"
              aria-label="Page précédente"
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
                role="button"
                aria-label="Page {{ page }}"
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
              role="button"
              aria-label="Page suivante"
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

    <!-- Modal Voir détails -->
    <div v-if="showDetailsModal" class="modal-overlay" @click="showDetailsModal = false">
      <div class="modal-content modal-details" @click.stop>
        <div class="modal-header">
          <h2>Détails de la commande</h2>
          <button
            role="button"
            aria-label="Fermer"
            @click="showDetailsModal = false"
            class="modal-close"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="selectedOrder">
          <div class="details-grid">
            <div class="details-section">
              <h4 class="section-subtitle">Informations générales</h4>
              <div class="detail-row">
                <span class="detail-label">N° Commande :</span>
                <span class="detail-value">{{ selectedOrder.numero_commande }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">ID Commande :</span>
                <span class="detail-value">#{{ selectedOrder.id_commande }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Date :</span>
                <span class="detail-value">{{ formatDate(selectedOrder.date_commande) }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Statut :</span>
                <span class="detail-value">
                  <span class="status-badge" :class="getStatusClass(selectedOrder.statut)">
                    {{ getStatusLabel(selectedOrder.statut) }}
                  </span>
                </span>
              </div>
            </div>

            <div class="details-section">
              <h4 class="section-subtitle">Informations client</h4>
              <div class="detail-row">
                <span class="detail-label">Nom :</span>
                <span class="detail-value">{{ getClientName(selectedOrder.id_client) }}</span>
              </div>
              <div v-if="getClient(selectedOrder.id_client)" class="detail-row">
                <span class="detail-label">Email :</span>
                <span class="detail-value">{{ getClient(selectedOrder.id_client).email }}</span>
              </div>
              <div v-if="getClient(selectedOrder.id_client)" class="detail-row">
                <span class="detail-label">Téléphone :</span>
                <span class="detail-value">{{
                  formatPhone(getClient(selectedOrder.id_client).telephone)
                }}</span>
              </div>
              <div v-if="getClient(selectedOrder.id_client)" class="detail-row">
                <span class="detail-label">Adresse :</span>
                <span class="detail-value">{{ getClient(selectedOrder.id_client).adresse }}</span>
              </div>
            </div>

            <div class="details-section full-width">
              <h4 class="section-subtitle">Lots commandés</h4>
              <div
                v-if="getOrderLots(selectedOrder.id_commande).length > 0"
                class="lots-table-container"
              >
                <table class="lots-table">
                  <thead>
                    <tr>
                      <th>Nom du lot</th>
                      <th>Description</th>
                      <th>Quantité</th>
                      <th>Stock actuel</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="orderLot in getOrderLots(selectedOrder.id_commande)"
                      :key="orderLot.id_lot"
                    >
                      <td class="lot-name">
                        {{ getLotInfo(orderLot.id_lot)?.nom || "Lot #" + orderLot.id_lot }}
                      </td>
                      <td class="lot-description">
                        {{ getLotInfo(orderLot.id_lot)?.description || "-" }}
                      </td>
                      <td class="lot-quantity">{{ orderLot.quantite }}</td>
                      <td class="lot-stock">
                        <span :class="getStockClass(getLotInfo(orderLot.id_lot))">
                          {{ getLotInfo(orderLot.id_lot)?.quantite_stock || "-" }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-else class="empty-lots">Aucun lot associé à cette commande</div>
            </div>

            <div class="details-section full-width">
              <h4 class="section-subtitle">Informations financières</h4>
              <div class="detail-row">
                <span class="detail-label">Montant HT :</span>
                <span class="detail-value">{{ formatCurrency(selectedOrder.montant_ht) }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Montant TTC :</span>
                <span class="detail-value amount">{{
                  formatCurrency(selectedOrder.montant_ttc)
                }}</span>
              </div>
            </div>
          </div>

          <div class="modal-actions">
            <button
              role="button"
              aria-label="Fermer"
              class="modal-btn secondary"
              @click="showDetailsModal = false"
            >
              Fermer
            </button>
            <button
              role="button"
              aria-label="Modifier la commande"
              class="modal-btn primary"
              @click="
                () => {
                  showDetailsModal = false;
                  openEditModal(selectedOrder);
                }
              "
            >
              Modifier la commande
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Créer commande -->
    <div v-if="showCreateModal" class="modal-overlay" @click="showCreateModal = false">
      <div class="modal-content modal-create" @click.stop>
        <div class="modal-header">
          <h2>Nouvelle commande</h2>
          <button
            role="button"
            aria-label="Fermer"
            @click="showCreateModal = false"
            class="modal-close"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submitNewOrder" class="create-order-form">
            <div class="form-grid">
              <!-- Sélection du client -->
              <div class="form-group">
                <label for="client-select" class="form-label">Client *</label>
                <select
                  id="client-select"
                  v-model="newOrderForm.id_client"
                  class="form-select"
                  required
                >
                  <option value="">Sélectionner un client</option>
                  <option
                    v-for="client in clients"
                    :key="client.id_client"
                    :value="client.id_client"
                  >
                    {{ getFullName(client) }}
                  </option>
                </select>
              </div>

              <!-- Date de commande -->
              <div class="form-group">
                <label for="date-input" class="form-label">Date de commande *</label>
                <input
                  id="date-input"
                  type="date"
                  v-model="newOrderForm.date_commande"
                  class="form-input"
                  required
                />
              </div>

              <!-- Statut -->
              <div class="form-group">
                <label for="status-select" class="form-label">Statut *</label>
                <select
                  id="status-select"
                  v-model="newOrderForm.statut"
                  class="form-select"
                  required
                >
                  <option :value="STATUT_ATTENTE">En attente</option>
                  <option :value="STATUT_PREPARATION">En préparation</option>
                  <option :value="STATUT_EXPEDIE">Expédiée</option>
                </select>
              </div>
            </div>

            <!-- Section des lots -->
            <div class="lots-section">
              <div class="lots-header">
                <h4 class="section-subtitle">Lots commandés</h4>
                <button type="button" class="add-lot-btn" @click="addLotToForm">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="16" />
                    <line x1="8" y1="12" x2="16" y2="12" />
                  </svg>
                  Ajouter un lot
                </button>
              </div>

              <div v-if="newOrderForm.lots.length > 0" class="lots-list">
                <div v-for="(lot, index) in newOrderForm.lots" :key="index" class="lot-item">
                  <div class="lot-select-group">
                    <label class="form-label">Lot</label>
                    <select v-model="lot.id_lot" class="form-select" required>
                      <option value="">Sélectionner un lot</option>
                      <option
                        v-for="availableLot in lots"
                        :key="availableLot.id_lot"
                        :value="availableLot.id_lot"
                        :class="{
                          'low-stock': availableLot.quantite_stock <= availableLot.seuil_alerte,
                        }"
                      >
                        {{ availableLot.nom }} (Stock: {{ availableLot.quantite_stock }})
                        {{ availableLot.quantite_stock <= availableLot.seuil_alerte ? "⚠️" : "" }}
                      </option>
                    </select>
                  </div>

                  <div class="lot-quantity-group">
                    <label class="form-label">Quantité</label>
                    <input
                      type="number"
                      v-model.number="lot.quantite"
                      min="1"
                      class="form-input"
                      required
                    />
                  </div>

                  <button
                    type="button"
                    class="remove-lot-btn"
                    @click="removeLotFromForm(index)"
                    title="Supprimer ce lot"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <line x1="18" y1="6" x2="6" y2="18" />
                      <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                  </button>
                </div>
              </div>
              <div v-else class="empty-lots">
                Aucun lot ajouté. Cliquez sur "Ajouter un lot" pour commencer.
              </div>
            </div>

            <!-- Montant total -->
            <div class="total-section">
              <div class="total-row">
                <span class="total-label">Montant HT :</span>
                <span class="total-value">{{ formatCurrency(calculateTotalHT) }}</span>
              </div>
              <div class="total-row">
                <span class="total-label">Montant TTC (20%) :</span>
                <span class="total-value total-ttc">{{
                  formatCurrency(calculateTotalHT * 1.2)
                }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="modal-actions">
              <button
                type="button"
                class="modal-btn secondary"
                @click="showCreateModal = false"
                :disabled="createOrderLoading"
              >
                Annuler
              </button>
              <button type="submit" class="modal-btn primary" :disabled="createOrderLoading">
                <span v-if="createOrderLoading">Création en cours...</span>
                <span v-else>Créer la commande</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Modifier commande -->
    <div v-if="showEditModal" class="modal-overlay" @click="showEditModal = false">
      <div class="modal-content modal-edit" @click.stop>
        <div class="modal-header">
          <h2>Modifier la commande #{{ editOrderForm.id_commande }}</h2>
          <button
            role="button"
            aria-label="Fermer"
            @click="showEditModal = false"
            class="modal-close"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submitEditOrder" class="edit-order-form">
            <div class="form-grid">
              <!-- Client -->
              <div class="form-group">
                <label for="edit-client-select" class="form-label">Client *</label>
                <select
                  id="edit-client-select"
                  v-model="editOrderForm.id_client"
                  class="form-select"
                  required
                >
                  <option value="">Sélectionner un client</option>
                  <option
                    v-for="client in clients"
                    :key="client.id_client"
                    :value="client.id_client"
                  >
                    {{ getFullName(client) }}
                  </option>
                </select>
              </div>

              <!-- Date -->
              <div class="form-group">
                <label for="edit-date-input" class="form-label">Date de commande *</label>
                <input
                  id="edit-date-input"
                  type="date"
                  v-model="editOrderForm.date_commande"
                  class="form-input"
                  required
                />
              </div>

              <!-- Statut -->
              <div class="form-group">
                <label for="edit-status-select" class="form-label">Statut *</label>
                <select
                  id="edit-status-select"
                  v-model="editOrderForm.statut"
                  class="form-select"
                  required
                >
                  <option value="0">En attente</option>
                  <option value="1">En préparation</option>
                  <option value="2">Expédiée</option>
                </select>
              </div>
            </div>

            <!-- Section des lots -->
            <div class="lots-section">
              <div class="lots-header">
                <h4 class="section-subtitle">Lots commandés</h4>
                <button type="button" class="add-lot-btn" @click="addLotToEditForm">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="16" />
                    <line x1="8" y1="12" x2="16" y2="12" />
                  </svg>
                  Ajouter un lot
                </button>
              </div>

              <div v-if="editOrderForm.lots.length > 0" class="lots-list">
                <div v-for="(lot, index) in editOrderForm.lots" :key="index" class="lot-item">
                  <div class="lot-select-group">
                    <label class="form-label">Lot</label>
                    <select v-model="lot.id_lot" class="form-select" required>
                      <option value="">Sélectionner un lot</option>
                      <option
                        v-for="availableLot in lots"
                        :key="availableLot.id_lot"
                        :value="availableLot.id_lot"
                        :class="{
                          'low-stock': availableLot.quantite_stock <= availableLot.seuil_alerte,
                        }"
                      >
                        {{ availableLot.nom }} (Stock: {{ availableLot.quantite_stock }})
                        {{ availableLot.quantite_stock <= availableLot.seuil_alerte ? "⚠️" : "" }}
                      </option>
                    </select>
                  </div>

                  <div class="lot-quantity-group">
                    <label class="form-label">Quantité</label>
                    <input
                      type="number"
                      v-model.number="lot.quantite"
                      min="1"
                      class="form-input"
                      required
                    />
                  </div>

                  <button
                    type="button"
                    class="remove-lot-btn"
                    @click="removeLotFromEditForm(index)"
                    title="Supprimer ce lot"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <line x1="18" y1="6" x2="6" y2="18" />
                      <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                  </button>
                </div>
              </div>
              <div v-else class="empty-lots">Aucun lot dans cette commande.</div>
            </div>

            <!-- Montant total -->
            <div class="total-section">
              <div class="total-row">
                <span class="total-label">Montant HT :</span>
                <span class="total-value">{{ formatCurrency(calculateEditTotalHT) }}</span>
              </div>
              <div class="total-row">
                <span class="total-label">Montant TTC (20%) :</span>
                <span class="total-value total-ttc">{{
                  formatCurrency(calculateEditTotalHT * 1.2)
                }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="modal-actions">
              <button
                type="button"
                class="modal-btn secondary"
                @click="showEditModal = false"
                :disabled="updateOrderLoading"
              >
                Annuler
              </button>
              <button type="submit" class="modal-btn primary" :disabled="updateOrderLoading">
                <span v-if="updateOrderLoading">Mise à jour en cours...</span>
                <span v-else>Mettre à jour la commande</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted, watch } from "vue";
  import { useRouter } from "vue-router";
  import { triggerToast } from "@/utils/toastHelper";
  import { VITE_API_URL } from "@/constants/constants";

  // Constantes pour les statuts de commande
  const STATUT_ATTENTE = "0";
  const STATUT_PREPARATION = "1";
  const STATUT_EXPEDIE = "2";

  export default {
    name: "OrdersView",
    metaInfo: {
      meta: [
        {
          name: "description",
          content:
            "Gérez les commandes de Fashion Chic, visualisez les détails, filtrez par client et statut, et accédez aux actions pour modifier ou imprimer.",
        },
      ],
    },
    setup() {
      const router = useRouter();
      const searchQuery = ref("");
      const orders = ref([]);
      const clients = ref([]);
      const lots = ref([]);
      const commandeLots = ref([]);
      const loading = ref(true);
      const error = ref(null);
      const currentPage = ref(1);
      const itemsPerPage = 10;
      const showDetailsModal = ref(false);
      const showCreateModal = ref(false);
      const showEditModal = ref(false);
      const selectedOrder = ref(null);

      const filters = ref({
        clientId: "",
        status: "",
      });

      // Données pour la nouvelle commande
      const newOrderForm = ref({
        id_client: "",
        date_commande: new Date().toISOString().split("T")[0],
        statut: STATUT_ATTENTE,
        lots: [],
      });

      // Données pour la modification de commande
      const editOrderForm = ref({
        id_commande: "",
        id_client: "",
        date_commande: "",
        statut: "",
        montant_ht: 0,
        lots: [],
      });

      const createOrderLoading = ref(false);
      const updateOrderLoading = ref(false);

      // Fonction pour récupérer toutes les données
      const fetchData = async () => {
        loading.value = true;
        error.value = null;

        try {
          // Récupérer les commandes
          const ordersResponse = await fetch(VITE_API_URL + "get_table?table=commande", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          // Récupérer les clients
          const clientsResponse = await fetch(VITE_API_URL + "get_table?table=client", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          // Récupérer les lots
          const lotsResponse = await fetch(VITE_API_URL + "get_table?table=lot", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          // Récupérer les relations commande-lot
          const commandeLotsResponse = await fetch(VITE_API_URL + "get_table?table=commande_lot", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          if (
            !ordersResponse.ok ||
            !clientsResponse.ok ||
            !lotsResponse.ok ||
            !commandeLotsResponse.ok
          ) {
            throw new Error("Erreur lors du chargement des données");
          }

          const ordersData = await ordersResponse.json();
          const clientsData = await clientsResponse.json();
          const lotsData = await lotsResponse.json();
          const commandeLotsData = await commandeLotsResponse.json();

          if (ordersData.success && ordersData.data) {
            // Trier les commandes par date décroissante
            orders.value = ordersData.data.sort(
              (a, b) => new Date(b.date_commande) - new Date(a.date_commande),
            );
          }

          if (clientsData.success && clientsData.data) {
            clients.value = clientsData.data;
          }

          if (lotsData.success && lotsData.data) {
            lots.value = lotsData.data;
          }

          if (commandeLotsData.success && commandeLotsData.data) {
            commandeLots.value = commandeLotsData.data;
          }
        } catch (err) {
          console.error("Erreur lors du chargement:", err);
          error.value = "Impossible de charger les données. Veuillez réessayer.";
        } finally {
          loading.value = false;
        }
      };

      // Computed properties
      const uniqueClients = computed(() => {
        return clients.value.sort((a, b) => {
          const nameA = getFullName(a);
          const nameB = getFullName(b);
          return nameA.localeCompare(nameB);
        });
      });

      const filteredOrders = computed(() => {
        let result = orders.value;

        // Filtre par client
        if (filters.value.clientId) {
          result = result.filter(order => order.id_client == filters.value.clientId);
        }

        // Filtre par statut
        if (filters.value.status) {
          result = result.filter(order => order.statut == filters.value.status);
        }

        // Filtre par recherche
        if (searchQuery.value) {
          if (searchQuery.value.startsWith("#")) {
            // Si la recherche commence par #, on filtre par id
            const id = searchQuery.value.slice(1).toLowerCase();
            result = result.filter(order => order.id_commande.toString().includes(id));
          } else {
            const query = searchQuery.value.toLowerCase();
            result = result.filter(order => {
              const client = getClient(order.id_client);
              const clientName = client ? getFullName(client).toLowerCase() : "";
              const numero_commande = order.numero_commande ? order.numero_commande.toString() : "";
              return (
                numero_commande.includes(query) ||
                clientName.includes(query) ||
                order.montant_ttc.toString().includes(query)
              );
            });
          }
        }

        return result;
      });

      // Pagination computed properties
      const totalPages = computed(() => {
        return Math.ceil(filteredOrders.value.length / itemsPerPage);
      });

      const startIndex = computed(() => {
        return (currentPage.value - 1) * itemsPerPage;
      });

      const endIndex = computed(() => {
        return Math.min(startIndex.value + itemsPerPage, filteredOrders.value.length);
      });

      const paginatedOrders = computed(() => {
        return filteredOrders.value.slice(startIndex.value, endIndex.value);
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

      const pendingCount = computed(() => {
        return orders.value.filter(order => order.statut === "0" || order.statut === 0).length;
      });

      const inProgressCount = computed(() => {
        return orders.value.filter(order => order.statut === "1" || order.statut === 1).length;
      });

      // Watchers
      watch([searchQuery, filters], () => {
        currentPage.value = 1;
      });

      // Fonctions utilitaires
      const getClient = clientId => {
        return clients.value.find(client => client.id_client === clientId);
      };

      const getFullName = client => {
        if (!client) return "";

        // Si le client a une raison sociale, l'utiliser
        if (client.raison_sociale) {
          return client.raison_sociale;
        }

        // Sinon, combiner prénom et nom
        const prenom = client.prénom || "";
        const nom = client.nom || "";
        return `${prenom} ${nom}`.trim();
      };

      const getClientName = clientId => {
        const client = getClient(clientId);
        if (!client) return `Client #${clientId}`;

        return getFullName(client) || `Client #${clientId}`;
      };

      // Fonctions pour les lots
      const getOrderLots = orderId => {
        return commandeLots.value.filter(cl => cl.id_commande === orderId);
      };

      const getOrderLotsCount = orderId => {
        return getOrderLots(orderId).length;
      };

      const getLotInfo = lotId => {
        return lots.value.find(lot => lot.id_lot === lotId);
      };

      const getStockClass = lot => {
        if (!lot) return "";
        if (lot.quantite_stock <= lot.seuil_alerte) return "stock-alert";
        if (lot.quantite_stock <= lot.seuil_alerte * 2) return "stock-warning";
        return "stock-ok";
      };

      const formatDate = dateString => {
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

      const formatCurrency = amount => {
        return new Intl.NumberFormat("fr-FR", {
          style: "currency",
          currency: "EUR",
        }).format(amount);
      };

      const formatPhone = phone => {
        if (!phone || phone === "32767") return "Non renseigné";

        // Formater le numéro de téléphone français
        const cleaned = phone.toString().replace(/\D/g, "");
        if (cleaned.length === 10) {
          return cleaned.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, "$1 $2 $3 $4 $5");
        }
        return phone;
      };

      const getStatusClass = status => {
        const statusStr = status?.toString();
        const statusClasses = {
          0: "status-pending", // attente
          1: "status-progress", // preparation
          2: "status-shipped", // expedie
          // Pour la compatibilité avec l'ancien format
          attente: "status-pending",
          preparation: "status-progress",
          expedie: "status-shipped",
        };
        return statusClasses[statusStr] || "status-default";
      };

      const getStatusLabel = status => {
        const statusStr = status?.toString();
        const statusLabels = {
          0: "En attente",
          1: "En préparation",
          2: "Expédiée",
          // Pour la compatibilité avec l'ancien format
          attente: "En attente",
          preparation: "En préparation",
          expedie: "Expédiée",
        };
        return statusLabels[statusStr] || status;
      };

      const goToPage = page => {
        if (page !== "..." && page >= 1 && page <= totalPages.value) {
          currentPage.value = page;
        }
      };

      const goToNewOrder = () => {
        // Réinitialiser le formulaire
        newOrderForm.value = {
          id_client: "",
          date_commande: new Date().toISOString().split("T")[0],
          statut: STATUT_ATTENTE,
          lots: [],
        };
        showCreateModal.value = true;
      };

      // Ajouter un lot au formulaire
      const addLotToForm = () => {
        newOrderForm.value.lots.push({
          id_lot: "",
          quantite: 1,
        });
      };

      // Supprimer un lot du formulaire
      const removeLotFromForm = index => {
        newOrderForm.value.lots.splice(index, 1);
      };

      // Ajouter un lot au formulaire d'édition
      const addLotToEditForm = () => {
        editOrderForm.value.lots.push({
          id_lot: "",
          quantite: 1,
        });
      };

      // Supprimer un lot du formulaire d'édition
      const removeLotFromEditForm = index => {
        editOrderForm.value.lots.splice(index, 1);
      };

      // Calculer le montant HT total
      const calculateTotalHT = computed(() => {
        return newOrderForm.value.lots.reduce((total, lot) => {
          const lotInfo = lots.value.find(l => l.id_lot == lot.id_lot);
          if (lotInfo && lot.quantite > 0) {
            // Utiliser un prix fixe pour l'instant (à remplacer par le vrai prix du lot)
            const prixUnitaire = 100;
            return total + lot.quantite * prixUnitaire;
          }
          return total;
        }, 0);
      });

      // Calculer le montant HT total pour l'édition
      const calculateEditTotalHT = computed(() => {
        return editOrderForm.value.lots.reduce((total, lot) => {
          const lotInfo = lots.value.find(l => l.id_lot == lot.id_lot);
          if (lotInfo && lot.quantite > 0) {
            // Utiliser un prix fixe pour l'instant (à remplacer par le vrai prix du lot)
            const prixUnitaire = 100;
            return total + lot.quantite * prixUnitaire;
          }
          return total;
        }, 0);
      });

      // Fonction pour mettre à jour une commande
      const updateOrder = async orderData => {
        try {
          const formData = new FormData();
          formData.append("id_commande", orderData.id_commande.toString());
          formData.append("id_client", orderData.id_client.toString());
          formData.append("date_commande", orderData.date_commande);
          formData.append("statut", orderData.statut.toString());
          formData.append("montant_ht", orderData.montant_ht.toString());

          console.log("FormData envoyé pour mise à jour:", {
            id_commande: orderData.id_commande,
            id_client: orderData.id_client,
            date_commande: orderData.date_commande,
            statut: orderData.statut,
            montant_ht: orderData.montant_ht,
          });

          const response = await fetch(VITE_API_URL + "update_commande", {
            method: "POST",
            body: formData,
            credentials: "include",
          });

          const data = await response.json();
          console.log("Réponse update_commande:", data);

          if (!response.ok || !data.success) {
            throw new Error(data.message || "Erreur lors de la mise à jour de la commande");
          }

          return data;
        } catch (error) {
          console.error("Erreur lors de la mise à jour de la commande:", error);
          throw error;
        }
      };

      // Fonction pour mettre à jour un lot de commande
      const updateOrderLot = async lotData => {
        try {
          const formData = new FormData();
          formData.append("id_lot", lotData.id_lot.toString());
          formData.append("id_commande", lotData.id_commande.toString());
          formData.append("quantite", lotData.quantite.toString());

          console.log("FormData envoyé pour mise à jour lot:", {
            id_lot: lotData.id_lot,
            id_commande: lotData.id_commande,
            quantite: lotData.quantite,
          });

          const response = await fetch(VITE_API_URL + "update_commande_lot", {
            method: "POST",
            body: formData,
            credentials: "include",
          });

          const data = await response.json();
          console.log("Réponse update_commande_lot:", data);

          if (!response.ok || !data.success) {
            throw new Error(data.message || "Erreur lors de la mise à jour du lot");
          }

          return data;
        } catch (error) {
          console.error("Erreur lors de la mise à jour du lot:", error);
          throw error;
        }
      };

      // Fonction pour supprimer un lot spécifique d'une commande
      const deleteOrderLot = async (id_lot, id_commande) => {
        try {
          const formData = new FormData();
          formData.append("id_lot", id_lot.toString());
          formData.append("id_commande", id_commande.toString());

          const response = await fetch(VITE_API_URL + "delete_commande_lot", {
            method: "POST",
            body: formData,
            credentials: "include",
          });

          const data = await response.json();

          if (!response.ok || !data.success) {
            throw new Error(data.message || "Erreur lors de la suppression du lot");
          }

          return data;
        } catch (error) {
          console.error("Erreur lors de la suppression du lot:", error);
          throw error;
        }
      };

      // Fonction pour supprimer tous les lots d'une commande
      const deleteOrderLots = async orderId => {
        try {
          const orderLots = getOrderLots(orderId);

          if (orderLots.length === 0) {
            console.log("Aucun lot à supprimer pour la commande:", orderId);
            return true;
          }

          const promises = orderLots.map(lot => {
            const formData = new FormData();
            formData.append("id_lot", lot.id_lot.toString());
            formData.append("id_commande", lot.id_commande.toString());

            console.log("Suppression lot:", {
              id_lot: lot.id_lot,
              id_commande: lot.id_commande,
            });

            return fetch(VITE_API_URL + "delete_commande_lot", {
              method: "POST",
              body: formData,
              credentials: "include",
            });
          });

          const responses = await Promise.all(promises);

          // Vérifier que toutes les suppressions ont réussi
          for (const response of responses) {
            const data = await response.json();
            if (!response.ok || !data.success) {
              console.warn("Erreur lors de la suppression d'un lot:", data.message);
            }
          }

          return true;
        } catch (error) {
          console.error("Erreur lors de la suppression des lots:", error);
          // On continue même en cas d'erreur
          return false;
        }
      };

      // Ouvrir la modal d'édition
      const openEditModal = order => {
        selectedOrder.value = order;

        // Pré-remplir le formulaire d'édition
        editOrderForm.value = {
          id_commande: order.id_commande,
          id_client: order.id_client,
          date_commande: order.date_commande.split(" ")[0], // Garder seulement la partie date
          statut: order.statut.toString(),
          montant_ht: order.montant_ht,
          lots: [],
        };

        // Charger les lots existants
        const orderLots = getOrderLots(order.id_commande);
        editOrderForm.value.lots = orderLots.map(lot => ({
          id_lot: lot.id_lot,
          quantite: lot.quantite,
        }));

        showEditModal.value = true;
      };

      // Soumettre la modification de commande
      const submitEditOrder = async () => {
        try {
          // Validation
          if (!editOrderForm.value.id_client) {
            triggerToast("Veuillez sélectionner un client", "error");
            return;
          }

          updateOrderLoading.value = true;

          // Mettre à jour la commande
          const orderData = {
            id_commande: editOrderForm.value.id_commande.toString(),
            id_client: editOrderForm.value.id_client.toString(),
            date_commande: editOrderForm.value.date_commande,
            statut: editOrderForm.value.statut.toString(),
            montant_ht: calculateEditTotalHT.value.toFixed(2),
          };

          console.log("Mise à jour commande avec:", orderData);

          await updateOrder(orderData);

          // Supprimer les anciens lots
          console.log(
            "Suppression des anciens lots de la commande:",
            editOrderForm.value.id_commande,
          );
          await deleteOrderLots(editOrderForm.value.id_commande);

          // Ajouter les nouveaux lots
          if (editOrderForm.value.lots.length > 0) {
            console.log("Ajout des nouveaux lots:", editOrderForm.value.lots);
            await addLotsToOrder(editOrderForm.value.id_commande, editOrderForm.value.lots);
          }

          triggerToast("Commande mise à jour avec succès", "success");
          showEditModal.value = false;

          // Recharger les données
          await fetchData();
        } catch (error) {
          triggerToast(error.message || "Erreur lors de la mise à jour de la commande", "error");
        } finally {
          updateOrderLoading.value = false;
        }
      };

      // Soumettre la nouvelle commande
      const submitNewOrder = async () => {
        try {
          // Validation
          if (!newOrderForm.value.id_client) {
            triggerToast("Veuillez sélectionner un client", "error");
            return;
          }

          if (newOrderForm.value.lots.length === 0) {
            triggerToast("Veuillez ajouter au moins un lot", "error");
            return;
          }

          const invalidLots = newOrderForm.value.lots.filter(
            lot => !lot.id_lot || lot.quantite <= 0,
          );
          if (invalidLots.length > 0) {
            triggerToast("Veuillez remplir tous les lots correctement", "error");
            return;
          }

          // Vérifier le stock disponible
          for (const lot of newOrderForm.value.lots) {
            const lotInfo = lots.value.find(l => l.id_lot == lot.id_lot);
            if (!lotInfo) {
              triggerToast(`Lot non trouvé: ${lot.id_lot}`, "error");
              return;
            }
            if (lot.quantite > lotInfo.quantite_stock) {
              triggerToast(
                `Stock insuffisant pour le lot ${lotInfo.nom} (disponible: ${lotInfo.quantite_stock})`,
                "error",
              );
              return;
            }
          }

          createOrderLoading.value = true;

          // Créer la commande
          const orderData = {
            id_client: newOrderForm.value.id_client.toString(),
            date_commande: newOrderForm.value.date_commande,
            statut: newOrderForm.value.statut.toString(),
            montant_ht: calculateTotalHT.value.toFixed(2),
          };

          console.log("Création commande avec:", orderData);

          const result = await createOrder(orderData);

          console.log("Résultat création commande:", result);

          // Recharger les données pour avoir la nouvelle commande
          await fetchData();

          // Trouver la commande créée (la plus récente = ID le plus élevé)
          const createdOrder = orders.value.sort(
            (a, b) => Number(b.id_commande) - Number(a.id_commande),
          )[0];

          if (createdOrder) {
            console.log("Commande créée, ID:", createdOrder.id_commande);

            // Ajouter les lots à la commande
            await addLotsToOrder(createdOrder.id_commande, newOrderForm.value.lots);
          } else {
            console.error("Commande créée non trouvée dans la liste");
          }

          triggerToast("Commande créée avec succès", "success");
          showCreateModal.value = false;

          // Recharger les données pour afficher la nouvelle commande
          await fetchData();
        } catch (error) {
          triggerToast(error.message || "Erreur lors de la création de la commande", "error");
        } finally {
          createOrderLoading.value = false;
        }
      };

      const exportOrders = () => {
        // Fonction d'export
        const data = [
          [
            "ID",
            "Numéro de commande",
            "ID Client",
            "Client",
            "Date de commande",
            "Nombre de lots",
            "Statut",
            "Montant HT",
            "Montant TTC",
          ],
          ...filteredOrders.value.map(o => [
            o.id_commande,
            o.numero_commande,
            o.id_client,
            getClientName(o.id_client),
            formatDate(o.date_commande),
            getOrderLotsCount(o.id_commande),
            getStatusLabel(o.statut),
            formatCurrency(o.montant_ht),
            formatCurrency(o.montant_ttc),
          ]),
        ];

        if (!data[1]) {
          triggerToast("Aucune commande à exporter !", "error");
          return;
        }
        const csvString = data.map(row => row.join(",")).join("\n");
        const blob = new Blob([csvString], { type: "text/csv;charset=utf-8;" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "commandes.csv";
        a.click();
        URL.revokeObjectURL(url);
      };

      const viewOrder = order => {
        selectedOrder.value = order;
        showDetailsModal.value = true;
      };

      const editOrder = orderId => {
        showDetailsModal.value = false;
        router.push(`/orders/${orderId}/edit`);
      };

      // Fonction pour créer une nouvelle commande
      const createOrder = async orderData => {
        try {
          // Créer la commande
          const formData = new FormData();
          formData.append("id_client", orderData.id_client.toString());
          formData.append("date_commande", orderData.date_commande);
          formData.append("statut", orderData.statut.toString());
          formData.append("montant_ht", orderData.montant_ht.toString());

          console.log("FormData envoyé pour création:", {
            id_client: orderData.id_client,
            date_commande: orderData.date_commande,
            statut: orderData.statut,
            montant_ht: orderData.montant_ht,
          });

          const response = await fetch(VITE_API_URL + "new_commande", {
            method: "POST",
            body: formData,
            credentials: "include",
          });

          const data = await response.json();
          console.log("Réponse new_commande:", data);

          if (!response.ok || !data.success) {
            throw new Error(data.message || "Erreur lors de la création de la commande");
          }

          return data;
        } catch (error) {
          console.error("Erreur lors de la création de la commande:", error);
          throw error;
        }
      };

      // Fonction pour ajouter des lots à une commande
      const addLotsToOrder = async (orderId, lots) => {
        try {
          // Filtrer les lots valides
          const validLots = lots.filter(lot => lot.id_lot && lot.quantite > 0);

          if (validLots.length === 0) {
            console.log("Aucun lot valide à ajouter");
            return true;
          }

          const promises = validLots.map(lot => {
            const formData = new FormData();
            formData.append("id_lot", lot.id_lot.toString());
            formData.append("id_commande", orderId.toString());
            formData.append("quantite", lot.quantite.toString());

            console.log("Ajout lot:", {
              id_lot: lot.id_lot,
              id_commande: orderId,
              quantite: lot.quantite,
            });

            return fetch(VITE_API_URL + "new_commande_lot", {
              method: "POST",
              body: formData,
              credentials: "include",
            }).then(async response => {
              const data = await response.json();
              if (!response.ok || !data.success) {
                console.error("Erreur new_commande_lot:", data);
                throw new Error(data.message || "Erreur lors de l'ajout du lot");
              }
              return data;
            });
          });

          const results = await Promise.all(promises);
          console.log("Tous les lots ajoutés:", results);

          return true;
        } catch (error) {
          console.error("Erreur lors de l'ajout des lots:", error);
          throw error;
        }
      };

      // Charger les données au montage
      onMounted(() => {
        fetchData();
      });

      return {
        searchQuery,
        filters,
        orders,
        clients,
        lots,
        commandeLots,
        loading,
        error,
        currentPage,
        showDetailsModal,
        showCreateModal,
        showEditModal,
        selectedOrder,
        newOrderForm,
        editOrderForm,
        createOrderLoading,
        updateOrderLoading,
        uniqueClients,
        filteredOrders,
        paginatedOrders,
        totalPages,
        startIndex,
        endIndex,
        visiblePages,
        pendingCount,
        inProgressCount,
        getClient,
        getFullName,
        getClientName,
        getOrderLots,
        getOrderLotsCount,
        getLotInfo,
        getStockClass,
        formatDate,
        formatCurrency,
        formatPhone,
        getStatusClass,
        getStatusLabel,
        goToPage,
        goToNewOrder,
        exportOrders,
        viewOrder,
        editOrder,
        openEditModal,
        createOrder,
        updateOrder,
        updateOrderLot,
        deleteOrderLot,
        addLotsToOrder,
        addLotToForm,
        removeLotFromForm,
        addLotToEditForm,
        removeLotFromEditForm,
        submitNewOrder,
        submitEditOrder,
        calculateTotalHT,
        calculateEditTotalHT,
        fetchData,
        // Constantes de statut exportées pour utilisation dans d'autres composants
        STATUT_ATTENTE,
        STATUT_PREPARATION,
        STATUT_EXPEDIE,
      };
    },
  };
</script>

<style scoped>
  .orders-page {
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

  .create-button {
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
    box-shadow: 0 2px 8px rgba(85, 0, 255, 0.3);
    font-size: 14px;
    font-weight: 500;
  }

  .create-button:hover {
    background: #5500cc;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(85, 0, 255, 0.3);
  }

  .create-button svg {
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
    background: #2563eb;
    transform: translateY(-1px);
  }

  .export-button svg {
    width: 16px;
    height: 16px;
    stroke-width: 2;
  }

  .search-container {
    position: relative;
    flex: 1;
    min-width: 340px;
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

  .filter-wrapper label {
    position: absolute;
    top: -25px;
    left: 3px;
    font-size: 14px;
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

  .stat-value.pending {
    color: #f59e0b;
  }

  .stat-value.progress {
    color: #0062ff;
  }

  .table-container {
    overflow-x: auto;
  }

  .orders-table {
    width: 100%;
    min-width: max-content;
  }

  .orders-table th {
    background: #f8fafc;
    padding: 1rem;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #e2e8f0;
  }

  .orders-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: black;
    vertical-align: middle;
  }

  .orders-table tbody tr:hover {
    background: #f8fafc;
  }

  .orders-table tbody tr:last-child td {
    border-bottom: none;
  }

  .order-id {
    font-weight: 600;
    color: #0f172a;
  }

  .client-name {
    font-weight: 500;
    color: black;
  }

  .order-date {
    color: #64748b;
    font-size: 13px;
  }

  .order-amount {
    font-weight: 600;
    color: #0f172a;
  }

  .lots-count {
    text-align: center;
  }

  .lots-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 8px;
    background: #e0e7ff;
    color: #4338ca;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
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
    text-transform: capitalize;
    white-space: nowrap;
  }

  .status-pending {
    background: #fef3c7;
    color: #92400e;
  }

  .status-progress {
    background: #dbeafe;
    color: #1e40af;
  }

  .status-shipped {
    background: #d1fae5;
    color: #047857;
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
    max-width: 800px;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  }

  .modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .modal-header h2 {
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

  /* MODAL DÉTAILS */
  .details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
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

  .detail-value.amount {
    font-size: 16px;
    color: #00b8d4;
  }

  /* TABLE DES LOTS */
  .lots-table-container {
    overflow-x: auto;
  }

  .lots-table {
    width: 100%;
    border-collapse: collapse;
  }

  .lots-table th {
    background: #f1f5f9;
    text-align: left;
    padding: 0.75rem;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #e2e8f0;
  }

  .lots-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
    color: #334155;
  }

  .lots-table tbody tr:last-child td {
    border-bottom: none;
  }

  .lot-name {
    font-weight: 600;
    color: #0f172a;
  }

  .lot-description {
    font-size: 13px;
    color: #64748b;
  }

  .lot-quantity {
    font-weight: 600;
    text-align: center;
    color: #0f172a;
  }

  .lot-stock {
    text-align: center;
    font-weight: 600;
  }

  .stock-ok {
    color: #059669;
  }

  .stock-warning {
    color: #f59e0b;
  }

  .stock-alert {
    color: #dc2626;
  }

  .empty-lots {
    text-align: center;
    padding: 2rem;
    color: #64748b;
    font-style: italic;
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
    color: black;
  }

  /* MODAL CRÉATION & ÉDITION */
  .modal-create,
  .modal-edit {
    max-width: 900px;
  }

  .create-order-form,
  .edit-order-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
  }

  .form-group {
    display: flex;
    flex-direction: column;
  }

  .form-label {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
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

  .form-select option.low-stock {
    color: #dc2626;
    font-weight: 600;
  }

  .lots-section {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
  }

  .lots-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .add-lot-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #00b8d4;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .add-lot-btn:hover {
    background: #0891a6;
  }

  .add-lot-btn svg {
    width: 16px;
    height: 16px;
    stroke-width: 2;
  }

  .lots-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .lot-item {
    display: grid;
    grid-template-columns: 2fr 1fr auto;
    gap: 1rem;
    align-items: end;
    padding: 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
  }

  .lot-select-group,
  .lot-quantity-group {
    display: flex;
    flex-direction: column;
  }

  .remove-lot-btn {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fca5a5;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .remove-lot-btn:hover {
    background: #fee2e2;
    border-color: #f87171;
  }

  .remove-lot-btn svg {
    width: 16px;
    height: 16px;
    stroke-width: 2;
  }

  .total-section {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
    border: 2px solid #e2e8f0;
  }

  .total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
  }

  .total-row:first-child {
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 0.75rem;
  }

  .total-label {
    font-size: 14px;
    color: #64748b;
    font-weight: 500;
  }

  .total-value {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
  }

  .total-ttc {
    font-size: 18px;
    color: #00b8d4;
  }

  .modal-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  /* RESPONSIVE */
  @media (max-width: 1024px) {
    .page-header {
      flex-direction: column;
      gap: 1rem;
      align-items: stretch;
    }

    .create-button {
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

    .details-grid {
      grid-template-columns: 1fr;
    }

    .form-grid {
      grid-template-columns: 1fr;
    }

    .lot-item {
      grid-template-columns: 1fr;
      gap: 0.5rem;
    }

    .remove-lot-btn {
      width: 100%;
    }
  }

  @media (max-width: 768px) {
    .search-container {
      min-width: auto;
    }

    .orders-table {
      font-size: 12px;
    }

    .orders-table th:nth-child(5),
    .orders-table td:nth-child(5) {
      display: none;
    }

    .status-badge {
      font-size: 10px;
      padding: 0.25rem 0.5rem;
    }

    .actions {
      gap: 0.5rem;
    }

    .action-btn {
      padding: 4px;
    }

    .action-btn svg {
      width: 20px;
      height: 20px;
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

    .lots-table {
      font-size: 12px;
    }

    .lots-table th,
    .lots-table td {
      padding: 0.5rem;
    }

    @media (max-width: 375px) {
      .search-container > input:placeholder-shown {
        text-overflow: ellipsis;
      }

      .search-container > input:focus::placeholder {
        color: transparent;
      }
    }
  }
</style>
