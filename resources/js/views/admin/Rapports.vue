<template>
    <div>
        <!-- Filtres -->
        <div class="filters-card">
            <div class="filter-group">
                <label>Filière</label>
                <select v-model="filtreFiliere" @change="chargerStats">
                    <option value="">Toutes les filières</option>
                    <option v-for="f in filieres" :key="f.id" :value="f.id">
                        {{ f.code }} — {{ f.nom }}
                    </option>
                </select>
            </div>
            <div class="filter-group">
                <label>Statut</label>
                <select v-model="filtreStatut" @change="chargerStats">
                    <option value="">Tous</option>
                    <option value="non-justifiee">Non justifiées</option>
                    <option value="justifiee">Justifiées</option>
                    <option value="en-attente">En attente</option>
                </select>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <p class="stat-label">Total absences</p>
                <p class="stat-value">{{ absencesFiltrees.length }}</p>
            </div>
            <div class="stat-card danger">
                <p class="stat-label">Non justifiées</p>
                <p class="stat-value">{{ nonJustifiees }}</p>
            </div>
            <div class="stat-card success">
                <p class="stat-label">Justifiées</p>
                <p class="stat-value">{{ justifiees }}</p>
            </div>
            <div class="stat-card warning">
                <p class="stat-label">En attente</p>
                <p class="stat-value">{{ enAttente }}</p>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <p class="card-title">Absences par module</p>
                <canvas id="moduleChart" height="120"></canvas>
            </div>
            <div class="chart-card">
                <p class="card-title">Absences par étudiant (top 5)</p>
                <canvas id="etudiantChart" height="120"></canvas>
            </div>
        </div>
        <div class="table-card">
            <div class="table-header">
                <p class="card-title">Détail des absences</p>
                <span class="total-badge"
                    >{{ absencesFiltrees.length }} résultats</span
                >
            </div>
            <div
                v-if="loading"
                style="text-align: center; padding: 20px; color: #9ca3af"
            >
                Chargement...
            </div>
            <table v-else class="abs-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Filière</th>
                        <th>Module</th>
                        <th>Professeur</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="abs in absencesFiltrees" :key="abs.id">
                        <td>
                            <div class="etudiant-cell">
                                <div class="mini-avatar">
                                    {{ initials(abs.etudiant?.user?.name) }}
                                </div>
                                {{ abs.etudiant?.user?.name }}
                            </div>
                        </td>
                        <td>
                            <span class="badge-filiere">{{
                                abs.etudiant?.filiere?.code
                            }}</span>
                        </td>
                        <td class="muted">{{ abs.seance?.module?.nom }}</td>
                        <td class="muted">
                            {{ abs.seance?.module?.professeur?.user?.name }}
                        </td>
                        <td class="muted">
                            {{ formatDate(abs.seance?.date) }}
                        </td>
                        <td>
                            <span class="badge-statut" :class="abs.statut">
                                {{ statutLabel(abs.statut) }}
                            </span>
                        </td>
                        <td>
                            <select
                                class="statut-select"
                                :value="abs.statut"
                                @change="
                                    changerStatut(abs.id, $event.target.value)
                                "
                            >
                                <option value="non-justifiee">
                                    Non justifiée
                                </option>
                                <option value="justifiee">Justifiée</option>
                                <option value="en-attente">En attente</option>
                            </select>
                        </td>
                    </tr>
                    <tr v-if="absencesFiltrees.length === 0">
                        <td
                            colspan="7"
                            style="
                                text-align: center;
                                padding: 20px;
                                color: #9ca3af;
                            "
                        >
                            Aucune absence trouvée
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { Chart, registerables } from "chart.js";
import axios from "../../axios.js";
Chart.register(...registerables);

