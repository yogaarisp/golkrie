<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const email = ref('admin@keetech.my.id');
const password = ref('password');
const isLoading = ref(false);
const error = ref('');
const settings = ref({});

const fetchSettings = async () => {
  try {
    const response = await axios.get('/api/landing'); // We can use the public landing API to get settings
    settings.value = response.data.settings || {};
  } catch (e) {
    console.error('Failed to fetch settings');
  }
};

onMounted(fetchSettings);

const handleLogin = async () => {
  isLoading.value = true;
  error.value = '';
  try {
    const response = await axios.post('/api/login', {
      email: email.value,
      password: password.value
    });
    
    localStorage.setItem('token', response.data.access_token);
    localStorage.setItem('user', JSON.stringify(response.data.user));
    
    // Set axios default header for subsequent requests
    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;
    
    router.push('/admin');
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed. Please check your credentials.';
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-background p-6">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/10 blur-[120px] rounded-full"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary/5 blur-[120px] rounded-full"></div>
    </div>

    <div class="w-full max-w-md z-10">
      <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-surface-container mb-6 shadow-2xl border border-white/5 group transition-all hover:scale-105 overflow-hidden">
           <img v-if="settings.app_logo" :src="settings.app_logo" class="w-full h-full object-contain p-2" :alt="settings.app_name" />
           <span v-else class="material-symbols-outlined text-4xl text-primary group-hover:rotate-12 transition-transform">sports_soccer</span>
        </div>
        <h1 class="text-4xl font-black text-on-surface tracking-tighter uppercase font-lexend mb-2">
          Golkrie <span class="text-primary">Admin</span>
        </h1>
        <p class="text-on-surface-variant font-medium">Please sign in to your account</p>
      </div>

      <div class="bg-surface-container/60 backdrop-blur-xl p-8 rounded-[2.5rem] border border-white/10 shadow-2xl">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <div v-if="error" class="p-4 rounded-2xl bg-error/10 border border-error/20 text-error text-sm font-bold flex items-center gap-3 animate-shake">
            <span class="material-symbols-outlined text-lg">error</span>
            {{ error }}
          </div>

          <div class="space-y-2">
            <label class="text-xs font-black uppercase tracking-widest text-on-surface-variant ml-2">Email Address</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">mail</span>
              <input 
                v-model="email"
                type="email" 
                required
                class="w-full bg-surface-container-high border border-white/5 rounded-2xl py-4 pl-12 pr-4 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary/50 transition-all placeholder:text-on-surface-variant/30"
                placeholder="admin@example.com"
              />
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-xs font-black uppercase tracking-widest text-on-surface-variant ml-2">Password</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">lock</span>
              <input 
                v-model="password"
                type="password" 
                required
                class="w-full bg-surface-container-high border border-white/5 rounded-2xl py-4 pl-12 pr-4 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary/50 transition-all placeholder:text-on-surface-variant/30"
                placeholder="••••••••"
              />
            </div>
          </div>

          <button 
            type="submit"
            :disabled="isLoading"
            class="w-full bg-primary text-on-primary py-4 rounded-2xl font-black uppercase tracking-widest hover:scale-[1.02] active:scale-[0.98] transition-all shadow-xl shadow-primary/20 flex items-center justify-center gap-3 disabled:opacity-50 disabled:hover:scale-100"
          >
            <span v-if="isLoading" class="animate-spin material-symbols-outlined">progress_activity</span>
            <span v-else>Sign In</span>
          </button>
        </form>
      </div>

      <p class="text-center mt-8 text-on-surface-variant/50 text-xs font-bold uppercase tracking-widest">
        &copy; 2026 Golkrie Community
      </p>
    </div>
  </div>
</template>

<style scoped>
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}
.animate-shake {
  animation: shake 0.2s ease-in-out 0s 2;
}
</style>
