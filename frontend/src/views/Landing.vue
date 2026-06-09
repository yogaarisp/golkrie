<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import PublicLayout from '../layouts/PublicLayout.vue';

const upcomingMatches = ref([]);
const matchHistory = ref([]);
const squadList = ref([]);
const activeMatchId = ref(null);
const activeSquadMatch = ref(null);
const loading = ref(false);
const matchesLoading = ref(true);
const sponsorsLoading = ref(true);
const squadLoading = ref(false);
const settings = ref({
  app_name: 'Golkrie',
  app_tagline: 'Golek Kringet, Jalin Seduluran.',
  instagram_url: 'https://instagram.com/golkrie',
  whatsapp_contact: '08123456789',
  hero_description: 'Tingkatkan skill dan jalin persaudaraan di lapangan hijau.',
  about_description: '',
  about_quote: '',
  about_est: 'EST 2024',
  about_hashtag: '#GolekKringet'
});
const sponsors = ref([]);
const copiedId = ref(null);
const showPayment = ref(false);

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
      
      // Use initial squad data from first response instead of making another call
      if (upcomingMatches.value && upcomingMatches.value.length > 0) {
        activeSquadMatch.value = upcomingMatches.value[0];
        // Hanya tampilkan squad kalau jadwal belum lewat
        const firstMatch = upcomingMatches.value[0];
        if (new Date(firstMatch.date_time) >= new Date()) {
          squadList.value = response.data.initialSquad || [];
        } else {
          squadList.value = [];
        }
      }
    }
  } catch (e) {
    console.error('Failed to fetch landing data:', e.response || e);
  } finally {
    matchesLoading.value = false;
    sponsorsLoading.value = false;
    loading.value = false;
  }
};

const copyToClipboard = (text, id) => {
  // Extract only numbers if it looks like a bank account line
  const accountNumber = text.replace(/[^0-9]/g, '');
  const finalContent = accountNumber.length >= 5 ? accountNumber : text;

  navigator.clipboard.writeText(finalContent).then(() => {
    copiedId.value = id;
    setTimeout(() => {
      if (copiedId.value === id) copiedId.value = null;
    }, 2000);
  });
};

const getWhatsAppLink = (phone, message = '') => {
  if (!phone) return '#';
  let cleanPhone = phone.replace(/[^0-9]/g, '');
  if (cleanPhone.startsWith('0')) {
    cleanPhone = '62' + cleanPhone.slice(1);
  }
  return `https://wa.me/${cleanPhone}${message ? '?text=' + encodeURIComponent(message) : ''}`;
};

const selectMatchForSquad = async (match) => {
  if (!match) return;
  activeSquadMatch.value = match;

  // Jika jadwal sudah lewat, kosongkan squad
  if (new Date(match.date_time) < new Date()) {
    squadList.value = [];
    return;
  }

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
    const match = upcomingMatches.value.find(m => m.id === registrationForm.value.match_id);
    
    alert(response.data.message);
    closeJoinModal();
    fetchLandingData();

    // Redirect to WhatsApp for confirmation
    const waMessage = `Halo Admin Golkrie,\n\nSaya ingin konfirmasi pendaftaran:\nMatch: ${match?.match_name} (${match?.title})\nNama: ${registrationForm.value.full_name}\nPosisi: ${registrationForm.value.position}\n\nSaya akan segera mengirimkan bukti bayar. Terima kasih!`;
    window.open(getWhatsAppLink(settings.value.whatsapp_contact, waMessage), '_blank');
    
  } catch (e) {
    alert(e.response?.data?.message || 'Pendaftaran gagal.');
  }
};

const formatPrice = (price) => {
  if (!price) return '0';
  return new Intl.NumberFormat('id-ID').format(price);
}

