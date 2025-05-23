import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
  }),

  actions: {
    async login({ login, password }) {
      const isAuthenticated = true;
      const response = await fetch(
        "http://localhost/mine/lamb/backend/login.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            login: login,
            password: password,
          }),
          credentials: "include",
        }
      );

      const result = await response.json();

      if (result.success) {
        this.user = result.user;
        localStorage.setItem("user", JSON.stringify(result.user));
      } else {
        throw new Error(result.message);
      }
    },
  },
});

