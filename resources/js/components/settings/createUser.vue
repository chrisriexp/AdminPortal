<template>
    <div class="w-[604px] h-fit grid gap-6 p-10 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                <p>Create User</p>
            </div>

            <!-- Close Create Project -->
            <div @click="$emit('close')" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><Icon :icon="'charm:cross'" height="32" class="m-auto text-custom-red" /></div>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-6">
            <!-- Name -->
            <InputText v-model="user.name" placeholder="Name *" />

            <!-- Role -->
            <Dropdown v-model="role" :options="roles" optionLabel="name" placeholder="Role *" class="w-full h-[48px] flex items-center text-[14px]" />
        </div>

        <!-- Email -->
        <InputText v-model="user.email" placeholder="Email *" />

        <!-- Password -->
        <InputText v-model="user.password" placeholder="Password *" type="password" class="h-[48px] w-full rounded-[4px] border-[#d3dbe3] border-[1px]" />

        <button @click="createUser" class="w-full h-[48px] text-white text-[16p] font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">Create User</button>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Dropdown from 'primevue/dropdown';
import { Icon } from '@iconify/vue';

export default{
    name: 'Create User',
    data(){
        return {
            ready: false,
            roles: [
                {
                    name: "User",
                    code: "user"
                },
                {
                    name: "Admin",
                    code: "admin"
                },
                {
                    name: "Super Admin",
                    code: "super-admin"
                }
            ],
            user: {
                name: "",
                role: "",
                email: "",
                password: "",
                confirm_password: ""
            },
            role: {}
        }
    },
    async mounted(){
        this.ready = true
    },
    watch: {
        role: async function(value){
            this.user.role = value.code
        }
    },
    methods: {
        async createUser() {
            this.$emit('loading')
            this.user.confirm_password = this.user.password
            await axios.post('/api/register', this.user)
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Email Update',
                    detail: response.data.message,
                    life: 2500
                })

                location.reload()
            })
            .catch(error => {
                this.$emit('loading')
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
                }
            })
        }
    },
    components: {
        InputText,
        Password,
        Dropdown,
        Icon
    }
}
</script>