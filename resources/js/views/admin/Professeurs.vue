<template>
    <div>
        <div class="actions-bar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Rechercher un professeur..."
                />
            </div>
            <button class="btn-add" @click="showModal = true">
                <i class="bi bi-plus"></i> Ajouter professeur
            </button>
        </div>

        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Professeur</th>
                        <th>Spécialité</th>
                        <th>Grade</th>
                        <th>Modules</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in filtered" :key="p.id">
                        <td>
                            <div class="etudiant-cell">
                                <div class="avatar blue">{{ p.initials }}</div>
                                <div>
                                    <p class="nom">{{ p.nom }}</p>
                                    <p class="email">{{ p.email }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-spec">{{ p.specialite }}</span>
                        </td>
                        <td class="muted">{{ p.grade }}</td>
                        <td class="muted">{{ p.modules.join(", ") || "—" }}</td>
                        <td>
                            <div class="actions">
                                <button
                                    class="btn-icon"
                                    @click="ouvrirVoir(p)"
                                    title="Voir"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button
                                    class="btn-icon"
                                    @click="ouvrirModifier(p)"
                                    title="Modifier"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button
                                    class="btn-icon danger"
                                    @click="supprimerProf(p.id)"
                                    title="Supprimer"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filtered.length === 0">
                        <td
                            colspan="5"
                            style="
                                text-align: center;
                                padding: 20px;
                                color: #9ca3af;
                            "
                        >
                            Aucun professeur trouvé
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Ajout -->
        <div
            v-if="showModal"
            class="modal-overlay"
            @click.self="showModal = false"
        >
            <div class="modal-box">
                <div class="modal-header">
                    <p class="modal-title">Ajouter un professeur</p>
                    <button class="btn-close-modal" @click="showModal = false">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom complet</label>
                            <input
                                v-model="newProf.nom"
                                type="text"
                                placeholder="Dr. Nom Prénom"
                            />
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input
                                v-model="newProf.email"
                                type="email"
                                placeholder="prof@univ.ma"
                            />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Spécialité</label>
                            <input
                                v-model="newProf.specialite"
                                type="text"
                                placeholder="Ex: Informatique"
                            />
                        </div>
                        <div class="form-group">
                            <label>Grade</label>
                            <select v-model="newProf.grade">
                                <option>Professeur</option>
                                <option>Professeur Assistant</option>
                                <option>Maître de conférences</option>
                                <option>Professeur Habilité</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel" @click="showModal = false">
                        Annuler
                    </button>
                    <button class="btn-save" @click="ajouterProf">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Voir -->
        <div
            v-if="showModalVoir"
            class="modal-overlay"
            @click.self="showModalVoir = false"
        >
            <div class="modal-box">
                <div class="modal-header">
                    <p class="modal-title">Détails du professeur</p>
                    <button
                        class="btn-close-modal"
                        @click="showModalVoir = false"
                    >
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="modal-body" v-if="profSelectionne">
                    <div class="profil-voir">
                        <div class="voir-avatar">
                            {{ profSelectionne.initials }}
                        </div>
                        <h3 class="voir-nom">{{ profSelectionne.nom }}</h3>
                        <p class="voir-email">{{ profSelectionne.email }}</p>
                    </div>
                    <div class="voir-details">
                        <div class="voir-row">
                            <span class="voir-label"
                                ><i class="bi bi-award"></i> Grade</span
                            >
                            <span class="voir-value">{{
                                profSelectionne.grade
                            }}</span>
                        </div>
                        <div class="voir-row">
                            <span class="voir-label"
                                ><i class="bi bi-book"></i> Spécialité</span
                            >
                            <span class="voir-value">{{
                                profSelectionne.specialite
                            }}</span>
                        </div>
                        <div class="voir-row">
                            <span class="voir-label"
                                ><i class="bi bi-journal-text"></i>
                                Modules</span
                            >
                            <span class="voir-value">{{
                                profSelectionne.modules.join(", ") || "—"
                            }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel" @click="showModalVoir = false">
                        Fermer
                    </button>
                    <button
                        class="btn-save"
                        @click="ouvrirModifier(profSelectionne)"
                    >
                        <i class="bi bi-pencil"></i> Modifier
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Modifier -->
        <div
            v-if="showModalModifier"
            class="modal-overlay"
            @click.self="showModalModifier = false"
        >
            <div class="modal-box">
                <div class="modal-header">
                    <p class="modal-title">Modifier le professeur</p>
                    <button
                        class="btn-close-modal"
                        @click="showModalModifier = false"
                    >
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom complet</label>
                            <input
                                v-model="profAModifier.nom"
                                type="text"
                                placeholder="Dr. Nom Prénom"
                            />
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input
                                v-model="profAModifier.email"
                                type="email"
                                placeholder="prof@univ.ma"
                            />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Spécialité</label>
                            <input
                                v-model="profAModifier.specialite"
                                type="text"
                            />
                        </div>
                        <div class="form-group">
                            <label>Grade</label>
                            <select v-model="profAModifier.grade">
                                <option>Professeur</option>
                                <option>Professeur Assistant</option>
                                <option>Maître de conférences</option>
                                <option>Professeur Habilité</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        class="btn-cancel"
                        @click="showModalModifier = false"
                    >
                        Annuler
                    </button>
                    <button class="btn-save" @click="enregistrerModification">
                        <i class="bi bi-check-circle"></i> Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../axios.js";

export default {
    name: "Professeurs",
    data() {
        return {
            search: "",
            showModal: false,
            showModalVoir: false,
            showModalModifier: false,
            profSelectionne: null,
            profAModifier: {
                id: null,
                nom: "",
                email: "",
                specialite: "",
                grade: "Professeur",
            },
            newProf: {
                nom: "",
                email: "",
                specialite: "",
                grade: "Professeur",
            },
            professeurs: [],
        };
    },
    computed: {
        filtered() {
            return this.professeurs.filter(
                (p) =>
                    p.nom.toLowerCase().includes(this.search.toLowerCase()) ||
                    p.specialite
                        .toLowerCase()
                        .includes(this.search.toLowerCase()),
            );
        },
    },
    async mounted() {
        await this.chargerProfesseurs();
    },
    methods: {
        async chargerProfesseurs() {
            const res = await axios.get("/professeurs");
            this.professeurs = res.data.map((p) => ({
                id: p.id,
                user_id: p.user_id,
                nom: p.user.name,
                email: p.user.email,
                specialite: p.specialite,
                grade: p.grade,
                modules: p.modules?.map((m) => m.nom) || [],
                initials: p.user.name
                    .split(" ")
                    .map((n) => n[0])
                    .join("")
                    .toUpperCase()
                    .slice(0, 2),
            }));
        },
        ouvrirVoir(prof) {
            this.profSelectionne = prof;
            this.showModalVoir = true;
            this.showModalModifier = false;
            this.showModal = false;
        },
        ouvrirModifier(prof) {
            this.profAModifier = {
                id: prof.id,
                nom: prof.nom,
                email: prof.email,
                specialite: prof.specialite,
                grade: prof.grade,
            };
            this.showModalModifier = true;
            this.showModalVoir = false;
            this.showModal = false;
        },
        async enregistrerModification() {
            if (!this.profAModifier.nom) return;
            try {
                await axios.put(
                    `/professeurs/${this.profAModifier.id}`,
                    this.profAModifier,
                );
                await this.chargerProfesseurs();
                this.showModalModifier = false;
            } catch (err) {
                alert(err.response?.data?.message || "Erreur modification");
            }
        },
        async ajouterProf() {
            if (!this.newProf.nom) return;
            try {
                await axios.post("/professeurs", this.newProf);
                await this.chargerProfesseurs();
                this.newProf = {
                    nom: "",
                    email: "",
                    specialite: "",
                    grade: "Professeur",
                };
                this.showModal = false;
            } catch (err) {
                alert(err.response?.data?.message || "Erreur");
            }
        },
        async supprimerProf(id) {
            if (!confirm("Confirmer la suppression ?")) return;
            await axios.delete(`/professeurs/${id}`);
            await this.chargerProfesseurs();
        },
    },
};
</script>

<style scoped>
.actions-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    gap: 12px;
}
.search-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background: white;
    border: 0.5px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px 14px;
    flex: 1;
    max-width: 340px;
    color: #9ca3af;
    font-size: 14px;
}
.search-box input {
    border: none;
    outline: none;
    font-size: 13px;
    color: #111827;
    background: transparent;
    width: 100%;
}
.btn-add {
    background: #3c9298;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 9px 16px;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-add:hover {
    background: #2d7a7f;
}
.table-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 18px;
    overflow-x: auto;
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
.muted {
    color: #6b7280;
}
.etudiant-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}
.avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e8f5f6;
    color: #3c9298;
    font-size: 11px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.avatar.blue {
    background: #e6f1fb;
    color: #0c447c;
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
.badge-spec {
    font-size: 11px;
    padding: 3px 10px;
    background: #eeedfe;
    color: #3c3489;
    border-radius: 20px;
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
.btn-icon:hover {
    background: #f3f4f6;
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
    max-width: 480px;
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
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.form-group label {
    font-size: 13px;
    color: #6b7280;
}
.form-group input,
.form-group select {
    padding: 8px 12px;
    border: 0.5px solid #d1d5db;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    background: #f9fafb;
}
.form-group input:focus,
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
.profil-voir {
    text-align: center;
    padding: 20px 0;
    border-bottom: 0.5px solid #e5e7eb;
    margin-bottom: 16px;
}
.voir-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #e6f1fb;
    color: #0c447c;
    font-size: 18px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
}
.voir-nom {
    margin: 0 0 4px;
    font-size: 16px;
    font-weight: 500;
    color: #111827;
}
.voir-email {
    margin: 0;
    font-size: 12px;
    color: #9ca3af;
}
.voir-details {
    padding: 0 4px;
}
.voir-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 0.5px solid #f3f4f6;
    font-size: 13px;
}
.voir-row:last-child {
    border-bottom: none;
}
.voir-label {
    color: #9ca3af;
    display: flex;
    align-items: center;
    gap: 6px;
}
.voir-value {
    color: #111827;
    font-weight: 500;
    text-align: right;
}
</style>
