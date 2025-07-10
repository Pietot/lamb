<template>
  <div
    class="flex flex-col items-center justify-center bg-bleu-profond rounded-lg p-2"
    style="height: -webkit-fill-available"
  >
    <BaseCard class="w-full max-w-2xl h-fit max-h-screen overflow-y-auto" padding="sm">
      <div class="text-center mb-4">
        <h2 class="text-2xl sm:text-3xl font-bold text-turquoise mb-2">🃏 Blackjack Casino 🃏</h2>
        <div
          class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-black px-3 py-1 rounded-full inline-block font-bold text-sm sm:text-lg"
        >
          💰 {{ playerChips }} Points
        </div>
      </div>

      <!-- Écran de démarrage -->
      <div v-if="gameState === 'start'" class="flex flex-col items-center space-y-3">
        <div class="text-center mb-2">
          <p class="text-lg sm:text-xl">Bienvenue au Blackjack Casino !</p>
          <p class="text-xs sm:text-sm opacity-75">Rapprochez-vous de 21 sans dépasser</p>
        </div>
        <button
          role="button"
          aria-label="Nouvelle Partie"
          class="btn-primary text-lg sm:text-xl px-6 py-3"
          @click="startGame"
        >
          🎮 Nouvelle Partie
        </button>
      </div>

      <!-- Écran de pari -->
      <div v-else-if="gameState === 'betting'" class="flex flex-col items-center space-y-3">
        <div class="text-black text-center mb-2">
          <p class="text-lg sm:text-xl">Placez votre mise</p>
          <p class="text-xs sm:text-sm opacity-75">Choisissez le montant que vous voulez parier</p>
        </div>

        <div class="bg-black bg-opacity-30 p-3 rounded-lg w-full">
          <div class="text-white text-center mb-3">
            <span class="text-sm sm:text-lg">Mise actuelle: </span>
            <span class="text-lg sm:text-2xl font-bold text-yellow-400"
              >{{ currentBet }} points</span
            >
          </div>

          <div class="grid grid-cols-3 gap-2 mb-3">
            <button
              role="button"
              aria-label="Choisir une mise"
              v-for="amount in betAmounts"
              :key="amount"
              @click="setBet(amount)"
              :disabled="amount > playerChips"
              :class="[
                'px-2 py-2 rounded-lg font-bold transition-all text-sm',
                amount <= playerChips
                  ? 'bg-turquoise hover:bg-turquoise-dark text-white hover:scale-105'
                  : 'bg-gray-600 text-gray-400 cursor-not-allowed',
              ]"
            >
              {{ amount }}
            </button>
          </div>

          <div class="flex justify-center">
            <button
              role="button"
              aria-label="Miser"
              @click="dealCards"
              :disabled="currentBet === 0"
              :class="[
                'px-4 py-2 rounded-lg font-bold transition-all text-sm',
                currentBet > 0
                  ? 'bg-green-600 hover:bg-green-700 text-white hover:scale-105'
                  : 'bg-gray-600 text-gray-400 cursor-not-allowed',
              ]"
            >
              ✅ Miser {{ currentBet }} points
            </button>
          </div>
        </div>
      </div>

      <!-- Jeu en cours -->
      <div v-else-if="gameState === 'playing' || gameState === 'dealer' || gameState === 'end'">
        <!-- Informations de la partie -->
        <div class="bg-black bg-opacity-30 p-2 rounded-lg mb-3">
          <div class="grid grid-cols-3 gap-2 text-white text-center">
            <div>
              <div class="text-xs opacity-75">Mise</div>
              <div class="text-sm sm:text-xl font-bold text-yellow-400">
                {{ currentBet }}
              </div>
            </div>
            <div>
              <div class="text-xs opacity-75">Gagnées</div>
              <div class="text-sm sm:text-xl font-bold text-green-400">
                {{ gamesWon }}
              </div>
            </div>
            <div>
              <div class="text-xs opacity-75">Perdues</div>
              <div class="text-sm sm:text-xl font-bold text-red-400">
                {{ gamesLost }}
              </div>
            </div>
          </div>
        </div>

        <!-- Main du croupier -->
        <div class="mb-3">
          <div class="text-center mb-2">
            <div class="text-sm sm:text-lg font-bold text-gray mb-1">
              🎩 Croupier {{ showDealerTotal ? `(${dealerTotal})` : "" }}
            </div>
            <div class="text-xs text-gray-300">
              {{ showDealerTotal ? getDealerStatus() : "Carte cachée" }}
            </div>
          </div>
          <div class="flex flex-wrap justify-center gap-1">
            <div
              v-for="(card, i) in dealerHand"
              :key="'d' + i"
              :class="[
                'card-container transition-all duration-300',
                i === 0 || showDealerTotal ? 'card-revealed' : 'card-hidden',
              ]"
            >
              <div class="card">
                {{ i === 0 || showDealerTotal ? cardToString(card) : "🂠" }}
              </div>
            </div>
          </div>
        </div>

        <!-- Main du joueur -->
        <div class="mb-3">
          <div class="text-center mb-2">
            <div class="text-sm sm:text-lg font-bold text-gray mb-1">
              🎯 Votre main ({{ playerTotal }})
            </div>
            <div class="text-xs text-gray-300">
              {{ getPlayerStatus() }}
            </div>
          </div>
          <div class="flex flex-wrap justify-center gap-1">
            <div
              v-for="(card, i) in playerHand"
              :key="'p' + i"
              class="card-container card-revealed"
            >
              <div class="card player-card">
                {{ cardToString(card) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Actions du joueur -->
        <div v-if="gameState === 'playing'" class="flex justify-center gap-3">
          <button
            role="button"
            aria-label="Tirer une carte"
            class="btn-action btn-hit"
            @click="hit"
            :disabled="playerTotal >= 21"
          >
            🎯 Tirer
          </button>
          <button role="button" aria-label="Rester" class="btn-action btn-stand" @click="stand">
            ✋ Rester
          </button>
        </div>

        <!-- Message du croupier -->
        <div v-else-if="gameState === 'dealer'" class="text-center text-white">
          <div class="text-lg animate-pulse">🎲 Le croupier joue...</div>
        </div>

        <!-- Résultat final -->
        <div v-else-if="gameState === 'end'" class="flex flex-col items-center space-y-3">
          <div class="text-center">
            <div class="text-lg sm:text-2xl font-bold mb-1" :class="resultClass">
              {{ resultMessage }}
            </div>
            <div class="text-sm sm:text-lg text-gray">
              {{ winLossMessage }}
            </div>
          </div>
          <button
            role="button"
            aria-label="Rejouer"
            class="btn-primary text-lg px-6 py-3"
            @click="startGame"
          >
            🔄 Rejouer
          </button>
        </div>
      </div>

      <!-- Message si plus de points -->
      <div v-if="playerChips === 0 && gameState === 'start'" class="text-center mt-3">
        <div class="text-red-400 text-lg font-bold mb-2">😱 Plus de points !</div>
        <button
          role="button"
          aria-label="Recevoir 1000 points bonus"
          class="btn-primary text-sm px-4 py-2"
          @click="resetChips"
        >
          🎁 Recevoir 1000 points bonus
        </button>
      </div>
    </BaseCard>
  </div>
</template>

<script>
  import BaseCard from "@/components/ui/BaseCard.vue";

  function getRandomCard() {
    const values = [2, 3, 4, 5, 6, 7, 8, 9, 10, "J", "Q", "K", "A"];
    const suits = ["♠", "♥", "♦", "♣"];
    const value = values[Math.floor(Math.random() * values.length)];
    const suit = suits[Math.floor(Math.random() * 4)];
    return { value, suit };
  }

  function calculateTotal(hand) {
    let total = 0;
    let aces = 0;
    for (const card of hand) {
      if (typeof card.value === "number") total += card.value;
      else if (card.value === "A") {
        total += 11;
        aces++;
      } else total += 10;
    }
    while (total > 21 && aces > 0) {
      total -= 10;
      aces--;
    }
    return total;
  }

  export default {
    name: "BlackjackView",
    components: { BaseCard },
    data() {
      return {
        playerHand: [],
        dealerHand: [],
        gameState: "start", // start, betting, playing, dealer, end
        resultMessage: "",
        showDealerTotal: false,
        playerChips: 1000,
        currentBet: 0,
        betAmounts: [10, 25, 50, 100, 250, 500],
        gamesWon: 0,
        gamesLost: 0,
        winLossMessage: "",
        resultClass: "",
      };
    },
    computed: {
      playerTotal() {
        return calculateTotal(this.playerHand);
      },
      dealerTotal() {
        return calculateTotal(this.dealerHand);
      },
    },
    methods: {
      cardToString(card) {
        return card.value + card.suit;
      },

      startGame() {
        if (this.playerChips === 0) {
          this.resetChips();
          return;
        }
        this.gameState = "betting";
        this.currentBet = 0;
        this.resultMessage = "";
        this.showDealerTotal = false;
        this.winLossMessage = "";
      },

      setBet(amount) {
        if (amount <= this.playerChips) {
          this.currentBet = amount;
        }
      },

      dealCards() {
        if (this.currentBet === 0) return;

        this.playerHand = [getRandomCard(), getRandomCard()];
        this.dealerHand = [getRandomCard(), getRandomCard()];
        this.gameState = "playing";

        // Vérifier blackjack naturel
        if (this.playerTotal === 21) {
          this.checkForBlackjack();
        }
      },

      checkForBlackjack() {
        if (this.playerTotal === 21 && this.dealerTotal === 21) {
          this.endGame(
            "Égalité ! Blackjack des deux côtés",
            "text-yellow-400",
            "Votre mise est rendue",
          );
        } else if (this.playerTotal === 21) {
          this.endGame(
            "🎉 BLACKJACK ! 🎉",
            "text-yellow-400",
            `Vous gagnez ${Math.floor(this.currentBet * 1.5)} points (1.5x)`,
          );
          this.playerChips += Math.floor(this.currentBet * 1.5);
          this.gamesWon++;
        } else {
          this.stand();
        }
      },

      hit() {
        this.playerHand.push(getRandomCard());
        if (this.playerTotal > 21) {
          this.endGame(
            "💥 Perdu ! Vous avez dépassé 21",
            "text-red-400",
            `Vous perdez ${this.currentBet} points`,
          );
          this.playerChips -= this.currentBet;
          this.gamesLost++;
        } else if (this.playerTotal === 21) {
          this.stand();
        }
      },

      stand() {
        this.gameState = "dealer";
        this.showDealerTotal = true;
        this.dealerTurn();
      },

      dealerTurn() {
        setTimeout(() => {
          while (this.dealerTotal < 17) {
            this.dealerHand.push(getRandomCard());
          }
          this.checkWinner();
        }, 1000);
      },

      checkWinner() {
        const player = this.playerTotal;
        const dealer = this.dealerTotal;

        if (dealer > 21) {
          this.endGame(
            "🎉 Gagné ! Le croupier a dépassé 21",
            "text-green-400",
            `Vous gagnez ${this.currentBet} points`,
          );
          this.playerChips += this.currentBet;
          this.gamesWon++;
        } else if (player > dealer) {
          this.endGame(
            "🎉 Gagné ! Votre main est plus forte",
            "text-green-400",
            `Vous gagnez ${this.currentBet} points`,
          );
          this.playerChips += this.currentBet;
          this.gamesWon++;
        } else if (player < dealer) {
          this.endGame(
            "😔 Perdu ! Le croupier a une main plus forte",
            "text-red-400",
            `Vous perdez ${this.currentBet} points`,
          );
          this.playerChips -= this.currentBet;
          this.gamesLost++;
        } else {
          this.endGame("🤝 Égalité !", "text-yellow-400", "Votre mise est rendue");
        }
      },

      endGame(msg, cssClass, winLossMsg) {
        this.resultMessage = msg;
        this.resultClass = cssClass;
        this.winLossMessage = winLossMsg;
        this.gameState = "end";
        this.showDealerTotal = true;
      },

      getPlayerStatus() {
        if (this.playerTotal === 21) return "🎯 Parfait !";
        if (this.playerTotal > 21) return "💥 Trop haut !";
        if (this.playerTotal >= 17) return "🔥 Bonne main !";
        return "🎲 Continuez à jouer";
      },

      getDealerStatus() {
        if (this.dealerTotal === 21) return "🎯 Blackjack !";
        if (this.dealerTotal > 21) return "💥 Dépassé !";
        if (this.dealerTotal >= 17) return "✋ Doit rester";
        return "🎲 Doit tirer";
      },

      resetChips() {
        this.playerChips = 10;
        this.gamesWon = 0;
        this.gamesLost = 0;
      },
    },

    beforeRouteLeave(to, from, next) {
      localStorage.setItem("konamiUnlocked", "false");
      next();
    },
    beforeUnmount() {
      localStorage.setItem("konamiUnlocked", "false");
    },
  };

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      localStorage.setItem("konamiUnlocked", "false");
      window.history.back();
    }
  });
