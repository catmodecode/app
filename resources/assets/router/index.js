import { createRouter, createWebHistory } from "vue-router";
import Public from "../views/Public.vue";
import Admin from "../views/Admin.vue";
import About from "../views/About.vue";

const routes = [
    {
        path: "/",
        name: "Home",
        component: Public,
        role: "public",
        children: [
            {
                path: "/about",
                name: "About",
                role: "public",
                component: About,
                children: [
                    {
                        path: "/about/a",
                        name: "Bebaut",
                        role: "public",
                        component: About
                    }
                ]
            },
            {
                path: "login",
                name: "Login",
                role: "public",
                component: About
            }
        ]
    },
    {
        path: "/admin",
        name: "Admin",
        role: "admin",
        component: Admin
    }
];

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes
});

export default router;
export { routes };
