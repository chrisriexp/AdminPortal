import {createRouter, createWebHistory} from 'vue-router';
import Login from './views/Login.vue'
import ResetPassword from './views/ResetPassword.vue'
import NotFound from './views/NotFound.vue'
import Dashboard from './views/Dashboard.vue'
import Notebooks from './views/Notebooks.vue'
import Settings from './views/Settings.vue'
import Reports from './views/Reports.vue'
import Pipeline from './views/Pipeline.vue'
import REACTSubAgents from './views/REACT/SubAgents.vue'
import REACTUploadStatements from './views/REACT/StatementUpload.vue'
import REACTSubAgent from './views/REACT/SubAgent.vue'
import OnboardingAgents from './views/Onboarding/Agents.vue'
import OnboardingAgency from './views/Onboarding/Agency.vue'
import MGA_Marketing from './views/Onboarding/Marketing.vue'
import ROVERErrors from './views/ROVER/Errors.vue'
import ROVERError from './views/ROVER/Error.vue'

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
        path: "/reset-password",
        name: "ResetPassword",
        component: ResetPassword
    },
    {
        path: "/settings/:category/:id?",
        name: "Settings",
        component: Settings,
        beforeEnter: validateAccessToken
    },
    {
        path: "/reports/:category",
        name: "Reports",
        component: Reports,
        beforeEnter: validateAccessToken
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
        path: "/pipeline/:project/:task?",
        name: "Pipeline",
        component: Pipeline,
        beforeEnter: validateAccessToken
    },
    {
        path: "/react",
        meta: {
            react: true
        },
        children: [
            {
                path: "sub-agents",
                name: "REACT_SubAgents",
                component: REACTSubAgents,
                beforeEnter: validateAccessToken
            },
            {
                path: "upload-statements",
                name: "REACT_UploadStatements",
                component: REACTUploadStatements,
                beforeEnter: validateAccessToken
            },
            {
                path: "sub-agent/:id/:category",
                name: "REACT_SubAgent",
                component: REACTSubAgent,
                beforeEnter: validateAccessToken
            }
        ]
    },
    {
        path: "/onboarding",
        meta: {
            onboarding: true
        },
        children:[
            {
                path: "mga-marketing",
                name: "MGA_Marketing",
                component: MGA_Marketing,
                beforeEnter: validateAccessToken
            },
            {
                path: "agents",
                name: "Onboarding_Agents",
                component: OnboardingAgents,
                beforeEnter: validateAccessToken
            },
            {
                path: "agency/:rocket_id/:category",
                name: "Onboarding_Agency",
                component: OnboardingAgency,
                beforeEnter: validateAccessToken
            }
        ]
    },
    {
        path: "/rover",
        meta: {
            rover: true
        },
        children: [
            {
                path: "errors",
                name: "ROVER_Errors",
                component: ROVERErrors,
                beforeEnter: validateAccessToken
            },
            {
                path: "error/:app_id/:carrier",
                name: "ROVER_Error",
                component: ROVERError,
                beforeEnter: validateAccessToken
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// router.beforeEach(async (to, from, next) => {
//     if(to.name !== "Login" && to.name !== "NotFound" && to.name !== "ResetPassword"){
//         await axios.post('/api/token/validate')
//         .then(response => {
//             if (response.data.valid) {
//                 to.meta.role = response.data.role;

//                 if(to.meta.admin){
//                     if(to.meta.role == 'admin' || to.meta.role == 'super-admin'){
//                         next();
//                     } else {
//                         next({ name: "Dashboard" })
//                     }
//                 }
//                 else if(to.meta.react){
//                     if(response.data.react){
//                         next();
//                     }else{
//                         next({ name: "Dashboard" })
//                     }
//                 }
//                 else if(to.meta.onboarding){
//                     if(response.data.onboarding){
//                         next();
//                     }else{
//                         next({ name: "Dashboard" })
//                     }
//                 }
//                 else {
//                     next();
//                 }
//             } else {
//                 next({ name: "Login" });
//             }
//         })
//         .catch(error => {
//             console.error(error);
//             next({ name: "Login" });
//         });
//     } else {
//         next()
//     }
// })

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
            }
            else if(to.meta.react){
                if(response.data.react){
                    next();
                }else{
                    next({ name: "Dashboard" })
                }
            }
            else if(to.meta.onboarding){
                if(response.data.onboarding){
                    next();
                }else{
                    next({ name: "Dashboard" })
                }
            }
            else {
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