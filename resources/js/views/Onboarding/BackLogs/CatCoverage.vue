<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Top Nav -->
    <div class="absolute z-30 w-full">
        <topNav class="fixed z-30" :active="'modules'" />
    </div>

    <div v-if="ready" class="w-full h-fit z-20 absolute pt-[110px] pb-[50px] px-10">
        <div class="w-full h-[48px] flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <p class="text-custom-black text-[24px] font-medium">Cat Coverage Appointment Back Logs</p>
            </div>

            <!-- Search Bar -->
            <div class="w-fit h-full flex items-center float-right">
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText v-model="search" placeholder="Enter agency name, agent name, or email" class="w-[403px] h-[48px] rounded-[4px]" />
                </span>
            </div>
        </div>

        <div class="w-full h-fit flex flex-wrap gap-[5%] mt-8">
            <!-- Information Required -->
            <div class="w-[30%] h-[129px] grid p-4 bg-custom-orange bg-opacity-20 rounded-[5px] shadow-newdrop relative">
                <p class="text-custom-orange text-[18px] font-semibold">{{ data.report[0].name }}</p>

                <p class="absolute bottom-4 right-4 text-custom-orange text-[24px] font-medium">{{ data.report[0].count }}</p>
            </div>

            <!-- Awaiting Agreement -->
            <div class="w-[30%] h-[129px] grid p-4 bg-custom-purple bg-opacity-20 rounded-[5px] shadow-newdrop relative">
                <p class="text-custom-purple text-[18px] font-semibold">{{ data.report[1].name }}</p>

                <p class="absolute bottom-4 right-4 text-custom-purple text-[24px] font-medium">{{ data.report[1].count }}</p>
            </div>

            <!-- Appointed -->
            <div class="w-[30%] h-[129px] grid p-4 bg-custom-red bg-opacity-20 rounded-[5px] shadow-newdrop relative">
                <p class="text-custom-red text-[18px] font-semibold">{{ data.report[2].name }}</p>

                <p class="absolute bottom-4 right-4 text-custom-red text-[24px] font-medium">{{ data.report[2].count }}</p>
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-6 mt-8">
            <div class="w-full h-fit grid gap-8">
                <!-- Completion Rate -->
                <div class="w-full h-fit grid gap-2">
                    <p class="text-[18px] font-medium"><span class="text-custom-purple">{{ data.completion_rate }}%</span> Completion Rate</p>
                    <div class="w-full h-[15px] flex items-center rounded-full bg-custom-gray bg-opacity-5"><div :class="'w-['+data.completion_rate+'%]'" class="h-full rounded-full bg-custom-purple"></div><img src="../../../../assets/progress_rocket.png" alt="Progress Rocket" class="ml-[-15px]"></div>
                </div>

                <!-- Agents -->
                <div class="w-full h-fit max-h-[500px] overflow-y-scroll grid gap-4">
                    <div @click="selected_agent = agent; selected_agent.data.eo_exp = moment(selected_agent.data.eo_exp).format('MM/DD/YYYY')" v-for="(agent, index) in filtered" :key="index" class="w-full h-[52px] grid px-4 border-[1px] border-custom-black border-opacity-10 rounded-[4px] cursor-pointer">
                        <div class="w-full h-fit my-auto grid grid-cols-2 gap-6">
                            <p class="text-custom-black font-medium text-[16px] truncate">{{ agent.data.agency_name }}</p>

                            <p class="text-[16px] text-custom-purple font-medium">{{ agent.status }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agent Data -->
            <div class="w-full h-fit grid gap-4">
                <div class="w-full h-full flow-root">
                    <div class="w-fit max-w-[70%] h-full flex items-center float-left">
                        <p class="truncate text-[18px] text-custom-black font-medium">{{ selected_agent.data.agency_name }}</p>
                    </div>
                    
                    <!-- Save Data Button -->
                    <div class="w-fit h-full flex items-center float-right">
                        <button @click="saveData" class="w-fit h-fit bg-custom-purple px-6 py-2 text-white font-medium text-[16px] rounded-[4px]">Save Data</button>
                    </div>
                </div>

                <!-- Agency Name and Agent Name -->
                <div class="w-full h-fit grid grid-cols-2 gap-8">
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Agency Name</p>
                        <InputText v-model="selected_agent.data.agency_name" class="w-full h-[48px]" />
                    </div>
                    
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Agent Name</p>
                        <InputText v-model="selected_agent.data.agent_name" class="w-full h-[48px]" />
                    </div>
                </div>

                <!-- Phone and Email -->
                <div class="w-full h-fit grid grid-cols-2 gap-8">
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Phone Number</p>
                        <InputMask v-model="selected_agent.data.phone" mask="(999) 999-9999" />
                    </div>
                    
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Email</p>
                        <InputText v-model="selected_agent.data.email" class="w-full h-[48px]" />
                    </div>
                </div>

                <!-- Address 1 and 2 -->
                <div class="w-full h-fit grid grid-cols-2 gap-8">
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Address 1</p>
                        <InputText v-model="selected_agent.data.address1" class="w-full h-[48px]" />
                    </div>
                    
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Address 2</p>
                        <InputText v-model="selected_agent.data.address2" class="w-full h-[48px]" />
                    </div>
                </div>

                <!-- City State Zip -->
                <div class="w-full h-fit grid grid-cols-3 gap-8">
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">City</p>
                        <InputText v-model="selected_agent.data.city" class="w-full h-[48px]" />
                    </div>
                    
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">State</p>
                        <InputText v-model="selected_agent.data.state" class="w-full h-[48px]" />
                    </div>

                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Zip</p>
                        <InputText v-model="selected_agent.data.zip" class="w-full h-[48px]" />
                    </div>
                </div>

                <!-- Agency Type and Tax ID -->
                <div class="w-full h-fit grid grid-cols-2 gap-8">
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Agency Type</p>
                        <Dropdown v-model="selected_agent.data.agency_type" :options="agencyTypes" optionLabel="name" placeholder="Select Agency Type" class="w-full h-[48px]" />
                    </div>
                    
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Tax ID</p>
                        <InputMask v-model="selected_agent.data.agency_tax_id" mask="99-9999999" />
                    </div>
                </div>

                <!-- Agency License and Agency NPN -->
                <div class="w-full h-fit grid grid-cols-2 gap-8">
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Agency License</p>
                        <InputText v-model="selected_agent.data.agency_license" class="w-full h-[48px]" />
                    </div>
                    
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Agent NPN</p>
                        <InputText v-model="selected_agent.data.agent_npn" class="w-full h-[48px]" />
                    </div>
                </div>

                <hr>
                <p class="text-[18px] text-custom-black my-2">E&O Information</p>

                <!-- EO Insuere and Policy -->
                <div class="w-full h-fit grid grid-cols-2 gap-8">
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Insurer</p>
                        <InputText v-model="selected_agent.data.eo_insurer" placeholder="E&O Insurer" class="w-full h-[48px]" />
                    </div>
                    
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Policy</p>
                        <InputText v-model="selected_agent.data.eo_policy" class="w-full h-[48px]" />
                    </div>
                </div>

                <!-- EO Limit and Exp -->
                <div class="w-full h-fit grid grid-cols-2 gap-8">
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Limit</p>
                        <InputNumber v-model="selected_agent.data.eo_limit" mode="currency" currency="USD" :min="500000" :max="10000000" :maxFractionDigits=0 class="w-full" />
                    </div>
                    
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-custom-black font-medium text-[16px] opacity-60">Exp Date</p>
                        <Calendar v-model="selected_agent.data.eo_exp" class="h-[48px]" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import topNav from '../../../components/topNav.vue'
import loading from '../../../components/loading.vue'
import InputText from 'primevue/inputtext';
import InputMask from 'primevue/inputmask';
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import InputNumber from 'primevue/inputnumber';
import agencyTypes from '../../../../assets/onboarding_agency_types.json';
import moment from 'moment'

export default{
    name: "Cat Coverage Appointment BackLogs",
    data(){
        return{
            ready: false,
            loading: false,
            agencyTypes,
            search: "",
            data:{
                agents: [],
                report: [],
                completion_rate: 0
            },
            selected_agent: {},
            filtered: []
        }
    },
    async created(){
        this.moment = moment
        this.loading = true

        await axios.get('/api/onboarding/backlogs/cat_coverage')
        .then(response => {
            console.log(response)
            const keys = Object.keys(this.data)

            keys.forEach(key => {
                this.data[key] = response.data[key]
            })
        })

        this.filtered = this.data.agents
        this.filtered[0].data.eo_exp = moment(this.filtered[0].data.eo_exp).format('MM/DD/YYYY')
        this.selected_agent = this.filtered[0]

        this.loading = false
        this.ready = true
    },
    watch: {
        search: async function(value){
            this.filtered = []

            this.data.agents.forEach(agent => {
                if(agent.data.agency_name.toLowerCase().includes(value.toLowerCase()) || agent.data.agent_name.toLowerCase().includes(value.toLowerCase()) || agent.data.email.toLowerCase().includes(value.toLowerCase()) ){
                    this.filtered.push(agent)
                }
            })
        }
    },
    methods: {
        async saveData(){
            await axios.post('/api/onboarding/backlogs/cat_coverage', {"agent": this.selected_agent})
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Agency',
                    detail: response.data.message,
                    life: 2500
                })
            })
        }
    },
    components: {
        topNav,
        loading,
        InputText,
        InputMask,
        Dropdown,
        Calendar,
        InputNumber
    }
}
</script>