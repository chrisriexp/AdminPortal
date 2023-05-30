<template>
    <div class="w-full h-fit grid gap-6 pb-12">
        <!-- Header -->
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <p class="text-[32px] text-custom-black font-semibold">System Users</p>
            </div>

            <div class="w-fit h-full flex items-center gap-6 float-right">
                <!-- Save User -->
                <button  @click="saveUser" class="w-[48px] h-[48px] grid bg-white rounded-[2px] shadow-newdrop">
                    <Icon :icon="'fluent:save-24-filled'" height="24" class="m-auto text-custom-black" />
                </button>

                <!-- Delete User -->
                <button @click="$emit('deleteUser')" class="w-[48px] h-[48px] grid bg-white rounded-[2px] shadow-newdrop">
                    <Icon :icon="'fluent:delete-20-filled'" height="24" class="m-auto text-custom-red" />
                </button>

                <!-- Create New User -->
                <button @click="$emit('createUser')" class="w-[217px] h-[48px] grid justify-items-center text-[16px] text-white font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">
                    <div class="w-fit h-fit flex items-center gap-4 my-auto">
                        <Icon :icon="'ic:round-plus'" height="24" />
                        <p>Create User</p>
                    </div>
                </button>
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-8">
            <!-- Select User -->
            <div class="w-full h-fit grid gap-2">
                <p class="text-[16px] text-custom-black font-medium">Select User</p>
                <Dropdown @update:model-value="selectUser" v-model="user_id" :options="users" optionLabel="name" class="w-full h-[48px] flex items-center" />
            </div>

            <!-- User Role -->
            <div v-if="user_id.id" class="w-full h-fit grid gap-2">
                <p class="text-[16px] text-custom-black font-medium">Role</p>
                <Dropdown v-model="user.role" :options="roles" optionLabel="name" class="w-full h-[48px] flex items-center text-[14px]" />
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-8">
            <!-- User Name -->
            <div class="w-full h-fit grid gap-2">
                <p class="text-[16px] text-custom-black font-medium">Name</p>
                <InputText v-model="user.name" placeholder="User Name" class="w-full h-[48px] mx-auto rounded-[2px]" />
            </div>

            <!-- Project Members -->
            <div v-if="user_id.id" class="w-full h-fit grid gap-2">
                <p class="text-[16px] text-custom-black font-medium">Email</p>
                <InputText v-model="user.email" placeholder="User Email" class="w-full h-[48px] mx-auto rounded-[2px]" />
            </div>
        </div>

        <!-- Modules -->
        <div class="w-full h-fit flex items-center gap-8">
            <!-- Onboarding Portal -->
            <div class="w-full h-[52px] mx-auto px-4 flow-root bg-white rounded-[4px] shadow-newdrop">
                <div class="w-fit h-full flex items-center float-left">
                    <p class="text-[16px] text-custom-black font-medium">Onboarding Portal</p>
                </div>

                <div class="w-fit h-full flex items-center float-right">
                    <InputSwitch v-model="user.onboarding" />
                </div>
            </div>

            <!-- ROVER -->
            <div class="w-full h-[52px] mx-auto px-4 flow-root bg-white rounded-[4px] shadow-newdrop">
                <div class="w-fit h-full flex items-center float-left">
                    <p class="text-[16px] text-custom-black font-medium">ROVER</p>
                </div>

                <div class="w-fit h-full flex items-center float-right">
                    <InputSwitch v-model="user.rover" />
                </div>
            </div>

            <!-- REACT -->
            <div class="w-full h-[52px] mx-auto px-4 flow-root bg-white rounded-[4px] shadow-newdrop">
                <div class="w-fit h-full flex items-center float-left">
                    <p class="text-[16px] text-custom-black font-medium">REACT</p>
                </div>

                <div class="w-fit h-full flex items-center float-right">
                    <InputSwitch v-model="user.react" />
                </div>
            </div>
        </div>

        <div class="w-fit h-fit flex items-center gap-6">
            <button @click="$emit('updateUserPassword')" class="w-[232px] h-[48px] grid justify-items-center text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">
                <div class="w-fit h-fit flex items-center gap-6 my-auto">
                    <Icon :icon="'ic:round-update'" height="24" />
                    <p>Update Password</p>
                </div>
            </button>
        </div>
    </div>
</template>

<script>
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';
import InputText from 'primevue/inputtext';

import { Icon } from '@iconify/vue';

export default{
    name: "System Users",
    data(){
        return {
            users: [],
            user_id: {},
            user: {},
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
            role: {}
        }
    },
    async mounted(){
        this.$emit('loading')

        await axios.get('/api/member-list')
        .then(response => {
            this.users = response.data.users
        })

        this.user_id = this.users[0]

        await axios.get('/api/user/' + this.users[0].id)
        .then(response => {
            this.user = response.data.user

            this.roles.forEach(role => {
                if (role.code == this.user.role) {
                    this.user.role = role
                }
            })

            const modules = ['onboarding', 'rover', 'react']

            modules.forEach(module => {
                if (this.user[module] == 1) {
                    this.user[module] = true
                } else if (this.user[module] == 0) {
                    this.user[module] = false
                }
            })
        })

        this.$router.replace({params:{id: this.user_id.id}})
        this.$emit('loading')
    },
    methods: {
        async selectUser(value){
            this.$emit('loading')

            await axios.get('/api/user/'+value.id)
            .then(response => {
                this.user = response.data.user

                this.roles.forEach(role => {
                    if(role.code == this.user.role){
                        this.user.role = role
                    }
                })

                const modules = ['onboarding', 'rover', 'react']

                modules.forEach(module => {
                    if(this.user[module] == 1){
                        this.user[module] = true
                    }else if(this.user[module] == 0){
                        this.user[module] = false
                    }
                })
            })

            this.$router.replace({params:{id: this.user_id.id}})
            this.$emit('loading')
        },
        async saveUser(){
            let data = Object.assign({}, this.user)

            data.role = data.role.code

            await axios.post('/api/user/update', data)
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'User',
                    detail: response.data.message,
                    life: 2500
                })
            })
        }
    },
    components: {
        Dropdown,
        InputText,
        InputSwitch,
        Icon
    }
}
</script>
