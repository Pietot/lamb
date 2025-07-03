<template>
  <div class="admin-page">
    <!-- En-tête -->
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Administration</h1>
      </div>
      <div class="header-right">
        <button class="new-user-button" @click="showNewUserModal = true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="16" />
            <line x1="8" y1="12" x2="16" y2="12" />
          </svg>
          <span>Nouvel utilisateur</span>
        </button>
      </div>
    </div>

    <!-- Onglets de navigation -->
    <div class="tabs-section">
      <div class="tabs-nav">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          class="tab-button"
          :class="{ active: activeTab === tab.id }"
          @click="activeTab = tab.id"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Contenu des onglets -->
      <div class="tab-content">
        <!-- Onglet Utilisateurs -->
        <div v-if="activeTab === 'users'" class="users-section">
          <div class="section-header">
            <div class="search-container">
              <input
                v-model="userSearch"
                type="text"
                placeholder="Rechercher un utilisateur..."
                class="search-input"
              />
              <svg
                class="search-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
              >
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
            </div>
          </div>

          <div v-if="loading" class="loading-container">
            <div class="loader"></div>
            <p>Chargement des utilisateurs...</p>
          </div>

          <div v-else-if="error" class="error-container">
            <p class="error-message">{{ error }}</p>
            <button @click="fetchData" class="retry-button">Réessayer</button>
          </div>

          <div v-else class="table-card">
            <div class="table-container">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>UTILISATEUR</th>
                    <th>EMAIL</th>
                    <th>RÔLE</th>
                    <th>ACTIONS</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in filteredUsers" :key="user.id_utilisateur">
                    <td class="user-info">
                      <div class="user-profile">
                        <div
                          class="user-avatar"
                          :style="{
                            backgroundColor: getUserColor(user.id_utilisateur),
                          }"
                        >
                          {{ getInitials(user) }}
                        </div>
                        <div class="user-details">
                          <p class="user-name">
                            {{ user.prenom }} {{ user.nom }}
                          </p>
                          <p class="user-id">ID #{{ user.id_utilisateur }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="user-email">{{ user.email }}</td>
                    <td>
                      <span
                        class="role-badge"
                        :class="getRoleClass(user.id_role)"
                      >
                        {{ getRoleName(user.id_role) }}
                      </span>
                    </td>
                    <td class="actions">
                      <button
                        class="action-btn primary"
                        @click="editUser(user)"
                        title="Modifier"
                      >
                        <svg
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                        >
                          <path
                            d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"
                          />
                          <path
                            d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"
                          />
                        </svg>
                      </button>
                      <button
                        class="action-btn danger"
                        @click="deleteUser(user)"
                        title="Supprimer"
                      >
                        <svg
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                        >
                          <polyline points="3 6 5 6 21 6" />
                          <path
                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                          />
                        </svg>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="filteredUsers.length === 0">
                    <td colspan="100%" class="empty-message">
                      Aucun utilisateur trouvé
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Onglet Rôles et permissions -->
        <div v-if="activeTab === 'roles'" class="roles-section">
          <div v-if="loading" class="loading-container">
            <div class="loader"></div>
            <p>Chargement des rôles...</p>
          </div>

          <div v-else-if="error" class="error-container">
            <p class="error-message">{{ error }}</p>
            <button @click="fetchData" class="retry-button">Réessayer</button>
          </div>

          <div v-else class="roles-grid">
            <div
              v-for="role in rolesWithStats"
              :key="role.id_role"
              class="role-card"
            >
              <div class="role-header">
                <div
                  class="role-icon"
                  :style="{ backgroundColor: getRoleColor(role.id_role) }"
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                  </svg>
                </div>
                <div class="role-info">
                  <h4 class="role-name">{{ role.role }}</h4>
                  <p class="role-description">
                    {{ getRoleDescription(role.id_role) }}
                  </p>
                </div>
              </div>
              <div class="role-stats">
                <span class="users-count"
                  >{{ role.usersCount }} utilisateur{{
                    role.usersCount > 1 ? "s" : ""
                  }}</span
                >
              </div>
              <div class="role-permissions">
                <h5>Permissions principales :</h5>
                <ul class="permissions-list">
                  <li
                    v-for="permission in getRolePermissions(role.id_role).slice(
                      0,
                      3
                    )"
                    :key="permission"
                  >
                    {{ permission }}
                  </li>
                  <li
                    v-if="getRolePermissions(role.id_role).length > 3"
                    class="more-permissions"
                  >
                    +{{ getRolePermissions(role.id_role).length - 3 }} autres
                  </li>
                </ul>
              </div>
              <div class="role-actions">
                <button class="role-edit-button" @click="editRole(role)">
                  Modifier les permissions
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Nouvel utilisateur -->
    <div
      v-if="showNewUserModal"
      class="modal-overlay"
      @click="showNewUserModal = false"
    >
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Nouvel utilisateur</h3>
          <button @click="closeNewUserModal" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="handleCreateUser" class="user-form">
            <div class="form-group">
              <label for="nom" class="form-label">Nom</label>
              <input
                id="nom"
                v-model="newUserForm.nom"
                type="text"
                class="form-input"
                placeholder="Entrez le nom"
                maxlength="50"
                required
              />
            </div>

            <div class="form-group">
              <label for="prenom" class="form-label">Prénom</label>
              <input
                id="prenom"
                v-model="newUserForm.prenom"
                type="text"
                class="form-input"
                placeholder="Entrez le prénom"
                maxlength="50"
                required
              />
            </div>

            <div class="form-group">
              <label for="email" class="form-label">Email</label>
              <input
                id="email"
                v-model="newUserForm.email"
                type="email"
                class="form-input"
                placeholder="exemple@lamb.com"
                required
              />
            </div>

            <div class="form-group">
              <label for="login" class="form-label">Login</label>
              <input
                id="login"
                v-model="newUserForm.login"
                type="text"
                class="form-input"
                placeholder="Nom d'utilisateur pour la connexion"
                maxlength="30"
                required
              />
            </div>

            <div class="form-group">
              <label for="password" class="form-label">Mot de passe</label>
              <div class="password-input-container">
                <input
                  id="password"
                  v-model="newUserForm.password"
                  :type="showPassword ? 'text' : 'password'"
                  class="form-input"
                  placeholder="Ex: MonPass123!"
                  pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,30}$"
                  minlength="8"
                  maxlength="30"
                  title="Le mot de passe doit contenir au moins : une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&)"
                  required
                />
                <button
                  type="button"
                  class="password-toggle"
                  @click="showPassword = !showPassword"
                >
                  <svg
                    v-if="!showPassword"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                  >
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>
                  <svg
                    v-else
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                  >
                    <path
                      d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"
                    />
                    <line x1="1" y1="1" x2="23" y2="23" />
                  </svg>
                </button>
              </div>
              <span class="form-hint"
                >Le mot de passe doit contenir au moins 8 caractères, une
                majuscule, une minuscule, un chiffre et un caractère spécial
                (@$!%*?&)</span
              >
            </div>

            <div class="form-group">
              <label for="role" class="form-label">Rôle</label>
              <select
                id="role"
                v-model="newUserForm.id_role"
                class="form-select"
                required
              >
                <option value="" disabled>Sélectionnez un rôle</option>
                <option
                  v-for="role in roles"
                  :key="role.id_role"
                  :value="role.id_role"
                >
                  {{ role.role }}
                </option>
              </select>
            </div>

            <div v-if="createError" class="form-error">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
              </svg>
              {{ createError }}
            </div>

            <div v-if="createSuccess" class="form-success">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
              Utilisateur créé avec succès !
            </div>

            <div class="modal-actions">
              <button
                type="button"
                class="modal-btn secondary"
                @click="closeNewUserModal"
              >
                Annuler
              </button>
              <button
                type="submit"
                class="modal-btn primary"
                :disabled="creatingUser"
              >
                <span v-if="creatingUser" class="loading-spinner"></span>
                {{ creatingUser ? "Création..." : "Créer l'utilisateur" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Modifier utilisateur -->
    <div
      v-if="showEditUserModal"
      class="modal-overlay"
      @click="showEditUserModal = false"
    >
      <div class="modal-content modal-edit" @click.stop>
        <div class="modal-header">
          <h3>Modifier l'utilisateur</h3>
          <button @click="showEditUserModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body" v-if="selectedUser">
          <div class="user-details-card">
            <div class="user-header">
              <div
                class="user-avatar large"
                :style="{
                  backgroundColor: getUserColor(selectedUser.id_utilisateur),
                }"
              >
                {{ getInitials(selectedUser) }}
              </div>
              <div class="user-info">
                <h4>{{ selectedUser.prenom }} {{ selectedUser.nom }}</h4>
                <p>{{ selectedUser.email }}</p>
              </div>
            </div>
            <div class="detail-row">
              <span class="detail-label">ID Utilisateur:</span>
              <span class="detail-value"
                >#{{ selectedUser.id_utilisateur }}</span
              >
            </div>
            <div class="detail-row">
              <span class="detail-label">Rôle actuel:</span>
              <span class="detail-value">
                <span
                  class="role-badge"
                  :class="getRoleClass(selectedUser.id_role)"
                >
                  {{ getRoleName(selectedUser.id_role) }}
                </span>
              </span>
            </div>
          </div>
          <div class="modal-actions">
            <button
              class="modal-btn secondary"
              @click="showEditUserModal = false"
            >
              Annuler
            </button>
            <button class="modal-btn primary">
              Enregistrer les modifications
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";

export default {
  name: "AdminView",
  setup() {
    const activeTab = ref("users");
    const userSearch = ref("");
    const showNewUserModal = ref(false);
    const showEditUserModal = ref(false);
    const selectedUser = ref(null);
    const users = ref([]);
    const roles = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const creatingUser = ref(false);
    const createError = ref(null);
    const createSuccess = ref(false);
    const showPassword = ref(false);

    const tabs = ref([
      { id: "users", label: "Utilisateurs" },
      { id: "roles", label: "Rôles et permissions" },
    ]);

    // Formulaire nouvel utilisateur
    const newUserForm = ref({
      nom: "",
      prenom: "",
      email: "",
      login: "",
      password: "",
      id_role: "",
    });

    // Mapping des permissions par rôle
    const rolePermissions = {
      1: [
        // Test
        "Accès de test",
        "Fonctionnalités limitées",
      ],
      2: [
        // Gestionnaire
        "Gestion des stocks",
        "Gestion des commandes",
        "Rapports et analyses",
        "Gestion des fournisseurs",
      ],
      3: [
        // Commercial
        "Gestion des clients",
        "Création de commandes",
        "Suivi des ventes",
      ],
      4: [
        // Administrateur
        "Gestion des utilisateurs",
        "Configuration système",
        "Accès aux logs",
        "Gestion des rôles",
        "Accès complet",
      ],
    };

    const roleDescriptions = {
      1: "Compte de test avec accès limité",
      2: "Gestion des opérations",
      3: "Gestion des ventes et clients",
      4: "Accès complet au système",
    };

    const roleColors = {
      1: "#6B7280", // Gris pour test
      2: "#059669", // Vert pour gestionnaire
      3: "#3B82F6", // Bleu pour commercial
      4: "#DC2626", // Rouge pour admin
    };

    // Fonction pour récupérer les données
    const fetchData = async () => {
      loading.value = true;
      error.value = null;

      try {
        // Récupérer les utilisateurs
        const usersResponse = await fetch(
          import.meta.env.VITE_API_URL + "get_table?table=utilisateur",
          {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          }
        );

        // Récupérer les rôles
        const rolesResponse = await fetch(
          import.meta.env.VITE_API_URL + "get_table?table=role",
          {
            method: "GET",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
          }
        );

        if (!usersResponse.ok || !rolesResponse.ok) {
          throw new Error("Erreur lors du chargement des données");
        }

        const usersData = await usersResponse.json();
        const rolesData = await rolesResponse.json();

        if (usersData.success && usersData.data) {
          users.value = usersData.data;
        }

        if (rolesData.success && rolesData.data) {
          roles.value = rolesData.data;
        }
      } catch (err) {
        console.error("Erreur lors du chargement:", err);
        error.value = "Impossible de charger les données. Veuillez réessayer.";
      } finally {
        loading.value = false;
      }
    };

    // Fonction pour créer un nouvel utilisateur
    const handleCreateUser = async () => {
      createError.value = null;
      createSuccess.value = false;
      creatingUser.value = true;

      try {
        // Créer les données en format URLSearchParams pour x-www-form-urlencoded
        const formData = new URLSearchParams();
        formData.append("nom", newUserForm.value.nom);
        formData.append("prenom", newUserForm.value.prenom);
        formData.append("email", newUserForm.value.email);
        formData.append("login", newUserForm.value.login);
        formData.append("password", newUserForm.value.password);
        formData.append("id_role", newUserForm.value.id_role.toString());

        const response = await fetch(
          import.meta.env.VITE_API_URL + "new_user",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            credentials: "include",
            body: formData.toString(),
          }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
          throw new Error(
            data.message || "Erreur lors de la création de l'utilisateur"
          );
        }

        createSuccess.value = true;

        // Rafraîchir la liste des utilisateurs après 1.5 secondes
        setTimeout(() => {
          fetchData();
          closeNewUserModal();
        }, 1500);
      } catch (err) {
        console.error("Erreur lors de la création:", err);
        // Adapter le message d'erreur selon le code de réponse
        if (err.message === "Champs invalides") {
          createError.value =
            "Vérifiez que le mot de passe respecte les critères de sécurité et que l'email est valide.";
        } else if (err.message === "Email déjà utilisé") {
          createError.value =
            "Cet email est déjà utilisé par un autre utilisateur.";
        } else if (err.message === "Champs manquants") {
          createError.value = "Veuillez remplir tous les champs obligatoires.";
        } else {
          createError.value =
            err.message ||
            "Impossible de créer l'utilisateur. Veuillez réessayer.";
        }
      } finally {
        creatingUser.value = false;
      }
    };

    // Fonction pour supprimer un utilisateur (placeholder)
    const deleteUser = (user) => {
      if (
        confirm(
          `Êtes-vous sûr de vouloir supprimer l'utilisateur ${user.prenom} ${user.nom} ?`
        )
      ) {
        console.log("Supprimer utilisateur:", user.id_utilisateur);
        // TODO: Implémenter l'API DELETE
      }
    };

    // Computed properties
    const filteredUsers = computed(() => {
      if (!userSearch.value) return users.value;
      const search = userSearch.value.toLowerCase();
      return users.value.filter(
        (user) =>
          user.nom.toLowerCase().includes(search) ||
          user.prenom.toLowerCase().includes(search) ||
          user.email.toLowerCase().includes(search)
      );
    });

    const rolesWithStats = computed(() => {
      return roles.value.map((role) => ({
        ...role,
        usersCount: users.value.filter((user) => user.id_role === role.id_role)
          .length,
      }));
    });

    // Fonctions utilitaires
    const getInitials = (user) => {
      const prenom = user.prenom || "";
      const nom = user.nom || "";
      return (prenom.charAt(0) + nom.charAt(0)).toUpperCase();
    };

    const getUserColor = (userId) => {
      const colors = [
        "#3B82F6",
        "#059669",
        "#DC2626",
        "#7C3AED",
        "#F59E0B",
        "#EC4899",
      ];
      return colors[userId % colors.length];
    };

    const getRoleName = (roleId) => {
      const role = roles.value.find((r) => r.id_role === roleId);
      return role ? role.role : "Non défini";
    };

    const getRoleClass = (roleId) => {
      const roleClasses = {
        1: "role-test",
        2: "role-manager",
        3: "role-commercial",
        4: "role-admin",
      };
      return roleClasses[roleId] || "role-default";
    };

    const getRoleColor = (roleId) => {
      return roleColors[roleId] || "#6B7280";
    };

    const getRoleDescription = (roleId) => {
      return roleDescriptions[roleId] || "Rôle personnalisé";
    };

    const getRolePermissions = (roleId) => {
      return rolePermissions[roleId] || [];
    };

    const editUser = (user) => {
      selectedUser.value = user;
      showEditUserModal.value = true;
    };

    const editRole = (role) => {
      console.log("Modifier rôle:", role.id_role);
      // TODO: Implémenter la modification des rôles
    };

    const closeNewUserModal = () => {
      showNewUserModal.value = false;
      showPassword.value = false;
      // Réinitialiser le formulaire
      newUserForm.value = {
        nom: "",
        prenom: "",
        email: "",
        login: "",
        password: "",
        id_role: "",
      };
      createError.value = null;
      createSuccess.value = false;
    };

    // Charger les données au montage
    onMounted(() => {
      fetchData();
    });

    return {
      activeTab,
      userSearch,
      showNewUserModal,
      showEditUserModal,
      selectedUser,
      tabs,
      users,
      roles,
      loading,
      error,
      creatingUser,
      createError,
      createSuccess,
      showPassword,
      newUserForm,
      filteredUsers,
      rolesWithStats,
      getInitials,
      getUserColor,
      getRoleName,
      getRoleClass,
      getRoleColor,
      getRoleDescription,
      getRolePermissions,
      editUser,
      deleteUser,
      editRole,
      fetchData,
      handleCreateUser,
      closeNewUserModal,
    };
  },
};
</script>

