<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Top Nav -->
    <div class="absolute z-30 w-full">
        <topNav class="fixed z-30" :active="'modules'" />
    </div>

    <div class="w-full h-screen z-20 absolute">
        <!-- Sidebar -->
        <div class="w-[305px] h-full grid bg-sidebar-bg z-20 absolute">
            <div class="w-full max-h-[800px] h-fit grid gap-4 mt-[100px] px-6 overflow-y-scroll text-[16px]">
                <p class="text-[24px] text-custom-black font-medium">Filter</p>

                <!-- Sort -->
                <Dropdown v-model="filters.sortBy" :options="sortBy" optionLabel="name" class="w-full h-[40px] flex items-center" >
                    <template #value="slotProps">
                        <div class="grid">
                            <p class="truncate flex items-center gap-2"><span class="text-[16px]  text-custom-black font-medium flex items-center gap-2"><i class="pi pi-filter-fill"></i>Sort:</span> {{ slotProps.value.name }}</p>
                        </div>
                    </template>
                </Dropdown>

                <!-- Onboarding Stages -->
                <MultiSelect v-model="filters.stages" :options="stages" optionLabel="name" placeholder="Onboarding Stages" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Onboarding Stages</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Onboarding Paperwork Stages -->
                <MultiSelect v-if="showPaperworkStages" v-model="filters.paperworkStages" :options="paperworkStages" optionLabel="name" placeholder="Paperwork Stages" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Paperwork Stages</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Onboarding Training Completed -->
                <MultiSelect v-if="showTrainingComplete" v-model="filters.training_completed" :options="training_completed" optionLabel="name" placeholder="Training" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Training</p>
                        </div>
                    </template>
                </MultiSelect>

                <div class="w-full h-fit grid gap-2">
                    <!-- Apply Filters -->
                    <button @click="applyFilters" class="w-full h-[32px] text-white font-medium text-[16px] bg-custom-purple rounded-[4px] shadow-newdrop">Apply Filters</button>
                    <!-- Clear Filters -->
                    <button @click="clearFilter" class="text-[14px] text-custom-purple font-medium">Clear Filters</button>
                </div>
            </div>
        </div>

        <div class="w-full h-full z-10 absolute pt-[110px] pb-12 pl-[350px] pr-[50px] overflow-y-scroll">
            <div class="w-full h-fit flow-root">
                <div class="w-fit h-fit grid gap-2 float-left">
                    <p class="text-[32px] text-custom-black font-semibold">MGA Onboarding</p>
                    <p class="text-custom-black text-[16px] opacity-60">Here are all agents onboarding with Rocket MGA, count: <span class="text-custom-purple">{{ searchView.length }}</span></p>
                </div>

                <div class="w-fit h-fit flex items-center gap-6 float-right">
                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText v-model="search" placeholder="Enter agency name, agent name, or email" class="w-[403px] h-[48px] rounded-[4px]" />
                    </span>
                </div>
            </div>

            <!-- Filter Chips -->
            <div class="w-fit max-w-full flex flex-wrap items-center gap-2 mt-4">
                <p class="text-[14px] text-custom-black">Sort By:</p>
                <p class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ filters.sortBy.name }}</p>
                <p v-if="filters.stages.length > 0" class="text-[14px] text-custom-black">Onboarding Stages:</p>
                <p v-for="(item, index) in filters.stages" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
                <p v-if="filters.paperworkStages.length > 0 && showPaperworkStages" class="text-[14px] text-custom-black">Paperwork Stages:</p>
                <p v-for="(item, index) in filters.paperworkStages" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full"><span v-if="showPaperworkStages">{{ item.name }}</span></p>
                <p v-if="filters.training_completed.length > 0 && showTrainingComplete" class="text-[14px] text-custom-black">Training:</p>
                <p v-for="(item, index) in filters.training_completed" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full"><span v-if="showTrainingComplete">{{ item.name }}</span></p>
            </div>

            <div v-if="searchView.length > 0" class="w-full h-fit grid gap-2 mt-6">
                <!-- List Headers -->
                <div class="w-full h-fit grid grid-cols-5 gap-8 px-4 py-2 text-[16px] text-custom-black opacity-50 border-b-[1px] border-custom-black border-opacity-10">
                    <p>Agency Name</p>
                    <p>Agent Name</p>
                    <p>Email</p>
                    <p class="col-span-2">Stage Based Information</p>
                </div>

                <a v-for="(agent, index) in searchView" :key="index" :href="'https://onboarding.rocketmga.com/admin/agency/'+agent.rocket_id" target="_blank" class="w-full h-[52px] grid grid-cols-5 gap-8 px-4 text-[16px] text-custom-black font-medium border-[1px] border-custom-black border-opacity-10 rounded-[2px] relative">
                    <p class="my-auto truncate pr-4">{{ agent.agency_name }}</p>
                    <p class="my-auto truncate pr-4">{{ agent.agent_name }}</p>
                    <p class="my-auto truncate pr-4">{{ agent.email }}</p>
                    
                    <!-- Stage Based Information -->
                    <p v-if="agent.onboarding_stage == 'incomplete' || agent.onboarding_stage == 'appointed'" class="my-auto truncate pr-4 text-custom-purple col-span-2">{{ agent.stage }}</p>
                    <img v-if="agent.onboarding_stage == 'completed'" :src="`/images/approval-${agent.approvalCount}.png`" alt="Approval Progress" class="col-span-2 my-auto">
                    <div v-if="agent.onboarding_stage == 'approved'" class="w-full col-span-2 h-fit my-auto grid grid-cols-2 gap-8">
                        <p>Training Complete: <span :class="agent.training_completed ? 'text-custom-purple' : 'opacity-50'">{{ agent.training_completed ? 'Yes' : 'No' }}</span></p>
                        <p>Carrier UIP(s): <span class="text-custom-purple">{{ agent.appointed }}</span>/{{ agent.total }}</p>
                    </div>
                </a>
            </div>

            <!-- No Agent Found -->
            <div v-else class=" w-full h-full grid">
                <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'250px'" :height="'250px'" :speed="0.75" class="m-auto" />
            </div>
        </div>
    </div>
