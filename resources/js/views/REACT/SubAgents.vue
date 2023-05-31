<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Top Nav -->
    <div class="absolute z-30 w-full">
        <topNav class="fixed z-30" :active="'modules'" />
    </div>

    <!-- Add Sub Agent Select -->
    <div v-if="add" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <addAgent class="m-auto" @close="add = false" @single="add = false; addSingle = true" @bulk="add = false; addBulk = true" />
    </div>

    <!-- Add Bulk Sub Agent -->
    <div v-if="addBulk" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <addBulkAgent class="m-auto" @close="addBulk = false" @update="addBulk = false; getAgents()" @back="addBulk = false; add = true" @loading="loading = !loading" />
    </div>

    <!-- Add Single Sub Agent -->
    <div v-if="addSingle" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <addSingleAgent class="m-auto" @close="addSingle = false" @update="addSingle = false; getAgents()" @back="addSingle = false; add = true" :agent="agent" @loading="loading = !loading" />
    </div>

    <!-- View Carrier Codes -->
    <div v-if="carrier_codes" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <viewCarrierCodes class="m-auto" @close="carrier_codes = false" :agent="agent" />
    </div>

    <div class="w-full h-screen z-20 absolute">
        <!-- Sidebar -->
        <div class="w-[305px] h-full grid bg-sidebar-bg z-20 absolute">
            <div class="w-full max-h-[800px] h-fit grid gap-4 mt-[100px] px-6 overflow-y-scroll text-[16px]">
                <!-- Go to Upload Statements -->
                <button @click="$router.push({name: 'REACT_UploadStatements'})" class="w-full h-[48px] grid text-custom-black hover:text-white hover:bg-custom-black rounded-[4px] border-[1px] border-custom-black">
                    <div class="w-fit h-fit flex items-center gap-4 m-auto">
                        <Icon :icon="'mingcute:file-upload-fill'" height="24" />
                        <p class="text-[16px] font-semibold">Upload Statements</p>
                    </div>
                </button>

                <p class="text-[24px] text-custom-black font-medium">Filter</p>

                <!-- Rocket Plus Status -->
                <MultiSelect v-model="filter.rocketPlus" :options="rocketPlus" optionLabel="name" placeholder="Carriers" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Rocket Plus</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Rocket Carrier Appointment Filter -->
                <MultiSelect v-model="filter.carriers" :options="carriers" optionLabel="name" placeholder="Carriers" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Rocket Carriers</p>
                        </div>
                    </template>
                </MultiSelect>

                <div class="w-full h-fit grid gap-2">
                    <!-- Apply Filters -->
                    <button @click="getAgents" class="w-full h-[32px] text-white font-medium text-[16px] bg-custom-purple rounded-[4px] shadow-newdrop">Apply Filters</button>
                    <!-- Clear Filters -->
                    <button @click="clearFilter" class="text-[14px] text-custom-purple font-medium">Clear Filters</button>
                </div>
            </div>
        </div>

        <div class="w-full h-full z-10 absolute pt-[110px] pb-12 pl-[350px] pr-[50px] overflow-y-scroll">
            <div class="w-full h-fit flow-root">
                <div class="w-fit h-fit grid gap-2 float-left">
                    <p class="text-[32px] text-custom-black font-semibold">REACT</p>
                    <p class="text-custom-black text-[16px] opacity-60">Here are all appointed sub agents, count: <span class="text-custom-purple">{{ searchView.length }}</span></p>
                </div>

                <div class="w-fit h-fit flex items-center gap-6 float-right">
                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText v-model="search" placeholder="Enter agency name or Rocket ID" class="w-[403px] h-[48px] rounded-[4px]" />
                    </span>

                    <!-- Add Sub Agent -->
                    <button @click="add = true" class="w-[246px] h-[48px] grid text-white text-[16px] font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">
                        <div class="w-fit h-fit m-auto flex items-center gap-4">
                            <Icon :icon="'ic:round-plus'" height="24" />
                            <p>Add Sub Agent</p>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Filter Chips -->
            <div class="w-fit max-w-full flex flex-wrap items-center gap-2 mt-4">
                <p v-if="filter.rocketPlus.length > 0" class="text-[14px] text-custom-black">RocketPlus:</p>
                <p v-for="(item, index) in filter.rocketPlus" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
                <p v-if="filter.carriers.length > 0" class="text-[14px] text-custom-black">Rocket Appointed Carriers:</p>
                <p v-for="(item, index) in filter.carriers" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
            </div>

            <div v-if="searchView.length > 0" class="w-full h-fit grid gap-2 mt-2">
                <!-- List Headers -->
                <div class="w-full h-fit grid grid-cols-3 px-8 py-2 text-[16px] text-custom-black opacity-50 border-b-[1px] border-custom-black border-opacity-10">
                    <p>Agency Name</p>
                    <p>Rocket ID</p>
                    <p>Rocket Plus</p>
                </div>

                <div v-for="(agent, index) in searchView" :key="index" class="w-full h-[52px] grid grid-cols-3 px-8 text-[16px] text-custom-black font-medium border-[1px] border-custom-black border-opacity-10 rounded-[2px] relative">
                    <p class="my-auto truncate pr-4">{{ agent.name }}</p>
                    <p class="my-auto">{{ agent.rocket_id }}</p>
                    <p :class="agent.rocketPlus ? 'text-custom-green' : 'text-custom-red'" class="my-auto">{{ agent.rocketPlus ? 'Yes' : 'No' }}</p>

                    <button @click="subAgentMenu($event, agent.id)" aria-haspopup="true" :aria-controls="agent.id+'_modules_menu'" class="my-auto h-full grid right-8 absolute"><Icon :icon="'carbon:overflow-menu-vertical'" height="24" class="my-auto" /></button>
                    <Menu :ref="agent.id+'_modules_menu'" :id="agent.id+'_modules_menu'" :model="subagent_menu_items" :popup="true" class="w-fit font-medium text-[16px] text-custom-black" />
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
import Menu from 'primevue/menu';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';

