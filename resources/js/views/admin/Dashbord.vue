<template>
    <div>
        <!-- Stats cards -->
        <div class="stats-grid">
            <div class="stat-card" v-for="stat in stats" :key="stat.label">
                <div class="stat-header">
                    <p class="stat-label">{{ stat.label }}</p>
                    <div class="stat-icon" :style="{ background: stat.iconBg }">
                        <i
                            :class="stat.icon"
                            :style="{ color: stat.iconColor }"
                        ></i>
                    </div>
                </div>
                <p class="stat-value">{{ stat.value }}</p>
                <p class="stat-trend" :style="{ color: stat.trendColor }">
                    {{ stat.trend }}
                </p>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-card large">
                <p class="card-title">Absences par module</p>
                <canvas id="barChart" height="100"></canvas>
            </div>
            <div class="chart-card">
                <p class="card-title">Par statut</p>
                <canvas id="doughnutChart" height="180"></canvas>
            </div>
        </div>

        <!-- Recent absences -->
        <div class="table-card">
            <div class="table-header">
                <p class="card-title">Absences récentes</p>
                <router-link to="/admin/absences" class="voir-plus"
                    >Voir tout →</router-link
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
                        <th>Module</th>
                        <th>Professeur</th>
                        <th>Date</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="abs in absencesRecentes" :key="abs.id">
                        <td>
                            <div class="etudiant-cell">
                                <div class="mini-avatar">
                                    {{ initials(abs.etudiant?.user?.name) }}
                                </div>
                                {{ abs.etudiant?.user?.name }}
                            </div>
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
                    </tr>
                    <tr v-if="absencesRecentes.length === 0">
                        <td
                            colspan="5"
                            style="
                                text-align: center;
                                padding: 20px;
                                color: #9ca3af;
                            "
                        >
                            Aucune absence enregistrée
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
    name: "DashboardAdmin",
    data() {
        return {
            loading: true,
            absences: [],
            etudiants: [],
            professeurs: [],
            modules: [],
            barChart: null,
            doughnutChart: null,
        };
    },
    computed: {
        stats() {
            const nonJust = this.absences.filter(
                (a) => a.statut === "non-justifiee",
            ).length;
            return [
                {
                    label: "Total étudiants",
                    value: this.etudiants.length,
                    trend: "inscrits",
                    trendColor: "#3c9298",
                    icon: "bi bi-people",
                    iconBg: "#e8f5f6",
                    iconColor: "#3c9298",
                },
                {
                    label: "Absences totales",
                    value: this.absences.length,
                    trend: nonJust + " non justifiées",
                    trendColor: "#E24B4A",
                    icon: "bi bi-exclamation-circle",
                    iconBg: "#FCEBEB",
                    iconColor: "#A32D2D",
                },
                {
                    label: "Professeurs",
                    value: this.professeurs.length,
                    trend: "actifs",
                    trendColor: "#9ca3af",
                    icon: "bi bi-person-badge",
                    iconBg: "#E6F1FB",
                    iconColor: "#185FA5",
                },
                {
                    label: "Modules",
                    value: this.modules.length,
                    trend: "ce semestre",
                    trendColor: "#9ca3af",
                    icon: "bi bi-book",
                    iconBg: "#FAEEDA",
                    iconColor: "#854F0B",
                },
            ];
        },
        absencesRecentes() {
            return this.absences.slice(0, 5);
        },
    },
    async mounted() {
        await this.chargerDonnees();
    },
    methods: {
        async chargerDonnees() {
            try {
                this.loading = true;
                const [resAbs, resEts, resProfs, resMods] = await Promise.all([
                    axios.get("/absences"),
                    axios.get("/etudiants"),
                    axios.get("/professeurs"),
                    axios.get("/modules"),
                ]);
                this.absences = resAbs.data;
                this.etudiants = resEts.data;
                this.professeurs = resProfs.data;
                this.modules = resMods.data;

                this.$nextTick(() => {
                    this.initBarChart();
                    this.initDoughnutChart();
                });
            } catch (err) {
                console.error("Erreur", err);
            } finally {
                this.loading = false;
            }
        },
        initBarChart() {
            if (this.barChart) this.barChart.destroy();

            // Compter absences par module
            const countByModule = {};
            this.absences.forEach((a) => {
                const nom = a.seance?.module?.nom || "Inconnu";
                countByModule[nom] = (countByModule[nom] || 0) + 1;
            });

            this.barChart = new Chart(document.getElementById("barChart"), {
                type: "bar",
                data: {
                    labels: Object.keys(countByModule),
                    datasets: [
                        {
                            label: "Absences",
                            data: Object.values(countByModule),
                            backgroundColor: "#3c9298",
                            borderRadius: 6,
                            borderSkipped: false,
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
            });
        },
        initDoughnutChart() {
            if (this.doughnutChart) this.doughnutChart.destroy();

            const nonJust = this.absences.filter(
                (a) => a.statut === "non-justifiee",
            ).length;
            const just = this.absences.filter(
                (a) => a.statut === "justifiee",
            ).length;
            const attente = this.absences.filter(
                (a) => a.statut === "en-attente",
            ).length;

            this.doughnutChart = new Chart(
                document.getElementById("doughnutChart"),
                {
                    type: "doughnut",
                    data: {
                        labels: ["Non justifiées", "Justifiées", "En attente"],
                        datasets: [
                            {
                                data: [nonJust, just, attente],
                                backgroundColor: [
                                    "#E24B4A",
                                    "#3c9298",
                                    "#EF9F27",
                                ],
                                borderWidth: 0,
                                hoverOffset: 4,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        cutout: "70%",
                        plugins: {
                            legend: {
                                position: "bottom",
                                labels: {
                                    font: { size: 11 },
                                    color: "#6b7280",
                                    padding: 12,
                                },
                            },
                        },
                    },
                },
            );
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
.charts-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
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
    margin: 0 0 16px;
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
.voir-plus {
    font-size: 13px;
    color: #3c9298;
    text-decoration: none;
}
.abs-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.abs-table th {
    text-align: left;
    padding: 6px 0;
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
</style>
