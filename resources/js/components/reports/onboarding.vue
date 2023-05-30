<template>
    <div class="w-full h-fit grid gap-6 pb-12">
        <p class="text-[32px] text-custom-black font-semibold">Onboarding Reports</p>

        <!-- Overview Analytics -->
        <div class="w-full h-fit grid grid-cols-3 gap-6">
            <div class="w-full h-fit grid gap-4">
                <!-- Agents Onboarding -->
                <div class="w-full h-[129px] grid p-6 bg-custom-purple bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Agents Onboarding</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-purple font-semibold float-right">{{ data.onboarding }}</p>
                    </div>
                </div>

                <!-- Agents Appointed -->
                <div class="w-full h-[129px] grid p-6 bg-custom-green bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Agents Appointed</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-green font-semibold float-right">{{ data.appointed }}</p>
                    </div>
                </div>
            </div>

            <div class="w-full h-fit grid gap-4">
                <!-- Agents Under Review -->
                <div class="w-full h-[129px] grid p-6 bg-custom-red bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Agents Under Review</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-red font-semibold float-right">{{ data.under_review }}</p>
                    </div>
                </div>

                <!-- Submitted this week -->
                <div class="w-full h-[129px] grid p-6 bg-[#2DA581] bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Submitted This Week</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-[#2DA581] font-semibold float-right">{{ data.submitted_this_week }}</p>
                    </div>
                </div>
            </div>

            <div class="w-full h-fit grid gap-4">
                <!-- Agents in training -->
                <div class="w-full h-[129px] grid p-6 bg-custom-yellow bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Agents In Training</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-custom-yellow font-semibold float-right">{{ data.in_training }}</p>
                    </div>
                </div>

                <!-- Submitted last Week -->
                <div class="w-full h-[129px] grid p-6 bg-[#5EC5FF] bg-opacity-20 rounded-[4px] shadow-newdrop">
                    <p class="text-[18px] text-custom-black font-semibold">Submitted Last Week</p>
                    <div class="w-full h-fit flow-root">
                        <p class="text-[32px] text-[#5EC5FF] font-semibold float-right">{{ data.submitted_last_week }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-3 gap-6">
            <!-- Bar Graph -->
            <Chart type="bar" :data="data.barGraph" class="w-full col-span-2"  />

            <!-- Appointement Conversion % -->
            <div class="w-full h-[336px] grid p-10 rounded-[4px] border-[1px] border-custom-black border-opacity-10 shadow-newdrop">
                <div class="w-full h-fit grid gap-2">
                    <p class="text-[20px] text-custom-black font-semibold">Appointment Conversion</p>
                    <p class="text-[16px] text-custom-black opacity-50">Here is the appointment conversion rate,</p>
                </div>
 
                <Knob v-model="data.conversion" valueTemplate="{value}%" class="mx-auto" :size="175" :strokeWidth="5" disabled  />
            </div>
        </div>
    </div>
</template>

<script>
import Chart from 'primevue/chart';
import Knob from 'primevue/knob';
import { Icon } from '@iconify/vue';

export default {
    name: "Onboarding - Reports",
    data(){
        return {
            data: {},
            chartOptions: {

            }
        }
    },
    async mounted(){
        this.$emit('loading')

        await axios.get('/api/onboarding/reports')
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