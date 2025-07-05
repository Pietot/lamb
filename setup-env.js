import { existsSync, readFileSync, appendFileSync } from "fs";

const RED = "\x1b[31m";
const YELLOW = "\x1b[33m";
const GREEN = "\x1b[32m";
const BLUE = "\x1b[34m";
const PURPLE = "\x1b[35m";
const RESET = "\x1b[0m";

function ensureEnvVars(filePath, requiredLines) {
  let content = "";
  if (existsSync(filePath)) {
    content = readFileSync(filePath, "utf-8");
    let addedLines = [];
    const lines = content.split(/\r?\n/);
    for (const line of requiredLines) {
      const inSet = lines.some((existingLine) => existingLine.includes(line));
      if (!inSet) {
        lines.push(line);
        appendFileSync(filePath, "\n" + line);
        addedLines.push(line);
      }
    }
    if (addedLines.length > 0) {
      console.log(
        `${YELLOW}Added missing variables found in ${PURPLE}${filePath}${RESET}.`
      );
    } else {
      console.log(
        `${GREEN}All required variables are present in ${PURPLE}${filePath}${RESET}`
      );
    }
  } else {
    console.log(
      `${RED}${filePath} not found. Please run:\n\n${BLUE}npm install\n${RESET}`
    );
  }
}

ensureEnvVars("api/routes/utils/.env", [
  "DB_HOST=",
  "DB_NAME=",
  "DB_USER=",
  "DB_PASSWORD=",
  "DOMAIN=",
  "DOMAIN_WITHOUT_PORT=",
  "DOMAIN_PROXY=",
  "DOMAIN_PC_AZALEE=",
  "COOKIE_SECURE=",
  "API_ROUTE=",
]);
ensureEnvVars(".env.local", ["VITE_API_URL="]);
ensureEnvVars(".env.production.local", ["VITE_API_URL="]);
