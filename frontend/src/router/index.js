import { createRouter, createWebHistory } from 'vue-router'
import Landing from '../views/Landing.vue'
import Login from '../views/Login.vue'
import AdminDashboard from '../views/AdminDashboard.vue'
import AdminMatches from '../views/AdminMatches.vue'
import AdminSettings from '../views/AdminSettings.vue'
import AdminMembers from '../views/AdminMembers.vue'
import AdminSponsors from '../views/AdminSponsors.vue'
import AdminTeams from '../views/AdminTeams.vue'

const routes = [
  {
    path: '/',
    name: 'Landing',
    component: Landing
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/matches',
    name: 'AdminMatches',
    component: AdminMatches,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/settings',
    name: 'AdminSettings',
    component: AdminSettings,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/members',
    name: 'AdminMembers',
    component: AdminMembers,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/sponsors',
    name: 'AdminSponsors',
    component: AdminSponsors,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/matches/:id/teams',
    name: 'AdminTeams',
    component: AdminTeams,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('token');
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
  } else if (to.path === '/login' && isAuthenticated) {
    next('/admin');
  } else {
    next();
  }
});

export default router
