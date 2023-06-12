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

                <!-- Sort By -->
                <Dropdown v-model="filters.sortBy" :options="sortBy" optionLabel="name" class="w-full h-[40px] flex items-center" >
                    <template #value="slotProps">
                        <div class="grid">
                            <p class="truncate flex items-center gap-2"><span class="text-[16px]  text-custom-black font-medium flex items-center gap-2"><i class="pi pi-filter-fill"></i>Sort:</span> {{ slotProps.value.name }}</p>
                        </div>
                    </template>
                </Dropdown>

                <!-- Product Type -->
                <MultiSelect v-model="filters.products" :options="products" optionLabel="name" placeholder="Product Type" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Product Type</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Stage -->
                <MultiSelect v-model="filters.stages" :options="stages" optionLabel="name" placeholder="Stages" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Stages</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Error Source -->
                <MultiSelect v-model="filters.error_sources" :options="error_sources" optionLabel="name" placeholder="Error Source" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Error Source</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Error Types -->
                <MultiSelect v-model="filters.error_types" :options="error_types" optionLabel="name" placeholder="Error Type" class="w-full h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Error Type</p>
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
                    <p class="text-[32px] text-custom-black font-semibold">ROVER</p>
                    <p class="text-custom-black text-[16px] opacity-60">Here are all application tasks, <a target="_blank" href="https://rover.rocketflood.com/report-error" class="text-custom-purple underline">report errors here</a>; count: <span class="text-custom-purple">{{ searchView.length }}</span></p>
                </div>

                <div class="w-fit h-fit flex items-center gap-6 float-right">
                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText v-model="search" placeholder="Enter Application ID" class="w-[403px] h-[48px] rounded-[4px]" />
                    </span>
                </div>
            </div>

            <div class="w-fit max-w-full flex flex-wrap items-center gap-2 mt-4">
                <p class="text-[14px] text-custom-black">Sort By:</p>
                <p class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ filters.sortBy.name }}</p>
                <p v-if="filters.products.length > 0" class="text-[14px] text-custom-black">Product Type:</p>
                <p v-for="(item, index) in filters.products" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
                <p v-if="filters.stages.length > 0" class="text-[14px] text-custom-black">Stages:</p>
                <p v-for="(item, index) in filters.stages" :key="index" :class="item.code == 'fixed' ? 'bg-custom-green': 'bg-custom-black'" class="px-2 bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
                <p v-if="filters.error_sources.length > 0" class="text-[14px] text-custom-black">Error Sources:</p>
                <p v-for="(item, index) in filters.error_sources" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
                <p v-if="filters.error_types.length > 0" class="text-[14px] text-custom-black">Error Types:</p>
                <p v-for="(item, index) in filters.error_types" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ item.name }}</p>
            </div>

            <div v-if="searchView.length > 0" class="w-full h-fit grid gap-2 mt-6">
                <!-- List Headers -->
                <div class="w-full h-fit grid grid-cols-6 gap-8 px-4 py-2 text-[16px] text-custom-black opacity-50 border-b-[1px] border-custom-black border-opacity-10">
                    <p>Application ID</p>
                    <p>Carrier</p>
                    <p>Product</p>
                    <p>Error Type</p>
                    <p>Error Source</p>
                    <p>Stage</p>
                </div>

                <a v-for="(error, index) in searchView" :key="index" :href="'/rover/error/'+error.app_id+'/'+error.carrier" class="w-full h-[52px] grid grid-cols-6 gap-8 px-4 text-[16px] text-custom-black font-medium border-[1px] border-custom-black border-opacity-10 rounded-[2px] relative">
                    <p class="my-auto truncate pr-4">{{ error.app_id }}</p>
                    <!-- Application Carrier Name -->
                    <p class="my-auto truncate pr-4">{{ error.product == 'HOME' ? carriers[error.carrier.substring(3)].name : carriers[error.carrier].name }}</p>
                    <p class="my-auto truncate pr-4">{{ error.product == 'FLOOD' ? 'Flood' : 'Home' }}</p>
                    <p class="my-auto truncate pr-4">{{ error.type }}</p>
                    <p class="my-auto truncate pr-4">{{ error.source == 'MGA' ? 'Rocket MGA' : 'Rocket Automation' }}</p>

                    <!-- Error Stage -->
                    <img v-if="error.status == 'test'" :src="`/images/tests-${error.tests}.png`" alt="Error Tests" class="my-auto">
                    <p v-else class="my-auto truncate pr-4 text-custom-purple">{{ 
                        error.status == 'debug' ? 'Debugging' : 
                        (error.status == 'update' ? 'Updates in Progress' :  (
                            error.status == 'digiprompt' ? 'DigiPrompt Queue' : 'Fixed'
                        ))
                    }}</p>
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
import carriers from '../../../assets/rover_carriers.json'

