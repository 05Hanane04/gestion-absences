<template>
    <div>
        <div class="week-nav">
            <button class="btn-week" @click="semainePrecedente">
                <i class="bi bi-chevron-left"></i>
            </button>
            <p class="week-label">
                Semaine du {{ debutSemaine }} au {{ finSemaine }}
            </p>
            <button class="btn-week" @click="semaineSuivante">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>

        <div class="planning-card">
            <div class="planning-grid">
                <div v-for="jour in jours" :key="jour.nom" class="jour-col">
                    <div class="jour-header" :class="{ today: jour.isToday }">
                        <p class="jour-nom">{{ jour.nom }}</p>
                        <p class="jour-date">{{ jour.date }}</p>
                    </div>
                    <div class="seances-list">
                        <div
                            v-for="seance in jour.seances"
                            :key="seance.id"
                            class="seance-card"
                            :style="{
                                background: seance.bg,
                                borderLeft: '3px solid ' + seance.color,
                            }"
                        >
                            <p class="seance-module">{{ seance.module }}</p>
                            <p class="seance-heure">{{ seance.heure }}</p>
                            <p class="seance-prof">{{ seance.prof }}</p>
                            <p class="seance-salle">{{ seance.salle }}</p>
                        </div>
                        <div v-if="jour.seances.length === 0" class="no-seance">
                            Pas de cours
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Planning",
    data() {
        return {
            offsetSemaine: 0,
            planning: [
                {
                    jour: 0,
                    id: 1,
                    module: "PHP / Laravel",
                    heure: "08h - 10h",
                    prof: "Ali Benali",
                    salle: "Salle A1",
                    bg: "#e8f5f6",
                    color: "#3c9298",
                },
                {
                    jour: 3,
                    id: 2,
                    module: "Réseaux Informatiques",
                    heure: "14h - 16h",
                    prof: "Sara Idrissi",
                    salle: "Labo R2",
                    bg: "#E6F1FB",
                    color: "#185FA5",
                },
                {
                    jour: 1,
                    id: 3,
                    module: "SQL Server",
                    heure: "10h - 12h",
                    prof: "Youssef Amrani",
                    salle: "Salle B2",
                    bg: "#EEEDFE",
                    color: "#534AB7",
                },
                {
                    jour: 1,
                    id: 4,
                    module: "Programmation JavaScript",
                    heure: "14h - 16h",
                    prof: "Hind Tazi",
                    salle: "Labo Info",
                    bg: "#FAEEDA",
                    color: "#854F0B",
                },
                {
                    jour: 2,
                    id: 5,
                    module: "Base de Données",
                    heure: "08h - 10h",
                    prof: "Omar Cherkaoui",
                    salle: "Labo Java",
                    bg: "#e8f5f6",
                    color: "#983c45",
                },
                {
                    jour: 3,
                    id: 6,
                    module: "Conception Orientée Objet",
                    heure: "10h - 12h",
                    prof: "Younes Bennani",
                    salle: "Labo R2",
                    bg: "#E6F1FB",
                    color: "#f7a3dc",
                },
            ],
        };
    },
    computed: {
        lundiSemaine() {
            const today = new Date();
            const day = today.getDay() || 7;
            const lundi = new Date(today);
            lundi.setDate(today.getDate() - day + 1 + this.offsetSemaine * 7);
            return lundi;
        },
        debutSemaine() {
            return this.lundiSemaine.toLocaleDateString("fr-FR", {
                day: "2-digit",
                month: "long",
            });
        },
        finSemaine() {
            const vendredi = new Date(this.lundiSemaine);
            vendredi.setDate(this.lundiSemaine.getDate() + 4);
            return vendredi.toLocaleDateString("fr-FR", {
                day: "2-digit",
                month: "long",
                year: "numeric",
            });
        },
        jours() {
            const noms = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi"];
            const today = new Date();
            return noms.map((nom, i) => {
                const d = new Date(this.lundiSemaine);
                d.setDate(this.lundiSemaine.getDate() + i);
                return {
                    nom,
                    date: d.toLocaleDateString("fr-FR", {
                        day: "2-digit",
                        month: "2-digit",
                    }),
                    isToday:
                        d.toDateString() === today.toDateString() &&
                        this.offsetSemaine === 0,
                    seances: this.planning.filter((s) => s.jour === i),
                };
            });
        },
    },
    methods: {
        semainePrecedente() {
            this.offsetSemaine--;
        },
        semaineSuivante() {
            this.offsetSemaine++;
        },
    },
};
</script>

<style scoped>
.week-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 12px 18px;
    margin-bottom: 16px;
}
.week-label {
    margin: 0;
    font-size: 14px;
    font-weight: 500;
    color: #111827;
}
.btn-week {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 0.5px solid #e5e7eb;
    background: white;
    color: #6b7280;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-week:hover {
    background: #f3f4f6;
}
.planning-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    padding: 16px;
    margin-bottom: 14px;
    overflow-x: auto;
}
.planning-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    min-width: 480px;
}
.jour-col {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.jour-header {
    text-align: center;
    padding: 8px;
    border-radius: 8px;
    background: #f8f9fb;
}
.jour-header.today {
    background: #e8f5f6;
}
.jour-nom {
    margin: 0;
    font-size: 12px;
    font-weight: 500;
    color: #111827;
}
.jour-date {
    margin: 0;
    font-size: 11px;
    color: #9ca3af;
}
.jour-header.today .jour-nom {
    color: #3c9298;
}
.jour-header.today .jour-date {
    color: #3c9298;
}
.seances-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.seance-card {
    padding: 8px 10px;
    border-radius: 8px;
    cursor: pointer;
    transition: opacity 0.15s;
}
.seance-card:hover {
    opacity: 0.85;
}
.seance-module {
    margin: 0;
    font-size: 12px;
    font-weight: 500;
    color: #111827;
}
.seance-heure {
    margin: 2px 0 0;
    font-size: 10px;
    color: #6b7280;
}
.seance-prof {
    margin: 2px 0 0;
    font-size: 10px;
    color: #6b7280;
}
.seance-salle {
    margin: 2px 0 0;
    font-size: 10px;
    color: #9ca3af;
}
.no-seance {
    text-align: center;
    font-size: 11px;
    color: #d1d5db;
    padding: 12px 0;
}

@media (max-width: 768px) {
    .week-label {
        font-size: 12px;
    }
    .planning-grid {
        min-width: 420px;
    }
    .seance-module {
        font-size: 10px;
    }
    .seance-heure,
    .seance-prof,
    .seance-salle {
        font-size: 9px;
    }
    .jour-nom {
        font-size: 10px;
    }
    .jour-date {
        font-size: 9px;
    }
}

@media (max-width: 480px) {
    .planning-grid {
        min-width: 360px;
    }
    .week-label {
        font-size: 11px;
    }
}
</style>
