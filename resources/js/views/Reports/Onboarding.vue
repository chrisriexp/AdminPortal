<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Top Nav -->
    <div class="absolute z-30 w-full">
        <topNav class="fixed z-30" :active="'modules'" />
    </div>

    <div v-if="ready" class="w-full h-screen z-20 absolute">
        <!-- Side Bar -->
        <div class="w-[305px] h-full grid bg-sidebar-bg z-20 absolute">
            <div class="w-full max-h-[800px] h-fit grid gap-6 mt-[100px] px-6 overflow-y-scroll text-[16px]">
                <p class="text-[24px] text-custom-black font-medium">Filter</p>

                <!-- Date Range -->
                <div class="w-full h-fit grid gap-2">
                    <p class="text-[16px] text-custom-black font-medium">Date Range</p>
                    <Dropdown v-model="timescale" :options="timescales" optionLabel="name" placeholder="Select a Date Range" class="w-full h-[40px] flex items-center" />
                    <Calendar v-model="filters.dateRange" selectionMode="range" showIcon :disabled="timescale.code == 'custom' ? false : true"  />
                </div>

                <!-- Rocket Rep -->
                <MultiSelect v-model="filters.reps" :options="reps" optionLabel="name" placeholder="Rocket Rep" class="w-fi h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Rocket Rep</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Sources -->
                <MultiSelect v-model="filters.sources" :options="sources" optionLabel="name" placeholder="Source" class="w-fi h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="grid">
                            <p class="truncate text-[16px] text-custom-black font-medium">Source</p>
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
            <div class="w-ful h-fit grid gap-8">
                <p class="text-[32px] text-custom-black font-semibold">Onboarding Reports</p>

                <!-- Overview Analytics -->
                <div class="w-full h-fit grid grid-cols-4 gap-6">
                    <div class="w-full h-fit grid gap-4">
                        <!-- Onboarding Started -->
                        <div class="w-full h-[129px] grid p-6 bg-custom-purple bg-opacity-20 rounded-[4px] shadow-newdrop">
                            <p class="text-[16px] text-custom-black font-semibold">Onboarding Incomplete</p>
                            <div class="w-full h-fit flow-root">
                                <p class="text-[32px] text-custom-purple font-semibold float-right">{{ data.incomplete }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-fit grid gap-4">
                        <!-- Paperwork Submitted -->
                        <div class="w-full h-[129px] grid p-6 bg-custom-red bg-opacity-20 rounded-[4px] shadow-newdrop">
                            <p class="text-[16px] text-custom-black font-semibold">Paperwork Submitted</p>
                            <div class="w-full h-fit flow-root">
                                <p class="text-[32px] text-custom-red font-semibold float-right">{{ data.submitted }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-fit grid gap-4">
                        <!-- Agents Approved -->
                        <div class="w-full h-[129px] grid p-6 bg-custom-yellow bg-opacity-20 rounded-[4px] shadow-newdrop">
                            <p class="text-[16px] text-custom-black font-semibold">Agents Approved</p>
                            <div class="w-full h-fit flow-root">
                                <p class="text-[32px] text-custom-yellow font-semibold float-right">{{ data.approved }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-fit grid gap-4">
                        <!-- Agents Appointed -->
                        <div class="w-full h-[129px] grid p-6 bg-[#5EC5FF] bg-opacity-20 rounded-[4px] shadow-newdrop">
                            <p class="text-[16px] text-custom-black font-semibold">Agents Appointed</p>
                            <div class="w-full h-fit flow-root">
                                <p class="text-[32px] text-[#5EC5FF] font-semibold float-right">{{ data.appointed }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paperwork Submitted By Rocket Rep -->
                <div class="w-full h-fit grid gap-2">
                    <p class="text-[18px] text-custom-black font-medium">Paperwork Submitted by Rocket Rep</p>

                    <div class="w-full h-fit grid gap-6 grid-cols-3">
                        <!-- Pie Chart -->
                        <Chart type="pie" :data="data.pieChart" :options="pieChartOptions" class="w-full col-span-2"  />

                        <div class="w-full h-fit p-4 rounded-[2px] shadow-newdrop">
                            <p class="text-[16px] text-custom-black font-medium mb-4">Paperwork Submitted by Rep:</p>

                            <div v-for="(rep, index) in data.pieChart.labels" :key="index" class="w-full h-fit grid grid-cols-5 py-2 text-[16px] text-custom-black border-b-[1px] border-custom-black border-opacity-10">
                                <p class="w-full h-fit my-auto col-span-3 truncate">{{ rep }}</p>
                                <p class="w-full h-fit my-auto grid justify-items-center">{{ data.pieChart.datasets[0].data[index] }}</p>
                                
                                <div class="w-full h-full flow-root">
                                    <p :class="data.pieChart.datasets[0].data[index] / data.submitted * 100 > 30 ? 'bg-custom-green' : (data.pieChart.datasets[0].data[index] / data.submitted * 100 > 10 ? 'bg-custom-orange' : 'bg-custom-red')" class="w-fit h-full flex items-center float-right p-[5px] px-2 bg-opacity-20 rounded-full font-medium text-[14px] text-custom-black">{{ (data.pieChart.datasets[0].data[index] / data.submitted * 100).toFixed(2) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agencies Created During Date Range -->
                <div class="w-full h-fit grid gap-2">
                    <p class="text-[18px] text-custom-black font-medium">Agencies Created during Date Range</p>

                    <!-- Bar Graph -->
                    <Chart type="bar" :data="data.barGraph" :options="barGraphOptions" class="w-full"  />
                </div>

                <!-- Onboarding Stage Changes during Date Range -->
                <div class="w-full h-fit grid gap-2">
                    <p class="text-[18px] text-custom-black font-medium">Onboarding Stage Changes during Date Range</p>

                    <!-- Line Graph -->
                    <Chart type="line" :data="data.lineGraph" :options="lineGraphOptions" class="w-full"  />
                </div>

                <!-- Appointment Conversion % -->
                <!-- <div class="w-full h-[336px] grid p-10 rounded-[4px] border-[1px] border-custom-black border-opacity-10 shadow-newdrop">
                    <div class="w-full h-fit grid gap-2">
                        <p class="text-[20px] text-custom-black font-semibold">Appointment Conversion</p>
                        <p class="text-[16px] text-custom-black opacity-50">Here is the appointment conversion rate,</p>
                    </div>
    
                    <Knob v-model="data.conversion" valueTemplate="{value}%" class="mx-auto" :size="175" :strokeWidth="5" disabled  />
                </div> -->
            </div>
        </div>
    </div>
</template>

<script>
import topNav from '../../components/topNav.vue'
import loading from '../../components/loading.vue'

import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import Chart from 'primevue/chart';
import Knob from 'primevue/knob';
import { Icon } from '@iconify/vue';
import moment from 'moment';
import MultiSelect from 'primevue/multiselect';

import RocketReps from '../../../assets/marketing_reps.json'
import OnboardingSources from '../../../assets/onboardingSources.json'

export default {
    name: "Onboarding - Reports",
    data(){
        return {
            loading: false,
            ready: false,
            data: {},
            barGraphOptions: {
                aspectRatio: 3,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true
                    }
                }
            },
            lineGraphOptions: {
                aspectRatio: 3
            },
            pieChartOptions: {
                aspectRatio: 3,
                plugins: {
                    legend: {
                        position: 'left'
                    }
                }
            },
            timescale: {},
            timescales: [
                {
                    name: "Custom",
                    code: "custom"
                },
                {
                    name: "Today",
                    code: "day"
                },
                {
                    name: "Yesterday",
                    code: "last_day"
                },
                {
                    name: "This Week",
                    code: "week"
                },
                {
                    name: "Last Week",
                    code: "last_week"
                },
                {
                    name: "This Month",
                    code: "month"
                },
                {
                    name: "Last Month",
                    code: "last_month"
                },
                {
                    name: "This Quarter",
                    code: "quarter"
                },
                {
                    name: "Last Quarter",
                    code: "last_quarter"
                },
                {
                    name: "This Year",
                    code: "year"
                }
            ],
            reps: RocketReps,
            sources: OnboardingSources,
            filters: {
                reps: [],
                sources: [],
                dateRange: null,
            }
        }
    },
    async created(){
        this.moment = moment
        this.loading = true

        // Set Default Time Scale
        this.timescale = {name: "This Week", code: "week"}
        this.updateTimescale(this.timescale.code)
        
        // Set Filters
        const keys = Object.keys(this.filters)
        keys.forEach(key => {
            if(key != 'dateRange'){
                this.filters[key] = this[key]
            }
        })

        const queryKeys = Object.keys(this.$route.query)
        for await (const key of queryKeys){
            if(key != 'timescale'){
                this.filters[key] = []

                // If there is more than one value for the filter
                if(Array.isArray(this.$route.query[key])){
                    this.$route.query[key].forEach(value => {
                        this[key].forEach(keyValue => {
                            if(keyValue.code == value){
                                this.filters[key].push(keyValue)
                            }
                        })
                    })
                }
                // If there is only one value for the filter (multiselect)
                else{
                    this[key].forEach(keyValue => {
                        if(keyValue.code == this.$route.query[key]){
                            this.filters[key].push(keyValue)
                        }
                    })
                }
            }
            // Set Timescale
            else{
                this.timescales.forEach(async time => {
                    if(time.code == this.$route.query.timescale){
                        this.timescale = time
                        await this.updateTimescale(time.code)
                    }
                })
            }
        }

        // Get Reports
        await axios.post('/api/onboarding/reports', {"filters": this.filters})
        .then(response => {
            this.data = response.data.data
        })
        
        this.loading = false
        this.ready = true
    },
    watch: {
        timescale:  async function(value){
            if(this.ready){
                await this.updateTimescale(value.code)
            }
        }
    },
    methods: {
        async updateTimescale(value){
            if(value == 'custom'){}
            else if(value.includes('last')){
                this.filters.dateRange = []
                let broken = value.split("_")

                this.filters.dateRange[0] = new Date(moment().subtract(1, broken[1]+'s').startOf(broken[1]).format('MM/DD/YYYY'))
                this.filters.dateRange[1] = new Date(moment().subtract(1, broken[1]+'s').endOf(broken[1]).format('MM/DD/YYYY'))
            }else{
                this.filters.dateRange = []
                this.filters.dateRange[0] = new Date(moment().startOf(value).format('MM/DD/YYYY'))
                this.filters.dateRange[1] = new Date(moment().endOf(value).format('MM/DD/YYYY'))
            }
        },
        async clearFilter(){
            this.loading = true

            // Set Timescale
            this.timescale = {name: "This Week", code: "week"}
            await this.updateTimescale(this.timescale.code)

            let filterQuery = {}
            const keys = Object.keys(this.filters)

            keys.forEach(key => {
                if(key != 'dateRange'){
                    this.filters[key] = this[key]
                }

                let values = []

                this.filters[key].forEach(value => {
                    if(key != 'dateRange'){
                        values.push(value.code)
                    }
                })

                filterQuery[key] = values
            })

            this.$router.push({ query: Object.assign({}, filterQuery, { timescale: this.timescale.code }) });

            // Get Reports
            await axios.post('/api/onboarding/reports', {"filters": this.filters})
            .then(response => {
                this.data = response.data.data
            })

            this.loading = false
        },
        async applyFilters(){
            this.loading = true

            // Update url query with new filters
            let filterQuery = {}
            const keys = Object.keys(this.filters)

            keys.forEach(key => {
                let values = []

                this.filters[key].forEach(value => {
                    if(key != 'dateRange'){
                        values.push(value.code)
                    }
                })

                filterQuery[key] = values
            })

            this.$router.push({ query: Object.assign({}, filterQuery, { timescale: this.timescale.code }) });

            // Get Reports
            await axios.post('/api/onboarding/reports', {"filters": this.filters})
            .then(response => {
                this.data = response.data.data
            })

            this.loading = false
        }
    },
    components: {
        Icon,
        Chart,
        Knob,
        Calendar,
        Dropdown,
        MultiSelect,
        topNav,
        loading
    }
}
</script>