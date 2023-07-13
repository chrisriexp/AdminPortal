<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Top Nav -->
    <div class="absolute z-30 w-full">
        <topNav class="fixed z-30" :active="'modules'" />
    </div>

    <!-- Agent Information Sidebar -->
    <Sidebar :visible="agent ? true : false" position="right" :showCloseIcon="false"  class="bg-sidebar-bg z-20">
        <div class="w-full h-fit grid gap-4 mb-12 text-custom-black text-[16px]">
            <!-- Close Button -->
            <div class="w-full h-fit flow-root"><Icon @click="agent = null" icon="ph:x-bold" height="24" class="text-custom-black opacity-60 cursor-pointer hover:text-custom-red float-right" /></div>

            <!-- Agency Name -->
            <p class="font-medium text-custom-purple text-[18px]">{{ agent.agency_name }}</p>

            <!-- Phone -->
            <div class="w-full h-fit grid">
                <p class="font-medium">Phone</p>
                <InputMask class="border-none p-0 disabled:opacity-100 bg-sidebar-bg" v-model="agent.phone" disabled mask="(999) 999-9999" placeholder="(999) 999-9999" />
            </div>

            <!-- Email -->
            <div class="w-full h-fit grid">
                <p class="font-medium">Email</p>
                <p>{{ agent.email }}</p>
            </div>

            <!-- Rocket Rep -->
            <div class="w-full h-fit grid">
                <p class="font-medium">Rocket Rep</p>
                <Dropdown :disabled="loading" v-model="agent.rocket_rep" :options="reps" optionLabel="name" class="w-full h-[48px]" >
                    <template #value="slotProps">
                        <div class="grid">
                            <p class="truncate">{{ slotProps.value.name }}</p>
                        </div>
                    </template>
                </Dropdown>
            </div>

            <!-- Onboarding Source -->
            <div class="w-full h-fit grid">
                <p class="font-medium">Onboarding Source</p>
                <Dropdown :disabled="loading" v-model="agent.source" :options="sources" optionLabel="name" class="w-full h-[48px]" >
                    <template #value="slotProps">
                        <div class="grid">
                            <p class="truncate">{{ slotProps.value.name }}</p>
                        </div>
                    </template>
                </Dropdown>
            </div>

            <!-- Follow Up Date -->
            <div class="w-full h-fit grid">
                <p class="font-medium">Follow Up Date</p>
                <Calendar :disabled="loading" v-model="agent.follow_up_date" showIcon class="h-[48px]" />
            </div>

            <!-- Save and Close Buttons -->
            <button @click="updateAgent" :disabled="loading" class="text-white text-[16px] font-medium py-[4px] bg-custom-purple rounded-[4px] shadow-newdrop">Save Updates</button>

            <!-- New Follow Up Log -->
            <div class="w-full h-fit grid gap-2">
                <p class="font-medium">Follow Up Log</p>
                <Editor :disabled="loading" v-model="follow_up_log" editorStyle="height: auto; font-size: 16px; min-height: 80px" class="w-full" >
                    <template v-slot:toolbar>
                        <span class="ql-formats flex items-center h-[30px]">
                            <button v-tooltip.bottom="'Bold'" class="ql-bold"></button>
                            <button v-tooltip.bottom="'Italic'" class="ql-italic"></button>
                            <button v-tooltip.bottom="'Underline'" class="ql-underline"></button>
                            <button v-tooltip.bottom="'Image'" class="ql-list" value="ordered"></button>
                            <button v-tooltip.bottom="'Image'" class="ql-list" value="bullet"></button>
                            <button v-tooltip.bottom="'Link'" class="ql-link"></button>
                            <button v-tooltip.bottom="'Code Block'" class="ql-code-block"></button>
                            <button v-tooltip.bottom="'Image'" class="ql-image"></button>
                        </span>
                    </template>
                </Editor>
            </div>

            <button :disabled="loading" @click="followUpLog" class="text-white text-[16px] font-medium py-[4px] bg-custom-purple rounded-[4px] shadow-newdrop">Save Follow Up Log</button>
        
            <!-- Existing Follow Up Logs -->
            <p class="font-medium">Existing Follow Up Logs</p>

            <!-- No Logs Found -->
            <div v-if="agent.follow_up_logs && agent.follow_up_logs.length == 0" class=" w-full h-full grid">
                <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'125px'" :height="'125px'" :speed="0.75" class="m-auto" />
            </div>

            <div v-else v-for="(log, index) in agent.follow_up_logs" :key="index" class="w-full h-fit p-2 flow root bg-white rounded-[2px] border-[1px] border-custom-black border-opacity-10">
                <div class="w-full h-fit grid float-left comment">
                    <!-- User Name -->
                    <p class="text-[14px] text-custom-black font-medium opacity-80 float-left">{{ log.by }}</p>

                    <!-- Comment -->
                    <Editor readonly v-model="log.log" id="newTaskDesc" editorStyle="height: auto; font-size: 14px;" class="max-w-[920px] text-[14px] opacity-60 text-custom-black" >
                        <template v-slot:toolbar>
                            <span class="ql-formats flex items-center">
                            </span>
                        </template>
                    </Editor>

                    <!-- Comment Date -->
                    <p class="text-[12px] text-custom-black opacity-60 float-right">{{ moment(log.created_at).format("dddd, MMMM Do YYYY, h:mm a") }}</p>
                </div>
            </div>
            
        </div>
    </Sidebar>

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

                <!-- Rocket Rep -->
                <MultiSelect v-model="filters.reps" :options="reps" optionLabel="name" placeholder="Rocket Rep" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Rocket Rep</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Source -->
                <MultiSelect v-model="filters.sources" :options="sources" optionLabel="name" placeholder="Source" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Source</p>
                        </div>
                    </template>
                </MultiSelect>

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
                <p v-if="filters.reps.length > 0" class="text-[14px] text-custom-black">Rocket Reps:</p>
                <p v-for="(item, index) in filters.reps" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
                <p v-if="filters.sources.length > 0" class="text-[14px] text-custom-black">Onboarding Sources:</p>
                <p v-for="(item, index) in filters.sources" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
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

                <!-- Link to Onboarding Agency -->
                <div v-for="(agent, index) in searchView" :key="index" class="w-full h-fit grid relative">
                    <a :href="agent.onboarding_stage == 'completed' ? '/onboarding/agency/'+agent.rocket_id+'/approval' : '/onboarding/agency/'+agent.rocket_id+'/agency'" class="w-full h-[52px] grid grid-cols-5 gap-8 px-4 text-[16px] text-custom-black font-medium border-[1px] border-custom-black border-opacity-10 rounded-[2px] relative">
                        <p class="my-auto truncate pr-4">{{ agent.agency_name }}</p>
                        <p class="my-auto truncate pr-4">{{ agent.agent_name }}</p>
                        <p class="my-auto truncate pr-4">{{ agent.email }}</p>
                        
                        <!-- Stage Based Information -->
                        <div v-if="agent.onboarding_stage == 'prospect' || agent.prospect || agent.onboarding_stage == 'incomplete' || agent.onboarding_stage == 'appointed'" class="w-full col-span-2 h-fit my-auto grid grid-cols-2 gap-8">
                            <p class="my-auto truncate pr-4 text-custom-purple">{{ agent.onboarding_stage == 'prospect' || agent.prospect ? 'Prospect' : agent.stage }}</p>

                            <p v-if="agent.onboarding_stage != 'appointed'" :class="moment(agent.follow_up_date).isBefore(new Date()) ? 'text-custom-red' : ''" class="my-auto truncate pr-4">{{ moment(agent.follow_up_date).format('ddd, MMM DD') }}</p>
                        </div>
                        <img v-else-if="agent.onboarding_stage == 'completed'" :src="`/images/approval-${agent.approvalCount}.png`" alt="Approval Progress" class="col-span-2 my-auto">
                        <div v-else-if="agent.onboarding_stage == 'approved'" class="w-full col-span-2 h-fit my-auto grid grid-cols-2 gap-8">
                            <p>Training Complete: <span :class="agent.training_completed ? 'text-custom-purple' : 'opacity-50'">{{ agent.training_completed ? 'Yes' : 'No' }}</span></p>
                            <p>Carrier UIP(s): <span class="text-custom-purple">{{ agent.appointed }}</span>/{{ agent.total }}</p>
                        </div>
                    </a>

                    <!-- Agency Information Overlay -->
                    <div class="w-[24px] h-[52px] grid absolute left-[-30px]">
                        <Icon @click="() => {this.agent = agent}" icon="streamline:interface-user-profile-focus-close-geometric-human-person-profile-focus-user" height="24" class="m-auto text-custom-black cursor-pointer hover:text-custom-purple" />
                    </div>
                </div>
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
import InputMask from 'primevue/inputmask';
import Sidebar from 'primevue/sidebar';
import Calendar from 'primevue/calendar';
import Editor from 'primevue/editor';

