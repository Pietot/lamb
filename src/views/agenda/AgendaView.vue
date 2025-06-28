<template>
  <div class="agenda-page">
    <!-- En-tête -->
    <div class="page-header">
      <h1 class="page-title">Agenda partagé</h1>
    </div>

    <!-- Cartes KPI -->
    <div class="kpi-section">
      <div class="kpi-card kpi-today">
        <div class="kpi-icon today-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
            <circle cx="12" cy="16" r="1" fill="currentColor"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Aujourd'hui</p>
          <p class="kpi-value">{{ todayCount }}</p>
          <p class="kpi-sublabel">{{ todayCount === 0 ? 'Aucun événement' : todayCount === 1 ? 'événement' : 'événements' }}</p>
        </div>
      </div>

      <div class="kpi-card kpi-deliveries">
        <div class="kpi-icon deliveries-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="1" y="3" width="15" height="13"/>
            <polygon points="16,3 19,7 19,13 16,13"/>
            <circle cx="5.5" cy="18.5" r="2.5"/>
            <circle cx="18.5" cy="18.5" r="2.5"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Livraisons<br>ce mois</p>
          <p class="kpi-value">{{ monthDeliveries }}</p>
          <p class="kpi-sublabel">{{ pendingDeliveries }} en attente</p>
        </div>
      </div>

      <div class="kpi-card kpi-orders">
        <div class="kpi-icon orders-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
            <path d="M16 10H8M16 14H8"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Expéditions<br>ce mois</p>
          <p class="kpi-value">{{ monthOrders }}</p>
          <p class="kpi-sublabel">{{ pendingOrders }} à traiter</p>
        </div>
      </div>
    </div>

    <!-- Navigation du calendrier -->
    <div class="calendar-navigation">
      <div class="nav-group">
        <button class="nav-btn" @click="previousMonth" title="Mois précédent">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="15 18 9 12 15 6"/>
          </svg>
        </button>
        <h2 class="current-month">{{ currentMonthLabel }}</h2>
        <button class="nav-btn" @click="nextMonth" title="Mois suivant">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="9 18 15 12 9 6"/>
          </svg>
        </button>
      </div>
      <div class="nav-actions">
        <button class="today-btn" @click="goToToday">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
          Aujourd'hui
        </button>
        <button class="view-mode-btn" @click="toggleViewMode" :class="{ active: viewMode === 'week' }">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="9" y1="4" x2="9" y2="22"/>
            <line x1="15" y1="4" x2="15" y2="22"/>
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
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <p class="error-message">{{ error }}</p>
          <button @click="fetchData" class="retry-button">Réessayer</button>
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
                'selected': selectedDate && day.date === selectedDate,
                'is-weekend': day.isWeekend,
                'is-past': day.isPast && !day.isToday
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
                      <rect x="1" y="3" width="15" height="13"/>
                      <polygon points="16,3 19,7 19,13 16,13"/>
                    </svg>
                    <span>{{ day.deliveries }}</span>
                  </div>
                  <div v-if="day.orders > 0" class="summary-item order">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M20 6L9 17l-5-5"/>
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
              <h3 class="details-title">{{ formatSelectedDate }}</h3>
              <p class="details-subtitle">
                {{ selectedDayEvents.length === 0 ? 'Aucun événement' : 
                   selectedDayEvents.length === 1 ? '1 événement' : 
                   selectedDayEvents.length + ' événements' }}
              </p>
            </div>
            <button class="close-btn" @click="selectedDate = null" title="Fermer">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>

          <div class="details-body">
            <!-- Si pas d'événements -->
            <div v-if="selectedDayEvents.length === 0" class="empty-state">
              <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
              </div>
              <p class="empty-text">Aucun événement prévu ce jour</p>
              <div class="empty-actions">
                <button class="add-event-btn delivery" @click="addDelivery">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="1" y="3" width="15" height="13"/>
                    <polygon points="16,3 19,7 19,13 16,13"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                  </svg>
                  Ajouter une livraison
                </button>
                <button class="add-event-btn order" @click="addOrder">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                  </svg>
                  Planifier une expédition
                </button>
              </div>
            </div>

            <!-- Liste des événements -->
            <div v-else class="events-list">
              <div class="events-section" v-if="deliveryEvents.length > 0">
                <h4 class="section-title">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="1" y="3" width="15" height="13"/>
                    <polygon points="16,3 19,7 19,13 16,13"/>
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
                        <rect x="1" y="3" width="15" height="13"/>
                        <polygon points="16,3 19,7 19,13 16,13"/>
                        <circle cx="5.5" cy="18.5" r="2.5"/>
                        <circle cx="18.5" cy="18.5" r="2.5"/>
                      </svg>
                    </div>

                    <div class="event-content">
                      <div class="event-header">
                        <span class="event-id">#{{ event.id }}</span>
                        <span v-if="event.completed" class="event-badge completed">Reçu</span>
                        <span v-else class="event-badge pending">En attente</span>
                      </div>
                      <div class="event-main">
                        <p class="event-partner">{{ event.partner }}</p>
                        <p class="event-info">{{ event.items || 'Articles divers' }}</p>
                      </div>
                    </div>

                    <div class="event-actions">
                      <button 
                        class="action-btn"
                        @click="viewDetails(event)"
                        title="Voir détails"
                      >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                          <circle cx="12" cy="12" r="3"/>
                        </svg>
                      </button>
                      <button 
                        v-if="!event.completed"
                        class="action-btn success"
                        @click="markAsReceived(event)"
                      >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Réceptionner
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="events-section" v-if="orderEvents.length > 0">
                <h4 class="section-title">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                  </svg>
                  Expéditions ({{ orderEvents.length }})
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
                        <path d="M20 6L9 17l-5-5"/>
                      </svg>
                    </div>

                    <div class="event-content">
                      <div class="event-header">
                        <span class="event-id">#{{ event.id }}</span>
                        <span v-if="event.completed" class="event-badge completed">Expédié</span>
                        <span v-else class="event-badge pending">À expédier</span>
                      </div>
                      <div class="event-main">
                        <p class="event-partner">{{ event.partner }}</p>
                        <p class="event-info">{{ formatCurrency(event.amount) }}</p>
                      </div>
                    </div>

                    <div class="event-actions">
                      <button 
                        class="action-btn"
                        @click="viewDetails(event)"
                        title="Voir détails"
                      >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                          <circle cx="12" cy="12" r="3"/>
                        </svg>
                      </button>
                      <button 
                        v-if="!event.completed"
                        class="action-btn primary"
                        @click="markAsShipped(event)"
                      >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        Expédier
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
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'AgendaView',
  setup() {
    const router = useRouter()
    
    // États
    const events = ref([])
    const loading = ref(true)
    const error = ref(null)
    const currentDate = ref(new Date())
    const selectedDate = ref(null)
    const viewMode = ref('month') // 'month' ou 'week'
    
    const weekDays = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']
    
    // Fonction pour récupérer les données
    const fetchData = async () => {
      loading.value = true
      error.value = null
      
      try {
        // Pour l'instant, on utilise des données mockées
        // TODO: Intégrer les vrais endpoints API
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        // Données simulées
        const today = new Date()
        events.value = [
          // Livraisons
          {
            id: '5001',
            type: 'delivery',
            partner: 'Durand SA',
            items: '15 articles - Collection Été',
            date: new Date(today.getFullYear(), today.getMonth(), 5),
            completed: false
          },
          {
            id: '5002',
            type: 'delivery',
            partner: 'Bernard FR',
            items: '8 articles - Accessoires',
            date: new Date(today.getFullYear(), today.getMonth(), 8),
            completed: false
          },
          {
            id: '5003',
            type: 'delivery',
            partner: 'Martin & Cie',
            items: '22 articles - Collection Printemps',
            date: new Date(today.getFullYear(), today.getMonth(), 8),
            completed: false
          },
          {
            id: '5004',
            type: 'delivery',
            partner: 'Dupont SAS',
            items: '5 articles - Nouveautés',
            date: new Date(today.getFullYear(), today.getMonth(), 15),
            completed: false
          },
          {
            id: '5005',
            type: 'delivery',
            partner: 'Fashion Import',
            items: '30 articles - Collection Hiver',
            date: new Date(today.getFullYear(), today.getMonth(), 22),
            completed: false
          },
          // Commandes à expédier
          {
            id: '00010',
            type: 'order',
            partner: 'Boutique Mode Paris',
            amount: 1850.00,
            date: new Date(today.getFullYear(), today.getMonth(), 3),
            completed: false
          },
          {
            id: '00011',
            type: 'order',
            partner: 'Fashion Store Lyon',
            amount: 2340.00,
            date: new Date(today.getFullYear(), today.getMonth(), 8),
            completed: false
          },
          {
            id: '00012',
            type: 'order',
            partner: 'Dupont SAS',
            amount: 980.00,
            date: new Date(today.getFullYear(), today.getMonth(), 12),
            completed: false
          },
          {
            id: '00013',
            type: 'order',
            partner: 'Durand SA',
            amount: 3200.00,
            date: new Date(today.getFullYear(), today.getMonth(), 20),
            completed: false
          },
          {
            id: '00014',
            type: 'order',
            partner: 'Martin & Cie',
            amount: 1500.00,
            date: new Date(today.getFullYear(), today.getMonth(), 20),
            completed: false
          },
          {
            id: '00015',
            type: 'order',
            partner: 'Style Boutique',
            amount: 2800.00,
            date: new Date(today.getFullYear(), today.getMonth(), 25),
            completed: false
          }
        ]
      } catch (err) {
        console.error('Erreur lors du chargement:', err)
        error.value = 'Impossible de charger l\'agenda. Veuillez réessayer.'
      } finally {
        loading.value = false
      }
    }

    // Computed properties
    const currentMonthLabel = computed(() => {
      const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                     'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
      return `${months[currentDate.value.getMonth()]} ${currentDate.value.getFullYear()}`
    })

    const calendarDays = computed(() => {
      const year = currentDate.value.getFullYear()
      const month = currentDate.value.getMonth()
      const firstDay = new Date(year, month, 1)
      const lastDay = new Date(year, month + 1, 0)
      const daysInMonth = lastDay.getDate()
      
      // Ajuster pour commencer par lundi
      let startingDayOfWeek = firstDay.getDay() - 1
      if (startingDayOfWeek === -1) startingDayOfWeek = 6
      
      const days = []
      
      // Jours du mois précédent
      const prevMonthLastDay = new Date(year, month, 0).getDate()
      for (let i = startingDayOfWeek - 1; i >= 0; i--) {
        const date = new Date(year, month - 1, prevMonthLastDay - i)
        days.push(createDayObject(date, true))
      }
      
      // Jours du mois actuel
      for (let i = 1; i <= daysInMonth; i++) {
        const date = new Date(year, month, i)
        days.push(createDayObject(date, false))
      }
      
      // Jours du mois suivant
      const remainingDays = 42 - days.length // 6 semaines complètes
      for (let i = 1; i <= remainingDays; i++) {
        const date = new Date(year, month + 1, i)
        days.push(createDayObject(date, true))
      }
      
      return days
    })

    const createDayObject = (date, otherMonth) => {
      const dateKey = formatDateKey(date)
      const dayEvents = events.value.filter(e => formatDateKey(e.date) === dateKey)
      const dayOfWeek = date.getDay()
      
      return {
        date: dateKey,
        number: date.getDate(),
        otherMonth,
        isToday: isToday(date),
        isWeekend: dayOfWeek === 0 || dayOfWeek === 6,
        isPast: date < new Date().setHours(0, 0, 0, 0),
        events: dayEvents,
        deliveries: dayEvents.filter(e => e.type === 'delivery').length,
        orders: dayEvents.filter(e => e.type === 'order').length
      }
    }

    const selectedDayEvents = computed(() => {
      if (!selectedDate.value) return []
      return events.value.filter(e => formatDateKey(e.date) === selectedDate.value)
    })

    const deliveryEvents = computed(() => {
      return selectedDayEvents.value.filter(e => e.type === 'delivery')
    })

    const orderEvents = computed(() => {
      return selectedDayEvents.value.filter(e => e.type === 'order')
    })

    const formatSelectedDate = computed(() => {
      if (!selectedDate.value) return ''
      const [year, month, day] = selectedDate.value.split('-').map(Number)
      const date = new Date(year, month - 1, day)
      const days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
      const months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 
                     'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre']
      return `${days[date.getDay()]} ${day} ${months[month - 1]} ${year}`
    })

    const todayCount = computed(() => {
      const today = formatDateKey(new Date())
      return events.value.filter(e => formatDateKey(e.date) === today).length
    })

    const monthDeliveries = computed(() => {
      const month = currentDate.value.getMonth()
      const year = currentDate.value.getFullYear()
      return events.value.filter(e => 
        e.type === 'delivery' && 
        e.date.getMonth() === month && 
        e.date.getFullYear() === year
      ).length
    })

    const monthOrders = computed(() => {
      const month = currentDate.value.getMonth()
      const year = currentDate.value.getFullYear()
      return events.value.filter(e => 
        e.type === 'order' && 
        e.date.getMonth() === month && 
        e.date.getFullYear() === year
      ).length
    })

    const pendingDeliveries = computed(() => {
      return events.value.filter(e => e.type === 'delivery' && !e.completed).length
    })

    const pendingOrders = computed(() => {
      return events.value.filter(e => e.type === 'order' && !e.completed).length
    })

    // Fonctions utilitaires
    const formatDateKey = (date) => {
      const d = new Date(date)
      return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }

    const isToday = (date) => {
      const today = new Date()
      const d = new Date(date)
      return d.getDate() === today.getDate() && 
             d.getMonth() === today.getMonth() && 
             d.getFullYear() === today.getFullYear()
    }

    const previousMonth = () => {
      currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1)
    }

    const nextMonth = () => {
      currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1)
    }

    const goToToday = () => {
      currentDate.value = new Date()
      const today = formatDateKey(new Date())
      selectedDate.value = today
    }

    const selectDay = (day) => {
      if (!day.otherMonth) {
        selectedDate.value = day.date
      }
    }

    const toggleViewMode = () => {
      viewMode.value = viewMode.value === 'month' ? 'week' : 'month'
    }

    const viewDetails = (event) => {
      if (event.type === 'delivery') {
        router.push('/reception')
      } else {
        router.push(`/prepare`)
      }
    }

    const markAsShipped = (event) => {
      console.log('Marquer comme expédié:', event.id)
      // TODO: Appeler l'API
      event.completed = true
    }

    const markAsReceived = (event) => {
      console.log('Marquer comme reçu:', event.id)
      // TODO: Appeler l'API
      event.completed = true
    }

    const addDelivery = () => {
      console.log('Ajouter une livraison pour:', selectedDate.value)
      // TODO: Ouvrir un modal ou rediriger
    }

    const addOrder = () => {
      console.log('Planifier une expédition pour:', selectedDate.value)
      // TODO: Ouvrir un modal ou rediriger
    }

    // Charger les données au montage
    onMounted(() => {
      fetchData()
    })

    return {
      events,
      loading,
      error,
      currentDate,
      selectedDate,
      viewMode,
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
      previousMonth,
      nextMonth,
      goToToday,
      selectDay,
      toggleViewMode,
      viewDetails,
      markAsShipped,
      markAsReceived,
      addDelivery,
      addOrder
    }
  }
}
</script>

