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
    <aside class="w-72 bg-surface-container-low border-r border-outline-variant/20 hidden md:flex flex-col sticky top-0 h-screen">
      <div class="p-8">
        <router-link to="/" class="flex flex-col">
          <span class="text-xs font-bold text-primary tracking-[0.3em] uppercase mb-1">Golkrie</span>
          <span class="text-2xl font-black text-white uppercase font-lexend tracking-tighter">Control Panel</span>
        </router-link>
      </div>
      
      <nav class="flex-1 px-4 py-2 space-y-1">
        <router-link to="/admin" 
          class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 group"
          active-class="bg-primary text-black shadow-lg shadow-primary/20"
          :class="$route.path === '/admin' ? '' : 'text-on-surface-variant hover:bg-white/5 hover:text-white'"
        >
          <span class="material-symbols-outlined text-2xl">dashboard</span>
          <span class="font-bold tracking-tight">Dashboard</span>
        </router-link>
        
        <router-link to="/admin/matches" 
          class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 group"
          active-class="bg-primary text-black shadow-lg shadow-primary/20"
          :class="$route.path === '/admin/matches' ? '' : 'text-on-surface-variant hover:bg-white/5 hover:text-white'"
        >
          <span class="material-symbols-outlined text-2xl">sports_soccer</span>
          <span class="font-bold tracking-tight">Manage Matches</span>
        </router-link>

        <router-link to="/admin/members" 
          class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 group"
          active-class="bg-primary text-black shadow-lg shadow-primary/20"
          :class="$route.path === '/admin/members' ? '' : 'text-on-surface-variant hover:bg-white/5 hover:text-white'"
        >
          <span class="material-symbols-outlined text-2xl">group</span>
          <span class="font-bold tracking-tight">Members List</span>
        </router-link>

        <router-link to="/admin/sponsors" 
          class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 group"
          active-class="bg-primary text-black shadow-lg shadow-primary/20"
          :class="$route.path === '/admin/sponsors' ? '' : 'text-on-surface-variant hover:bg-white/5 hover:text-white'"
        >
          <span class="material-symbols-outlined text-2xl">handshake</span>
          <span class="font-bold tracking-tight">Our Sponsors</span>
        </router-link>

        <div class="pt-6 pb-2">
          <div class="px-4 text-[10px] font-black uppercase text-white/20 tracking-widest">Configuration</div>
        </div>

        <router-link to="/admin/settings" 
          class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 group"
          active-class="bg-primary text-black shadow-lg shadow-primary/20"
          :class="$route.path === '/admin/settings' ? '' : 'text-on-surface-variant hover:bg-white/5 hover:text-white'"
        >
          <span class="material-symbols-outlined text-2xl">settings</span>
          <span class="font-bold tracking-tight">Global Settings</span>
        </router-link>
      </nav>

      <div class="p-6">
        <router-link to="/" class="flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-surface-container border border-outline-variant/30 text-on-surface-variant hover:text-red-400 hover:border-red-400/30 transition-all group">
          <span class="material-symbols-outlined text-xl group-hover:-translate-x-1 transition-transform">logout</span>
          <span class="font-bold text-sm">Exit Admin</span>
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
