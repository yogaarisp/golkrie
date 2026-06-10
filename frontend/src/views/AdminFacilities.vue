<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminLayout from '../layouts/AdminLayout.vue';

const facilities = ref([]);
const loading = ref(true);
const saving = ref(false);
const newFacility = ref('');
const editingIdx = ref(null);
const editingValue = ref('');

const fetchFacilities = async () => {
  try {
    const res = await axios.get('/api/admin/settings');
    const raw = res.data.settings?.default_facilities;
    facilities.value = raw ? JSON.parse(raw) : [];
  } catch (e) {
    console.error('Failed to fetch facilities', e);
    facilities.value = [];
  } finally {
    loading.value = false;
  }
};

const saveFacilities = async () => {
  saving.value = true;
  try {
    await axios.post('/api/admin/settings', {
      default_facilities: JSON.stringify(facilities.value)
    });
  } catch (e) {
    alert('Gagal menyimpan.');
  } finally {
    saving.value = false;
  }
};

const addFacility = async () => {
  const val = newFacility.value.trim();
  if (!val) return;
  facilities.value.push(val);
  newFacility.value = '';
  await saveFacilities();
};

const removeFacility = async (idx) => {
  facilities.value.splice(idx, 1);
  await saveFacilities();
};

const startEdit = (idx) => {
  editingIdx.value = idx;
  editingValue.value = facilities.value[idx];
};

const saveEdit = async (idx) => {
  if (editingValue.value.trim()) {
    facilities.value[idx] = editingValue.value.trim();
    await saveFacilities();
  }
  editingIdx.value = null;
};

const moveUp = async (idx) => {
  if (idx === 0) return;
  [facilities.value[idx - 1], facilities.value[idx]] = [facilities.value[idx], facilities.value[idx - 1]];
  await saveFacilities();
};

const moveDown = async (idx) => {
  if (idx === facilities.value.length - 1) return;
  [facilities.value[idx + 1], facilities.value[idx]] = [facilities.value[idx], facilities.value[idx + 1]];
  await saveFacilities();
};

onMounted(fetchFacilities);
</script>

<template>
  <AdminLayout>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
      <div>
        <h1 class="text-3xl font-bold text-white mb-2">Fasilitas Default</h1>
        <p class="text-on-surface-variant text-sm">Kelola daftar fasilitas yang muncul sebagai pilihan saat membuat match.</p>
      </div>
      <div class="text-xs text-on-surface-variant/50 font-bold uppercase tracking-widest">
        {{ facilities.length }} fasilitas tersedia
      </div>
    </div>

    <div v-if="loading" class="spinner"></div>

    <div v-else class="max-w-2xl space-y-4">
      <!-- Add New -->
      <div class="glass-card p-6">
        <label class="block text-xs font-black uppercase text-primary tracking-widest mb-3">Tambah Fasilitas Baru</label>
        <div class="flex gap-3">
          <input
            v-model="newFacility"
            @keyup.enter="addFacility"
            type="text"
            placeholder="e.g. Wasit lengkap"
            class="flex-1 bg-surface-container border border-outline-variant rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary transition-all placeholder:text-white/20"
          />
          <button
            @click="addFacility"
            :disabled="!newFacility.trim()"
            class="bg-primary text-black font-black px-5 py-3 rounded-xl hover:scale-105 active:scale-95 transition-all disabled:opacity-30 flex items-center gap-2"
          >
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah
          </button>
        </div>
      </div>

      <!-- List -->
      <div class="glass-card overflow-hidden">
        <div class="p-4 border-b border-white/5 bg-white/3">
          <span class="text-xs font-black uppercase text-on-surface-variant tracking-widest">Daftar Fasilitas</span>
        </div>

        <div v-if="facilities.length === 0" class="p-12 text-center">
          <span class="material-symbols-outlined text-4xl text-white/10 mb-3 block">construction</span>
          <p class="text-white/20 text-sm font-bold uppercase tracking-widest">Belum ada fasilitas</p>
        </div>

        <ul class="divide-y divide-white/5">
          <li v-for="(f, idx) in facilities" :key="idx"
            class="flex items-center gap-3 px-5 py-4 hover:bg-white/3 transition-all group"
          >
            <!-- Nomor -->
            <span class="text-[10px] font-black text-primary/60 w-5 text-center">{{ idx + 1 }}</span>

            <!-- Edit mode -->
            <div v-if="editingIdx === idx" class="flex-1 flex gap-2">
              <input
                v-model="editingValue"
                @keyup.enter="saveEdit(idx)"
                @keyup.escape="editingIdx = null"
                autofocus
                class="flex-1 bg-surface-container border border-primary/50 rounded-lg px-3 py-1.5 text-sm text-white focus:outline-none"
              />
              <button @click="saveEdit(idx)" class="text-primary hover:text-white transition-all">
                <span class="material-symbols-outlined text-sm">check</span>
              </button>
              <button @click="editingIdx = null" class="text-on-surface-variant hover:text-white transition-all">
                <span class="material-symbols-outlined text-sm">close</span>
              </button>
            </div>

            <!-- View mode -->
            <span v-else class="flex-1 text-sm text-white">{{ f }}</span>

            <!-- Actions -->
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all">
              <button @click="moveUp(idx)" :disabled="idx === 0" class="p-1 text-on-surface-variant hover:text-white disabled:opacity-20 transition-all">
                <span class="material-symbols-outlined text-sm">arrow_upward</span>
              </button>
              <button @click="moveDown(idx)" :disabled="idx === facilities.length - 1" class="p-1 text-on-surface-variant hover:text-white disabled:opacity-20 transition-all">
                <span class="material-symbols-outlined text-sm">arrow_downward</span>
              </button>
              <button @click="startEdit(idx)" class="p-1 text-on-surface-variant hover:text-primary transition-all">
                <span class="material-symbols-outlined text-sm">edit</span>
              </button>
              <button @click="removeFacility(idx)" class="p-1 text-on-surface-variant hover:text-red-400 transition-all">
                <span class="material-symbols-outlined text-sm">delete</span>
              </button>
            </div>
          </li>
        </ul>
      </div>

      <p class="text-xs text-white/20 text-center pb-4">
        Perubahan tersimpan otomatis. Fasilitas ini akan muncul sebagai pilihan checkbox saat membuat/edit match.
      </p>
    </div>
  </AdminLayout>
</template>