<style scoped>
.agenda-page {
  padding: 0;
  font-family: 'Inter', sans-serif;
}

/* EN-TÊTE */
.page-header {
  margin-bottom: 2rem;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  color: #0F172A;
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
  border: 1px solid #F1F5F9;
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
  background: #F3E8FF;
  color: #7C3AED;
}

.deliveries-icon {
  background: #F0FDF4;
  color: #059669;
}

.orders-icon {
  background: #EFF6FF;
  color: #2563EB;
}

.kpi-content {
  flex: 1;
}

.kpi-label {
  font-size: 13px;
  color: #64748B;
  margin: 0 0 4px 0;
  line-height: 1.3;
  font-weight: 500;
}

.kpi-value {
  font-size: 28px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
  line-height: 1;
}

.kpi-sublabel {
  font-size: 12px;
  color: #94A3B8;
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
  border: 1px solid #F1F5F9;
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
  color: #64748B;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.nav-btn:hover {
  background: #F1F5F9;
  color: #334155;
}

.nav-btn svg {
  width: 20px;
  height: 20px;
  stroke-width: 2;
}

.current-month {
  font-size: 20px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
  min-width: 200px;
  text-align: center;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.today-btn {
  background: #3B82F6;
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

.today-btn:hover {
  background: #2563EB;
}

.today-btn svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

.view-mode-btn {
  background: none;
  border: 1px solid #E2E8F0;
  color: #64748B;
  border-radius: 6px;
  padding: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.view-mode-btn:hover,
.view-mode-btn.active {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.view-mode-btn svg {
  width: 18px;
  height: 18px;
  stroke-width: 1.5;
}

/* LOADING & ERROR */
.loading-container,
.error-container {
  text-align: center;
  padding: 4rem;
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

.error-icon {
  width: 48px;
  height: 48px;
  color: #DC2626;
  margin: 0 auto 1rem;
}

.error-message {
  color: #DC2626;
  margin-bottom: 1rem;
  font-weight: 500;
}

.retry-button {
  background: #00B8D4;
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
  background: #0891A6;
}

/* CALENDRIER */
.calendar-section {
  margin-bottom: 2rem;
}

.calendar-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
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
  color: #64748B;
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
  background: #FAFBFC;
  border: 1px solid #E2E8F0;
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
  background: #F1F5F9;
  border-color: #CBD5E1;
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
  background: #FEF3C7;
  border-color: #FCD34D;
}

.day-cell.is-today:hover {
  background: #FDE68A;
}

.day-cell.is-weekend {
  background: #F8FAFC;
}

.day-cell.is-past {
  opacity: 0.6;
}

.day-cell.has-events {
  cursor: pointer;
}

.day-cell.has-events:hover {
  background: #E0F2FE;
  border-color: #00B8D4;
}

.day-cell.selected {
  background: #E0F2FE;
  border-color: #00B8D4;
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
  color: #0F172A;
}

.today-label {
  font-size: 10px;
  font-weight: 600;
  color: #D97706;
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
  background: #2563EB;
}

.more-indicator {
  font-size: 10px;
  color: #64748B;
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
  background: #E2E8F0;
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
  border: 1px solid #F1F5F9;
  overflow: hidden;
}

.details-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  background: #F8FAFC;
  border-bottom: 1px solid #E2E8F0;
}

.header-left {
  flex: 1;
}

.details-title {
  font-size: 18px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
  text-transform: capitalize;
}

.details-subtitle {
  font-size: 14px;
  color: #64748B;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  color: #64748B;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.close-btn:hover {
  background: #E2E8F0;
  color: #334155;
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
  color: #CBD5E1;
}

.empty-icon svg {
  width: 100%;
  height: 100%;
  stroke-width: 1.5;
}

.empty-text {
  font-size: 16px;
  color: #64748B;
  margin: 0 0 2rem 0;
}

.empty-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.add-event-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  color: white;
}

.add-event-btn svg {
  width: 18px;
  height: 18px;
  stroke-width: 1.5;
}

.add-event-btn.delivery {
  background: #059669;
}

.add-event-btn.delivery:hover {
  background: #047857;
}

.add-event-btn.order {
  background: #2563EB;
}

.add-event-btn.order:hover {
  background: #1D4ED8;
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
  color: #0F172A;
  margin: 0;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #E2E8F0;
}

.section-title svg {
  width: 18px;
  height: 18px;
  stroke-width: 1.5;
  color: #64748B;
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
  border: 1px solid #E2E8F0;
  background: white;
  transition: all 0.2s ease;
}

.event-item:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  transform: translateY(-1px);
}

.event-item.completed {
  opacity: 0.6;
  background: #F8FAFC;
}

.event-item.delivery {
  border-left: 4px solid #059669;
}

.event-item.order {
  border-left: 4px solid #2563EB;
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
  background: #F0FDF4;
  color: #059669;
}

.event-item.order .event-icon {
  background: #EFF6FF;
  color: #2563EB;
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
  color: #64748B;
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
  background: #FEF3C7;
  color: #92400E;
}

.event-badge.completed {
  background: #D1FAE5;
  color: #047857;
}

.event-main {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.event-partner {
  font-size: 15px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.event-info {
  font-size: 13px;
  color: #64748B;
  margin: 0;
}

.event-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.action-btn {
  background: none;
  border: 1px solid #E2E8F0;
  color: #64748B;
  padding: 6px;
  border-radius: 6px;
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

.action-btn.primary {
  background: #2563EB;
  color: white;
  border-color: #2563EB;
  padding: 6px 16px;
  font-size: 13px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.action-btn.primary:hover {
  background: #1D4ED8;
  border-color: #1D4ED8;
}

.action-btn.success {
  background: #059669;
  color: white;
  border-color: #059669;
  padding: 6px 16px;
  font-size: 13px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.action-btn.success:hover {
  background: #047857;
  border-color: #047857;
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
    border-top: 1px solid #E2E8F0;
  }
  
  .empty-actions {
    flex-direction: column;
  }
  
  .add-event-btn {
    width: 100%;
    justify-content: center;
  }
}
</style>