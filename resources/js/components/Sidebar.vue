<template>
    <div class="sidebar">
        <!-- Logo -->
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-people-fill"></i>
            </div>
            <span class="brand-name">GestAbsences</span>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <p class="nav-section-label">Principal</p>

            <router-link to="/admin/dashboard" class="nav-item">
                <i class="bi bi-grid-1x2"></i>
                Dashboard
            </router-link>

            <router-link to="/admin/etudiants" class="nav-item">
                <i class="bi bi-person"></i>
                Étudiants
            </router-link>

            <router-link to="/admin/professeurs" class="nav-item">
                <i class="bi bi-person-badge"></i>
                Professeurs
            </router-link>

            <router-link to="/admin/filieres" class="nav-item">
                <i class="bi bi-book"></i>
                Filières
            </router-link>

            <p class="nav-section-label mt-3">Absences</p>

            <router-link to="/admin/absences" class="nav-item">
                <i class="bi bi-file-earmark-text"></i>
                Saisir les absences
            </router-link>

            <router-link to="/admin/rapports" class="nav-item">
                <i class="bi bi-bar-chart-line"></i>
                Rapports & Statistiques
            </router-link>
        </nav>

        <!-- User info -->
        <div class="sidebar-user">
            <div class="user-avatar">{{ userInitials }}</div>
            <div class="user-info">
                <p class="user-name">{{ userName }}</p>
                <p class="user-email">{{ userEmail }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Sidebar",
    computed: {
        userName() {
            return "Admin";
        },
        userEmail() {
            return "admin@univ.ma";
        },
        userInitials() {
            return "AD";
        },
    },
};
</script>

<style scoped>
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
    background: #0f6e56;
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
    background: #e1f5ee;
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
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e1f5ee;
    color: #3c9298;
    font-size: 11px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
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
}
.user-email {
    margin: 0;
    font-size: 11px;
    color: #9ca3af;
}
</style>
