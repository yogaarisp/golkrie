import { createRouter, createWebHistory } from 'vue-router'
import Landing from '../views/Landing.vue'
import AdminDashboard from '../views/AdminDashboard.vue'
import AdminMatches from '../views/AdminMatches.vue'
import AdminSettings from '../views/AdminSettings.vue'
import AdminMembers from '../views/AdminMembers.vue'
import AdminSponsors from '../views/AdminSponsors.vue'

const routes = [
  {
    path: '/',
    name: 'Landing',
    component: Landing
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboard
  },
  {
    path: '/admin/matches',
    name: 'AdminMatches',
    component: AdminMatches
  },
  {
    path: '/admin/settings',
    name: 'AdminSettings',
    component: AdminSettings
  },
  {
    path: '/admin/members',
    name: 'AdminMembers',
    component: AdminMembers
  },
  {
    path: '/admin/sponsors',
    name: 'AdminSponsors',
    component: AdminSponsors
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
