<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import PublicLayout from '../layouts/PublicLayout.vue';

const upcomingMatches = ref([]);
const matchHistory = ref([]);
const squadList = ref([]);
const activeMatchId = ref(null);
const activeSquadMatch = ref(null);
const loading = ref(true);
const squadLoading = ref(false);
const settings = ref({
  app_name: 'Golkrie',
  app_tagline: 'Golek Kringet, Jalin Seduluran.',
  instagram_url: 'https://instagram.com/golkrie',
  whatsapp_contact: '08123456789',
  hero_description: 'Tingkatkan skill dan jalin persaudaraan di lapangan hijau.',
  about_description: ''
});
const sponsors = ref([]);

const isModalOpen = ref(false);
const selectedMatchId = ref(null);
const memberCheckLoading = ref(false);
const isExistingMember = ref(false);
const memberData = ref(null);

const registrationForm = ref({
  match_id: '',
  full_name: '',
  phone_number: '',
  position: ''
});

const fetchLandingData = async () => {
  try {
    const response = await axios.get('/api/landing');
    console.log('API Response:', response.data);
    
    if (response && response.data) {
      if (response.data.settings) {
        settings.value = { ...settings.value, ...response.data.settings };
      }
      upcomingMatches.value = response.data.upcomingMatches || response.data.matches || [];
      sponsors.value = response.data.sponsors || [];
      
      // Set first match as active squad by default
      if (upcomingMatches.value && upcomingMatches.value.length > 0) {
        selectMatchForSquad(upcomingMatches.value[0]);
      }
    }
  } catch (e) {
    console.error('Failed to fetch landing data:', e.response || e);
    // Ensure we still turn off loading even on total failure
  } finally {
    setTimeout(() => {
      loading.value = false;
    }, 500); // Small delay to ensure render
  }
};

const selectMatchForSquad = async (match) => {
  if (!match) return;
  activeSquadMatch.value = match;
  squadLoading.value = true;
  try {
    const response = await axios.get(`/api/landing?match_id=${match.id}`);
    squadList.value = response.data?.initialSquad || [];
  } catch (e) {
    console.error('Failed to fetch squad', e);
    squadList.value = [];
  } finally {
    squadLoading.value = false;
  }
};

onMounted(fetchLandingData);

const openJoinModal = (matchId) => {
  selectedMatchId.value = matchId;
  registrationForm.value.match_id = matchId;
  isModalOpen.value = true;
};

const closeJoinModal = () => {
  isModalOpen.value = false;
  registrationForm.value = { match_id: '', full_name: '', phone_number: '', position: '' };
  isExistingMember.value = false;
  memberData.value = null;
};

let checkTimeout = null;
const handleNameInput = () => {
  clearTimeout(checkTimeout);
  const name = registrationForm.value.full_name.trim();
  if (name.length < 3) {
    isExistingMember.value = false;
    memberData.value = null;
    return;
  }

  memberCheckLoading.value = true;
  checkTimeout = setTimeout(async () => {
    try {
      const response = await axios.post('/api/check-member', { name });
      isExistingMember.value = response.data.exists;
      memberData.value = response.data.member;
      if (response.data.exists) registrationForm.value.phone_number = response.data.member.phone_number;
    } catch (e) {
      console.error('Member check failed', e);
    } finally {
      memberCheckLoading.value = false;
    }
  }, 600);
};

const submitRegistration = async () => {
  try {
    const response = await axios.post('/api/register', registrationForm.value);
    alert(response.data.message);
    closeJoinModal();
    fetchLandingData();
  } catch (e) {
    alert(e.response?.data?.message || 'Pendaftaran gagal.');
  }
};

const formatDate = (dateString) => {
  if (!dateString) return 'TBA';
  const options = { weekday: 'long', day: 'numeric', month: 'short' };
  try {
    return new Date(dateString).toLocaleDateString('id-ID', options);
  } catch (e) {
    return 'TBA';
  }
};

const formatTime = (dateString) => {
  if (!dateString) return '--:--';
  try {
    return new Date(dateString).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
  } catch (e) {
    return '--:--';
  }
};
</script>

