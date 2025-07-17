<template>
  <div class="prepare-page">
    <!-- En-tête -->
    <div class="page-header">
      <h2 class="page-title">Gestion des préparations</h2>
    </div>

    <!-- Cartes KPI -->
    <div class="kpi-section">
      <div class="kpi-card kpi-urgent">
        <div class="kpi-icon urgent-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
            />
            <line x1="12" y1="9" x2="12" y2="13" />
            <line x1="12" y1="17" x2="12.01" y2="17" />
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Urgentes</p>
          <p class="kpi-value">{{ urgentCount }}</p>
        </div>
      </div>

      <div class="kpi-card kpi-normal">
        <div class="kpi-icon normal-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <polyline points="14 2 14 8 20 8" />
            <line x1="16" y1="13" x2="8" y2="13" />
            <line x1="16" y1="17" x2="8" y2="17" />
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">En attente</p>
          <p class="kpi-value">{{ totalPending }}</p>
        </div>
      </div>

      <div class="kpi-card kpi-progress">
        <div class="kpi-icon progress-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <circle cx="12" cy="12" r="10" />
            <polyline points="12 6 12 12 16 14" />
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">En préparation</p>
          <p class="kpi-value">{{ inProgressCount }}</p>
        </div>
      </div>

      <div class="kpi-card kpi-shipped">
        <div class="kpi-icon shipped-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M9 11l3 3L22 4"/>
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Expédiées</p>
          <p class="kpi-value">{{ shippedCount }}</p>
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
            placeholder="Rechercher par numéro de commande, client ou montant"
            class="search-input"
          />
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </div>

        <div class="filter-wrapper">
          <label for="priority-filter" class="filter-label">Priorité :</label>
          <select id="priority-filter" v-model="filterPriority" class="filter-select">
            <option value="">Toutes les priorités</option>
            <option value="urgent">Urgente</option>
            <option value="normal">Normale</option>
          </select>
          <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>

        <div class="filter-wrapper">
          <label for="status-filter" class="filter-label">Statut :</label>
          <select id="status-filter" v-model="filterStatus" class="filter-select">
            <option value="">Tous les statuts</option>
            <option value="attente">En attente</option>
            <option value="1">En attente (1)</option>
            <option value="preparation">En préparation</option>
            <option value="expedie">Expédiée</option>
            <option value="2">Expédiée</option>

          </select>
          <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Liste des commandes -->
    <div class="orders-section">
      <div class="orders-card">
        <div class="orders-header">
          <h3 class="orders-title">File d'attente</h3>
          <span class="orders-count">{{ filteredOrders.length }} commande(s)</span>
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

        <div v-else class="orders-list">
          <div
            v-for="order in filteredOrders"
            :key="order.id_commande"
            class="order-item"
            :class="{ urgent: isUrgent(order) }"
          >
            <div class="order-main">
              <div class="order-info">
                <span class="order-id">{{ order.numero_commande || `#${String(order.id_commande).padStart(5, "0")}` }}</span>
                <span class="order-client">{{ getClientName(order.id_client) }}</span>
                <span class="order-date">{{ formatDate(order.date_commande) }}</span>
                <span class="order-amount">{{ formatCurrency(order.montant_ttc && order.montant_ttc !== "0.00" ? order.montant_ttc : order.montant_ht) }}</span>
              </div>
            </div>

            <div class="order-right">
              <div class="order-badges">
                <span v-if="isUrgent(order)" class="badge badge-urgent">Urgent</span>
                <span class="badge" :class="getStatusClass(order.statut)">
                  {{ getStatusLabel(order.statut) }}
                </span>
              </div>

              <div class="order-actions">
                <button
                  role="button"
                  aria-label="Commencer la préparation"
                  v-if="order.statut === 'attente' || order.statut === '1'"
                  class="action-btn primary"
                  @click="startPreparation(order)"
                >
                  Commencer
                </button>

                <button
                  role="button"
                  aria-label="Terminer la préparation"
                  v-else-if="order.statut === 'preparation'"
                  class="action-btn success"
                  @click="completePreparation(order)"
                >
                  Terminer
                </button>

                <button
                  role="button"
                  aria-label="Commande expédiée"
                  v-else-if="order.statut === 'expedie'"
                  class="action-btn completed"
                  disabled
                >
                  Expédiée
                </button>

                <button
                  role="button"
                  aria-label="Voir le contenu"
                  class="action-btn icon-btn"
                  @click="viewOrderContent(order)"
                  title="Voir contenu"
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path
                      d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"
                    />
                    <line x1="9" y1="12" x2="15" y2="12" />
                    <line x1="9" y1="16" x2="15" y2="16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <div v-if="filteredOrders.length === 0" class="empty-message">
            <p>Aucune commande à préparer</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Contenu de la commande -->
    <div v-if="showContentModal" class="modal-overlay" @click="showContentModal = false">
      <div class="modal-content modal-details" @click.stop>
        <div class="modal-header">
          <h2>Contenu de la commande</h2>
          <button
            role="button"
            aria-label="Fermer"
            @click="showContentModal = false"
            class="modal-close"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="selectedOrder">
          <div class="order-summary">
            <div class="summary-item">
              <span class="summary-label">Commande:</span>
              <span class="summary-value">{{ selectedOrder.numero_commande || `#${String(selectedOrder.id_commande).padStart(5, "0")}` }}</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Client:</span>
              <span class="summary-value">{{ getClientName(selectedOrder.id_client) }}</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Date:</span>
              <span class="summary-value">{{ formatFullDate(selectedOrder.date_commande) }}</span>
            </div>
          </div>

          <div class="articles-section">
            <h4 class="section-subtitle">Articles de la commande</h4>
            <div v-if="loadingArticles" class="loading-container">
              <div class="small-loader"></div>
              <p>Chargement des articles...</p>
            </div>
            <div v-else-if="orderArticles.length > 0" class="articles-list">
              <div v-for="(article, index) in orderArticles" :key="index" class="article-item">
                <div class="article-info">
                  <span class="article-name">{{
                    article.nom || `Article #${article.id_article}`
                  }}</span>
                  <span class="article-ref">Réf: {{ article.reference || "N/A" }}</span>
                  <span v-if="article.description" class="article-description">{{ article.description }}</span>
                </div>
                <div class="article-quantity">
                  <span class="quantity-label">Quantité:</span>
                  <span class="quantity-value">{{ article.quantite || 1 }}</span>
                </div>
              </div>
            </div>
            <div v-else class="empty-articles">
              <p>Aucun article trouvé pour cette commande</p>
            </div>
          </div>

          <div class="modal-footer">
            <div class="total-section">
              <span class="total-label">Total de la commande:</span>
              <span class="total-value">{{ formatCurrency(selectedOrder.montant_ttc && selectedOrder.montant_ttc !== "0.00" ? selectedOrder.montant_ttc : selectedOrder.montant_ht) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Confirmation terminer -->
    <div v-if="showConfirmModal" class="modal-overlay" @click="showConfirmModal = false">
      <div class="modal-content modal-confirm" @click.stop>
        <div class="modal-header">
          <h3>Confirmer la fin de préparation</h3>
          <button
            role="button"
            aria-label="Fermer"
            @click="showConfirmModal = false"
            class="modal-close"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="orderToComplete">
          <div class="confirm-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <p class="confirm-message">
            Êtes-vous sûr de vouloir marquer la commande
            <strong>{{ orderToComplete.numero_commande || `#${String(orderToComplete.id_commande).padStart(5, "0")}` }}</strong>
            comme terminée ?
          </p>
          <p class="confirm-submessage">
            Cette action changera le statut de la commande en "Expédiée".
          </p>
          <div class="modal-actions">
            <button
              role="button"
              aria-label="Annuler"
              class="modal-btn secondary"
              @click="showConfirmModal = false"
            >
              Annuler
            </button>
            <button
              role="button"
              aria-label="Confirmer"
              class="modal-btn primary"
              @click="confirmCompletePreparation"
            >
              Confirmer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted } from "vue";
  import { triggerToast } from "@/utils/toastHelper";

  const VITE_API_URL = import.meta.env.VITE_API_URL || "http://localhost/esgi/lamb/lamb/api/";

  export default {
    name: "PrepareView",
    metaInfo: {
      meta: [
        {
          name: "description",
          content:
            "Gérez les commandes à préparer de Fashion Chic, visualisez les détails, filtrez par client et statut, et accédez aux actions pour modifier ou imprimer.",
        },
      ],
    },
    setup() {
      // États
      const orders = ref([]);
      const clients = ref([]);
      const loading = ref(true);
      const error = ref(null);
      const searchQuery = ref("");
      const filterPriority = ref("");
      const filterStatus = ref("");

      // États pour les modales
      const showContentModal = ref(false);
      const showConfirmModal = ref(false);
      const selectedOrder = ref(null);
      const orderToComplete = ref(null);
      const orderArticles = ref([]);
      const loadingArticles = ref(false);

      // Fonction pour récupérer les données
      const fetchData = async () => {
        loading.value = true;
        error.value = null;

        try {
          // Récupérer les commandes
          const ordersResponse = await fetch(VITE_API_URL + "get_table?table=commande", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
              Authorization: `Bearer ${import.meta.env.VITE_API_KEY}`,
            },
            credentials: "include",
          });

          // Récupérer les clients
          const clientsResponse = await fetch(VITE_API_URL + "get_table?table=client", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
              Authorization: `Bearer ${import.meta.env.VITE_API_KEY}`,
            },
            credentials: "include",
          });

          if (!ordersResponse.ok || !clientsResponse.ok) {
            throw new Error("Erreur lors du chargement des commandes");
          }

          const ordersData = await ordersResponse.json();
          const clientsData = await clientsResponse.json();

          if (ordersData.success && ordersData.data) {
            // Récupérer toutes les commandes sans filtrage
            orders.value = ordersData.data;
            console.log("Nombre total de commandes:", orders.value.length);
          }

          if (clientsData.success && clientsData.data) {
            clients.value = clientsData.data;
          }
        } catch (err) {
          console.error("Erreur lors du chargement:", err);
          error.value = "Impossible de charger les commandes.";
        } finally {
          loading.value = false;
        }
      };

      // Computed properties
      const urgentCount = computed(() => {
        return orders.value.filter(order => order.statut !== 'expedie' && isUrgent(order)).length;
      });

      const totalPending = computed(() => {
        return orders.value.filter(order => order.statut === "attente" || order.statut === "1").length;
      });

      const inProgressCount = computed(() => {
        return orders.value.filter(order => order.statut === "preparation").length;
      });

      const shippedCount = computed(() => {
        return orders.value.filter(order => order.statut === "expedie").length;
      });

      const filteredOrders = computed(() => {
        let result = [...orders.value];

        // Filtre par priorité
        if (filterPriority.value === "urgent") {
          result = result.filter(order => isUrgent(order));
        } else if (filterPriority.value === "normal") {
          result = result.filter(order => !isUrgent(order));
        }

        // Filtre par statut
        if (filterStatus.value) {
          result = result.filter(order => order.statut === filterStatus.value);
        }

        // Filtre par recherche
        if (searchQuery.value) {
          const query = searchQuery.value.toLowerCase();
          result = result.filter(order => {
            const client = getClient(order.id_client);
            const clientName = client ? getClientName(order.id_client).toLowerCase() : "";
            const numeroCommande = order.numero_commande ? order.numero_commande.toLowerCase() : "";
            return (
              order.id_commande.toString().includes(query) ||
              numeroCommande.includes(query) ||
              clientName.includes(query) ||
              (order.montant_ht && order.montant_ht.toString().includes(query)) ||
              (order.montant_ttc && order.montant_ttc.toString().includes(query))
            );
          });
        }

        // Trier par urgence puis par date
        return result.sort((a, b) => {
          if (isUrgent(a) !== isUrgent(b)) {
            return isUrgent(a) ? -1 : 1;
          }
          return new Date(a.date_commande) - new Date(b.date_commande);
        });
      });

      // Fonctions utilitaires
      const getClient = clientId => {
        return clients.value.find(client => client.id_client === clientId);
      };

      const getClientName = clientId => {
        const client = getClient(clientId);
        if (!client) return `Client #${clientId}`;
        
        // Vérifier si on a prénom et nom, sinon utiliser raison_sociale
        if (client.prénom && client.nom) {
          return `${client.prénom} ${client.nom}`;
        } else if (client.raison_sociale) {
          return client.raison_sociale;
        } else if (client.nom) {
          return client.nom;
        }
        
        return `Client #${clientId}`;
      };

      const formatDate = dateString => {
        const date = new Date(dateString);
        const now = new Date();
        const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return "Aujourd'hui";
        if (diffDays === 1) return "Hier";
        if (diffDays <= 7) return `Il y a ${diffDays} jours`;

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
        return `${date.getDate()} ${months[date.getMonth()]}`;
      };

      const formatFullDate = dateString => {
        const date = new Date(dateString);
        const months = [
          "janvier",
          "février",
          "mars",
          "avril",
          "mai",
          "juin",
          "juillet",
          "août",
          "septembre",
          "octobre",
          "novembre",
          "décembre",
        ];
        return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
      };

      const formatCurrency = amount => {
        const numAmount = typeof amount === 'string' ? parseFloat(amount) : amount;
        return new Intl.NumberFormat("fr-FR", {
          style: "currency",
          currency: "EUR",
        }).format(numAmount || 0);
      };

      const isUrgent = order => {
        // Ne pas marquer comme urgent si la commande est déjà expédiée
        if (order.statut === 'expedie') return false;
        
        // Considérer une commande comme urgente si elle date de plus de 3 jours
        const orderDate = new Date(order.date_commande);
        const now = new Date();
        const diffDays = Math.floor((now - orderDate) / (1000 * 60 * 60 * 24));
        return diffDays > 3;
      };

      const getStatusClass = status => {
        const statusClasses = {
          attente: "badge-waiting",
          preparation: "badge-progress",
          expedie: "badge-shipped",
          "1": "badge-waiting", // Statut 1 = En attente
          "2": "badge-progress" // Statut 1 = En préparation
        };
        return statusClasses[status] || "badge-default";
      };

      const getStatusLabel = status => {
        const statusLabels = {
          attente: "En attente",
          preparation: "En préparation",
          expedie: "Expédiée",
          "1": "En attente", // Statut 1 = En attente
          "2": "En préparation" // Statut 2 = En préparation
        };
        return statusLabels[status] || status;
      };

      const startPreparation = async order => {
        try {
          // TODO: Implémenter l'appel API pour mettre à jour le statut
          // Pour l'instant, on met à jour localement
          const index = orders.value.findIndex(o => o.id_commande === order.id_commande);
          if (index !== -1) {
            orders.value[index].statut = "preparation";
            triggerToast("Commande en préparation", "success");
          }
        } catch (err) {
          console.error("Erreur lors de la mise à jour:", err);
          triggerToast("Erreur lors de la mise à jour", "error");
        }
      };

      const completePreparation = async order => {
        orderToComplete.value = order;
        showConfirmModal.value = true;
      };

      const confirmCompletePreparation = async () => {
        if (!orderToComplete.value) return;

        try {
          // TODO: Implémenter l'appel API pour mettre à jour le statut
          // Pour l'instant, on met à jour localement
          const index = orders.value.findIndex(
            o => o.id_commande === orderToComplete.value.id_commande,
          );
          if (index !== -1) {
            orders.value[index].statut = "expedie";
            triggerToast("Commande marquée comme expédiée", "success");
          }
        } catch (err) {
          console.error("Erreur lors de la mise à jour:", err);
          triggerToast("Erreur lors de la mise à jour", "error");
        }

        showConfirmModal.value = false;
        orderToComplete.value = null;
      };

      const viewOrderContent = async order => {
        selectedOrder.value = order;
        showContentModal.value = true;
        loadingArticles.value = true;
        orderArticles.value = [];

        try {
          // Récupérer les liaisons commande-lot
          const commandeLotResponse = await fetch(VITE_API_URL + "get_table?table=commande_lot", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
              Authorization: `Bearer ${import.meta.env.VITE_API_KEY}`,
            },
            credentials: "include",
          });

          // Récupérer les informations des lots
          const lotsResponse = await fetch(VITE_API_URL + "get_table?table=lot", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
              Authorization: `Bearer ${import.meta.env.VITE_API_KEY}`,
            },
            credentials: "include",
          });

          if (!commandeLotResponse.ok || !lotsResponse.ok) {
            throw new Error("Erreur lors du chargement des articles");
          }

          const commandeLotData = await commandeLotResponse.json();
          const lotsData = await lotsResponse.json();

          if (commandeLotData.success && lotsData.success) {
            // Filtrer les lots de cette commande
            const orderLots = commandeLotData.data.filter(
              cl => cl.id_commande === order.id_commande
            );

            // Créer la liste des articles avec leurs quantités
            orderArticles.value = orderLots.map(orderLot => {
              const lot = lotsData.data.find(l => l.id_lot === orderLot.id_lot);
              return {
                id_article: orderLot.id_lot,
                nom: lot ? lot.nom : `Lot #${orderLot.id_lot}`,
                reference: lot ? `LOT-${orderLot.id_lot}` : "N/A",
                quantite: orderLot.quantite,
                description: lot ? lot.description : ""
              };
            });
          }
        } catch (err) {
          console.error("Erreur lors du chargement des articles:", err);
          // Si erreur, afficher un message
          orderArticles.value = [];
        } finally {
          loadingArticles.value = false;
        }
      };

      // Charger les données au montage
      onMounted(() => {
        fetchData();
      });

      return {
        orders,
        loading,
        error,
        searchQuery,
        filterPriority,
        filterStatus,
        showContentModal,
        showConfirmModal,
        selectedOrder,
        orderToComplete,
        orderArticles,
        loadingArticles,
        urgentCount,
        totalPending,
        inProgressCount,
        shippedCount,
        filteredOrders,
        fetchData,
        getClientName,
        formatDate,
        formatFullDate,
        formatCurrency,
        isUrgent,
        getStatusClass,
        getStatusLabel,
        startPreparation,
        completePreparation,
        confirmCompletePreparation,
        viewOrderContent,
      };
    },
  };
