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
                <p class="card-title">Absences par semaine</p>
                <canvas id="barChart" height="100"></canvas>
            </div>
            <div class="chart-card">
                <p class="card-title">Par filière</p>
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
            <table class="abs-table">
                <thead>
                    <tr>
                        <th>Étudiant(e)</th>
                        <th>Module</th>
                        <th>Professeur</th>
                        <th>Date</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="abs in recentAbsences" :key="abs.id">
                        <td>
                            <div class="etudiant-cell">
                                <div class="mini-avatar">
                                    {{ abs.initials }}
                                </div>
                                {{ abs.nom }}
                            </div>
                        </td>
                        <td class="muted">{{ abs.module }}</td>
                        <td class="muted">{{ abs.nom }}</td>
                        <td class="muted">{{ abs.date }}</td>
                        <td>
                            <span class="badge-statut" :class="abs.statut">
                                {{ abs.statutLabel }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { Chart, registerables } from "chart.js";
Chart.register(...registerables);

export default {
    name: "DashboardAdmin",
    data() {
        return {
            stats: [
                {
                    label: "Total étudiants",
                    value: "342",
                    trend: "+12 ce mois",
                    trendColor: "#3c9298",
                    icon: "bi bi-people",
                    iconBg: "#E1F5EE",
                    iconColor: "#3c9298",
                },
                {
                    label: "Absences aujourd'hui",
                    value: "28",
                    trend: "+5 vs hier",
                    trendColor: "#E24B4A",
                    icon: "bi bi-exclamation-circle",
                    iconBg: "#FCEBEB",
                    iconColor: "#A32D2D",
                },
                {
                    label: "Professeurs actifs",
                    value: "24",
                    trend: "stable",
                    trendColor: "#9ca3af",
                    icon: "bi bi-person-badge",
                    iconBg: "#E6F1FB",
                    iconColor: "#185FA5",
                },
                {
                    label: "Taux d'absence",
                    value: "8.2%",
                    trend: "+1.2% ce mois",
                    trendColor: "#E24B4A",
                    icon: "bi bi-bar-chart-line",
                    iconBg: "#FAEEDA",
                    iconColor: "#854F0B",
                },
            ],
            recentAbsences: [
                {
                    id: 1,
                    nom: "Ali Benali",
                    initials: "AB",
                    module: "PHP / Laravel",
                    date: "26/03/2026",
                    statut: "non-justifiee",
                    statutLabel: "Non justifiée",
                },
                {
                    id: 2,
                    nom: "Sara Idrissi",
                    initials: "SI",
                    module: "Réseaux Informatiques",
                    date: "26/03/2026",
                    statut: "justifiee",
                    statutLabel: "Justifiée",
                },
                {
                    id: 3,
                    nom: "Youssef Amrani",
                    initials: "YA",
                    module: "SQL Server",
                    date: "25/03/2026",
                    statut: "en-attente",
                    statutLabel: "En attente",
                },
                {
                    id: 4,
                    nom: "Hind Tazi",
                    initials: "HT",
                    module: "Programmation JavaScript",
                    date: "25/03/2026",
                    statut: "non-justifiee",
                    statutLabel: "Non justifiée",
                },
                {
                    id: 5,
                    nom: "Omar Cherkaoui",
                    initials: "OC",
                    module: "Base de données",
                    date: "24/03/2026",
                    statut: "justifiee",
                    statutLabel: "Justifiée",
                },
            ],
        };
    },
    mounted() {
        this.initBarChart();
        this.initDoughnutChart();
    },
    methods: {
        initBarChart() {
            new Chart(document.getElementById("barChart"), {
                type: "bar",
                data: {
                    labels: ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi"],
                    datasets: [
                        {
                            label: "Absences",
                            data: [18, 32, 15, 28, 12],
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
            new Chart(document.getElementById("doughnutChart"), {
                type: "doughnut",
                data: {
                    labels: ["3IIR", "2GC", "1CP", "autres"],
                    datasets: [
                        {
                            data: [42, 28, 20, 10],
                            backgroundColor: [
                                "#3c9298",
                                "#185FA5",
                                "#854F0B",
                                "#e5e7eb",
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
    color: #111827;
}
.abs-table tr:last-child td {
    border-bottom: none;
}
.muted {
    color: #6b7280 !important;
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
    background: #e1f5ee;
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
    font-weight: 400;
}
.badge-statut.justifiee {
    background: #e1f5ee;
    color: #3c9298;
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