<style scoped>
.admin-page {
  padding: 0;
  font-family: "Inter", sans-serif;
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
  color: #0f172a;
  margin: 0;
}

.new-user-button {
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

.new-user-button:hover {
  background: #0891a6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 184, 212, 0.4);
}

.new-user-button svg {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

/* LOADING & ERROR */
.loading-container,
.error-container {
  text-align: center;
  padding: 3rem;
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

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.loading-spinner {
  display: inline-block;
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.error-message {
  color: #dc2626;
  margin-bottom: 1rem;
}

.retry-button {
  background: #00b8d4;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.retry-button:hover {
  background: #0891a6;
}

/* ONGLETS */
.tabs-section {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #f1f5f9;
  overflow: hidden;
}

.tabs-nav {
  display: flex;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
}

.tab-button {
  background: none;
  border: none;
  padding: 1rem 1.5rem;
  font-size: 14px;
  font-weight: 500;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
  border-bottom: 3px solid transparent;
}

.tab-button:hover {
  color: black;
  background: #f1f5f9;
}

.tab-button.active {
  color: #00b8d4;
  background: white;
  border-bottom-color: #00b8d4;
}

.tab-content {
  padding: 1.5rem;
}

/* SECTION UTILISATEURS */
.section-header {
  margin-bottom: 1.5rem;
}

.search-container {
  position: relative;
  max-width: 400px;
}

.search-input {
  width: 100%;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  font-size: 14px;
  color: black;
  transition: all 0.2s ease;
}

.search-input:focus {
  outline: none;
  border-color: #00b8d4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  color: #94a3b8;
  stroke-width: 2;
  pointer-events: none;
}

/* TABLEAU */
.table-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  background: #f8fafc;
  text-align: left;
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
  color: black;
  vertical-align: middle;
}

.data-table tbody tr:hover {
  background: #f8fafc;
}

.data-table tbody tr:last-child td {
  border-bottom: none;
}

.empty-message {
  text-align: center;
  color: #64748b;
  font-style: italic;
  padding: 2rem;
}

/* PROFIL UTILISATEUR */
.user-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 14px;
  flex-shrink: 0;
}

.user-avatar.large {
  width: 64px;
  height: 64px;
  font-size: 20px;
}

.user-name {
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 0.25rem 0;
}

.user-id {
  font-size: 12px;
  color: #64748b;
  margin: 0;
}

.user-email {
  color: black;
}

/* BADGES */
.role-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.role-admin {
  background: #fef2f2;
  color: #991b1b;
}

.role-manager {
  background: #f0fdf4;
  color: #166534;
}

.role-commercial {
  background: #eff6ff;
  color: #1e40af;
}

.role-test {
  background: #f3f4f6;
  color: #4b5563;
}

.role-default {
  background: #f9fafb;
  color: #6b7280;
}

/* ACTIONS */
.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  background: none;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 6px;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: black;
}

.action-btn.primary {
  background: #00b8d4;
  color: white;
  border-color: #00b8d4;
}

.action-btn.primary:hover {
  background: #0891a6;
  border-color: #0891a6;
}

.action-btn.danger {
  background: #fee2e2;
  color: #dc2626;
  border-color: #fecaca;
}

.action-btn.danger:hover {
  background: #fca5a5;
  border-color: #f87171;
}

.action-btn svg {
  width: 16px;
  height: 16px;
  stroke-width: 1.5;
}

/* SECTION RÔLES */
.roles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.role-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  transition: all 0.2s ease;
}

