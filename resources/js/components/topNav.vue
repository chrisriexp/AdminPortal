<template>
    <Reminders v-if="reminders" @close-reminder="reminders = false" />

    <div class="w-full h-[45px] grid bg-custom-bg dark:bg-custom-dark-bg border-b-[2px] border-custom-dark-blue dark:border-white shadow-customdrop relative">
        <div class="w-fit h-full flex mx-6 items-center font-semibold text-[14px] text-custom-light-gray dark:text-white">
            <router-link :to="{name: 'Dashboard'}" :class="active=='dashboard' ? 'bg-custom-dark-blue dark:bg-custom-gray text-white' : 'hover:text-custom-dark-blue dark:hover:bg-custom-gray dark:hover:text-white'" class="h-full px-6 items-center flex gap-2"><i class="pi pi-home"></i>Dashboard</router-link>
            <router-link :to="{name: 'Notebooks'}" :class="active=='notebooks' ? 'bg-custom-dark-blue dark:bg-custom-gray text-white' : 'hover:text-custom-dark-blue dark:hover:bg-custom-gray dark:hover:text-white'" class="h-full px-6 items-center flex gap-2"><i class="pi pi-file-edit"></i>Notebooks</router-link>
            <router-link :to="{name: 'Pipeline', params: {project: 'my-tasks'}}" :class="active=='pipeline' ? 'bg-custom-dark-blue dark:bg-custom-gray text-white' : 'hover:text-custom-dark-blue dark:hover:bg-custom-gray dark:hover:text-white'" class="h-full px-6 items-center flex gap-2"><i class="pi pi-sitemap"></i>Pipeline</router-link>
            <button @click="toggle_modules_menu" aria-haspopup="true" aria-controls="modules_menu" class="h-full px-6 items-center flex gap-2"><i class="pi pi-th-large"></i>Modules</button>
            <Menu ref="modules_menu" id="modules_menu" :model="module_items" :popup="true" class="font-medium text-[14px] text-custom-light-gray dark:text-white" />
        </div>

        <div class="w-fit h-full flex gap-4 items-center right-4 absolute text-custom-gray dark:text-white">
            <!-- Dark Mode Toggle -->
            <div class="w-fit h-fit flex items-center my-auto gap-2">
                <InputSwitch @click="themeFromCreated = false" id="darkModeInput" v-model="darkMode" />
                <p class="text-[14px] font-medium">{{ darkMode ? 'Dark Mode' : 'Light Mode' }}</p>
                <i :class="darkMode ? 'pi pi-moon' : 'pi pi-sun'"></i>
            </div>

            <!-- Profile Menu Popup -->
            <div @click="toggle" aria-haspopup="true" aria-controls="overlay_menu" class="h-[35px] w-[35px] grid bg-[#dad8d8] rounded-full cursor-pointer shadow-newdrop"><p style="user-select: none;" class="m-auto font-bold font-pt-sans text-custom-dark-blue">{{ initals }}</p></div>
            <Menu ref="menu" id="overlay_menu" :model="items" :popup="true" />
        </div>
    </div>
</template>

<script>
import { BellIcon, ChatBubbleBottomCenterTextIcon, InformationCircleIcon, Cog8ToothIcon } from '@heroicons/vue/24/outline';

import Menu from 'primevue/menu';
import Reminders from '../views/Reminders.vue'

import InputSwitch from 'primevue/inputswitch';

import { useDark, useToggle } from '@vueuse/core';

const isDark = useDark();
const toggleDark = useToggle(isDark);

export default{
    name: 'Top Nav Menu',
    props: {
        active: {
            type: String,
            defaut: 'null'
        }
    },
    data(){
        return {
            initals: "",
            darkMode: false,
            themeFromCreated: true,
            profilePopup: false,
            reminders: false,
            items: [
                {
                    label: 'Reminders',
                    icon: 'pi pi-hourglass',
                    command: async () => {
                        this.reminders = true
                    }
                },
                {
                    label: 'Reports',
                    icon: 'pi pi-chart-bar',
                    command: async () => {
                        
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
                        this.$router.push({name: 'Settings'})
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
            module_items: [
                {
                    label: 'Onboarding Portal',
                    icon: '',
                    command: async () => {
                        console.log('go to user onboading')
                    }
                },
                {
                    label: 'ROVER',
                    icon: '',
                    command: async () => {
                        console.log('go to user rover')
                    }
                },
                {
                    label: 'REACT',
                    icon: '',
                    command: async () => {
                        console.log('go to user react')
                    }
                }
            ]
        }
    },
    async created(){
        await axios.get('/api/user')
        .then(response => {
            const names = response.data.name.split(" ")
            const i1 = Array.from(names[0])[0]
            const i2 = Array.from(names[1])[0]
            this.initals = i1+i2

            if(response.data.theme == 'dark-mode'){
                this.darkMode = true
            }else{
                toggleDark(false)
            }
        })
    },
    watch: {
        darkMode: async function(value){
            toggleDark(value)

            if(value == true){
                this.$primevue.changeTheme('light-mode', 'dark-mode', 'theme-link', () => {});
                document.body.classList.remove('bg-custom-light-blue')
                document.body.classList.add('bg-custom-gray-bg')

                if(!this.themeFromCreated){
                    await axios.post('/api/theme/dark-mode')
                    .then(response =>  {
                        this.$toast.add({
                            severity: 'success',
                            summary: 'Theme',
                            detail: response.data.message,
                            life: 2500
                        })
                    })
                }

                this.themeFromCreated = true
            }else{
                this.$primevue.changeTheme('dark-mode', 'light-mode', 'theme-link', () => {});
                document.body.classList.remove('bg-custom-gray-bg')
                document.body.classList.add('bg-custom-light-blue')

                await axios.post('/api/theme/light-mode')
                .then(response =>  {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Theme',
                        detail: response.data.message,
                        life: 2500
                    })
                })
            }
        }
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
        BellIcon,
        ChatBubbleBottomCenterTextIcon,
        InformationCircleIcon,
        Cog8ToothIcon,
        Menu,
        Reminders,
        InputSwitch
    }
}
</script>

<style>
.p-inputswitch.p-focus .p-inputswitch-slider {
    box-shadow: none !important;
}
.p-inputswitch-slider{
    height: 25px !important;
}
</style>