<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <div v-if="ready" class="w-screen h-screen grid grid-cols-2">
        <!-- Login Animation and Information -->
        <div class="w-full h-full grid bg-sidebar-bg">
            <div class="w-fit h-fit m-auto grid justify-items-center">
                <div class="w-[600px] h-fit grid text-[18px] text-custom-gray font-pt-sans">
                    <p class="text-custom-dark-blue">Welcome to the,<br><span class="text-[32px] font-bold">Rocket Flood Admin Portal!</span></p>
                    <p class="mt-6 italic">Please enter your login credentials to access your account.</p>
                    <p class="italic">If you don't have an account, please contact our support team to get started.<br><br>Thank you for using Rocket Flood!</p>
                </div>
                
                <LottieAnimation :animationData="LoginAnimation" :renderer="'canvas'" :width="'500px'" :height="'500px'" :speed="0.75" class="m-auto" />
            </div>
        </div>

        <div class="w-full h-full grid bg-white">
            <!-- Login Form -->
            <form @submit.prevent="login" class="w-fit h-fit m-auto">
                <div class="grid gap-8 w-[400px] h-fit">
                    <!-- Logo -->
                    <img src="../../assets/purpleLogo64.png" alt="Logo" class="w-fit h-fit mx-auto">

                    <p class="w-fit h-fit mx-auto text-[32px] text-custom-black font-semibold">Login</p>

                    <div class="w-full h-fit grid gap-4 py-8 relative">
                        <!-- Username -->
                        <span class="p-input-icon-left">
                            <i class="pi pi-user" />
                            <InputText v-model="form.email" placeholder="Email" class="w-full h-[48px] rounded-[4px]" />
                        </span>

                        <!-- Password -->
                        <span class="p-input-icon-left">
                            <i class="pi pi-lock" />
                            <InputText v-model="form.password" type="password" placeholder="Password" class="w-full h-[48px] rounded-[4px] border-[#d3dbe3] border-[1px]" />
                        </span>

                        <a href="/reset-password" class="right-0 bottom-0 absolute text-[16px] text-custom-purple underline">Forgot Password?</a>
                    </div>

                    <!-- Submit Button -->
                    <input type="submit" value="Login" style="border-radius: 4px;" class="w-full h-[48px] cursor-pointer bg-custom-purple text-[16px] text-white font-semibold shadow-newdrop">
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import loading from '../components/loading.vue'
import textInput from '../components/textInput.vue'

import LoginAnimation from '../../assets/newLogin.json'

import InputText from 'primevue/inputtext';
import Password from 'primevue/password';

export default {
    name: "Home",
    data(){
        return {
            LoginAnimation,
            ready: false,
            loading: true,
            form: {
                email: "",
                password: ""
            }
        }
    },
    async created(){
        const accessToken = localStorage.getItem('token');
        if (accessToken) {
            axios.post('/api/token/validate')
            .then(response => {
                if (response.data.valid) {
                    this.$router.push({name: 'Dashboard'})
                }
            })
            .catch(error => {
                console.error(error);
            });
        }

        this.ready = true
        this.loading = false
    },
    methods: {
        async login(){
            this.loading = true
            let valid = true

            const keys = Object.keys(this.form)

            keys.forEach(key => {
                if(!this.form[key]){
                    valid = false
                    this.$toast.add({ 
                        severity: 'warn',
                        summary: 'Validation Error',
                        detail: key == 'email' ? 'Please enter your username.' : 'Please enter your password.',
                        life: 2500
                    })
                }
            })

            if(valid){
                setTimeout(async () => {
                    await axios.post('/api/login', this.form)
                    .then(response => {
                        if(response.data.success){
                            localStorage.setItem('token', response.data.token)
                            this.$router.push({name: 'Dashboard'})
                            this.$toast.add({ 
                                severity: 'success',
                                summary: 'Login',
                                detail: response.data.message,
                                life: 2500
                            })
                        }
                    })
                    .catch(error => {
                        this.form.password = ''
                        if(error.response.status == 400){
                            const keys = Object.keys(error.response.data.message)
                            keys.forEach(key => {
                                error.response.data.message[key].forEach(error => {
                                    this.$toast.add({ 
                                        severity: 'error',
                                        summary: 'Login Error',
                                        detail: error,
                                        life: 2500
                                    })
                                })
                            })
                        } else if(error.response.status == 401){
                            this.$toast.add({ 
                                severity: 'error',
                                summary: 'Login Error',
                                detail: error.response.data.message,
                                life: 2500
                            })
                        }

                        this.loading = false
                    })
                }, 200);
            } else {
                this.loading = false
            }
        }
    },
    components: {
        loading,
        textInput,
        InputText,
        Password
    }
}
</script>