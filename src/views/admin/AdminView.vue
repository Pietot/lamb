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
        <button v-for="tab in tabs" :key="tab.id" class="tab-button" :class="{ 'active': activeTab === tab.id }"
          @click="activeTab = tab.id">
          {{ tab.label }}
        </button>
      </div>

      <!-- Contenu des onglets -->
      <div class="tab-content">
        <!-- Onglet Utilisateurs -->
        <div v-if="activeTab === 'users'" class="users-section">
          <div class="section-header">
            <div class="search-container">
              <input v-model="userSearch" type="text" placeholder="Rechercher un utilisateur..." class="search-input" />
              <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
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
                    <th>STATUT</th>
                    <th>DERNIÈRE CONNEXION</th>
                    <th>ACTIONS</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in filteredUsers" :key="user.id_utilisateur">
                    <td class="user-info">
                      <div class="user-profile">
                        <div class="user-avatar" :style="{ backgroundColor: getUserColor(user.id_utilisateur) }">
                          {{ getInitials(user) }}
                        </div>
                        <div class="user-details">
                          <p class="user-name">{{ user.prenom }} {{ user.nom }}</p>
                          <p class="user-id">ID #{{ user.id_utilisateur }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="user-email">{{ user.email }}</td>
                    <td>
                      <span class="role-badge" :class="getRoleClass(user.id_role)">
                        {{ getRoleName(user.id_role) }}
                      </span>
                    </td>
                    <td>
                      <span class="status-badge" :class="getUserStatus(user) ? 'status-active' : 'status-inactive'">
                        {{ getUserStatus(user) ? 'Actif' : 'Inactif' }}
                      </span>
                    </td>
                    <td class="last-connection">
                      {{ user.token_init ? formatDate(user.token_init) : 'Jamais connecté' }}
                    </td>
                    <td class="actions">
                      <button class="action-btn primary" @click="editUser(user)" title="Modifier">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                      </button>
                      <button class="action-btn" @click="toggleUserStatus(user)"
                        :title="getUserStatus(user) ? 'Désactiver' : 'Activer'">
                        <svg v-if="getUserStatus(user)" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <polyline points="20 6 9 17 4 12" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="filteredUsers.length === 0">
                    <td colspan="6" class="empty-message">Aucun utilisateur trouvé</td>
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
            <div v-for="role in rolesWithStats" :key="role.id_role" class="role-card">
              <div class="role-header">
                <div class="role-icon" :style="{ backgroundColor: getRoleColor(role.id_role) }">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                  </svg>
                </div>
                <div class="role-info">
                  <h4 class="role-name">{{ role.role }}</h4>
                  <p class="role-description">{{ getRoleDescription(role.id_role) }}</p>
                </div>
              </div>
              <div class="role-stats">
                <span class="users-count">{{ role.usersCount }} utilisateur{{ role.usersCount > 1 ? 's' : '' }}</span>
              </div>
              <div class="role-permissions">
                <h5>Permissions principales :</h5>
                <ul class="permissions-list">
                  <li v-for="permission in getRolePermissions(role.id_role).slice(0, 3)" :key="permission">
                    {{ permission }}
                  </li>
                  <li v-if="getRolePermissions(role.id_role).length > 3" class="more-permissions">
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

    <!-- Modal Nouvel utilisateur (placeholder) -->
    <div v-if="showNewUserModal" class="modal-overlay" @click="showNewUserModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Nouvel utilisateur</h3>
          <button @click="showNewUserModal = false" class="modal-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <p>Fonctionnalité en cours de développement</p>
        </div>
      </div>
    </div>

    <!-- Modal Modifier utilisateur -->
    <div v-if="showEditUserModal" class="modal-overlay" @click="showEditUserModal = false">
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
              <div class="user-avatar large" :style="{ backgroundColor: getUserColor(selectedUser.id_utilisateur) }">
                {{ getInitials(selectedUser) }}
              </div>
              <div class="user-info">
                <h4>{{ selectedUser.prenom }} {{ selectedUser.nom }}</h4>
                <p>{{ selectedUser.email }}</p>
              </div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Login:</span>
              <span class="detail-value">{{ selectedUser.login }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Rôle actuel:</span>
              <span class="detail-value">
                <span class="role-badge" :class="getRoleClass(selectedUser.id_role)">
                  {{ getRoleName(selectedUser.id_role) }}
                </span>
              </span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Statut:</span>
              <span class="detail-value">
                <span class="status-badge" :class="getUserStatus(selectedUser) ? 'status-active' : 'status-inactive'">
                  {{ getUserStatus(selectedUser) ? 'Actif' : 'Inactif' }}
                </span>
              </span>
            </div>
          </div>
          <div class="modal-actions">
            <button class="modal-btn secondary" @click="showEditUserModal = false">
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
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'AdminView',
  setup() {
    const activeTab = ref('users')
    const userSearch = ref('')
    const showNewUserModal = ref(false)
    const showEditUserModal = ref(false)
    const selectedUser = ref(null)
    const users = ref([])
    const roles = ref([])
    const loading = ref(true)
    const error = ref(null)

    const tabs = ref([
      { id: 'users', label: 'Utilisateurs' },
      { id: 'roles', label: 'Rôles et permissions' }
    ])

    // Mapping des permissions par rôle
    const rolePermissions = {
      0: [ // Administrateur
        'Gestion des utilisateurs',
        'Configuration système',
        'Accès aux logs',
        'Gestion des rôles',
        'Accès complet'
      ],
      1: [ // Test
        'Accès de test',
        'Fonctionnalités limitées'
      ],
      2: [ // Gestionnaire
        'Gestion des stocks',
        'Gestion des commandes',
        'Rapports et analyses',
        'Gestion des fournisseurs'
      ],
      3: [ // Commercial
        'Gestion des clients',
        'Création de commandes',
        'Suivi des ventes'
      ]
    }

    const roleDescriptions = {
      0: 'Accès complet au système',
      1: 'Compte de test avec accès limité',
      2: 'Gestion des opérations',
      3: 'Gestion des ventes et clients'
    }

    const roleColors = {
      0: '#DC2626', // Rouge pour admin
      1: '#6B7280', // Gris pour test
      2: '#059669', // Vert pour gestionnaire
      3: '#3B82F6'  // Bleu pour commercial
    }

    // Fonction pour récupérer les données
    const fetchData = async () => {
      loading.value = true
      error.value = null

      try {
        // Récupérer les utilisateurs
        const usersResponse = await fetch(import.meta.env.VITE_API_URL + "get_table?table=utilisateur", {
          method: "GET",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            Authorization: `Bearer ${import.meta.env.VITE_API_KEY}`,
          },
          credentials: 'include',
        })

        // Récupérer les rôles
        const rolesResponse = await fetch(import.meta.env.VITE_API_URL + "get_table?table=role", {
          method: "GET",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            Authorization: `Bearer ${import.meta.env.VITE_API_KEY}`,
          },
          credentials: 'include',
        })

        if (!usersResponse.ok || !rolesResponse.ok) {
          throw new Error('Erreur lors du chargement des données')
        }

        const usersData = await usersResponse.json()
        const rolesData = await rolesResponse.json()

        if (usersData.success && usersData.articles) {
          users.value = usersData.articles
        }

        if (rolesData.success && rolesData.articles) {
          roles.value = rolesData.articles
        }
      } catch (err) {
        console.error('Erreur lors du chargement:', err)
        error.value = 'Impossible de charger les données. Veuillez réessayer.'
      } finally {
        loading.value = false
      }
    }

    // Computed properties
    const filteredUsers = computed(() => {
      if (!userSearch.value) return users.value
      const search = userSearch.value.toLowerCase()
      return users.value.filter(user =>
        user.nom.toLowerCase().includes(search) ||
        user.prenom.toLowerCase().includes(search) ||
        user.email.toLowerCase().includes(search) ||
        user.login.toLowerCase().includes(search)
      )
    })

    const rolesWithStats = computed(() => {
      return roles.value.map(role => ({
        ...role,
        usersCount: users.value.filter(user => user.id_role === role.id_role).length
      }))
    })

    // Fonctions utilitaires
    const getInitials = (user) => {
      const prenom = user.prenom || ''
      const nom = user.nom || ''
      return (prenom.charAt(0) + nom.charAt(0)).toUpperCase()
    }

    const getUserColor = (userId) => {
      const colors = ['#3B82F6', '#059669', '#DC2626', '#7C3AED', '#F59E0B', '#EC4899']
      return colors[userId % colors.length]
    }

    const getRoleName = (roleId) => {
      const role = roles.value.find(r => r.id_role === roleId)
      return role ? role.role : 'Non défini'
    }

    const getRoleClass = (roleId) => {
      const roleClasses = {
        0: 'role-admin',
        2: 'role-manager',
        3: 'role-commercial',
        1: 'role-test'
      }
      return roleClasses[roleId] || 'role-default'
    }

    const getRoleColor = (roleId) => {
      return roleColors[roleId] || '#6B7280'
    }

    const getRoleDescription = (roleId) => {
      return roleDescriptions[roleId] || 'Rôle personnalisé'
    }

    const getRolePermissions = (roleId) => {
      return rolePermissions[roleId] || []
    }

    const getUserStatus = (user) => {
      // Un utilisateur est considéré actif s'il a un token
      return user.token !== null
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'Jamais'
      const date = new Date(dateString)
      const now = new Date()
      const diffTime = Math.abs(now - date)
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

      if (diffDays === 0) return "Aujourd'hui"
      if (diffDays === 1) return "Hier"
      if (diffDays < 7) return `Il y a ${diffDays} jours`

      const options = { day: 'numeric', month: 'short', year: 'numeric' }
      return date.toLocaleDateString('fr-FR', options)
    }

    const editUser = (user) => {
      selectedUser.value = user
      showEditUserModal.value = true
    }

    const toggleUserStatus = (user) => {
      console.log('Toggle status for user:', user.id_utilisateur)
      // TODO: Implémenter l'API pour changer le statut
    }

    const editRole = (role) => {
      console.log('Modifier rôle:', role.id_role)
      // TODO: Implémenter la modification des rôles
    }

    // Charger les données au montage
    onMounted(() => {
      fetchData()
    })

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
      filteredUsers,
      rolesWithStats,
      getInitials,
      getUserColor,
      getRoleName,
      getRoleClass,
      getRoleColor,
      getRoleDescription,
      getRolePermissions,
      getUserStatus,
      formatDate,
      editUser,
      toggleUserStatus,
      editRole,
      fetchData
    }
  }
}
</script>

