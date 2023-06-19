<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Top Nav -->
    <div class="absolute z-30 w-full">
        <topNav class="fixed z-30" :active="'modules'" />
    </div>

    <!-- Follow Up Log -->
    <div v-if="showLog" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <div class="w-[604px] h-fit max-h-[800px] m-auto grid gap-4 p-6 bg-sidebar-bg rounded-[4px] shadow-newdrop overflow-y-scroll">
            <div class="w-full h-fit flow-root">
                <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                    <p>Follow Up Log</p>
                </div>

                    <!-- Close -->
                <div @click="showLog = false; follow_up_log = {};" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><Icon :icon="'charm:cross'" height="32" class="text-custom-red m-auto" /></div>
            </div>
           

            <div class="w-full h-fit grid gap-2">
                <Editor v-model="follow_up_log" editorStyle="height: auto; font-size: 16px;" class="w-full" >
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

            <button @click="followedUp(this.rocket_id, true)" class="w-full h-[48px] text-white text-[16px] font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Log Follow Up</button>
        </div>
    </div>

    <div class="w-full h-screen z-20 absolute">
        <!-- Sidebar -->
        <div class="w-[305px] h-full grid bg-sidebar-bg z-20 absolute">
            <div class="w-full max-h-[800px] h-fit grid gap-4 mt-[100px] px-6 overflow-y-scroll text-[16px]">
                <p class="text-[24px] text-custom-black font-medium">Filter</p>

                <!-- Follow Up Date -->
                <Dropdown v-model="follow_up" :options="follow_up_options" optionLabel="name" class="w-full h-[40px] flex items-center" >
                    <template #value="slotProps">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Follow Up: {{ slotProps.value.name }}</p>
                        </div>
                    </template>
                </Dropdown>

                <!-- Source -->
                <MultiSelect v-model="filters.sources" :options="sources" optionLabel="name" placeholder="Source" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Source</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Type -->
                <MultiSelect v-model="filters.type" :options="type" optionLabel="name" placeholder="Type" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Type</p>
                        </div>
                    </template>
                </MultiSelect>
                
                <!-- Rocket Rep -->
                <MultiSelect v-model="filters.reps" :options="reps" optionLabel="name" placeholder="Rocket Rep" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Rocket Rep</p>
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
                    <p class="text-[32px] text-custom-black font-semibold">MGA Marketing</p>
                    <p class="text-custom-black text-[16px] opacity-60">Here are all agents within MGA Marketing, count: <span class="text-custom-purple">{{ searchView.length }}</span></p>
                </div>

                <div class="w-fit h-fit flex items-center gap-6 float-right">
                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText v-model="search" placeholder="Enter agency name, agent name, email, or phone" class="w-[403px] h-[48px] rounded-[4px]" />
                    </span>
                </div>
            </div>

            <!-- Filter Chips -->
            <div class="w-fit max-w-full flex flex-wrap items-center gap-2 mt-4">
                <p v-if="filters.sources.length > 0" class="text-[14px] text-custom-black">Marketing Sources:</p>
                <p v-for="(item, index) in filters.sources" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
                <p v-if="filters.type.length > 0" class="text-[14px] text-custom-black">Agent Type:</p>
                <p v-for="(item, index) in filters.type" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
                <p v-if="filters.reps.length > 0" class="text-[14px] text-custom-black">Rocket Rep:</p>
                <p v-for="(item, index) in filters.reps" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
            </div>

            <div v-if="searchView.length > 0" class="w-full h-fit grid gap-2 mt-6">
                <!-- List Headers -->
                <div class="w-full h-fit grid grid-cols-6 gap-8 px-4 py-2 text-[16px] text-custom-black opacity-50 border-b-[1px] border-custom-black border-opacity-10">
                    <p>Agency Name</p>
                    <p>Agent Name</p>
                    <p>Phone</p>
                    <p>Email</p>
                    <p>Follow Up Date</p>
                    <p>Agent Type</p>
                </div>

                <!-- Link to Onboarding Agency -->
                <div v-for="(agent, index) in searchView" :key="index" class="w-full h-fit grid gap-2 relative">
                    <a :href="'/onboarding/agency/'+agent.rocket_id+'/agency'" class="w-full h-[52px] grid grid-cols-6 gap-8 px-4 text-[16px] text-custom-black font-medium border-[1px] border-custom-black border-opacity-10 rounded-[2px] relative">
                        <p class="my-auto truncate pr-4">{{ agent.agency_name }}</p>
                        <p class="my-auto truncate pr-4">{{ agent.agent_name }}</p>
                        <p class="my-auto truncate pr-4">{{ agent.phone }}</p>
                        <p class="my-auto truncate pr-4">{{ agent.email }}</p>
                        <p :class="moment(agent.follow_up_date).isBefore(new Date()) ? 'text-custom-red' : ''" class="my-auto truncate pr-4">{{ moment(agent.follow_up_date).format('ddd, MMM DD') }}</p>
                        <p class="my-auto truncate pr-4 text-custom-purple">{{ agent.prospect ? 'Prospect' : 'Currently Onboarding' }}</p>
                    </a>

                    <div class="w-[24px] h-[52px] grid absolute left-[-30px]">
                        <Icon @click="followedUp(agent.rocket_id, false)" icon="fluent-mdl2:completed-solid" height="24" class="m-auto text-custom-black cursor-pointer hover:text-custom-purple" />
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
import MultiSelect from 'primevue/multiselect';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Editor from 'primevue/editor';

