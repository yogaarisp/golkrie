<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import draggable from 'vuedraggable';
import AdminLayout from '../layouts/AdminLayout.vue';

const route = useRoute();
const router = useRouter();
const matchId = route.params.id;

const match = ref(null);
const allPlayers = ref([]);
const loading = ref(true);
const saving = ref(false);
const teamCount = ref(2);

// Teams structure: { name: string, players: array, background: string }
const teams = ref([]);
const playerPool = ref([]);

// Add player panel
const showAddPlayer = ref(false);
const addPlayerForm = ref({ full_name: '', phone_number: '', position: 'GK' });
const addPlayerLoading = ref(false);
const memberSearch = ref('');
const allMembers = ref([]);
const filteredMembers = ref([]);
const showMemberDropdown = ref(false);

const fetchTeams = async () => {
  try {
    const response = await axios.get(`/api/admin/matches/${matchId}/teams`);
    match.value = response.data.match;
    allPlayers.value = response.data.registrations;
    
    // Config from match metadata
    const config = match.value.team_config || {};
    
    // Determine number of teams from data or default
    const uniqueTeams = [...new Set(allPlayers.value.map(p => p.team_name).filter(Boolean))];
    const initialTeamCount = Math.max(uniqueTeams.length, 2);
    
    setupTeams(initialTeamCount, config);
  } catch (e) {
    console.error('Failed to fetch teams', e);
  } finally {
    loading.value = false;
  }
};

const setupTeams = (count, config = {}) => {
  const newTeams = [];
  for (let i = 0; i < count; i++) {
    const defaultName = "Team " + chr(65 + i);
    const teamName = Object.keys(config)[i] || defaultName;
    const teamData = config[teamName] || {};
    
    newTeams.push({
      name: teamName,
      players: allPlayers.value.filter(p => p.team_name === teamName),
      background: teamData.background || ''
    });
  }
  
  // Players not in any of these teams go to the pool
  const assignedNames = newTeams.map(t => t.name);
  playerPool.value = allPlayers.value.filter(p => !p.team_name || !assignedNames.includes(p.team_name));
  
  teams.value = newTeams;
};

const chr = (code) => String.fromCharCode(code);

const shufflePlayers = async () => {
  if (!confirm(`Shuffle players into ${teamCount.value} teams?`)) return;
  
  loading.value = true;
  try {
    const response = await axios.post(`/api/admin/matches/${matchId}/shuffle`, {
      team_count: teamCount.value
    });
    allPlayers.value = response.data.registrations;
    playerPool.value = []; // After shuffle, usually everyone is assigned
    setupTeams(teamCount.value, match.value.team_config || {});
  } catch (e) {
    alert('Failed to shuffle players.');
  } finally {
    loading.value = false;
  }
};

const saveTeams = async () => {
  saving.value = true;
  try {
    // 1. Save Player Assignments
    const assignments = [];
    
    // Assigned players
    teams.value.forEach(team => {
      team.players.forEach(player => {
        assignments.push({
          id: player.id,
          team_name: team.name
        });
      });
    });

    // Unassigned players (Pool)
    playerPool.value.forEach(player => {
      assignments.push({
        id: player.id,
        team_name: null
      });
    });
    
    await axios.post(`/api/admin/matches/${matchId}/teams`, { assignments });

    // 2. Save Team Config
    const config = {};
    teams.value.forEach(team => {
      config[team.name] = {
        background: team.background
      };
    });
    
    await axios.post(`/api/admin/matches/${matchId}/teams/config`, { team_config: config });
    
    alert('Teams and configuration saved successfully!');
  } catch (e) {
    alert('Failed to save teams.');
  } finally {
    saving.value = false;
  }
};

const triggerFileInput = (index) => {
  document.getElementById(`team-bg-${index}`).click();
};

const handleFileUpload = async (event, index) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('file', file);
  formData.append('bucket', 'matches');

  try {
    const response = await axios.post('/api/admin/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    teams.value[index].background = response.data.url;
  } catch (e) {
    alert('Upload failed. Please try again.');
  }
};

