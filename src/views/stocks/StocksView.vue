<template>
  <div class="stocks-page">
    <!-- En-tête avec bouton Ajouter -->
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Stock</h1>
      </div>
      <div class="header-right">
        <button class="add-button" @click="showAddModal = true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Filtres de recherche -->
    <div class="filters-section">
      <div class="filter-group">
        <select v-model="filters.reference" class="filter-select">
          <option value="">Référence</option>
          <option value="A1A">A1A</option>
          <option value="A2A">A2A</option>
          <option value="A3A">A3A</option>
          <option value="A6A">A6A</option>
        </select>
        
        <select v-model="filters.category" class="filter-select">
          <option value="">Catégorie</option>
          <option value="Catégorie 1">Catégorie 1</option>
          <option value="Catégorie 2">Catégorie 2</option>
          <option value="Catégorie 3">Catégorie 3</option>
        </select>
        
        <select v-model="filters.supplier" class="filter-select">
          <option value="">Fournisseur</option>
          <option value="Bernard FR">Bernard FR</option>
          <option value="Durand SA">Durand SA</option>
          <option value="Dupont SAS">Dupont SAS</option>
        </select>
        
        <button class="search-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          Rechercher
        </button>
      </div>
    </div>

    <!-- Graphique En stock -->
    <div class="chart-section">
      <div class="chart-card">
        <h3 class="chart-title">En stock</h3>
        <div class="stock-chart">
          <div class="chart-container">
            <div class="chart-bar" :style="{ height: '75%' }" data-value="30"></div>
            <div class="chart-bar" :style="{ height: '85%' }" data-value="40"></div>
            <div class="chart-bar" :style="{ height: '60%' }" data-value="25"></div>
            <div class="chart-bar" :style="{ height: '50%' }" data-value="20"></div>
            <div class="chart-bar" :style="{ height: '70%' }" data-value="30"></div>
            <div class="chart-bar" :style="{ height: '90%' }" data-value="45"></div>
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
    </div>

    <!-- Tableau des stocks -->
    <div class="table-section">
      <div class="table-card">
        <div class="table-container">
          <table class="stocks-table">
            <thead>
              <tr>
                <th>Référence</th>
                <th>Produit</th>
                <th>Catégorie</th>
                <th>Fournisseur</th>
                <th>En stock</th>
                <th>Seuil</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in stockItems" :key="item.reference" :class="{ 'low-stock': item.stock <= item.threshold }">
                <td class="reference">{{ item.reference }}</td>
                <td>{{ item.product }}</td>
                <td>{{ item.category }}</td>
                <td>{{ item.supplier }}</td>
                <td class="stock-value">{{ item.stock }}</td>
                <td class="threshold-value">{{ item.threshold }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Ajouter un article (placeholder) -->
    <div v-if="showAddModal" class="modal-overlay" @click="showAddModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Ajouter un article</h3>
          <button @click="showAddModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
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
import { ref, reactive } from 'vue'

export default {
  name: 'StocksView',
  setup() {
    const showAddModal = ref(false)
    
    const filters = reactive({
      reference: '',
      category: '',
      supplier: ''
    })

    // Données des stocks selon la maquette
    const stockItems = ref([
      {
        reference: 'A1A',
        product: 'Article A',
        category: 'Catégorie 1',
        supplier: 'Bernard FR',
        stock: 20,
        threshold: 10
      },
      {
        reference: 'A2A',
        product: 'Article B',
        category: 'Catégorie 2',
        supplier: 'Durand SA',
        stock: 40,
        threshold: 15
      },
      {
        reference: 'A3A',
        product: 'Article C',
        category: 'Catégorie 3',
        supplier: 'Bernard FR',
        stock: 18,
        threshold: 10
      },
      {
        reference: 'A8A',
        product: 'Article D',
        category: 'Catégorie 2',
        supplier: 'Dupont SAS',
        stock: 25,
        threshold: 10
      },
      {
        reference: 'A6A',
        product: 'Article E',
        category: 'Catégorie 1',
        supplier: 'Bernard FR',
        stock: 30,
        threshold: 5
      }
    ])

    return {
      showAddModal,
      filters,
      stockItems
    }
  }
}
</script>

<style scoped>
.stocks-page {
  padding: 0;
  font-family: 'Inter', sans-serif;
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
  color: #0F172A;
  margin: 0;
}

.add-button {
  background: #00B8D4;
  color: white;
  border: none;
  border-radius: 8px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 8px rgba(0, 184, 212, 0.3);
}

.add-button:hover {
  background: #0891A6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
}

.add-button svg {
  width: 20px;
  height: 20px;
  stroke-width: 2;
}

/* FILTRES */
.filters-section {
  margin-bottom: 2rem;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.filter-select {
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-size: 14px;
  color: #64748B;
  min-width: 140px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #00B8D4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

.search-button {
  background: #3B82F6;
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
  background: #2563EB;
  transform: translateY(-1px);
}

.search-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* GRAPHIQUE */
.chart-section {
  margin-bottom: 2rem;
}

.chart-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
}

.chart-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1.5rem 0;
}

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
  padding: 0 8px;
}

.chart-bar {
  background: linear-gradient(to top, #3B82F6, #60A5FA);
  border-radius: 4px 4px 0 0;
  flex: 1;
  min-height: 20px;
  transition: all 0.3s ease;
  position: relative;
  cursor: pointer;
}

.chart-bar:hover {
  background: linear-gradient(to top, #2563EB, #3B82F6);
  transform: scaleY(1.05);
}

.chart-bar:hover::after {
  content: attr(data-value);
  position: absolute;
  top: -25px;
  left: 50%;
  transform: translateX(-50%);
  background: #1F2937;
  color: white;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 10px;
  white-space: nowrap;
}

.chart-labels {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #64748B;
  font-weight: 500;
  padding: 0 8px;
}

/* TABLEAU */
.table-section {
  margin-bottom: 2rem;
}

.table-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  overflow: hidden;
}

.table-container {
  overflow-x: auto;
}

.stocks-table {
  width: 100%;
  border-collapse: collapse;
}

.stocks-table th {
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

.stocks-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
}

.stocks-table tr:hover {
  background: #F8FAFC;
}

.stocks-table tr.low-stock {
  background: #FEF2F2;
}

.stocks-table tr.low-stock td {
  color: #991B1B;
}

.reference {
  font-weight: 600;
  color: #0F172A;
}

.stock-value,
.threshold-value {
  font-weight: 600;
  text-align: center;
}

.stock-value {
  color: #059669;
}

.threshold-value {
  color: #DC2626;
}

.low-stock .stock-value {
  color: #DC2626;
  font-weight: 700;
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
  border-bottom: 1px solid #E2E8F0;
}

.modal-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  color: #64748B;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background: #F1F5F9;
  color: #334155;
}

.modal-close svg {
  width: 20px;
  height: 20px;
  stroke-width: 2;
}

.modal-body {
  padding: 1.5rem;
  text-align: center;
  color: #64748B;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .filter-group {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-select,
  .search-button {
    width: 100%;
  }
  
  .chart-container {
    padding: 0 4px;
    gap: 4px;
  }
  
  .chart-labels {
    padding: 0 4px;
  }
  
  .stocks-table {
    font-size: 12px;
  }
  
  .stocks-table th,
  .stocks-table td {
    padding: 0.75rem 0.5rem;
  }
}
</style>
