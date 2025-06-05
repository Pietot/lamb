import { existsSync, writeFileSync } from "fs";
import { join, dirname } from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const backendEnvPath = join(__dirname, "backend", ".env");
const backendEnvLines = [
  "DB_HOST=",
  "DB_NAME=",
  "DB_USER=",
  "DB_PASSWORD=",
  "",
  "DOMAIN=http://localhost:5173",
];
const backendEnvContent = backendEnvLines.join("\n");
if (!existsSync(backendEnvPath)) {
  writeFileSync(backendEnvPath, backendEnvContent);
  console.log("backend/.env created with default values.");
} else {
  console.log("backend/.env already exists. Skipping creation.");
}

const envLocalPath = join(__dirname, ".env.local");
const envLocalLines = ["VITE_API_URL=http://localhost:5173/backend/api/"];
const envLocalContent = envLocalLines.join("\n");
if (!existsSync(envLocalPath)) {
  writeFileSync(envLocalPath, envLocalContent);
  console.log(".env.local created with default values.");
} else {
  console.log(".env.local already exists. Skipping creation.");
}

const envProdPath = join(__dirname, ".env.prod");
const envProdLines = ["VITE_API_URL=https://lamb.com/backend/api/"];
const envProdContent = envProdLines.join("\n");
if (!existsSync(envProdPath)) {
  writeFileSync(envProdPath, envProdContent);
  console.log(".env.prod created with default values.");
} else {
  console.log(".env.prod already exists. Skipping creation.");
}
