<template>
    <div>
        <div class="filters-card">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Rechercher un étudiant..."
                />
            </div>
            <div class="filter-group">
                <select v-model="filtreStatut" class="filter-select">
                    <option value="">Tous les statuts</option>
                    <option value="non-justifiee">Non justifiée</option>
                    <option value="justifiee">Justifiée</option>
                    <option value="en-attente">En attente</option>
                </select>
            </div>
            <div class="filter-group">
                <select v-model="filtreModule" class="filter-select">
                    <option value="">Tous les modules</option>
                    <option v-for="m in modules" :key="m" :value="m">
                        {{ m }}
                    </option>
                </select>
            </div>
            <span class="total-badge">{{ filtered.length }} absence(s)</span>
        </div>
        <div class="table-card">
            <div class="table-header">
                <p class="card-title">Toutes les absences</p>
                <button class="btn-add" @click="showModal = true">
                    <i class="bi bi-plus"></i> Ajouter absence
                </button>
            </div>

            <div
                v-if="loading"
                style="text-align: center; padding: 30px; color: #9ca3af"
            >
                <i class="bi bi-arrow-repeat"></i> Chargement...
            </div>

            <table v-else class="data-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Module</th>
                        <th>Professeur</th>
                        <th>Date</th>
                        <th>Séance</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="abs in filtered" :key="abs.id">
                        <td>
                            <div class="etudiant-cell">
                                <div class="mini-avatar">
                                    {{ initials(abs.etudiant?.user?.name) }}
                                </div>
                                <div>
                                    <p class="nom">
                                        {{ abs.etudiant?.user?.name }}
                                    </p>
                                    <p class="email">
                                        {{ abs.etudiant?.filiere?.code }} —
                                        {{ abs.etudiant?.groupe }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="muted">{{ abs.seance?.module?.nom }}</td>
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
                        <td>
                            <div class="actions">
                                <button
                                    class="btn-icon danger"
                                    title="Supprimer"
                                    @click="supprimerAbsence(abs.id)"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filtered.length === 0">
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
        <div
            v-if="showModal"
            class="modal-overlay"
            @click.self="showModal = false"
        >
            <div class="modal-box">
                <div class="modal-header">
                    <p class="modal-title">Ajouter une absence</p>
                    <button class="btn-close-modal" @click="showModal = false">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Étudiant</label>
                        <select v-model="newAbsence.etudiant_id">
                            <option value="">Choisir un étudiant...</option>
                            <option
                                v-for="e in etudiants"
                                :key="e.id"
                                :value="e.id"
                            >
                                {{ e.user.name }} — {{ e.cne }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Séance</label>
                        <select v-model="newAbsence.seance_id">
                            <option value="">Choisir une séance...</option>
                            <option
                                v-for="s in seances"
                                :key="s.id"
                                :value="s.id"
                            >
                                {{ s.module?.nom }} — {{ formatDate(s.date) }}
                                {{ formatHeure(s.heure_debut) }}-{{
                                    formatHeure(s.heure_fin)
                                }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Statut</label>
                        <select v-model="newAbsence.statut">
                            <option value="non-justifiee">Non justifiée</option>
                            <option value="justifiee">Justifiée</option>
                            <option value="en-attente">En attente</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel" @click="showModal = false">
                        Annuler
                    </button>
                    <button class="btn-save" @click="ajouterAbsence">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../axios.js";

export default {
    name: "Absences",
    data() {
        return {
            loading: true,
            search: "",
            filtreStatut: "",
            filtreModule: "",
            absences: [],
            etudiants: [],
            seances: [],
            showModal: false,
            newAbsence: {
                etudiant_id: "",
                seance_id: "",
                statut: "non-justifiee",
            },
        };
    },
    computed: {
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
                const matchSearch =
                    !this.search ||
                    a.etudiant?.user?.name
                        ?.toLowerCase()
                        .includes(this.search.toLowerCase());
                const matchStatut =
                    !this.filtreStatut || a.statut === this.filtreStatut;
                const matchModule =
                    !this.filtreModule ||
                    a.seance?.module?.nom === this.filtreModule;
                return matchSearch && matchStatut && matchModule;
            });
        },
    },
    async mounted() {
        await this.chargerDonnees();
    },
    methods: {
        async chargerDonnees() {
            try {
                this.loading = true;
                const [resAbs, resEts, resSeances] = await Promise.all([
                    axios.get("/absences"),
                    axios.get("/etudiants"),
                    axios.get("/seances"),
                ]);
                this.absences = resAbs.data;
                this.etudiants = resEts.data;
                this.seances = resSeances.data;
            } catch (err) {
                console.error("Erreur", err);
            } finally {
                this.loading = false;
            }
        },
        async changerStatut(absenceId, statut) {
            try {
                await axios.put(`/absences/${absenceId}/statut`, { statut });
                const abs = this.absences.find((a) => a.id === absenceId);
                if (abs) abs.statut = statut;
            } catch (err) {
                alert("Erreur mise à jour statut");
            }
        },
        async supprimerAbsence(id) {
            if (!confirm("Confirmer la suppression ?")) return;
            try {
                await axios.delete(`/absences/${id}`);
                this.absences = this.absences.filter((a) => a.id !== id);
            } catch (err) {
                alert("Erreur suppression");
            }
        },
        async ajouterAbsence() {
            if (!this.newAbsence.etudiant_id || !this.newAbsence.seance_id)
                return;
            try {
                await axios.post("/absences", this.newAbsence);
                await this.chargerDonnees();
                this.newAbsence = {
                    etudiant_id: "",
                    seance_id: "",
                    statut: "non-justifiee",
                };
                this.showModal = false;
            } catch (err) {
                alert(err.response?.data?.message || "Erreur");
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
    },
};
</script>

<style scoped>
.filters-card {
    display: flex;
    align-items: center;
    gap: 12px;
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 14px 18px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.search-box {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 0.5px solid #e5e7eb;
    border-radius: 8px;
    padding: 7px 12px;
    flex: 1;
    max-width: 280px;
    color: #9ca3af;
    font-size: 13px;
}
.search-box input {
    border: none;
    outline: none;
    font-size: 13px;
    color: #111827;
    background: transparent;
    width: 100%;
}
.filter-select {
    padding: 7px 12px;
    border: 0.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    color: #6b7280;
    background: white;
    outline: none;
    cursor: pointer;
}
.filter-select:focus {
    border-color: #3c9298;
}
.total-badge {
    margin-left: auto;
    font-size: 12px;
    padding: 3px 10px;
    background: #e8f5f6;
    color: #3c9298;
    border-radius: 20px;
}
.table-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 18px;
    overflow-x: auto;
}
.table-header {
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
.btn-add {
    background: #3c9298;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-add:hover {
    background: #2d7a7f;
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
    padding: 10px 0;
    border-bottom: 0.5px solid #f3f4f6;
}
.data-table tr:last-child td {
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
    width: 30px;
    height: 30px;
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
.nom {
    margin: 0;
    font-size: 13px;
    font-weight: 500;
    color: #111827;
}
.email {
    margin: 0;
    font-size: 11px;
    color: #9ca3af;
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
.actions {
    display: flex;
    gap: 4px;
}
.btn-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 0.5px solid #e5e7eb;
    background: transparent;
    color: #6b7280;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-icon.danger:hover {
    background: #fcebeb;
    color: #a32d2d;
    border-color: #f09595;
}
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-box {
    background: white;
    border-radius: 14px;
    width: 100%;
    max-width: 460px;
    overflow: hidden;
}
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 20px;
    border-bottom: 0.5px solid #e5e7eb;
}
.modal-title {
    margin: 0;
    font-size: 15px;
    font-weight: 500;
}
.btn-close-modal {
    background: none;
    border: none;
    font-size: 18px;
    color: #9ca3af;
    cursor: pointer;
    padding: 0;
}
.modal-body {
    padding: 20px;
}
.modal-footer {
    padding: 14px 20px;
    border-top: 0.5px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 14px;
}
.form-group label {
    font-size: 13px;
    color: #6b7280;
}
.form-group select {
    padding: 8px 12px;
    border: 0.5px solid #d1d5db;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    background: #f9fafb;
    color: #111827;
}
.form-group select:focus {
    border-color: #3c9298;
}
.btn-cancel {
    padding: 8px 16px;
    border-radius: 8px;
    border: 0.5px solid #e5e7eb;
    background: white;
    font-size: 13px;
    cursor: pointer;
    color: #6b7280;
}
.btn-save {
    padding: 8px 20px;
    border-radius: 8px;
    border: none;
    background: #3c9298;
    color: white;
    font-size: 13px;
    cursor: pointer;
}
.btn-save:hover {
    background: #2d7a7f;
}
</style>