<style scoped>
.admin-page {
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

.new-user-button {
  background: #00B8D4;
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
  background: #0891A6;
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
  to {
    transform: rotate(360deg);
  }
}

.error-message {
  color: #DC2626;
  margin-bottom: 1rem;
}

.retry-button {
  background: #00B8D4;
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
  background: #0891A6;
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
  vertical-align: middle;
}

.data-table tbody tr:hover {
  background: #F8FAFC;
}

.data-table tbody tr:last-child td {
  border-bottom: none;
}

.empty-message {
  text-align: center;
  color: #64748B;
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
  color: #0F172A;
  margin: 0 0 0.25rem 0;
}

.user-id {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

.user-email {
  color: #334155;
}

.last-connection {
  color: #64748B;
  font-size: 13px;
}

/* BADGES */
.role-badge,
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.role-admin {
  background: #FEF2F2;
  color: #991B1B;
}

.role-manager {
  background: #F0FDF4;
  color: #166534;
}

.role-commercial {
  background: #EFF6FF;
  color: #1E40AF;
}

.role-test {
  background: #F3F4F6;
  color: #4B5563;
}

.role-default {
  background: #F9FAFB;
  color: #6B7280;
}

.status-active {
  background: #D1FAE5;
  color: #047857;
}

.status-inactive {
  background: #FEF2F2;
  color: #DC2626;
}

/* ACTIONS */
.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  background: none;
  border: 1px solid #E2E8F0;
  border-radius: 6px;
  padding: 6px;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

.action-btn.primary {
  background: #00B8D4;
  color: white;
  border-color: #00B8D4;
}

.action-btn.primary:hover {
  background: #0891A6;
  border-color: #0891A6;
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
  border: 1px solid #E2E8F0;
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
  color: #0F172A;
  margin: 0 0 0.25rem 0;
}

.role-description {
  font-size: 14px;
  color: #64748B;
  margin: 0;
}

.role-stats {
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #E2E8F0;
}

.users-count {
  font-size: 14px;
  color: #00B8D4;
  font-weight: 500;
}

.role-permissions h5 {
  font-size: 14px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.75rem 0;
}

.permissions-list {
  list-style: none;
  padding: 0;
  margin: 0 0 1rem 0;
}

.permissions-list li {
  font-size: 13px;
  color: #64748B;
  margin-bottom: 0.25rem;
  position: relative;
  padding-left: 1rem;
}

.permissions-list li::before {
  content: '•';
  color: #00B8D4;
  position: absolute;
  left: 0;
}

.more-permissions {
  font-style: italic;
  color: #94A3B8;
}

.role-actions {
  margin-top: 1rem;
}

.role-edit-button {
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  font-size: 13px;
  font-weight: 500;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 100%;
}

.role-edit-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
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
  color: #64748B;
}

/* MODAL EDIT USER */
.user-details-card {
  background: #F8FAFC;
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
  border-bottom: 1px solid #E2E8F0;
}

.user-info h4 {
  font-size: 18px;
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
}

.user-info p {
  font-size: 14px;
  color: #64748B;
  margin: 0;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #E2E8F0;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-size: 13px;
  color: #64748B;
  font-weight: 500;
}

.detail-value {
  font-size: 14px;
  color: #0F172A;
  font-weight: 600;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.modal-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.modal-btn.primary {
  background: #00B8D4;
  color: white;
}

.modal-btn.primary:hover {
  background: #0891A6;
}

.modal-btn.secondary {
  background: #F1F5F9;
  color: #64748B;
}

.modal-btn.secondary:hover {
  background: #E2E8F0;
  color: #334155;
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
  }
}

@media (max-width: 640px) {

  .data-table th:nth-child(3),
  .data-table td:nth-child(3),
  .data-table th:nth-child(5),
  .data-table td:nth-child(5) {
    display: none;
  }
}
</style>