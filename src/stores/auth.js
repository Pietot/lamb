import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: JSON.parse(localStorage.getItem("user")) || null,
    isAuthenticated: !!localStorage.getItem("user"),
  }),

  actions: {
    async login({ login, password }) {
      const response = await fetch(import.meta.env.VITE_API_URL + "login", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        credentials: "include",
        body: new URLSearchParams({
          login,
          password,
        }),
      });

      const result = await response.json();

      if (result.success) {
        this.user = result.user;
        this.isAuthenticated = true;
        localStorage.setItem("user", JSON.stringify(result.user));
      } else {
        throw new Error(result.message || "Erreur de connexion");
      }
    },

    async logout() {
      await fetch(import.meta.env.VITE_API_URL + "logout", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        credentials: "include",
      });
      this.user = null;
      this.isAuthenticated = false;
      localStorage.removeItem("user");
      if (window.location.pathname !== "/login") {
        window.location.href = "/login";
      }
    },

    async initAuth() {
      const savedUser = localStorage.getItem("user");
      if (savedUser) {
        const valid = await this.verifyUser();
        this.isAuthenticated = valid;
        return valid;
      } else {
        this.logout();
        return false;
      }
    },

    async verifyUser() {
      // Vérifie la session côté back
      const response = await fetch(
        import.meta.env.VITE_API_URL + "verify_user",
        {
          method: "GET",
          credentials: "include",
        }
      );

      const result = await response.json();

      if (result.success) {
        this.user = result.user;
        this.isAuthenticated = true;
        localStorage.setItem("user", JSON.stringify(result.user));
        return true;
      } else {
        this.logout();
        return false;
      }
    },
  },
});
