<template>
    <div>
        <div v-if="loading" class="loading">Chargement...</div>

        <div v-else class="profil-grid">
            <div class="profil-card">
                <div class="profil-avatar-section">
                    <div class="big-avatar">{{ userInitials }}</div>
                    <h2 class="profil-name">{{ user.name }}</h2>
                    <p class="profil-email">{{ user.email }}</p>
                    <div class="profil-badges">
                        <span class="badge-filiere">{{
                            etudiant.filiere?.code
                        }}</span>
                        <span class="badge-groupe"
                            >Groupe {{ etudiant.groupe }}</span
                        >
                    </div>
                </div>

                <div class="profil-info">
                    <div class="info-row">
                        <span class="info-label"
                            ><i class="bi bi-credit-card"></i> CNE</span
                        >
                        <span class="info-value">{{ etudiant.cne }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"
                            ><i class="bi bi-book"></i> Filière</span
                        >
                        <span class="info-value">{{
                            etudiant.filiere?.nom
                        }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"
                            ><i class="bi bi-people"></i> Groupe</span
                        >
                        <span class="info-value">{{ etudiant.groupe }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"
                            ><i class="bi bi-calendar3"></i> Année</span
                        >
                        <span class="info-value">3ème année — 2025/2026</span>
                    </div>
                </div>
            </div>

            <div class="right-col">
                <div class="resume-card">
                    <p class="card-title">Résumé des absences</p>
                    <div class="resume-stats">
                        <div class="resume-stat">
                            <p class="rs-value">{{ totalAbsences }}h</p>
                            <p class="rs-label">Total</p>
                        </div>
                        <div class="resume-stat danger">
                            <p class="rs-value">{{ nonJustifiees }}h</p>
                            <p class="rs-label">Non justifiées</p>
                        </div>
                        <div class="resume-stat success">
                            <p class="rs-value">{{ justifiees }}h</p>
                            <p class="rs-label">Justifiées</p>
                        </div>
                        <div class="resume-stat warning">
                            <p class="rs-value">{{ enAttente }}h</p>
                            <p class="rs-label">En attente</p>
                        </div>
                    </div>
                    <div class="progress-section">
                        <div class="progress-header">
                            <span class="progress-label"
                                >Seuil d'absences autorisées</span
                            >
                            <span
                                class="progress-pct"
                                :style="{
                                    color:
                                        totalAbsences >= seuilMax
                                            ? '#E24B4A'
                                            : '#3c9298',
                                }"
                            >
                                {{
                                    Math.round(
                                        (totalAbsences / seuilMax) * 100,
                                    )
                                }}%
                            </span>
                        </div>
                        <div class="progress-bar">
                            <div
                                class="progress-fill"
                                :style="{
                                    width:
                                        Math.min(
                                            (totalAbsences / seuilMax) * 100,
                                            100,
                                        ) + '%',
                                    background:
                                        totalAbsences >= seuilMax
                                            ? '#E24B4A'
                                            : '#3c9298',
                                }"
                            ></div>
                        </div>
                        <p class="progress-note">
                            {{ totalAbsences }}h / {{ seuilMax }}h maximum
                            autorisées
                        </p>
                        <div
                            v-if="totalAbsences >= seuilMax"
                            class="alert-danger"
                        >
                            <i class="bi bi-exclamation-triangle"></i>
                            Seuil maximum atteint — contactez l'administration
                        </div>
                    </div>
                </div>

                <div class="modules-card">
                    <p class="card-title">Absences par module</p>
                    <div v-if="absencesParModule.length === 0" class="no-data">
                        Aucune absence enregistrée
                    </div>
                    <div
                        v-for="m in absencesParModule"
                        :key="m.module"
                        class="module-row"
                    >
                        <div class="module-info">
                            <span class="module-nom">{{ m.module }}</span>
                            <div class="module-right">
                                <span class="module-h">{{ m.heures }}h</span>
                                <span
                                    class="module-badge"
                                    :class="
                                        m.heures > 4
                                            ? 'badge-danger'
                                            : 'badge-ok'
                                    "
                                >
                                    {{ m.heures > 4 ? "Critique" : "OK" }}
                                </span>
                            </div>
                        </div>
                        <div class="module-bar">
                            <div
                                class="module-fill"
                                :style="{
                                    width:
                                        Math.min((m.heures / 8) * 100, 100) +
                                        '%',
                                    background:
                                        m.heures > 4 ? '#E24B4A' : '#3c9298',
                                }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../axios.js";

