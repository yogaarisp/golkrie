<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminLayout from '../layouts/AdminLayout.vue';

const settings = ref({
  app_name: '',
  app_tagline: '',
  footer_text: '',
  instagram_url: '',
  whatsapp_contact: '',
  hero_description: ''
});

const loading = ref(true);
const saving = ref(false);

const fetchSettings = async () => {
  try {
    const response = await axios.get('/api/admin/settings');
    settings.value = { ...settings.value, ...response.data.settings };
  } catch (e) {
    console.error('Failed to fetch settings', e);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchSettings);

const saveSettings = async () => {
  saving.value = true;
  try {
    await axios.post('/api/admin/settings', settings.value);
    alert('Settings updated successfully!');
  } catch (e) {
    alert('Failed to update settings.');
  } finally {
    saving.value = false;
  }
};
</script>

<template>
  <AdminLayout>
    <div class="mb-10">
      <h1 class="text-3xl font-bold text-white mb-2">Site Settings</h1>
      <p class="text-on-surface-variant">Atur profil komunitas dan tampilan halaman publik.</p>
    </div>

    <div v-if="loading" class="spinner"></div>

    <div v-else class="max-w-4xl">
      <form @submit.prevent="saveSettings" class="space-y-8">
        
        <!-- App Identity -->
        <div class="glass-card p-8 space-y-6">
          <h3 class="text-xl font-bold text-primary flex items-center gap-2">
            <span class="material-symbols-outlined">branding_watermark</span>
            Identitas Komunitas
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-xs font-bold uppercase text-on-surface-variant mb-2">Nama Aplikasi / Komunitas</label>
              <input v-model="settings.app_name" type="text" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase text-on-surface-variant mb-2">Tagline</label>
              <input v-model="settings.app_tagline" type="text" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase text-on-surface-variant mb-2">Hero Description (Halaman Depan)</label>
            <textarea v-model="settings.hero_description" rows="3" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all"></textarea>
          </div>
        </div>

        <!-- Footer & Social -->
        <div class="glass-card p-8 space-y-6">
          <h3 class="text-xl font-bold text-secondary flex items-center gap-2">
            <span class="material-symbols-outlined">public</span>
            Footer & Sosial Media
          </h3>

          <div>
            <label class="block text-xs font-bold uppercase text-on-surface-variant mb-2">Teks Copyright Footer</label>
            <input v-model="settings.footer_text" type="text" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-xs font-bold uppercase text-on-surface-variant mb-2">Instagram URL</label>
              <input v-model="settings.instagram_url" type="url" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase text-on-surface-variant mb-2">WhatsApp Contact</label>
              <input v-model="settings.whatsapp_contact" type="text" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all" />
            </div>
          </div>
        </div>

        <div class="flex justify-end">
          <button type="submit" :disabled="saving" class="bg-primary-container text-on-primary-container px-10 py-4 rounded-2xl font-bold flex items-center gap-3 hover:scale-105 active:scale-95 transition-all disabled:opacity-50 shadow-xl shadow-primary/20">
            <span v-if="saving" class="spinner"></span>
            <span v-else class="material-symbols-outlined">save</span>
            {{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </div>

      </form>
    </div>
  </AdminLayout>
</template>
