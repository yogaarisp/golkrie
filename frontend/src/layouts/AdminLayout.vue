<script setup>
</script>

<template>
  <div class="min-h-screen bg-background text-on-surface flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-surface-container border-r border-outline-variant/30 hidden md:flex flex-col">
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
          <span class="font-bold">Manage Matches</span>
        </router-link>

        <router-link to="/admin/settings" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 text-on-surface-variant hover:text-white"
          active-class="bg-primary-container !text-on-primary-container"
        >
          <span class="material-symbols-outlined">settings</span>
          <span class="font-bold">Site Settings</span>
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
      </nav>

      <div class="p-4 border-t border-outline-variant/30">
        <router-link to="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-on-surface-variant hover:text-red-400 transition-all">
          <span class="material-symbols-outlined">logout</span>
          <span class="font-bold">Back to Site</span>
        </router-link>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
      <slot />
    </main>
  </div>
</template>
