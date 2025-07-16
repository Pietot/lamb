export const VITE_API_URL = getApiUrl();

export const ADMIN = 1;
export const GESTIONAIRE = 2;
export const COMMERCIAL = 3;

function getApiUrl() {
  return `${window.location.protocol}//${window.location.hostname}${import.meta.env.VITE_API_PATH}`;
}
