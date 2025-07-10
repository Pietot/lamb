<template>
  <div class="error-page">
    <div class="error-container">
      <div class="error-icon">
        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
          <circle cx="16" cy="16" id="BG" r="16" style="fill: #d72828" />
          <path
            d="M14.5,25h3v-3h-3V25z M14.5,6v13h3V6H14.5z"
            id="Exclamatory_x5F_Sign"
            style="fill: #e6e6e6"
          />
        </svg>
      </div>

      <div class="error-code">404</div>

      <div class="error-message">
        <h1 class="error-title">Page non trouvée</h1>
        <p class="error-description">
          Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
        </p>
      </div>

      <div class="error-actions">
        <button role="button" aria-label="Retour à l'accueil" class="btn-primary" @click="goHome">
          <svg class="btn-icon" viewBox="0 0 24 24" fill="currentColor">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
          </svg>
          Retour à l'accueil
        </button>

        <button role="button" aria-label="Page précédente" class="btn-secondary" @click="goBack">
          <svg class="btn-icon" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
          </svg>
          Page précédente
        </button>
      </div>

      <div class="suggestions-section">
        <h2 class="suggestions-title">Liens utiles</h2>
        <div class="suggestions-grid">
          <a href="/" class="suggestion-card">
            <div class="suggestion-icon orders-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
              </svg>
            </div>
            <div class="suggestion-content">
              <p class="suggestion-label">Dashboard</p>
              <p class="suggestion-desc">Vue d'ensemble</p>
            </div>
          </a>

          <a href="/stocks" class="suggestion-card">
            <div class="suggestion-icon delivery-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                  d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"
                />
              </svg>
            </div>
            <div class="suggestion-content">
              <p class="suggestion-label">Gestion des stocks</p>
              <p class="suggestion-desc">Inventaire</p>
            </div>
          </a>

          <a href="/orders" class="suggestion-card">
            <div class="suggestion-icon alert-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" />
                <polyline points="14,2 14,8 20,8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
              </svg>
            </div>
            <div class="suggestion-content">
              <p class="suggestion-label">Commandes</p>
              <p class="suggestion-desc">Suivi des commandes</p>
            </div>
          </a>

          <a href="/prepare" class="suggestion-card">
            <div class="suggestion-icon alerts-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <rect x="1" y="3" width="15" height="13" />
                <polygon points="16,3 19,7 19,13 16,13" />
                <circle cx="5.5" cy="18.5" r="2.5" />
                <circle cx="18.5" cy="18.5" r="2.5" />
              </svg>
            </div>
            <div class="suggestion-content">
              <p class="suggestion-label">Livraisons</p>
              <p class="suggestion-desc">Réceptions</p>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "Error404Page",
    metaInfo: {
      meta: [
        {
          name: "description",
          content:
            "Page non trouvée - Fashion Chic",
        },
      ],
    },
    methods: {
      goHome() {
        this.$router.push("/");
      },
      goBack() {
        window.history.back();
      },
    },
  };
</script>

<style scoped>
  .error-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    font-family: "Inter", sans-serif;
    padding: 2rem;
  }

  .error-container {
    max-width: 600px;
    width: 100%;
    text-align: center;
  }

  .error-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 2rem;
    background: #fef2f2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #dc2626;
  }

  .error-icon svg {
    width: 40px;
    height: 40px;
  }

  .error-code {
    font-size: 8rem;
    font-weight: 900;
    color: #0f172a;
    line-height: 1;
    margin-bottom: 1rem;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .error-message {
    margin-bottom: 3rem;
  }

  .error-title {
    font-size: 2rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 1rem 0;
  }

  .error-description {
    font-size: 1.125rem;
    color: #64748b;
    margin: 0;
    line-height: 1.6;
  }

  .error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 3rem;
    flex-wrap: wrap;
  }

  .btn-primary,
  .btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-primary {
    background: #2563eb;
    color: white;
  }

  .btn-primary:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
  }

  .btn-secondary {
    background: white;
    color: #64748b;
    border: 1px solid #e2e8f0;
  }

  .btn-secondary:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .btn-icon {
    width: 16px;
    height: 16px;
  }

  .suggestions-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
  }

  .suggestions-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 1.5rem 0;
  }

  .suggestions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }

  .suggestion-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .suggestion-card:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    transform: translateY(-1px);
  }

  .suggestion-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .suggestion-icon svg {
    width: 20px;
    height: 20px;
  }

  .alert-icon {
    background: #fef2f2;
    color: #dc2626;
  }

  .orders-icon {
    background: #eff6ff;
    color: #2563eb;
  }

  .delivery-icon {
    background: #f0fdf4;
    color: #059669;
  }

  .alerts-icon {
    background: #fffbeb;
    color: #d97706;
  }

  .suggestion-content {
    flex: 1;
    text-align: left;
  }

  .suggestion-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 0.25rem 0;
  }

  .suggestion-desc {
    font-size: 0.75rem;
    color: #64748b;
    margin: 0;
  }

  @media (max-width: 768px) {
    .error-page {
      padding: 1rem;
    }

    .error-code {
      font-size: 6rem;
    }

    .error-title {
      font-size: 1.5rem;
    }

    .error-description {
      font-size: 1rem;
    }

    .error-actions {
      flex-direction: column;
      align-items: center;
    }

    .btn-primary,
    .btn-secondary {
      width: 100%;
      max-width: 280px;
      justify-content: center;
    }

    .suggestions-grid {
      grid-template-columns: 1fr;
    }

    .suggestions-section {
      padding: 1.5rem;
    }
  }

  @media (max-width: 480px) {
    .error-code {
      font-size: 4rem;
    }

    .suggestion-card {
      padding: 0.75rem;
    }

    .suggestion-icon {
      width: 32px;
      height: 32px;
    }

    .suggestion-icon svg {
      width: 16px;
      height: 16px;
    }
  }
</style>
