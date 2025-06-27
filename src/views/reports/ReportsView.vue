<template>
  <div class="reports-page">
    <!-- En-tête avec contrôles -->
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Rapports et analyses</h1>
      </div>
      <div class="header-controls">
        <select v-model="selectedPeriod" class="period-select">
          <option value="cette-annee">Cette année</option>
          <option value="mois-dernier">Mois dernier</option>
          <option value="trimestre">Ce trimestre</option>
          <option value="annee-derniere">Année dernière</option>
        </select>

        <button class="filter-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46" />
          </svg>
          Filtres
        </button>

        <button class="export-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="7,10 12,15 17,10" />
            <line x1="12" y1="15" x2="12" y2="3" />
          </svg>
          Exporter
        </button>
      </div>
    </div>

    <!-- Navigation des onglets -->
    <div class="tabs-nav">
      <button v-for="tab in tabs" :key="tab.id" class="tab-button" :class="{ 'active': activeTab === tab.id }"
        @click="activeTab = tab.id">
        <component :is="tab.icon" class="tab-icon" />
        {{ tab.label }}
      </button>
    </div>

    <!-- Contenu des onglets -->
    <div class="tab-content">
      <!-- Onglet Tableau de bord -->
      <div v-if="activeTab === 'dashboard'" class="tab-panel">
        <!-- KPI Cards -->
        <div class="kpi-section">
          <div class="kpi-card kpi-sales">
            <div class="kpi-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 6v6l4 2" />
              </svg>
            </div>
            <div class="kpi-content">
              <h3 class="kpi-title">Total des ventes</h3>
              <p class="kpi-value">212 110 €</p>
              <p class="kpi-trend positive">+7% par rapport à l'année précédente</p>
            </div>
          </div>

          <div class="kpi-card kpi-orders">
            <div class="kpi-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" />
                <polyline points="14,2 14,8 20,8" />
              </svg>
            </div>
            <div class="kpi-content">
              <h3 class="kpi-title">Nombre de commandes</h3>
              <p class="kpi-value">486</p>
              <p class="kpi-trend positive">+5% par rapport à l'année précédente</p>
            </div>
          </div>

          <div class="kpi-card kpi-basket">
            <div class="kpi-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <circle cx="9" cy="21" r="1" />
                <circle cx="20" cy="21" r="1" />
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
              </svg>
            </div>
            <div class="kpi-content">
              <h3 class="kpi-title">Panier moyen</h3>
              <p class="kpi-value">436 €</p>
              <p class="kpi-trend negative">-2% par rapport à l'année précédente</p>
            </div>
          </div>

          <div class="kpi-card kpi-clients">
            <div class="kpi-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
              </svg>
            </div>
            <div class="kpi-content">
              <h3 class="kpi-title">Nouveaux clients</h3>
              <p class="kpi-value">24</p>
              <p class="kpi-trend negative">-8% par rapport à l'année précédente</p>
            </div>
          </div>
        </div>

        <!-- Graphiques -->
        <div class="charts-section">
          <!-- Évolution des ventes -->
          <div class="chart-card">
            <h3 class="chart-title">Évolution des ventes</h3>
            <div class="chart-container">
              <canvas ref="salesChart" class="chart-canvas"></canvas>
            </div>
          </div>

          <!-- Répartition des ventes par client -->
          <div class="chart-card">
            <h3 class="chart-title">Répartition des ventes par client</h3>
            <div class="chart-container">
              <canvas ref="clientsChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>

        <!-- Ventes par catégorie -->
        <div class="category-chart-section">
          <div class="chart-card">
            <h3 class="chart-title">Ventes par catégorie</h3>
            <div class="chart-container">
              <canvas ref="categoryChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>

        <!-- Rapports récents -->
        <div class="reports-section">
          <div class="section-card">
            <h3 class="section-title">Rapports récents</h3>

            <div class="reports-list">
              <div v-for="report in recentReports" :key="report.id" class="report-item">
                <div class="report-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14,2 14,8 20,8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10,9 9,9 8,9" />
                  </svg>
                </div>
                <div class="report-info">
                  <h4 class="report-title">{{ report.title }}</h4>
                  <p class="report-date">Généré le {{ report.date }}</p>
                </div>
                <button class="download-button" @click="downloadReport(report.id)">
                  Télécharger
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Autres onglets (placeholder) -->
      <div v-else class="tab-panel">
        <div class="placeholder-content">
          <div class="placeholder-icon">
            <component :is="tabs.find(t => t.id === activeTab)?.icon" />
          </div>
          <h3>{{tabs.find(t => t.id === activeTab)?.label}}</h3>
          <p>Cette section est en cours de développement</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, nextTick } from 'vue'

// Icônes pour les onglets
const DashboardIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
      <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
    </svg>
  `
}

const AnalyticsIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
    </svg>
  `
}

const StocksIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
    </svg>
  `
}

const ReportsIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
      <polyline points="14,2 14,8 20,8"/>
      <line x1="16" y1="13" x2="8" y2="13"/>
      <line x1="16" y1="17" x2="8" y2="17"/>
    </svg>
  `
}

