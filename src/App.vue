<template>
  <div id="app">
    <component :is="layoutComponent">
      <router-view />
    </component>
  </div>
</template>

<script>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import MainLayout from '@/layouts/MainLayout.vue'

export default {
  name: 'App',
  components: {
    MainLayout
  },
  setup() {
    const route = useRoute()
    const authStore = useAuthStore()

    const layoutComponent = computed(() => {
      if (route.meta.layout === 'main') {
        return 'MainLayout'
      }
      return 'div' // Layout vide pour login
    })

    onMounted(() => {
      authStore.initAuth()
    })

    return {
      layoutComponent
    }
  }
}
</script>