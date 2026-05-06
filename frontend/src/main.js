import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'

import axios from 'axios'

// Axios configuration
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || ''
const token = localStorage.getItem('token')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

// Global response interceptor for 401
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

const app = createApp(App)
app.use(router)
app.mount('#app')

