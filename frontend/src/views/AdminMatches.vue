<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import AdminLayout from '../layouts/AdminLayout.vue';

const matches = ref([]);
const loading = ref(true);
const isModalOpen = ref(false);
const editingMatch = ref(null);

const matchForm = ref({
  id: null,
  title: 'Futsal',
  match_name: '',
  date_time: '',
  end_time: '',
  location: '',
  location_url: '',
  quota: 14,
  quota_gk: 2,
  quota_df: 4,
  quota_mf: 4,
  quota_fw: 4,
  price: '0',
  status: 'upcoming'
});

// Auto-fill quotas based on match type
watch(() => matchForm.value.title, (newTitle) => {
  if (editingMatch.value) return; // Don't auto-fill when editing existing match
  
  if (newTitle === 'Big Pitch') {
    matchForm.value.quota_gk = 4;
    matchForm.value.quota_df = 16;
    matchForm.value.quota_mf = 12;
    matchForm.value.quota_fw = 12;
    matchForm.value.quota = 44;
  } else if (newTitle === 'Mini Soccer' || newTitle === 'Futsal') {
    matchForm.value.quota_gk = 2;
    matchForm.value.quota_df = 4;
    matchForm.value.quota_mf = 4;
    matchForm.value.quota_fw = 4;
    matchForm.value.quota = 14;
  }
});

// Auto-update total quota when position quotas change
watch([
  () => matchForm.value.quota_gk,
  () => matchForm.value.quota_df,
  () => matchForm.value.quota_mf,
  () => matchForm.value.quota_fw
], () => {
  matchForm.value.quota = 
    (matchForm.value.quota_gk || 0) + 
    (matchForm.value.quota_df || 0) + 
    (matchForm.value.quota_mf || 0) + 
    (matchForm.value.quota_fw || 0);
});

