<template>
    <div class="w-full h-fit grid gap-6 pb-12">
        <!-- Header -->
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <p class="text-[32px] text-custom-black font-semibold">Profile</p>
            </div>

            <div class="w-fit h-full hidden items-center gap-2 float-right">
                <InputSwitch v-model="darkMode" />
                <p class="text-[16px] text-custom-black font-medium opacity-50">{{ darkMode ? 'Dark Mode' : 'Light Mode' }}</p>
            </div>
        </div>

        <!-- User Name and Role -->
        <div class="w-full h-[183px] flow-root p-6 bg-custom-purple bg-opacity-10 rounded-[4px]">
            <div class="w-fit h-full flex gap-8 items-center float-left">
                <!-- User Initals/PFP -->
                <div class="w-[119px] h-[119px] grid bg-custom-yellow rounded-full">
                    <p class="m-auto text-[64px] text-custom-black font-semibold">{{ initals }}</p>
                </div>

                <!-- User Name and Role -->
                <div class="w-fit h-fit grid gap-2">
                    <p class="text-[32px] text-custom-black font-semibold">{{ user.name }}</p>
                    <p class="text-[16px] text-custom-black opacity-60">{{ user.role == 'super-admin' ? "Super Admin" : (user.role == 'admin' ? 'Admin' : 'User') }}</p>
                </div>
            </div>

            <!-- Email Notifications -->
            <div class="w-fit h-fit flex items-center gap-4 float-right">
                <InputSwitch @click="updateEmailNotifications" v-model="user.email_notifications" />
                <p class="text-[16px] text-custom-black font-medium opacity-50">Email Notifications</p>
            </div>
        </div>

        <!-- User Email -->
        <div class="w-fit h-fit grid">
            <p class="text-[16px] text-custom-black font-medium">Email</p>
            <InputText disabled v-model="user.email" placeholder="Task Name" class="w-[519px] h-[48px] mx-auto rounded-[2px] bg-sidebar-bg" />
        </div>

        <!-- Modules -->
        <div class="w-full h-fit flex items-center gap-8">
            <!-- Onboarding Portal -->
            <div v-if="user.onboarding" class="w-full h-[52px] mx-auto px-4 flow-root bg-white rounded-[4px] shadow-newdrop">
                <div class="w-fit h-full flex items-center float-left">
                    <p class="text-[16px] text-custom-black font-medium">Onboarding Portal</p>
                </div>

                <div class="w-fit h-full flex items-center float-right">
                    <InputSwitch disabled v-model="user.onboarding" />
                </div>
            </div>

            <!-- ROVER -->
            <div v-if="user.rover" class="w-full h-[52px] mx-auto px-4 flow-root bg-white rounded-[4px] shadow-newdrop">
                <div class="w-fit h-full flex items-center float-left">
                    <p class="text-[16px] text-custom-black font-medium">ROVER</p>
                </div>

                <div class="w-fit h-full flex items-center float-right">
                    <InputSwitch disabled v-model="user.rover" />
                </div>
            </div>

            <!-- REACT -->
            <div v-if="user.react" class="w-full h-[52px] mx-auto px-4 flow-root bg-white rounded-[4px] shadow-newdrop">
                <div class="w-fit h-full flex items-center float-left">
                    <p class="text-[16px] text-custom-black font-medium">REACT</p>
                </div>

                <div class="w-fit h-full flex items-center float-right">
                    <InputSwitch disabled v-model="user.react" />
                </div>
            </div>
        </div>

        <div class="w-fit h-fit flex items-center gap-6">
            <!-- Update Email -->
            <button @click="$emit('updateEmail')" class="w-[232px] h-[48px] grid justify-items-center text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">
                <div class="w-fit h-fit flex items-center gap-6 my-auto">
                    <Icon :icon="'ic:round-update'" height="24" />
                    <p>Update Email</p>
                </div>
            </button>

            <!-- Update Password -->
            <button @click="$emit('updatePassword')" class="w-[232px] h-[48px] grid justify-items-center text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">
                <div class="w-fit h-fit flex items-center gap-6 my-auto">
                    <Icon :icon="'ic:round-update'" height="24" />
                    <p>Update Password</p>
                </div>
            </button>
        </div>
    </div>
</template>

<script>
import InputSwitch from 'primevue/inputswitch';
import InputText from 'primevue/inputtext';
import { Icon } from '@iconify/vue';

import { useDark, useToggle } from '@vueuse/core';

const isDark = useDark();
const toggleDark = useToggle(isDark);

export default{
    name: "User Profile Settings",
    data(){
        return{
            user: {},
            initals: "",
            darkMode: false
        }
    },
    async mounted() {
        this.$emit('loading')

        await axios.get('/api/user')
        .then(response => {
            this.user = response.data
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

        const items = ['onboarding', 'rover', 'react', 'email_notifications']

        items.forEach(item => {
            if(this.user[item] == 1){
                this.user[item] = true
            } else if(this.user[item] == 0){
                this.user[item] = false
            }
        })

        this.$emit('loading')
    },
    watch: {
        darkMode: async function(value){
            toggleDark(value)

            if(value == true){
                this.$primevue.changeTheme('light-mode', 'dark-mode', 'theme-link', () => {});
                document.body.classList.remove('bg-custom-light-blue')
                document.body.classList.add('bg-custom-gray-bg')

                await axios.post('/api/theme/dark-mode')
                .then(response =>  {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Theme',
                        detail: response.data.message,
                        life: 2500
                    })
                })
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
        async updateEmailNotifications(){
            setTimeout(async () => {
                await axios.post('/api/update/email-notification', {email_notifications: this.user.email_notifications})
                .then(response => [
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Email Notifications',
                        detail: response.data.message,
                        life: 2500
                    })
                ])
            }, 10)
        }
    },
    components: {
        InputSwitch,
        InputText,
        Icon
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
