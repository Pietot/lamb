<template>
  <div class="suppliers-page">
    <!-- En-tête avec boutons -->
    <div class="page-header">
      <div class="header-left">
        <button class="back-button" @click="goBack">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="15,18 9,12 15,6"/>
          </svg>
          Retour
        </button>
        <h1 class="page-title">Détail du fournisseur</h1>
      </div>
      <div class="header-actions">
        <button class="action-button print-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="6,9 6,2 18,2 18,9"/>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
            <rect x="6" y="14" width="12" height="8"/>
          </svg>
          Imprimer
        </button>
        <button class="action-button edit-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
          Modifier
        </button>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="supplier-content">
      <!-- Informations fournisseur -->
      <div class="supplier-info-section">
        <div class="supplier-card">
          <!-- En-tête avec logo -->
          <div class="supplier-header">
            <div class="supplier-logo">
              B
            </div>
            <div class="supplier-main-info">
              <h2 class="supplier-name">{{ supplierData.name }}</h2>
              <p class="supplier-since">Fournisseur actif depuis {{ supplierData.since }}</p>
            </div>
          </div>

          <!-- Informations de contact -->
          <div class="contact-section">
            <div class="contact-item">
              <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
              <div class="contact-details">
                <span class="contact-label">Email</span>
                <span class="contact-value">{{ supplierData.email }}</span>
              </div>
            </div>

            <div class="contact-item">
              <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
              <div class="contact-details">
                <span class="contact-label">Téléphone</span>
                <span class="contact-value">{{ supplierData.phone }}</span>
              </div>
            </div>

            <div class="contact-item">
              <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              <div class="contact-details">
                <span class="contact-label">Adresse</span>
                <span class="contact-value">{{ supplierData.address }}</span>
              </div>
            </div>

            <div class="contact-item">
              <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10"/>
                <line x1="2" y1="12" x2="22" y2="12"/>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
              </svg>
              <div class="contact-details">
                <span class="contact-label">Site web</span>
                <a :href="supplierData.website" target="_blank" class="contact-link">{{ supplierData.website }}</a>
              </div>
            </div>
          </div>

          <!-- Contact principal -->
          <div class="primary-contact">
            <h4 class="section-subtitle">Contact principal</h4>
            <div class="contact-person">
              <div class="contact-avatar">
                ML
              </div>
              <div class="contact-person-info">
                <p class="contact-person-name">{{ supplierData.primaryContact.name }}</p>
                <p class="contact-person-title">{{ supplierData.primaryContact.title }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistiques -->
      <div class="stats-section">
        <div class="stats-card">
          <h3 class="stats-title">Statistiques</h3>
          
          <div class="stats-grid">
            <div class="stat-item">
              <p class="stat-label">Produits fournis</p>
              <p class="stat-value large">{{ supplierData.stats.products }}</p>
            </div>
            
            <div class="stat-item">
              <p class="stat-label">Livraisons totales</p>
              <p class="stat-value large">{{ supplierData.stats.deliveries }}</p>
            </div>
            
            <div class="stat-item">
              <p class="stat-label">Commandes en cours</p>
              <p class="stat-value large">{{ supplierData.stats.currentOrders }}</p>
            </div>
          </div>

          <div class="metrics-grid">
            <div class="metric-item">
              <p class="metric-label">Délais SLA</p>
              <p class="metric-value">{{ supplierData.metrics.slaAmount }}</p>
            </div>
            
            <div class="metric-item">
              <p class="metric-label">Délai prévu</p>
              <p class="metric-value">{{ supplierData.metrics.avgDelay }}</p>
            </div>
            
            <div class="metric-item">
              <p class="metric-label">Note qualité</p>
              <div class="rating-display">
                <span class="metric-value">{{ supplierData.metrics.rating }}</span>
                <div class="stars">
                  <span v-for="i in 5" :key="i" class="star" :class="{ 'filled': i <= Math.floor(supplierData.metrics.rating) }">
                    ★
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Onglets de navigation -->
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
        <div v-if="activeTab === 'products'" class="tab-panel">
          <div class="products-header">
            <div class="search-container">
              <input
                v-model="productSearch"
                type="text"
                placeholder="Rechercher un produit..."
                class="search-input"
              />
              <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
              </svg>
            </div>
            <button class="export-button">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7,10 12,15 17,10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
              Exporter
            </button>
          </div>

          <div class="products-table-container">
            <table class="products-table">
              <thead>
                <tr>
                  <th>Référence</th>
                  <th>Produit</th>
                  <th>Catégorie</th>
                  <th>Prix unitaire</th>
                  <th>En stock</th>
                  <th>Seuil</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="product in filteredProducts" :key="product.reference">
                  <td class="product-reference">{{ product.reference }}</td>
                  <td class="product-name">{{ product.name }}</td>
                  <td class="product-category">{{ product.category }}</td>
                  <td class="product-price">{{ formatCurrency(product.unitPrice) }}</td>
                  <td class="product-stock">{{ product.stock }}</td>
                  <td class="product-threshold">{{ product.threshold }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Autres onglets (placeholder) -->
        <div v-else class="tab-panel">
          <div class="placeholder-content">
            <p>Contenu de l'onglet "{{ tabs.find(t => t.id === activeTab)?.label }}" en cours de développement</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'SuppliersView',
  setup() {
    const router = useRouter()
    const activeTab = ref('products')
    const productSearch = ref('')

    // Données du fournisseur selon la maquette
    const supplierData = ref({
      name: 'Bernard FR',
      since: '2019',
      email: 'contact@bernard-fr.com',
      phone: '01 45 67 89 01',
      address: '42 rue du Commerce, 75015 Paris',
      website: 'www.bernard-fr.com',
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
      { id: 'deliveries', label: 'Livraisons' },
      { id: 'history', label: 'Historique' },
      { id: 'documents', label: 'Documents' }
    ])

    // Produits du fournisseur selon la maquette
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

    const filteredProducts = computed(() => {
      if (!productSearch.value) return products.value
      return products.value.filter(product =>
        product.reference.toLowerCase().includes(productSearch.value.toLowerCase()) ||
        product.name.toLowerCase().includes(productSearch.value.toLowerCase()) ||
        product.category.toLowerCase().includes(productSearch.value.toLowerCase())
      )
    })

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
      productSearch,
      supplierData,
      tabs,
      products,
      filteredProducts,
      formatCurrency,
      goBack
    }
  }
}
</script>

