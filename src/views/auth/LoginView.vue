<template>
  <div class="login-page">
    <!-- Arrière-plan avec formes organiques -->
    <div class="background-shapes">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
      <div class="shape shape-4"></div>
    </div>

    <!-- Contenu principal -->
    <div class="login-container">
      <!-- Logo LAMB Solutions -->
      <div class="logo-section">
        <div class="logo-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M12 2L2 7l10 5 10-5-10-5z" />
            <path d="M2 17l10 5 10-5" />
            <path d="M2 12l10 5 10-5" />
          </svg>
        </div>
        <h1 class="logo-text">LAMB Solutions</h1>
      </div>

      <!-- Formulaire de connexion -->
      <div class="login-form-container">
        <form @submit.prevent="handleLogin" class="login-form">
          <!-- Champ LOGIN -->
          <div class="form-field">
            <div class="input-wrapper">
              <div class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                  <circle cx="12" cy="7" r="4" />
                </svg>
              </div>
              <input
                v-model="credentials.login"
                type="text"
                placeholder="Login"
                class="form-input"
                required
              />
            </div>
          </div>

          <!-- Champ MOT DE PASSE -->
          <div class="form-field">
            <div class="input-wrapper">
              <div class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                  <circle cx="12" cy="16" r="1" />
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
              </div>
              <input
                v-model="credentials.password"
                type="password"
                placeholder="Mot de passe"
                class="form-input"
                required
              />
            </div>
          </div>

          <!-- Bouton LOGIN -->
          <button type="submit" :disabled="isLoading" class="login-button">
            {{ isLoading ? "CONNEXION..." : "LOGIN" }}
          </button>
        </form>

        <!-- Lien mot de passe oublié -->
        <div class="forgot-password">
          <a href="#" class="forgot-link">Mot de passe oublié</a>
        </div>
      </div>

      <!-- Message d'erreur -->
      <div v-if="error" class="error-message">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, reactive } from "vue";
  import { useRouter } from "vue-router";
  import { useAuthStore } from "@/stores/auth";

  export default {
    name: "LoginView",
    setup() {
      const router = useRouter();
      const authStore = useAuthStore();

      const isLoading = ref(false);
      const error = ref("");
      const credentials = reactive({
        login: "",
        password: "",
      });

      const handleLogin = async () => {
        error.value = "";

        if (!credentials.login || !credentials.password) {
          error.value = "Veuillez remplir tous les champs";
          return;
        }

        isLoading.value = true;

        try {
          await authStore.login(credentials);
          router.push("/");
        } catch (err) {
          error.value = err.message;
        } finally {
          isLoading.value = false;
        }
      };

      return {
        credentials,
        error,
        isLoading,
        handleLogin,
      };
    },
  };
</script>

<style scoped>
  .login-page {
    min-height: 100vh;
    background: #00b8d4;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: "Inter", sans-serif;
    overflow: hidden;
  }

  /* FORMES ORGANIQUES EN ARRIÈRE-PLAN */
  .background-shapes {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
  }

  .shape {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 20s infinite linear;
  }

  .shape-1 {
    width: 300px;
    height: 300px;
    top: -150px;
    right: -150px;
    background: rgba(10, 37, 64, 0.3);
    animation-delay: 0s;
  }

  .shape-2 {
    width: 200px;
    height: 200px;
    bottom: -100px;
    left: -100px;
    background: rgba(255, 255, 255, 0.1);
    animation-delay: -5s;
  }

  .shape-3 {
    width: 150px;
    height: 150px;
    top: 20%;
    left: 10%;
    background: rgba(10, 37, 64, 0.2);
    animation-delay: -10s;
  }

  .shape-4 {
    width: 400px;
    height: 400px;
    bottom: -200px;
    right: -200px;
    background: rgba(255, 255, 255, 0.05);
    animation-delay: -15s;
  }

  @keyframes float {
    0%,
    100% {
      transform: translateY(0px) rotate(0deg);
    }

    25% {
      transform: translateY(-20px) rotate(90deg);
    }

    50% {
      transform: translateY(0px) rotate(180deg);
    }

    75% {
      transform: translateY(20px) rotate(270deg);
    }
  }

  /* CONTENU PRINCIPAL */
  .login-container {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 400px;
    padding: 0 2rem;
    text-align: center;
  }

  /* LOGO */
  .logo-section {
    margin-bottom: 3rem;
  }

  .logo-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    margin: 0 auto 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  .logo-icon svg {
    width: 40px;
    height: 40px;
    color: white;
    stroke-width: 1.5;
  }

  .logo-text {
    color: white;
    font-size: 2rem;
    font-weight: 300;
    margin: 0;
    letter-spacing: 1px;
  }

  /* FORMULAIRE */
  .login-form-container {
    background: white;
    border-radius: 20px;
    padding: 2.5rem 2rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    margin-bottom: 1.5rem;
  }

  .login-form {
    margin-bottom: 1.5rem;
  }

  .form-field {
    margin-bottom: 1.5rem;
  }

  .input-wrapper {
    position: relative;
  }

  .input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
  }

  .input-icon svg {
    width: 20px;
    height: 20px;
    color: #6b7280;
    stroke-width: 1.5;
  }

  .form-input {
    width: 100%;
    background: #f9fafb;
    border: 2px solid #f3f4f6;
    border-radius: 12px;
    padding: 16px 16px 16px 50px;
    font-size: 14px;
    font-weight: 500;
    color: #1f2937;
    transition: all 0.2s ease;
  }

  .form-input::placeholder {
    color: #9ca3af;
    font-weight: 400;
    letter-spacing: 0.5px;
  }

  .form-input:focus {
    outline: none;
    border-color: #00b8d4;
    background: white;
    box-shadow: 0 0 0 3px rgba(0, 184, 212, 0.1);
  }

  .form-input:focus + .input-icon svg {
    color: #00b8d4;
  }

  /* BOUTON LOGIN */
  .login-button {
    width: 100%;
    background: #00b8d4;
    color: white;
    border: none;
    border-radius: 12px;
    padding: 16px;
    font-size: 16px;
    font-weight: 600;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-transform: uppercase;
  }

  .login-button:hover:not(:disabled) {
    background: #0891a6;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(0, 184, 212, 0.3);
  }

  .login-button:active {
    transform: translateY(0);
  }

  .login-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
  }

  /* LIEN MOT DE PASSE OUBLIÉ */
  .forgot-password {
    text-align: center;
  }

  .forgot-link {
    color: #6b7280;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: color 0.2s ease;
  }

  .forgot-link:hover {
    color: #00b8d4;
    text-decoration: underline;
  }

  /* MESSAGE D'ERREUR */
  .error-message {
    background: rgb(239, 68, 68);
    color: white;
    padding: 1rem;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid rgb(255, 0, 0);
  }

  /* RESPONSIVE */
  @media (max-width: 480px) {
    .login-container {
      padding: 0 1rem;
    }

    .login-form-container {
      padding: 2rem 1.5rem;
    }

    .logo-text {
      font-size: 1.5rem;
    }

    .logo-icon {
      width: 60px;
      height: 60px;
    }

    .logo-icon svg {
      width: 30px;
      height: 30px;
    }
  }
</style>
