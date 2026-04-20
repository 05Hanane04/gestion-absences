<template>
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">Choisir le module et la séance</h5>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Module</label>
                    <select
                        v-model="form.module_id"
                        class="form-select"
                        @change="onModuleChange"
                    >
                        <option value="">Sélectionner un module...</option>
                        <option
                            v-for="mod in modules"
                            :key="mod.id"
                            :value="mod.id"
                        >
                            {{ mod.nom }} — {{ mod.classe }}
                        </option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Séance existante</label>
                    <select v-model="form.seance_id" class="form-select">
                        <option value="">Sélectionner une séance...</option>
                        <option v-for="s in seances" :key="s.id" :value="s.id">
                            {{ s.date }} | {{ s.heure_debut }}-{{
                                s.heure_fin
                            }}
                            | {{ s.salle }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Filière</label>
                    <select v-model="form.filiere" class="form-select">
                        <option value="">Toutes les filières</option>
                        <option value="IIR">
                            IIR — Ingénierie Informatique et Réseaux
                        </option>
                        <option value="GC">Génie Civil</option>
                        <option value="GF">Génie Financier</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Niveau</label>
                    <select v-model="form.niveau" class="form-select">
                        <option value="">Tous les niveaux</option>
                        <option value="2A">2ème Année</option>
                        <option value="3A">3ème Année</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Groupe</label>
                    <select v-model="form.groupe" class="form-select">
                        <option value="">Tous les groupes</option>
                        <option value="G1">G1</option>
                        <option value="G2">G2</option>
                        <option value="G3">G3</option>
                        <option value="G4">G4</option>
                    </select>
                </div>
            </div>

            <div class="text-center text-muted my-3">
                — Ou créer une nouvelle séance —
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input
                        type="date"
                        v-model="form.date"
                        class="form-control"
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Heure début</label>
                    <input
                        type="time"
                        v-model="form.heure_debut"
                        class="form-control"
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Heure fin</label>
                    <input
                        type="time"
                        v-model="form.heure_fin"
                        class="form-control"
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Salle</label>
                    <input
                        type="text"
                        v-model="form.salle"
                        class="form-control"
                        placeholder="Ex: Salle A1"
                    />
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <button
                    class="btn btn-outline-primary"
                    @click="creerSeance"
                    :disabled="!canCreate"
                >
                    <i class="bi bi-plus-circle me-1"></i> Créer cette séance
                </button>
                <button
                    class="btn btn-primary"
                    @click="suivant"
                    :disabled="!canNext"
                >
                    Suivant — Choisir les absents
                    <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";

const modules = ref([]);
const seances = ref([]);

const form = ref({
    module_id: "",
    seance_id: "",
    filiere: "",
    niveau: "",
    groupe: "",
    date: "",
    heure_debut: "",
    heure_fin: "",
    salle: "",
});

onMounted(async () => {
    const res = await axios.get("/api/prof/modules");
    modules.value = res.data;
});

const onModuleChange = async () => {
    form.value.seance_id = "";
    if (!form.value.module_id) return;
    const res = await axios.get(
        `/api/prof/seances?module_id=${form.value.module_id}`,
    );
    seances.value = res.data;
};

const canCreate = computed(
    () =>
        form.value.module_id &&
        form.value.date &&
        form.value.heure_debut &&
        form.value.heure_fin,
);

const canNext = computed(
    () => form.value.module_id && (form.value.seance_id || canCreate.value),
);

const creerSeance = async () => {
    const res = await axios.post("/api/prof/seances", {
        module_id: form.value.module_id,
        date: form.value.date,
        heure_debut: form.value.heure_debut,
        heure_fin: form.value.heure_fin,
        salle: form.value.salle,
        filiere: form.value.filiere,
        niveau: form.value.niveau,
        groupe: form.value.groupe,
    });
    form.value.seance_id = res.data.id;
    seances.value.push(res.data);
};

const suivant = () => {
    router.push({
        name: "saisir.absents",
        query: {
            seance_id: form.value.seance_id,
            filiere: form.value.filiere,
            niveau: form.value.niveau,
            groupe: form.value.groupe,
        },
    });
};
</script>
