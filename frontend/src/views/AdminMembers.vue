<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminLayout from '../layouts/AdminLayout.vue';

const members = ref([]);
const loading = ref(true);
const isModalOpen = ref(false);
const memberForm = ref({ id: null, full_name: '', phone_number: '' });

const fetchMembers = async () => {
  try {
    const response = await axios.get('/api/admin/members');
    members.value = response.data.members;
  } catch (e) {
    console.error('Failed to fetch members', e);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchMembers);

const openModal = (member = null) => {
  if (member) {
    memberForm.value = { ...member };
  } else {
    memberForm.value = { id: null, full_name: '', phone_number: '' };
  }
  isModalOpen.value = true;
};

const saveMember = async () => {
  try {
    if (memberForm.value.id) {
      await axios.patch(`/api/admin/members/${memberForm.value.id}`, memberForm.value);
    } else {
      await axios.post('/api/admin/members', memberForm.value);
    }
    fetchMembers();
    isModalOpen.value = false;
  } catch (e) {
    alert('Failed to save member');
  }
};

const deleteMember = async (id) => {
  if (confirm('Hapus member ini?')) {
    try {
      await axios.delete(`/api/admin/members/${id}`);
      fetchMembers();
    } catch (e) {
      alert('Failed to delete member');
    }
  }
};
</script>

<template>
  <AdminLayout>
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-bold text-white mb-2">Member Management</h1>
        <p class="text-on-surface-variant">Kelola daftar semua pemain Golkrie.</p>
      </div>
      <button @click="openModal()" class="bg-primary-container text-on-primary-container px-6 py-3 rounded-xl font-bold flex items-center gap-2 hover:scale-105 transition-all">
        <span class="material-symbols-outlined">person_add</span>
        Tambah Member
      </button>
    </div>

    <div v-if="loading" class="spinner"></div>

    <div v-else class="glass-card overflow-hidden">
      <table class="w-full text-left">
        <thead>
          <tr class="bg-surface-container-high border-b border-outline-variant/30">
            <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Name</th>
            <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Phone / WhatsApp</th>
            <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/20">
          <tr v-for="member in members" :key="member.id" class="hover:bg-white/5 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">
                  {{ member.full_name.charAt(0) }}
                </div>
                <span class="text-white font-semibold">{{ member.full_name }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-on-surface-variant font-mono">{{ member.phone_number || '-' }}</td>
            <td class="px-6 py-4 text-right">
              <div class="flex justify-end gap-2">
                <button @click="openModal(member)" class="p-2 hover:bg-blue-500/10 text-blue-400 rounded-lg transition-colors">
                  <span class="material-symbols-outlined">edit</span>
                </button>
                <button @click="deleteMember(member.id)" class="p-2 hover:bg-red-500/10 text-red-400 rounded-lg transition-colors">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="members.length === 0">
            <td colspan="3" class="px-6 py-12 text-center text-on-surface-variant">Belum ada member.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form -->
    <div v-if="isModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-6">
      <div class="absolute inset-0 bg-background/80 backdrop-blur-sm" @click="isModalOpen = false"></div>
      <div class="glass-card w-full max-w-md relative z-10 p-8">
        <h2 class="text-2xl font-bold text-white mb-6">{{ memberForm.id ? 'Edit Member' : 'Tambah Member' }}</h2>
        
        <form @submit.prevent="saveMember" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-2 uppercase">Full Name</label>
            <input v-model="memberForm.full_name" type="text" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" required />
          </div>
          <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-2 uppercase">Phone Number</label>
            <input v-model="memberForm.phone_number" type="text" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" />
          </div>
          <div class="flex gap-4 pt-4">
            <button type="button" @click="isModalOpen = false" class="flex-1 px-6 py-3 rounded-xl font-bold text-on-surface-variant hover:bg-white/5 transition-all">Batal</button>
            <button type="submit" class="flex-1 bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-dark transition-all">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