export default {
  name: 'ReportsView',
  components: {
    DashboardIcon,
    AnalyticsIcon,
    StocksIcon,
    ReportsIcon
  },
  setup() {
    const activeTab = ref('dashboard')
    const selectedPeriod = ref('cette-annee')
    const salesChart = ref(null)
    const clientsChart = ref(null)
    const categoryChart = ref(null)

    const tabs = ref([
      { id: 'dashboard', label: 'Tableau de bord', icon: 'DashboardIcon' },
      { id: 'analytics', label: 'Analyse des ventes', icon: 'AnalyticsIcon' },
      { id: 'stocks', label: 'Analyse des stocks', icon: 'StocksIcon' },
      { id: 'reports', label: 'Rapports', icon: 'ReportsIcon' }
    ])

    const recentReports = ref([
      {
        id: 1,
        title: 'Rapport mensuel - Avril 2025',
        date: '02/05/2025'
      },
      {
        id: 2,
        title: 'Analyse trimestrielle - Q1 2025',
        date: '15/04/2025'
      },
      {
        id: 3,
        title: 'Rapport stock - Avril 2025',
        date: '30/04/2025'
      },
      {
        id: 4,
        title: 'Analyse clients - 2025',
        date: '10/04/2025'
      }
    ])

    const initCharts = () => {
      nextTick(() => {
        // Graphique évolution des ventes (ligne)
        if (salesChart.value) {
          const ctx = salesChart.value.getContext('2d')

          // Simulation d'un graphique simple avec Canvas
          ctx.fillStyle = '#F8FAFC'
          ctx.fillRect(0, 0, salesChart.value.width, salesChart.value.height)

          // Grille
          ctx.strokeStyle = '#E2E8F0'
          ctx.lineWidth = 1
          for (let i = 0; i <= 10; i++) {
            const y = (salesChart.value.height / 10) * i
            ctx.beginPath()
            ctx.moveTo(0, y)
            ctx.lineTo(salesChart.value.width, y)
            ctx.stroke()
          }

          // Ligne de données
          ctx.strokeStyle = '#3B82F6'
          ctx.lineWidth = 3
          ctx.beginPath()
          const points = [
            { x: 50, y: 180 },
            { x: 100, y: 150 },
            { x: 150, y: 120 },
            { x: 200, y: 90 },
            { x: 250, y: 100 },
            { x: 300, y: 80 },
            { x: 350, y: 60 },
            { x: 400, y: 50 },
            { x: 450, y: 40 },
            { x: 500, y: 30 }
          ]

          points.forEach((point, index) => {
            if (index === 0) {
              ctx.moveTo(point.x, point.y)
            } else {
              ctx.lineTo(point.x, point.y)
            }
          })
          ctx.stroke()

          // Points
          ctx.fillStyle = '#3B82F6'
          points.forEach(point => {
            ctx.beginPath()
            ctx.arc(point.x, point.y, 4, 0, Math.PI * 2)
            ctx.fill()
          })
        }

        // Graphique clients (camembert)
        if (clientsChart.value) {
          const ctx = clientsChart.value.getContext('2d')
          const centerX = clientsChart.value.width / 2
          const centerY = clientsChart.value.height / 2
          const radius = Math.min(centerX, centerY) - 20

          const data = [
            { label: 'Dupont SAS', value: 35, color: '#3B82F6' },
            { label: 'Martin & Cie', value: 25, color: '#10B981' },
            { label: 'Bernard FR', value: 20, color: '#F59E0B' },
            { label: 'Durand SA', value: 20, color: '#EF4444' }
          ]

          let currentAngle = 0
          data.forEach(item => {
            const sliceAngle = (item.value / 100) * 2 * Math.PI

            ctx.fillStyle = item.color
            ctx.beginPath()
            ctx.moveTo(centerX, centerY)
            ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle)
            ctx.closePath()
            ctx.fill()

            currentAngle += sliceAngle
          })
        }

        // Graphique catégories (barres)
        if (categoryChart.value) {
          const ctx = categoryChart.value.getContext('2d')
          const barWidth = 60
          const spacing = 20
          const maxHeight = categoryChart.value.height - 40

          const categories = [
            { name: 'Catégorie 1', value: 80, color: '#3B82F6' },
            { name: 'Catégorie 2', value: 65, color: '#10B981' },
            { name: 'Catégorie 3', value: 45, color: '#F59E0B' },
            { name: 'Catégorie 4', value: 30, color: '#EF4444' },
            { name: 'Autres', value: 15, color: '#8B5CF6' }
          ]

          categories.forEach((cat, index) => {
            const x = (barWidth + spacing) * index + spacing
            const barHeight = (cat.value / 100) * maxHeight
            const y = categoryChart.value.height - barHeight - 20

            ctx.fillStyle = cat.color
            ctx.fillRect(x, y, barWidth, barHeight)

            // Labels
            ctx.fillStyle = '#64748B'
            ctx.font = '12px Inter'
            ctx.textAlign = 'center'
            ctx.fillText(cat.name, x + barWidth / 2, categoryChart.value.height - 5)
          })
        }
      })
    }

    const downloadReport = (reportId) => {
      console.log('Télécharger rapport:', reportId)
      // Logique de téléchargement
    }

    onMounted(() => {
      initCharts()
    })

    return {
      activeTab,
      selectedPeriod,
      salesChart,
      clientsChart,
      categoryChart,
      tabs,
      recentReports,
      downloadReport
    }
  }
}
</script>

