<script setup>
</script>

<template>
  <div class="min-h-screen bg-background text-on-surface flex flex-col md:flex-row">
    <!-- Top Mobile Header -->
    <header class="md:hidden flex items-center justify-between px-6 h-16 bg-surface-container border-b border-outline-variant/30 sticky top-0 z-40">
      <router-link to="/" class="text-xl font-black text-primary uppercase font-lexend tracking-tighter">Golkrie Admin</router-link>
      <router-link to="/" class="p-2 text-on-surface-variant">
        <span class="material-symbols-outlined">logout</span>
      </router-link>
    </header>

    <!-- Sidebar (Desktop) -->
    <aside class="w-64 bg-surface-container border-r border-outline-variant/30 hidden md:flex flex-col sticky top-0 h-screen">
      <div class="p-6">
        <router-link to="/" class="text-2xl font-black text-primary uppercase font-lexend">Golkrie Admin</router-link>
      </div>
      
      <nav class="flex-1 px-4 py-4 space-y-2">
        <router-link to="/admin" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 text-on-surface-variant hover:text-white"
          active-class="bg-primary-container !text-on-primary-container"
        >
          <span class="material-symbols-outlined">dashboard</span>
          <span class="font-bold">Dashboard</span>
        </router-link>
        
        <router-link to="/admin/matches" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 text-on-surface-variant hover:text-white"
          active-class="bg-primary-container !text-on-primary-container"
        >
          <span class="material-symbols-outlined">sports_soccer</span>
          <span class="font-bold">Matches</span>
        </router-link>

        <router-link to="/admin/members" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 text-on-surface-variant hover:text-white"
          active-class="bg-primary-container !text-on-primary-container"
        >
          <span class="material-symbols-outlined">group</span>
          <span class="font-bold">Members</span>
        </router-link>

        <router-link to="/admin/sponsors" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 text-on-surface-variant hover:text-white"
          active-class="bg-primary-container !text-on-primary-container"
        >
          <span class="material-symbols-outlined">handshake</span>
          <span class="font-bold">Sponsors</span>
        </router-link>

        <router-link to="/admin/settings" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 text-on-surface-variant hover:text-white"
          active-class="bg-primary-container !text-on-primary-container"
        >
          <span class="material-symbols-outlined">settings</span>
          <span class="font-bold">Settings</span>
        </router-link>
      </nav>

      <div class="p-4 border-t border-outline-variant/30">
        <router-link to="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-on-surface-variant hover:text-red-400 transition-all">
          <span class="material-symbols-outlined">logout</span>
          <span class="font-bold">Back to Site</span>
        </router-link>
      </div>
    </aside>

    <!-- Bottom Mobile Nav -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-surface-container border-t border-outline-variant/20 flex justify-around items-center h-16 px-2">
      <router-link to="/admin" class="flex flex-col items-center gap-1 text-on-surface-variant" active-class="!text-primary">
        <span class="material-symbols-outlined text-xl">dashboard</span>
        <span class="text-[8px] font-bold uppercase tracking-widest">Dash</span>
      </router-link>
      <router-link to="/admin/matches" class="flex flex-col items-center gap-1 text-on-surface-variant" active-class="!text-primary">
        <span class="material-symbols-outlined text-xl">sports_soccer</span>
        <span class="text-[8px] font-bold uppercase tracking-widest">Matches</span>
      </router-link>
      <router-link to="/admin/members" class="flex flex-col items-center gap-1 text-on-surface-variant" active-class="!text-primary">
        <span class="material-symbols-outlined text-xl">group</span>
        <span class="text-[8px] font-bold uppercase tracking-widest">Users</span>
      </router-link>
      <router-link to="/admin/sponsors" class="flex flex-col items-center gap-1 text-on-surface-variant" active-class="!text-primary">
        <span class="material-symbols-outlined text-xl">handshake</span>
        <span class="text-[8px] font-bold uppercase tracking-widest">Sponsors</span>
      </router-link>
      <router-link to="/admin/settings" class="flex flex-col items-center gap-1 text-on-surface-variant" active-class="!text-primary">
        <span class="material-symbols-outlined text-xl">settings</span>
        <span class="text-[8px] font-bold uppercase tracking-widest">Settings</span>
      </router-link>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 p-4 md:p-8 mb-20 md:mb-0 overflow-x-hidden">
      <slot />
    </main>
  </div>
</template>
