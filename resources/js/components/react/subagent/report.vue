<template>
    <div v-if="ready" class="w-full h-fit grid gap-6 pb-12">
        <div class="w-fit h-fit flex items-center gap-4 ">
            <!-- Back to REACT Sub Agents View -->
            <button @click="$router.push({name: 'REACT_SubAgents'})" class="w-[24px] h-[24px] grid bg-white shadow-newdrop rounded-[2px]">
                <Icon :icon="'material-symbols:arrow-back-rounded'" height="18" class="text-custom-purple m-auto" />
            </button>

            <p class="text-[16px] text-custom-black font-medium">REACT/<span class="text-custom-purple">{{ agency.name }}</span></p>
        </div>

        <div class="w-full h-fit flow-root">
            <div class="w-fit h-fit grid gap-2 text-[16px] text-custom-black float-left">
                <p class="text-[32px] font-semibold">{{ agency.name }}</p>
                <p class="opacity-60">Here are all agency details,</p>
            </div>

            <button @click="$emit('viewCarrierCodes', agency)" class="w-fit h-[48px] px-6 text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop float-right">Agency Carrier Codes</button>
        </div>

        <!-- Overview Analytics -->
        <div class="w-full h-fit grid grid-cols-3 gap-6">
            <!-- Premium Written -->
            <div class="w-full h-fit grid gap-4">
                <!-- Average -->
                <div class="w-full h-[129px] grid p-6 bg-custom-purple bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Avg. Premium</p>
                    <p class="text-[32px] text-custom-purple font-semibold">${{ revenue.avgPrem }}/<span class="text-[16px] text-custom-black font-medium opacity-50">policy</span></p>
                </div>

                <!-- Total -->
                <div class="w-full h-[129px] grid p-6 bg-custom-green bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Total Premium</p>
                    <p class="text-[32px] text-custom-green font-semibold">${{ revenue.prem }}</p>
                </div>
            </div>

            <!-- Override Revenue -->
            <div class="w-full h-fit grid gap-4">
                <!-- Average -->
                <div class="w-full h-[129px] grid p-6 bg-custom-red bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Avg. Monthly Override Revenue</p>
                    <p class="text-[32px] text-custom-red font-semibold">${{ revenue.avgOverride }}/<span class="text-[16px] text-custom-black font-medium opacity-50">month</span></p>
                </div>

                <!-- Total -->
                <div class="w-full h-[129px] grid p-6 bg-[#2DA581] bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Total Override Revenue</p>
                    <p class="text-[32px] text-[#2DA581] font-semibold">${{ revenue.override }}</p>
                </div>
            </div>

            <!-- Policies Written -->
            <div class="w-full h-fit grid gap-4">
                <!-- Average -->
                <div class="w-full h-[129px] grid p-6 bg-custom-yellow bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Avg. Monthly Policies</p>
                    <p class="text-[32px] text-custom-yellow font-semibold">{{ revenue.avgPolicies }}/<span class="text-[16px] text-custom-black font-medium opacity-50">month</span></p>
                </div>

                <!-- Total -->
                <div class="w-full h-[129px] grid p-6 bg-[#5EC5FF] bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Total Policies</p>
                    <p class="text-[32px] text-[#5EC5FF] font-semibold">{{ revenue.policies }}</p>
                </div>
            </div>
        </div>

        <!-- Total Override Revenu -->
        <div class="w-full h-fit grid gap-4 p-10 border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
            <p class="mt-8 text-[24px] text-custom-black font-medium">Total Override Revenue</p>

            <div class="w-full h-fit flex items-center gap-12">
                <Chart type="line" :data="lineChart" class="w-[800px]" />

                <!-- Override Revenue Breakdown Table -->
                <div class="w-fit h-fit max-h-[331px] border-[1px] border-custom-black border-opacity-10 rounded-[2px] overflow-y-scroll">
                    <div class="w-full h-[44px] px-4 grid grid-cols-2 text-[16px] text-custom-black font-medium bg-[#FBFBFB]">
                        <p class="my-auto w-[150px]">Month</p>
                        <p class="my-auto w-[150px]">Revenue</p>
                    </div>

                    <div v-for="(month, index) in revenue.months" :key="index" class="w-full h-[40px] px-4 grid grid-cols-2 text-[14px] text-custom-black border-b-[1px] border-custom-black border-opacity-10">
                        <!-- Month -->
                        <div class="w-[150px] h-full grid">
                            <p class="my-auto truncate pr-4">{{ moment(month.month).format('MMMM YYYY') }}</p>
                        </div>

                        <!-- Revenue -->
                        <div class="w-[150px] h-full grid">
                            <p class="my-auto truncate pr-4">${{ month.revenue }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carrier Breakdown -->
        <div class="w-full h-fit grid gap-4 px-10 border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
            <p class="mt-8 text-[24px] text-custom-black font-medium">Total Carrier Revenue</p>

            <div class="w-fit h-fit mt-[-60px] flex items-center gap-12">
                <Chart type="pie" :data="pieChart" :options="pieChartOptions" class="w-[500px]" />

                <!-- Carrier Breakdown Table -->
                <div class="w-fit h-fit border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                    <div class="w-full h-[44px] px-4 grid grid-cols-3 text-[16px] text-custom-black font-medium bg-[#FBFBFB]">
                        <p class="my-auto w-[150px]">Carrier</p>
                        <p class="my-auto w-[150px]">Premium</p>
                        <p class="my-auto w-[150px]">Policies</p>
                    </div>

                    <div v-for="(carrier, index) in carriers" :key="index" class="w-full h-[40px] px-4 grid grid-cols-3 text-[14px] text-custom-black border-b-[1px] border-custom-black border-opacity-10">
                        <!-- Carrier Name -->
                        <div class="w-[150px] h-full grid">
                            <p class="my-auto truncate pr-4">{{ carrier.name }}</p>
                        </div>

                        <!-- Carrier Total Premium -->
                        <div class="w-[150px] h-full grid">
                            <p class="my-auto truncate pr-4">${{ agency[carrier.code].prem }}</p>
                        </div>

                        <!-- Carrie Total Policies -->
                        <div class="w-[150px] h-full grid">
                            <p class="my-auto truncate pr-4">{{ agency[carrier.code].policies }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Icon } from '@iconify/vue';
import carriers from '../../../../assets/react_carriers.json'

import Chart from 'primevue/chart';
import moment from 'moment';

export default {
    name: "Sub Agent - Report",
    data(){
        return{
            carriers,
            ready: false,
            agency: {},
            revenue: {},
            pieChart: {},
            pieChartOptions: {
                plugins: {
                    legend: {
                        position: 'left'
                    }
                }
            },
            lineChart: {}
        }
    },
    async mounted(){
        this.$emit('loading')

        this.moment = moment

        await axios.get('/api/react/sub-agent/report/'+this.$route.params.id)
        .then(response => {
            this.agency = response.data.agency
            this.revenue = response.data.revenue
            this.pieChart = response.data.pieChart
            this.lineChart = response.data.lineChart

            this.lineChart.labels.forEach((label, index) => {
                this.lineChart.labels[index] = moment(label).format('MMMM YYYY')
            })
        })

        this.$emit('loading')
        this.ready = true
    },
    components: {
        Icon,
        Chart
    }
}
</script>