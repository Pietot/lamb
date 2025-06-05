import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd());

  return {
    plugins: [vue()],
    resolve: {
      alias: {
        "@": path.resolve(__dirname, "./src"),
      },
    },
    css: {
      postcss: "./postcss.config.js",
    },
    server: {
      proxy: {
        "/backend": {
          target: env.VITE_API_URL,
          changeOrigin: true,
          rewrite: (p) => p,
        },
      },
    },
  };
});
