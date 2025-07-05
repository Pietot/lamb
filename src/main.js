import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "./router";
import App from "./App.vue";
import "./style.css";

const app = createApp(App);

app.use(createPinia());
app.use(router);

app.mount("#app");

// Listen if the konami code is entered
let konamiCode = [
  "ArrowUp",
  "ArrowUp",
  "ArrowDown",
  "ArrowDown",
  "ArrowLeft",
  "ArrowRight",
  "ArrowLeft",
  "ArrowRight",
  "b",
  "a",
];
let konamiCodePosition = 0;
document.addEventListener("keydown", (event) => {
  const key = event.key;
  if (key === konamiCode[konamiCodePosition]) {
    konamiCodePosition++;
    if (konamiCodePosition === konamiCode.length) {
      // Débloquer la page secrète
      localStorage.setItem("konamiUnlocked", "true");
      router.push("/blackjack");
      konamiCodePosition = 0;
    }
  } else {
    konamiCodePosition = 0;
  }
});