import { Icon } from '@iconify/vue';

import NotFoundAnimation from '../../../assets/newNotFound.json'
import RocketReps from '../../../assets/marketing_reps.json'
import onboardingSources from '../../../assets/onboardingSources.json'
import moment from 'moment';

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
                        name: "Prospects",
                        code: "prospect"
                    },
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
                paperworkStages: [],
                training_completed: [],
                reps: [],
                sources: []
            },
            stages: [
                {
                    name: "Prospects",
                    code: "prospect"
                },
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
                { name: 'Follow up Date', code: 'follow_up_date' },
                { name: 'Agency Name', code: 'agency_name' },
                { name: 'Agent Name', code: 'agent_name' },
                { name: 'Onboarding Stage', code: 'onboarding_stage' },
                { name: 'Date Submitted', code: 'submitted_at' },
                { name: 'Date Approved', code: 'approved_at' },
                { name: 'Date Appointed', code: 'appointed_at' },
            ],
            reps: RocketReps,
            sources: onboardingSources,
            agents: [],
            searchView: [],
            agent: null,
            follow_up_log: {}
        }
    },
    async created(){
        this.moment = moment

        const filterKeys = Object.keys(this.filters)

        filterKeys.forEach(key =>  {
            if(key != 'sortBy' && key != 'stages'){
                this.filters[key] = this[key]
            }
        })

        // Check for filters in the url query
        const filterArrs = ['stages', 'paperworkStages', 'training_completed', 'reps', 'sources']
        const keys = Object.keys(this.$route.query)

        keys.forEach(key => {
            if(filterArrs.includes(key)){
                this.filters[key] = []

                if(Array.isArray(this.$route.query[key])){
                    this.$route.query[key].forEach(value => {
                        this[key].forEach(keyValue => {
                            if(keyValue.code == value){
                                this.filters[key].push(keyValue)
                            }else if(value == 'true' && keyValue.code == true){
                                this.filters[key].push(keyValue)
                            }else if(value == 'false' && keyValue.code == false){
                                this.filters[key].push(keyValue)
                            }
                        })
                    })
                }else{
                    this[key].forEach(keyValue => {
                        if(keyValue.code == this.$route.query[key]){
                            this.filters[key].push(keyValue)
                        }
                    })
                }
            }else{
                if(key != 'search'){
                    this[key].forEach(value => {
                        if(value.code == this.$route.query[key]){
                            this.filters[key] = value
                        }
                    })
                }
            }
        })

        await axios.post('/api/onboarding', {"filters": this.filters})
        .then(response => {
            this.agents = response.data.agents
            this.searchView = this.agents
        })

        
        this.agents.forEach(agent => {
            // Update All Agent Sources to an Object
            this.sources.forEach(source => {
                if(source.code == agent.source){
                    agent.source = source
                }
            })

            // Update Follow Up Date
            if(agent.follow_up_date){
                agent.follow_up_date = moment(agent.follow_up_date).format('MM/DD/YYYY')
            }
        })

        // Update Search with search value in query
        if(this.$route.query.search){
            this.search = this.$route.query.search
        }

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

            // Add Search value to query
            this.$router.push({ query: Object.assign({}, this.$route.query, { search: value }) });
        },
        agent: async function(value){
            this.loading = true

            if(this.agent){
                await axios.get('/api/onboarding/follow-up-logs/'+value.rocket_id)
                .then(response => {
                    this.agent.follow_up_logs = response.data.logs
                })
            }

            this.loading = false
        }
    },
    methods: {
        async updateAgent(){
            this.loading = true;

            let agent = Object.assign({}, this.agent)
            agent.source = agent.source.code
            
            if(agent.follow_up_date){
                agent.follow_up_date = moment(agent.follow_up_date).format('YYYY-MM-DD')
            }

            await axios.post('/api/onboarding', {"update": agent})
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Agency',
                    detail: response.data.message,
                    life: 2500
                })
            })
            
            this.agent = null

            this.loading = false;
        },
        async followUpLog(){
            this.loading = true;

            if(Object.keys(this.follow_up_log).length == 0){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Follow Up',
                    detail: "Please enter log data.",
                    life: 2500
                })

                this.loading = false
                return
            }

            await axios.post('/api/onboarding/followed-up/'+this.agent.rocket_id, {log: this.follow_up_log})
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Follow Up',
                    detail: response.data.message,
                    life: 2500
                })

                this.follow_up_log = {}
            })

            await axios.get('/api/onboarding/follow-up-logs/'+this.agent.rocket_id)
            .then(response => {
                this.agent.follow_up_logs = response.data.logs
            })

            this.loading = false;
        },
        async clearFilter(){
            this.loading = true
            const keys = Object.keys(this.filters)

            keys.forEach(key => {
                this.filters[key] = this[key]
            })

            this.filters.sortBy = { name: 'Date Created', code: 'created_at' }
            this.filters.stages = [
                {
                    name: "Prospects",
                    code: "prospect"
                },
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

            this.agents.forEach(agent => {
                // Update All Agent Sources to an Object
                this.sources.forEach(source => {
                    if(source.code == agent.source){
                        agent.source = source
                    }
                })

                // Update Follow Up Date
                if(agent.follow_up_date){
                    agent.follow_up_date = moment(agent.follow_up_date).format('MM/DD/YYYY')
                }
            })

            // Update url query with new filters
            let filterQuery = {}

            keys.forEach(key => {
                if(Array.isArray(this.filters[key])){
                    let values = []

                    this.filters[key].forEach(value => {
                        values.push(value.code)
                    })

                    filterQuery[key] = values
                }else{
                    filterQuery[key] = this.filters[key].code
                }
            })

            this.$router.push({query: filterQuery})

            this.loading = false
        },
        async applyFilters(){
            this.loading = true

            await axios.post('/api/onboarding', {"filters": this.filters})
            .then(response => {
                this.agents = response.data.agents
                this.searchView = this.agents
            })

            this.agents.forEach(agent => {
                // Update All Agent Sources to an Object
                this.sources.forEach(source => {
                    if(source.code == agent.source){
                        agent.source = source
                    }
                })

                // Update Follow Up Date
                if(agent.follow_up_date){
                    agent.follow_up_date = moment(agent.follow_up_date).format('MM/DD/YYYY')
                }
            })

            // Update url query with new filters
            let filterQuery = {}

            const keys = Object.keys(this.filters)

            keys.forEach(key => {
                if(Array.isArray(this.filters[key])){
                    let values = []

                    this.filters[key].forEach(value => {
                        values.push(value.code)
                    })

                    filterQuery[key] = values
                }else{
                    filterQuery[key] = this.filters[key].code
                }
            })

            this.$router.push({query: filterQuery})

            this.loading = false
        }
    },
    components: {
        topNav,
        loading,
        Dropdown,
        MultiSelect,
        InputText,
        Icon,
        InputMask,
        Sidebar,
        Calendar,
        Editor
    }
}
</script>

<style scoped>
:deep( .comment .p-editor-toolbar ){
    display: none !important;
}
:deep( .comment .p-editor-container .p-editor-content .ql-editor ){
    background-color: white !important;
}
:deep( .comment .p-editor-container .p-editor-content.ql-snow ){
    border: none !important;
}

:deep( .comment .ql-editor ){
    padding: 0px !important;
}
</style>