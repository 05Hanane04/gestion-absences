<template>
    <div>
        <div class="profil-grid">
            <!-- Carte profil -->
            <div class="profil-card">
                <div class="profil-avatar-section">
                    <div class="big-avatar">{{ userInitials }}</div>
                    <h2 class="profil-name">{{ user.name }}</h2>
                    <p class="profil-email">{{ user.email }}</p>
                    <div class="profil-badges">
                        <span class="badge-filiere">3IIR</span>
                        <span class="badge-groupe">Groupe G3</span>
                    </div>
                </div>

                <div class="profil-info">
                    <div class="info-row">
                        <span class="info-label"
                            ><i class="bi bi-credit-card"></i>Matricule</span
                        >
                        <span class="info-value">C-2026-076859</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"
                            ><i class="bi bi-book"></i> Filière</span
                        >
                        <span class="info-value"
                            >Ingénierie Informatique et Réseaux</span
                        >
                    </div>
                    <div class="info-row">
                        <span class="info-label"
                            ><i class="bi bi-people"></i> Groupe</span
                        >
                        <span class="info-value">G3 - G32</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"
                            ><i class="bi bi-envelope"></i>Adresse
                            email</span>
                        <span class="info-value">hananchallal07@gmail.com</span>
                    </div>
                     <div class="info-row">
                        <span class="info-label"
                            ><i class="bi bi-telephone"></i>Téléphone</span>
                        <span class="info-value">+212652383037</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="bi bi-calendar3"></i> Année Universitaire</span
                        >
                        <span class="info-value">3ème année - 2025/2026</span>
                    </div>
                </div>
            </div>

            <div class="right-col">
                <div class="resume-card">
                    <p class="card-title">Résumé des absences</p>
                    <div class="resume-stats">
                        <div class="resume-stat">
                            <p class="rs-value">{{ totalAbsences }}h</p>
                            <p class="rs-label">Total</p>
                        </div>
                        <div class="resume-stat danger">
                            <p class="rs-value">{{ nonJustifiees }}h</p>
                            <p class="rs-label">Non justifiées</p>
                        </div>
                        <div class="resume-stat success">
                            <p class="rs-value">{{ justifiees }}h</p>
                            <p class="rs-label">Justifiées</p>
                        </div>
                    </div>

                    <!-- Barre de progression -->
                    <div class="progress-section">
                        <div class="progress-header">
                            <span class="progress-label"
                                >Seuil d'absences autorisées</span
                            >
                            <span class="progress-pct"
                                >{{
                                    Math.round(
                                        (totalAbsences / seuilMax) * 100,
                                    )
                                }}%</span
                            >
                        </div>
                        <div class="progress-bar">
                            <div
                                class="progress-fill"
                                :style="{
                                    width:
                                        Math.min(
                                            (totalAbsences / seuilMax) * 100,
                                            100,
                                        ) + '%',
                                    background:
                                        totalAbsences >= seuilMax
                                            ? '#E24B4A'
                                            : '#3c9298',
                                }"
                            ></div>
                        </div>
                        <p class="progress-note">
                            {{ totalAbsences }}h / {{ seuilMax }}h maximum
                            autorisées
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Profil",
    data() {
        return {
            seuilMax: 16,
            absencesParModule: [
                { module: "Algorithmique", heures: 4, color: "#3c9298" },
                { module: "Réseaux", heures: 2, color: "#185FA5" },
                { module: "Maths", heures: 2, color: "#534AB7" },
                { module: "POO Java", heures: 2, color: "#0F6E56" },
                { module: "BD", heures: 2, color: "#854F0B" },
            ],
        };
    },
    computed: {
        user() {
            return JSON.parse(localStorage.getItem("user") || "{}");
        },
        userInitials() {
            return (this.user.name || "ET")
                .split(" ")
                .map((n) => n[0])
                .join("")
                .toUpperCase()
                .slice(0, 2);
        },
        totalAbsences() {
            return this.absencesParModule.reduce((s, m) => s + m.heures, 0);
        },
        nonJustifiees() {
            return 6;
        },
        justifiees() {
            return this.totalAbsences - this.nonJustifiees;
        },
    },
};
</script>

<style scoped>
.profil-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 16px;
    align-items: start;
}
.profil-card {
    background: white;
    border-radius: 10px;
    border: 0.5px solid #e5e7eb;
    overflow: hidden;
    width: 400px;
}
.profil-avatar-section {
    background: #e8f5f6;
    padding: 28px 20px;
    text-align: center;
    border-bottom: 0.5px solid #e5e7eb;
}
.big-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #3c9298;
    color: white;
    font-size: 22px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}
.profil-name {
    margin: 0 0 4px;
    font-size: 16px;
    font-weight: 500;
    color: #111827;
}
.profil-email {
    margin: 0 0 10px;
    font-size: 12px;
    color: #6b7280;
}
.profil-badges {
    display: flex;
    gap: 6px;
    justify-content: center;
    flex-wrap: wrap;
}
.badge-filiere {
    font-size: 11px;
    padding: 3px 10px;
    background: #3c9298;
    color: white;
    border-radius: 20px;
}
.badge-groupe {
    font-size: 11px;
    padding: 3px 10px;
    background: white;
    color: #3c9298;
    border: 0.5px solid #3c9298;
    border-radius: 20px;
}

.profil-info {
    padding: 16px 20px;
}
.info-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 0.5px solid #f3f4f6;
    font-size: 13px;
    gap: 10px;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    color: #9ca3af;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.info-value {
    color: #111827;
    font-weight: 500;
    text-align: right;
}

.right-col {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.resume-card {
    width: 550px;
    margin-left: 130px;
}
.resume-card,
.modules-card {
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

.resume-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}
.resume-stat {
    text-align: center;
    padding: 12px;
    border-radius: 8px;
    background: #f8f9fb;
}
.resume-stat.danger {
    background: #fcebeb;
}
.resume-stat.success {
    background: #e1f5ee;
}
.rs-value {
    margin: 0;
    font-size: 22px;
    font-weight: 500;
    color: #111827;
}
.resume-stat.danger .rs-value {
    color: #e24b4a;
}
.resume-stat.success .rs-value {
    color: #0f6e56;
}
.rs-label {
    margin: 4px 0 0;
    font-size: 11px;
    color: #9ca3af;
}

.progress-section {
    margin-top: 4px;
}
.progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
    font-size: 12px;
}
.progress-label {
    color: #6b7280;
}
.progress-pct {
    color: #111827;
    font-weight: 500;
}
.progress-bar {
    height: 6px;
    background: #f3f4f6;
    border-radius: 3px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s;
}
.progress-note {
    margin: 6px 0 0;
    font-size: 11px;
    color: #9ca3af;
}

.module-row {
    margin-bottom: 12px;
}
.module-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 4px;
    font-size: 12px;
}
.module-nom {
    color: #6b7280;
}
.module-h {
    color: #111827;
    font-weight: 500;
}
.module-bar {
    height: 5px;
    background: #f3f4f6;
    border-radius: 3px;
    overflow: hidden;
}
.module-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s;
}
</style>
