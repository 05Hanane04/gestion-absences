<template>
    <div>
        <div class="table-card">
            <div class="table-top">
                <p class="card-title">Historique des absences saisies</p>
                <select v-model="filtreModule" class="filter-select">
                    <option value="">Tous les modules</option>
                    <option v-for="m in modules" :key="m.id" :value="m.id">
                        {{ m.nom }}
                    </option>
                </select>
            </div>

            <div
                v-if="loading"
                style="text-align: center; padding: 30px; color: #9ca3af"
            >
                Chargement...
            </div>

            <table v-else class="abs-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Module</th>
                        <th>Date</th>
                        <th>Séance</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="abs in filtered" :key="abs.id">
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
                            {{ formatDate(abs.seance?.date) }}
                        </td>
                        <td class="muted">
                            {{ formatHeure(abs.seance?.heure_debut) }} -
                            {{ formatHeure(abs.seance?.heure_fin) }}
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
                    <tr v-if="filtered.length === 0">
                        <td
                            colspan="6"
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
import axios from "../../axios.js";

export default {
    name: "Historique",
    data() {
        return {
            loading: true,
            absences: [],
            modules: [],
            filtreModule: "",
            professeurId: null,
        };
    },
    computed: {
        filtered() {
            if (!this.filtreModule) return this.absences;
            return this.absences.filter(
                (a) => a.seance?.module?.id == this.filtreModule,
            );
        },
    },
    async mounted() {
        await this.chargerDonnees();
    },
    methods: {
        async chargerDonnees() {
            try {
                this.loading = true;
                const user = JSON.parse(localStorage.getItem("user") || "{}");

                const resProfs = await axios.get("/professeurs");
                const prof = resProfs.data.find((p) => p.user_id === user.id);
                if (!prof) return;
                this.professeurId = prof.id;

                const resMods = await axios.get("/modules");
                this.modules = resMods.data.filter(
                    (m) => m.professeur_id === prof.id,
                );

                const resAbs = await axios.get("/absences");
                const modIds = this.modules.map((m) => m.id);
                this.absences = resAbs.data.filter((a) =>
                    modIds.includes(a.seance?.module?.id),
                );
            } catch (err) {
                console.error("Erreur", err);
            } finally {
                this.loading = false;
            }
        },
        async changerStatut(absenceId, nouveauStatut) {
            try {
                await axios.put(`/absences/${absenceId}/statut`, {
                    statut: nouveauStatut,
                });
                const abs = this.absences.find((a) => a.id === absenceId);
                if (abs) abs.statut = nouveauStatut;
            } catch (err) {
                alert("Erreur lors de la mise à jour du statut");
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
}
.card-title {
    margin: 0;
    font-size: 14px;
    font-weight: 500;
    color: #111827;
}
.filter-select {
    padding: 6px 10px;
    border: 0.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 12px;
    color: #6b7280;
    background: white;
    outline: none;
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
    flex-shrink: 0;
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
