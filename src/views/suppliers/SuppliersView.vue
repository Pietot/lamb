<template>
  <div class="supplier-detail-page">
    <!-- En-tête avec boutons -->
    <div class="page-header">
      <div class="header-left">
        <button class="back-button" @click="goBack">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="15,18 9,12 15,6"/>
          </svg>
          <span>Retour</span>
        </button>
        <h1 class="page-title">Détail du fournisseur</h1>
      </div>
      <div class="header-actions">
        <button class="print-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="6,9 6,2 18,2 18,9"/>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
            <rect x="6" y="14" width="12" height="8"/>
          </svg>
          <span>Imprimer</span>
        </button>
        <button class="edit-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
          <span>Modifier</span>
        </button>
      </div>
    </div>

    <!-- Informations principales -->
    <div class="info-section">
      <div class="info-card">
        <div class="supplier-header">
          <div class="supplier-logo">B</div>
          <div class="supplier-main">
            <h2 class="supplier-name">{{ supplierData.name }}</h2>
            <p class="supplier-since">Fournisseur actif depuis {{ supplierData.since }}</p>
          </div>
        </div>

        <div class="info-grid">
          <div class="info-item">
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
              <polyline points="22,6 12,13 2,6"/>
            </svg>
            <div class="info-content">
              <span class="info-label">EMAIL</span>
              <span class="info-value">{{ supplierData.email }}</span>
            </div>
          </div>

          <div class="info-item">
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            <div class="info-content">
              <span class="info-label">TÉLÉPHONE</span>
              <span class="info-value">{{ supplierData.phone }}</span>
            </div>
          </div>

          <div class="info-item">
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <div class="info-content">
              <span class="info-label">ADRESSE</span>
              <span class="info-value">{{ supplierData.address }}</span>
            </div>
          </div>
        </div>

        <div class="contact-section">
          <h3 class="section-title">Contact principal</h3>
          <div class="contact-person">
            <div class="contact-avatar">ML</div>
            <div class="contact-info">
              <p class="contact-name">{{ supplierData.primaryContact.name }}</p>
              <p class="contact-role">{{ supplierData.primaryContact.title }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistiques -->
      <div class="stats-card">
        <h3 class="card-title">Statistiques</h3>
        
        <div class="stats-grid">
          <div class="stat-item">
            <p class="stat-label">Produits fournis</p>
            <p class="stat-value">{{ supplierData.stats.products }}</p>
          </div>
          
          <div class="stat-item">
            <p class="stat-label">Livraisons totales</p>
            <p class="stat-value">{{ supplierData.stats.deliveries }}</p>
          </div>
          
          <div class="stat-item">
            <p class="stat-label">Commandes en cours</p>
            <p class="stat-value">{{ supplierData.stats.currentOrders }}</p>
          </div>
        </div>

        <div class="metrics-section">
          <div class="metric-item">
            <span class="metric-label">Délais SLA</span>
            <span class="metric-value">{{ supplierData.metrics.slaAmount }}</span>
          </div>
          
          <div class="metric-item">
            <span class="metric-label">Délai prévu</span>
            <span class="metric-value">{{ supplierData.metrics.avgDelay }}</span>
          </div>
          
          <div class="metric-item">
            <span class="metric-label">Note qualité</span>
            <div class="rating">
              <span class="metric-value">{{ supplierData.metrics.rating }}</span>
              <div class="stars">
                <span v-for="i in 5" :key="i" class="star" :class="{ 'filled': i <= Math.floor(supplierData.metrics.rating) }">★</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Onglets -->
    <div class="tabs-section">
      <div class="tabs-nav">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          class="tab-button"
          :class="{ 'active': activeTab === tab.id }"
          @click="activeTab = tab.id"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Contenu des onglets -->
      <div class="tab-content">
        <!-- Onglet Produits -->
        <div v-if="activeTab === 'products'" class="products-section">
          <div class="table-header">
            <h3 class="table-title">Produits fournis</h3>
            <button class="export-button">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7,10 12,15 17,10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
              Exporter
            </button>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>RÉFÉRENCE</th>
                  <th>PRODUIT</th>
                  <th>CATÉGORIE</th>
                  <th>PRIX UNITAIRE</th>
                  <th>EN STOCK</th>
                  <th>SEUIL</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="product in products" :key="product.reference">
                  <td class="reference">{{ product.reference }}</td>
                  <td>{{ product.name }}</td>
                  <td>{{ product.category }}</td>
                  <td class="price">{{ formatCurrency(product.unitPrice) }}</td>
                  <td class="stock">{{ product.stock }}</td>
                  <td class="threshold">{{ product.threshold }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Onglet Commandes -->
        <div v-else-if="activeTab === 'orders'" class="placeholder-section">
          <p>Section des commandes en cours de développement</p>
        </div>

        <!-- Onglet Livraisons -->
        <div v-else-if="activeTab === 'deliveries'" class="placeholder-section">
          <p>Section des livraisons en cours de développement</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'SupplierDetailView',
  setup() {
    const router = useRouter()
    const activeTab = ref('products')

    const supplierData = ref({
      name: 'Bernard FR',
      since: '2019',
      email: 'contact@bernard-fr.com',
      phone: '01 45 67 89 01',
      address: '42 rue du Commerce, 75015 Paris',
      primaryContact: {
        name: 'Marie Lefevre',
        title: 'Responsable commercial'
      },
      stats: {
        products: 47,
        deliveries: 128,
        currentOrders: 3
      },
      metrics: {
        slaAmount: '52 480 €',
        avgDelay: '4,2 jours',
        rating: 4.8
      }
    })

    const tabs = ref([
      { id: 'products', label: 'Produits' },
      { id: 'orders', label: 'Commandes' },
      { id: 'deliveries', label: 'Livraisons' }
    ])

    const products = ref([
      {
        reference: 'A1A',
        name: 'Article A',
        category: 'Catégorie 1',
        unitPrice: 50.00,
        stock: 20,
        threshold: 10
      },
      {
        reference: 'A3A',
        name: 'Article C',
        category: 'Catégorie 3',
        unitPrice: 75.00,
        stock: 18,
        threshold: 10
      }
    ])

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }

    const goBack = () => {
      router.push('/suppliers')
    }

    return {
      activeTab,
      supplierData,
      tabs,
      products,
      formatCurrency,
      goBack
    }
  }
}
</script>

