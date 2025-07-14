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
          <p class="stat-label">Livraisons à réceptionner</p>
          <p class="stat-value">3</p>
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
          <p class="stat-label">Réceptionnées ce mois</p>
          <p class="stat-value">12</p>
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
          <p class="stat-label">Problèmes signalés</p>
          <p class="stat-value">1</p>
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
            placeholder="Rechercher par ID, référence ou fournisseur"
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
            <option value="Durand SA">Durand SA</option>
            <option value="Bernard FR">Bernard FR</option>
            <option value="Martin SA">Martin SA</option>
          </select>
          <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6" />
          </svg>
        </div>

        <div class="filter-wrapper">
          <label for="status-filter" class="filter-label">Statut :</label>
          <select id="status-filter" v-model="filters.status" class="filter-select">
            <option value="">Tous les statuts</option>
            <option value="En attente">En attente</option>
            <option value="Problème">Problème</option>
            <option value="Réceptionnée">Réceptionnée</option>
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

    <!-- Livraisons à réceptionner -->
    <div class="deliveries-section">
      <div class="section-card">
        <div class="section-header">
          <h3 class="section-title">Livraisons à réceptionner</h3>
          <div class="table-stats">
            <span class="stat-item">
              <span class="stat-label">En attente :</span>
              <span class="stat-value">{{ pendingCount }}</span>
            </span>
            <span class="stat-item">
              <span class="stat-label">Problèmes :</span>
              <span class="stat-value low">{{ problemCount }}</span>
            </span>
          </div>
        </div>

        <div class="table-container">
          <table class="deliveries-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Fournisseur</th>
                <th>Date prévue</th>
                <th>Commande liée</th>
                <th>Nb. articles</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="delivery in filteredDeliveries" :key="delivery.id">
                <td class="delivery-id">{{ delivery.id }}</td>
                <td class="supplier-info">
                  <div class="supplier-profile">
                    <div class="supplier-avatar-container">
                      <div class="supplier-avatar">
                        {{ getSupplierInitials(delivery.supplier) }}
                      </div>
                    </div>
                    <div class="supplier-details">
                      <p class="supplier-name">{{ delivery.supplier }}</p>
                    </div>
                  </div>
                </td>
                <td class="delivery-date">{{ delivery.expectedDate }}</td>
                <td class="order-reference">{{ delivery.orderRef }}</td>
                <td class="articles-count">{{ delivery.articlesCount }}</td>
                <td>
                  <span class="status-badge" :class="getStatusClass(delivery.status)">
                    {{ delivery.status }}
                  </span>
                </td>
                <td class="actions">
                  <button
                    role="button"
                    aria-label="Recevoir la livraison"
                    v-if="delivery.status === 'En attente'"
                    class="action-btn success"
                    @click="receiveDelivery(delivery.id)"
                  >
                    <svg viewBox="0 0 448 512">
                      <path
                        d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"
                        fill="currentColor"
                      />
                    </svg>
                  </button>
                  <button
                    role="button"
                    aria-label="Signaler un problème"
                    v-if="delivery.status === 'Problème'"
                    class="action-btn danger"
                    @click="reportProblem(delivery.id)"
                  >
                    <svg viewBox="0 0 512 512">
                      <path
                        fill="currentColor"
                        d="M34.5 420.4c-1.6 2.8-2.5 6-2.5 9.3c0 10.2 8.2 18.4 18.4 18.4l411.2 0c10.2 0 18.4-8.2 18.4-18.4c0-3.3-.9-6.4-2.5-9.3L276.5 75.8C272.2 68.5 264.4 64 256 64s-16.2 4.5-20.5 11.8L34.5 420.4zM6.9 404.2l201-344.6C217.9 42.5 236.2 32 256 32s38.1 10.5 48.1 27.6l201 344.6c4.5 7.7 6.9 16.5 6.9 25.4c0 27.8-22.6 50.4-50.4 50.4L50.4 480C22.6 480 0 457.4 0 429.6c0-8.9 2.4-17.7 6.9-25.4zM256 160c8.8 0 16 7.2 16 16l0 128c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zM232 384a24 24 0 1 1 48 0 24 24 0 1 1 -48 0z"
                      />
                    </svg>
                  </button>
                  <button
                    role="button"
                    aria-label="Voir détails"
                    class="action-btn secondary"
                    @click="viewDetails(delivery.id)"
                    title="Voir détails"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
            <tr v-if="filteredDeliveries.length === 0">
              <td colspan="100%" class="empty-message">Aucune livraison trouvée</td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <!-- Historique des livraisons -->
    <div class="history-section">
      <div class="section-card">
        <div class="section-header">
          <h3 class="section-title">Historique des livraisons</h3>
          <div class="table-stats">
            <span class="stat-item">
              <span class="stat-label">Total :</span>
              <span class="stat-value">{{ deliveryHistory.length }}</span>
            </span>
          </div>
        </div>

        <div class="table-container">
          <table class="history-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Fournisseur</th>
                <th>Date réception</th>
                <th>Commande liée</th>
                <th>Réceptionnée par</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in deliveryHistory" :key="item.id">
                <td class="delivery-id">{{ item.id }}</td>
                <td>{{ item.supplier }}</td>
                <td class="delivery-date">{{ item.receptionDate }}</td>
                <td class="order-reference">{{ item.orderRef }}</td>
                <td>{{ item.receivedBy }}</td>
                <td>
                  <span class="status-badge status-received">
                    {{ item.status }}
                  </span>
                </td>
                <td class="actions">
                  <button
                    role="button"
                    aria-label="Voir détails"
                    class="action-btn secondary"
                    @click="viewDeliveryDetails(item.id)"
                    title="Voir détails"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Nouvelle livraison (placeholder) -->
    <div v-if="showNewDeliveryModal" class="modal-overlay" @click="showNewDeliveryModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Enregistrer une livraison</h3>
          <button
            role="button"
            aria-label="Fermer"
            @click="showNewDeliveryModal = false"
            class="modal-close"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <p>Fonctionnalité en cours de développement</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed } from "vue";
  import { triggerToast } from "@/utils/toastHelper";
  import { VITE_API_URL } from "@/constants/constants.js";

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
      const searchQuery = ref("");
      const showNewDeliveryModal = ref(false);

      const filters = ref({
        supplier: "",
        status: "",
      });

      const deliveries = ref([
        {
          id: "5003",
          supplier: "Durand SA",
          expectedDate: "6 mai 2023",
          orderRef: "CMD-1205",
          articlesCount: 8,
          status: "En attente",
        },
        {
          id: "5002",
          supplier: "Bernard FR",
          expectedDate: "6 mai 2023",
          orderRef: "CMD-1273",
          articlesCount: 5,
          status: "En attente",
        },
        {
          id: "5001",
          supplier: "Bernard FR",
          expectedDate: "3 mai 2023",
          orderRef: "CMD-1254",
          articlesCount: 3,
          status: "Problème",
        },
      ]);

      const deliveryHistory = ref([
        {
          id: "4998",
          supplier: "Martin SA",
          receptionDate: "2 mai 2023",
          orderRef: "CMD-1201",
          receivedBy: "Jean Dupont",
          status: "Réceptionnée",
        },
        {
          id: "4997",
          supplier: "Durand SA",
          receptionDate: "1 mai 2023",
          orderRef: "CMD-1198",
          receivedBy: "Marie Martin",
          status: "Réceptionnée",
        },
        {
          id: "4996",
          supplier: "Bernard FR",
          receptionDate: "29 avr 2023",
          orderRef: "CMD-1195",
          receivedBy: "Pierre Leroy",
          status: "Réceptionnée",
        },
      ]);

      const filteredDeliveries = computed(() => {
        let result = deliveries.value;

        if (filters.value.supplier) {
          result = result.filter(delivery => delivery.supplier === filters.value.supplier);
        }

        if (filters.value.status) {
          result = result.filter(delivery => delivery.status === filters.value.status);
        }

        // Filtre par recherche
        if (searchQuery.value) {
          if (searchQuery.value.startsWith("#")) {
            // Si la recherche commence par #, on filtre par id
            const id = searchQuery.value.slice(1).toLowerCase();
            result = result.filter(supplier => supplier.id.toString().includes(id));
          } else {
            const query = searchQuery.value.toLowerCase();
            result = result.filter(supplier => {
              const supplierName = supplier.supplier.toLowerCase();
              const expectedDate = supplier.expectedDate.toLowerCase();
              const orderRef = supplier.orderRef.toLowerCase();
              const status = supplier.status.toLowerCase();
              return (
                supplierName.includes(query) ||
                expectedDate.includes(query) ||
                orderRef.includes(query) ||
                status.includes(query)
              );
            });
          }
        }

        return result;
      });

      const pendingCount = computed(() => {
        return deliveries.value.filter(d => d.status === "En attente").length;
      });

      const problemCount = computed(() => {
        return deliveries.value.filter(d => d.status === "Problème").length;
      });

      const getSupplierInitials = name => {
        return name
          .split(" ")
          .map(n => n[0])
          .join("")
          .toUpperCase()
          .substring(0, 2);
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

      const getStatusClass = status => {
        const statusClasses = {
          "En attente": "status-pending",
          Problème: "status-problem",
          Réceptionnée: "status-received",
        };
        return statusClasses[status] || "status-default";
      };

      const getStatusLabel = status => {
        const statusLabels = {
          probleme: "Problème",
          attente: "En attente",
          receptionnee: "Réceptionnée",
        };
        return statusLabels[status] || status;
      };

      const exportDelivery = () => {
        // Fonction d'export
        const data = [
          ["ID", "Fournisseur", "Date prévue", "Commande liée", "Nb. articles", "Statut"],
          ...filteredDeliveries.value.map(d => [
            d.id,
            d.supplier,
            formatDate(d.expectedDate),
            d.orderRef,
            d.receivedBy,
            getStatusLabel(d.status),
          ]),
        ];

        if (!data[1]) {
          triggerToast("Aucune livraison à exporter !", "error");
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

      const receiveDelivery = deliveryId => {
        console.log("Réceptionner livraison:", deliveryId);
      };

      const reportProblem = deliveryId => {
        console.log("Signaler problème:", deliveryId);
      };

      const viewDetails = deliveryId => {
        console.log("Voir détails:", deliveryId);
      };

      const viewDeliveryDetails = deliveryId => {
        console.log("Voir détails livraison:", deliveryId);
      };

      return {
        searchQuery,
        showNewDeliveryModal,
        filters,
        deliveries,
        deliveryHistory,
        filteredDeliveries,
        pendingCount,
        problemCount,
        getSupplierInitials,
        getStatusClass,
        receiveDelivery,
        reportProblem,
        exportDelivery,
        viewDetails,
        viewDeliveryDetails,
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
    box-shadow: 0 2px 8px rgba(85, 0, 255, 0.3);
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
    min-width: 350px;
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

  /* SECTIONS */
  .deliveries-section,
  .history-section {
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

  .deliveries-table,
  .history-table {
    width: 100%;
    min-width: max-content;
    text-align: center;
  }

  .deliveries-table th,
  .history-table th {
    background: #f8fafc;
    padding: 1rem;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #e2e8f0;
  }

  .deliveries-table td,
  .history-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: black;
    vertical-align: middle;
  }

  .deliveries-table tbody tr:hover,
  .history-table tbody tr:hover {
    background: #f8fafc;
  }

  .deliveries-table tbody tr:last-child td,
  .history-table tbody tr:last-child td {
    border-bottom: none;
  }

  .delivery-id {
    color: #0f172a;
  }

  .supplier-profile {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .supplier-avatar-container {
    display: flex;
    width: 40%;
    justify-content: flex-end;
  }

  .supplier-avatar {
    width: 32px;
    height: 32px;
    background: #0062ff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 12px;
    flex-shrink: 0;
  }

  .supplier-avatar.large {
    width: 64px;
    height: 64px;
    font-size: 20px;
  }

  .supplier-details {
    width: 60%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }

  .delivery-date {
    color: #64748b;
    font-size: 13px;
  }

  .order-reference {
    font-weight: 500;
    color: #0062ff;
    cursor: pointer;
  }

  .order-reference:hover {
    text-decoration: underline;
  }

  .articles-count {
    font-weight: 600;
    color: #0f172a;
    text-align: center;
  }

  .empty-message {
    text-align: center;
    color: #64748b;
    font-style: italic;
    padding: 1rem;
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

  .status-problem {
    background: #fef2f2;
    color: #ab0000;
  }

  .status-received {
    background: #d1fae5;
    color: #047857;
  }

  /* ACTIONS */
  .actions {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
  }

  .action-btn {
    border: solid 1px;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    padding: 6px;
    transition: all 0.2s ease;
  }

  .action-btn.success {
    background: none;
    border-color: #10b981;
    color: #10b981;
    transition: all 0.2s ease;
  }

  .action-btn.success:hover {
    background: #10b981;
    color: white;
    transform: translateY(-2px);
  }

  .action-btn.success:active {
    background: #059669;
    border-color: #059669;
    color: white;
  }

  .action-btn.danger {
    background: none;
    border-color: #dc2626;
    color: #dc2626;
    transition: all 0.2s ease;
  }

  .action-btn.danger:hover {
    background: #dc2626;
    color: white;
    transform: translateY(-2px);
  }

  .action-btn.danger:active {
    background: #b91c1c;
    border-color: #b91c1c;
    color: white;
  }

  .action-btn.secondary {
    background: none;
    border-color: #e2e8f0;
    color: #64748b;
  }

  .action-btn.secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: black;
  }

  .action-btn svg {
    width: 16px;
    height: 16px;
    stroke-width: 1.5;
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
    text-align: center;
    color: #64748b;
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
      justify-content: center;
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

    .section-stats {
      width: 100%;
      justify-content: space-between;
    }

    .deliveries-table,
    .history-table {
      font-size: 12px;
    }

    .search-input,
    .filter-select,
    .search-button {
      width: 100%;
    }

    .actions {
      gap: 0.5rem;
    }

    .action-btn {
      padding: 0.375rem 0.75rem;
      font-size: 11px;
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