.role-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.role-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.role-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.role-icon svg {
  width: 24px;
  height: 24px;
  stroke-width: 2;
}

.role-name {
  font-size: 16px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 0.25rem 0;
}

.role-description {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.role-stats {
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e2e8f0;
}

.users-count {
  font-size: 14px;
  color: #00b8d4;
  font-weight: 500;
}

.role-permissions h5 {
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 0.75rem 0;
}

.permissions-list {
  list-style: none;
  padding: 0;
  margin: 0 0 1rem 0;
}

.permissions-list li {
  font-size: 13px;
  color: #64748b;
  margin-bottom: 0.25rem;
  position: relative;
  padding-left: 1rem;
}

.permissions-list li::before {
  content: "•";
  color: #00b8d4;
  position: absolute;
  left: 0;
}

.more-permissions {
  font-style: italic;
  color: #94a3b8;
}

.role-actions {
  margin-top: 1rem;
}

.role-edit-button {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  font-size: 13px;
  font-weight: 500;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 100%;
}

.role-edit-button:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: black;
}

/* FORMULAIRE */
.user-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-size: 14px;
  font-weight: 500;
  color: #0f172a;
}

.form-input,
.form-select {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-size: 14px;
  color: black;
  transition: all 0.2s ease;
  width: 100%;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #00b8d4;
  box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
}

