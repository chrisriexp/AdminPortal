import {createRouter, createWebHistory} from 'vue-router';
import Login from './views/Login.vue'
import NotFound from './views/NotFound.vue'
import Dashboard from './views/Dashboard.vue'
import Notebooks from './views/Notebooks.vue'
import Settings from './views/Settings.vue'
import Pipeline from './views/Pipeline.vue'

const routes = [
    {
        path: "/:catchAll(.*)",
        name: "NotFound",
        component: NotFound,
    },
    {
        path: "/login",
        name: "Login",
        component: Login
    },
    {
        path: "/dashboard",
        name: "Dashboard",
        component: Dashboard,
        beforeEnter: validateAccessToken
    },
    {
        path: "/notebooks",
        name: "Notebooks",
        component: Notebooks,
        beforeEnter: validateAccessToken
    },
    {
        path: "/settings",
        name: "Settings",
        component: Settings,
        beforeEnter: validateAccessToken
    },
    {
        path: "/pipeline/:project/:task?",
        name: "Pipeline",
        component: Pipeline,
        beforeEnter: validateAccessToken
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

async function validateAccessToken(to, from, next) {
    const accessToken = localStorage.getItem('token');

    if (!accessToken) {
        next({ name: "Login" });
        return;
    }

    await axios.post('/api/token/validate')
    .then(response => {
        if (response.data.valid) {
            to.meta.role = response.data.role;

            if(to.meta.admin){
                if(to.meta.role == 'admin' || to.meta.role == 'super-admin'){
                    next();
                } else {
                    next({ name: "Dashboard" })
                }
            }else {
                next();
            }
        } else {
            next({ name: "Login" });
        }
    })
    .catch(error => {
        console.error(error);
        next({ name: "Login" });
    });

    
}

export default router