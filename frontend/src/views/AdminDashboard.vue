<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminLayout from '../layouts/AdminLayout.vue';

const stats = ref({ totalMembers: 0, upcomingMatches: 0, finishedMatches: 0 });
const pendingRegistrations = ref([]);
const loading = ref(true);

const fetchDashboardData = async () => {
  try {
    const response = await axios.get('/api/admin/dashboard');
    stats.value = response.data.stats;
    pendingRegistrations.value = response.data.pendingRegistrations;
  } catch (e) {
    console.error('Failed to fetch admin data', e);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchDashboardData);

const acceptPlayer = async (id) => {
  if (confirm('Terima pendaftaran ini?')) {
    try {
      await axios.post(`/api/admin/registrations/${id}/accept`);
      fetchDashboardData();
    } catch (e) {
      alert('Gagal menerima pendaftaran.');
    }
  }
};

const rejectPlayer = async (id) => {
  if (confirm('Tolak pendaftaran ini?')) {
    try {
      await axios.delete(`/api/admin/registrations/${id}/reject`);
      fetchDashboardData();
    } catch (e) {
      alert('Gagal menolak pendaftaran.');
    }
  }
};

const formatWA = (phone, name, matchName) => {
  const msg = `Halo ${name}, pendaftaran Golkrie kamu untuk ${matchName} sudah diterima. Silahkan konfirmasi pembayaran ya!`;
  return `https://wa.me/${phone.replace(/^0/, '62')}?text=${encodeURIComponent(msg)}`;
};
</script>

<template>
  <AdminLayout>
    <div class="mb-10">
      <h1 class="text-3xl font-bold text-white mb-2">Dashboard</h1>
      <p class="text-on-surface-variant">Ringkasan aktivitas komunitas Golkrie.</p>
    </div>

    <div v-if="loading" class="spinner"></div>

    <div v-else>
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
          <div class="glass-card p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
              <span class="material-symbols-outlined text-3xl">group</span>
            </div>
            <div>
              <p class="text-xs font-bold uppercase text-on-surface-variant">Total Members</p>
              <p class="text-3xl font-black text-white">{{ stats.totalMembers }}</p>
            </div>
          </div>
          <div class="glass-card p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-secondary/10 flex items-center justify-center text-secondary">
              <span class="material-symbols-outlined text-3xl">event</span>
            </div>
            <div>
              <p class="text-xs font-bold uppercase text-on-surface-variant">Upcoming Matches</p>
              <p class="text-3xl font-black text-white">{{ stats.upcomingMatches }}</p>
            </div>
          </div>
          <div class="glass-card p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-green-500/10 flex items-center justify-center text-green-500">
              <span class="material-symbols-outlined text-3xl">task_alt</span>
            </div>
            <div>
              <p class="text-xs font-bold uppercase text-on-surface-variant">Finished Matches</p>
              <p class="text-3xl font-black text-white">{{ stats.finishedMatches }}</p>
            </div>
          </div>
        </div>

        <!-- Pending Requests -->
        <div>
          <h2 class="text-2xl font-bold text-white mb-6">Pending Requests</h2>
          
          <div class="space-y-4">
            <div v-for="reg in pendingRegistrations" :key="reg.id" 
              class="glass-card p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-secondary-container/20 flex items-center justify-center text-secondary font-bold text-xs">
                  {{ reg.player_name.split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase() }}
                </div>
                <div>
                  <p class="text-white font-bold">{{ reg.player_name }}</p>
                  <p class="text-xs text-on-surface-variant">
                    {{ reg.match.match_name }} • {{ reg.position }} • {{ reg.member.phone_number }}
                  </p>
                </div>
              </div>
              
              <div class="flex items-center gap-2">
                <a :href="formatWA(reg.member.phone_number, reg.player_name, reg.match.match_name)" target="_blank" 
                  class="w-10 h-10 rounded-xl bg-green-500/10 text-green-500 flex items-center justify-center hover:bg-green-500/20 transition-all">
                  <span class="material-symbols-outlined text-xl">chat</span>
                </a>
                <button @click="acceptPlayer(reg.id)" class="w-10 h-10 rounded-xl bg-primary-container/20 text-primary flex items-center justify-center hover:bg-primary-container/30 transition-all">
                  <span class="material-symbols-outlined text-xl">check</span>
                </button>
                <button @click="rejectPlayer(reg.id)" class="w-10 h-10 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500/20 transition-all">
                  <span class="material-symbols-outlined text-xl">close</span>
                </button>
              </div>
            </div>
            
            <div v-if="pendingRegistrations.length === 0" class="text-center py-12 glass-card">
              <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-2">verified</span>
              <p class="text-on-surface-variant">Semua pendaftaran telah diproses!</p>
            </div>
          </div>
        </div>
    </div>
  </AdminLayout>
</template>
