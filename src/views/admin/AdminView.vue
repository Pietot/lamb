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
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
          </svg>
          Nouvel utilisateur
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
          :class="{ 'active': activeTab === tab.id }"
          @click="activeTab = tab.id"
        >
          <component :is="tab.icon" class="tab-icon" />
          {{ tab.label }}
        </button>
      </div>

      <!-- Contenu des onglets -->
      <div class="tab-content">
        <!-- Onglet Utilisateurs -->
        <div v-if="activeTab === 'users'" class="tab-panel">
          <div class="users-header">
            <div class="search-container">
              <input
                v-model="userSearch"
                type="text"
                placeholder="Rechercher un utilisateur..."
                class="search-input"
              />
              <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
              </svg>
            </div>
          </div>

          <div class="users-table-container">
            <table class="users-table">
              <thead>
                <tr>
                  <th>Utilisateur</th>
                  <th>Email</th>
                  <th>Rôle</th>
                  <th>Statut</th>
                  <th>Dernière connexion</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in filteredUsers" :key="user.id">
                  <td class="user-info">
                    <div class="user-profile">
                      <div class="user-avatar" :style="{ backgroundColor: user.avatarColor }">
                        {{ user.initials }}
                      </div>
                      <div class="user-details">
                        <p class="user-name">{{ user.name }}</p>
                        <p class="user-id">ID #{{ user.id }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="user-email">{{ user.email }}</td>
                  <td>
                    <span class="role-badge" :class="getRoleClass(user.role)">
                      {{ getRoleLabel(user.role) }}
                    </span>
                  </td>
                  <td>
                    <span class="status-badge" :class="getStatusClass(user.status)">
                      {{ getStatusLabel(user.status) }}
                    </span>
                  </td>
                  <td class="last-connection">{{ user.lastConnection }}</td>
                  <td class="actions-cell">
                    <div class="action-buttons">
                      <button class="action-button edit-button" @click="editUser(user.id)" title="Modifier">
                        Modifier
                      </button>
                      <button class="action-button deactivate-button" @click="toggleUserStatus(user.id)" title="Désactiver">
                        {{ user.status === 'active' ? 'Désactiver' : 'Activer' }}
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Onglet Rôles et permissions -->
        <div v-if="activeTab === 'roles'" class="tab-panel">
          <div class="roles-content">
            <div class="roles-grid">
              <div v-for="role in roles" :key="role.id" class="role-card">
                <div class="role-header">
                  <div class="role-icon" :style="{ backgroundColor: role.color }">
                    <component :is="role.icon" />
                  </div>
                  <div class="role-info">
                    <h4 class="role-name">{{ role.name }}</h4>
                    <p class="role-description">{{ role.description }}</p>
                  </div>
                </div>
                <div class="role-stats">
                  <span class="users-count">{{ role.usersCount }} utilisateurs</span>
                </div>
                <div class="role-permissions">
                  <h5>Permissions principales :</h5>
                  <ul class="permissions-list">
                    <li v-for="permission in role.permissions.slice(0, 3)" :key="permission">
                      {{ permission }}
                    </li>
                    <li v-if="role.permissions.length > 3" class="more-permissions">
                      +{{ role.permissions.length - 3 }} autres
                    </li>
                  </ul>
                </div>
                <div class="role-actions">
                  <button class="role-edit-button" @click="editRole(role.id)">
                    Modifier
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Autres onglets (placeholder) -->
        <div v-else-if="activeTab !== 'users' && activeTab !== 'roles'" class="tab-panel">
          <div class="placeholder-content">
            <div class="placeholder-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="16"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
              </svg>
            </div>
            <h3>{{ tabs.find(t => t.id === activeTab)?.label }}</h3>
            <p>Cette fonctionnalité est en cours de développement</p>
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
import { ref, computed } from 'vue'

// Icônes pour les onglets
const UsersIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
      <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
  `
}

const RolesIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
    </svg>
  `
}

const LogsIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
      <polyline points="14,2 14,8 20,8"/>
      <line x1="16" y1="13" x2="8" y2="13"/>
      <line x1="16" y1="17" x2="8" y2="17"/>
      <polyline points="10,9 9,9 8,9"/>
    </svg>
  `
}

const SettingsIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <circle cx="12" cy="12" r="3"/>
      <path d="M12 1v6m0 6v6m11-7h-6m-6 0H1"/>
    </svg>
  `
}

const BackupIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
      <polyline points="17,8 12,3 7,8"/>
      <line x1="12" y1="3" x2="12" y2="15"/>
    </svg>
  `
}

// Icônes pour les rôles
const AdminIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
    </svg>
  `
}

const ManagerIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
      <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
  `
}

const CommercialIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <circle cx="9" cy="21" r="1"/>
      <circle cx="20" cy="21" r="1"/>
      <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
    </svg>
  `
}

const SupportIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M9 12l2 2 4-4"/>
      <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
      <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
      <path d="M3 12v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
    </svg>
  `
}

export default {
  name: 'AdminView',
  components: {
    UsersIcon,
    RolesIcon,
    LogsIcon,
    SettingsIcon,
    BackupIcon,
    AdminIcon,
    ManagerIcon,
    CommercialIcon,
    SupportIcon
  },
  setup() {
    const activeTab = ref('users')
    const userSearch = ref('')
    const showNewUserModal = ref(false)

    const tabs = ref([
      { id: 'users', label: 'Utilisateurs', icon: 'UsersIcon' },
      { id: 'roles', label: 'Rôles et permissions', icon: 'RolesIcon' },
      { id: 'logs', label: 'Logs d\'activité', icon: 'LogsIcon' },
      { id: 'settings', label: 'Paramètres système', icon: 'SettingsIcon' },
      { id: 'backup', label: 'Sauvegarde et restauration', icon: 'BackupIcon' }
    ])

    // Données des utilisateurs selon la maquette
    const users = ref([
      {
        id: '001',
        name: 'Nicolas Martin',
        email: 'n.martin@example.com',
        role: 'admin',
        status: 'active',
        lastConnection: 'Aujourd\'hui, 09:45',
        initials: 'NM',
        avatarColor: '#3B82F6'
      },
      {
        id: '002',
        name: 'Sophie Dubois',
        email: 's.dubois@example.com',
        role: 'manager',
        status: 'active',
        lastConnection: 'Hier, 16:32',
        initials: 'SD',
        avatarColor: '#059669'
      },
      {
        id: '003',
        name: 'Jean Leroy',
        email: 'j.leroy@example.com',
        role: 'commercial',
        status: 'active',
        lastConnection: '30/04/2023, 11:20',
        initials: 'JL',
        avatarColor: '#DC2626'
      },
      {
        id: '004',
        name: 'Marie Bertrand',
        email: 'm.bertrand@example.com',
        role: 'support',
        status: 'inactive',
        lastConnection: '15/04/2023, 09:12',
        initials: 'MB',
        avatarColor: '#7C3AED'
      }
    ])

    // Données des rôles
    const roles = ref([
      {
        id: 'admin',
        name: 'Administrateur',
        description: 'Accès complet au système',
        usersCount: 1,
        color: '#3B82F6',
        icon: 'AdminIcon',
        permissions: [
          'Gestion des utilisateurs',
          'Configuration système',
          'Accès aux logs',
          'Sauvegarde/restauration',
          'Gestion des rôles'
        ]
      },
      {
        id: 'manager',
        name: 'Gestionnaire',
        description: 'Gestion des opérations',
        usersCount: 1,
        color: '#059669',
        icon: 'ManagerIcon',
        permissions: [
          'Gestion des stocks',
          'Gestion des commandes',
          'Rapports et analyses',
          'Gestion des fournisseurs'
        ]
      },
      {
        id: 'commercial',
        name: 'Commercial',
        description: 'Gestion des ventes',
        usersCount: 1,
        color: '#DC2626',
        icon: 'CommercialIcon',
        permissions: [
          'Gestion des clients',
          'Création de commandes',
          'Suivi des ventes'
        ]
      },
      {
        id: 'support',
        name: 'Support',
        description: 'Support technique',
        usersCount: 1,
        color: '#7C3AED',
        icon: 'SupportIcon',
        permissions: [
          'Consultation des données',
          'Support client',
          'Gestion des tickets'
        ]
      }
    ])

    const filteredUsers = computed(() => {
      if (!userSearch.value) return users.value
      return users.value.filter(user =>
        user.name.toLowerCase().includes(userSearch.value.toLowerCase()) ||
        user.email.toLowerCase().includes(userSearch.value.toLowerCase())
      )
    })

    const getRoleClass = (role) => {
      const roleClasses = {
        admin: 'role-admin',
        manager: 'role-manager',
        commercial: 'role-commercial',
        support: 'role-support'
      }
      return roleClasses[role] || 'role-default'
    }

    const getRoleLabel = (role) => {
      const roleLabels = {
        admin: 'Administrateur',
        manager: 'Gestionnaire',
        commercial: 'Commercial',
        support: 'Support'
      }
      return roleLabels[role] || role
    }

    const getStatusClass = (status) => {
      return status === 'active' ? 'status-active' : 'status-inactive'
    }

    const getStatusLabel = (status) => {
      return status === 'active' ? 'Actif' : 'Inactif'
    }

    const editUser = (userId) => {
      console.log('Modifier utilisateur:', userId)
    }

    const toggleUserStatus = (userId) => {
      console.log('Changer statut utilisateur:', userId)
    }

    const editRole = (roleId) => {
      console.log('Modifier rôle:', roleId)
    }

    return {
      activeTab,
      userSearch,
      showNewUserModal,
      tabs,
      users,
      roles,
      filteredUsers,
      getRoleClass,
      getRoleLabel,
      getStatusClass,
      getStatusLabel,
      editUser,
      toggleUserStatus,
      editRole
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
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.new-user-button:hover {
  background: #2563EB;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.new-user-button svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
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
  min-width: fit-content;
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

.tab-icon {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

.tab-content {
  padding: 1.5rem;
}

/* ONGLET UTILISATEURS */
.users-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.search-container {
  position: relative;
  flex: 1;
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

/* TABLEAU UTILISATEURS */
.users-table-container {
  overflow-x: auto;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.users-table th {
  background: #F8FAFC;
  text-align: left;
  padding: 0.75rem 1rem;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #E2E8F0;
  white-space: nowrap;
}

.users-table td {
  padding: 1rem;
  border-bottom: 1px solid #F1F5F9;
  font-size: 14px;
  color: #334155;
  vertical-align: middle;
}

.users-table tr:hover {
  background: #F8FAFC;
}

/* PROFIL UTILISATEUR */
.user-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  min-width: 180px;
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

.user-name {
  font-weight: 600;
  color: #0F172A;
  margin: 0 0 0.25rem 0;
  line-height: 1.2;
}

.user-id {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

.user-email {
  color: #3B82F6;
  font-weight: 500;
}

.last-connection {
  color: #64748B;
  font-size: 13px;
}

/* BADGES */
.role-badge,
.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.role-admin {
  background: #EFF6FF;
  color: #1E40AF;
}

.role-manager {
  background: #F0FDF4;
  color: #166534;
}

.role-commercial {
  background: #FEF2F2;
  color: #991B1B;
}

.role-support {
  background: #F3E8FF;
  color: #7C2D12;
}

.status-active {
  background: #D1FAE5;
  color: #047857;
}

.status-inactive {
  background: #FEF2F2;
  color: #DC2626;
}

/* ACTIONS UTILISATEURS */
.actions-cell {
  min-width: 150px;
}

.action-buttons {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.action-button {
  border: none;
  border-radius: 6px;
  padding: 0.5rem 0.75rem;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.edit-button {
  background: #3B82F6;
  color: white;
}

.edit-button:hover {
  background: #2563EB;
  transform: translateY(-1px);
}

.deactivate-button {
  background: none;
  border: 1px solid #E2E8F0;
  color: #64748B;
}

.deactivate-button:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #334155;
}

/* ONGLET RÔLES */
.roles-content {
  padding: 0;
}

.roles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.role-card {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 12px;
  padding: 1.5rem;
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
  color: #3B82F6;
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
  background: none;
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
  background: #F1F5F9;
  border-color: #CBD5E1;
  color: #334155;
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
@media (max-width: 1024px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .users-header {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-container {
    max-width: none;
  }
  
  .roles-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  }
}

@media (max-width: 768px) {
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
  
  .users-table {
    font-size: 12px;
  }
  
  .users-table th,
  .users-table td {
    padding: 0.75rem 0.5rem;
  }
  
  .user-profile {
    min-width: 120px;
  }
  
  .action-buttons {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .roles-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .users-table th:nth-child(3),
  .users-table td:nth-child(3),
  .users-table th:nth-child(5),
  .users-table td:nth-child(5) {
    display: none;
  }
}
</style>
