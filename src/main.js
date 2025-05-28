import { useAuthStore } from "@/stores/auth";
import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "./router";
import App from "./App.vue";
import "./style.css";

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

const authStore = useAuthStore();

authStore.initAuth().then(() => {
  app.mount("#app");
});