</script>

<style scoped>
  .prepare-page {
    padding: 0;
    font-family: "Inter", sans-serif;
  }

  /* EN-TÊTE */
  .page-header {
    margin-bottom: 2rem;
  }

  .page-title {
    font-size: 24px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
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
    border: 1px solid #f1f5f9;
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

  .urgent-icon {
    background: #fef2f2;
    color: #dc2626;
  }

  .normal-icon {
    background: #eff6ff;
    color: #2563eb;
  }

  .progress-icon {
    background: #fef3c7;
    color: #d97706;
  }

  .shipped-icon {
    background: #d1fae5;
    color: #059669;
  }

  .kpi-content {
    flex: 1;
  }

  .kpi-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
    margin: 0 0 4px 0;
  }

  .kpi-value {
    font-size: 28px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1;
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
    min-width: 380px;
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
    color: #64748b;
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
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
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

  /* LISTE DES COMMANDES */
  .orders-section {
    margin-bottom: 2rem;
  }

  .orders-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
    overflow: hidden;
  }

  .orders-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .orders-title {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
  }

  .orders-count {
    font-size: 14px;
    color: #64748b;
  }

  .orders-list {
    max-height: 600px;
    overflow-y: auto;
  }

  .order-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
    gap: 2rem;
  }

  .order-item:hover {
    background: #f8fafc;
  }

  .order-item.urgent {
    border-left: 4px solid #dc2626;
    background: #fef2f2;
  }

  .order-item:last-child {
    border-bottom: none;
  }

  .order-main {
    flex: 1;
    display: flex;
    align-items: center;
  }

  .order-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
  }

  .order-right {
    display: flex;
    align-items: center;
    gap: 2rem;
  }

  .order-id {
    font-weight: 600;
    color: #0f172a;
    font-size: 13px;
    white-space: nowrap;
  }

  .order-client {
    font-weight: 500;
    color: black;
    font-size: 14px;
  }

  .order-date {
    font-size: 13px;
    color: #64748b;
  }

  .order-amount {
    font-weight: 600;
    color: #0f172a;
    font-size: 14px;
  }

  .order-badges {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 255px;
  }

  .badge {
    padding: 6px 8px;
    border-radius: 24px;
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    letter-spacing: -0.01em;
    min-width: 120px;
    width: 120px;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .badge-urgent {
    background: #dc2626;
    color: white;
  }

  .badge-waiting {
    background: #fef3c7;
    color: #92400e;
  }

  .badge-progress {
    background: #dbeafe;
    color: #1e40af;
  }

  .badge-shipped {
    background: #d1fae5;
    color: #047857;
  }

  .badge-default {
    background: #f1f5f9;
    color: #64748b;
  }

  .order-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
    padding-left: 1rem;
    border-left: 1px solid #e2e8f0;
    margin-left: 1rem;
  }

  .action-btn {
    border: none;
    border-radius: 8px;
    padding: 0.625rem 1rem;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 120px;
    width: 120px;
    text-align: center;
  }

  .action-btn.primary {
    background: #00b8d4;
    color: white;
  }

  .action-btn.primary:hover {
    background: #0891a6;
  }

  .action-btn.success {
    background: #059669;
    color: white;
  }

  .action-btn.success:hover {
    background: #047857;
  }

  .action-btn.completed {
    background: #e2e8f0;
    color: #64748b;
    cursor: not-allowed;
  }

  .action-btn.completed:hover {
    background: #e2e8f0;
  }

  .action-btn.icon-btn {
    background: white;
    border: 1px solid #e2e8f0;
    color: #64748b;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    min-width: 36px;
  }

  .action-btn.icon-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: black;
  }

  .action-btn.icon-btn svg {
    width: 18px;
    height: 18px;
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
    max-width: 600px;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  }

  .modal-confirm {
    max-width: 450px;
  }

  .modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .modal-header h2,
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
    overflow-y: auto;
    max-height: calc(90vh - 120px);
  }

  /* MODAL CONTENU */
  .order-summary {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
  }

  .summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
  }

  .summary-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
  }

  .summary-value {
    font-size: 14px;
    color: #0f172a;
    font-weight: 600;
  }

  .articles-section {
    margin-bottom: 1.5rem;
  }

  .section-subtitle {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .small-loader {
    width: 24px;
    height: 24px;
    border: 2px solid #f1f5f9;
    border-top-color: #00b8d4;
    border-radius: 50%;
    margin: 0 auto 0.5rem;
    animation: spin 1s linear infinite;
  }

  .articles-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .article-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    gap: 1rem;
  }

  .article-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    flex: 1;
  }

  .article-name {
    font-size: 14px;
    font-weight: 500;
    color: #0f172a;
  }

  .article-ref {
    font-size: 12px;
    color: #64748b;
  }

  .article-description {
    font-size: 12px;
    color: #94a3b8;
    font-style: italic;
  }

  .article-quantity {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
    padding-top: 0.25rem;
  }

  .quantity-label {
    font-size: 12px;
    color: #64748b;
  }

  .quantity-value {
    font-size: 16px;
    font-weight: 600;
    color: #00b8d4;
  }

  .empty-articles {
    text-align: center;
    padding: 2rem;
    color: #64748b;
    font-style: italic;
  }

  .modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
  }

  .total-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .total-label {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
  }

  .total-value {
    font-size: 20px;
    font-weight: 700;
    color: #00b8d4;
  }

  /* MODAL CONFIRMATION */
  .confirm-icon {
    display: flex;
    justify-content: center;
    margin-bottom: 1.5rem;
  }

  .confirm-icon svg {
    width: 64px;
    height: 64px;
    color: #059669;
    stroke-width: 1.5;
  }

  .confirm-message {
    text-align: center;
    font-size: 16px;
    color: #0f172a;
    margin-bottom: 0.5rem;
  }

  .confirm-message strong {
    color: #00b8d4;
  }

  .confirm-submessage {
    text-align: center;
    font-size: 14px;
    color: #64748b;
    margin-bottom: 2rem;
  }

  .modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
  }

  .modal-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    min-width: 100px;
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
  @media (max-width: 1200px) {
    .kpi-section {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 1024px) {
    .filter-group {
      flex-direction: column;
      align-items: stretch;
    }

    .filter-select,
    .search-container {
      width: 100%;
      max-width: none;
      justify-content: center;
    }
  }

  @media (max-width: 768px) {
    .kpi-section {
      grid-template-columns: 1fr;
    }

    .search-container {
      min-width: auto;
    }

    .order-item {
      flex-direction: column;
      align-items: stretch;
      gap: 1rem;
    }

    .order-main {
      width: 100%;
    }

    .order-info {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5rem;
    }

    .order-right {
      width: 100%;
      flex-direction: column;
      align-items: stretch;
      gap: 1rem;
    }

    .order-badges {
      justify-content: flex-start;
    }

    .order-actions {
      justify-content: flex-end;
      padding-left: 0;
      border-left: none;
      margin-left: 0;
      padding-top: 0.75rem;
      border-top: 1px solid #e2e8f0;
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