export default {
    name: "Rapports",
    data() {
        return {
            loading: true,
            absences: [],
            filieres: [],
            filtreFiliere: "",
            filtreStatut: "",
            moduleChart: null,
            etudiantChart: null,
        };
    },
    computed: {
        absencesFiltrees() {
            return this.absences.filter((a) => {
                const matchFiliere =
                    !this.filtreFiliere ||
                    a.etudiant?.filiere_id == this.filtreFiliere;
                const matchStatut =
                    !this.filtreStatut || a.statut === this.filtreStatut;
                return matchFiliere && matchStatut;
            });
        },
        nonJustifiees() {
            return this.absencesFiltrees.filter(
                (a) => a.statut === "non-justifiee",
            ).length;
        },
        justifiees() {
            return this.absencesFiltrees.filter((a) => a.statut === "justifiee")
                .length;
        },
        enAttente() {
            return this.absencesFiltrees.filter(
                (a) => a.statut === "en-attente",
            ).length;
        },
    },
    async mounted() {
        await this.chargerDonnees();
    },
    methods: {
        async chargerDonnees() {
            try {
                this.loading = true;
                const [resAbs, resFil] = await Promise.all([
                    axios.get("/absences"),
                    axios.get("/filieres"),
                ]);
                this.absences = resAbs.data;
                this.filieres = resFil.data;
                this.$nextTick(() => this.initCharts());
            } catch (err) {
                console.error("Erreur", err);
            } finally {
                this.loading = false;
            }
        },
        async chargerStats() {
            this.$nextTick(() => this.initCharts());
        },
        initCharts() {
            const countModule = {};
            this.absencesFiltrees.forEach((a) => {
                const nom = a.seance?.module?.nom || "Inconnu";
                countModule[nom] = (countModule[nom] || 0) + 1;
            });
            if (this.moduleChart) this.moduleChart.destroy();
            this.moduleChart = new Chart(
                document.getElementById("moduleChart"),
                {
                    type: "bar",
                    data: {
                        labels: Object.keys(countModule),
                        datasets: [
                            {
                                data: Object.values(countModule),
                                backgroundColor: "#3c9298",
                                borderRadius: 6,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                grid: { color: "#f3f4f6" },
                                ticks: { color: "#9ca3af", font: { size: 11 } },
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: "#9ca3af", font: { size: 11 } },
                            },
                        },
                    },
                },
            );

            const countEtudiant = {};
            this.absencesFiltrees.forEach((a) => {
                const nom = a.etudiant?.user?.name || "Inconnu";
                countEtudiant[nom] = (countEtudiant[nom] || 0) + 1;
            });
            const sorted = Object.entries(countEtudiant)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 5);

            if (this.etudiantChart) this.etudiantChart.destroy();
            this.etudiantChart = new Chart(
                document.getElementById("etudiantChart"),
                {
                    type: "bar",
                    data: {
                        labels: sorted.map((e) => e[0]),
                        datasets: [
                            {
                                data: sorted.map((e) => e[1]),
                                backgroundColor: "#E24B4A",
                                borderRadius: 6,
                            },
                        ],
                    },
                    options: {
                        indexAxis: "y",
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                grid: { color: "#f3f4f6" },
                                ticks: { color: "#9ca3af", font: { size: 11 } },
                            },
                            y: {
                                grid: { display: false },
                                ticks: { color: "#9ca3af", font: { size: 11 } },
                            },
                        },
                    },
                },
            );
        },
        async changerStatut(absenceId, statut) {
            try {
                await axios.put(`/absences/${absenceId}/statut`, { statut });
                const abs = this.absences.find((a) => a.id === absenceId);
                if (abs) abs.statut = statut;
                this.$nextTick(() => this.initCharts());
            } catch (err) {
                alert("Erreur mise à jour");
            }
        },
        initials(name) {
            if (!name) return "?";
            return name
                .split(" ")
                .map((n) => n[0])
                .join("")
                .toUpperCase()
                .slice(0, 2);
        },
        formatDate(date) {
            if (!date) return "";
            return new Date(date).toLocaleDateString("fr-FR", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
            });
        },
        statutLabel(statut) {
            const labels = {
                justifiee: "Justifiée",
                "non-justifiee": "Non justifiée",
                "en-attente": "En attente",
            };
            return labels[statut] || statut;
        },
    },
};
</script>

<style scoped>
.filters-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 16px 18px;
    margin-bottom: 16px;
    display: flex;
    align-items: flex-end;
    gap: 14px;
    flex-wrap: wrap;
}
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.filter-group label {
    font-size: 12px;
    color: #9ca3af;
}
.filter-group select {
    padding: 7px 12px;
    border: 0.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    color: #111827;
    background: white;
    outline: none;
}
.filter-group select:focus {
    border-color: #3c9298;
}
.btn-export {
    margin-left: auto;
    background: #3c9298;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-export:hover {
    background: #2d7a7f;
}

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
    text-align: center;
}
.stat-card.danger {
    background: #fcebeb;
    border-color: #f09595;
}
.stat-card.success {
    background: #e1f5ee;
    border-color: #9fe1cb;
}
.stat-card.warning {
    background: #faeeda;
    border-color: #fac775;
}
.stat-label {
    margin: 0 0 8px;
    font-size: 12px;
    color: #9ca3af;
}
.stat-card.danger .stat-label {
    color: #a32d2d;
}
.stat-card.success .stat-label {
    color: #085041;
}
.stat-card.warning .stat-label {
    color: #633806;
}
.stat-value {
    margin: 0;
    font-size: 28px;
    font-weight: 500;
    color: #111827;
}
.stat-card.danger .stat-value {
    color: #e24b4a;
}
.stat-card.success .stat-value {
    color: #0f6e56;
}
.stat-card.warning .stat-value {
    color: #854f0b;
}

.charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 20px;
}
.chart-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 18px;
}
.card-title {
    margin: 0 0 14px;
    font-size: 14px;
    font-weight: 500;
    color: #111827;
}

.table-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 18px;
}
.table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}
.total-badge {
    font-size: 12px;
    padding: 3px 10px;
    background: #e8f5f6;
    color: #3c9298;
    border-radius: 20px;
}
.abs-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.abs-table th {
    text-align: left;
    padding: 8px 0;
    color: #9ca3af;
    font-weight: 400;
    border-bottom: 0.5px solid #e5e7eb;
}
.abs-table td {
    padding: 10px 0;
    border-bottom: 0.5px solid #f3f4f6;
}
.abs-table tr:last-child td {
    border-bottom: none;
}
.muted {
    color: #6b7280;
}
.etudiant-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}
.mini-avatar {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #e8f5f6;
    color: #3c9298;
    font-size: 10px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
}
.badge-filiere {
    font-size: 11px;
    padding: 3px 8px;
    background: #e6f1fb;
    color: #0c447c;
    border-radius: 20px;
}
.badge-statut {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
}
.badge-statut.justifiee {
    background: #e1f5ee;
    color: #085041;
}
.badge-statut.non-justifiee {
    background: #fcebeb;
    color: #a32d2d;
}
.badge-statut.en-attente {
    background: #faeeda;
    color: #633806;
}
.statut-select {
    padding: 4px 8px;
    border: 0.5px solid #e5e7eb;
    border-radius: 6px;
    font-size: 11px;
    color: #6b7280;
    background: white;
    outline: none;
    cursor: pointer;
}
.statut-select:focus {
    border-color: #3c9298;
}
</style>
