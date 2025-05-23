<template>
  <div class="dashboard">
    <!-- Cartes KPI -->
    <div class="kpi-section">
      <div class="kpi-card kpi-alert">
        <div class="kpi-icon alert-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Seuil de stock</p>
          <p class="kpi-value">3</p>
        </div>
      </div>

      <div class="kpi-card kpi-orders">
        <div class="kpi-icon orders-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
            <polyline points="14,2 14,8 20,8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10,9 9,9 8,9"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Commandes<br>en préparation</p>
          <p class="kpi-value">12</p>
        </div>
      </div>

      <div class="kpi-card kpi-delivery">
        <div class="kpi-icon delivery-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <rect x="1" y="3" width="15" height="13"/>
            <polygon points="16,3 19,7 19,13 16,13"/>
            <circle cx="5.5" cy="18.5" r="2.5"/>
            <circle cx="18.5" cy="18.5" r="2.5"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Livraisons<br>à réceptionner</p>
          <p class="kpi-value">5</p>
        </div>
      </div>

      <div class="kpi-card kpi-alerts">
        <div class="kpi-icon alerts-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
        </div>
        <div class="kpi-content">
          <p class="kpi-label">Alertes</p>
          <p class="kpi-value">1</p>
        </div>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="dashboard-content">
      <!-- Graphique niveau de stock -->
      <div class="content-section">
        <div class="section-card">
          <h3 class="section-title">Niveau de stock</h3>
          <div class="stock-chart">
            <div class="chart-container">
              <div class="chart-bar" :style="{ height: '70%' }"></div>
              <div class="chart-bar" :style="{ height: '85%' }"></div>
              <div class="chart-bar" :style="{ height: '60%' }"></div>
              <div class="chart-bar" :style="{ height: '45%' }"></div>
              <div class="chart-bar" :style="{ height: '90%' }"></div>
              <div class="chart-bar" :style="{ height: '75%' }"></div>
            </div>
            <div class="chart-labels">
              <span>A1</span>
              <span>A2</span>
              <span>A3</span>
              <span>A4</span>
              <span>A5</span>
              <span>A6</span>
            </div>
          </div>
        </div>

        <!-- Commandes récentes -->
        <div class="section-card">
          <h3 class="section-title">Commandes récentes</h3>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Client</th>
                  <th>Date</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="order-id">1005</td>
                  <td>Dupont SAS</td>
                  <td>4 mai 2023</td>
                  <td><span class="status status-preparing">En préparation</span></td>
                </tr>
                <tr>
                  <td class="order-id">1004</td>
                  <td>Martin & Cie</td>
                  <td>4 mai 2023</td>
                  <td><span class="status status-preparing">En préparation</span></td>
                </tr>
                <tr>
                  <td class="order-id">1005</td>
                  <td>Durand SA</td>
                  <td>3 mai 2023</td>
                  <td><span class="status status-received">Réceptionnée</span></td>
                </tr>
                <tr>
                  <td class="order-id">1002</td>
                  <td>Durand SA</td>
                  <td>2 mai 2023</td>
                  <td><span class="status status-received">Réceptionnée</span></td>
                </tr>
                <tr>
                  <td class="order-id">1001</td>
                  <td>Bernard FR</td>
                  <td>2 mai 2023</td>
                  <td><span class="status status-received">Réceptionnée</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Section inférieure -->
      <div class="dashboard-bottom">
        <!-- Livraisons à réceptionner -->
        <div class="section-card">
          <h3 class="section-title">Livraisons à réceptionner</h3>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Fournisseur</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="delivery-id">5003</td>
                  <td>Durand SA</td>
                  <td>6 mai 2023</td>
                </tr>
                <tr>
                  <td class="delivery-id">5002</td>
                  <td>Bernard FR</td>
                  <td>6 mai 2023</td>
                </tr>
                <tr>
                  <td class="delivery-id">5001</td>
                  <td>Bernard FR</td>
                  <td>3 mai 2023</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Alertes -->
        <div class="section-card">
          <h3 class="section-title">Alertes</h3>
          <div class="alerts-container">
            <div class="alert-item">
              <div class="alert-icon-container">
                <div class="alert-dot stock-alert"></div>
              </div>
              <div class="alert-content">
                <p class="alert-title">Stock bas</p>
                <p class="alert-description">Article DEF</p>
              </div>
            </div>
            <div class="alert-item">
              <div class="alert-icon-container">
                <div class="alert-dot delivery-alert"></div>
              </div>
              <div class="alert-content">
                <p class="alert-title">Problème de livraison</p>
                <p class="alert-description">Livraison #5001</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardView'
}
</script>

<style scoped>
.dashboard {
  padding: 0;
  font-family: 'Inter', sans-serif;
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
  border: 1px solid #F1F5F9;
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

.alert-icon {
  background: #FEF2F2;
  color: #DC2626;
}

.orders-icon {
  background: #EFF6FF;
  color: #2563EB;
}

.delivery-icon {
  background: #F0FDF4;
  color: #059669;
}

.alerts-icon {
  background: #FFFBEB;
  color: #D97706;
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

/* CONTENU DASHBOARD */
.dashboard-content {
  display: grid;
  gap: 1.5rem;
}

.content-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.dashboard-bottom {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.section-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1.5rem 0;
}

/* GRAPHIQUE STOCK */
.stock-chart {
  height: 200px;
  display: flex;
  flex-direction: column;
}

.chart-container {
  flex: 1;
  display: flex;
  align-items: end;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 12px;
}

.chart-bar {
  background: linear-gradient(to top, #00B8D4, #0891A6);
  border-radius: 4px 4px 0 0;
  flex: 1;
  min-height: 20px;
  transition: all 0.3s ease;
}

.chart-bar:hover {
  background: linear-gradient(to top, #0891A6, #0E7490);
  transform: scaleY(1.05);
}

.chart-labels {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #64748B;
  font-weight: 500;
}

/* TABLEAUX */
.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  text-align: left;
  padding: 8px 12px;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #E2E8F0;
}

.data-table td {
  padding: 12px;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
}

.order-id, .delivery-id {
  font-weight: 600;
  color: #0F172A;
}

.status {
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.status-preparing {
  background: #EFF6FF;
  color: #1D4ED8;
}

.status-received {
  background: #F0FDF4;
  color: #166534;
}

/* ALERTES */
.alerts-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.alert-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #F8FAFC;
  border-radius: 8px;
  border: 1px solid #E2E8F0;
}

.alert-icon-container {
  flex-shrink: 0;
}

.alert-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
}

.stock-alert {
  background: #EF4444;
}

.delivery-alert {
  background: #F59E0B;
}

.alert-content {
  flex: 1;
}

.alert-title {
  font-size: 14px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 2px 0;
}

.alert-description {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .kpi-section {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .content-section,
  .dashboard-bottom {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .kpi-section {
    grid-template-columns: 1fr;
  }
  
  .kpi-card {
    padding: 1rem;
  }
  
  .section-card {
    padding: 1rem;
  }
  
  .data-table th,
  .data-table td {
    padding: 8px;
    font-size: 12px;
  }
}
</style>