<style scoped>
.suppliers-page {
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

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.back-button {
  background: none;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.5rem 1rem;
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
  align-items: center;
  gap: 0.75rem;
}

.action-button {
  border: none;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.print-button {
  background: #F8FAFC;
  color: #64748B;
  border: 1px solid #E2E8F0;
}

.print-button:hover {
  background: #E2E8F0;
  color: #334155;
}

.edit-button {
  background: #3B82F6;
  color: white;
}

.edit-button:hover {
  background: #2563EB;
  transform: translateY(-1px);
}

.action-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* CONTENU PRINCIPAL */
.supplier-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-bottom: 2rem;
}

.supplier-info-section,
.stats-section {
  display: flex;
  flex-direction: column;
}

.supplier-card,
.stats-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  height: fit-content;
}

/* INFORMATIONS FOURNISSEUR */
.supplier-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #F1F5F9;
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

/* CONTACT */
.contact-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 2rem;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
}

.contact-icon {
  width: 18px;
  height: 18px;
  color: #64748B;
  stroke-width: 2;
  margin-top: 2px;
  flex-shrink: 0;
}

.contact-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.contact-label {
  font-size: 12px;
  color: #64748B;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.contact-value {
  font-size: 14px;
  color: #334155;
  font-weight: 500;
}

.contact-link {
  font-size: 14px;
  color: #3B82F6;
  font-weight: 500;
  text-decoration: none;
}

.contact-link:hover {
  text-decoration: underline;
}

/* CONTACT PRINCIPAL */
.primary-contact {
  border-top: 1px solid #F1F5F9;
  padding-top: 1.5rem;
}

.section-subtitle {
  font-size: 14px;
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

.contact-person-name {
  font-size: 14px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
}

.contact-person-title {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

/* STATISTIQUES */
.stats-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1.5rem 0;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #F1F5F9;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #F1F5F9;
}

.stat-item {
  text-align: center;
}

.stat-label {
  font-size: 12px;
  color: #64748B;
  font-weight: 500;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
  line-height: 1;
}

.stat-value.large {
  font-size: 28px;
}

/* MÉTRIQUES */
.metrics-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.metric-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
}

.metric-item:not(:last-child) {
  border-bottom: 1px solid #F8FAFC;
}

.metric-label {
  font-size: 14px;
  color: #64748B;
  font-weight: 500;
  margin: 0;
}

.metric-value {
  font-size: 14px;
  color: #0F172A;
  font-weight: 600;
  margin: 0;
}

.rating-display {
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
  color: #3B82F6;
  background: white;
  border-bottom-color: #3B82F6;
}

.tab-content {
  padding: 1.5rem;
}

/* ONGLET PRODUITS */
.products-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.search-container {
  position: relative;
  flex: 1;
  min-width: 250px;
}

.search-input {
  width: 100%;
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  font-size: 14px;
  color: #334155;
  transition: all 0.2s ease;
}

.search-input:focus {
  outline: none;
  border-color: #00B8D4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  color: #94A3B8;
  stroke-width: 2;
  pointer-events: none;
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

/* TABLEAU PRODUITS */
.products-table-container {
  overflow-x: auto;
}

.products-table {
  width: 100%;
  border-collapse: collapse;
}

.products-table th {
  background: #F8FAFC;
  text-align: left;
  padding: 0.75rem 1rem;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #E2E8F0;
}

.products-table th:nth-child(4),
.products-table th:nth-child(5),
.products-table th:nth-child(6) {
  text-align: right;
}

.products-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
}

.products-table tr:hover {
  background: #F8FAFC;
}

.product-reference {
  font-weight: 600;
  color: #0F172A;
}

.product-price,
.product-stock,
.product-threshold {
  text-align: right;
  font-weight: 500;
}

/* PLACEHOLDER */
.placeholder-content {
  text-align: center;
  padding: 3rem 1rem;
  color: #64748B;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .supplier-content {
    grid-template-columns: 1fr;
  }
  
  .page-header {
    flex-direction: column;
    align-items: stretch;
  }
  
  .header-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .tabs-nav {
    flex-wrap: wrap;
  }
  
  .tab-button {
    flex: 1;
    min-width: 100px;
  }
  
  .products-header {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-container {
    min-width: auto;
  }
  
  .products-table {
    font-size: 12px;
  }
  
  .products-table th,
  .products-table td {
    padding: 0.5rem;
  }
}

@media (max-width: 640px) {
  .supplier-header {
    flex-direction: column;
    align-items: flex-start;
    text-align: center;
  }
  
  .products-table th:nth-child(3),
  .products-table td:nth-child(3),
  .products-table th:nth-child(4),
  .products-table td:nth-child(4) {
    display: none;
  }
}
</style>