const addTeam = () => {
  if (teams.value.length >= 6) return;
  teams.value.push({
    name: "Team " + chr(65 + teams.value.length),
    players: [],
    background: ''
  });
};

const removeTeam = (index) => {
  if (teams.value.length <= 2) return;
  const playersToMove = teams.value[index].players;
  playerPool.value = [...playerPool.value, ...playersToMove];
  teams.value.splice(index, 1);
};

// Fetch all members untuk autocomplete
const fetchMembers = async () => {
  try {
    const res = await axios.get('/api/admin/members');
    allMembers.value = res.data.members || [];
  } catch (e) {}
};

const onMemberSearchInput = () => {
  const q = memberSearch.value.toLowerCase();
  if (q.length < 1) {
    filteredMembers.value = [];
    showMemberDropdown.value = false;
    return;
  }
  filteredMembers.value = allMembers.value
    .filter(m => m.full_name.toLowerCase().includes(q))
    .slice(0, 8);
  showMemberDropdown.value = filteredMembers.value.length > 0;
};

const selectMember = (member) => {
  addPlayerForm.value.full_name    = member.full_name;
  addPlayerForm.value.phone_number = member.phone_number;
  memberSearch.value               = member.full_name;
  showMemberDropdown.value         = false;
};

const submitAddPlayer = async () => {
  if (!addPlayerForm.value.full_name) return;
  addPlayerLoading.value = true;
  try {
    const res = await axios.post(`/api/admin/matches/${matchId}/registrations`, addPlayerForm.value);
    // Tambahkan ke pool langsung tanpa reload
    const newPlayer = res.data.registration;
    if (newPlayer) {
      allPlayers.value.push(newPlayer);
      playerPool.value.push(newPlayer);
    }
    // Reset form
    addPlayerForm.value = { full_name: '', phone_number: '', position: 'GK' };
    memberSearch.value  = '';
    showAddPlayer.value = false;
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menambahkan pemain.');
  } finally {
    addPlayerLoading.value = false;
  }
};

const removePlayerFromMatch = async (player) => {
  if (!confirm(`Hapus ${player.player_name} dari match ini?`)) return;
  try {
    await axios.delete(`/api/admin/matches/${matchId}/registrations/${player.id}`);
    // Hapus dari semua list
    allPlayers.value  = allPlayers.value.filter(p => p.id !== player.id);
    playerPool.value  = playerPool.value.filter(p => p.id !== player.id);
    teams.value.forEach(t => {
      t.players = t.players.filter(p => p.id !== player.id);
    });
  } catch (e) {
    alert('Gagal menghapus pemain.');
  }
};

onMounted(() => {
  fetchTeams();
  fetchMembers();
});
</script>