import { Icon } from '@iconify/vue';
import moment from 'moment';

import RocketReps from '../../../assets/marketing_reps.json'
import onboardingSources from '../../../assets/onboardingSources.json'
import NotFoundAnimation from '../../../assets/newNotFound.json'

export default {
    name: "MGA - Marketing",
    data(){
        return {
            NotFoundAnimation,
            loading: true,
            rocket_id: "",
            showLog: false,
            follow_up_log: {},
            follow_up: {
                name: "Today",
                code: 'today'
            },
            filters: {
                sources: [],
                type: [],
                reps: []
            },
            follow_up_options: [
                {
                    name: "Today",
                    code: 'today'
                },
                {
                    name: "Late",
                    code: 'late'
                },
                {
                    name: "All",
                    code: 'all'
                }
            ],
            sources: onboardingSources,
            type: [
                {
                    name: "Prospect",
                    code: true
                },
                {
                    name: "Currently Onboarding",
                    code: false
                }
            ],
            reps: RocketReps,
            agents: [],
            search: "",
            searchView: []
        }
    },
    async created(){
        this.moment = moment
        const keys = Object.keys(this.filters)

        keys.forEach(key => {
            this.filters[key] = this[key]
        })

        await axios.post('/api/onboarding/marketing', {"filters": this.filters, "follow_up": this.follow_up.code})
        .then(response => {
            this.agents = response.data.agents
            this.searchView = this.agents
        })

        this.loading = false
    },
    watch: {
        search: async function(value){
            this.searchView = []

            this.agents.forEach(agent => {
                if(agent.agency_name == null || agent.agency_name.toLowerCase().includes(value.toLowerCase()) || agent.agent_name.toLowerCase().includes(value.toLowerCase()) || agent.email.toLowerCase().includes(value.toLowerCase()) || agent.phone.toLowerCase().includes(value.toLowerCase())){
                    this.searchView.push(agent)
                }
            })
        }
    },
    methods: {
        async followedUp(id, ready){
            this.loading = true
            this.rocket_id = id

            if(!ready){
                this.showLog = true

                this.loading = false
                return
            }

            await axios.post('/api/onboarding/marketing/followed-up/'+id, {log: this.follow_up_log})
            .then(response => {
                console.log(response)
                this.$toast.add({
                    severity: 'success',
                    summary: 'Follow Up',
                    detail: response.data.message,
                    life: 2500
                })
            })

            await axios.post('/api/onboarding/marketing', {"filters": this.filters, "follow_up": this.follow_up.code})
            .then(response => {
                this.agents = response.data.agents
                this.searchView = this.agents
            })

            this.follow_up_log = {}
            this.showLog = false

            this.loading = false
        },
        async clearFilter(){
            this.loading = true
            const keys = Object.keys(this.filters)

            keys.forEach(key => {
                this.filters[key] = this[key]
            })

            await axios.post('/api/onboarding/marketing', {"filters": this.filters, "follow_up": this.follow_up.code})
            .then(response => {
                this.agents = response.data.agents
                this.searchView = this.agents
            })

            this.loading = false
        },
        async applyFilters(){
            this.loading = true

            await axios.post('/api/onboarding/marketing', {"filters": this.filters, "follow_up": this.follow_up.code})
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
        MultiSelect,
        Dropdown,
        InputText,
        Icon,
        Editor
    }
}
</script>