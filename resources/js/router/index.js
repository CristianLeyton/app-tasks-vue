import { createRouter, createWebHistory } from "vue-router";

import LoginPage from "../pages/LoginPage.vue";
import RegisterPage from "../pages/RegisterPage.vue";
import HomePage from "../pages/HomePage.vue";
import PanelPage from "../pages/PanelPage.vue";

function isAuthenticated() {
    return !!localStorage.getItem("token") && localStorage.getItem("token") !== "undefined";
}

const routes = [
    { path: "/", name: "home", component: HomePage, meta: { requiresAuth: true },},
    { path: "/panel", name: "panel", component: PanelPage, meta: { requiresAuth: true },},
    { path: "/login", name: "login", component: LoginPage },
    { path: "/register", name: "register", component: RegisterPage },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const loggedIn = isAuthenticated();

    //RUTAS PROTEGIDAS
    if (to.meta.requiresAuth && !loggedIn) {
        return next({ name: "login" });
    }

    //IMPEDIR ACCESO A LOGIN / REGISTER SI YA ESTÁ LOGUEADO
    if ((to.name === "login" || to.name === "register") && loggedIn) {
        return next({ name: "home" });
    }
    next();
});

export default router;