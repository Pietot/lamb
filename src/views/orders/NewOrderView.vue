<template>
  <div class="new-order-page">
    <!-- En-tête avec bouton retour -->
    <div class="page-header">
      <div class="header-left">
        <button class="back-button" @click="goBack">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="15,18 9,12 15,6" />
          </svg>
          Retour
        </button>
        <h1 class="page-title">Nouvelle commande</h1>
      </div>
    </div>

    <!-- Formulaire de commande -->
    <form @submit.prevent="saveOrder" class="order-form">
      <!-- Informations client -->
      <div class="form-section">
        <div class="section-card">
          <h3 class="section-title">Informations client</h3>

          <div class="client-search">
            <div class="search-container">
              <input v-model="clientSearch" type="text" placeholder="Rechercher un client..."
                class="client-search-input" @input="searchClients" @focus="showClientDropdown = true" />
              <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
            </div>

            <!-- Dropdown clients -->
            <div v-if="showClientDropdown && filteredClients.length" class="client-dropdown">
              <div v-for="client in filteredClients" :key="client.id" class="client-option"
                @click="selectClient(client)">
                <div class="client-name">{{ client.name }}</div>
                <div class="client-contact">{{ client.contact }}</div>
              </div>
            </div>
          </div>

          <!-- Client sélectionné -->
          <div v-if="selectedClient" class="selected-client">
            <div class="client-card">
              <button type="button" class="remove-client" @click="removeClient">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
              <div class="client-info">
                <h4>{{ selectedClient.name }}</h4>
                <p>{{ selectedClient.contact }}</p>
                <p>{{ selectedClient.phone }}</p>
                <p>{{ selectedClient.address }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Détails de la commande -->
      <div class="form-section">
        <div class="section-card">
          <h3 class="section-title">Détails de la commande</h3>

          <div class="form-grid">
            <div class="form-field">
              <label class="field-label">Date</label>
              <input v-model="orderDetails.date" type="date" class="form-input" required />
            </div>

            <div class="form-field">
              <label class="field-label">Mode de livraison</label>
              <select v-model="orderDetails.deliveryMode" class="form-select" required>
                <option value="">Sélectionner</option>
                <option value="Standard">Standard</option>
                <option value="Express">Express</option>
                <option value="Urgente">Urgente</option>
              </select>
            </div>

            <div class="form-field">
              <label class="field-label">Mode de paiement</label>
              <select v-model="orderDetails.paymentMode" class="form-select" required>
                <option value="">Sélectionner</option>
                <option value="Virement bancaire">Virement bancaire</option>
                <option value="Carte bancaire">Carte bancaire</option>
                <option value="Chèque">Chèque</option>
                <option value="Espèces">Espèces</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Articles -->
      <div class="form-section">
        <div class="section-card">
          <div class="section-header">
            <h3 class="section-title">Articles</h3>
            <button type="button" class="add-article-button" @click="showAddArticleModal = true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="16" />
                <line x1="8" y1="12" x2="16" y2="12" />
              </svg>
              Ajouter un article
            </button>
          </div>

          <!-- Tableau des articles -->
          <div class="articles-table-container">
            <table class="articles-table">
              <thead>
                <tr>
                  <th>Référence</th>
                  <th>Produit</th>
                  <th>Prix unitaire</th>
                  <th>Quantité</th>
                  <th>Total</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in orderItems" :key="index">
                  <td class="item-reference">{{ item.reference }}</td>
                  <td class="item-product">{{ item.product }}</td>
                  <td class="item-price">{{ formatCurrency(item.unitPrice) }}</td>
                  <td class="item-quantity">
                    <input v-model.number="item.quantity" type="number" min="1" class="quantity-input"
                      @input="updateItemTotal(index)" />
                  </td>
                  <td class="item-total">{{ formatCurrency(item.total) }}</td>
                  <td class="item-actions">
                    <button type="button" class="remove-item-button" @click="removeItem(index)">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polyline points="3,6 5,6 21,6" />
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                      </svg>
                    </button>
                  </td>
                </tr>
                <tr v-if="orderItems.length === 0">
                  <td colspan="6" class="empty-table">
                    Aucun article ajouté
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Totaux -->
          <div class="order-totals">
            <div class="totals-row">
              <span class="total-label">Sous-total</span>
              <span class="total-value">{{ formatCurrency(subtotal) }}</span>
            </div>
            <div class="totals-row">
              <span class="total-label">TVA (20%)</span>
              <span class="total-value">{{ formatCurrency(tvaAmount) }}</span>
            </div>
            <div class="totals-row total-final">
              <span class="total-label">Total</span>
              <span class="total-value">{{ formatCurrency(totalAmount) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="form-actions">
        <button type="button" class="cancel-button" @click="goBack">
          Annuler
        </button>
        <button type="submit" class="save-button" :disabled="!canSave">
          Enregistrer la commande
        </button>
      </div>
    </form>

    <!-- Modal Ajouter un article -->
    <div v-if="showAddArticleModal" class="modal-overlay" @click="showAddArticleModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Ajouter un article</h3>
          <button @click="showAddArticleModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-field">
            <label class="field-label">Article</label>
            <select v-model="newArticle.reference" class="form-select" @change="updateNewArticle">
              <option value="">Sélectionner un article</option>
              <option v-for="article in availableArticles" :key="article.reference" :value="article.reference">
                {{ article.reference }} - {{ article.product }}
              </option>
            </select>
          </div>
          <div class="form-field">
            <label class="field-label">Quantité</label>
            <input v-model.number="newArticle.quantity" type="number" min="1" class="form-input" placeholder="1" />
          </div>
          <div class="modal-actions">
            <button type="button" class="cancel-button" @click="showAddArticleModal = false">
              Annuler
            </button>
            <button type="button" class="add-button" @click="addArticle" :disabled="!newArticle.reference">
              Ajouter
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, reactive } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'NewOrderView',
  setup() {
    const router = useRouter()

    const clientSearch = ref('')
    const showClientDropdown = ref(false)
    const selectedClient = ref(null)
    const showAddArticleModal = ref(false)

    const orderDetails = reactive({
      date: new Date().toISOString().split('T')[0],
      deliveryMode: 'Standard',
      paymentMode: ''
    })

    const orderItems = ref([
      {
        reference: 'A2A',
        product: 'Article B',
        unitPrice: 125.00,
        quantity: 2,
        total: 250.00
      }
    ])

    const newArticle = reactive({
      reference: '',
      quantity: 1
    })

    // Données clients
    const clients = ref([
      {
        id: 1,
        name: 'Dupont SAS',
        contact: 'contact@dupont-sas.fr',
        phone: '01 23 45 67 89',
        address: '7 rue des Entrepreneurs, 75015 Paris'
      },
      {
        id: 2,
        name: 'Martin & Cie',
        contact: 'info@martin-cie.fr',
        phone: '01 34 56 78 90',
        address: '12 avenue de la République, 69001 Lyon'
      },
      {
        id: 3,
        name: 'Bernard FR',
        contact: 'contact@bernard-fr.com',
        phone: '01 45 67 89 01',
        address: '25 boulevard Haussmann, 75009 Paris'
      }
    ])

    // Articles disponibles
    const availableArticles = ref([
      { reference: 'A1A', product: 'Article A', unitPrice: 100.00 },
      { reference: 'A2A', product: 'Article B', unitPrice: 125.00 },
      { reference: 'A3A', product: 'Article C', unitPrice: 75.00 },
      { reference: 'A6A', product: 'Article E', unitPrice: 90.00 },
      { reference: 'A8A', product: 'Article D', unitPrice: 150.00 }
    ])

    const filteredClients = computed(() => {
      if (!clientSearch.value) return clients.value
      return clients.value.filter(client =>
        client.name.toLowerCase().includes(clientSearch.value.toLowerCase()) ||
        client.contact.toLowerCase().includes(clientSearch.value.toLowerCase())
      )
    })

    const subtotal = computed(() => {
      return orderItems.value.reduce((sum, item) => sum + item.total, 0)
    })

    const tvaAmount = computed(() => {
      return subtotal.value * 0.20
    })

    const totalAmount = computed(() => {
      return subtotal.value + tvaAmount.value
    })

    const canSave = computed(() => {
      return selectedClient.value && orderItems.value.length > 0 && orderDetails.paymentMode
    })

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }

    const searchClients = () => {
      showClientDropdown.value = true
    }

    const selectClient = (client) => {
      selectedClient.value = client
      clientSearch.value = client.name
      showClientDropdown.value = false
    }

    const removeClient = () => {
      selectedClient.value = null
      clientSearch.value = ''
    }

    const updateNewArticle = () => {
      const article = availableArticles.value.find(a => a.reference === newArticle.reference)
      if (article) {
        newArticle.product = article.product
        newArticle.unitPrice = article.unitPrice
      }
    }

    const addArticle = () => {
      if (!newArticle.reference) return

      const article = availableArticles.value.find(a => a.reference === newArticle.reference)
      if (article) {
        const existingIndex = orderItems.value.findIndex(item => item.reference === newArticle.reference)

        if (existingIndex >= 0) {
          orderItems.value[existingIndex].quantity += newArticle.quantity
          orderItems.value[existingIndex].total = orderItems.value[existingIndex].quantity * orderItems.value[existingIndex].unitPrice
        } else {
          orderItems.value.push({
            reference: article.reference,
            product: article.product,
            unitPrice: article.unitPrice,
            quantity: newArticle.quantity,
            total: article.unitPrice * newArticle.quantity
          })
        }

        newArticle.reference = ''
        newArticle.quantity = 1
        showAddArticleModal.value = false
      }
    }

    const removeItem = (index) => {
      orderItems.value.splice(index, 1)
    }

    const updateItemTotal = (index) => {
      const item = orderItems.value[index]
      item.total = item.unitPrice * item.quantity
    }

    const saveOrder = () => {
      // Simulation sauvegarde
      alert('Commande enregistrée avec succès !')
      router.push('/orders')
    }

    const goBack = () => {
      router.push('/orders')
    }

    return {
      clientSearch,
      showClientDropdown,
      selectedClient,
      showAddArticleModal,
      orderDetails,
      orderItems,
      newArticle,
      clients,
      availableArticles,
      filteredClients,
      subtotal,
      tvaAmount,
      totalAmount,
      canSave,
      formatCurrency,
      searchClients,
      selectClient,
      removeClient,
      updateNewArticle,
      addArticle,
      removeItem,
      updateItemTotal,
      saveOrder,
      goBack
    }
  }
}
</script>

