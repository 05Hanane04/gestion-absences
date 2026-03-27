<template>
    <div>
        <!-- Actions bar -->
        <div class="actions-bar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Rechercher un étudiant..."
                />
            </div>
            <button class="btn-add" @click="showModal = true">
                <i class="bi bi-plus"></i> Ajouter étudiant
            </button>
        </div>

        <!-- Table -->
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>CNE</th>
                        <th>Filière</th>
                        <th>Groupe</th>
                        <th>Absences</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="e in filtered" :key="e.id">
                        <td>
                            <div class="etudiant-cell">
                                <div class="avatar">{{ e.initials }}</div>
                                <div>
                                    <p class="nom">{{ e.nom }}</p>
                                    <p class="email">{{ e.email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="muted">{{ e.cne }}</td>
                        <td>
                            <span class="badge-filiere">{{ e.filiere }}</span>
                        </td>
                        <td class="muted">{{ e.groupe }}</td>
                        <td>
                            <span
                                :class="
                                    e.absences > 5 ? 'badge-danger' : 'badge-ok'
                                "
                            >
                                {{ e.absences }}h
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn-icon" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button
                                    class="btn-icon danger"
                                    title="Supprimer"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal ajout -->
        <div
            v-if="showModal"
            class="modal-overlay"
            @click.self="showModal = false"
        >
            <div class="modal-box">
                <div class="modal-header">
                    <p class="modal-title">Ajouter un étudiant</p>
                    <button class="btn-close-modal" @click="showModal = false">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom complet</label>
                            <input
                                v-model="newEtudiant.nom"
                                type="text"
                                placeholder="Ex: Ali Benali"
                            />
                        </div>
                        <div class="form-group">
                            <label>CNE</label>
                            <input
                                v-model="newEtudiant.cne"
                                type="text"
                                placeholder="Ex: R123456789"
                            />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input
                                v-model="newEtudiant.email"
                                type="email"
                                placeholder="ali@univ.ma"
                            />
                        </div>
                        <div class="form-group">
                            <label>Filière</label>
                            <select v-model="newEtudiant.filiere">
                                <option value="">Choisir...</option>
                                <option>3IIR</option>
                                <option>2GC</option>
                                <option>1CP</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Groupe</label>
                        <select v-model="newEtudiant.groupe">
                            <option value="">Choisir...</option>
                            <option>G1</option>
                            <option>G2</option>
                            <option>G3</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel" @click="showModal = false">
                        Annuler
                    </button>
                    <button class="btn-save" @click="ajouterEtudiant">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Etudiants",
    data() {
        return {
            search: "",
            showModal: false,
            newEtudiant: {
                nom: "",
                cne: "",
                email: "",
                filiere: "",
                groupe: "",
            },
            etudiants: [
                {
                    id: 1,
                    nom: "Ali Benali",
                    initials: "AB",
                    email: "ali@univ.ma",
                    cne: "R110234567",
                    filiere: "3IIR",
                    groupe: "G1",
                    absences: 8,
                },
                {
                    id: 2,
                    nom: "Sara Idrissi",
                    initials: "SI",
                    email: "sara@univ.ma",
                    cne: "R110234568",
                    filiere: "3IIR",
                    groupe: "G2",
                    absences: 2,
                },
                {
                    id: 3,
                    nom: "Youssef Amrani",
                    initials: "YA",
                    email: "youssef@univ.ma",
                    cne: "R110234569",
                    filiere: "2GC",
                    groupe: "G1",
                    absences: 5,
                },
                {
                    id: 4,
                    nom: "Hind Tazi",
                    initials: "HT",
                    email: "hind@univ.ma",
                    cne: "R110234570",
                    filiere: "1CP",
                    groupe: "G3",
                    absences: 0,
                },
                {
                    id: 5,
                    nom: "Omar Cherkaoui",
                    initials: "OC",
                    email: "omar@univ.ma",
                    cne: "R110234571",
                    filiere: "3IIR",
                    groupe: "G1",
                    absences: 12,
                },
            ],
        };
    },
    computed: {
        filtered() {
            return this.etudiants.filter(
                (e) =>
                    e.nom.toLowerCase().includes(this.search.toLowerCase()) ||
                    e.cne.toLowerCase().includes(this.search.toLowerCase()) ||
                    e.filiere.toLowerCase().includes(this.search.toLowerCase()),
            );
        },
    },
    methods: {
        ajouterEtudiant() {
            if (!this.newEtudiant.nom || !this.newEtudiant.cne) return;
            this.etudiants.push({
                id: Date.now(),
                initials: this.newEtudiant.nom
                    .split(" ")
                    .map((n) => n[0])
                    .join("")
                    .toUpperCase(),
                absences: 0,
                ...this.newEtudiant,
            });
            this.newEtudiant = {
                nom: "",
                cne: "",
                email: "",
                filiere: "",
                groupe: "",
            };
            this.showModal = false;
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
    background: #3c9298;
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
    background: #e1f5ee;
    color: #3c9298;
    font-size: 11px;
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

.badge-filiere {
    font-size: 11px;
    padding: 3px 10px;
    background: #e6f1fb;
    color: #0c447c;
    border-radius: 20px;
}
.badge-ok {
    font-size: 12px;
    color: #3c9298;
    font-weight: 500;
}
.badge-danger {
    font-size: 12px;
    color: #e24b4a;
    font-weight: 500;
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
    padding: 0;
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
    margin-bottom: 14px;
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
    background: #3c9298;
}
</style>
