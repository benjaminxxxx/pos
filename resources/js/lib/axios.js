import axios from 'axios'

// Obtener el token CSRF del meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const api = axios.create({
  baseURL: '/api',
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
    ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }) // Agrega el token solo si existe
  },
  withCredentials: true
})

export default api
