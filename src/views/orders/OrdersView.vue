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
            <option value="attente">En attente</option>
            <option value="preparation">En préparation</option>
            <option value="expedie">Expédiée</option>
          </select>
          <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>

        <button role="button" aria-label="Rechercher" class="search-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          Rechercher
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
                <th>N° Commande</th>
                <th>Client</th>
                <th>Date</th>
                <th>Montant TTC</th>
                <th>État</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in paginatedOrders" :key="order.id_commande">
                <td class="order-id">#{{ String(order.id_commande).padStart(5, "0") }}</td>
                <td class="client-name">
                  {{ getClientName(order.id_client) }}
                </td>
                <td class="order-date">
                  {{ formatDate(order.date_commande) }}
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
                    @click="editOrder(order.id_commande)"
                    title="Modifier"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                  </button>
                  <button
                    role="button"
                    aria-label="Imprimer"
                    class="action-btn"
                    @click="printOrder(order.id_commande)"
                    title="Imprimer"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <polyline points="6 9 6 2 18 2 18 9" />
                      <path
                        d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"
                      />
                      <rect x="6" y="14" width="12" height="8" />
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
                <span class="detail-value"
                  >#{{ String(selectedOrder.id_commande).padStart(5, "0") }}</span
                >
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
            </div>

            <div class="details-section full-width">
              <h4 class="section-subtitle">Informations financières</h4>
              <div class="detail-row">
                <span class="detail-label">Montant total :</span>
                <span class="detail-value amount">{{
                  formatCurrency(selectedOrder.montant_total)
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
              @click="editOrder(selectedOrder.id_commande)"
            >
              Modifier la commande
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted, watch } from "vue";
  import { useRouter } from "vue-router";
  import { VITE_API_URL } from "@/constants/constants.js";

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
      const loading = ref(true);
      const error = ref(null);
      const currentPage = ref(1);
      const itemsPerPage = 10;
      const showDetailsModal = ref(false);
      const selectedOrder = ref(null);

      const filters = ref({
        clientId: "",
        status: "",
      });

      // Fonction pour récupérer les commandes et les clients
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

          if (!ordersResponse.ok || !clientsResponse.ok) {
            throw new Error("Erreur lors du chargement des données");
          }

          const ordersData = await ordersResponse.json();
          const clientsData = await clientsResponse.json();

          if (ordersData.success && ordersData.data) {
            // Trier les commandes par date décroissante
            orders.value = ordersData.data.sort(
              (a, b) => new Date(b.date_commande) - new Date(a.date_commande),
            );
          }

          if (clientsData.success && clientsData.data) {
            clients.value = clientsData.data;
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
          result = result.filter(order => order.statut === filters.value.status);
        }

        // Filtre par recherche
        if (searchQuery.value) {
          const query = searchQuery.value.toLowerCase();
          result = result.filter(order => {
            const client = getClient(order.id_client);
            const clientName = client ? getFullName(client).toLowerCase() : "";
            return (
              order.id_commande.toString().padStart(5, "0").includes(query) ||
              clientName.includes(query) ||
              order.montant_total.toString().includes(query)
            );
          });
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
        return orders.value.filter(order => order.statut === "attente").length;
      });

      const inProgressCount = computed(() => {
        return orders.value.filter(order => order.statut === "preparation").length;
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
        const statusClasses = {
          attente: "status-pending",
          preparation: "status-progress",
          expedie: "status-shipped",
        };
        return statusClasses[status] || "status-default";
      };

      const getStatusLabel = status => {
        const statusLabels = {
          attente: "En attente",
          preparation: "En préparation",
          expedie: "Expédiée",
        };
        return statusLabels[status] || status;
      };

      const goToPage = page => {
        if (page !== "..." && page >= 1 && page <= totalPages.value) {
          currentPage.value = page;
        }
      };

      const goToNewOrder = () => {
        router.push("/orders/new");
      };

      const viewOrder = order => {
        selectedOrder.value = order;
        showDetailsModal.value = true;
      };

      const editOrder = orderId => {
        showDetailsModal.value = false;
        router.push(`/orders/${orderId}/edit`);
      };

      const printOrder = orderId => {
        console.log("Imprimer la commande:", orderId);
        // TODO: Implémenter la fonction d'impression
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
        loading,
        error,
        currentPage,
        showDetailsModal,
        selectedOrder,
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
        getClientName,
        formatDate,
        formatCurrency,
        formatPhone,
        getStatusClass,
        getStatusLabel,
        goToPage,
        goToNewOrder,
        viewOrder,
        editOrder,
        printOrder,
        fetchData,
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

  .search-button {
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

  .search-button:hover {
    background: #2563eb;
    transform: translateY(-1px);
  }

  .search-button svg {
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

  .search-input:focus {
    outline: none;
    border-color: #00b8d4;
    box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
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
    max-width: 600px;
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

  /* MODAL DÉTAILS */
  .details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
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
  }

  @media (max-width: 768px) {
    .search-container {
      min-width: auto;
    }

    .orders-table {
      font-size: 12px;
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