</template>

<script>
import topNav from '../../components/topNav.vue'
import loading from '../../components/loading.vue'
import Dropdown from 'primevue/dropdown';
import MultiSelect from 'primevue/multiselect';
import InputText from 'primevue/inputtext';

import { Icon } from '@iconify/vue';

import NotFoundAnimation from '../../../assets/newNotFound.json'

export default{
    name: "Onboarding Portal - Agents",
    data(){
        return{
            NotFoundAnimation,
            loading: true,
            showPaperworkStages: true,
            showTrainingComplete: true,
            search: "",
            filters: {
                sortBy: { name: 'Date Created', code: 'created_at' },
                stages: [
                    {
                        name: "Incomplete Paperwork",
                        code: "incomplete"
                    },
                    {
                        name: "Completed Paperwork - Pending Approval",
                        code: "completed"
                    },
                    {
                        name: "Approved - Pending Carriers",
                        code: "approved"
                    }
                ],
                paperworkStages: [
                    {
                        name: "Agency Information",
                        code: "agency"
                    },
                    {
                        name: "Carrier Information",
                        code: "carrier"
                    },
                    {
                        name: "Entity Information",
                        code: "entity"
                    },
                    {
                        name: "Agreement Signing",
                        code: "agreement"
                    },
                    {
                        name: "Submitted",
                        code: "submitted"
                    }
                ],
                training_completed: [
                    {
                        name: "Complete",
                        code: true
                    },
                    {
                        name: "Incomplete",
                        code: false
                    }
                ]
            },
            stages: [
                    {
                        name: "Incomplete Paperwork",
                        code: "incomplete"
                    },
                    {
                        name: "Completed Paperwork - Pending Approval",
                        code: "completed"
                    },
                    {
                        name: "Approved - Pending Carriers",
                        code: "approved"
                    },
                    {
                        name: "Appointed",
                        code: "appointed"
                    }
            ],
            paperworkStages: [
                {
                    name: "Agency Information",
                    code: "agency"
                },
                {
                    name: "Carrier Information",
                    code: "carrier"
                },
                {
                    name: "Entity Information",
                    code: "entity"
                },
                {
                    name: "Agreement Signing",
                    code: "agreement"
                },
                {
                    name: "Submitted",
                    code: "submitted"
                }
            ],
            training_completed: [
                {
                    name: "Complete",
                    code: true
                },
                {
                    name: "Incomplete",
                    code: false
                }
            ],
            sortBy: [
                { name: 'Date Created', code: 'created_at' },
                { name: 'Agency Name', code: 'agency_name' },
                { name: 'Agent Name', code: 'agent_name' },
                { name: 'Onboarding Stage', code: 'onboarding_stage' },
                { name: 'Date Submitted', code: 'submitted_at' },
                { name: 'Date Approved', code: 'approved_at' },
                { name: 'Date Appointed', code: 'appointed_at' },
            ],
            agents: [],
            searchView: []
        }
    },
    async created(){
        await axios.post('/api/onboarding', {"filters": this.filters})
        .then(response => {
            this.agents = response.data.agents
            this.searchView = this.agents
        })

        this.loading = false
    },
    watch: {
        'filters.stages': async function(){
            let stages = []

            this.filters.stages.forEach(stage => {
                stages.push(stage.code)
            })

            if(stages.includes('incomplete')){
                this.showPaperworkStages = true
            }else{
                this.showPaperworkStages = false
            }

            if(stages.includes('approved')){
                this.showTrainingComplete = true
            }else{
                this.showTrainingComplete = false
            }
        },
        search: async function(value){
            this.searchView = []

            this.agents.forEach(agent => {
                if(agent.agency_name.toLowerCase().includes(value.toLowerCase()) || agent.agent_name.toLowerCase().includes(value.toLowerCase()) || agent.email.toLowerCase().includes(value.toLowerCase())){
                    this.searchView.push(agent)
                }
            })
        }
    },
    methods: {
        async clearFilter(){
            this.loading = true
            const keys = Object.keys(this.filters)

            keys.forEach(key => {
                this.filters[key] = this[key]
            })

            this.filters.sortBy = { name: 'Date Created', code: 'created_at' }
            this.filters.stages = [
                {
                    name: "Incomplete Paperwork",
                    code: "incomplete"
                },
                {
                    name: "Completed Paperwork - Pending Approval",
                    code: "completed"
                },
                {
                    name: "Approved - Pending Carriers",
                    code: "approved"
                }
            ]

            await axios.post('/api/onboarding', {"filters": this.filters})
            .then(response => {
                this.agents = response.data.agents
                this.searchView = this.agents
            })

            this.loading = false
        },
        async applyFilters(){
            this.loading = true

            await axios.post('/api/onboarding', {"filters": this.filters})
            .then(response => {
                this.agents = response.data.agents
                this.searchView = this.agents
            })

            this.loading = false
        }
    },
    components: {
        topNav,
        loading,
        Dropdown,
        MultiSelect,
        InputText,
        Icon
    }
}
</script>