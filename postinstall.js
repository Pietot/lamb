import { existsSync, writeFileSync } from "fs";
import { join, dirname } from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const GREEN = "\x1b[32m";
const RESET = "\x1b[0m";

const apiEnvPath = join(__dirname, "api/routes/utils/", ".env");
const backendEnvLines = [
  "DB_HOST=",
  "DB_NAME=",
  "DB_USER=",
  "DB_PASSWORD=",
  "",
  "DOMAIN=http://localhost:5173",
  "DOMAIN_WITHOUT_PORT=localhost",
  "DOMAIN_PROXY=",
  "DOMAIN_PC_AZALEE=",
  "",
  "COOKIE_SECURE=false",
  "API_ROUTE=",
];
const backendEnvContent = backendEnvLines.join("\n");
writeFileSync(apiEnvPath, backendEnvContent);
console.log(
  `${GREEN}api/routes/utils/.env créé avec des valeurs par défaut (écrasé si existait).${RESET}`,
);

const envLocalPath = join(__dirname, ".env.local");
const envLocalLines = ["VITE_API_PATH=/lamb/api/"];
const envLocalContent = envLocalLines.join("\n");
writeFileSync(envLocalPath, envLocalContent);
console.log(`${GREEN}.env.local créé avec des valeurs par défaut (écrasé si existait).${RESET}`);

const envProdPath = join(__dirname, ".env.production.local");
const envProdLines = ["VITE_API_PATH="];
const envProdContent = envProdLines.join("\n");
writeFileSync(envProdPath, envProdContent);
console.log(
  `${GREEN}.env.production.local créé avec des valeurs par défaut (écrasé si existait).${RESET}`,
);