import { Icon } from '@iconify/vue';

import carriers from '../../../assets/react_carriers.json'
import NotFoundAnimation from '../../../assets/newNotFound.json'

import addBulkAgent from '../../components/react/addBulkAgent.vue'
import addSingleAgent from '../../components/react/addSingleAgent.vue'
import addAgent from '../../components/react/addAgent.vue'
import viewCarrierCodes from '../../components/react/viewCarrierCodes.vue'

export default{
    name: "REACT - Sub Agents",
    data(){
        return {
            NotFoundAnimation,
            carriers,
            rocketPlus: [
                {
                    name: "Yes",
                    code: true
                },
                {
                    name: "No",
                    code: false
                }
            ],
            loading: true,
            add: false,
            addSingle: false,
            addBulk: false,
            carrier_codes: false,
            agents: [],
            agent: {},
            id: "",
            subagent_menu_items: [
                {
                    label: 'Carrier Codes',
                    icon: 'pi pi-code',
                    command: async () => {
                        this.carrier_codes = true
                    }
                },
                {
                    label: 'Go to Agency View',
                    icon: 'pi pi-eye',
                    command: async () => {
                        this.$router.push({name: "REACT_SubAgent", params: {id: this.id, category: 'report'}})
                    }
                },
            ],
            search: "",
            searchView: [],
            filter: {
                carriers: [],
                rocketPlus: []
            }
        }
    },
    async created(){
        this.filter.carriers = this.carriers
        this.filter.rocketPlus = this.rocketPlus

        await axios.post('/api/react/sub-agents', {"filter": this.filter})
        .then(response => {
            this.agents = response.data.agents

            this.agents.forEach(agent => {
                if(agent.rocketPlus == 1){
                    agent.rocketPlus = true
                }else if(agent.rocketPlus == 0) {
                    agent.rocketPlus = false
                }
            })
        })

        this.searchView = this.agents

        this.loading = false
    },
    watch: {
        search: async function(value){
            this.searchView = []
            this.agents.forEach(agent => {
                if(agent.name.toLowerCase().includes(value.toLowerCase()) || agent.rocket_id.toLowerCase().includes(value.toLowerCase())){
                    this.searchView.push(agent)
                }
            })
        }
    },
    methods: {
        async subAgentMenu(event, id){
            this.id = id

            this.agents.forEach(agent => {
                if(agent.id ==  id){
                    this.agent = agent
                }
            })

            this.$refs[id+'_modules_menu'][0].toggle(event);
        },
        async updateAgents(){
            await axios.get('/api/react/sub-agents')
            .then(response => {
                this.agents = response.data.agents

                this.agents.forEach(agent => {
                    if(agent.rocketPlus == 1){
                        agent.rocketPlus = true
                    }else if(agent.rocketPlus == 0) {
                        agent.rocketPlus = false
                    }
                })
            })
        },
        async getAgents(){
            this.loading = true

            await axios.post('/api/react/sub-agents', {'filter': this.filter})
            .then(response => {
                this.agents = response.data.agents

                this.agents.forEach(agent => {
                    if(agent.rocketPlus == 1){
                        agent.rocketPlus = true
                    }else if(agent.rocketPlus == 0) {
                        agent.rocketPlus = false
                    }
                })
            })

            this.searchView = this.agents

            this.loading = false
        },
        async clearFilter(){
            this.loading = true
            this.filter.carriers = this.carriers
            this.filter.rocketPlus = this.rocketPlus

            await axios.post('/api/react/sub-agents', {'filter': this.filter})
            .then(response => {
                this.agents = response.data.agents

                this.agents.forEach(agent => {
                    if(agent.rocketPlus == 1){
                        agent.rocketPlus = true
                    }else if(agent.rocketPlus == 0) {
                        agent.rocketPlus = false
                    }
                })
            })

            this.searchView = this.agents

            this.loading = false
        }
    },
    components: {
        topNav,
        loading,
        addSingleAgent,
        addBulkAgent,
        addAgent,
        Menu,
        viewCarrierCodes,
        Icon,
        InputText,
        MultiSelect
    }
}
</script>