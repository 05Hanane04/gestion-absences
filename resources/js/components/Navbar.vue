<template>
  <div class="navbar-top">
    <div class="navbar-left">
      <p class="page-title">Bienvenue, {{ userName }} dans votre espace personnel</p>
    </div>
    <div class="navbar-right">
      <div class="avatar-wrapper" @click="toggleUserMenu">
        <div class="user-avatar">{{ userInitials }}</div>
      </div>
      <div v-if="showUserMenu" class="user-dropdown">
        <div class="user-dropdown-header">
          <div class="user-avatar-lg">{{ userInitials }}</div>
          <div>
            <p class="dropdown-name">{{ userEmail }}</p>
            <span class="dropdown-role">{{ userRole }}</span>
          </div>
        </div>
        <div class="dropdown-divider"></div>
        <button class="dropdown-logout" @click="logout">
          <i class="bi bi-box-arrow-right"></i>
          Se déconnecter
        </button>
      </div>

    </div>
  </div>
</template>

<script>
import axios from '../axios.js'

export default {
  name: 'Navbar',
  props: {
    title: { type: String, default: 'Dashboard' }
  },
  data() {
    return {
      showNotif: false,
      showUserMenu: false,
      notifications: [
        { id: 2, text: 'Nouveau rapport disponible',             time: 'Il y a 1h'     },
        { id: 3, text: 'Prof. Alami a saisi les absences',       time: 'Il y a 2h'     },
      ]
    }
  },
  computed: {
    user()         { return JSON.parse(localStorage.getItem('user') || '{}') },
    userName()     { return this.user.name  || 'Utilisateur' },
    userEmail()    { return this.user.email || '' },
    userInitials() {
      return this.userName.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    },
    userRole() {
      const roles = { admin: 'Administrateur', prof: 'Professeur', etudiant: 'Étudiant(e)' }
      return roles[this.user.role] || this.user.role
    },
    notifCount() { return this.notifications.length }
  },
  methods: {
    toggleNotif()    {
      this.showNotif    = !this.showNotif
      this.showUserMenu = false
    },
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu
      this.showNotif    = false
    },
    async logout() {
      try {
        await axios.post('/logout')
      } catch (e) {
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        this.$router.push('/login')
      }
    }
  }
}
</script>

<style scoped>
.navbar-top {
  background: white;
  border-bottom: 0.5px solid #e5e7eb;
  padding: 0 24px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 10;
}
.page-title { margin: 0; font-size: 15px; font-weight: 500; }
.page-sub   { margin: 0; font-size: 11px; color: #9ca3af; }

.navbar-right {
  display: flex;
  align-items: center;
  gap: 12px;
  position: relative;
}

.avatar-wrapper { cursor: pointer; }
.user-avatar {
  width: 34px; height: 34px;
  border-radius: 50%;
  background: #E1F5EE;
  color: #3c9298;
  font-size: 12px; font-weight: 500;
  display: flex; align-items: center; justify-content: center;
}

.user-dropdown {
  position: absolute;
  top: 44px; right: 0;
  width: 240px;
  background: white;
  border: 0.5px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  z-index: 100;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.user-dropdown-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px;
}
.user-avatar-lg {
  width: 38px; height: 38px;
  border-radius: 50%;
  background: #E1F5EE;
  color: #3c9298;
  font-size: 13px; font-weight: 500;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.dropdown-name  { margin: 0; font-size: 13px; font-weight: 500; color: #111827; }
.dropdown-email { margin: 0; font-size: 11px; color: #9ca3af; }
.dropdown-role  {
  font-size: 10px;
  padding: 2px 8px;
  background: #E1F5EE;
  color: #3c9298;
  border-radius: 20px;
  margin-top: 4px;
  display: inline-block;
}
.dropdown-divider { height: 0.5px; background: #e5e7eb; }
.dropdown-logout {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 12px 14px;
  border: none;
  background: transparent;
  font-size: 13px;
  color: #E24B4A;
  cursor: pointer;
  text-align: left;
}
.dropdown-logout:hover { background: #FCEBEB; }
</style>