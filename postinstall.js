import { existsSync, writeFileSync } from "fs";
import { join, dirname } from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const GREEN = "\x1b[32m";
const BLUE = "\x1b[34m";
const RESET = "\x1b[0m";

const apiEnvPath = join(__dirname, "api/routes/utils/", ".env");
const backendEnvLines = [
  "DB_HOST=",
  "DB_NAME=",
  "DB_USER=",
  "DB_PASSWORD=",
  "",
  "DOMAIN=http://localhost:5173",
  "API_KEY=",
  "API_ROUTE=",
];
const backendEnvContent = backendEnvLines.join("\n");
if (!existsSync(apiEnvPath)) {
  writeFileSync(apiEnvPath, backendEnvContent);
  console.log(
    `${BLUE}api/routes/utils/.env created with default values.${RESET}`
  );
} else {
  console.log(
    `${GREEN}api/routes/utils/.env already exists. Skipping creation.${RESET}`
  );
}

const envLocalPath = join(__dirname, ".env.local");
const envLocalLines = [
  "VITE_API_URL=http://localhost/lamb/api/",
  "VITE_API_KEY=",
];
const envLocalContent = envLocalLines.join("\n");
if (!existsSync(envLocalPath)) {
  writeFileSync(envLocalPath, envLocalContent);
  console.log(`${BLUE}.env.local created with default values.${RESET}`);
} else {
  console.log(`${GREEN}.env.local already exists. Skipping creation.${RESET}`);
}

const envProdPath = join(__dirname, ".env.prod");
const envProdLines = ["VITE_API_URL=https://lamb.com/api/", "VITE_API_KEY="];
const envProdContent = envProdLines.join("\n");
if (!existsSync(envProdPath)) {
  writeFileSync(envProdPath, envProdContent);
  console.log(`${BLUE}.env.prod created with default values.${RESET}`);
} else {
  console.log(`${GREEN}.env.prod already exists. Skipping creation.${RESET}`);
}