const formatDate = (dateString) => {
  if (!dateString) return 'TBA';
  const options = { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' };
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
    <div>
        <!-- SECTION 1: HOME (Hero) -->
        <section id="home" class="relative h-[600px] md:h-[800px] flex items-start justify-center pt-24 md:pt-40 overflow-hidden">
          <div class="absolute inset-0 z-0">
            <img 
              alt="Football pitch" 
              class="w-full h-full object-cover" 
              src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1200&q=75&fm=webp" 
              srcset="https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=600&q=75&fm=webp 600w, https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1200&q=75&fm=webp 1200w"
              sizes="(max-width: 768px) 100vw, 1200px"
              fetchpriority="high"
              decoding="async"
            />
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
          <div class="px-6 max-w-7xl mx-auto mb-10 text-center">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tighter uppercase">Upcoming <span class="text-primary">Matches</span></h2>
            <div class="w-16 h-1.5 bg-primary mx-auto rounded-full mb-6"></div>
            <p class="text-on-surface-variant text-base">Pilih jadwal pertandingan dan amankan slot kamu sekarang.</p>
          </div>

          <div v-if="matchesLoading" class="px-6 max-w-7xl mx-auto flex flex-wrap justify-center gap-6 md:gap-8">
            <div v-for="i in 3" :key="'skeleton-'+i" 
              class="bento-card p-6 shadow-2xl w-full md:w-[340px] bg-surface-container/20 backdrop-blur-sm relative overflow-hidden animate-pulse"
            >
              <div class="flex justify-between items-start mb-5">
                <div class="h-6 w-20 bg-white/10 rounded-full"></div>
                <div class="h-6 w-16 bg-white/10 rounded"></div>
              </div>
              <div class="h-8 w-48 bg-white/10 rounded-xl mb-6"></div>
              <div class="space-y-4 mb-8 bg-surface-container/30 p-5 rounded-2xl border border-white/5">
                <div class="h-4 w-3/4 bg-white/10 rounded"></div>
                <div class="h-4 w-1/2 bg-white/10 rounded"></div>
                <div class="h-4 w-2/3 bg-white/10 rounded"></div>
              </div>
              <div class="h-12 w-full bg-white/10 rounded-xl"></div>
            </div>
          </div>

          <div v-else-if="!upcomingMatches || upcomingMatches.length === 0" class="w-full text-center py-20 px-6">
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

              <div class="flex justify-between items-start mb-5" :class="{'mt-6': activeSquadMatch?.id === match.id}">
                <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-[10px] font-black uppercase border border-primary/20 tracking-widest">{{ match.title }}</span>
                <div class="text-right">
                  <span class="block text-on-surface-variant text-[9px] font-bold uppercase tracking-tighter">Status Slot</span>
                  <span class="text-base font-black" :class="match.registrations_count >= match.quota ? 'text-secondary' : 'text-white'">
                    {{ match.registrations_count >= match.quota ? 'FULL' : `${match.registrations_count}/${match.quota}` }}
                  </span>
                </div>
              </div>

              <div class="mb-5">
                <a v-if="match.location_url" :href="match.location_url" target="_blank" class="block group/title">
                  <h3 class="text-xl font-black text-white uppercase tracking-tighter group-hover/title:text-primary transition-colors flex items-center gap-2">
                    {{ match.match_name }}
                    <span class="material-symbols-outlined text-sm opacity-0 group-hover/title:opacity-100 transition-all -translate-x-2 group-hover/title:translate-x-0">near_me</span>
                  </h3>
                </a>
                <h3 v-else class="text-xl font-black text-white uppercase tracking-tighter">{{ match.match_name }}</h3>
              </div>
              
              <div class="space-y-4 mb-8 bg-surface-container/30 p-5 rounded-2xl border border-white/5">
                <div class="flex items-center gap-4">
                  <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0 border border-primary/20">
                    <span class="material-symbols-outlined text-primary text-lg">calendar_month</span>
                  </div>
                  <span class="text-sm font-black text-white tracking-tight">{{ formatDate(match.date_time) }}</span>
                </div>
                <div class="flex items-center gap-4">
                  <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0 border border-primary/20">
                    <span class="material-symbols-outlined text-primary text-lg">schedule</span>
                  </div>
                  <span class="text-sm font-black text-white tracking-tight">
                    {{ formatTime(match.date_time) }} 
                    <span v-if="match.end_time" class="opacity-40 mx-1">-</span>
                    <span v-if="match.end_time">{{ formatTime(match.end_time) }}</span>
                  </span>
                </div>
                <div class="flex items-center gap-4">
                  <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0 border border-primary/20">
                    <span class="material-symbols-outlined text-primary text-lg">location_on</span>
                  </div>
                  <span class="text-sm font-black text-white tracking-tight truncate">{{ match.location }}</span>
                </div>
                
                <div class="h-[1px] w-full bg-white/5 my-2"></div>

                <div class="flex items-start gap-4">
                  <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0 border border-primary/20">
                    <span class="material-symbols-outlined text-primary text-lg">payments</span>
                  </div>
                  <div class="flex-1 pt-0.5">
                    <div class="flex justify-between items-center mb-1.5">
                      <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">Kiper (GK)</span>
                      <span class="text-sm font-black text-white">Rp {{ formatPrice(match.price_gk) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">Pemain (Player)</span>
                      <span class="text-sm font-black text-white">Rp {{ formatPrice(match.price) }}</span>
                    </div>
                  </div>
                </div>

                <div v-if="settings.bank_account" class="pt-4 border-t border-white/5">
                  <div @click="showPayment = !showPayment" 
                    class="w-full flex items-center justify-between cursor-pointer group"
                  >
                    <div class="flex items-center gap-4">
                      <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0 border border-primary/20 group-hover:bg-primary/20 transition-colors">
                        <span class="material-symbols-outlined text-primary text-lg">payments</span>
                      </div>
                      <span class="text-[10px] font-black uppercase tracking-widest text-primary group-hover:text-primary-light transition-colors">Informasi Pembayaran</span>
                    </div>
                    <span class="material-symbols-outlined text-primary transition-transform duration-300" :class="showPayment ? 'rotate-180' : ''">
                      expand_more
                    </span>
                  </div>

                      <Transition name="slide-fade">
                        <div v-if="showPayment" class="mt-3 space-y-2">
                          <div v-for="(line, idx) in settings.bank_account.split(/[\n|]/).filter(l => l.trim())" :key="idx" 
                            class="group relative flex items-center justify-between p-3 bg-surface-container/50 rounded-2xl border border-white/5 hover:bg-primary/10 hover:border-primary/20 transition-all cursor-pointer overflow-hidden"
                            @click.stop="copyToClipboard(line.trim(), idx)"
                          >
                            <div class="flex items-center gap-3">
                              <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center shrink-0 border border-primary/20 transition-colors group-hover:bg-primary/20">
                                <span class="material-symbols-outlined text-primary text-base">account_balance</span>
                              </div>
                              <div class="flex flex-col">
                                <span class="text-[11px] font-black text-white tracking-tight leading-none mb-1">
                                  {{ line.split(' a.n ')[0] }}
                                </span>
                                <span v-if="line.includes(' a.n ')" class="text-[8px] font-bold text-on-surface-variant/50 uppercase tracking-tighter">
                                  a.n {{ line.split(' a.n ')[1] }}
                                </span>
                              </div>
                            </div>

                            <div class="shrink-0 ml-4">
                              <span class="material-symbols-outlined text-sm text-primary transition-all" :class="copiedId === idx ? 'scale-0 opacity-0' : 'opacity-20 group-hover:opacity-100'">content_copy</span>
                            </div>
                            
                            <!-- Animated Success Overlay -->
                            <div class="absolute inset-0 bg-primary flex items-center justify-center transition-all duration-300" 
                              :class="copiedId === idx ? 'translate-y-0 opacity-100' : 'translate-y-full opacity-0'">
                              <div class="flex items-center gap-2 text-black font-black text-[10px] uppercase tracking-tighter">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                Copied!
                              </div>
                            </div>
                          </div>
                        </div>
                      </Transition>
                    </div>
                  </div>
              
                  <div class="w-full bg-white/5 h-2 rounded-full mb-6 overflow-hidden border border-white/5">
                <div class="bg-primary h-full rounded-full progress-glow" :style="{width: `${(match.registrations_count / match.quota) * 100}%`}"></div>
              </div>

              <button @click.stop="openJoinModal(match.id)" 
                :disabled="match.registrations_count >= match.quota || new Date(match.date_time) < new Date()"
                class="w-full py-4 rounded-xl font-black uppercase tracking-widest transition-all text-center text-sm"
                :class="new Date(match.date_time) < new Date() ? 'bg-surface-container-high text-on-surface-variant/40 cursor-not-allowed' : match.registrations_count >= match.quota ? 'bg-surface-container-high text-on-surface-variant cursor-not-allowed' : 'bg-primary text-white hover:bg-primary-dark shadow-xl shadow-primary/30 active:scale-95'"
              >
                {{ new Date(match.date_time) < new Date() ? 'Jadwal Selesai' : match.registrations_count >= match.quota ? 'Waitlist' : 'Amankan Slot' }}
              </button>
            </div>
          </div>
        </section>

        <!-- SQUAD COMPOSITION (Dynamic Section) - hanya tampil kalau jadwal belum lewat -->
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

            <!-- Jadwal sudah lewat - tampilkan placeholder coming soon -->
            <div v-else-if="new Date(activeSquadMatch.date_time) < new Date()" class="flex flex-col items-center justify-center py-20 text-center">
              <span class="material-symbols-outlined text-6xl text-primary/20 mb-4">calendar_clock</span>
              <p class="text-on-surface-variant/40 font-black uppercase tracking-[0.3em] text-sm mb-2">Next Event</p>
              <p class="text-on-surface-variant/25 text-xs uppercase tracking-widest">Squad akan diumumkan segera</p>
            </div>

            <div v-else class="px-6 max-w-7xl mx-auto">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="pos in (activeSquadMatch.title === 'Big Pitch' ? [
                  {key: 'GK', label: 'GK', quota: 4, icon: 'sports_handball'},
                  {key: 'CB', label: 'CB', quota: 8, icon: 'shield'},
                  {key: 'RLB', label: 'RLB', quota: 8, icon: 'shield_person'},
                  {key: 'MF', label: 'MF', quota: 12, icon: 'settings_input_component'},
                  {key: 'RLWF', label: 'RLWF', quota: 8, icon: 'bolt'},
                  {key: 'CF', label: 'CF', quota: 4, icon: 'target'}
                ] : [
                  {key: 'GK', label: 'Kiper', quota: activeSquadMatch.quota_gk, icon: 'sports_handball'},
                  {key: 'DF', label: 'Player', quota: activeSquadMatch.quota_df, icon: 'group'}
                ])" :key="pos.key" class="bento-card p-6 flex flex-col h-[400px] bg-surface-container/20 backdrop-blur-sm border border-white/5">
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
                      <span v-if="player.is_paid" class="text-[8px] font-black uppercase tracking-tighter text-green-400 border border-green-400/20 bg-green-400/10 px-1.5 py-0.5 rounded shadow-[0_0_10px_rgba(74,222,128,0.2)]">PAID</span>
                      <span v-else class="text-[8px] font-black uppercase tracking-tighter text-on-surface-variant/40 border border-white/10 px-1.5 py-0.5 rounded">UNPAID</span>
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
            <div class="grid gap-3" :class="upcomingMatches.find(m => m.id === registrationForm.match_id)?.title === 'Big Pitch' ? 'grid-cols-3' : 'grid-cols-2'">
              <button v-for="pos in (upcomingMatches.find(m => m.id === registrationForm.match_id)?.title === 'Big Pitch' ? [
                {key: 'GK', label: 'GK', quota: 4},
                {key: 'CB', label: 'CB', quota: 8},
                {key: 'RLB', label: 'RLB', quota: 8},
                {key: 'MF', label: 'MF', quota: 12},
                {key: 'RLWF', label: 'RLWF', quota: 8},
                {key: 'CF', label: 'CF', quota: 4}
              ] : [
                {key: 'GK', label: 'Kiper', quota: upcomingMatches.find(m => m.id === registrationForm.match_id)?.quota_gk || 2},
                {key: 'DF', label: 'Player', quota: upcomingMatches.find(m => m.id === registrationForm.match_id)?.quota_df || 4}
              ])" :key="pos.key" type="button" 
                @click="squadList.filter(p => p.position === pos.key).length < pos.quota ? registrationForm.position = pos.key : null"
                :disabled="squadList.filter(p => p.position === pos.key).length >= pos.quota"
                class="py-4 rounded-2xl font-black border transition-all relative"
                :class="[
                  registrationForm.position === pos.key ? 'bg-primary border-primary text-white shadow-lg shadow-primary/30' : 'bg-surface-container/50 border-outline-variant/30 text-on-surface-variant hover:border-white/20',
                  squadList.filter(p => p.position === pos.key).length >= pos.quota ? 'opacity-40 cursor-not-allowed grayscale' : ''
                ]"
              >
                <span v-if="squadList.filter(p => p.position === pos.key).length >= pos.quota" class="absolute -top-2 left-1/2 -translate-x-1/2 bg-secondary text-white text-[7px] px-1.5 py-0.5 rounded-full font-black">FULL</span>
                {{ pos.label }}
              </button>
            </div>
            <p v-if="registrationForm.position" class="mt-3 text-[10px] text-on-surface-variant italic uppercase tracking-wider">
              Slot tersisa: {{ 
                ({
                  GK: 4,
                  CB: 8,
                  RLB: 8,
                  MF: 12,
                  RLWF: 8,
                  CF: 4,
                  DF: upcomingMatches.find(m => m.id === registrationForm.match_id)?.quota_df || 4
                }[registrationForm.position] || 0) - 
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

              <a v-if="settings.whatsapp_contact" :href="getWhatsAppLink(settings.whatsapp_contact)" target="_blank" class="flex items-center gap-3 bg-surface-container px-6 py-4 rounded-2xl border border-outline-variant/40 hover:border-green-500/50 transition-all group">
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
                <div class="w-20 h-20 bg-primary/10 rounded-3xl flex items-center justify-center overflow-hidden border border-primary/20 p-2 shadow-inner">
                  <img v-if="settings.app_logo" :src="settings.app_logo" class="w-full h-full object-contain" />
                  <span v-else class="text-white text-4xl font-black">G</span>
                </div>
                <div>
                  <h3 class="text-2xl font-black text-white uppercase tracking-tighter">{{ settings.app_name || 'GOLKRIE' }}</h3>
                  <p class="text-primary font-bold text-sm uppercase tracking-widest">{{ settings.app_tagline || 'Community' }}</p>
                </div>
              </div>
              <p class="text-on-surface-variant italic leading-relaxed mb-6">
                "{{ settings.about_quote || 'Bukan sekadar mengejar bola, tapi mengejar keringat dan mempererat tali silaturahmi antar pecinta sepakbola di Semarang.' }}"
              </p>
              <div class="h-[1px] w-full bg-outline-variant/30 mb-6"></div>
              <div class="flex items-center justify-between text-xs font-bold text-on-surface-variant">
                <span>{{ settings.about_est || 'EST 2024' }}</span>
                <span class="text-primary">{{ settings.about_hashtag || '#GolekKringet' }}</span>
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

.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}
.slide-fade-leave-active {
  transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}
</style>
