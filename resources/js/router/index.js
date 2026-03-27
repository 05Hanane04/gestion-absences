import { createRouter, createWebHistory } from 'vue-router'
import Login          from '../views/auth/Login.vue'
import AdminLayout    from '../views/admin/Layout.vue'
import Dashboard      from '../views/admin/Dashbord.vue'
import Etudiants      from '../views/admin/Etudiants.vue'
import Professeurs    from '../views/admin/Professeurs.vue'
import EtudiantLayout from '../views/etudiant/Layout.vue'
import MesAbsences    from '../views/etudiant/MesAbsences.vue'
import Planning       from '../views/etudiant/Planning.vue'
import Profil         from '../views/etudiant/Profil.vue'

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: Login },
  {
    path: '/admin',
    component: AdminLayout,
    children: [
      { path: '',            redirect: 'dashboard'  },
      { path: 'dashboard',   component: Dashboard   },
      { path: 'etudiants',   component: Etudiants   },
      { path: 'professeurs', component: Professeurs },
    ]
  },
  {
    path: '/etudiant',
    component: EtudiantLayout,
    children: [
      { path: '',         redirect: 'absences' },
      { path: 'absences', component: MesAbsences },
      { path: 'planning', component: Planning    },
      { path: 'profil',   component: Profil      },
    ]
  }
]

export default createRouter({
  history: createWebHistory(),
  routes
})