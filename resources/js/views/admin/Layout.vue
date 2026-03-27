<template>
    <div class="app-layout">
        <Sidebar />
        <div class="main-content">
            <Navbar :title="pageTitle" />
            <div class="page-body">
                <router-view />
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "../../components/Sidebar.vue";
import Navbar from "../../components/Navbar.vue";

export default {
    name: "AdminLayout",
    components: { Sidebar, Navbar },
    computed: {
        pageTitle() {
            const titles = {
                "/admin/dashboard": "Dashboard",
                "/admin/etudiants": "Gestion des étudiants",
                "/admin/professeurs": "Gestion des professeurs",
                "/admin/filieres": "Filières",
                "/admin/absences": "Saisir les absences",
                "/admin/rapports": "Rapports & Statistiques",
            };
            return titles[this.$route.path] || "Dashboard";
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
