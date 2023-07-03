<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <div class="w-screen h-screen grid grid-cols-2">
        <!-- Reset Animation and Information -->
        <div class="w-full h-full grid bg-sidebar-bg">
            <div class="w-fit h-fit m-auto grid justify-items-center">
                <div class="w-[600px] h-fit grid text-[18px] text-custom-gray font-pt-sans">
                    <p class="text-custom-dark-blue">Welcome to the,<br><span class="text-[32px] font-bold">Rocket Flood Admin Portal!</span></p>
                    <p class="mt-6 italic">If you have an exisiting admin portal account you can reset your password here.</p>
                    <p class="italic">If you don't have an account, please contact our support team to get started.<br><br>Thank you for using Rocket Flood!</p>
                </div>
                
                <LottieAnimation :animationData="ResetPasswordAnimation" :renderer="'canvas'" :width="'500px'" :height="'500px'" :speed="0.75" class="m-auto" />
            </div>
        </div>

        <div class="w-full h-full grid bg-white">
            <!-- Reset Password Form -->
            <form @submit.prevent="login" class="w-fit h-fit m-auto">
                <div class="grid gap-8 w-[400px] h-fit">
                    <!-- Logo -->
                    <img src="../../assets/purpleLogo64.png" alt="Logo" class="w-fit h-fit mx-auto">

                    <p class="w-fit h-fit mx-auto text-[32px] text-custom-black font-semibold">Reset Password</p>

                    <div class="w-full h-fit grid gap-4 py-8 relative">
                        <!-- Email -->
                        <span v-if="!token" class="p-input-icon-left">
                            <i class="pi pi-user" />
                            <InputText v-model="email" placeholder="Email" class="w-full h-[48px] rounded-[4px]" />
                        </span>

                        <div v-else class="w-full h-fit grid gap-4">
                            <!-- Token -->
                            <span class="p-input-icon-left">
                                <i class="pi pi-hashtag" />
                                <InputText v-model="form.token" placeholder="Token" class="w-full h-[48px] rounded-[4px]" />
                            </span>

                            <!-- Password -->
                            <span class="p-input-icon-left">
                                <i class="pi pi-lock" />
                                <InputText v-model="form.password" type="password" placeholder="New Password" class="w-full h-[48px] rounded-[4px] border-[#d3dbe3] border-[1px]" />
                            </span>
                        </div>

                        <a href="/login" class="right-0 bottom-0 absolute text-[16px] text-custom-purple underline">Login</a>
                    </div>

                    <!-- Submit Button -->
                    <input v-if="token" type="submit" value="Reset Password" style="border-radius: 4px;" class="w-full h-[48px] cursor-pointer bg-custom-purple text-[16px] text-white font-semibold shadow-newdrop">
                    <button v-else @click="requestToken()" type="button" class="w-full h-[48px] text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">Request Token</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import loading from '../components/loading.vue'
import textInput from '../components/textInput.vue'

import ResetPasswordAnimation from '../../assets/resetPassword.json'

import InputText from 'primevue/inputtext';
import Password from 'primevue/password';

export default {
    name: "Reset Password",
    data(){
        return {
            ResetPasswordAnimation,
            loading: false,
            token: false,
            email: "",
            form: {
                token: "",
                password: "",
                confirm_password: ""
            }
        }
    },
    methods: {
        async requestToken(){
            this.loading = true

            await axios.get('/api/reset-password/token/'+this.email)
            .then(response => {
                if(response.data.success){
                    this.$toast.add({ 
                        severity: 'success',
                        summary: 'Email Sent',
                        detail: response.data.message,
                        life: 3000
                    })
                    this.token = true
                }
            })
            .catch(error => {
                this.email = ""
                this.$toast.add({ 
                    severity: 'error',
                    summary: 'Validation Error',
                    detail: error.response.data.message,
                    life: 3000
                })
            })

            this.loading = false
        },
        async login(){
            this.loading = true
            this.form.confirm_password = this.form.password

            await axios.post('/api/reset-password', this.form)
            .then(response => {
                if(response.data.success){
                    this.$toast.add({ 
                        severity: 'success',
                        summary: 'Password Reset',
                        detail: response.data.message,
                        life: 2500
                    })
                    
                    this.$router.push({name: "Login"})
                }
            })
            .catch(error => {
                if(error.response.status == 400){
                    const keys = Object.keys(error.response.data.message)
                    keys.forEach(key => {
                        error.response.data.message[key].forEach(error => {
                            this.$toast.add({ 
                                severity: 'error',
                                summary: 'Validation Error',
                                detail: error,
                                life: 2500
                            })
                        })
                    })
                } else if(error.response.status == 401){
                    this.$toast.add({ 
                        severity: 'error',
                        summary: 'Token Error',
                        detail: error.response.data.message,
                        life: 2500
                    })
                }

                this.loading = false
            })
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