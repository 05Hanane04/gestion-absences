<template>
    <div class="app-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <span class="brand-name">GestAbsences</span>
            </div>

            <nav class="sidebar-nav">
                <p class="nav-section-label">Mon espace</p>

                <router-link to="/etudiant/absences" class="nav-item">
                    <i class="bi bi-calendar-x"></i>
                    Mes absences
                </router-link>

                <router-link to="/etudiant/planning" class="nav-item">
                    <i class="bi bi-calendar-week"></i>
                    Mon planning
                </router-link>

                <router-link to="/etudiant/profil" class="nav-item">
                    <i class="bi bi-person-circle"></i>
                    Mon profil
                </router-link>
            </nav>
            <div class="sidebar-user">
                <router-link to="/etudiant/profil" class="user-avatar">
                    {{ userInitials }}
                </router-link>
                <div class="user-info">
                    <p class="user-name">{{ userName }}</p>
                    <p class="user-email">{{ userEmail }}</p>
                </div>
            </div>
        </div>
        <div class="main-content">
            <Navbar :title="pageTitle" />
            <div class="page-body">
                <router-view />
            </div>
        </div>
    </div>
</template>

<script>
import Navbar from "../../components/Navbar.vue";

export default {
    name: "EtudiantLayout",
    components: { Navbar },
    computed: {
        user() {
            return JSON.parse(localStorage.getItem("user") || "{}");
        },
        userName() {
            return this.user.name || "Étudiant";
        },
        userEmail() {
            return this.user.email || "";
        },
        userInitials() {
            return this.userName
                .split(" ")
                .map((n) => n[0])
                .join("")
                .toUpperCase()
                .slice(0, 2);
        },
        pageTitle() {
            const titles = {
                "/etudiant/absences": "Mes absences",
                "/etudiant/planning": "Mon planning",
                "/etudiant/profil": "Mon profil",
            };
            return titles[this.$route.path] || "Mon espace";
        },
    },
};
</script>

<style>
.app-layout {
    display: flex;
    height: 100vh;
    overflow: hidden;
}
.sidebar {
    width: 220px;
    min-width: 220px;
    background: white;
    border-right: 0.5px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    height: 100vh;
    position: sticky;
    top: 0;
}
.sidebar-brand {
    padding: 20px;
    border-bottom: 0.5px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 10px;
}
.brand-icon {
    width: 32px;
    height: 32px;
    background: #3c9298;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 15px;
}
.brand-name {
    font-size: 14px;
    font-weight: 500;
    color: #111827;
}
.sidebar-nav {
    padding: 12px 10px;
    flex: 1;
    overflow-y: auto;
}
.nav-section-label {
    font-size: 10px;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 0 8px;
    margin: 0 0 6px;
}
.nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    border-radius: 8px;
    color: #6b7280;
    font-size: 13px;
    text-decoration: none;
    margin-bottom: 2px;
    transition: all 0.15s;
}
.nav-item:hover {
    background: #f3f4f6;
    color: #111827;
}
.nav-item.router-link-active {
    background: #e8f5f6;
    color: #3c9298;
    font-weight: 500;
}
.nav-item i {
    font-size: 15px;
}
.sidebar-user {
    padding: 14px 16px;
    border-top: 0.5px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 10px;
}
.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e8f5f6;
    color: #3c9298;
    font-size: 11px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    text-decoration: none;
}
.user-info {
    flex: 1;
    min-width: 0;
}
.user-name {
    margin: 0;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #111827;
}
.user-email {
    margin: 0;
    font-size: 11px;
    color: #9ca3af;
}
.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #f8f9fb;
}
.page-body {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
}
</style>