export default {
    name: "ROVER - Errors",
    data(){
        return{
            loading: true,
            carriers,
            NotFoundAnimation,
            errors: [],
            search: "",
            searchView: [],
            filters: {
                sortBy: {name: "Date Created", code: "created_at"},
                stages: [
                    {name: "Debugging", code: "debug"},
                    {name: "Updates in Progress", code: "update"},
                    {name: "Testing", code: "test"},
                    {name: "DigiPrompt Queue", code: "digiprompt"}
                ],
                products: [
                    {name: "Flood", code: "FLOOD"},
                    {name: "Home", code: "HOME"}
                ],
                error_sources: [
                    {name: "Rocket Automation", code: "RA"},
                    {name: "Rocket MGA", code: "MGA"}
                ],
                error_types: [
                    {name: "API Error", code: "API"},
                    {name: "Bot Error", code: "BOT"}
                ],
            },
            sortBy: [
                {name: "Date Created", code: "created_at"},
                {name: "Date Updated", code: "updated_at"},
                {name: "Date Fixed", code: "fixed_date"},
                {name: "Carrier", code: "carrier"},
                {name: "Application ID", code: "app_id"},
            ],
            stages: [
                {name: "Debugging", code: "debug"},
                {name: "Updates in Progress", code: "update"},
                {name: "Testing", code: "test"},
                {name: "DigiPrompt Queue", code: "digiprompt"},
                {name: "Fixed", code: "fixed"},
            ],
            products: [
                {name: "Flood", code: "FLOOD"},
                {name: "Home", code: "HOME"}
            ],
            error_sources: [
                {name: "Rocket Automation", code: "RA"},
                {name: "Rocket MGA", code: "MGA"}
            ],
            error_types: [
                {name: "API Error", code: "API"},
                {name: "Bot Error", code: "BOT"}
            ],
        }
    },
    async created(){
        await axios.post('/api/rover/errors', {"filters": this.filters})
        .then(response => {
            this.errors = response.data.errors
            this.searchView = this.errors
        })

        this.loading = false
    },
    watch: {
        search: async function(value){
            this.searchView = []

            this.errors.forEach(error => {
                if(error.product == 'HOME'){
                    if(error.app_id.toLowerCase().includes(value.toLowerCase()) || this.carriers[error.carrier.substring(3)].name.toLowerCase().includes(value.toLowerCase())){
                        this.searchView.push(error)
                    }
                }else{
                    if(error.app_id.toLowerCase().includes(value.toLowerCase()) || this.carriers[error.carrier].name.toLowerCase().includes(value.toLowerCase())){
                        this.searchView.push(error)
                    }
                }
            })
        }
    },
    methods: {
        async applyFilters(){
            this.loading = true
            
            await axios.post('/api/rover/errors', {"filters": this.filters})
            .then(response => {
                this.errors = response.data.errors
                this.searchView = this.errors
            })

            this.loading = false
        },
        async clearFilter(){
            this.loading = true

            const keys = Object.keys(this.filters)
            
            keys.forEach(key => {
                this.filters[key] = this[key]
            })

            this.filters.sortBy = {name: "Date Created", code: "created_at"}
            this.filters.stages = [
                {name: "Debugging", code: "debug"},
                {name: "Updates in Progress", code: "update"},
                {name: "Testing", code: "test"},
                {name: "DigiPrompt Queue", code: "digiprompt"}
            ]
            
            await axios.post('/api/rover/errors', {"filters": this.filters})
            .then(response => {
                this.errors = response.data.errors
                this.searchView = this.errors
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
        Icon,
    }
}
</script>