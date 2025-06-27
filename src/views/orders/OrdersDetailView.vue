<template>
  <div class="order-detail-page">
    <!-- En-tête avec bouton retour et actions -->
    <div class="page-header">
      <div class="header-left">
        <button class="back-button" @click="goBack">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="15,18 9,12 15,6" />
          </svg>
          Retour
        </button>
        <h1 class="page-title">Détail de la commande #{{ orderId }}</h1>
      </div>
      <div class="header-actions">
        <button class="action-button print-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="6,9 6,2 18,2 18,9" />
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
            <rect x="6" y="14" width="12" height="8" />
          </svg>
          Imprimer
        </button>
        <button class="action-button edit-button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
          </svg>
          Modifier
        </button>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="order-content">
      <!-- Informations client -->
      <div class="info-section">
        <div class="info-card">
          <h3 class="section-title">Informations client</h3>
          <div class="client-info">
            <div class="client-details">
              <p class="client-name">{{ orderData.client.name }}</p>
              <p class="client-contact">{{ orderData.client.contact }}</p>
              <p class="client-phone">{{ orderData.client.phone }}</p>
              <p class="client-address">{{ orderData.client.address }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Détails de la commande -->
      <div class="info-section">
        <div class="info-card">
          <h3 class="section-title">Détails de la commande</h3>
          <div class="order-details">
            <div class="detail-row">
              <span class="detail-label">Date</span>
              <span class="detail-value">{{ orderData.details.date }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Statut</span>
              <span class="status-badge" :class="getStatusClass(orderData.details.status)">
                {{ orderData.details.status }}
              </span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Total HT</span>
              <span class="detail-value amount">{{ formatCurrency(orderData.details.totalHT) }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">TVA (20%)</span>
              <span class="detail-value amount">{{ formatCurrency(orderData.details.tva) }}</span>
            </div>
            <div class="detail-row total-row">
              <span class="detail-label">Total TTC</span>
              <span class="detail-value amount total">{{ formatCurrency(orderData.details.totalTTC) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Livraison -->
      <div class="info-section">
        <div class="info-card">
          <h3 class="section-title">Livraison</h3>
          <div class="delivery-info">
            <div class="detail-row">
              <span class="detail-label">Mode</span>
              <span class="detail-value">{{ orderData.delivery.mode }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Adresse</span>
              <span class="detail-value">{{ orderData.delivery.address }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Statut</span>
              <span class="status-badge delivery-status">{{ orderData.delivery.status }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Date prévue</span>
              <span class="detail-value">{{ orderData.delivery.expectedDate }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Articles commandés -->
    <div class="items-section">
      <div class="items-card">
        <h3 class="section-title">Articles commandés</h3>
        <div class="table-container">
          <table class="items-table">
            <thead>
              <tr>
                <th>Référence</th>
                <th>Produit</th>
                <th>Prix unitaire</th>
                <th>Quantité</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in orderData.items" :key="item.reference">
                <td class="item-reference">{{ item.reference }}</td>
                <td class="item-product">{{ item.product }}</td>
                <td class="item-price">{{ formatCurrency(item.unitPrice) }}</td>
                <td class="item-quantity">{{ item.quantity }}</td>
                <td class="item-total">{{ formatCurrency(item.total) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

export default {
  name: 'OrderDetailView',
  setup() {
    const router = useRouter()
    const route = useRoute()

    const orderId = computed(() => route.params.id || '00006')

    // Données de la commande selon la maquette
    const orderData = ref({
      client: {
        name: 'Dupont SAS',
        contact: 'contact@dupont-sas.fr',
        phone: '01 23 45 67 89',
        address: '7 rue des Entrepreneurs, 75015 Paris'
      },
      details: {
        date: '30 mai 2023',
        status: 'En cours',
        totalHT: 1250.00,
        tva: 250.00,
        totalTTC: 1500.00
      },
      delivery: {
        mode: 'Standard',
        address: 'Identique au client',
        status: 'En préparation',
        expectedDate: '5 juin 2023'
      },
      items: [
        {
          reference: 'A2A',
          product: 'Article B',
          unitPrice: 125.00,
          quantity: 4,
          total: 500.00
        },
        {
          reference: 'A3A',
          product: 'Article C',
          unitPrice: 75.00,
          quantity: 5,
          total: 375.00
        },
        {
          reference: 'A8A',
          product: 'Article D',
          unitPrice: 150.00,
          quantity: 2,
          total: 300.00
        },
        {
          reference: 'A6A',
          product: 'Article E',
          unitPrice: 75.00,
          quantity: 1,
          total: 75.00
        }
      ]
    })

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }

    const getStatusClass = (status) => {
      const statusClasses = {
        'En attente': 'status-pending',
        'En cours': 'status-progress',
        'Expédiée': 'status-shipped',
        'En préparation': 'status-preparing'
      }
      return statusClasses[status] || 'status-default'
    }

    const goBack = () => {
      router.push('/orders')
    }

    return {
      orderId,
      orderData,
      formatCurrency,
      getStatusClass,
      goBack
    }
  }
}
</script>

<style scoped>
.order-detail-page {
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
.order-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.info-section {
  display: flex;
  flex-direction: column;
}

.info-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
  height: fit-content;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1.5rem 0;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #F1F5F9;
}

/* INFORMATIONS CLIENT */
.client-info {
  display: flex;
  flex-direction: column;
}

.client-name {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.5rem 0;
}

.client-contact,
.client-phone,
.client-address {
  font-size: 14px;
  color: #64748B;
  margin: 0 0 0.25rem 0;
  line-height: 1.5;
}

/* DÉTAILS COMMANDE */
.order-details,
.delivery-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.detail-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.5rem 0;
}

.detail-row:not(:last-child) {
  border-bottom: 1px solid #F8FAFC;
}

.detail-label {
  font-size: 14px;
  color: #64748B;
  font-weight: 500;
}

.detail-value {
  font-size: 14px;
  color: #334155;
  font-weight: 500;
  text-align: right;
}

.detail-value.amount {
  font-weight: 600;
  color: #0F172A;
}

.total-row {
  border-top: 2px solid #E2E8F0;
  padding-top: 0.75rem;
  margin-top: 0.5rem;
}

.total-row .detail-label,
.total-row .detail-value {
  font-size: 16px;
  font-weight: 700;
  color: #0F172A;
}

/* BADGES DE STATUT */
.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
  text-transform: capitalize;
}

.status-pending {
  background: #FEF3C7;
  color: #92400E;
}

.status-progress {
  background: #DBEAFE;
  color: #1E40AF;
}

.status-shipped {
  background: #D1FAE5;
  color: #047857;
}

.status-preparing {
  background: #E0E7FF;
  color: #4338CA;
}

.delivery-status {
  background: #FEF3C7;
  color: #92400E;
}

/* ARTICLES COMMANDÉS */
.items-section {
  margin-top: 2rem;
}

.items-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
}

.table-container {
  overflow-x: auto;
  margin-top: 1rem;
}

.items-table {
  width: 100%;
  border-collapse: collapse;
}

.items-table th {
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

.items-table th:nth-child(3),
.items-table th:nth-child(4),
.items-table th:nth-child(5) {
  text-align: right;
}

.items-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
}

.items-table tr:hover {
  background: #F8FAFC;
}

.item-reference {
  font-weight: 600;
  color: #0F172A;
}

.item-price,
.item-quantity,
.item-total {
  text-align: right;
  font-weight: 500;
}

.item-total {
  font-weight: 600;
  color: #0F172A;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
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
    justify-content: flex-end;
  }

  .order-content {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .header-actions {
    flex-direction: column;
    width: 100%;
  }

  .action-button {
    justify-content: center;
  }

  .items-table {
    font-size: 12px;
  }

  .items-table th,
  .items-table td {
    padding: 0.5rem;
  }

  .detail-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }

  .detail-value {
    text-align: left;
  }
}

@media (max-width: 640px) {

  .items-table th:nth-child(3),
  .items-table td:nth-child(3) {
    display: none;
  }
}
</style>