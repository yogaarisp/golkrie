<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminLayout from '../layouts/AdminLayout.vue';

const sponsors = ref([]);
const loading = ref(true);
const isModalOpen = ref(false);
const sponsorForm = ref({ id: null, name: '', logo_url: '', link_url: '', order: 0 });
const uploading = ref(false);

const handleImageUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('file', file);
  formData.append('bucket', 'logos');

  uploading.value = true;
  try {
    const response = await axios.post('/api/admin/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    sponsorForm.value.logo_url = response.data.url;
  } catch (e) {
    alert('Upload failed. Pastikan kamu sudah membuat Bucket bernama "logos" di Supabase Storage dan mengaturnya menjadi "Public".');
  } finally {
    uploading.value = false;
  }
};

const fetchSponsors = async () => {
  try {
    const response = await axios.get('/api/admin/sponsors');
    sponsors.value = response.data.sponsors;
  } catch (e) {
    console.error('Failed to fetch sponsors', e);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchSponsors);

const openModal = (sponsor = null) => {
  if (sponsor) {
    sponsorForm.value = { ...sponsor };
  } else {
    sponsorForm.value = { id: null, name: '', logo_url: '', link_url: '', order: 0 };
  }
  isModalOpen.value = true;
};

const saveSponsor = async () => {
  try {
    if (sponsorForm.value.id) {
      await axios.patch(`/api/admin/sponsors/${sponsorForm.value.id}`, sponsorForm.value);
    } else {
      await axios.post('/api/admin/sponsors', sponsorForm.value);
    }
    fetchSponsors();
    isModalOpen.value = false;
  } catch (e) {
    alert('Failed to save sponsor');
  }
};

const deleteSponsor = async (id) => {
  if (confirm('Hapus sponsor ini?')) {
    try {
      await axios.delete(`/api/admin/sponsors/${id}`);
      fetchSponsors();
    } catch (e) {
      alert('Failed to delete sponsor');
    }
  }
};
</script>

<template>
  <AdminLayout>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
      <div>
        <h1 class="text-3xl font-bold text-white mb-2">Sponsorships</h1>
        <p class="text-on-surface-variant text-sm">Kelola partner dan sponsor yang tampil di halaman depan.</p>
      </div>
      <button @click="openModal()" class="w-full md:w-auto bg-secondary text-on-secondary px-6 py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:scale-105 transition-all">
        <span class="material-symbols-outlined">add_business</span>
        Tambah Sponsor
      </button>
    </div>

    <div v-if="loading" class="spinner"></div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="sponsor in sponsors" :key="sponsor.id" class="glass-card p-6 flex items-center gap-6 group">
        <div class="w-20 h-20 bg-white/5 rounded-xl flex items-center justify-center overflow-hidden border border-white/10">
          <img :src="sponsor.logo_url" class="max-w-[80%] max-h-[80%] object-contain grayscale group-hover:grayscale-0 transition-all" />
        </div>
        <div class="flex-1">
          <h3 class="text-lg font-bold text-white">{{ sponsor.name }}</h3>
          <p class="text-xs text-on-surface-variant mb-4 truncate">{{ sponsor.link_url || 'No link' }}</p>
          <div class="flex gap-2">
            <button @click="openModal(sponsor)" class="p-2 bg-white/5 hover:bg-white/10 rounded-lg text-blue-400 transition-all">
              <span class="material-symbols-outlined text-sm">edit</span>
            </button>
            <button @click="deleteSponsor(sponsor.id)" class="p-2 bg-white/5 hover:bg-white/10 rounded-lg text-red-400 transition-all">
              <span class="material-symbols-outlined text-sm">delete</span>
            </button>
          </div>
        </div>
      </div>
      
      <div v-if="sponsors.length === 0" class="col-span-full py-20 text-center text-on-surface-variant glass-card">
        Belum ada sponsor terdaftar.
      </div>
    </div>

    <!-- Modal Form -->
    <div v-if="isModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-6">
      <div class="absolute inset-0 bg-background/80 backdrop-blur-sm" @click="isModalOpen = false"></div>
      <div class="glass-card w-full max-w-md relative z-10 p-8">
        <h2 class="text-2xl font-bold text-white mb-6">{{ sponsorForm.id ? 'Edit Sponsor' : 'Tambah Sponsor' }}</h2>
        
        <form @submit.prevent="saveSponsor" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-2 uppercase">Sponsor Name</label>
            <input v-model="sponsorForm.name" type="text" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" required />
          </div>
          <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-2 uppercase">Logo Sponsor</label>
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 bg-white/5 rounded-xl border border-white/10 flex items-center justify-center overflow-hidden">
                <img v-if="sponsorForm.logo_url" :src="sponsorForm.logo_url" class="max-w-[80%] max-h-[80%] object-contain" />
                <span v-else class="material-symbols-outlined opacity-30">image</span>
              </div>
              <div class="flex-1">
                <input type="file" @change="handleImageUpload" class="hidden" id="logo-upload" accept="image/*" />
                <label for="logo-upload" class="cursor-pointer bg-white/5 hover:bg-white/10 border border-white/10 px-4 py-2 rounded-lg text-xs font-bold transition-all inline-flex items-center gap-2">
                  <span v-if="uploading" class="spinner"></span>
                  <span v-else class="material-symbols-outlined text-sm">upload</span>
                  {{ uploading ? 'Uploading...' : 'Pilih File' }}
                </label>
                <p class="text-[10px] text-on-surface-variant mt-1 italic">*Format PNG/JPG, Maks 2MB</p>
              </div>
            </div>
            <input v-model="sponsorForm.logo_url" type="hidden" required />
          </div>
          <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-2 uppercase">Website / Social Link</label>
            <input v-model="sponsorForm.link_url" type="url" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" placeholder="https://..." />
          </div>
          <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <button type="button" @click="isModalOpen = false" class="order-2 sm:order-1 flex-1 px-6 py-3 rounded-xl font-bold text-on-surface-variant hover:bg-white/5 transition-all">Batal</button>
            <button type="submit" class="order-1 sm:order-2 flex-1 bg-secondary text-on-secondary px-6 py-3 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg shadow-secondary/20">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
