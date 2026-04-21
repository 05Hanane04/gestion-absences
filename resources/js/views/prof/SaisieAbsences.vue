<template>
    <div>
        <div class="step-card" v-if="etape === 1">
            <p class="card-title">
                <span class="step-badge">1</span>
                Choisir le module et la séance
            </p>

            <div class="form-row">
                <div class="form-group">
                    <label>Module</label>
                    <select
                        v-model="moduleSelectionne"
                        @change="chargerSeances"
                    >
                        <option value="">Sélectionner un module...</option>
                        <option v-for="m in modules" :key="m.id" :value="m.id">
                            {{ m.nom }} — {{ m.filiere?.code }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Séance existante</label>
                    <select
                        v-model="seanceSelectionnee"
                        :disabled="!moduleSelectionne"
                    >
                        <option value="">Sélectionner une séance...</option>
                        <option
                            v-for="s in seancesFiltrees"
                            :key="s.id"
                            :value="s.id"
                        >
                            {{ formatDate(s.date) }} —
                            {{ formatHeure(s.heure_debut) }} à
                            {{ formatHeure(s.heure_fin) }} — {{ s.salle }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-row" v-if="moduleSelectionne">
                <div class="form-group">
                    <label>Filière</label>
                    <select
                        v-model="filtreFiliereId"
                        @change="filtrerEtudiants"
                    >
                        <option value="">Toutes les filières</option>
                        <option v-for="f in filieres" :key="f.id" :value="f.id">
                            {{ f.code }} — {{ f.nom }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Niveau</label>
                    <select v-model="filtreNiveau" @change="filtrerEtudiants">
                        <option value="">Tous les niveaux</option>
                        <option value="Bac+3">2ème année</option>
                        <option value="Bac+4">3ème année</option>
                        <option value="Bac+5">5ème année</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Groupe</label>
                    <select v-model="filtreGroupe" @change="filtrerEtudiants">
                        <option value="">Tous les groupes</option>
                        <option value="G1">G1</option>
                        <option value="G2">G2</option>
                        <option value="G3">G3</option>
                        <option value="G4">G4</option>
                    </select>
                </div>
            </div>
            <div class="nouvelle-seance-section">
                <p class="ou-label">— Ou créer une nouvelle séance —</p>
                <div class="form-row">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" v-model="nouvelleSeance.date" />
                    </div>
                    <div class="form-group">
                        <label>Heure début</label>
                        <input
                            type="time"
                            v-model="nouvelleSeance.heure_debut"
                        />
                    </div>
                    <div class="form-group">
                        <label>Heure fin</label>
                        <input type="time" v-model="nouvelleSeance.heure_fin" />
                    </div>
                    <div class="form-group">
                        <label>Salle</label>
                        <input
                            type="text"
                            v-model="nouvelleSeance.salle"
                            placeholder="Ex: Salle A1"
                        />
                    </div>
                </div>
                <button
                    class="btn-creer"
                    @click="creerSeance"
                    :disabled="!moduleSelectionne || !nouvelleSeance.date"
                >
                    <i class="bi bi-plus-circle"></i> Créer cette séance
                </button>
            </div>

            <div class="step-footer">
                <button
                    class="btn-suivant"
                    @click="etape = 2"
                    :disabled="!seanceSelectionnee"
                >
                    Suivant — Choisir les absents
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        <div class="step-card" v-if="etape === 2">
            <div class="step-header">
                <button class="btn-retour" @click="etape = 1">
                    <i class="bi bi-arrow-left"></i> Retour
                </button>
                <p class="card-title">
                    <span class="step-badge">2</span>
                    Sélectionner les étudiants absents
                </p>
            </div>

            <div class="seance-info">
                <i class="bi bi-calendar-event"></i>
                {{ seanceInfoLabel }}
            </div>
            <div
                class="filtres-actifs"
                v-if="filtreFiliereId || filtreGroupe || filtreNiveau"
            >
                <span class="filtre-badge" v-if="filtreFiliereId">
                    <i class="bi bi-funnel"></i>
                    {{ filieres.find((f) => f.id == filtreFiliereId)?.code }}
                </span>
                <span class="filtre-badge" v-if="filtreNiveau">{{
                    filtreNiveau
                }}</span>
                <span class="filtre-badge" v-if="filtreGroupe">{{
                    filtreGroupe
                }}</span>
                <span class="filtre-count"
                    >{{ etudiantsFiltres.length }} étudiant(s)</span
                >
            </div>
            <div class="selection-rapide">
                <button class="btn-select-all" @click="toutSelectionner">
                    Tout cocher
                </button>
                <button class="btn-deselect-all" @click="toutDeselectionner">
                    Tout décocher
                </button>
                <span class="count-label"
                    >{{ absentsSelectionnes.length }} absent(s)
                    sélectionné(s)</span
                >
            </div>

            <div
                v-if="loadingEtudiants"
                style="text-align: center; padding: 20px; color: #9ca3af"
            >
                Chargement des étudiants...
            </div>
            <div v-else class="etudiants-list">
                <div
                    v-for="e in etudiantsFiltres"
                    :key="e.id"
                    class="etudiant-row"
                    :class="{ absent: absentsSelectionnes.includes(e.id) }"
                    @click="toggleAbsent(e.id)"
                >
                    <div class="etudiant-left">
                        <div
                            class="checkbox"
                            :class="{
                                checked: absentsSelectionnes.includes(e.id),
                            }"
                        >
                            <i
                                class="bi bi-check"
                                v-if="absentsSelectionnes.includes(e.id)"
                            ></i>
                        </div>
                        <div class="mini-avatar">{{ e.initials }}</div>
                        <div>
                            <p class="etudiant-nom">{{ e.nom }}</p>
                            <p class="etudiant-info">
                                {{ e.cne }} — {{ e.filiere }} — {{ e.groupe }}
                            </p>
                        </div>
                    </div>
                    <span
                        v-if="absentsSelectionnes.includes(e.id)"
                        class="badge-absent"
                        >Absent</span
                    >
                </div>
                <div
                    v-if="etudiantsFiltres.length === 0"
                    style="text-align: center; padding: 20px; color: #9ca3af"
                >
                    Aucun étudiant trouvé avec ces filtres
                </div>
            </div>

            <div class="step-footer">
                <button
                    class="btn-enregistrer"
                    @click="enregistrerAbsences"
                    :disabled="saving"
                >
                    <span v-if="saving"
                        ><i class="bi bi-arrow-repeat"></i>
                        Enregistrement...</span
                    >
                    <span v-else
                        ><i class="bi bi-check-circle"></i> Enregistrer
                        {{ absentsSelectionnes.length }} absence(s)</span
                    >
                </button>
            </div>
        </div>

        <!-- Étape 3 : Confirmation -->
        <div class="step-card success-card" v-if="etape === 3">
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h2 class="success-title">Absences enregistrées !</h2>
            <p class="success-sub">
                {{ dernierEnregistrement }} absence(s) ont été enregistrées avec
                succès.
            </p>
            <div class="success-actions">
                <button class="btn-nouvelle-saisie" @click="reinitialiser">
                    <i class="bi bi-plus-circle"></i> Nouvelle saisie
                </button>
                <button
                    class="btn-voir-historique"
                    @click="$router.push('/prof/historique')"
                >
                    <i class="bi bi-clock-history"></i> Voir l'historique
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../axios.js";

export default {
    name: "SaisieAbsences",
    data() {
        return {
            etape: 1,
            loadingEtudiants: false,
            saving: false,
            modules: [],
            seances: [],
            etudiants: [],
            filieres: [],
            moduleSelectionne: "",
            seanceSelectionnee: "",
            absentsSelectionnes: [],
            dernierEnregistrement: 0,
            professeurId: null,
            // Filtres
            filtreFiliereId: "",
            filtreNiveau: "",
            filtreGroupe: "",
            nouvelleSeance: {
                date: "",
                heure_debut: "",
                heure_fin: "",
                salle: "",
            },
        };
    },
    computed: {
        seancesFiltrees() {
            if (!this.moduleSelectionne) return [];
            return this.seances.filter(
                (s) => s.module_id == this.moduleSelectionne,
            );
        },
        seanceInfoLabel() {
            const s = this.seances.find((s) => s.id == this.seanceSelectionnee);
            if (!s) return "";
            const m = this.modules.find((m) => m.id == this.moduleSelectionne);
            return `${m?.nom} — ${this.formatDate(s.date)} de ${this.formatHeure(s.heure_debut)} à ${this.formatHeure(s.heure_fin)}`;
        },
        etudiantsFiltres() {
            return this.etudiants.filter((e) => {
                const matchFiliere =
                    !this.filtreFiliereId ||
                    e.filiere_id == this.filtreFiliereId;
                const matchGroupe =
                    !this.filtreGroupe || e.groupe === this.filtreGroupe;
                const matchNiveau =
                    !this.filtreNiveau || e.niveau === this.filtreNiveau;
                return matchFiliere && matchGroupe && matchNiveau;
            });
        },
    },
    async mounted() {
        if (this.$route.query.module) {
            this.moduleSelectionne = this.$route.query.module;
        }
        await this.chargerDonnees();
    },
    methods: {
        async chargerDonnees() {
            try {
                const user = JSON.parse(localStorage.getItem("user") || "{}");

                // Trouver le prof
                const resProfs = await axios.get("/professeurs");
                const prof = resProfs.data.find((p) => p.user_id === user.id);
                if (!prof) return;
                this.professeurId = prof.id;

                // Charger les modules de CE prof uniquement
                const resMods = await axios.get(
                    `/modules/professeur/${prof.id}`,
                );
                this.modules = resMods.data;

                // Charger les séances
                const resSeances = await axios.get("/seances");
                this.seances = resSeances.data;

                // Charger les filières
                const resFilieres = await axios.get("/filieres");
                this.filieres = resFilieres.data;

                // Charger tous les étudiants
                await this.chargerEtudiants();
            } catch (err) {
                console.error("Erreur", err);
            }
        },
        async chargerEtudiants() {
            try {
                this.loadingEtudiants = true;
                const res = await axios.get("/etudiants");
                this.etudiants = res.data.map((e) => ({
                    id: e.id,
                    nom: e.user.name,
                    cne: e.cne,
                    groupe: e.groupe,
                    filiere_id: e.filiere_id,
                    filiere: e.filiere?.code,
                    niveau: e.filiere?.niveau,
                    initials: e.user.name
                        .split(" ")
                        .map((n) => n[0])
                        .join("")
                        .toUpperCase()
                        .slice(0, 2),
                }));
            } catch (err) {
                console.error("Erreur étudiants", err);
            } finally {
                this.loadingEtudiants = false;
            }
        },
        filtrerEtudiants() {
            // Les filtres sont réactifs via computed etudiantsFiltres
        },
        async chargerSeances() {
            const res = await axios.get("/seances");
            this.seances = res.data;
            this.seanceSelectionnee = "";
        },
        async creerSeance() {
            if (!this.moduleSelectionne || !this.nouvelleSeance.date) return;
            try {
                const res = await axios.post("/seances", {
                    module_id: this.moduleSelectionne,
                    date: this.nouvelleSeance.date,
                    heure_debut: this.nouvelleSeance.heure_debut || "08:00",
                    heure_fin: this.nouvelleSeance.heure_fin || "10:00",
                    salle: this.nouvelleSeance.salle || "À définir",
                });
                this.seances.push(res.data);
                this.seanceSelectionnee = res.data.id;
                this.nouvelleSeance = {
                    date: "",
                    heure_debut: "",
                    heure_fin: "",
                    salle: "",
                };
                alert("Séance créée avec succès !");
            } catch (err) {
                alert("Erreur lors de la création de la séance");
            }
        },
        toggleAbsent(id) {
            const idx = this.absentsSelectionnes.indexOf(id);
            if (idx === -1) this.absentsSelectionnes.push(id);
            else this.absentsSelectionnes.splice(idx, 1);
        },
        toutSelectionner() {
            this.absentsSelectionnes = this.etudiantsFiltres.map((e) => e.id);
        },
        toutDeselectionner() {
            this.absentsSelectionnes = [];
        },
        async enregistrerAbsences() {
            if (this.absentsSelectionnes.length === 0) {
                alert("Sélectionnez au moins un étudiant absent.");
                return;
            }
            try {
                this.saving = true;
                await Promise.all(
                    this.absentsSelectionnes.map((etudiantId) =>
                        axios.post("/absences", {
                            etudiant_id: etudiantId,
                            seance_id: this.seanceSelectionnee,
                            statut: "non-justifiee",
                        }),
                    ),
                );
                this.dernierEnregistrement = this.absentsSelectionnes.length;
                this.etape = 3;
            } catch (err) {
                alert("Erreur lors de l'enregistrement");
                console.error(err);
            } finally {
                this.saving = false;
            }
        },
        reinitialiser() {
            this.etape = 1;
            this.moduleSelectionne = "";
            this.seanceSelectionnee = "";
            this.absentsSelectionnes = [];
            this.filtreFiliereId = "";
            this.filtreNiveau = "";
            this.filtreGroupe = "";
            this.nouvelleSeance = {
                date: "",
                heure_debut: "",
                heure_fin: "",
                salle: "",
            };
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
.step-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 24px;
    margin-bottom: 16px;
}
.card-title {
    margin: 0 0 20px;
    font-size: 15px;
    font-weight: 500;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 10px;
}
.step-badge {
    width: 24px;
    height: 24px;
    background: #3c9298;
    color: white;
    border-radius: 50%;
    font-size: 12px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.step-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
}
.step-header .card-title {
    margin: 0;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
    color: #111827;
}
.form-group input:focus,
.form-group select:focus {
    border-color: #3c9298;
}
.form-group select:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.nouvelle-seance-section {
    border-top: 0.5px solid #e5e7eb;
    padding-top: 16px;
    margin-top: 8px;
}
.ou-label {
    text-align: center;
    font-size: 12px;
    color: #9ca3af;
    margin: 0 0 14px;
}
.btn-creer {
    background: white;
    color: #3c9298;
    border: 0.5px solid #3c9298;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-creer:hover {
    background: #e8f5f6;
}
.btn-creer:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.seance-info {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #e8f5f6;
    color: #3c9298;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 16px;
}

.filtres-actifs {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.filtre-badge {
    font-size: 11px;
    padding: 3px 10px;
    background: #e8f5f6;
    color: #3c9298;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.filtre-count {
    font-size: 12px;
    color: #9ca3af;
    margin-left: auto;
}

.selection-rapide {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.btn-select-all,
.btn-deselect-all {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    cursor: pointer;
    border: 0.5px solid #e5e7eb;
    background: white;
    color: #6b7280;
}
.btn-select-all:hover {
    background: #e8f5f6;
    color: #3c9298;
    border-color: #3c9298;
}
.btn-deselect-all:hover {
    background: #fcebeb;
    color: #a32d2d;
    border-color: #f09595;
}
.count-label {
    font-size: 12px;
    color: #9ca3af;
    margin-left: auto;
}

.etudiants-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.etudiant-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    border-radius: 8px;
    border: 0.5px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.15s;
}
.etudiant-row:hover {
    background: #f8f9fb;
}
.etudiant-row.absent {
    background: #fcebeb;
    border-color: #f09595;
}
.etudiant-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.checkbox {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    border: 0.5px solid #d1d5db;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    flex-shrink: 0;
    transition: all 0.15s;
}
.checkbox.checked {
    background: #e24b4a;
    border-color: #e24b4a;
    color: white;
}
.mini-avatar {
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
.etudiant-row.absent .mini-avatar {
    background: #fcebeb;
    color: #a32d2d;
}
.etudiant-nom {
    margin: 0;
    font-size: 13px;
    font-weight: 500;
    color: #111827;
}
.etudiant-info {
    margin: 0;
    font-size: 11px;
    color: #9ca3af;
}
.badge-absent {
    font-size: 11px;
    padding: 3px 10px;
    background: #e24b4a;
    color: white;
    border-radius: 20px;
}

.step-footer {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
}
.btn-suivant {
    background: #3c9298;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-suivant:hover {
    background: #2d7a7f;
}
.btn-suivant:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}
.btn-retour {
    background: white;
    color: #6b7280;
    border: 0.5px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-retour:hover {
    background: #f3f4f6;
}
.btn-enregistrer {
    background: #0f6e56;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px 24px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}
.btn-enregistrer:hover {
    background: #085041;
}
.btn-enregistrer:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.success-card {
    text-align: center;
    padding: 48px 24px;
}
.success-icon {
    font-size: 48px;
    color: #0f6e56;
    margin-bottom: 16px;
}
.success-title {
    margin: 0 0 8px;
    font-size: 22px;
    font-weight: 500;
    color: #111827;
}
.success-sub {
    margin: 0 0 28px;
    font-size: 14px;
    color: #6b7280;
}
.success-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}
.btn-nouvelle-saisie {
    background: #3c9298;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-nouvelle-saisie:hover {
    background: #2d7a7f;
}
.btn-voir-historique {
    background: white;
    color: #3c9298;
    border: 0.5px solid #3c9298;
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-voir-historique:hover {
    background: #e8f5f6;
}

@media (max-width: 768px) {
    .step-card {
        padding: 16px;
    }
    .form-row {
        grid-template-columns: 1fr;
    }
    .selection-rapide {
        flex-direction: column;
        align-items: flex-start;
    }
    .count-label {
        margin-left: 0;
    }
    .success-actions {
        flex-direction: column;
    }
    .btn-suivant,
    .btn-enregistrer {
        width: 100%;
        justify-content: center;
    }
}
</style>
