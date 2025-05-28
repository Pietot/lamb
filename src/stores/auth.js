import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

const API_URL = import.meta.env.VITE_API_URL;

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
  }),

  actions: {
    async login({ login, password }) {
      const isAuthenticated = true;
      const response = await fetch(
        API_URL,
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

