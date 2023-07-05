<template>
    <div class="w-full h-fit grid gap-6 pb-12">
        <div class="w-full h-full flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <p class="text-[32px] text-custom-black font-semibold">REACT Reports</p>
            </div>

            <!-- All Time Commissions Reports -->
            <div class="w-fit h-full flex items-center float-right">
                <button @click="downloadAllCommissions" class="w-fit h-[52px] grid px-4 text-[16px] font-semibold text-white rounded-[4px] bg-custom-purple shadow-newdrop">
                    <div class="w-fit h-fit m-auto flex items-center gap-4">
                        <Icon :icon="'bi:file-earmark-pdf-fill'" height="24" />
                        <p>Download Commission PDFs</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Overview Analytics -->
        <div class="w-full h-fit grid grid-cols-3 gap-6">
            <div class="w-full h-fit grid gap-4">
                <!-- Avg Prem -->
                <div class="w-full h-[129px] grid p-6 bg-custom-purple bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Avg. Premium</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-purple font-semibold float-right">${{ data.avg_prem }}/<span class="text-[16px] text-custom-black font-medium opacity-50">policy</span></p>
                    </div>
                </div>

                <!-- Total Prem -->
                <div class="w-full h-[129px] grid p-6 bg-custom-green bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Total Prem</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-green font-semibold float-right">${{ data.total_prem }}</p>
                    </div>
                </div>
            </div>

            <div class="w-full h-fit grid gap-4">
                <!-- Avg Override -->
                <div class="w-full h-[129px] grid p-6 bg-custom-red bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Avg. Monthly Override Revenue</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-red font-semibold float-right">${{ data.avg_monthly_override }}/<span class="text-[16px] text-custom-black font-medium opacity-50">month</span></p>
                    </div>
                </div>

                <!-- Total Override Revenue -->
                <div class="w-full h-[129px] grid p-6 bg-[#2DA581] bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Total Override Revenue</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-[#2DA581] font-semibold float-right">${{ data.total_override }}</p>
                    </div>
                </div>
            </div>

            <div class="w-full h-fit grid gap-4">
                <!-- Avg Monthly Policies -->
                <div class="w-full h-[129px] grid p-6 bg-custom-yellow bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Avg. Monthly Policies</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-yellow font-semibold float-right">{{ data.avg_monthly_policies }}/<span class="text-[16px] text-custom-black font-medium opacity-50">month</span></p>
                    </div>
                </div>

                <!-- Total Policies -->
                <div class="w-full h-[129px] grid p-6 bg-[#5EC5FF] bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Total Policies</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-[#5EC5FF] font-semibold float-right">{{ data.total_policies }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Month Breakdown -->
        <div class="w-full h-fit grid grid-cols-3 gap-6 p-6 border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
            <div class="w-full grid col-span-2 gap-2">
                <p class="text-[24px] text-custom-black font-medium">Total Override Revenue</p>
                <Chart type="line" :data="data.lineChart" class="w-full mt-[-60px ]"  />
            </div>

            <!-- Override Revenue Breakdown Table -->
            <div class="w-fit h-fit max-h-[331px] border-[1px] border-custom-black border-opacity-10 rounded-[2px] overflow-y-scroll">
                <div class="w-full h-[44px] px-4 grid grid-cols-2 text-[16px] text-custom-black font-medium bg-[#FBFBFB]">
                    <p class="my-auto w-[150px]">Month</p>
                    <p class="my-auto w-[150px]">Revenue</p>
                </div>

                <div v-for="(month, index) in data.revenue" :key="index" class="w-full h-[40px] px-4 grid grid-cols-2 text-[14px] text-custom-black border-b-[1px] border-custom-black border-opacity-10">
                    <!-- Month -->
                    <div class="w-[150px] h-full grid">
                        <p class="my-auto truncate pr-4">{{ moment(month.month).format('MMMM YYYY') }}</p>
                    </div>

                    <!-- Revenue -->
                    <div class="w-[150px] h-full grid">
                        <p class="my-auto truncate pr-4">${{ month.override }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carrier Breakdown -->
        <div class="w-full h-fit grid grid-cols-2 gap-6 p-6 border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
            <div class="w-full grid gap-2">
                <p class="text-[24px] text-custom-black font-medium">Carrier Revenue</p>
                <Chart type="pie" :data="data.pieChart" :options="pieChartOptions" class="w-[500px] mt-[-60px]" />
            </div>

            <!-- Carrier Breakdown Table -->
            <div class="w-fit h-fit border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                <div class="w-fit h-[44px] px-4 grid grid-cols-4 text-[16px] text-custom-black font-medium bg-[#FBFBFB]">
                    <p class="my-auto w-[150px]">Carrier</p>
                    <p class="my-auto w-[150px]">Premium</p>
                    <p class="my-auto w-[150px]">Override</p>
                    <p class="my-auto w-[150px]">Policies</p>
                </div>

                <div v-for="(carrier, index) in data.carriers" :key="index" class="w-fit h-[40px] px-4 grid grid-cols-4 text-[14px] text-custom-black border-b-[1px] border-custom-black border-opacity-10">
                    <!-- Carrier Name -->
                    <div class="w-[150px] h-full grid">
                        <p class="my-auto truncate pr-4">{{ carrier.name }}</p>
                    </div>

                    <!-- Carrier Total Premium -->
                    <div class="w-[150px] h-full grid">
                        <p class="my-auto truncate pr-4">${{ carrier.prem }}</p>
                    </div>
                    
                    <!-- Carrier Total Override -->
                    <div class="w-[150px] h-full grid">
                        <p class="my-auto truncate pr-4">${{ carrier.override }}</p>
                    </div>

                    <!-- Carrie Total Policies -->
                    <div class="w-[150px] h-full grid">
                        <p class="my-auto truncate pr-4">{{ carrier.policies }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub Agent Breakdown -->
        <div class="w-full h-fit grid grid-cols-3 gap-6 p-6 border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
            <div class="w-full grid col-span-2 gap-2">
                <p class="text-[24px] text-custom-black font-medium">Sub Agent Total Override Revenue</p>
                <Chart type="bar" :data="data.barGraph" :options="barChartOptions" class="w-full"  />
            </div>

            <!-- Override Revenue Breakdown Table -->
            <div class="w-fit h-fit border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                <div class="w-full h-[44px] px-4 grid grid-cols-2 text-[16px] text-custom-black font-medium bg-[#FBFBFB]">
                    <p class="my-auto w-[150px]">Agency</p>
                    <p class="my-auto w-[150px]">Revenue</p>
                </div>

                <div v-for="(agency, index) in data.sub_agent_rev" :key="index" class="w-full h-[40px] px-4 grid grid-cols-2 text-[14px] text-custom-black border-b-[1px] border-custom-black border-opacity-10">
                    <!-- Month -->
                    <div class="w-[150px] h-full grid">
                        <p class="my-auto truncate pr-4">{{ agency.name }}</p>
                    </div>

                    <!-- Revenue -->
                    <div class="w-[150px] h-full grid">
                        <p class="my-auto truncate pr-4">${{ agency.override }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Chart from 'primevue/chart';
import Knob from 'primevue/knob';
import { Icon } from '@iconify/vue';
import moment from 'moment';

export default {
    name: "REACT - Reports",
    data(){
        return {
            data: {},
            pieChartOptions: {
                plugins: {
                    legend: {
                        position: 'left'
                    }
                }
            },
            barChartOptions: {
                indexAxis: 'y'
            }
        }
    },
    async mounted(){
        this.$emit('loading')

        this.moment = moment

        await axios.get('/api/react/reports')
        .then(response => {
            this.data = response.data.data
        })

        this.data.lineChart.labels.forEach((label, index) => {
            this.data.lineChart.labels[index] = moment(label).format('MMMM YYYY')
        })

        this.$emit('loading')
    },
    methods: {
        async downloadAllCommissions(){
            this.$emit('loading')

            await axios.get('/api/commission/download/alltime', {responseType: 'blob'})
            .then(response => {
                let blob = new Blob([response.data], {type: 'application/zip'})
                const pdfUrl = URL.createObjectURL(blob)
                window.open(pdfUrl)
            })

            this.$emit('loading')
        }
    },
    components: {
        Icon,
        Chart,
        Knob
    }
}
</script>