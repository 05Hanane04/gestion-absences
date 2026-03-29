import { createRouter, createWebHistory } from "vue-router";
import Login from "../views/auth/Login.vue";
import AdminLayout from "../views/admin/Layout.vue";
import Dashboard from "../views/admin/Dashbord.vue";
import Etudiants from "../views/admin/Etudiants.vue";
import Professeurs from "../views/admin/Professeurs.vue";
import EtudiantLayout from "../views/etudiant/Layout.vue";
import MesAbsences from "../views/etudiant/MesAbsences.vue";
import Planning from "../views/etudiant/Planning.vue";
import Profil from "../views/etudiant/Profil.vue";
import ProfLayout from "../views/prof/Layout.vue";
import DashboardProf from "../views/prof/Dashboard.vue";
import SaisieAbsences from "../views/prof/SaisieAbsences.vue";
import Historique from "../views/prof/Historique.vue";
import Rapports from "../views/admin/Rapports.vue";
import Absences from "../views/admin/Absences.vue";

const routes = [
    { path: "/", redirect: "/login" },
    { path: "/login", component: Login, meta: { guest: true } },
    {
        path: "/admin",
        component: AdminLayout,
        meta: { requiresAuth: true, role: "admin" },
        children: [
            { path: "", redirect: "dashboard" },
            { path: "dashboard", component: Dashboard },
            { path: "etudiants", component: Etudiants },
            { path: "professeurs", component: Professeurs },
            { path: "rapports", component: Rapports },
            { path: "absences", component: Absences },
        ],
    },
    {
        path: "/etudiant",
        component: EtudiantLayout,
        meta: { requiresAuth: true, role: "etudiant" },
        children: [
            { path: "", redirect: "absences" },
            { path: "absences", component: MesAbsences },
            { path: "planning", component: Planning },
            { path: "profil", component: Profil },
        ],
    },
    {
        path: "/prof",
        component: ProfLayout,
        meta: { requiresAuth: true, role: "prof" },
        children: [
            { path: "", redirect: "dashboard" },
            { path: "dashboard", component: DashboardProf },
            { path: "saisie", component: SaisieAbsences },
            { path: "historique", component: Historique },
        ],
    },
    { path: "/:pathMatch(.*)*", redirect: "/login" },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem("token");
    const user = JSON.parse(localStorage.getItem("user") || "{}");

    if (to.meta.requiresAuth) {
        if (!token) {
            return next("/login");
        }
        if (to.meta.role && user.role !== to.meta.role) {
            if (user.role === "admin") return next("/admin/dashboard");
            if (user.role === "prof") return next("/prof/dashboard");
            if (user.role === "etudiant") return next("/etudiant/absences");
            return next("/login");
        }
    }

    if (to.meta.guest && token) {
        if (user.role === "admin") return next("/admin/dashboard");
        if (user.role === "prof") return next("/prof/dashboard");
        if (user.role === "etudiant") return next("/etudiant/absences");
    }

    next();
});

export default router;
