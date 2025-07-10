<template>
  <div class="agenda-page">
    <!-- En-tête -->
    <div class="page-header">
      <h2 class="page-title">Agenda partagé</h2>
    </div>

    <!-- Cartes KPI -->
    <div class="kpi-section">
      <div class="kpi-card kpi-today">
        <div class="kpi-icon today-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
            <circle cx="12" cy="16" r="1" fill="currentColor" />
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Aujourd'hui</p>
          <p class="kpi-value">{{ todayCount }}</p>
          <p class="kpi-sublabel">
            {{
              todayCount === 0 ? "Aucun événement" : todayCount === 1 ? "événement" : "événements"
            }}
          </p>
        </div>
      </div>

      <div class="kpi-card kpi-deliveries">
        <div class="kpi-icon deliveries-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="1" y="3" width="15" height="13" />
            <polygon points="16,3 19,7 19,13 16,13" />
            <circle cx="5.5" cy="18.5" r="2.5" />
            <circle cx="18.5" cy="18.5" r="2.5" />
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Livraisons ce mois</p>
          <p class="kpi-value">{{ monthDeliveries }}</p>
          <p class="kpi-sublabel">{{ pendingDeliveries }} en attente</p>
        </div>
      </div>

      <div class="kpi-card kpi-orders">
        <div class="kpi-icon orders-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
            <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
            <path d="M16 10H8M16 14H8" />
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Commandes ce mois</p>
          <p class="kpi-value">{{ monthOrders }}</p>
          <p class="kpi-sublabel">{{ pendingOrders }} à traiter</p>
        </div>
      </div>
    </div>

    <!-- Navigation du calendrier -->
    <div class="calendar-navigation">
      <div class="nav-group">
        <button
          role="button"
          aria-label="Mois précédent"
          class="nav-btn"
          @click="previousMonth"
          title="Mois précédent"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="15 18 9 12 15 6" />
          </svg>
        </button>
        <h2 class="current-month">{{ currentMonthLabel }}</h2>
        <button
          role="button"
          aria-label="Mois suivant"
          class="nav-btn"
          @click="nextMonth"
          title="Mois suivant"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="9 18 15 12 9 6" />
          </svg>
        </button>
      </div>
      <div class="nav-actions">
        <button role="button" aria-label="Aujourd'hui" class="today-btn" @click="goToToday">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" />
            <polyline points="12 6 12 12 16 14" />
          </svg>
          Aujourd'hui
        </button>
        <button
          role="button"
          aria-label="Rafraîchir les données"
          class="refresh-btn"
          @click="fetchData"
          :disabled="loading"
        >
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            :class="{ 'animate-spin': loading }"
          >
            <path
              d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Calendrier -->
    <div class="calendar-section">
      <div class="calendar-card">
        <div v-if="loading" class="loading-container">
          <div class="loader"></div>
          <p>Chargement de l'agenda...</p>
        </div>

        <div v-else-if="error" class="error-container">
          <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
          <p class="error-message">{{ error }}</p>
          <button role="button" aria-label="Réessayer" @click="fetchData" class="retry-button">
            Réessayer
          </button>
        </div>

        <div v-else class="calendar-grid">
          <!-- En-têtes des jours -->
          <div class="weekdays">
            <div v-for="day in weekDays" :key="day" class="weekday">
              {{ day }}
            </div>
          </div>

          <!-- Grille des jours -->
          <div class="days-grid">
            <div
              v-for="day in calendarDays"
              :key="day.date"
              class="day-cell"
              :class="{
                'other-month': day.otherMonth,
                'is-today': day.isToday,
                'has-events': day.events.length > 0,
                selected: selectedDate && day.date === selectedDate,
                'is-weekend': day.isWeekend,
                'is-past': day.isPast && !day.isToday,
              }"
              @click="selectDay(day)"
            >
              <div class="day-header">
                <span class="day-number">{{ day.number }}</span>
                <span v-if="day.isToday" class="today-label">Aujourd'hui</span>
              </div>

              <div v-if="day.events.length > 0" class="day-content">
                <div class="event-summary">
                  <div v-if="day.deliveries > 0" class="summary-item delivery">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <rect x="1" y="3" width="15" height="13" />
                      <polygon points="16,3 19,7 19,13 16,13" />
                    </svg>
                    <span>{{ day.deliveries }}</span>
                  </div>
                  <div v-if="day.orders > 0" class="summary-item order">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M20 6L9 17l-5-5" />
                    </svg>
                    <span>{{ day.orders }}</span>
                  </div>
                </div>
                <div v-if="day.events.length > 2" class="more-indicator">
                  +{{ day.events.length - 2 }}
                </div>
              </div>

              <div v-else-if="!day.otherMonth" class="day-empty">
                <span class="empty-dot"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Détails du jour sélectionné -->
    <transition name="slide-up">
      <div v-if="selectedDate" class="day-details-section">
        <div class="day-details-card">
          <div class="details-header">
            <div class="header-left">
              <h2 class="details-title">{{ formatSelectedDate }}</h2>
              <p class="details-subtitle">
                {{
                  selectedDayEvents.length === 0
                    ? "Aucun événement"
                    : selectedDayEvents.length === 1
                      ? "1 événement"
                      : selectedDayEvents.length + " événements"
                }}
              </p>
            </div>
            <button
              role="button"
              aria-label="Fermer"
              class="close-btn"
              @click="selectedDate = null"
              title="Fermer"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>
          </div>

          <div class="details-body">
            <!-- Si pas d'événements -->
            <div v-if="selectedDayEvents.length === 0" class="empty-state">
              <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                  <line x1="16" y1="2" x2="16" y2="6" />
                  <line x1="8" y1="2" x2="8" y2="6" />
                  <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
              </div>
              <p class="empty-text">Aucun événement prévu ce jour</p>
            </div>

            <!-- Liste des événements -->
            <div v-else class="events-list">
              <div class="events-section" v-if="deliveryEvents.length > 0">
                <h4 class="section-title">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="1" y="3" width="15" height="13" />
                    <polygon points="16,3 19,7 19,13 16,13" />
                  </svg>
                  Livraisons ({{ deliveryEvents.length }})
                </h4>
                <div class="section-events">
                  <div
                    v-for="event in deliveryEvents"
                    :key="event.id"
                    class="event-item delivery"
                    :class="{ completed: event.completed }"
                  >
                    <div class="event-icon">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="1" y="3" width="15" height="13" />
                        <polygon points="16,3 19,7 19,13 16,13" />
                        <circle cx="5.5" cy="18.5" r="2.5" />
                        <circle cx="18.5" cy="18.5" r="2.5" />
                      </svg>
                    </div>

                    <div class="event-content">
                      <div class="event-header">
                        <span class="event-id">{{ event.numero_livraison || "#" + event.id }}</span>
                        <span v-if="event.completed" class="event-badge completed">{{
                          getDeliveryStatusLabel(event.statut)
                        }}</span>
                        <span v-else class="event-badge pending">{{
                          getDeliveryStatusLabel(event.statut)
                        }}</span>
                      </div>
                      <div class="event-main">
                        <p class="event-partner">{{ event.partner }}</p>
                        <p class="event-info">Bon: {{ event.numero_bon_livraison || "N/A" }}</p>
                        <p v-if="event.notes" class="event-notes">
                          {{ event.notes }}
                        </p>
                      </div>
                    </div>

                    <div class="event-actions">
                      <button
                        role="button"
                        aria-label="Voir détails"
                        class="action-btn"
                        @click="viewDeliveryDetails(event)"
                        title="Voir détails"
                      >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                          <circle cx="12" cy="12" r="3" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="events-section" v-if="orderEvents.length > 0">
                <h4 class="section-title">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path
                      d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"
                    />
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                  </svg>
                  Commandes ({{ orderEvents.length }})
                </h4>
                <div class="section-events">
                  <div
                    v-for="event in orderEvents"
                    :key="event.id"
                    class="event-item order"
                    :class="{ completed: event.completed }"
                  >
                    <div class="event-icon">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M20 6L9 17l-5-5" />
                      </svg>
                    </div>

                    <div class="event-content">
                      <div class="event-header">
                        <span class="event-id">#{{ String(event.id).padStart(5, "0") }}</span>
                        <span class="event-badge" :class="getOrderStatusClass(event.statut)">{{
                          getOrderStatusLabel(event.statut)
                        }}</span>
                      </div>
                      <div class="event-main">
                        <p class="event-partner">{{ event.partner }}</p>
                        <p class="event-info">
                          {{ formatCurrency(event.amount) }}
                        </p>
                      </div>
                    </div>

                    <div class="event-actions">
                      <button
                        role="button"
                        aria-label="Voir détails"
                        class="action-btn"
                        @click="viewOrderDetails(event)"
                        title="Voir détails"
                      >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                          <circle cx="12" cy="12" r="3" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
  import { ref, computed, onMounted } from "vue";
  import { useRouter } from "vue-router";
  import { VITE_API_URL } from "@/constants/constants.js";

  export default {
    name: "AgendaView",
    metaInfo: {
      meta: [
        {
          name: "description",
          content: "L'agenda partagé de Fashion Chic pour suivre les livraisons et commandes.",
        },
      ],
    },
    setup() {
      const router = useRouter();

      // États
      const events = ref([]);
      const clients = ref([]);
      const fournisseurs = ref([]);
      const loading = ref(true);
      const error = ref(null);
      const currentDate = ref(new Date());
      const selectedDate = ref(null);

      const weekDays = ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"];

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

          // Récupérer les commandes
          const commandesResponse = await fetch(VITE_API_URL + "get_table?table=commande", {
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

          // Récupérer les fournisseurs
          const fournisseursResponse = await fetch(VITE_API_URL + "get_table?table=fournisseur", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          if (
            !livraisonsResponse.ok ||
            !commandesResponse.ok ||
            !clientsResponse.ok ||
            !fournisseursResponse.ok
          ) {
            throw new Error("Erreur lors du chargement des données");
          }

          const livraisonsData = await livraisonsResponse.json();
          const commandesData = await commandesResponse.json();
          const clientsData = await clientsResponse.json();
          const fournisseursData = await fournisseursResponse.json();

          // Stocker les clients et fournisseurs
          if (clientsData.success && clientsData.data) {
            clients.value = clientsData.data;
          }

          if (fournisseursData.success && fournisseursData.data) {
            fournisseurs.value = fournisseursData.data;
          }

          // Traiter les données
          const allEvents = [];

          // Ajouter les livraisons
          if (livraisonsData.success && livraisonsData.data) {
            livraisonsData.data.forEach(livraison => {
              const fournisseur = fournisseurs.value.find(
                f => f.id_fournisseur === livraison.id_fournisseur,
              );
              allEvents.push({
                id: livraison.id_livraison,
                type: "delivery",
                partner: fournisseur
                  ? fournisseur.nom_entreprise
                  : `Fournisseur #${livraison.id_fournisseur}`,
                date: new Date(livraison.date_livraison),
                completed: livraison.statut === "completee",
                statut: livraison.statut,
                numero_livraison: livraison.numero_livraison,
                numero_bon_livraison: livraison.numero_bon_livraison,
                notes: livraison.notes,
                rawData: livraison,
              });
            });
          }

          // Ajouter les commandes
          if (commandesData.success && commandesData.data) {
            commandesData.data.forEach(commande => {
              const client = clients.value.find(c => c.id_client === commande.id_client);
              allEvents.push({
                id: commande.id_commande,
                type: "order",
                partner: client
                  ? `${client.prénom} ${client.nom}`
                  : `Client #${commande.id_client}`,
                date: new Date(commande.date_commande),
                amount: commande.montant_total,
                completed: commande.statut === "expedie",
                statut: commande.statut,
                rawData: commande,
              });
            });
          }

          events.value = allEvents;
        } catch (err) {
          console.error("Erreur lors du chargement:", err);
          error.value = "Impossible de charger l'agenda. Veuillez réessayer.";
        } finally {
          loading.value = false;
        }
      };

      // Computed properties
      const currentMonthLabel = computed(() => {
        const months = [
          "Janvier",
          "Février",
          "Mars",
          "Avril",
          "Mai",
          "Juin",
          "Juillet",
          "Août",
          "Septembre",
          "Octobre",
          "Novembre",
          "Décembre",
        ];
        return `${months[currentDate.value.getMonth()]} ${currentDate.value.getFullYear()}`;
      });

      const calendarDays = computed(() => {
        const year = currentDate.value.getFullYear();
        const month = currentDate.value.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();

        // Ajuster pour commencer par lundi
        let startingDayOfWeek = firstDay.getDay() - 1;
        if (startingDayOfWeek === -1) startingDayOfWeek = 6;

        const days = [];

        // Jours du mois précédent
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        for (let i = startingDayOfWeek - 1; i >= 0; i--) {
          const date = new Date(year, month - 1, prevMonthLastDay - i);
          days.push(createDayObject(date, true));
        }

        // Jours du mois actuel
        for (let i = 1; i <= daysInMonth; i++) {
          const date = new Date(year, month, i);
          days.push(createDayObject(date, false));
        }

        return days;
      });

      const createDayObject = (date, otherMonth) => {
        const dateKey = formatDateKey(date);
        const dayEvents = events.value.filter(e => formatDateKey(e.date) === dateKey);
        const dayOfWeek = date.getDay();

        return {
          date: dateKey,
          number: date.getDate(),
          otherMonth,
          isToday: isToday(date),
          isWeekend: dayOfWeek === 0 || dayOfWeek === 6,
          isPast: date < new Date().setHours(0, 0, 0, 0),
          events: dayEvents,
          deliveries: dayEvents.filter(e => e.type === "delivery").length,
          orders: dayEvents.filter(e => e.type === "order").length,
        };
      };

      const selectedDayEvents = computed(() => {
        if (!selectedDate.value) return [];
        return events.value.filter(e => formatDateKey(e.date) === selectedDate.value);
      });

      const deliveryEvents = computed(() => {
        return selectedDayEvents.value.filter(e => e.type === "delivery");
      });

      const orderEvents = computed(() => {
        return selectedDayEvents.value.filter(e => e.type === "order");
      });

      const formatSelectedDate = computed(() => {
        if (!selectedDate.value) return "";
        const [year, month, day] = selectedDate.value.split("-").map(Number);
        const date = new Date(year, month - 1, day);
        const days = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
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
        return `${days[date.getDay()]} ${day} ${months[month - 1]} ${year}`;
      });

      const todayCount = computed(() => {
        const today = formatDateKey(new Date());
        return events.value.filter(e => formatDateKey(e.date) === today).length;
      });

      const monthDeliveries = computed(() => {
        const month = currentDate.value.getMonth();
        const year = currentDate.value.getFullYear();
        return events.value.filter(
          e =>
            e.type === "delivery" && e.date.getMonth() === month && e.date.getFullYear() === year,
        ).length;
      });

      const monthOrders = computed(() => {
        const month = currentDate.value.getMonth();
        const year = currentDate.value.getFullYear();
        return events.value.filter(
          e => e.type === "order" && e.date.getMonth() === month && e.date.getFullYear() === year,
        ).length;
      });

      const pendingDeliveries = computed(() => {
        return events.value.filter(e => e.type === "delivery" && !e.completed).length;
      });

      const pendingOrders = computed(() => {
        return events.value.filter(e => e.type === "order" && e.statut === "attente").length;
      });

      // Fonctions utilitaires
      const formatDateKey = date => {
        const d = new Date(date);
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(d.getDate()).padStart(2, "0")}`;
      };

      const formatCurrency = amount => {
        return new Intl.NumberFormat("fr-FR", {
          style: "currency",
          currency: "EUR",
        }).format(amount);
      };

      const isToday = date => {
        const today = new Date();
        const d = new Date(date);
        return (
          d.getDate() === today.getDate() &&
          d.getMonth() === today.getMonth() &&
          d.getFullYear() === today.getFullYear()
        );
      };

      const getDeliveryStatusLabel = status => {
        const labels = {
          attente: "En attente",
          partielle: "Partielle",
          completee: "Complétée",
          annulee: "Annulée",
        };
        return labels[status] || status;
      };

      const getOrderStatusLabel = status => {
        const labels = {
          attente: "En attente",
          preparation: "En préparation",
          expedie: "Expédiée",
          annule: "Annulée",
        };
        return labels[status] || status;
      };

      const getOrderStatusClass = status => {
        const classes = {
          attente: "pending",
          preparation: "progress",
          expedie: "completed",
          annule: "cancelled",
        };
        return classes[status] || "pending";
      };

      const previousMonth = () => {
        currentDate.value = new Date(
          currentDate.value.getFullYear(),
          currentDate.value.getMonth() - 1,
        );
      };

      const nextMonth = () => {
        currentDate.value = new Date(
          currentDate.value.getFullYear(),
          currentDate.value.getMonth() + 1,
        );
      };

      const goToToday = () => {
        currentDate.value = new Date();
        const today = formatDateKey(new Date());
        selectedDate.value = today;
      };

      const selectDay = day => {
        if (!day.otherMonth) {
          selectedDate.value = day.date;
        }
      };

      const viewDeliveryDetails = event => {
        router.push("/reception");
      };

      const viewOrderDetails = event => {
        router.push(`/orders`);
      };

      // Charger les données au montage
      onMounted(() => {
        fetchData();
      });

      return {
        events,
        loading,
        error,
        currentDate,
        selectedDate,
        weekDays,
        currentMonthLabel,
        calendarDays,
        selectedDayEvents,
        deliveryEvents,
        orderEvents,
        formatSelectedDate,
        todayCount,
        monthDeliveries,
        monthOrders,
        pendingDeliveries,
        pendingOrders,
        fetchData,
        formatDateKey,
        formatCurrency,
        isToday,
        getDeliveryStatusLabel,
        getOrderStatusLabel,
        getOrderStatusClass,
        previousMonth,
        nextMonth,
        goToToday,
        selectDay,
        viewDeliveryDetails,
        viewOrderDetails,
      };
    },
  };
</script>

<style scoped>
  .agenda-page {
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
    grid-template-columns: repeat(3, 1fr);
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

  .today-icon {
    background: #f3e8ff;
    color: #7c3aed;
  }

  .deliveries-icon {
    background: #f0fdf4;
    color: #059669;
  }

  .orders-icon {
    background: #eff6ff;
    color: #2563eb;
  }

  .kpi-content {
    flex: 1;
  }

  .kpi-label {
    font-size: 13px;
    color: #64748b;
    line-height: 1.3;
    font-weight: 500;
  }

  .kpi-value {
    font-size: 28px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1;
  }

  .kpi-sublabel {
    font-size: 12px;
    color: #64748b;
    margin: 4px 0 0 0;
  }

  /* NAVIGATION CALENDRIER */
  .calendar-navigation {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  }

  .nav-group {
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }

  .nav-btn {
    background: none;
    border: none;
    color: #64748b;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
  }

  .nav-btn:hover {
    background: #f1f5f9;
    color: black;
  }

  .nav-btn svg {
    width: 20px;
    height: 20px;
    stroke-width: 2;
  }

  .current-month {
    font-size: 20px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
    min-width: 200px;
    text-align: center;
  }

  .nav-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .today-btn,
  .refresh-btn {
    background: #0062ff;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.625rem 1.5rem;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .today-btn:hover,
  .refresh-btn:hover {
    background: #2563eb;
  }

  .refresh-btn {
    padding: 0.625rem;
    background: #00b8d4;
  }

  .refresh-btn:hover {
    background: #0891a6;
  }

  .refresh-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .today-btn svg,
  .refresh-btn svg {
    width: 16px;
    height: 16px;
    stroke-width: 2;
  }

  @keyframes spin {
    from {
      transform: rotate(0deg);
    }

    to {
      transform: rotate(360deg);
    }
  }

  .animate-spin {
    animation: spin 1s linear infinite;
  }

  /* LOADING & ERROR */
  .loading-container,
  .error-container {
    text-align: center;
    padding: 4rem;
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

  /* CALENDRIER */
  .calendar-section {
    margin-bottom: 2rem;
  }

  .calendar-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
    overflow: hidden;
    padding: 1.5rem;
  }

  .weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    margin-bottom: 0.5rem;
  }

  .weekday {
    text-align: center;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.75rem 0;
  }

  .days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
  }

  .day-cell {
    aspect-ratio: 1;
    background: #fafbfc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    min-height: 90px;
    display: flex;
    flex-direction: column;
  }

  .day-cell:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  .day-cell.other-month {
    opacity: 0.3;
    cursor: default;
  }

  .day-cell.other-month:hover {
    transform: none;
    box-shadow: none;
  }

  .day-cell.is-today {
    background: #fef3c7;
    border-color: #fcd34d;
  }

  .day-cell.is-today:hover {
    background: #fde68a;
  }

  .day-cell.is-weekend {
    background: #f8fafc;
  }

  .day-cell.is-past {
    opacity: 0.6;
  }

  .day-cell.has-events {
    cursor: pointer;
  }

  .day-cell.has-events:hover {
    background: #e0f2fe;
    border-color: #00b8d4;
  }

  .day-cell.selected {
    background: #e0f2fe;
    border-color: #00b8d4;
    box-shadow: 0 0 0 2px rgba(0, 184, 212, 0.2);
  }

  .day-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 0.5rem;
  }

  .day-number {
    font-size: 14px;
    font-weight: 500;
    color: #0f172a;
  }

  .today-label {
    font-size: 10px;
    font-weight: 600;
    color: black;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .day-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }

  .event-summary {
    display: flex;
    gap: 0.25rem;
  }

  .summary-item {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 2px;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    color: white;
  }

  .summary-item svg {
    width: 12px;
    height: 12px;
    stroke-width: 2;
  }

  .summary-item.delivery {
    background: #059669;
  }

  .summary-item.order {
    background: #2563eb;
  }

  .more-indicator {
    font-size: 10px;
    color: #64748b;
    text-align: center;
    margin-top: 2px;
  }

  .day-empty {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .empty-dot {
    width: 4px;
    height: 4px;
    background: #e2e8f0;
    border-radius: 50%;
  }

  /* DÉTAILS DU JOUR */
  .day-details-section {
    margin-bottom: 2rem;
  }

  .day-details-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
    overflow: hidden;
  }

  .details-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
  }

  .header-left {
    flex: 1;
  }

  .details-title {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 0.25rem 0;
    text-transform: capitalize;
  }

  .details-subtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
  }

  .close-btn {
    background: none;
    border: none;
    color: #64748b;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
  }

  .close-btn:hover {
    background: #e2e8f0;
    color: black;
  }

  .close-btn svg {
    width: 20px;
    height: 20px;
    stroke-width: 2;
  }

  .details-body {
    padding: 1.5rem;
  }

  /* ÉTAT VIDE */
  .empty-state {
    text-align: center;
    padding: 2rem;
  }

  .empty-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1rem;
    color: #cbd5e1;
  }

  .empty-icon svg {
    width: 100%;
    height: 100%;
    stroke-width: 1.5;
  }

  .empty-text {
    font-size: 16px;
    color: #64748b;
    margin: 0;
  }

  /* LISTE DES ÉVÉNEMENTS */
  .events-list {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }

  .events-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .section-title svg {
    width: 18px;
    height: 18px;
    stroke-width: 1.5;
    color: #64748b;
  }

  .section-events {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .event-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: white;
    transition: all 0.2s ease;
  }

  .event-item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transform: translateY(-1px);
  }

  .event-item.completed {
    opacity: 0.6;
    background: #f8fafc;
  }

  .event-item.delivery {
    border-left: 4px solid #059669;
  }

  .event-item.order {
    border-left: 4px solid #2563eb;
  }

  .event-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .event-item.delivery .event-icon {
    background: #f0fdf4;
    color: #059669;
  }

  .event-item.order .event-icon {
    background: #eff6ff;
    color: #2563eb;
  }

  .event-icon svg {
    width: 20px;
    height: 20px;
    stroke-width: 1.5;
  }

  .event-content {
    flex: 1;
  }

  .event-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
  }

  .event-id {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
  }

  .event-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 10px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .event-badge.pending {
    background: #fef3c7;
    color: #92400e;
  }

  .event-badge.progress {
    background: #dbeafe;
    color: #1e40af;
  }

  .event-badge.completed {
    background: #d1fae5;
    color: #047857;
  }

  .event-badge.cancelled {
    background: #fee2e2;
    color: #dc2626;
  }

  .event-main {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }

  .event-partner {
    font-size: 15px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
  }

  .event-info {
    font-size: 13px;
    color: #64748b;
    margin: 0;
  }

  .event-notes {
    font-size: 12px;
    color: #94a3b8;
    margin: 0;
    font-style: italic;
  }

  .event-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .action-btn {
    background: none;
    border: 1px solid #e2e8f0;
    color: #64748b;
    padding: 6px;
    border-radius: 6px;
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

  /* ANIMATIONS */
  .slide-up-enter-active,
  .slide-up-leave-active {
    transition: all 0.3s ease;
  }

  .slide-up-enter-from {
    opacity: 0;
    transform: translateY(20px);
  }

  .slide-up-leave-to {
    opacity: 0;
    transform: translateY(20px);
  }

  /* RESPONSIVE */
  @media (max-width: 1024px) {
    .kpi-section {
      grid-template-columns: 1fr;
    }

    .calendar-navigation {
      flex-direction: column;
      gap: 1rem;
    }

    .nav-group {
      width: 100%;
      justify-content: center;
    }

    .nav-actions {
      width: 100%;
      justify-content: center;
    }
  }

  @media (max-width: 768px) {
    .day-cell {
      min-height: 70px;
      padding: 0.25rem;
    }

    .day-number {
      font-size: 12px;
    }

    .summary-item {
      font-size: 10px;
      padding: 1px 4px;
    }

    .summary-item svg {
      width: 10px;
      height: 10px;
    }

    .event-item {
      flex-direction: column;
      align-items: stretch;
    }

    .event-actions {
      justify-content: flex-end;
      margin-top: 0.75rem;
      padding-top: 0.75rem;
      border-top: 1px solid #e2e8f0;
    }
  }
</style>