const fetchMatches = async () => {
  try {
    const response = await axios.get('/api/admin/matches');
    matches.value = response.data.matches;
  } catch (e) {
    console.error('Failed to fetch matches', e);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchMatches);

const openCreateModal = () => {
  editingMatch.value = null;
  matchForm.value = { 
    title: 'Futsal', 
    match_name: '', 
    date_time: '', 
    end_time: '', 
    location: '', 
    location_url: '', 
    quota: 14,
    quota_gk: 2,
    quota_df: 4,
    quota_mf: 4,
    quota_fw: 4,
    price: '0' 
  };
  isModalOpen.value = true;
};

const openEditModal = (match) => {
  editingMatch.value = match;
  matchForm.value = { 
    title: match.title, 
    match_name: match.match_name, 
    date_time: match.date_time ? match.date_time.slice(0, 16) : '', 
    end_time: match.end_time ? match.end_time.slice(0, 16) : '', 
    location: match.location, 
    location_url: match.location_url,
    quota: match.quota, 
    quota_gk: match.quota_gk || 2,
    quota_df: match.quota_df || 4,
    quota_mf: match.quota_mf || 4,
    quota_fw: match.quota_fw || 4,
    price: match.price 
  };
  isModalOpen.value = true;
};

const saveMatch = async () => {
  try {
    if (editingMatch.value) {
      await axios.patch(`/api/admin/matches/${editingMatch.value.id}`, matchForm.value);
    } else {
      await axios.post('/api/admin/matches', matchForm.value);
    }
    isModalOpen.value = false;
    fetchMatches();
  } catch (e) {
    alert('Gagal menyimpan match.');
  }
};

const deleteMatch = async (id) => {
  if (confirm('Yakin ingin menghapus match ini?')) {
    try {
      await axios.delete(`/api/admin/matches/${id}`);
      fetchMatches();
    } catch (e) {
      alert('Gagal menghapus match.');
    }
  }
};

const updateStatus = async (id, status) => {
    try {
        await axios.patch(`/api/admin/matches/${id}`, { status });
        fetchMatches();
    } catch (e) {
        alert('Gagal update status.');
    }
};
</script>

<template>
  <AdminLayout>
    <div class="flex justify-between items-center mb-10">
      <div>
        <h1 class="text-3xl font-bold text-white mb-2">Manage Matches</h1>
        <p class="text-on-surface-variant">Buat dan atur jadwal pertandingan Golkrie.</p>
      </div>
      <button @click="openCreateModal" class="bg-primary-container text-on-primary-container px-6 py-3 rounded-xl font-bold flex items-center gap-2 hover:scale-105 active:scale-95 transition-all">
        <span class="material-symbols-outlined">add</span>
        Tambah Match
      </button>
    </div>

    <div v-if="loading" class="spinner"></div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="match in matches" :key="match.id" class="glass-card p-6">
        <div class="flex justify-between items-start mb-4">
          <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border" 
            :class="match.status === 'upcoming' ? 'border-primary/30 text-primary bg-primary/5' : 'border-green-500/30 text-green-500 bg-green-500/5'">
            {{ match.status.toUpperCase() }}
          </span>
          <div class="flex gap-2">
            <button @click="openEditModal(match)" class="text-on-surface-variant hover:text-white"><span class="material-symbols-outlined">edit</span></button>
            <button @click="deleteMatch(match.id)" class="text-on-surface-variant hover:text-red-400"><span class="material-symbols-outlined">delete</span></button>
          </div>
        </div>
        
        <h4 class="text-xl font-bold text-white mb-1">{{ match.match_name }}</h4>
        <p class="text-sm text-on-surface-variant mb-4">{{ match.title }} • {{ match.location }}</p>
        
        <div class="flex items-center justify-between text-xs font-bold text-on-surface-variant mb-6">
          <div class="flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">calendar_month</span>
            {{ new Date(match.date_time).toLocaleDateString() }}
          </div>
          <div class="flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">group</span>
            {{ match.registrations_count }} / {{ match.quota }}
          </div>
        </div>

        <div class="flex gap-2">
            <button v-if="match.status === 'upcoming'" 
              @click="updateStatus(match.id, 'finished')"
              class="flex-1 bg-green-500/10 text-green-500 py-2 rounded-lg font-bold text-xs hover:bg-green-500/20"
            >
              Tandai Selesai
            </button>
            <button v-else 
              @click="updateStatus(match.id, 'upcoming')"
              class="flex-1 bg-primary-container/10 text-primary py-2 rounded-lg font-bold text-xs hover:bg-primary-container/20"
            >
              Set Upcoming
            </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal-overlay" :class="{ active: isModalOpen }" @click.self="isModalOpen = false">
      <div class="modal-content">
        <h3 class="text-2xl font-bold text-white mb-6">{{ editingMatch ? 'Edit Match' : 'Tambah Match Baru' }}</h3>
        
        <form @submit.prevent="saveMatch" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-[10px] font-bold uppercase text-on-surface-variant mb-1">Tipe</label>
              <select v-model="matchForm.title" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-2 focus:outline-none">
                <option value="Futsal">Futsal</option>
                <option value="Mini Soccer">Mini Soccer</option>
                <option value="Big Pitch">Big Pitch</option>
              </select>
            </div>
            <div>
              <label class="block text-[10px] font-bold uppercase text-on-surface-variant mb-1">Total Quota (Auto)</label>
              <input v-model="matchForm.quota" type="number" readonly class="w-full bg-surface-container/50 border border-outline-variant rounded-xl px-4 py-2 focus:outline-none opacity-70" />
            </div>
          </div>

          <!-- Position Quotas -->
          <div class="bg-surface-container/30 p-4 rounded-xl border border-outline-variant/30">
            <label class="block text-[10px] font-bold uppercase text-primary mb-3">Detail Kuota Per Posisi</label>
            <div class="grid grid-cols-4 gap-2">
              <div>
                <label class="block text-[8px] font-bold text-on-surface-variant mb-1">GK</label>
                <input v-model.number="matchForm.quota_gk" type="number" class="w-full bg-background border border-outline-variant rounded-lg px-2 py-1.5 text-xs focus:outline-none" />
              </div>
              <div>
                <label class="block text-[8px] font-bold text-on-surface-variant mb-1">DF</label>
                <input v-model.number="matchForm.quota_df" type="number" class="w-full bg-background border border-outline-variant rounded-lg px-2 py-1.5 text-xs focus:outline-none" />
              </div>
              <div>
                <label class="block text-[8px] font-bold text-on-surface-variant mb-1">MF</label>
                <input v-model.number="matchForm.quota_mf" type="number" class="w-full bg-background border border-outline-variant rounded-lg px-2 py-1.5 text-xs focus:outline-none" />
              </div>
              <div>
                <label class="block text-[8px] font-bold text-on-surface-variant mb-1">FW</label>
                <input v-model.number="matchForm.quota_fw" type="number" class="w-full bg-background border border-outline-variant rounded-lg px-2 py-1.5 text-xs focus:outline-none" />
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-[10px] font-bold uppercase text-on-surface-variant mb-1">Nama Match</label>
            <input v-model="matchForm.match_name" type="text" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-2 focus:outline-none" />
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-on-surface-variant mb-2 uppercase">Waktu Mulai</label>
              <input v-model="matchForm.date_time" type="datetime-local" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" required />
            </div>
            <div>
              <label class="block text-xs font-bold text-on-surface-variant mb-2 uppercase">Waktu Selesai</label>
              <input v-model="matchForm.end_time" type="datetime-local" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-on-surface-variant mb-2 uppercase">Stadium Name</label>
              <input v-model="matchForm.location" type="text" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" placeholder="e.g. Jatidiri Stadium" required />
            </div>
            <div>
              <label class="block text-xs font-bold text-on-surface-variant mb-2 uppercase">Google Maps URL</label>
              <input v-model="matchForm.location_url" type="url" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" placeholder="https://maps.app.goo.gl/..." />
            </div>
          </div>

          <div>
            <label class="block text-[10px] font-bold uppercase text-on-surface-variant mb-1">Harga</label>
            <input v-model="matchForm.price" type="text" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-2 focus:outline-none" />
          </div>

          <div class="pt-4">
            <button type="submit" class="w-full bg-primary-container text-on-primary-container font-bold py-3 rounded-xl hover:scale-105 active:scale-95 transition-all">
              {{ editingMatch ? 'Update Match' : 'Simpan Match' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
