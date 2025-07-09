import { defineStore } from "pinia";
import router from "@/router";
import { VITE_API_URL } from "@/constants/constants.js";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: JSON.parse(localStorage.getItem("user")) || null,
    isAuthenticated: !!localStorage.getItem("user"),
  }),

  actions: {
    async login({ login, password }) {
      const response = await fetch(
        VITE_API_URL + "login",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          credentials: "include",
          body: new URLSearchParams({
            login,
            password,
          }),
        },
      );

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
      await fetch(VITE_API_URL + "logout", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        credentials: "include",
      });
      this.user = null;
      this.isAuthenticated = false;
      localStorage.removeItem("user");

      if (router.currentRoute.value.name !== "Login") {
        router.push({ name: "Login" });
      }
    },

    async initAuth() {
      const savedUser = localStorage.getItem("user");
      if (savedUser) {
        const valid = await this.verifyUser();
        this.isAuthenticated = valid;
        return valid;
      } else {
        this.user = null;
        this.isAuthenticated = false;
        localStorage.removeItem("user");
        return false;
      }
    },

    async verifyUser() {
      try {
        const response = await fetch(
          VITE_API_URL + "verify_user",
          {
            method: "GET",
            credentials: "include",
          },
        );

        const result = await response.json();

        if (result.success) {
          this.user = result.user;
          this.isAuthenticated = true;
          localStorage.setItem("user", JSON.stringify(result.user));
          return true;
        } else {
          this.user = null;
          this.isAuthenticated = false;
          localStorage.removeItem("user");
          return false;
        }
      } catch (error) {
        console.error("Erreur lors de la vérification:", error);
        this.user = null;
        this.isAuthenticated = false;
        localStorage.removeItem("user");
        return false;
      }
    },
  },
});
