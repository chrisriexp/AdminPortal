<template>
    <div class="w-full h-fit grid gap-6 pb-12">
        <p class="text-[32px] text-custom-black font-semibold">ROVER Reports</p>

        <!-- Overview Analytics -->
        <div class="w-full h-fit grid grid-cols-3 gap-6">
            <div class="w-full h-fit grid gap-4">
                <!-- Agents Onboarding -->
                <div class="w-full h-[129px] grid p-6 bg-custom-purple bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Today's Errors</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-purple font-semibold float-right">{{ data.today }}</p>
                    </div>
                </div>

                <!-- Avgerage Time to Complete -->
                <div class="w-full h-[129px] grid p-6 bg-custom-green bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Avg. Time to Complete</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-green font-semibold float-right">{{ data.avg_fix_time }}<span class="text-[16px] font-medium"> Days</span></p>
                    </div>
                </div>
            </div>

            <div class="w-full h-fit grid gap-4">
                <!-- Agents Under Review -->
                <div class="w-full h-[129px] grid p-6 bg-custom-red bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Pending Errors</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-red font-semibold float-right">{{ data.pending }}</p>
                    </div>
                </div>

                <!-- Completion Percentage -->
                <div class="w-full h-[129px] grid p-6 bg-[#2DA581] bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Completion Rate</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-[#2DA581] font-semibold float-right">{{ data.completion_rate }}%</p>
                    </div>
                </div>
            </div>

            <div class="w-full h-fit grid gap-4">
                <!-- Agents in training -->
                <div class="w-full h-[129px] grid p-6 bg-custom-yellow bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Fixed Errors</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-yellow font-semibold float-right">{{ data.fixed }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-3 gap-6">
            <!-- Bar Graph -->
            <div class="w-full grid gap-2 col-span-2 p p-6 border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                <p class="text-[20px] text-custom-black font-medium">Weekly Errors</p>
                <Chart type="line" :data="data.lineGraph" class="w-full"  />
            </div>

            <!-- Carrier Errors This Month -->
            <div class="w-full h-fit max-h-[475px] grid gap-2 p-6 rounded-[4px] border-[1px] border-custom-black border-opacity-10 overflow-y-scroll">
                <p class="text-[20px] text-custom-black font-medium">Carrier Errors This Month</p>
                
                <div v-for="(carrier, index) in data.months_errors" :key="index" class="w-full h-full flow-root border-b-[1px] border-custom-black border-opacity-10">
                    
                    <div class="w-fit h-full flex items-center mt-2 text-[16px] text-custom-black float-left"><p>
                        {{ 
                            carrier.carrier.slice(0, 2) == "PL" || carrier.carrier.slice(0, 2) == "QR" || carrier.carrier.slice(0, 2) == "RA" || carrier.carrier.slice(0, 2) == "SP" ?
                            carrier.carrier.slice(0, 2)+"-"+carriers[carrier.carrier.substring(3)].name : 
                            carriers[carrier.carrier].name
                        }}
                    </p></div>
                    <div class="w-fit h-full flex items-center text-[20px] text-custom-black font-medium float-right"><p>{{ carrier.total }}</p></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Chart from 'primevue/chart';
import Knob from 'primevue/knob';
import { Icon } from '@iconify/vue';
import carriers from '../../../assets/rover_carriers.json'

export default {
    name: "ROVER - Reports",
    data(){
        return {
            carriers,
            data: {},
            chartOptions: {

            }
        }
    },
    async created(){
        this.$emit('loading')

        await axios.get('/api/rover/reports')
        .then(response => {
            this.data = response.data.data
        })

        this.$emit('loading')
    },
    components: {
        Icon,
        Chart,
        Knob
    }
}
</script>