<style scoped>
.new-order-page {
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

/* FORMULAIRE */
.order-form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-section {
  display: flex;
  flex-direction: column;
}

.section-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #F1F5F9;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 1.5rem 0;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #F1F5F9;
}

/* RECHERCHE CLIENT */
.client-search {
  position: relative;
}

.search-container {
  position: relative;
}

.client-search-input {
  width: 100%;
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  font-size: 14px;
  color: #334155;
  transition: all 0.2s ease;
}

.client-search-input:focus {
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

.client-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  z-index: 10;
  max-height: 200px;
  overflow-y: auto;
}

.client-option {
  padding: 0.75rem;
  cursor: pointer;
  border-bottom: 1px solid #F1F5F9;
  transition: background 0.2s ease;
}

.client-option:hover {
  background: #F8FAFC;
}

.client-option:last-child {
  border-bottom: none;
}

.client-name {
  font-weight: 500;
  color: #0F172A;
  margin-bottom: 0.25rem;
}

.client-contact {
  font-size: 12px;
  color: #64748B;
}

/* CLIENT SÉLECTIONNÉ */
.selected-client {
  margin-top: 1rem;
}

.client-card {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 1rem;
  position: relative;
}

.remove-client {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: none;
  border: none;
  color: #64748B;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.remove-client:hover {
  background: #E2E8F0;
  color: #334155;
}

