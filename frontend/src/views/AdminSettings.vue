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
  hero_description: '',
  about_description: '',
  app_logo: '',
  app_favicon: '',
  bank_account: ''
});

const loading = ref(true);
const saving = ref(false);
const logoLoading = ref(false);
const faviconLoading = ref(false);

const handleFileUpload = async (event, type) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('file', file);
  formData.append('bucket', 'logos');
  
  if (type === 'logo') logoLoading.value = true;
  else faviconLoading.value = true;

  try {
    const response = await axios.post('/api/admin/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    settings.value[type === 'logo' ? 'app_logo' : 'app_favicon'] = response.data.url;
  } catch (e) {
    alert('Gagal mengunggah gambar.');
  } finally {
    if (type === 'logo') logoLoading.value = false;
    else faviconLoading.value = false;
  }
};

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
        <div class="glass-card p-8 space-y-8">
          <h3 class="text-xl font-bold text-primary flex items-center gap-2">
            <span class="material-symbols-outlined">branding_watermark</span>
            Identitas Komunitas
          </h3>
          
          <!-- Logo & Favicon Upload -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
              <label class="block text-xs font-bold uppercase text-on-surface-variant">Official Logo</label>
              <div class="flex items-center gap-6 bg-background/50 p-6 rounded-2xl border border-dashed border-outline-variant/50">
                <div class="w-20 h-20 bg-surface-container rounded-xl flex items-center justify-center overflow-hidden border border-white/5 shadow-inner">
                  <img v-if="settings.app_logo" :src="settings.app_logo" class="max-w-full max-h-full object-contain" />
                  <span v-else class="material-symbols-outlined text-white/10 text-4xl">image</span>
                </div>
                <div class="flex-1">
                  <input type="file" @change="handleFileUpload($event, 'logo')" accept="image/*" class="hidden" id="logo-upload" />
                  <label for="logo-upload" class="inline-flex items-center gap-2 bg-primary/10 text-primary px-4 py-2 rounded-lg font-bold text-sm cursor-pointer hover:bg-primary/20 transition-all">
                    <span v-if="logoLoading" class="spinner !w-4 !h-4"></span>
                    <span v-else class="material-symbols-outlined text-sm">upload</span>
                    {{ settings.app_logo ? 'Ganti Logo' : 'Upload Logo' }}
                  </label>
                  <p class="text-[10px] text-on-surface-variant mt-2 italic">Format: PNG, JPG (Transparent recommended)</p>
                </div>
              </div>
            </div>

            <div class="space-y-4">
              <label class="block text-xs font-bold uppercase text-on-surface-variant">Site Favicon</label>
              <div class="flex items-center gap-6 bg-background/50 p-6 rounded-2xl border border-dashed border-outline-variant/50">
                <div class="w-12 h-12 bg-surface-container rounded-lg flex items-center justify-center overflow-hidden border border-white/5">
                  <img v-if="settings.app_favicon" :src="settings.app_favicon" class="w-full h-full object-contain" />
                  <span v-else class="material-symbols-outlined text-white/10">language</span>
                </div>
                <div class="flex-1">
                  <input type="file" @change="handleFileUpload($event, 'favicon')" accept="image/x-icon,image/png" class="hidden" id="favicon-upload" />
                  <label for="favicon-upload" class="inline-flex items-center gap-2 bg-secondary/10 text-secondary px-4 py-2 rounded-lg font-bold text-sm cursor-pointer hover:bg-secondary/20 transition-all">
                    <span v-if="faviconLoading" class="spinner !w-4 !h-4"></span>
                    <span v-else class="material-symbols-outlined text-sm">upload</span>
                    {{ settings.app_favicon ? 'Ganti Icon' : 'Upload Icon' }}
                  </label>
                  <p class="text-[10px] text-on-surface-variant mt-2 italic">Format: ICO, PNG (32x32px)</p>
                </div>
              </div>
            </div>
          </div>

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
            <textarea v-model="settings.hero_description" rows="2" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all"></textarea>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase text-on-surface-variant mb-2">About Us Description (Bagian Tentang Kami)</label>
            <textarea v-model="settings.about_description" rows="4" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all"></textarea>
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
            <div class="md:col-span-2">
              <label class="block text-xs font-bold uppercase text-on-surface-variant mb-2">Nomor Rekening / Metode Pembayaran</label>
              <input v-model="settings.bank_account" type="text" placeholder="BCA 123456789 a/n Golkrie" class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all font-mono" />
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-6">
          <button type="submit" :disabled="saving" class="w-full md:w-auto bg-primary-container text-on-primary-container px-10 py-4 rounded-2xl font-bold flex items-center justify-center gap-3 hover:scale-105 active:scale-95 transition-all disabled:opacity-50 shadow-xl shadow-primary/20">
            <span v-if="saving" class="spinner"></span>
            <span v-else class="material-symbols-outlined">save</span>
            {{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </div>

      </form>
    </div>
  </AdminLayout>
</template>
