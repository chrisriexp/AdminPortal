<template>
    <QuickTools v-if="reminders" @close-reminder="reminders = false" />

    <div class="w-full h-[72px] bg-white px-8 flow-root shadow-customdrop border-b-[1px] border-custom-black border-opacity-50 relative">
        <div class="w-fit h-full float-left flex items-center gap-32">
            <img src="../../assets/adminLogoLight.png" alt="Logo">

            <div class="w-fit h-fit flex items-center gap-6">
                <router-link :to="{name: 'Dashboard'}" :class="active == 'dashboard' ? 'bg-custom-purple text-white' : 'text-custom-black opacity-50'" class="flex items-center gap-2 px-4 py-[4px] text-[16px] font-medium rounded-[4px]"><Icon :icon="'heroicons:home-solid'" height="24" class="m-auto" /> <span>Dashboard</span></router-link>
                <router-link :to="{name: 'Notebooks'}" :class="active == 'notebooks' ? 'bg-custom-purple text-white' : 'text-custom-black opacity-50'" class="flex items-center gap-2 px-4 py-[4px] text-[16px] font-medium rounded-[4px]"><Icon :icon="'icon-park-solid:notes'" height="24" class="m-auto" /> <span>Notebooks</span></router-link>
                <router-link :to="{name: 'Pipeline', params: {project: 'my-tasks'}}" :class="active == 'pipeline' ? 'bg-custom-purple text-white' : 'text-custom-black opacity-50'" class="flex items-center gap-2 px-4 py-[4px] text-[16px] font-medium rounded-[4px]"><Icon :icon="'icon-park-solid:connection-point'" height="24" class="m-auto" /> <span>Pipelines</span></router-link>
                
                <!-- Modules Menu -->
                <button @click="toggle_modules_menu" aria-haspopup="true" aria-controls="modules_menu" :class="active=='modules' ? 'bg-custom-purple text-white' : 'text-custom-black opacity-50'" class="flex items-center gap-2 px-4 py-[4px] text-[16px] font-medium rounded-[4px]"><Icon :icon="'material-symbols:space-dashboard-rounded'" height="24" class="m-auto" />Modules</button>
                <Menu ref="modules_menu" id="modules_menu" :model="module_items" :popup="true" class="w-fit text-[16px] text-custom-black dark:text-white" />
            </div>
        </div>

        <div @click="toggle" aria-haspopup="true" aria-controls="overlay_menu" class="w-fit h-full flex gap-2 items-center float-right cursor-pointer">
            <div class="w-fit h-fit grid text-[16px] text-custom-black">
                <p class="font-medium">{{ name }}</p>
                <p class="opacity-50">{{ role == 'super-admin' ? 'Super Admin' : (role == 'admin' ? 'Admin' : (role == 'user' ? 'User' : '')) }}</p>
            </div>
            <div class="w-[48px] h-[48px] grid rounded-full bg-[#C6C6C6]"><p class="m-auto text-[20px] font-bold text-custom-black">{{ initals }}</p></div>
        </div>
        <Menu ref="menu" id="overlay_menu" :model="items" :popup="true" />
    </div>
</template>

<script>
import { HomeIcon, DocumentTextIcon, RectangleGroupIcon } from '@heroicons/vue/24/outline';

import Menu from 'primevue/menu';
import QuickTools from '../views/QuickTools.vue'

import { Icon } from '@iconify/vue';

export default{
    name: 'Top Nav Menu',
    props: {
        active: {
            type: String,
            default: 'null'
        }
    },
    data(){
        return {
            name: "",
            initals: "",
            role: "",
            profilePopup: false,
            reminders: false,
            items: [
                {
                    label: 'Quick Tools',
                    icon: 'pi pi-compass',
                    command: async () => {
                        this.reminders = true
                    }
                },
                {
                    label: 'Help Center',
                    icon: 'pi pi-info-circle',
                    command: async () => {
                        
                    }
                },
                {
                    label: 'Settings',
                    icon: 'pi pi-cog',
                    command: async () => {
                        this.$router.push({name: 'Settings', params: {category: 'user'}})
                    }
                },
                {
                    label: 'Logout',
                    icon: 'pi pi-sign-out',
                    command: async () => {
                        this.$emit('loading')

                        setTimeout(async () => {
                            await axios.get('/api/logout')
                            .then(response => {
                                this.$router.push({name: "Login"})
                            })
                        }, 200);
                    }
                }
            ],
            module_items: [],
            onboarding: {
                label: 'MGA Onboarding',
                icon: '',
                items: [
                    {
                        label: 'Agents',
                        icon: 'pi pi-users',
                        command: async () => {
                            this.$router.push({name: 'Onboarding_Agents'})
                        }
                    },
                    {
                        label: 'Reports',
                        icon: 'pi pi-chart-line',
                        command: async () => {
                            this.$router.push({name: 'Report_Onboarding'})
                        }
                    },
                    {
                        label: 'Cat Coverage BackLogs',
                        icon: 'pi pi-history',
                        command: async () => {
                            this.$router.push({name: 'CatCovBackLog'})
                        }
                    }
                ]
                
            },
            rover: {
                label: 'ROVER',
                icon: '',
                items: [
                    {
                        label: 'Errors',
                        icon: 'pi pi-exclamation-circle',
                        command: async () => {
                            this.$router.push({name: 'ROVER_Errors'})
                        }
                    },
                    {
                        label: 'Reports',
                        icon: 'pi pi-chart-bar',
                        command: async () => {
                            
                        }
                    }
                ]
            },
            react:  {
                label: 'REACT',
                icon: '',
                items: [
                    {
                        label: 'Statement Upload',
                        icon: 'pi pi-cloud-upload',
                        command: async () => {
                            this.$router.push({name: 'REACT_UploadStatements'})
                        }
                    },
                    {
                        label: 'MGA Companies',
                        icon: 'pi pi-list',
                        command: async () => {
                            this.$router.push({name: 'REACT_SubAgents'})
                        }
                    },
                    {
                        label: 'Commissions',
                        icon: 'pi pi-money-bill',
                        command: async () => {
                            
                        }
                    },
                    {
                        label: 'Reports',
                        icon: 'pi pi-chart-pie',
                        command: async () => {
                            
                        }
                    }
                ]
            }
        }
    },
    async created(){
        this.role = this.$route.meta.role

        const modules = ['onboarding', 'rover', 'react']

        await axios.get('/api/user')
        .then(response => {
            const names = response.data.name.split(" ")
            const i1 = Array.from(names[0])[0]
            const i2 = Array.from(names[1])[0]
            this.initals = i1+i2
            this.name = response.data.name

            modules.forEach(module => {
                if(response.data[module] == 1){
                    this.module_items.push(this[module])
                }
            })
        })
    },
    methods: {
        toggle(event){
            this.$refs['menu'].toggle(event);
        },
        toggle_modules_menu(event){
            this.$refs['modules_menu'].toggle(event);
        },
    },
    components: {
        HomeIcon,
        DocumentTextIcon,
        RectangleGroupIcon,
        Menu,
        QuickTools,
        Icon
    }
}
</script>

<style>
.p-menu .p-menuitem>.p-menuitem-content .p-menuitem-link:hover{
    background-color: #F4F4F4 !important;
    color: #a778e5 !important;
}

.p-menu .p-menuitem>.p-menuitem-content .p-menuitem-link:hover .p-menuitem-icon, .p-menu .p-menuitem>.p-menuitem-content .p-menuitem-link:hover .p-menuitem-text {
    color: #a778e5 !important;
}
</style>