.remove-client svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

.client-info h4 {
  font-size: 16px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.5rem 0;
}

.client-info p {
  font-size: 14px;
  color: #64748B;
  margin: 0 0 0.25rem 0;
}

/* GRILLE FORMULAIRE */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.form-field {
  display: flex;
  flex-direction: column;
}

.field-label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input,
.form-select {
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.75rem;
  font-size: 14px;
  color: #334155;
  transition: all 0.2s ease;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #00B8D4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

/* BOUTON AJOUTER ARTICLE */
.add-article-button {
  background: #00B8D4;
  color: white;
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

.add-article-button:hover {
  background: #0891A6;
  transform: translateY(-1px);
}

.add-article-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* TABLEAU ARTICLES */
.articles-table-container {
  overflow-x: auto;
  margin: 1rem 0;
}

.articles-table {
  width: 100%;
  border-collapse: collapse;
}

.articles-table th {
  background: #F8FAFC;
  text-align: left;
  padding: 0.75rem;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #E2E8F0;
}

.articles-table th:nth-child(3),
.articles-table th:nth-child(4),
.articles-table th:nth-child(5) {
  text-align: right;
}

.articles-table td {
  padding: 0.75rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
}

.articles-table tr:hover {
  background: #F8FAFC;
}

.item-reference {
  font-weight: 600;
  color: #0F172A;
}

.item-price,
.item-total {
  text-align: right;
  font-weight: 500;
}

.quantity-input {
  width: 80px;
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 4px;
  padding: 0.25rem 0.5rem;
  font-size: 14px;
  text-align: center;
}

.remove-item-button {
  background: none;
  border: none;
  color: #EF4444;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.remove-item-button:hover {
  background: #FEF2F2;
}

.remove-item-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

.empty-table {
  text-align: center;
  color: #64748B;
  font-style: italic;
  padding: 2rem;
}

/* TOTAUX */
.order-totals {
  border-top: 2px solid #E2E8F0;
  padding-top: 1rem;
  margin-top: 1rem;
}

.totals-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
}

.total-label {
  font-size: 14px;
  color: #64748B;
  font-weight: 500;
}

.total-value {
  font-size: 14px;
  color: #0F172A;
  font-weight: 600;
}

.total-final {
  border-top: 1px solid #E2E8F0;
  padding-top: 0.75rem;
  margin-top: 0.5rem;
}

.total-final .total-label,
.total-final .total-value {
  font-size: 16px;
  font-weight: 700;
}

/* ACTIONS */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 2rem;
  border-top: 2px solid #F1F5F9;
}

.cancel-button {
  background: none;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 0.75rem 1.5rem;
  font-size: 14px;
  font-weight: 500;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s ease;
}

.cancel-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.save-button {
  background: #059669;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.75rem 1.5rem;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.save-button:hover:not(:disabled) {
  background: #047857;
  transform: translateY(-1px);
}

.save-button:disabled {
  background: #94A3B8;
  cursor: not-allowed;
  transform: none;
}

.add-button {
  background: #00B8D4;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.75rem 1.5rem;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.add-button:hover:not(:disabled) {
  background: #0891A6;
}

.add-button:disabled {
  background: #94A3B8;
  cursor: not-allowed;
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
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .articles-table {
    font-size: 12px;
  }

  .articles-table th:nth-child(3),
  .articles-table td:nth-child(3) {
    display: none;
  }
}
</style>