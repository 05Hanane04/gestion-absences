<template>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="brand">
                <div class="brand-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <p class="brand-name">GestAbsences</p>
                </div>
            </div>

            <h2 class="title">Connexion</h2>
            <p class="subtitle">Accédez à votre espace personnel</p>

            <div v-if="error" class="alert alert-danger py-2 small">
                {{ error }}
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="form-control"
                    placeholder="exemple@univ.ma"
                />
            </div>

            <div class="mb-2">
                <label class="form-label">Mot de passe</label>
                <input
                    v-model="form.password"
                    type="password"
                    class="form-control"
                    placeholder="••••••••"
                />
            </div>

            <div class="text-end mb-4">
                <a href="#" class="forgot-link">Mot de passe oublié ?</a>
            </div>

            <button
                @click="login"
                class="btn btn-primary w-100"
                :disabled="loading"
            >
                <span
                    v-if="loading"
                    class="spinner-border spinner-border-sm me-2"
                ></span>
                Se connecter
            </button>
        </div>
    </div>
</template>

<script>
import axios from "../../axios.js";

export default {
    name: "Login",
    data() {
        return {
            form: { email: "", password: "" },
            error: "",
            loading: false,
        };
    },
    methods: {
        async login() {
            this.error = "";
            this.loading = true;
            try {
                const res = await axios.post("/login", this.form);
                localStorage.setItem("token", res.data.token);
                localStorage.setItem("user", JSON.stringify(res.data.user));
                const role = res.data.user.role;
                if (role === "admin") this.$router.push("/admin/dashboard");
                else if (role === "prof") this.$router.push("/prof/dashboard");
                else this.$router.push("/etudiant/profil");
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Erreur de connexion.";
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.login-wrapper {
    min-height: 100vh;
    background: #f8f9fb;
    display: flex;
    align-items: center;
    justify-content: center;
}
.login-card {
    background: white;
    border-radius: 16px;
    border: 0.5px solid #e5e7eb;
    padding: 40px 36px;
    width: 100%;
    max-width: 400px;
}
.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 32px;
}
.brand-icon {
    width: 36px;
    height: 36px;
    background: #3c9298;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}
.brand-name {
    margin: 0;
    font-size: 15px;
    font-weight: 500;
}
.title {
    font-size: 22px;
    font-weight: 500;
    margin: 0 0 4px;
}
.subtitle {
    font-size: 13px;
    color: #6b7280;
    margin: 0 0 28px;
}
.form-label {
    font-size: 13px;
    color: #6b7280;
}
.form-control {
    font-size: 14px;
    border: 0.5px solid #d1d5db;
    background: #f9fafb;
}
.form-control:focus {
    border-color: #3c9298;
    box-shadow: 0 0 0 3px rgba(60, 146, 152, 0.1);
}
.forgot-link {
    font-size: 12px;
    color: #3c9298;
    text-decoration: none;
}
.btn-primary {
    background: #3c9298;
    border: none;
    padding: 10px;
    font-size: 14px;
}
.btn-primary:hover {
    background: #2d7a7f;
}

@media (max-width: 768px) {
    .login-wrapper {
        align-items: flex-start;
        padding: 20px 16px;
        padding-top: 50px;
    }
    .login-card {
        padding: 28px 20px;
        border-radius: 12px;
        max-width: 100%;
    }
}
</style>