</script>

<style scoped>
  .bg-bleu-profond {
    background-color: var(--color-bleu-profond);
    background-image:
      radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(255, 192, 203, 0.15) 0%, transparent 50%);
  }

  .card-container {
    perspective: 1000px;
  }

  .card {
    background-color: white;
    color: #1f2937;
    border-radius: 0.5rem;
    padding: 0.5rem;
    font-size: 0.875rem;
    font-weight: bold;
    text-align: center;
    box-shadow:
      0 4px 6px -1px rgba(0, 0, 0, 0.1),
      0 2px 4px -1px rgba(0, 0, 0, 0.06);
    min-width: 35px;
    min-height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    transform-style: preserve-3d;
    transition: transform 0.3s ease;
  }

  @media (min-width: 640px) {
    .card {
      padding: 0.75rem;
      font-size: 1rem;
      min-width: 45px;
      min-height: 60px;
    }
  }

  .card:hover {
    transform: translateY(-2px) scale(1.05);
  }

  .player-card {
    background-color: var(--color-turquoise);
    color: white;
  }

  .card-hidden .card {
    background-color: #1f2937;
    color: #9ca3af;
  }

  .card-revealed {
    animation: cardReveal 0.5s ease-out;
  }

  @keyframes cardReveal {
    from {
      transform: rotateY(180deg);
      opacity: 0;
    }
    to {
      transform: rotateY(0deg);
      opacity: 1;
    }
  }

  .btn-primary {
    background-color: var(--color-turquoise);
    color: white;
    font-weight: bold;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    transition: all 0.2s;
    transform: scale(1);
    box-shadow:
      0 10px 15px -3px rgba(0, 0, 0, 0.1),
      0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border: none;
    cursor: pointer;
  }

  .btn-primary:hover {
    filter: brightness(0.9);
    transform: scale(1.05);
  }

  .btn-action {
    font-weight: bold;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    transition: all 0.2s;
    transform: scale(1);
    box-shadow:
      0 10px 15px -3px rgba(0, 0, 0, 0.1),
      0 4px 6px -2px rgba(0, 0, 0, 0.05);
    color: white;
    border: none;
    cursor: pointer;
  }

  .btn-action:hover {
    transform: scale(1.05);
  }

  .btn-hit {
    background-color: #059669;
  }

  .btn-hit:hover {
    background-color: #047857;
  }

  .btn-stand {
    background-color: #d97706;
  }

  .btn-stand:hover {
    background-color: #b45309;
  }

  .btn-primary:disabled,
  .btn-action:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
  }

  .btn-primary:disabled:hover,
  .btn-action:disabled:hover {
    transform: none;
    filter: none;
  }

  .animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }

  @keyframes pulse {
    0%,
    100% {
      opacity: 1;
    }
    50% {
      opacity: 0.5;
    }
  }
</style>
