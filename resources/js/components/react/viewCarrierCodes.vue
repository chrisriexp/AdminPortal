<template>
    <div class="w-[604px] h-fit p-6 grid gap-4 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div class="w-full h-full flow-root">
            <div class="w-fit max-w-[450px] grid float-left">
                <p class="text-[32px] truncate text-custom-black font-semibold">{{ agent.name }}</p>
            </div>

            <div class="w-fit h-full flex items-center float-right">
                <!-- Close Button -->
                <button @click="$emit('close')" class="w-[39px] h-[39px] grid bg-white shadow-newdrop rounded-[2px]">
                    <Icon :icon="'charm:cross'" height="24" class="text-custom-red m-auto" />
                </button>
            </div>
        </div>

        <!-- Carriers -->
        <div class="w-full h-fit max-h-[300px] grid grid-cols-2 gap-8 overflow-y-scroll">
            <div class="w-full h-fit grid gap-6">
                <div v-for="(carrier, index) in carriers" :key="index" :class="index % 2 == 0 ? 'grid' : 'hidden'" class="w-full h-fit gap-[5px]">
                    <div class="w-full h-fit flow-root">
                        <!-- Carrier Name -->
                        <p class="float-left text-[16px] text-custom-black font-medium">{{ carrier.name }}</p>
                        <!-- Rocket VS Direct Appointment -->
                        <InputSwitch v-model="agent[carrier.code].direct" class="h-[25px] flex items-center float-right" />
                    </div>

                    <!-- Carrier Code -->
                    <InputText v-model="agent[carrier.code].commission_id" :disabled="agent[carrier.code].direct ? true : false" class="h-[48px] text-[16px] text-custom-black" />
                </div>
            </div>

            <div class="w-full h-fit grid gap-6">
                <div v-for="(carrier, index) in carriers" :key="index" :class="index % 2 == 0 ? 'hidden' : 'grid'" class="w-full h-fit gap-[5px]">
                    <div class="w-full h-fit flow-root">
                        <!-- Carrier Name -->
                        <p class="float-left text-[16px] text-custom-black font-medium">{{ carrier.name }}</p>
                        <!-- Rocket VS Direct Appointment -->
                        <InputSwitch v-model="agent[carrier.code].direct" class="h-[25px] flex items-center float-right" />
                    </div>

                    <!-- Carrier Code -->
                    <InputText v-model="agent[carrier.code].commission_id" :disabled="agent[carrier.code].direct ? true : false" class="h-[48px] text-[16px] text-custom-black" />
                </div>
            </div>
        </div>

        <!-- Update Agent Appointments -->
        <button @click="updateAgent" class="my-4 w-full h-[48px] text-[16px] text-white font-seimbold bg-custom-purple rounded-[4px] shadow-newdrop">Update</button>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import InputSwitch from 'primevue/inputswitch';

import { Icon } from '@iconify/vue';

import carriers from '../../../assets/react_carriers.json'

export default{
    name: "View Sub Agent Carrier Codes",
    props: {
        rocket_id: String
    },
    data(){
        return {
            carriers,
            agent: {}
        }
    },
    async mounted(){
        this.$emit('loading')

        await axios.get('/api/react/sub-agent/carriers/'+this.rocket_id)
        .then(response => {
            this.agent = response.data.agent
        })

        this.$emit('loading')
    },
    methods: {
        async updateAgent(){
            await axios.put('/api/react/sub-agent/'+this.agent.rocket_id, this.agent)
            .then(response => {
                if(response.data.success){
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Agent',
                        detail: response.data.message,
                        life: 2500
                    })

                    this.$emit('close')
                }
            })
            .catch(error => {
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
                }
            })
        }
    },
    components: {
        InputText,
        InputSwitch,
        Icon
    }
}
</script>