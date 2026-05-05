<script setup>
import { ref } from 'vue';

defineProps({
  settings: {
    type: Object,
    default: () => ({})
  }
});

const isMenuOpen = ref(false);
</script>

<template>
  <div class="min-h-screen bg-background text-on-surface">
    <!-- TopNavBar -->
    <header class="fixed top-0 w-full z-50 bg-surface-container/80 backdrop-blur-md border-b border-outline-variant/30">
      <nav class="flex justify-between items-center h-20 px-6 max-w-7xl mx-auto">
        <router-link to="/" class="flex items-center gap-3">
          <img v-if="settings.app_logo" :src="settings.app_logo" class="h-10 w-auto object-contain" :alt="settings.app_name" />
          <span v-else class="text-2xl font-black text-primary tracking-tighter uppercase font-lexend">
            {{ settings.app_name || 'Golkrie' }}
          </span>
        </router-link>
        
          <a href="#home" class="text-on-surface/70 hover:text-white transition-colors font-bold uppercase tracking-wider text-sm">Home</a>
          <a href="#schedule" class="text-on-surface/70 hover:text-white transition-colors font-bold uppercase tracking-wider text-sm">Schedule</a>
          <a href="#sponsors" class="text-on-surface/70 hover:text-white transition-colors font-bold uppercase tracking-wider text-sm">Sponsors</a>
          <router-link to="/admin" class="text-on-surface/70 hover:text-white transition-colors font-bold uppercase tracking-wider text-sm">Admin</router-link>

        <div class="flex items-center gap-4">
          <button class="hidden md:block bg-primary-container text-on-primary-container px-6 py-2 rounded-full font-bold text-sm hover:scale-105 transition-all active:scale-95">
            Join Next Match
          </button>
        </div>
      </nav>
    </header>

    <main class="pt-20 pb-24 md:pb-0">
      <slot />
    </main>

    <!-- Bottom Navigation (Mobile Only) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-surface-container/90 backdrop-blur-xl border-t border-white/5 pb-safe">
        <div class="flex justify-around items-center h-20 px-4 relative">
            <!-- Home -->
            <router-link to="/" class="flex flex-col items-center gap-1 transition-all" :class="$route.path === '/' ? 'text-primary' : 'text-on-surface-variant'">
                <span class="material-symbols-outlined text-2xl" :class="{'fill-1': $route.path === '/'}">home</span>
                <span class="text-[10px] font-bold uppercase tracking-widest">Home</span>
            </router-link>

            <!-- Schedule -->
            <a href="#schedule" class="flex flex-col items-center gap-1 text-on-surface-variant">
                <span class="material-symbols-outlined text-2xl">calendar_month</span>
                <span class="text-[10px] font-bold uppercase tracking-widest">Match</span>
            </a>
            
            <!-- Central Action Button (Floating) -->
            <div class="relative -translate-y-6">
                <div class="absolute inset-0 bg-primary/30 blur-xl rounded-full"></div>
                <button class="relative bg-primary-container p-4 rounded-full shadow-2xl border-4 border-background flex items-center justify-center group active:scale-90 transition-all">
                    <span class="material-symbols-outlined text-white text-3xl group-hover:rotate-12 transition-transform">sports_soccer</span>
                </button>
                <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[10px] font-black uppercase text-primary tracking-tighter">Join</span>
            </div>

            <!-- Gallery -->
            <a href="#sponsors" class="flex flex-col items-center gap-1 text-on-surface-variant">
                <span class="material-symbols-outlined text-2xl">handshake</span>
                <span class="text-[10px] font-bold uppercase tracking-widest">Sponsors</span>
            </a>

            <!-- Admin -->
            <router-link to="/admin" class="flex flex-col items-center gap-1 text-on-surface-variant">
                <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
                <span class="text-[10px] font-bold uppercase tracking-widest">Admin</span>
            </router-link>
        </div>
    </nav>

    <!-- Footer -->
    <footer class="bg-surface-container-low w-full py-12 border-t border-outline-variant/30">
      <div class="flex flex-col md:flex-row justify-between items-center px-6 max-w-7xl mx-auto gap-8">
        <div class="text-xl font-bold text-on-surface">{{ settings.app_name || 'Golkrie' }}</div>
        <div class="text-sm text-on-surface-variant text-center md:text-left">
          {{ settings.footer_text || '© 2024 Golkrie Community. Golek Kringet, Jalin Seduluran.' }}
        </div>
        <div class="flex gap-6">
          <a href="#" class="text-on-surface-variant hover:text-primary transition-colors text-sm">Privacy</a>
          <a href="#" class="text-on-surface-variant hover:text-primary transition-colors text-sm">Terms</a>
          <a :href="settings.instagram_url || '#'" target="_blank" class="text-on-surface-variant hover:text-primary transition-colors text-sm">Instagram</a>
        </div>
      </div>
    </footer>
  </div>
</template>
