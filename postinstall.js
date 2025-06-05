import { existsSync, writeFileSync } from "fs";
import { join, dirname } from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const apiEnvPath = join(__dirname, "api/routes/utils/", ".env");
const backendEnvLines = [
  "DB_HOST=",
  "DB_NAME=",
  "DB_USER=",
  "DB_PASSWORD=",
  "",
  "DOMAIN=http://localhost:5173",
];
const backendEnvContent = backendEnvLines.join("\n");
if (!existsSync(apiEnvPath)) {
  writeFileSync(apiEnvPath, backendEnvContent);
  console.log("api/routes/utils/.env created with default values.");
} else {
  console.log("api/routes/utils/.env already exists. Skipping creation.");
}

const envLocalPath = join(__dirname, ".env.local");
const envLocalLines = ["VITE_API_URL=http://localhost/lamb/api/"];
const envLocalContent = envLocalLines.join("\n");
if (!existsSync(envLocalPath)) {
  writeFileSync(envLocalPath, envLocalContent);
  console.log(".env.local created with default values.");
} else {
  console.log(".env.local already exists. Skipping creation.");
}

const envProdPath = join(__dirname, ".env.prod");
const envProdLines = ["VITE_API_URL=https://lamb.com/api/"];
const envProdContent = envProdLines.join("\n");
if (!existsSync(envProdPath)) {
  writeFileSync(envProdPath, envProdContent);
  console.log(".env.prod created with default values.");
} else {
  console.log(".env.prod already exists. Skipping creation.");
}
