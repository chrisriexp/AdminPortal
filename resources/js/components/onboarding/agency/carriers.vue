<template>
    <div class="w-full h-fit grid gap-8">
        <div class="w-full h-full flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <div class="w-fit h-fit grid"><p class="w-fit h-fit truncate max-w-[700px] text-[24px] text-custom-black font-semibold">{{ agency_name }} - {{ rocket_id }}</p></div>
            </div>

            <div class="w-fit h-full flex items-center float-right">
                <!-- Update Agency Button -->
                <button @click="updateAgency" class="w-[223px] h-[48px] text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">
                    <div class="w-fit h-fit m-auto flex items-center gap-4">
                        <Icon icon="ic:round-update" height="24" />
                        <p>Update Agency</p>
                    </div>
                </button>
            </div>
        </div>

        <div v-if="ready" class="w-full h-fit grid gap-6">
            <p class="text-[24px] text-custom-black font-medium">Rocket MGA Carrier Credentials</p>

            <div v-for="(carrier, index) in data" :key="index" class="w-full h-fit p-4 grid grid-cols-3 gap-6 text-[16px] text-custom-black bg-white rounded-[2px] border-[1px] border-custom-black border-opacity-10">
                <div class="w-full h-full grid gap-4">
                    <!-- Carrier Name -->
                    <div>
                        <p class="font-medum invisible">.</p>
                        <div class="h-[48px] w-fit grid">
                            <p class="m-auto text-[24px] text-custom-black font-medium">{{ carrier.name }}</p>
                        </div>
                    </div>

                    <!-- Appointment Disposition -->
                    <div>
                        <p class="font-medum">Appointment</p>
                        <div class="w-full h-[48px] grid grid-cols-2 bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                            <button @click="carrier.direct = true" :class="carrier.direct ? 'bg-custom-purple text-white' : 'text-custom-black'" class="w-full h-full grid">
                                <p class="text-[14px] font-medium m-auto">Direct</p>
                            </button>

                            <button @click="carrier.direct = false" :class="!carrier.direct ? 'bg-custom-purple text-white' : 'text-custom-black'" class="w-full h-full grid">
                                <p class="text-[14px] font-medium m-auto">RocketMGA</p>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="w-full h-full grid gap-4">
                    <!-- Carrier Username -->
                    <div>
                        <p class="font-medum">Portal - Username</p>
                        <InputText v-model="carrier.carrier_username" class="w-full h-[48px]" />
                    </div>

                    <!-- Commission ID -->
                    <div>
                        <p class="font-medum">Commission / Agent ID</p>
                        <InputText v-model="carrier.commission_id" class="w-full h-[48px]" />
                    </div>
                </div>

                <!-- Carrier UIP(s) -->
                <div class="w-full h-full grid gap-4">
                    <div v-for="(uip, index) in carrier.uip_fields" :key="index" class="w-full h-fit grid">
                        <p class="font-medum">UIP ({{ uip.name }})</p>
                        <InputText v-model="uip.value" class="w-full h-[48px]" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import InputSwitch from 'primevue/inputswitch';
import { Icon } from '@iconify/vue';
import carriers from '../../../../assets/mga_carriers.json'

export default {
    name: "Onboarding Agency - Carriers",
    props: {
        agency_name: String,
        rocket_id: String
    },
    data(){
        return{
            ready: false,
            carriers,
            data: {},
        }
    },
    async mounted(){
        await axios.get('/api/onboarding/agency/'+this.rocket_id+'/carriers')
        .then(response => {
            this.data = response.data.data
        })
        .catch(error => {
            if(error.response.status == 500){
                this.$toast.add({
                    severity: 'error',
                    summary: 'Internal Service Error',
                    detail: "Please contact a system admin",
                    life: 2500
                })
            }
        })
        
        this.ready = true
    },
    methods: {
        async updateAgency(){
            this.$emit('loading')

            const data = Object.assign({}, this.data)

            await axios.put('/api/onboarding/agency/'+this.rocket_id+'/carriers', {
                "carriers": data
            })
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Agency',
                    detail: response.data.message,
                    life: 2500
                })
            })

            this.$emit('loading')
        },
    },
    components: {
        InputText,
        Icon,
        InputSwitch
    }
}
</script>