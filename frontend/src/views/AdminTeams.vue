<script setup>
import { ref, onMounted, computed } from 'vue';
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

// Computed property to group players into teams
const teams = ref([]);

const fetchTeams = async () => {
  try {
    const response = await axios.get(`/api/admin/matches/${matchId}/teams`);
    match.value = response.data.match;
    allPlayers.value = response.data.registrations;
    
    // Determine number of teams from data or default
    const uniqueTeams = [...new Set(allPlayers.value.map(p => p.team_name).filter(Boolean))];
    const initialTeamCount = Math.max(uniqueTeams.length, 2);
    
    setupTeams(initialTeamCount);
  } catch (e) {
    console.error('Failed to fetch teams', e);
  } finally {
    loading.value = false;
  }
};

const setupTeams = (count) => {
  const newTeams = [];
  for (let i = 0; i < count; i++) {
    const name = "Team " + chr(65 + i);
    newTeams.push({
      name: name,
      players: allPlayers.value.filter(p => p.team_name === name)
    });
  }
  
  // Add unassigned players to a virtual "Unassigned" list if needed
  const unassigned = allPlayers.value.filter(p => !p.team_name || !newTeams.find(t => t.name === p.team_name));
  if (unassigned.length > 0) {
    // Distribute unassigned players or keep them separate? 
    // For now, let's just make sure everyone is in the list
    if (newTeams.length > 0) {
       newTeams[0].players = [...newTeams[0].players, ...unassigned];
    }
  }
  
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
    setupTeams(teamCount.value);
  } catch (e) {
    alert('Failed to shuffle players.');
  } finally {
    loading.value = false;
  }
};

const saveTeams = async () => {
  saving.value = true;
  try {
    const assignments = [];
    teams.value.forEach(team => {
      team.players.forEach(player => {
        assignments.push({
          id: player.id,
          team_name: team.name
        });
      });
    });
    
    await axios.post(`/api/admin/matches/${matchId}/teams`, { assignments });
    alert('Teams saved successfully!');
  } catch (e) {
    alert('Failed to save teams.');
  } finally {
    saving.value = false;
  }
};

onMounted(fetchTeams);

const addTeam = () => {
  if (teams.value.length >= 6) return;
  teams.value.push({
    name: "Team " + chr(65 + teams.value.length),
    players: []
  });
};

const removeTeam = (index) => {
  if (teams.value.length <= 2) return;
  // Move players to the first team
  const playersToMove = teams.value[index].players;
  teams.value[0].players = [...teams.value[0].players, ...playersToMove];
  teams.value.splice(index, 1);
};

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
            Save Teams
          </button>
        </div>
      </div>

      <!-- Teams Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-start">
        <div v-for="(team, tIdx) in teams" :key="tIdx" class="glass-card flex flex-col h-full min-h-[400px]">
          <!-- Team Header -->
          <div class="p-4 border-b border-white/5 flex justify-between items-center bg-white/5">
            <input v-model="team.name" class="bg-transparent border-none text-primary font-black uppercase tracking-widest focus:outline-none w-full text-sm" />
            <button v-if="teams.length > 2" @click="removeTeam(tIdx)" class="text-on-surface-variant hover:text-red-400 p-1">
              <span class="material-symbols-outlined text-xs">close</span>
            </button>
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
              <div class="p-3 bg-white/5 rounded-xl border border-white/5 cursor-move hover:bg-white/10 transition-all flex items-center justify-between group">
                <div class="flex flex-col">
                  <span class="text-xs font-bold text-white">{{ element.player_name }}</span>
                  <span class="text-[10px] text-on-surface-variant/60 uppercase tracking-tighter">{{ element.position }}</span>
                </div>
                <span class="material-symbols-outlined text-xs text-on-surface-variant/30 group-hover:text-primary transition-colors">drag_indicator</span>
              </div>
            </template>
          </draggable>

          <!-- Team Footer -->
          <div class="p-3 bg-black/20 text-center">
            <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">{{ team.players.length }} Players</span>
          </div>
        </div>

        <!-- Add Team Button -->
        <button v-if="teams.length < 6" @click="addTeam" class="h-[400px] rounded-3xl border-2 border-dashed border-white/5 flex flex-col items-center justify-center gap-4 text-on-surface-variant hover:border-primary/40 hover:text-primary transition-all group">
          <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-primary/10 transition-all">
            <span class="material-symbols-outlined">add</span>
          </div>
          <span class="font-bold text-sm uppercase tracking-widest">Add Team</span>
        </button>
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
</style>
