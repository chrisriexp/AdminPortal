<template>
    <div class="w-[604px] h-fit p-6 grid gap-4 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div class="w-full h-full flow-root">
            <div class="w-fit h-full flex items-center gap-4 float-left">
                <p class="text-[32px] truncate text-custom-black font-semibold">Sub Agent</p>
            </div>

            <div class="w-fit h-full flex items-center float-right">
                <!-- Close Button -->
                <button @click="$emit('close')" class="w-[39px] h-[39px] grid bg-white shadow-newdrop rounded-[2px]">
                    <Icon :icon="'charm:cross'" height="24" class="text-custom-red m-auto" />
                </button>
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-8">
            <!-- Agency Name -->
            <div class="w-full h-fit grid gap-[5px]">
                <p class="float-left text-[16px] text-custom-black font-medium">Name</p>
                <InputText v-model="agent.name" class="h-[48px] text-[16px] text-custom-black" />
            </div>

            <!-- Agency Rocket ID -->
            <div class="w-full h-fit grid gap-[5px]">
                <p class="float-left text-[16px] text-custom-black font-medium">Rocket ID</p>
                <InputText v-model="agent.rocket_id" class="h-[48px] text-[16px] text-custom-black" />
            </div>
        </div>

        <!-- Carriers -->
        <div class="w-full h-fit max-h-[300px] grid grid-cols-2 gap-8 overflow-y-scroll">
            <div class="w-full h-fit grid gap-6">
                <div v-for="(carrier, index) in carriers" :key="index" :class="index % 2 == 0 ? 'grid' : 'hidden'" class="w-full h-fit gap-[5px]">
                    <p class="float-left text-[16px] text-custom-black font-medium">{{ carrier.name }}</p>

                    <!-- Carrier Code -->
                    <InputText v-model="agent[carrier.code]" class="h-[48px] text-[16px] text-custom-black" />
                </div>
            </div>

            <div class="w-full h-fit grid gap-6">
                <div v-for="(carrier, index) in carriers" :key="index" :class="index % 2 == 0 ? 'hidden' : 'grid'" class="w-full h-fit gap-[5px]">
                    <p class="float-left text-[16px] text-custom-black font-medium">{{ carrier.name }}</p>

                    <!-- Carrier Code -->
                    <InputText v-model="agent[carrier.code]" class="h-[48px] text-[16px] text-custom-black" />
                </div>
            </div>
        </div>

        <div class="w-full h-fit grid">
            <!-- Add Sub Agent -->
            <button @click="addAgent" class="my-4 w-full h-[48px] text-[16px] text-white font-seimbold bg-custom-purple rounded-[4px] shadow-newdrop">Add</button>
            <!-- Back to Add Menu -->
            <button @click="$emit('back')" class="text-[16px] text-custom-purple font-medium underline">Back To Select Menu</button>
        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import carriers from '../../../assets/react_carriers.json'

import { Icon } from '@iconify/vue';

export default{
    name: "Add Sub Agent",
    data(){
        return {
            carriers,
            agent: {
                name: "",
                rocket_id: "",
                aon: "",
                beyond: "",
                cat: "",
                dual: "",
                flow: "",
                neptune: "",
                palomar: "",
                sterling: "",
                wright: ""
            }
        }
    },
    methods: {
        async addAgent(){
            this.$emit('loading')

            await axios.post('/api/react/sub-agent', this.agent)
            .then(response => {
                if(response.data.success){
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Agent',
                        detail: response.data.message,
                        life: 2500
                    })

                    this.$emit('loading')
                    this.$emit('update')
                }
            })
            .catch(error => {
                this.$emit('loading')
                
                if(error.response.status == 400){
                    const errors = Object.keys(error.response.data.message)

                    errors.forEach(item => {
                        error.response.data.message[item].forEach(message => {
                            this.$toast.add({
                                severity: 'error',
                                summary: 'Agent Creation',
                                detail: message,
                                life: 3000
                            })
                        })
                    })
                } else if (error.response.status == 422){
                    this.$toast.add({
                        severity: 'warn',
                        summary: 'Agent Creation',
                        detail: error.response.data.message,
                        life: 3000
                    })
                }
            })
        }
    },
    components: {
        InputText,
        Icon
    }
}
</script>