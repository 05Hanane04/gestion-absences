<template>
    <div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-label">Mes modules</p>
                    <div class="stat-icon" style="background: #e8f5f6">
                        <i class="bi bi-book" style="color: #3c9298"></i>
                    </div>
                </div>
                <p class="stat-value">{{ modules.length }}</p>
                <p class="stat-trend muted">ce semestre</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-label">Absences saisies</p>
                    <div class="stat-icon" style="background: #fcebeb">
                        <i
                            class="bi bi-pencil-square"
                            style="color: #a32d2d"
                        ></i>
                    </div>
                </div>
                <p class="stat-value">{{ totalAbsences }}</p>
                <p class="stat-trend muted">au total</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-label">Séances effectuées</p>
                    <div class="stat-icon" style="background: #e6f1fb">
                        <i
                            class="bi bi-calendar-check"
                            style="color: #185fa5"
                        ></i>
                    </div>
                </div>
                <p class="stat-value">{{ seances.length }}</p>
                <p class="stat-trend muted">ce semestre</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-label">Étudiants suivis</p>
                    <div class="stat-icon" style="background: #faeeda">
                        <i class="bi bi-people" style="color: #854f0b"></i>
                    </div>
                </div>
                <p class="stat-value">{{ totalEtudiants }}</p>
                <p class="stat-trend muted">dans mes filières</p>
            </div>
        </div>

        <!-- Mes modules -->
        <div class="table-card">
            <p class="card-title">Mes modules</p>
            <div
                v-if="loading"
                style="text-align: center; padding: 20px; color: #9ca3af"
            >
                Chargement...
            </div>
            <table v-else class="data-table">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Code</th>
                        <th>Filière</th>
                        <th>Volume horaire</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="m in modules" :key="m.id">
                        <td class="bold">{{ m.nom }}</td>
                        <td>
                            <span class="badge-code">{{ m.code }}</span>
                        </td>
                        <td class="muted">{{ m.filiere?.nom }}</td>
                        <td class="muted">{{ m.volume_horaire }}h</td>
                        <td>
                            <button
                                class="btn-saisir"
                                @click="allerSaisie(m.id)"
                            >
                                <i class="bi bi-pencil-square"></i> Saisir
                                absences
                            </button>
                        </td>
                    </tr>
                    <tr v-if="modules.length === 0">
                        <td
                            colspan="5"
                            style="
                                text-align: center;
                                padding: 20px;
                                color: #9ca3af;
                            "
                        >
                            Aucun module assigné
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from "../../axios.js";

export default {
    name: "DashboardProf",
    data() {
        return {
            loading: true,
            modules: [],
            seances: [],
            totalAbsences: 0,
            totalEtudiants: 0,
            professeurId: null,
        };
    },
    async mounted() {
        await this.chargerDonnees();
    },
    methods: {
        async chargerDonnees() {
            try {
                this.loading = true;
                const user = JSON.parse(localStorage.getItem("user") || "{}");

                // Trouver le professeur
                const resProfs = await axios.get("/professeurs");
                const prof = resProfs.data.find((p) => p.user_id === user.id);
                if (!prof) return;
                this.professeurId = prof.id;

                // Charger les modules de ce prof
                const resMods = await axios.get("/modules");
                this.modules = resMods.data.filter(
                    (m) => m.professeur_id === prof.id,
                );

                // Charger les séances
                const resSeances = await axios.get("/seances");
                const modIds = this.modules.map((m) => m.id);
                this.seances = resSeances.data.filter((s) =>
                    modIds.includes(s.module_id),
                );

                // Charger les absences
                const resAbs = await axios.get("/absences");
                const seanceIds = this.seances.map((s) => s.id);
                this.totalAbsences = resAbs.data.filter((a) =>
                    seanceIds.includes(a.seance_id),
                ).length;

                // Charger les étudiants
                const resEts = await axios.get("/etudiants");
                this.totalEtudiants = resEts.data.length;
            } catch (err) {
                console.error("Erreur", err);
            } finally {
                this.loading = false;
            }
        },
        allerSaisie(moduleId) {
            this.$router.push({
                path: "/prof/saisie",
                query: { module: moduleId },
            });
        },
    },
};
</script>

<style scoped>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
.stat-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 16px;
}
.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.stat-label {
    margin: 0;
    font-size: 12px;
    color: #9ca3af;
}
.stat-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}
.stat-value {
    margin: 0;
    font-size: 24px;
    font-weight: 500;
    color: #111827;
}
.stat-trend {
    margin: 4px 0 0;
    font-size: 11px;
}
.stat-trend.muted {
    color: #9ca3af;
}

.table-card {
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
.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.data-table th {
    text-align: left;
    padding: 8px 0;
    color: #9ca3af;
    font-weight: 400;
    border-bottom: 0.5px solid #e5e7eb;
}
.data-table td {
    padding: 12px 0;
    border-bottom: 0.5px solid #f3f4f6;
}
.data-table tr:last-child td {
    border-bottom: none;
}
.bold {
    font-weight: 500;
    color: #111827;
}
.muted {
    color: #6b7280;
}
.badge-code {
    font-size: 11px;
    padding: 3px 10px;
    background: #e8f5f6;
    color: #3c9298;
    border-radius: 20px;
    font-family: monospace;
}
.btn-saisir {
    background: #3c9298;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-saisir:hover {
    background: #2d7a7f;
}
</style>
