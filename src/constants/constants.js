export const VITE_API_URL = getApiUrl();

function getApiUrl() {
  return `${window.location.protocol}//${window.location.hostname}${import.meta.env.VITE_API_PATH}`;
}
