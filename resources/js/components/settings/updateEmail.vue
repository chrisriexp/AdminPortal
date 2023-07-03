<template>
    <div v-if="ready" class="w-[604px] h-fit grid gap-6 p-10 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                <p>Update Email</p>
            </div>

            <!-- Close Create Project -->
            <div @click="$emit('close')" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><Icon :icon="'charm:cross'" height="32" class="m-auto text-custom-red" /></div>
        </div>
        
        <!-- New Email -->
        <InputText v-model="email" placeholder="Email *" class="h-[48px] w-full" />

        <!-- Password -->
        <InputText v-model="password" placeholder="Password *" type="password" class="h-[48px] w-full rounded-[4px] border-[#d3dbe3] border-[1px]" />

        <button @click="updateEmail" class="w-full h-[48px] text-white text-[16p] font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">Update Email</button>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import { Icon } from '@iconify/vue';

export default{
    name: "Update Email",
    data(){
        return {
            ready: false,
            email: "",
            password: ""
        }
    },
    async mounted(){
        await axios.get('/api/user')
        .then(response => {
            this.email = response.data.email
        })

        this.ready = true
    },
    methods: {
        async updateEmail () {
            await axios.post('/api/resetEmail', {
                "email": this.email,
                "confirm_email": this.email,
                "password": this.password
            })
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Email Update',
                    detail: response.data.message,
                    life: 2500
                })

                this.$emit('close')
                location.reload()
            })
            .catch(error => {
                if(error.response.status == 400){
                    const errorKeys = Object.keys(error.response.data.message)

                    errorKeys.forEach(item => {
                        error.response.data.message[item].forEach(errorItem => {
                            this.$toast.add({
                                severity: 'error',
                                summary: 'Email Update',
                                detail: errorItem,
                                life: 2500
                            })
                        })
                    })
                }else if(error.response.status == 401){
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Email Update',
                        detail: error.response.data.message,
                        life: 2500
                    })
                }
            })
        }
    },
    components: {
        Icon,
        InputText,
        Password
    }
}
</script>