export default {
    name: "Profil",
    data() {
        return { loading: true, seuilMax: 16, etudiant: {}, absences: [] };
    },
    computed: {
        user() {
            return JSON.parse(localStorage.getItem("user") || "{}");
        },
        userInitials() {
            return (this.user.name || "ET")
                .split(" ")
                .map((n) => n[0])
                .join("")
                .toUpperCase()
                .slice(0, 2);
        },
        totalAbsences() {
            return this.absences.reduce((s) => s + 2, 0);
        },
        nonJustifiees() {
            return this.absences
                .filter((a) => a.statut === "non-justifiee")
                .reduce((s) => s + 2, 0);
        },
        justifiees() {
            return this.absences
                .filter((a) => a.statut === "justifiee")
                .reduce((s) => s + 2, 0);
        },
        enAttente() {
            return this.absences
                .filter((a) => a.statut === "en-attente")
                .reduce((s) => s + 2, 0);
        },
        absencesParModule() {
            const map = {};
            this.absences.forEach((a) => {
                const nom = a.seance?.module?.nom || "Inconnu";
                if (!map[nom]) map[nom] = 0;
                map[nom] += 2;
            });
            return Object.entries(map).map(([module, heures]) => ({
                module,
                heures,
            }));
        },
    },
    async mounted() {
        await this.chargerProfil();
    },
    methods: {
        async chargerProfil() {
            try {
                this.loading = true;
                const userId = this.user.id;
                const resEtudiants = await axios.get("/etudiants");
                this.etudiant =
                    resEtudiants.data.find((e) => e.user_id === userId) || {};
                if (this.etudiant.id) {
                    const resAbsences = await axios.get(
                        `/absences/etudiant/${this.etudiant.id}`,
                    );
                    this.absences = resAbsences.data;
                }
            } catch (err) {
                console.error("Erreur chargement profil", err);
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: #9ca3af;
    font-size: 14px;
}
.profil-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 16px;
    align-items: start;
}
.profil-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    overflow: hidden;
}
.profil-avatar-section {
    background: #e8f5f6;
    padding: 28px 20px;
    text-align: center;
    border-bottom: 0.5px solid #e5e7eb;
}
.big-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #3c9298;
    color: white;
    font-size: 22px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}
.profil-name {
    margin: 0 0 4px;
    font-size: 16px;
    font-weight: 500;
    color: #111827;
}
.profil-email {
    margin: 0 0 10px;
    font-size: 12px;
    color: #6b7280;
}
.profil-badges {
    display: flex;
    gap: 6px;
    justify-content: center;
    flex-wrap: wrap;
}
.badge-filiere {
    font-size: 11px;
    padding: 3px 10px;
    background: #3c9298;
    color: white;
    border-radius: 20px;
}
.badge-groupe {
    font-size: 11px;
    padding: 3px 10px;
    background: white;
    color: #3c9298;
    border: 0.5px solid #3c9298;
    border-radius: 20px;
}
.profil-info {
    padding: 16px 20px;
}
.info-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 0.5px solid #f3f4f6;
    font-size: 13px;
    gap: 10px;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    color: #9ca3af;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.info-value {
    color: #111827;
    font-weight: 500;
    text-align: right;
}
.right-col {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.resume-card,
.modules-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 18px;
}
.card-title {
    margin: 0 0 16px;
    font-size: 14px;
    font-weight: 500;
    color: #111827;
}
.resume-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}
.resume-stat {
    text-align: center;
    padding: 12px;
    border-radius: 8px;
    background: #f8f9fb;
}
.resume-stat.danger {
    background: #fcebeb;
}
.resume-stat.success {
    background: #e1f5ee;
}
.resume-stat.warning {
    background: #faeeda;
}
.rs-value {
    margin: 0;
    font-size: 22px;
    font-weight: 500;
    color: #111827;
}
.resume-stat.danger .rs-value {
    color: #e24b4a;
}
.resume-stat.success .rs-value {
    color: #0f6e56;
}
.resume-stat.warning .rs-value {
    color: #854f0b;
}
.rs-label {
    margin: 4px 0 0;
    font-size: 11px;
    color: #9ca3af;
}
.progress-section {
    margin-top: 4px;
}
.progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
    font-size: 12px;
}
.progress-label {
    color: #6b7280;
}
.progress-bar {
    height: 6px;
    background: #f3f4f6;
    border-radius: 3px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s;
}
.progress-note {

    margin: 6px 0 0;
    font-size: 11px;
    color: #9ca3af;
}
.alert-danger {
    margin-top: 10px;
    padding: 8px 12px;
    background: #fcebeb;
    color: #a32d2d;
    border-radius: 8px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.no-data {
    text-align: center;
    color: #9ca3af;
    font-size: 13px;
    padding: 20px 0;
}
.module-row {
    margin-bottom: 14px;
}
.module-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}
.module-right {
    display: flex;
    align-items: center;
    gap: 8px;
}
.module-nom {
    font-size: 13px;
    color: #6b7280;
}
.module-h {
    font-size: 13px;
    color: #111827;
    font-weight: 500;
}
.module-badge {
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 20px;
}
.badge-ok {
    background: #e1f5ee;
    color: #085041;
}
.badge-danger {
    background: #fcebeb;
    color: #a32d2d;
}
.module-bar {
    height: 6px;
    background: #f3f4f6;
    border-radius: 3px;
    overflow: hidden;
}
.module-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s;
}

@media (max-width: 768px) {
    .profil-grid {
        grid-template-columns: 1fr;
    }
    .resume-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    .rs-value {
        font-size: 18px;
    }
    .info-row {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .resume-stats {
        gap: 8px;
    }
    .profil-avatar-section {
        padding: 20px 16px;
    }
    .big-avatar {
        width: 52px;
        height: 52px;
        font-size: 18px;
    }
    .profil-name {
        font-size: 14px;
    }
}
</style>