<template>
  <PublicLayout :settings="settings">
    <div v-if="loading" class="flex items-center justify-center h-screen">
        <div class="spinner !w-12 !h-12"></div>
    </div>
    
    <div v-else>
        <!-- SECTION 1: HOME (Hero) -->
        <section id="home" class="relative h-[600px] md:h-[800px] flex items-center justify-center overflow-hidden">
          <div class="absolute inset-0 z-0">
            <img alt="Football pitch" class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80&w=2000" />
            <div class="absolute inset-0 bg-gradient-to-b from-background/95 via-background/70 to-background"></div>
          </div>
          <div class="relative z-10 text-center px-6 max-w-5xl mx-auto">
            <h1 class="text-5xl md:text-7xl font-black mb-8 text-white leading-[0.9] tracking-tighter drop-shadow-2xl">
              {{ settings.app_name || 'GOLKRIE' }},<br />
              <span class="text-primary drop-shadow-[0_0_40px_rgba(239,68,68,0.4)]">{{ settings.app_tagline || 'Jalin Seduluran.' }}</span>
            </h1>
            <p class="text-lg md:text-xl text-white/80 mb-10 max-w-2xl mx-auto leading-relaxed font-medium px-4">
              {{ settings.hero_description || 'Tingkatkan skill dan jalin persaudaraan di lapangan hijau dua kali seminggu.' }}
            </p>
            <div class="flex flex-wrap justify-center gap-4">
              <a href="#schedule" class="bg-primary text-white px-8 py-3.5 rounded-xl font-bold text-base hover:scale-105 active:scale-95 transition-all shadow-xl shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-xl">calendar_today</span>
                Lihat Jadwal
              </a>
            </div>
          </div>
          <!-- Animated Scroll Indicator -->
          <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce opacity-30">
            <span class="material-symbols-outlined text-4xl text-white">expand_more</span>
          </div>
        </section>

        <!-- SECTION 2: SCHEDULE -->
        <section id="schedule" class="py-24 bg-background relative z-10">
          <div class="px-6 max-w-7xl mx-auto mb-16 text-center">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tighter uppercase">Upcoming <span class="text-primary">Matches</span></h2>
            <div class="w-16 h-1.5 bg-primary mx-auto rounded-full mb-6"></div>
            <p class="text-on-surface-variant text-base">Pilih jadwal pertandingan dan amankan slot kamu sekarang.</p>
          </div>

          <div v-if="!upcomingMatches || upcomingMatches.length === 0" class="w-full text-center py-20 px-6">
            <p class="text-on-surface-variant font-bold italic tracking-widest uppercase opacity-50">Belum ada jadwal pertandingan.</p>
          </div>
          <div v-else class="px-6 max-w-7xl mx-auto flex flex-wrap justify-center gap-6 md:gap-8">
            <div v-for="(match, index) in upcomingMatches" :key="match.id" 
              @click="selectMatchForSquad(match)"
              class="bento-card p-6 group transition-all duration-500 hover:border-primary/50 shadow-2xl w-full md:w-[340px] bg-surface-container/20 backdrop-blur-sm cursor-pointer relative overflow-hidden"
              :class="activeSquadMatch?.id === match.id ? 'ring-2 ring-primary border-primary/40 scale-[1.02] active-card-glow' : 'hover:scale-[1.01]'"
            >
              <!-- Active Indicator (Inside Card) -->
              <div v-if="activeSquadMatch?.id === match.id" class="absolute top-0 left-0 right-0 bg-primary/20 border-b border-primary/30 py-1 text-center">
                <span class="text-primary text-[8px] font-black uppercase tracking-[0.2em]">Viewing Squad</span>
              </div>

              <div class="flex justify-between items-start mb-6" :class="{'mt-6': activeSquadMatch?.id === match.id}">
                <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-[10px] font-black uppercase border border-primary/20 tracking-widest">{{ match.title }}</span>
                <div class="text-right">
                  <span class="block text-on-surface-variant text-[9px] font-bold uppercase tracking-tighter">Status Slot</span>
                  <span class="text-base font-black" :class="match.registrations_count >= match.quota ? 'text-secondary' : 'text-white'">
                    {{ match.registrations_count >= match.quota ? 'FULL' : `${match.registrations_count}/${match.quota}` }}
                  </span>
                </div>
              </div>

              <h3 class="text-xl font-black mb-5 text-white uppercase italic tracking-tighter">{{ match.match_name }}</h3>
              
              <div class="space-y-3 mb-6 bg-white/5 p-4 rounded-xl border border-white/5">
                <div class="flex items-center gap-3 text-on-surface-variant">
                  <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">calendar_month</span>
                  </div>
                  <span class="text-xs font-bold text-white">{{ formatDate(match.date_time) }}</span>
                </div>
                <div class="flex items-center gap-3 text-on-surface-variant">
                  <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">schedule</span>
                  </div>
                  <span class="text-xs font-bold text-white">
                    {{ formatTime(match.date_time) }} 
                    <span v-if="match.end_time"> - {{ formatTime(match.end_time) }}</span>
                  </span>
                </div>
                <div class="flex items-center gap-3 text-on-surface-variant">
                  <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">location_on</span>
                  </div>
                  <span class="text-xs font-bold text-white truncate">{{ match.location }}</span>
                </div>
                <div class="flex items-center gap-3 text-on-surface-variant">
                  <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">payments</span>
                  </div>
                  <div class="flex flex-col">
                    <span class="text-xs font-bold text-white">Rp {{ match.price_gk }} <span class="text-[9px] font-normal opacity-70">(GK)</span></span>
                    <span class="text-xs font-bold text-white">Rp {{ match.price }} <span class="text-[9px] font-normal opacity-70">(Player Lain)</span></span>
                    <span v-if="settings.bank_account" class="text-[9px] font-bold text-primary mt-1 px-2 py-0.5 bg-primary/10 rounded-md border border-primary/20 w-fit">
                      Transfer: {{ settings.bank_account }}
                    </span>
                  </div>
                </div>
              </div>
              
              <div class="w-full bg-white/5 h-2 rounded-full mb-6 overflow-hidden border border-white/5">
                <div class="bg-primary h-full rounded-full progress-glow" :style="{width: `${(match.registrations_count / match.quota) * 100}%`}"></div>
              </div>

              <button @click.stop="openJoinModal(match.id)" 
                :disabled="match.registrations_count >= match.quota"
                class="w-full py-4 rounded-xl font-black uppercase tracking-widest transition-all text-center text-sm"
                :class="match.registrations_count >= match.quota ? 'bg-surface-container-high text-on-surface-variant cursor-not-allowed' : 'bg-primary text-white hover:bg-primary-dark shadow-xl shadow-primary/30 active:scale-95'"
              >
                {{ match.registrations_count >= match.quota ? 'Waitlist' : 'Amankan Slot' }}
              </button>
            </div>
          </div>
        </section>

        <!-- SQUAD COMPOSITION (Dynamic Section) -->
        <transition name="fade">
          <section v-if="activeSquadMatch" class="py-24 bg-surface-container/5 border-y border-white/5">
            <div class="px-6 max-w-7xl mx-auto mb-16 text-center">
              <span class="text-primary font-black uppercase tracking-[0.3em] text-xs mb-2 block">Line-up Details</span>
              <h2 class="text-3xl md:text-5xl font-black text-white mb-4 tracking-tighter uppercase italic">Squad <span class="text-primary">Composition</span></h2>
              <p class="text-on-surface-variant">Daftar pemain untuk pertandingan <span class="text-white font-bold">{{ activeSquadMatch.match_name }}</span></p>
            </div>

            <div v-if="squadLoading" class="flex justify-center py-20">
              <div class="spinner !w-10 !h-10"></div>
            </div>

            <div v-else class="px-6 max-w-7xl mx-auto">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="pos in [
                  {key: 'GK', label: 'Goalkeeper', quota: activeSquadMatch.quota_gk, icon: 'sports_handball'},
                  {key: 'DF', label: 'Defender', quota: activeSquadMatch.quota_df, icon: 'shield'},
                  {key: 'MF', label: 'Midfielder', quota: activeSquadMatch.quota_mf, icon: 'settings_input_component'},
                  {key: 'FW', label: 'Forward', quota: activeSquadMatch.quota_fw, icon: 'bolt'}
                ]" :key="pos.key" class="bento-card p-6 flex flex-col h-[400px] bg-surface-container/20 backdrop-blur-sm border border-white/5">
                  <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-2 text-primary">
                      <span class="material-symbols-outlined text-lg">{{ pos.icon }}</span>
                      <span class="font-black uppercase tracking-widest text-[10px]">{{ pos.label }}</span>
                    </div>
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20">
                      {{ squadList.filter(p => p.position === pos.key).length }}/{{ pos.quota }}
                    </span>
                  </div>

                  <div class="space-y-2 overflow-y-auto custom-scrollbar flex-1 pr-1">
                    <div v-for="player in squadList.filter(p => p.position === pos.key)" :key="player.id" 
                      class="flex justify-between items-center bg-white/5 border border-white/5 px-4 py-3 rounded-xl mb-2 hover:border-primary/30 transition-all group"
                    >
                      <span class="text-sm font-bold text-white group-hover:text-primary transition-colors">{{ player.player_name }}</span>
                      <span class="text-[8px] font-black uppercase tracking-tighter text-primary/60 border border-primary/20 px-1.5 py-0.5 rounded">PAID</span>
                    </div>

                    <!-- Open Slots -->
                    <div v-for="i in Math.max(0, pos.quota - squadList.filter(p => p.position === pos.key).length)" :key="'open-'+i"
                      class="flex items-center gap-3 bg-white/[0.02] border border-dashed border-white/10 px-4 py-3 rounded-xl mb-2 opacity-40"
                    >
                      <div class="w-2 h-2 rounded-full border border-white/30"></div>
                      <span class="text-[9px] font-black uppercase tracking-widest text-on-surface-variant">Open Slot</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </transition>

        <!-- SECTION 3: SPONSORS -->
        <section id="sponsors" class="py-32 bg-surface-container/10 border-y border-white/5 overflow-hidden">
          <div class="px-6 max-w-7xl mx-auto mb-16 text-center">
            <h2 class="text-3xl md:text-5xl font-black text-white mb-4 tracking-tighter uppercase">Our Official <span class="text-primary">Partners</span></h2>
            <p class="text-on-surface-variant max-w-2xl mx-auto">Didukung oleh brand-brand yang peduli dengan perkembangan sepakbola komunitas GOLKRIE.</p>
          </div>

          <div class="relative flex overflow-x-hidden">
            <div class="py-12 animate-marquee whitespace-nowrap flex items-center gap-16">
              <div v-for="n in 2" :key="'sp-group-'+n" class="flex items-center gap-16">
                <div v-for="sponsor in sponsors" :key="sponsor.id" class="flex items-center">
                  <img :src="sponsor.logo_url" :alt="sponsor.name" class="h-16 md:h-20 opacity-40 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-500 cursor-pointer" />
                </div>
                <div v-if="sponsors.length === 0" class="text-on-surface-variant font-black opacity-20 text-3xl md:text-5xl tracking-widest italic uppercase">
                  GOLKRIE PARTNERSHIP • JOIN US NOW • BECOME A SPONSOR
                </div>
              </div>
            </div>
          </div>
        </section>
    </div>

    <!-- Join Modal -->
    <div class="modal-overlay" :class="{ active: isModalOpen }" @click.self="closeJoinModal">
      <div class="modal-content !max-w-lg">
        <div class="flex justify-between items-center mb-8">
          <h3 class="text-3xl font-black text-white uppercase tracking-tighter italic">Join <span class="text-primary">Match</span></h3>
          <button @click="closeJoinModal" class="text-on-surface-variant hover:text-white transition-colors">
            <span class="material-symbols-outlined text-3xl">close</span>
          </button>
        </div>

        <form @submit.prevent="submitRegistration" class="space-y-6">
          <div>
            <label class="block text-xs font-black uppercase text-on-surface-variant mb-3 tracking-widest">Nama Lengkap</label>
            <input v-model="registrationForm.full_name" @input="handleNameInput" type="text" placeholder="Input nama sesuai KTP..." class="w-full bg-surface-container/50 border border-outline-variant/30 rounded-2xl px-5 py-4 focus:border-primary focus:outline-none text-white font-bold transition-all" required />
            <div v-if="memberCheckLoading" class="mt-3 flex items-center gap-2">
              <div class="spinner !w-4 !h-4"></div>
              <span class="text-xs text-on-surface-variant font-bold">Verifikasi data...</span>
            </div>
          </div>

          <transition enter-active-class="transition duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
            <div v-if="!isExistingMember && registrationForm.full_name.length >= 3">
              <label class="block text-xs font-black uppercase text-on-surface-variant mb-3 tracking-widest">Nomor WhatsApp</label>
              <input v-model="registrationForm.phone_number" type="tel" placeholder="Contoh: 081234..." class="w-full bg-surface-container/50 border border-outline-variant/30 rounded-2xl px-5 py-4 focus:border-primary focus:outline-none text-white font-bold transition-all" :required="!isExistingMember" />
            </div>
          </transition>

          <div>
            <label class="block text-xs font-black uppercase text-on-surface-variant mb-3 tracking-widest">Posisi Lapangan</label>
            <div class="grid grid-cols-4 gap-3">
              <button v-for="pos in [
                {key: 'GK', quota: upcomingMatches.find(m => m.id === registrationForm.match_id)?.quota_gk || 2},
                {key: 'DF', quota: upcomingMatches.find(m => m.id === registrationForm.match_id)?.quota_df || 4},
                {key: 'MF', quota: upcomingMatches.find(m => m.id === registrationForm.match_id)?.quota_mf || 4},
                {key: 'FW', quota: upcomingMatches.find(m => m.id === registrationForm.match_id)?.quota_fw || 4}
              ]" :key="pos.key" type="button" 
                @click="squadList.filter(p => p.position === pos.key).length < pos.quota ? registrationForm.position = pos.key : null"
                :disabled="squadList.filter(p => p.position === pos.key).length >= pos.quota"
                class="py-4 rounded-2xl font-black border transition-all relative"
                :class="[
                  registrationForm.position === pos.key ? 'bg-primary border-primary text-white shadow-lg shadow-primary/30' : 'bg-surface-container/50 border-outline-variant/30 text-on-surface-variant hover:border-white/20',
                  squadList.filter(p => p.position === pos.key).length >= pos.quota ? 'opacity-40 cursor-not-allowed grayscale' : ''
                ]"
              >
                <span v-if="squadList.filter(p => p.position === pos.key).length >= pos.quota" class="absolute -top-2 left-1/2 -translate-x-1/2 bg-secondary text-white text-[7px] px-1.5 py-0.5 rounded-full font-black">FULL</span>
                {{ pos.key }}
              </button>
            </div>
            <p v-if="registrationForm.position" class="mt-3 text-[10px] text-on-surface-variant italic uppercase tracking-wider">
              Slot tersisa: {{ 
                ({
                  GK: upcomingMatches.find(m => m.id === registrationForm.match_id)?.quota_gk || 2,
                  DF: upcomingMatches.find(m => m.id === registrationForm.match_id)?.quota_df || 4,
                  MF: upcomingMatches.find(m => m.id === registrationForm.match_id)?.quota_mf || 4,
                  FW: upcomingMatches.find(m => m.id === registrationForm.match_id)?.quota_fw || 4
                }[registrationForm.position]) - 
                squadList.filter(p => p.position === registrationForm.position).length 
              }}
            </p>
          </div>

          <button type="submit" class="w-full bg-primary text-white font-black py-5 rounded-2xl uppercase tracking-[0.2em] shadow-2xl shadow-primary/40 hover:scale-[1.02] active:scale-95 transition-all text-lg mt-4">
            Amankan Slot
          </button>
        </form>
      </div>
    </div>
    <!-- About Section -->
    <section id="about" class="py-24 px-6 bg-surface-container-low border-t border-outline-variant/30">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
          <div>
            <div class="flex items-center gap-4 mb-6">
              <div class="h-1 w-12 bg-primary rounded-full"></div>
              <span class="text-primary font-bold uppercase tracking-[0.2em] text-xs">About Golkrie</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-black text-white mb-8 leading-tight tracking-tighter">
              Golek Kringet, <br />
              <span class="text-primary">Jalin Seduluran.</span>
            </h2>
            <p class="text-on-surface-variant text-lg leading-relaxed mb-8">
              {{ settings.about_description || 'Golkrie adalah komunitas sepakbola yang berfokus pada kesehatan dan tali persaudaraan. Kami percaya bahwa olahraga adalah cara terbaik untuk menjaga tubuh tetap bugar sekaligus menambah relasi baru.' }}
            </p>
            
            <div class="flex flex-wrap gap-4">
              <a v-if="settings.instagram_url" :href="settings.instagram_url" target="_blank" class="flex items-center gap-3 bg-surface-container px-6 py-4 rounded-2xl border border-outline-variant/40 hover:border-primary/50 transition-all group">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                  <span class="material-symbols-outlined">photo_camera</span>
                </div>
                <div>
                  <div class="text-[10px] uppercase font-bold text-on-surface-variant">Instagram</div>
                  <div class="text-white font-bold">Follow Us</div>
                </div>
              </a>

              <a v-if="settings.whatsapp_contact" :href="'https://wa.me/' + settings.whatsapp_contact.replace(/[^0-9]/g, '')" target="_blank" class="flex items-center gap-3 bg-surface-container px-6 py-4 rounded-2xl border border-outline-variant/40 hover:border-green-500/50 transition-all group">
                <div class="w-10 h-10 rounded-full bg-green-500/10 flex items-center justify-center text-green-500 group-hover:scale-110 transition-transform">
                  <span class="material-symbols-outlined">chat</span>
                </div>
                <div>
                  <div class="text-[10px] uppercase font-bold text-on-surface-variant">WhatsApp</div>
                  <div class="text-white font-bold">Chat Admin</div>
                </div>
              </a>
            </div>
          </div>

          <div class="relative">
            <div class="absolute -inset-4 bg-primary/20 blur-3xl rounded-full"></div>
            <div class="relative glass-card p-10 overflow-hidden group">
              <div class="flex items-center gap-6 mb-8">
                <div class="w-20 h-20 bg-primary rounded-3xl flex items-center justify-center text-white text-4xl font-black">G</div>
                <div>
                  <h3 class="text-2xl font-black text-white uppercase tracking-tighter">{{ settings.app_name || 'GOLKRIE' }}</h3>
                  <p class="text-primary font-bold text-sm uppercase tracking-widest">{{ settings.app_tagline || 'Community' }}</p>
                </div>
              </div>
              <p class="text-on-surface-variant italic leading-relaxed mb-6">
                "Bukan sekadar mengejar bola, tapi mengejar keringat dan mempererat tali silaturahmi antar pecinta sepakbola di Semarang."
              </p>
              <div class="h-[1px] w-full bg-outline-variant/30 mb-6"></div>
              <div class="flex items-center justify-between text-xs font-bold text-on-surface-variant">
                <span>EST 2024</span>
                <span class="text-primary">#GolekKringet</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<style scoped>
.animate-marquee {
  display: flex;
  animation: marquee 30s linear infinite;
}

@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.animate-marquee:hover {
  animation-play-state: paused;
}

.progress-glow {
  box-shadow: 0 0 15px var(--primary);
}

.active-card-glow {
  box-shadow: 0 0 30px rgba(239, 68, 68, 0.2);
}

/* Smooth Scrolling */
html {
  scroll-behavior: smooth;
}
</style>
