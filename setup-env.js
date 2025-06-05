import { existsSync, readFileSync, appendFileSync } from "fs";
import { join } from "path";

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
        appendFileSync(filePath, line + "\n");
        addedLines.push(line);
      }
    }
    if (addedLines.length > 0) {
      console.log(
        `Missing variables found in ${filePath}.${"\n"} Added: ${"\n"}${addedLines.join("\n")}`
      );
    } else {
      console.log(`All required variables are present in ${filePath}.`);
    }
  } else {
    console.log(`${filePath} not found. Creating new file.`);
  }
}

ensureEnvVars(join("backend", ".env"), [
  "DB_HOST=",
  "DB_NAME=",
  "DB_USER=",
  "DB_PASSWORD=",
  "DOMAIN=",
]);
ensureEnvVars(".env.local", ["VITE_API_URL="]);
ensureEnvVars(".env.prod", ["VITE_API_URL="]);