.form-input::placeholder {
  color: #94a3b8;
}

.form-hint {
  font-size: 12px;
  color: #64748b;
  margin-top: 0.25rem;
  line-height: 1.4;
}

.password-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input-container .form-input {
  padding-right: 2.5rem;
}

.password-toggle {
  position: absolute;
  right: 0.75rem;
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s ease;
}

.password-toggle:hover {
  color: black;
}

.password-toggle svg {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

.form-error,
.form-success {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border-radius: 8px;
  font-size: 14px;
}

.form-error {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.form-success {
  background: #f0fdf4;
  color: #059669;
  border: 1px solid #bbf7d0;
}

.form-error svg,
.form-success svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
  flex-shrink: 0;
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

.modal-content.modal-edit {
  max-width: 600px;
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
  color: black;
}

.modal-close svg {
  width: 20px;
  height: 20px;
  stroke-width: 2;
}

.modal-body {
  padding: 1.5rem;
  color: #64748b;
  overflow-y: auto;
  max-height: calc(90vh - 120px);
}

/* MODAL EDIT USER */
.user-details-card {
  background: #f8fafc;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

.user-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.user-info h4 {
  font-size: 18px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 0.25rem 0;
}

.user-info p {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #e2e8f0;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-size: 13px;
  color: #64748b;
  font-weight: 500;
}

.detail-value {
  font-size: 14px;
  color: #0f172a;
  font-weight: 600;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
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
  gap: 0.5rem;
}

.modal-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.modal-btn.primary {
  background: #00b8d4;
  color: white;
}

.modal-btn.primary:hover:not(:disabled) {
  background: #0891a6;
}

.modal-btn.secondary {
  background: #f1f5f9;
  color: #64748b;
}

.modal-btn.secondary:hover {
  background: #e2e8f0;
  color: black;
}

input:placeholder-shown {
  text-overflow: ellipsis;
}

input:focus::placeholder {
  color: transparent;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .new-user-button {
    width: 100%;
    justify-content: center;
  }

  .search-container {
    max-width: none;
  }

  .roles-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  }
}

@media (max-width: 768px) {
  .data-table {
    font-size: 12px;
  }

  .data-table th,
  .data-table td {
    padding: 0.75rem 0.5rem;
  }

  .actions {
    flex-direction: column;
    gap: 0.25rem;
  }

  .action-btn {
    padding: 4px;
  }

  .action-btn svg {
    width: 14px;
    height: 14px;
  }

  .roles-grid {
    grid-template-columns: 1fr;
  }

  .modal-actions {
    flex-direction: column;
  }

  .modal-btn {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 640px) {
  .data-table th:nth-child(3),
  .data-table td:nth-child(3) {
    display: none;
  }
}
</style>