<style scoped>
.supplier-detail-page {
  padding: 0;
  font-family: 'Inter', sans-serif;
}

/* EN-TÊTE */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  gap: 2rem;
  flex-wrap: wrap;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.back-button {
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

.back-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.back-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 0.75rem;
}

.print-button,
.edit-button {
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

.print-button {
  background: white;
  color: #64748B;
  border: 1px solid #E2E8F0;
}

.print-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.edit-button {
  background: #00B8D4;
  color: white;
  box-shadow: 0 2px 8px rgba(0, 184, 212, 0.3);
}

.edit-button:hover {
  background: #0891A6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
}

.print-button svg,
.edit-button svg {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

/* SECTION INFO */
.info-section {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
  margin-bottom: 2rem;
}

.info-card,
.stats-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
}

.supplier-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
}

.supplier-logo {
  width: 60px;
  height: 60px;
  background: #3B82F6;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 24px;
  font-weight: 700;
  flex-shrink: 0;
}

.supplier-name {
  font-size: 20px;
  font-weight: 700;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
}

.supplier-since {
  font-size: 14px;
  color: #64748B;
  margin: 0;
}

.info-grid {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  margin-bottom: 2rem;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
}

.info-icon {
  width: 18px;
  height: 18px;
  color: #64748B;
  stroke-width: 2;
  margin-top: 2px;
  flex-shrink: 0;
}

.info-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.info-label {
  font-size: 12px;
  color: #64748B;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-value {
  font-size: 14px;
  color: #334155;
  font-weight: 500;
}

/* CONTACT */
.contact-section {
  border-top: 1px solid #E2E8F0;
  padding-top: 1.5rem;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1rem 0;
}

.contact-person {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.contact-avatar {
  width: 40px;
  height: 40px;
  background: #00B8D4;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 14px;
  flex-shrink: 0;
}

.contact-name {
  font-size: 14px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
}

.contact-role {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

/* STATISTIQUES */
.card-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1.5rem 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.25rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #E2E8F0;
}

.stat-item {
  text-align: center;
}

.stat-label {
  font-size: 12px;
  color: #64748B;
  font-weight: 500;
  margin: 0 0 0.5rem 0;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
  line-height: 1;
}

.metrics-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.metric-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #F8FAFC;
}

.metric-item:last-child {
  border-bottom: none;
}

.metric-label {
  font-size: 14px;
  color: #64748B;
  font-weight: 500;
}

.metric-value {
  font-size: 14px;
  color: #0F172A;
  font-weight: 600;
}

.rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.stars {
  display: flex;
  gap: 1px;
}

.star {
  color: #E2E8F0;
  font-size: 14px;
}

.star.filled {
  color: #F59E0B;
}

/* ONGLETS */
.tabs-section {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  overflow: hidden;
}

.tabs-nav {
  display: flex;
  border-bottom: 1px solid #E2E8F0;
  background: #F8FAFC;
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
}

.tab-button:hover {
  color: #334155;
  background: #F1F5F9;
}

.tab-button.active {
  color: #00B8D4;
  background: white;
  border-bottom-color: #00B8D4;
}

.tab-content {
  padding: 1.5rem;
}

/* TABLE */
.table-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.table-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

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

.export-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.export-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  background: #F8FAFC;
  text-align: left;
  padding: 1rem;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #E2E8F0;
}

.data-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
}

.data-table tbody tr:hover {
  background: #F8FAFC;
}

.data-table tbody tr:last-child td {
  border-bottom: none;
}

.reference {
  font-weight: 600;
  color: #0F172A;
}

.price,
.stock,
.threshold {
  font-weight: 600;
  text-align: center;
}

.placeholder-section {
  text-align: center;
  padding: 3rem;
  color: #64748B;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .info-section {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: stretch;
  }
  
  .header-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }
  
  .header-actions {
    justify-content: space-between;
  }
  
  .supplier-header {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  
  .tabs-nav {
    flex-wrap: wrap;
  }
  
  .tab-button {
    flex: 1;
    min-width: 100px;
  }
  
  .data-table {
    font-size: 12px;
  }
  
  .data-table th,
  .data-table td {
    padding: 0.75rem 0.5rem;
  }
}

@media (max-width: 640px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .data-table th:nth-child(3),
  .data-table td:nth-child(3),
  .data-table th:nth-child(4),
  .data-table td:nth-child(4) {
    display: none;
  }
}
</style>