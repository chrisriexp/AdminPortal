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
        <div class="w-full h-fit grid grid-cols-2 gap-8">
            <div class="w-full h-fit grid gap-6">
                <div v-for="(carrier, index) in carriers" :key="index" :class="index % 2 == 0 ? 'grid' : 'hidden'" class="w-full h-fit gap-[5px]">
                    <div class="w-full h-fit flow-root">
                        <!-- Carrier Name -->
                        <p class="float-left text-[16px] text-custom-black font-medium">{{ carrier.name }}</p>
                        <!-- Rocket VS Direct Appointment -->
                        <InputSwitch v-model="agent[carrier.code].rocket" class="h-[25px] flex items-center float-right" />
                    </div>

                    <!-- Carrier Code -->
                    <InputText v-model="agent[carrier.code].code" :disabled="agent[carrier.code].rocket ? false : true" class="h-[48px] text-[16px] text-custom-black" />
                </div>
            </div>

            <div class="w-full h-fit grid gap-6">
                <div v-for="(carrier, index) in carriers" :key="index" :class="index % 2 == 0 ? 'hidden' : 'grid'" class="w-full h-fit gap-[5px]">
                    <div class="w-full h-fit flow-root">
                        <!-- Carrier Name -->
                        <p class="float-left text-[16px] text-custom-black font-medium">{{ carrier.name }}</p>
                        <!-- Rocket VS Direct Appointment -->
                        <InputSwitch v-model="agent[carrier.code].rocket" class="h-[25px] flex items-center float-right" />
                    </div>

                    <!-- Carrier Code -->
                    <InputText v-model="agent[carrier.code].code" :disabled="agent[carrier.code].rocket ? false : true" class="h-[48px] text-[16px] text-custom-black" />
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
        agent: Object
    },
    data(){
        return {
            carriers
        }
    },
    methods: {
        async updateAgent(){
            await axios.put('/api/react/sub-agent/'+this.agent.id, this.agent)
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