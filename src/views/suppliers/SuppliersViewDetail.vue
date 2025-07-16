<template>
  <div class="supplier-detail-page">
    <!-- En-tête avec boutons -->
    <div class="page-header">
      <div class="header-left">
        <button role="button" aria-label="Retour" class="back-button" @click="goBack">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="15,18 9,12 15,6" />
          </svg>
          <span>Retour</span>
        </button>
        <h2 class="page-title">Détail du fournisseur</h2>
      </div>
      <div class="header-actions">
        <button
          role="button"
          aria-label="Imprimer le fournisseur"
          class="print-button"
          @click="printSupplier"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="6,9 6,2 18,2 18,9" />
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
            <rect x="6" y="14" width="12" height="8" />
          </svg>
          <span>Imprimer</span>
        </button>
        <button
          role="button"
          aria-label="Modifier le fournisseur"
          class="edit-button"
          @click="showEditModal = true"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
          </svg>
          <span>Modifier</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loader"></div>
      <p>Chargement des informations...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <circle cx="12" cy="12" r="10" />
        <line x1="12" y1="8" x2="12" y2="13" />
        <line x1="12" y1="16" x2="12" y2="17" />
      </svg>
      <p class="error-message">{{ error }}</p>
      <button role="button" aria-label="Réessayer" @click="fetchSupplier" class="retry-button">
        Réessayer
      </button>
    </div>

    <!-- Content -->
    <template v-else-if="supplier">
      <!-- Informations principales -->
      <div class="info-section">
        <div class="info-card">
          <div class="supplier-header">
            <div class="supplier-logo" :style="{ backgroundColor: getSupplierColor() }">
              {{ getSupplierInitials() }}
            </div>
            <div class="supplier-main">
              <h2 class="supplier-name">{{ supplier.nom }}</h2>
              <p class="supplier-since">
                Fournisseur actif depuis
                {{ getYearFromDate(supplier.date_creation) }}
              </p>
              <span v-if="supplier.actif" class="status-badge active">Actif</span>
              <span v-else class="status-badge inactive">Inactif</span>
            </div>
          </div>

          <div class="info-grid">
            <div class="info-item">
              <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path
                  d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                />
                <polyline points="22,6 12,13 2,6" />
              </svg>
              <div class="info-content">
                <span class="info-label">EMAIL</span>
                <span class="info-value">{{ supplier.email }}</span>
              </div>
            </div>

            <div class="info-item">
              <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path
                  d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                />
              </svg>
              <div class="info-content">
                <span class="info-label">TÉLÉPHONE</span>
                <span class="info-value">{{ formatPhone(supplier.telephone) }}</span>
              </div>
            </div>

            <div class="info-item">
              <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                <circle cx="12" cy="10" r="3" />
              </svg>
              <div class="info-content">
                <span class="info-label">ADRESSE</span>
                <span class="info-value">
                  {{ supplier.adresse }}<br />
                  {{ supplier.code_postal }} {{ supplier.ville }},
                  {{ supplier.pays }}
                </span>
              </div>
            </div>

            <div v-if="supplier.site_web" class="info-item">
              <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10" />
                <line x1="2" y1="12" x2="22" y2="12" />
                <path
                  d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"
                />
              </svg>
              <div class="info-content">
                <span class="info-label">SITE WEB</span>
                <a :href="supplier.site_web" target="_blank" class="info-value link">{{
                  supplier.site_web
                }}</a>
              </div>
            </div>
          </div>

          <div class="contact-section">
            <h2 class="section-title">Contact principal</h2>
            <div class="contact-person">
              <div class="contact-avatar">{{ getContactInitials() }}</div>
              <div class="contact-info">
                <p class="contact-name">{{ supplier.contact_prenom }} {{ supplier.contact_nom }}</p>
                <p class="contact-role">Contact commercial</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-card">
          <h2 class="card-title">Informations commerciales</h2>

          <div class="stats-grid">
            <div class="stat-item">
              <p class="stat-label">Note qualité</p>
              <div class="rating-display">
                <span class="rating-value">{{ supplier.note_qualite || "N/A" }}</span>
                <div v-if="supplier.note_qualite" class="stars-mini">
                  <span
                    v-for="i in 5"
                    :key="i"
                    class="star-container"
                    :style="{
                      '--fill-width': getStarFillWidth(i, parseFloat(supplier.note_qualite)),
                    }"
                  >
                    <span class="star-background">★</span>
                    <span class="star-fill">★</span>
                  </span>
                </div>
              </div>
            </div>

            <div class="stat-item">
              <p class="stat-label">Délai de livraison</p>
              <p class="stat-value">
                {{ supplier.delai_livraison }} <span class="unit">jours</span>
              </p>
            </div>

            <div class="stat-item">
              <p class="stat-label">ID Fournisseur</p>
              <p class="stat-value">
                {{ supplier.id_fournisseur }}
              </p>
            </div>
          </div>

          <div class="metrics-section">
            <div class="metric-item">
              <span class="metric-label">Conditions de paiement</span>
              <span class="metric-value">{{
                supplier.conditions_paiement || "Non spécifiées"
              }}</span>
            </div>

            <div class="metric-item">
              <span class="metric-label">Date de création</span>
              <span class="metric-value">{{ formatDate(supplier.date_creation) }}</span>
            </div>

            <div class="metric-item">
              <span class="metric-label">Dernière modification</span>
              <span class="metric-value">{{ formatDate(supplier.date_modification) }}</span>
            </div>
          </div>

          <!-- Stats supplémentaires (simulées pour l'instant) -->
          <div class="additional-stats">
            <h3 class="subsection-title">Statistiques d'activité</h3>
            <div class="mini-stats">
              <div class="mini-stat">
                <span class="mini-label">Commandes en cours</span>
                <span class="mini-value">{{ Math.floor(Math.random() * 10) + 1 }}</span>
              </div>
              <div class="mini-stat">
                <span class="mini-label">Articles fournis</span>
                <span class="mini-value">{{ Math.floor(Math.random() * 50) + 10 }}</span>
              </div>
              <div class="mini-stat">
                <span class="mini-label">CA annuel</span>
                <span class="mini-value">{{ formatCurrency(Math.random() * 100000 + 10000) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Produits -->
      <div class="products-section-card">
        <div class="table-header">
          <h2 class="table-title">Produits fournis</h2>
          <button
            role="button"
            aria-label="Exporter les produits"
            class="export-button"
            @click="exportProducts"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
              <polyline points="7,10 12,15 17,10" />
              <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
            Exporter
          </button>
        </div>

        <div v-if="loadingProducts" class="loading-mini">
          <div class="loader-mini"></div>
          <p>Chargement des produits...</p>
        </div>

        <div v-else-if="products.length === 0" class="empty-state">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path
              d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"
            />
            <polyline points="3.27,6.96 12,12.01 20.73,6.96" />
            <line x1="12" y1="22.08" x2="12" y2="12" />
          </svg>
          <p>Aucun produit fourni par ce fournisseur</p>
        </div>

        <div v-else class="table-container">
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
              <tr v-for="product in products" :key="product.id_article">
                <td class="reference">
                  {{ product.reference || `ART${product.id_article}` }}
                </td>
                <td>{{ product.nom }}</td>
                <td>{{ product.categorie || "Non catégorisé" }}</td>
                <td class="price">{{ formatCurrency(product.prix) }}</td>
                <td
                  class="stock"
                  :class="{
                    'low-stock': product.quantite_stock <= product.seuil_alerte,
                  }"
                >
                  {{ product.quantite_stock }}
                </td>
                <td class="threshold">{{ product.seuil_alerte }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- Modal Modifier fournisseur -->
    <div v-if="showEditModal" class="modal-overlay" @click="showEditModal = false">
      <div class="modal-content modal-edit" @click.stop>
        <div class="modal-header">
          <h3>Modifier le fournisseur</h3>
          <button @click="showEditModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="updateSupplier" v-if="editForm">
            <div class="form-grid">
              <!-- Informations générales -->
              <div class="form-section">
                <h4 class="form-subtitle">Informations générales</h4>

                <div class="form-group">
                  <label for="nom" class="form-label">Nom du fournisseur *</label>
                  <input
                    id="nom"
                    v-model="editForm.nom"
                    type="text"
                    class="form-input"
                    required
                    placeholder="Ex: Textiles Premium SA"
                  />
                </div>
              </div>

              <!-- Contact -->
              <div class="form-section">
                <h4 class="form-subtitle">Contact principal</h4>

                <div class="form-row">
                  <div class="form-group">
                    <label for="contact_prenom" class="form-label">Prénom *</label>
                    <input
                      id="contact_prenom"
                      v-model="editForm.contact_prenom"
                      type="text"
                      class="form-input"
                      required
                      placeholder="Ex: Marie"
                    />
                  </div>

                  <div class="form-group">
                    <label for="contact_nom" class="form-label">Nom *</label>
                    <input
                      id="contact_nom"
                      v-model="editForm.contact_nom"
                      type="text"
                      class="form-input"
                      required
                      placeholder="Ex: Dupont"
                    />
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="email" class="form-label">Email *</label>
                    <input
                      id="email"
                      v-model="editForm.email"
                      type="email"
                      class="form-input"
                      required
                      placeholder="Ex: contact@fournisseur.com"
                    />
                  </div>

                  <div class="form-group">
                    <label for="telephone" class="form-label">Téléphone *</label>
                    <input
                      id="telephone"
                      v-model="editForm.telephone"
                      type="tel"
                      class="form-input"
                      required
                      placeholder="Ex: 0612345678"
                    />
                  </div>
                </div>
              </div>

              <!-- Adresse -->
              <div class="form-section full-width">
                <h4 class="form-subtitle">Adresse</h4>

                <div class="form-group">
                  <label for="adresse" class="form-label">Adresse *</label>
                  <input
                    id="adresse"
                    v-model="editForm.adresse"
                    type="text"
                    class="form-input"
                    required
                    placeholder="Ex: 123 rue de la République"
                  />
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="code_postal" class="form-label">Code postal *</label>
                    <input
                      id="code_postal"
                      v-model="editForm.code_postal"
                      type="text"
                      class="form-input"
                      required
                      placeholder="Ex: 75001"
                    />
                  </div>

                  <div class="form-group">
                    <label for="ville" class="form-label">Ville *</label>
                    <input
                      id="ville"
                      v-model="editForm.ville"
                      type="text"
                      class="form-input"
                      required
                      placeholder="Ex: Paris"
                    />
                  </div>

                  <div class="form-group">
                    <label for="pays" class="form-label">Pays *</label>
                    <input
                      id="pays"
                      v-model="editForm.pays"
                      type="text"
                      class="form-input"
                      required
                      placeholder="Ex: France"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Error message -->
            <div v-if="updateError" class="form-error">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
              </svg>
              <span>{{ updateError }}</span>
            </div>

            <!-- Success message -->
            <div v-if="updateSuccess" class="form-success">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
              <span>Fournisseur mis à jour avec succès !</span>
            </div>

            <!-- Modal actions -->
            <div class="modal-actions">
              <button type="button" class="modal-btn secondary" @click="showEditModal = false">
                Annuler
              </button>
              <button type="submit" class="modal-btn primary" :disabled="submitting">
                <span v-if="submitting" class="loader-inline"></span>
                <span v-else>Enregistrer les modifications</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted, reactive } from "vue";
  import { useRouter, useRoute } from "vue-router";
  import { VITE_API_URL } from "@/constants/constants.js";

  export default {
    name: "SupplierDetailView",
    metaInfo: {
      meta: [
        {
          name: "description",
          content:
            "Détails du fournisseur - Visualisez et gérez les informations de votre fournisseur.",
        },
      ],
    },
    setup() {
      const router = useRouter();
      const route = useRoute();
      const supplier = ref(null);
      const products = ref([]);
      const loading = ref(true);
      const loadingProducts = ref(true);
      const error = ref(null);
      const showEditModal = ref(false);
      const submitting = ref(false);
      const updateError = ref(null);
      const updateSuccess = ref(false);

      // Formulaire d'édition
      const editForm = reactive({
        id_fournisseur: "",
        nom: "",
        contact_nom: "",
        contact_prenom: "",
        email: "",
        telephone: "",
        adresse: "",
        ville: "",
        code_postal: "",
        pays: "",
      });

      // Récupérer l'ID du fournisseur depuis l'URL
      const supplierId = computed(() => {
        return route.params.id || route.params.supplierId || route.query.id;
      });

      // Fonction pour récupérer les informations du fournisseur
      const fetchSupplier = async () => {
        loading.value = true;
        error.value = null;

        try {
          const response = await fetch(VITE_API_URL + "get_table?table=fournisseur", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
          }

          const data = await response.json();

          if (data.success && data.data) {
            // Trouver le fournisseur par ID
            const foundSupplier = data.data.find(f => f.id_fournisseur == supplierId.value);
            if (foundSupplier) {
              supplier.value = foundSupplier;
              // Initialiser le formulaire avec les données actuelles
              Object.assign(editForm, {
                id_fournisseur: foundSupplier.id_fournisseur,
                nom: foundSupplier.nom || "",
                contact_nom: foundSupplier.contact_nom || "",
                contact_prenom: foundSupplier.contact_prenom || "",
                email: foundSupplier.email || "",
                telephone: foundSupplier.telephone || "",
                adresse: foundSupplier.adresse || "",
                ville: foundSupplier.ville || "",
                code_postal: foundSupplier.code_postal || "",
                pays: foundSupplier.pays || "",
              });
            } else {
              throw new Error("Fournisseur introuvable");
            }
          } else {
            throw new Error("Format de données invalide");
          }
        } catch (err) {
          console.error("Erreur lors du chargement du fournisseur:", err);
          error.value = "Impossible de charger les informations du fournisseur.";
        } finally {
          loading.value = false;
        }
      };

      // Fonction pour récupérer les produits du fournisseur
      const fetchProducts = async () => {
        loadingProducts.value = true;

        try {
          const response = await fetch(VITE_API_URL + "get_table?table=article", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
          }

          const data = await response.json();

          if (data.success && data.data) {
            // Filtrer les articles par fournisseur (à adapter selon votre structure)
            // Pour l'instant, on simule avec des données aléatoires
            products.value = data.data.slice(0, 5);
          }
        } catch (err) {
          console.error("Erreur lors du chargement des produits:", err);
        } finally {
          loadingProducts.value = false;
        }
      };

      // Fonction pour mettre à jour le fournisseur
      const updateSupplier = async () => {
        submitting.value = true;
        updateError.value = null;
        updateSuccess.value = false;

        try {
          // Préparer les données du formulaire
          const formData = new URLSearchParams();
          Object.keys(editForm).forEach(key => {
            formData.append(key, editForm[key]);
          });

          const response = await fetch(VITE_API_URL + "update_fournisseur", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
            body: formData,
          });

          const data = await response.json();

          if (data.success) {
            updateSuccess.value = true;
            // Mettre à jour les données locales
            Object.assign(supplier.value, editForm);
            // Fermer la modale après 2 secondes
            setTimeout(() => {
              showEditModal.value = false;
              updateSuccess.value = false;
            }, 2000);
          } else {
            throw new Error(data.message || "Erreur lors de la mise à jour");
          }
        } catch (err) {
          console.error("Erreur lors de la mise à jour:", err);
          updateError.value = err.message || "Impossible de mettre à jour le fournisseur.";
        } finally {
          submitting.value = false;
        }
      };

      // Fonctions utilitaires
      const formatCurrency = amount => {
        return new Intl.NumberFormat("fr-FR", {
          style: "currency",
          currency: "EUR",
        }).format(amount || 0);
      };

      const formatDate = dateString => {
        if (!dateString) return "N/A";
        const date = new Date(dateString);
        return date.toLocaleDateString("fr-FR");
      };

      const formatPhone = phone => {
        if (!phone) return "Non renseigné";
        const cleaned = phone.toString().replace(/\D/g, "");
        if (cleaned.length === 10) {
          return cleaned.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, "$1 $2 $3 $4 $5");
        }
        return phone;
      };

      const getSupplierInitials = () => {
        if (!supplier.value) return "";
        const words = supplier.value.nom.split(" ");
        return words
          .map(w => w[0])
          .join("")
          .toUpperCase()
          .substring(0, 2);
      };

      const getContactInitials = () => {
        if (!supplier.value) return "";
        const firstInitial = supplier.value.contact_prenom ? supplier.value.contact_prenom[0] : "";
        const lastInitial = supplier.value.contact_nom ? supplier.value.contact_nom[0] : "";
        return (firstInitial + lastInitial).toUpperCase();
      };

      const getSupplierColor = () => {
        const colors = ["#00B8D4", "#2563EB", "#059669", "#D97706", "#7C3AED", "#DC2626"];
        const id = supplier.value?.id_fournisseur || 0;
        return colors[id % colors.length];
      };

      const getYearFromDate = dateString => {
        if (!dateString) return "N/A";
        return new Date(dateString).getFullYear();
      };

      const goBack = () => {
        router.push("/suppliers");
      };

      const printSupplier = () => {
        window.print();
      };

      const exportProducts = () => {
        if (!products.value.length) return;

        const csv = [
          ["Référence", "Produit", "Catégorie", "Prix unitaire", "Stock", "Seuil"],
          ...products.value.map(p => [
            p.reference || `ART${p.id_article}`,
            p.nom,
            p.categorie || "Non catégorisé",
            p.prix,
            p.quantite_stock,
            p.seuil_alerte,
          ]),
        ]
          .map(row => row.join(","))
          .join("\n");

        const blob = new Blob([csv], { type: "text/csv" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `produits_${supplier.value.nom.replace(/\s+/g, "_")}.csv`;
        a.click();
        URL.revokeObjectURL(url);
      };

      // Charger les données au montage
      onMounted(() => {
        if (!supplierId.value) {
          // Charger quand même les données pour récupérer le premier fournisseur
          fetchAllSuppliers();
        } else {
          fetchSupplier();
          fetchProducts();
        }
      });

      // Fonction pour récupérer tous les fournisseurs et sélectionner le premier
      const fetchAllSuppliers = async () => {
        loading.value = true;
        error.value = null;

        try {
          const response = await fetch(VITE_API_URL + "get_table?table=fournisseur", {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          });

          if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
          }

          const data = await response.json();

          if (data.success && data.data && data.data.length > 0) {
            // Prendre le premier fournisseur
            supplier.value = data.data[0];
            // Initialiser le formulaire
            Object.assign(editForm, {
              id_fournisseur: supplier.value.id_fournisseur,
              nom: supplier.value.nom || "",
              contact_nom: supplier.value.contact_nom || "",
              contact_prenom: supplier.value.contact_prenom || "",
              email: supplier.value.email || "",
              telephone: supplier.value.telephone || "",
              adresse: supplier.value.adresse || "",
              ville: supplier.value.ville || "",
              code_postal: supplier.value.code_postal || "",
              pays: supplier.value.pays || "",
            });
            // Charger aussi les produits
            fetchProducts();
          } else {
            throw new Error("Aucun fournisseur trouvé");
          }
        } catch (err) {
          console.error("Erreur lors du chargement des fournisseurs:", err);
          error.value = "Impossible de charger les informations du fournisseur.";
        } finally {
          loading.value = false;
        }
      };

      return {
        supplier,
        products,
        loading,
        loadingProducts,
        error,
        showEditModal,
        editForm,
        submitting,
        updateError,
        updateSuccess,
        supplierId,
        fetchSupplier,
        fetchProducts,
        fetchAllSuppliers,
        updateSupplier,
        formatCurrency,
        formatDate,
        formatPhone,
        getSupplierInitials,
        getContactInitials,
        getSupplierColor,
        getYearFromDate,
        goBack,
        printSupplier,
        exportProducts,
      };
    },
    methods: {
      getStarFillWidth(starIndex, rating) {
        const fillPercentage = Math.min(Math.max(rating - (starIndex - 1), 0), 1);
        return `${fillPercentage * 100}%`;
      },
    },
  };
</script>

<style scoped>
  .supplier-detail-page {
    padding: 0;
    font-family: "Inter", sans-serif;
  }

  /* LOADING & ERROR */
  .loading-container,
  .error-container {
    text-align: center;
    padding: 4rem 2rem;
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

  .loader-mini {
    width: 24px;
    height: 24px;
    border: 2px solid #f1f5f9;
    border-top-color: #00b8d4;
    border-radius: 50%;
    margin: 0 auto 0.5rem;
    animation: spin 1s linear infinite;
  }

  .loader-inline {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #f1f5f9;
    border-top-color: white;
    border-radius: 50%;
    margin-right: 0.5rem;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  .loading-mini {
    text-align: center;
    padding: 2rem;
    color: #64748b;
    font-size: 14px;
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
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .back-button:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
  }

  .back-button svg {
    width: 16px;
    height: 16px;
    stroke-width: 2;
  }

  .page-title {
    font-size: 24px;
    font-weight: 600;
    color: #0f172a;
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
    color: #64748b;
    border: 1px solid #e2e8f0;
  }

  .print-button:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
  }

  .edit-button {
    background: #00b8d4;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0, 184, 212, 0.3);
    font-size: 14px;
    font-weight: 500;
  }

  .edit-button:hover {
    background: #0891a6;
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
  .stats-card,
  .products-section-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
  }

  .supplier-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .supplier-logo {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    font-weight: 700;
    flex-shrink: 0;
  }

  .supplier-main {
    flex: 1;
  }

  .supplier-name {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.25rem 0;
  }

  .supplier-since {
    font-size: 14px;
    color: #64748b;
    margin: 0 0 0.75rem 0;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 500;
  }

  .status-badge.active {
    background: #d1fae5;
    color: #047857;
  }

  .status-badge.inactive {
    background: #fef2f2;
    color: #dc2626;
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
    color: #64748b;
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
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .info-value {
    font-size: 14px;
    color: #0f172a;
    font-weight: 500;
    line-height: 1.4;
  }

  .info-value.link {
    color: #00b8d4;
    text-decoration: none;
    transition: color 0.2s ease;
  }

  .info-value.link:hover {
    color: #0891a6;
    text-decoration: underline;
  }

  /* CONTACT */
  .contact-section {
    border-top: 1px solid #e2e8f0;
    padding-top: 1.5rem;
  }

  .section-title {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
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
    background: #00b8d4;
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
    color: #0f172a;
    margin: 0 0 0.25rem 0;
  }

  .contact-role {
    font-size: 12px;
    color: #64748b;
    margin: 0;
  }

  /* STATISTIQUES */
  .card-title {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 1.5rem 0;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .stat-item {
    text-align: center;
  }

  .stat-label {
    font-size: 1rem;
    color: #64748b;
    font-weight: 500;
    margin: 0 0 0.5rem 0;
  }

  .stat-value {
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1;
  }

  .stat-value .unit {
    font-size: 16px;
    font-weight: 500;
    color: #64748b;
  }

  .metrics-section {
    display: flex;
    flex-direction: column;
  }

  .metric-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
  }

  .metric-item:last-child {
    border-bottom: none;
  }

  .metric-label {
    font-size: 14px;
    color: #64748b;
    font-weight: 500;
  }

  .metric-value {
    font-size: 14px;
    color: #0f172a;
    font-weight: 600;
    text-align: right;
  }

  /* RATING */
  .rating-display {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
  }

  .rating-value {
    font-weight: 600;
    color: #0f172a;
  }

  .stars-mini {
    display: flex;
    gap: 1px;
  }

  .star-container {
    position: relative;
    display: inline-block;
    font-size: 12px;
  }

  .star-container span {
    font-size: 1.25rem;
    -webkit-user-drag: none;
    -moz-user-drag: none;
    -ms-user-drag: none;
  }

  .star-background {
    color: #e2e8f0;
  }

  .star-fill {
    position: absolute;
    top: 0;
    left: 0;
    color: #f59e0b;
    overflow: hidden;
    width: var(--fill-width, 0%);
  }

  /* STATS SUPPLÉMENTAIRES */
  .additional-stats {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
  }

  .subsection-title {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 1rem 0;
  }

  .mini-stats {
    display: grid;
    grid-template-columns: 1fr;
  }

  .mini-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
  }

  .mini-label {
    font-size: 13px;
    color: #64748b;
  }

  .mini-value {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
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
    color: #0f172a;
    margin: 0;
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

  .table-container {
    overflow-x: auto;
  }

  .data-table {
    width: 100%;
    min-width: max-content;
    text-align: center;
  }

  .data-table th {
    background: #f8fafc;
    padding: 1rem;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #e2e8f0;
  }

  .data-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: #0f172a;
  }

  .data-table tbody tr:hover {
    background: #f8fafc;
  }

  .data-table tbody tr:last-child td {
    border-bottom: none;
  }

  .reference {
    font-weight: 600;
    color: #0f172a;
  }

  .price,
  .stock,
  .threshold {
    font-weight: 600;
    text-align: center;
  }

  .stock.low-stock {
    color: #dc2626;
    background: #fef2f2;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    background-clip: content-box;
  }

  .empty-state {
    text-align: center;
    padding: 3rem;
    color: #64748b;
  }

  .empty-state svg {
    width: 48px;
    height: 48px;
    margin: 0 auto 1rem;
    color: #cbd5e1;
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
    padding: 1rem;
  }

  .modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 800px;
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
    color: #334155;
  }

  .modal-close svg {
    width: 20px;
    height: 20px;
    stroke-width: 2;
  }

  .modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    max-height: calc(90vh - 120px);
  }

  /* FORMULAIRE */
  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
  }

  .form-section {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
  }

  .form-section.full-width {
    grid-column: 1 / -1;
  }

  .form-subtitle {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 1rem 0;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .form-group {
    margin-bottom: 1rem;
  }

  .form-group:last-child {
    margin-bottom: 0;
  }

  .form-label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    margin-bottom: 0.5rem;
  }

  .form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 14px;
    color: #0f172a;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    transition: all 0.2s ease;
  }

  .form-input:focus {
    outline: none;
    border-color: #00b8d4;
    box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
  }

  .form-input::placeholder {
    color: #94a3b8;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .form-error,
  .form-success {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin-top: 1rem;
  }

  .form-error {
    background: #fef2f2;
    color: #dc2626;
  }

  .form-success {
    background: #f0fdf4;
    color: #047857;
  }

  .form-error svg,
  .form-success svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
  }

  .modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
    margin-top: 1.5rem;
  }

  .modal-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .modal-btn.primary {
    background: #00b8d4;
    color: white;
    min-width: 180px;
  }

  .modal-btn.primary:hover:not(:disabled) {
    background: #0891a6;
  }

  .modal-btn.primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }

  .modal-btn.secondary {
    background: #f1f5f9;
    color: #64748b;
  }

  .modal-btn.secondary:hover {
    background: #e2e8f0;
    color: #334155;
  }

  /* RESPONSIVE */
  @media (max-width: 1024px) {
    .info-section {
      grid-template-columns: 1fr;
    }

    .stats-grid {
      grid-template-columns: repeat(3, 1fr);
    }

    .form-grid {
      grid-template-columns: 1fr;
    }

    .form-row {
      grid-template-columns: 1fr;
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

    .data-table {
      font-size: 12px;
    }

    .modal-actions {
      flex-direction: column;
    }

    .modal-btn {
      width: 100%;
    }
  }

  @media (max-width: 640px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }
  }

  @media print {
    .back-button,
    .print-button,
    .edit-button,
    .export-button {
      display: none;
    }

    .products-section-card {
      box-shadow: none;
      border: 1px solid #e2e8f0;
      margin-top: 2rem;
      page-break-inside: avoid;
    }
  }
</style>