<style scoped>
.reports-page {
  padding: 0;
  font-family: 'Inter', sans-serif;
}

/* EN-TÊTE */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.header-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.period-select {
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-size: 14px;
  color: #334155;
  cursor: pointer;
  transition: all 0.2s ease;
}

.period-select:focus {
  outline: none;
  border-color: #00B8D4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

.filter-button,
.export-button {
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-size: 14px;
  font-weight: 500;
  color: #64748B;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-button:hover,
.export-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.filter-button svg,
.export-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* ONGLETS */
.tabs-nav {
  display: flex;
  background: white;
  border-radius: 12px 12px 0 0;
  border: 1px solid #E2E8F0;
  border-bottom: none;
  overflow-x: auto;
}

.tab-button {
  background: none;
  border: none;
  padding: 1rem 1.5rem;
  font-size: 14px;
  font-weight: 500;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s ease;
  border-bottom: 3px solid transparent;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  white-space: nowrap;
}

.tab-button:hover {
  color: #334155;
  background: #F8FAFC;
}

.tab-button.active {
  color: #3B82F6;
  background: #F8FAFC;
  border-bottom-color: #3B82F6;
}

.tab-icon {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* CONTENU */
.tab-content {
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 0 0 12px 12px;
  padding: 2rem;
}

/* KPI SECTION */
.kpi-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.kpi-card {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.2s ease;
}

.kpi-card:hover {
  transform: translateY(-2px);
}

.kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.kpi-icon svg {
  width: 24px;
  height: 24px;
}

.kpi-sales .kpi-icon {
  background: #EFF6FF;
  color: #2563EB;
}

.kpi-orders .kpi-icon {
  background: #F0FDF4;
  color: #059669;
}

.kpi-basket .kpi-icon {
  background: #FFFBEB;
  color: #D97706;
}

.kpi-clients .kpi-icon {
  background: #F3E8FF;
  color: #7C3AED;
}

.kpi-title {
  font-size: 14px;
  font-weight: 500;
  color: #64748B;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
}

.kpi-value {
  font-size: 24px;
  font-weight: 700;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
  line-height: 1;
}

.kpi-trend {
  font-size: 12px;
  font-weight: 500;
  margin: 0;
}

.kpi-trend.positive {
  color: #059669;
}

.kpi-trend.negative {
  color: #DC2626;
}

/* GRAPHIQUES */
.charts-section {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.category-chart-section {
  margin-bottom: 2rem;
}

.chart-card {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 12px;
  padding: 1.5rem;
}

.chart-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1rem 0;
}

.chart-container {
  width: 100%;
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chart-canvas {
  width: 100%;
  height: 100%;
  border-radius: 8px;
}

/* RAPPORTS RÉCENTS */
.reports-section {
  margin-top: 2rem;
}

.section-card {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 12px;
  padding: 1.5rem;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1.5rem 0;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #E2E8F0;
}

.reports-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.report-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.report-item:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.report-icon {
  width: 40px;
  height: 40px;
  background: #EFF6FF;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #3B82F6;
  flex-shrink: 0;
}

.report-icon svg {
  width: 20px;
  height: 20px;
  stroke-width: 2;
}

.report-info {
  flex: 1;
}

.report-title {
  font-size: 14px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
}

.report-date {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

.download-button {
  background: #3B82F6;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.download-button:hover {
  background: #2563EB;
  transform: translateY(-1px);
}

/* PLACEHOLDER */
.placeholder-content {
  text-align: center;
  padding: 4rem 2rem;
  color: #64748B;
}

.placeholder-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 1rem;
  color: #CBD5E1;
}

.placeholder-icon svg {
  width: 100%;
  height: 100%;
  stroke-width: 1;
}

.placeholder-content h3 {
  font-size: 18px;
  font-weight: 600;
  color: #334155;
  margin: 0 0 0.5rem 0;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .page-header {
    flex-direction: column;
    align-items: stretch;
  }

  .header-controls {
    justify-content: center;
  }

  .charts-section {
    grid-template-columns: 1fr;
  }

  .kpi-section {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .tab-content {
    padding: 1rem;
  }

  .kpi-section {
    grid-template-columns: 1fr;
  }

  .tabs-nav {
    flex-direction: column;
  }

  .tab-button {
    justify-content: flex-start;
    border-bottom: none;
    border-right: 3px solid transparent;
  }

  .tab-button.active {
    border-bottom: none;
    border-right-color: #3B82F6;
  }

  .header-controls {
    flex-direction: column;
    align-items: stretch;
  }

  .chart-container {
    height: 250px;
  }

  .report-item {
    flex-direction: column;
    text-align: center;
    gap: 0.75rem;
  }
}

@media (max-width: 640px) {
  .kpi-card {
    flex-direction: column;
    text-align: center;
  }

  .chart-container {
    height: 200px;
  }
}
</style>