<template>
  <AdminLayout>
    <div v-if="loading" class="flex items-center justify-center h-64">
      <div class="spinner"></div>
    </div>

    <div v-else>
      <!-- Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
        <div class="flex items-center gap-4">
          <button @click="router.back()" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-white/10 transition-all">
            <span class="material-symbols-outlined text-white">arrow_back</span>
          </button>
          <div>
            <h1 class="text-3xl font-bold text-white mb-1">Manage Teams</h1>
            <p class="text-on-surface-variant text-sm">{{ match?.match_name }} • {{ match?.title }}</p>
          </div>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
          <select v-model="teamCount" class="bg-surface-container border border-outline-variant rounded-xl px-4 py-2 text-white focus:outline-none focus:border-primary text-sm">
            <option :value="2">2 Teams</option>
            <option :value="3">3 Teams</option>
            <option :value="4">4 Teams</option>
          </select>
          <button @click="shufflePlayers" class="bg-white/5 text-white px-4 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 hover:bg-white/10 transition-all">
            <span class="material-symbols-outlined text-sm">shuffle</span>
            Shuffle
          </button>
          <button @click="saveTeams" :disabled="saving" class="bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 hover:scale-105 active:scale-95 transition-all shadow-lg shadow-primary/20">
            <span v-if="saving" class="spinner-sm"></span>
            <span v-else class="material-symbols-outlined text-sm">save</span>
            Save All Changes
          </button>
        </div>
      </div>

      <div class="flex flex-col lg:flex-row gap-8 items-start">
        <!-- Player Pool (Available Players) -->
        <div class="w-full lg:w-80 shrink-0 sticky top-24">
          <div class="glass-card flex flex-col h-full max-h-[calc(100vh-200px)]">
            <div class="p-4 border-b border-white/5 bg-primary/10 flex justify-between items-center">
              <span class="text-primary font-black uppercase tracking-widest text-xs">Available Players</span>
              <div class="flex items-center gap-2">
                <span class="bg-primary text-black text-[10px] px-2 py-0.5 rounded-full font-bold">{{ playerPool.length }}</span>
                <button @click="showAddPlayer = !showAddPlayer" class="bg-primary/20 text-primary hover:bg-primary/30 rounded-lg px-2 py-1 text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-1">
                  <span class="material-symbols-outlined text-xs">person_add</span>
                  Tambah
                </button>
              </div>
            </div>

            <!-- Form Tambah Pemain -->
            <transition name="slide-down">
              <div v-if="showAddPlayer" class="p-3 border-b border-white/5 bg-white/3 space-y-2">
                <!-- Autocomplete Member -->
                <div class="relative">
                  <input
                    v-model="memberSearch"
                    @input="onMemberSearchInput"
                    @blur="setTimeout(() => showMemberDropdown = false, 200)"
                    type="text"
                    placeholder="Cari nama member..."
                    class="w-full bg-surface-container border border-outline-variant rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-primary placeholder:text-white/20"
                  />
                  <!-- Dropdown member -->
                  <div v-if="showMemberDropdown" class="absolute z-50 top-full left-0 right-0 bg-surface-container-high border border-white/10 rounded-lg mt-1 overflow-hidden shadow-xl">
                    <button
                      v-for="m in filteredMembers" :key="m.id"
                      @mousedown.prevent="selectMember(m)"
                      class="w-full text-left px-3 py-2 text-xs text-white hover:bg-primary/20 transition-all"
                    >
                      {{ m.full_name }}
                      <span class="text-white/30 ml-1">{{ m.phone_number }}</span>
                    </button>
                  </div>
                </div>

                <!-- Kalau nama baru (tidak ada di members) tampilkan input no HP -->
                <input
                  v-if="memberSearch && !allMembers.find(m => m.full_name === addPlayerForm.full_name)"
                  v-model="addPlayerForm.phone_number"
                  type="text"
                  placeholder="No. HP (member baru)"
                  class="w-full bg-surface-container border border-outline-variant rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-primary placeholder:text-white/20"
                />

                <!-- Posisi -->
                <select v-model="addPlayerForm.position" class="w-full bg-surface-container border border-outline-variant rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-primary">
                  <option value="GK">GK - Kiper</option>
                  <option value="DF">DF - Defender</option>
                  <option value="MF">MF - Midfielder</option>
                  <option value="FW">FW - Forward</option>
                </select>

                <button
                  @click="submitAddPlayer"
                  :disabled="addPlayerLoading || !memberSearch"
                  class="w-full bg-primary text-black font-black text-xs py-2 rounded-lg hover:scale-105 active:scale-95 transition-all disabled:opacity-40"
                >
                  <span v-if="addPlayerLoading">Menambahkan...</span>
                  <span v-else>+ Tambah ke Pool</span>
                </button>
              </div>
            </transition>
            
            <draggable 
              v-model="playerPool" 
              group="players" 
              item-key="id"
              class="flex-1 p-3 space-y-2 overflow-y-auto"
              ghost-class="opacity-50"
            >
              <template #item="{element}">
                <div class="p-3 bg-white/5 rounded-xl border border-white/5 cursor-move hover:bg-white/10 transition-all flex items-center justify-between group">
                  <div class="flex flex-col">
                    <span class="text-xs font-bold text-white">{{ element.player_name }}</span>
                    <span class="text-[10px] text-on-surface-variant/60 uppercase tracking-tighter">{{ element.position }}</span>
                  </div>
                  <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs text-on-surface-variant/30 group-hover:text-primary transition-colors">drag_indicator</span>
                    <button @click.stop="removePlayerFromMatch(element)" class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-300 transition-all ml-1">
                      <span class="material-symbols-outlined text-xs">close</span>
                    </button>
                  </div>
                </div>
              </template>
            </draggable>
            
            <div v-if="playerPool.length === 0 && !showAddPlayer" class="p-8 text-center opacity-20 italic text-[10px] uppercase tracking-widest">
              All players assigned
            </div>
          </div>
        </div>

        <!-- Teams Grid -->
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <div v-for="(team, tIdx) in teams" :key="tIdx" 
            class="glass-card flex flex-col h-full min-h-[450px] relative transition-all"
            :style="team.background ? { backgroundImage: `linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.9)), url(${team.background})`, backgroundSize: 'cover', backgroundPosition: 'center' } : {}"
          >
            <!-- Team Header -->
            <div class="p-4 border-b border-white/5 flex justify-between items-center" :class="!team.background ? 'bg-white/5' : ''">
              <input v-model="team.name" class="bg-transparent border-none text-primary font-black uppercase tracking-widest focus:outline-none w-full text-sm placeholder:text-primary/30" placeholder="Enter Team Name" />
              <div class="flex items-center gap-1">
                <button @click="triggerFileInput(tIdx)" class="text-on-surface-variant hover:text-white p-1 transition-colors" title="Change Background">
                  <span class="material-symbols-outlined text-sm">image</span>
                </button>
                <button v-if="teams.length > 2" @click="removeTeam(tIdx)" class="text-on-surface-variant hover:text-red-400 p-1">
                  <span class="material-symbols-outlined text-xs">close</span>
                </button>
              </div>
              <input :id="`team-bg-${tIdx}`" type="file" class="hidden" @change="handleFileUpload($event, tIdx)" accept="image/*" />
            </div>

            <!-- Player List -->
            <draggable 
              v-model="team.players" 
              group="players" 
              item-key="id"
              class="flex-1 p-3 space-y-2 min-h-[300px]"
              ghost-class="opacity-50"
              drag-class="scale-105"
            >
              <template #item="{element}">
                <div class="p-3 bg-white/5 backdrop-blur-md rounded-xl border border-white/10 cursor-move hover:bg-white/20 transition-all flex items-center justify-between group">
                  <div class="flex flex-col">
                    <span class="text-xs font-bold text-white">{{ element.player_name }}</span>
                    <span class="text-[10px] text-on-surface-variant/80 uppercase tracking-tighter">{{ element.position }}</span>
                  </div>
                  <span class="material-symbols-outlined text-xs text-on-surface-variant/50 group-hover:text-primary transition-colors">drag_indicator</span>
                </div>
              </template>
            </draggable>

            <!-- Team Footer -->
            <div class="p-3 bg-black/40 text-center backdrop-blur-sm">
              <span class="text-[10px] font-bold text-white uppercase tracking-widest">{{ team.players.length }} Players</span>
            </div>
          </div>

          <!-- Add Team Button -->
          <button v-if="teams.length < 6" @click="addTeam" class="h-[450px] rounded-3xl border-2 border-dashed border-white/5 flex flex-col items-center justify-center gap-4 text-on-surface-variant hover:border-primary/40 hover:text-primary transition-all group">
            <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-primary/10 transition-all">
              <span class="material-symbols-outlined">add</span>
            </div>
            <span class="font-bold text-sm uppercase tracking-widest">Add Team</span>
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.glass-card {
  background: rgba(255, 255, 255, 0.03);
  backdrop-filter: blur(10px);
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.05);
  overflow: hidden;
}

.spinner-sm {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

::-webkit-scrollbar {
  width: 4px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}

.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.2s ease;
}
.slide-down-enter-from, .slide-down-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
