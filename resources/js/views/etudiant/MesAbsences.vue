<template>
    <div>
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
                <p class="stat-trend danger" v-if="nonJustifiees >= 8">
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

            <div
                v-if="loading"
                style="text-align: center; padding: 30px; color: #9ca3af"
            >
                <i class="bi bi-arrow-repeat"></i> Chargement...
            </div>

            <div v-else class="table-wrapper">
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
                            <td class="bold">{{ abs.seance?.module?.nom }}</td>
                            <td class="muted">
                                {{ abs.seance?.module?.professeur?.user?.name }}
                            </td>
                            <td class="muted">
                                {{ formatDate(abs.seance?.date) }}
                            </td>
                            <td class="muted">
                                {{ formatHeure(abs.seance?.heure_debut) }} -
                                {{ formatHeure(abs.seance?.heure_fin) }}
                            </td>
                            <td class="muted">2h</td>
                            <td>
                                <span class="badge-statut" :class="abs.statut">
                                    {{ statutLabel(abs.statut) }}
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
    </div>
</template>

<script>
import axios from "../../axios.js";

export default {
    name: "MesAbsences",
    data() {
        return {
            loading: true,
            filtreStatut: "",
            filtreModule: "",
            absences: [],
            etudiantId: null,
        };
    },
    computed: {
        user() {
            return JSON.parse(localStorage.getItem("user") || "{}");
        },
        modules() {
            return [
                ...new Set(
                    this.absences
                        .map((a) => a.seance?.module?.nom)
                        .filter(Boolean),
                ),
            ];
        },
        filtered() {
            return this.absences.filter((a) => {
                const matchStatut =
                    !this.filtreStatut || a.statut === this.filtreStatut;
                const matchModule =
                    !this.filtreModule ||
                    a.seance?.module?.nom === this.filtreModule;
                return matchStatut && matchModule;
            });
        },
        totalAbsences() {
            return this.absences.length * 2;
        },
        nonJustifiees() {
            return (
                this.absences.filter((a) => a.statut === "non-justifiee")
                    .length * 2
            );
        },
        justifiees() {
            return (
                this.absences.filter((a) => a.statut === "justifiee").length * 2
            );
        },
        enAttente() {
            return (
                this.absences.filter((a) => a.statut === "en-attente").length *
                2
            );
        },
    },
    async mounted() {
        await this.chargerAbsences();
    },
    methods: {
        async chargerAbsences() {
            try {
                this.loading = true;
                const resEtudiants = await axios.get("/etudiants");
                const etudiant = resEtudiants.data.find(
                    (e) => e.user_id === this.user.id,
                );
                if (etudiant) {
                    this.etudiantId = etudiant.id;
                    const res = await axios.get(
                        `/absences/etudiant/${etudiant.id}`,
                    );
                    this.absences = res.data;
                }
            } catch (err) {
                console.error("Erreur chargement absences", err);
            } finally {
                this.loading = false;
            }
        },
        formatDate(date) {
            if (!date) return "";
            return new Date(date).toLocaleDateString("fr-FR", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
            });
        },
        formatHeure(heure) {
            if (!heure) return "";
            return heure.substring(0, 5).replace(":", "h");
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

.table-wrapper {
    overflow-x: auto;
}
.abs-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    min-width: 500px;
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

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .stat-value {
        font-size: 20px;
    }
    .table-top {
        flex-direction: column;
        align-items: flex-start;
    }
    .filters {
        width: 100%;
        flex-direction: column;
    }
    .filter-select {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr 1fr;
    }
    .stat-label {
        font-size: 11px;
    }
}
</style>
