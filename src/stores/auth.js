import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))
  
  const isAuthenticated = computed(() => !!token.value)
  
  // Utilisateur de demo
  const demoUser = {
    id: '1',
    name: 'Nicolas Martin',
    email: 'nicolas.martin@fashionchic.fr',
    role: 'admin',
    avatar: 'NM'
  }
  
  function login(credentials) {
    return new Promise((resolve, reject) => {
      // Simulation API call
      setTimeout(() => {
        if (credentials.login === 'admin' && credentials.password === 'admin') {
          const demoToken = 'demo-token-123'
          user.value = demoUser
          token.value = demoToken
          localStorage.setItem('token', demoToken)
          localStorage.setItem('user', JSON.stringify(demoUser))
          resolve({ user: demoUser, token: demoToken })
        } else {
          reject(new Error('Identifiants incorrects'))
        }
      }, 1000)
    })
  }
  
  function logout() {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }
  
  function initAuth() {
    const savedUser = localStorage.getItem('user')
    if (savedUser && token.value) {
      user.value = JSON.parse(savedUser)
    }
  }
  
  return {
    user,
    token,
    isAuthenticated,
    login,
    logout,
    initAuth
  }
})