<template>
    <div>
        <!-- Stats rapides -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-label">Total absences</p>
                    <div class="stat-icon" style="background: #e8f5f6">
                        <i class="bi bi-calendar-x" style="color: #3c9298"></i>
                    </div>
                </div>
                <p class="stat-value">{{ totalAbsences }}h</p>
                <p class="stat-trend muted">ce semestre</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-label">Non justifiées</p>
                    <div class="stat-icon" style="background: #fcebeb">
                        <i
                            class="bi bi-exclamation-circle"
                            style="color: #a32d2d"
                        ></i>
                    </div>
                </div>
                <p class="stat-value" style="color: #e24b4a">
                    {{ nonJustifiees }}h
                </p>
                <p class="stat-trend danger" v-if="nonJustifiees >= 4">
                    ⚠ Seuil critique approché
                </p>
                <p class="stat-trend muted" v-else>En règle</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-label">Justifiées</p>
                    <div class="stat-icon" style="background: #e1f5ee">
                        <i
                            class="bi bi-check-circle"
                            style="color: #0f6e56"
                        ></i>
                    </div>
                </div>
                <p class="stat-value" style="color: #0f6e56">
                    {{ justifiees }}h
                </p>
                <p class="stat-trend muted">acceptées</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-label">En attente</p>
                    <div class="stat-icon" style="background: #faeeda">
                        <i class="bi bi-clock" style="color: #854f0b"></i>
                    </div>
                </div>
                <p class="stat-value" style="color: #854f0b">
                    {{ enAttente }}h
                </p>
                <p class="stat-trend muted">à traiter</p>
            </div>
        </div>

        <!-- Filtres + tableau -->
        <div class="table-card">
            <div class="table-top">
                <p class="card-title">Historique des absences</p>
                <div class="filters">
                    <select v-model="filtreStatut" class="filter-select">
                        <option value="">Tous les statuts</option>
                        <option value="justifiee">Justifiée</option>
                        <option value="non-justifiee">Non justifiée</option>
                        <option value="en-attente">En attente</option>
                    </select>
                    <select v-model="filtreModule" class="filter-select">
                        <option value="">Tous les modules</option>
                        <option v-for="m in modules" :key="m" :value="m">
                            {{ m }}
                        </option>
                    </select>
                </div>
            </div>

            <table class="abs-table">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Professeur</th>
                        <th>Date</th>
                        <th>Séance</th>
                        <th>Durée</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="abs in filtered" :key="abs.id">
                        <td class="bold">{{ abs.module }}</td>
                        <td class="muted">{{ abs.prof }}</td>
                        <td class="muted">{{ abs.date }}</td>
                        <td class="muted">{{ abs.seance }}</td>
                        <td class="muted">{{ abs.duree }}h</td>
                        <td>
                            <span class="badge-statut" :class="abs.statut">
                                {{ abs.statutLabel }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="filtered.length === 0">
                        <td
                            colspan="6"
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
export default {
    name: "MesAbsences",
    data() {
        return {
            filtreStatut: "",
            filtreModule: "",
            absences: [
                {
                    id: 1,
                    module: "PHP / Laravel",
                    prof: "Ali Benali",
                    date: "26/03/2026",
                    seance: "08h - 10h",
                    duree: 2,
                    statut: "non-justifiee",
                    statutLabel: "Non justifiée",
                },
                {
                    id: 2,
                    module: "Réseaux Informatiques",
                    prof: "Sara Idrissi",
                    date: "24/03/2026",
                    seance: "10h - 12h",
                    duree: 2,
                    statut: "justifiee",
                    statutLabel: "Justifiée",
                },
                {
                    id: 3,
                    module: "SQL Server",
                    prof: "Youssef Amrani",
                    date: "20/03/2026",
                    seance: "14h - 16h",
                    duree: 2,
                    statut: "en-attente",
                    statutLabel: "En attente",
                },
                {
                    id: 4,
                    module: "Programmation JavaScript",
                    prof: "Hind Tazi",
                    date: "18/03/2026",
                    seance: "08h - 10h",
                    duree: 2,
                    statut: "non-justifiee",
                    statutLabel: "Non justifiée",
                },
                {
                    id: 5,
                    module: "Base de Données",
                    prof: "Omar Cherkaoui",
                    date: "15/03/2026",
                    seance: "16h - 18h",
                    duree: 2,
                    statut: "justifiee",
                    statutLabel: "Justifiée",
                },
                {
                    id: 6,
                    module: "Conception Orientée Objet",
                    prof: "Younes Bennani",
                    date: "12/03/2026",
                    seance: "08h - 10h",
                    duree: 2,
                    statut: "non-justifiee",
                    statutLabel: "Non justifiée",
                },
            ],
        };
    },
    computed: {
        modules() {
            return [...new Set(this.absences.map((a) => a.module))];
        },
        filtered() {
            return this.absences.filter((a) => {
                const matchStatut =
                    !this.filtreStatut || a.statut === this.filtreStatut;
                const matchModule =
                    !this.filtreModule || a.module === this.filtreModule;
                return matchStatut && matchModule;
            });
        },
        totalAbsences() {
            return this.absences.reduce((s, a) => s + a.duree, 0);
        },
        nonJustifiees() {
            return this.absences
                .filter((a) => a.statut === "non-justifiee")
                .reduce((s, a) => s + a.duree, 0);
        },
        justifiees() {
            return this.absences
                .filter((a) => a.statut === "justifiee")
                .reduce((s, a) => s + a.duree, 0);
        },
        enAttente() {
            return this.absences
                .filter((a) => a.statut === "en-attente")
                .reduce((s, a) => s + a.duree, 0);
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
.stat-trend.danger {
    color: #e24b4a;
}

.table-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 18px;
}
.table-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    flex-wrap: wrap;
    gap: 10px;
}
.card-title {
    margin: 0;
    font-size: 14px;
    font-weight: 500;
    color: #111827;
}
.filters {
    display: flex;
    gap: 8px;
}
.filter-select {
    padding: 6px 10px;
    border: 0.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 12px;
    color: #6b7280;
    background: white;
    outline: none;
    cursor: pointer;
}
.filter-select:focus {
    border-color: #3c9298;
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
.bold {
    font-weight: 500;
    color: #111827;
}
.muted {
    color: #6b7280;
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
