<template>
    <div class="w-full h-fit grid gap-6 pb-12">
        <div class="w-full h-full flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <p class="text-[32px] text-custom-black font-semibold">Commission Reports</p>
            </div>

            <div class="w-fit h-full flex items-center gap-6 float-right">
                <p class="text-[18px] text-custom-black font-medium opacity-60">Select Month:</p>
                <Calendar v-model="month" view="month" dateFormat="yy-mm" />
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-3 gap-6">
            <!-- total Policies -->
            <div class="w-full h-[129px] grid p-6 bg-custom-purple bg-opacity-20 rounded-[4px] shadow-newdrop">
                <p class="text-[18px] text-custom-black font-semibold">Policies</p>
                <div class="w-full h-fit flow-root">
                    <p class="text-[32px] text-custom-purple font-semibold float-right">{{ total.policies }}</p>
                </div>
            </div>

            <!-- Total Commission -->
            <div class="w-full h-[129px] grid p-6 bg-custom-red bg-opacity-20 rounded-[4px] shadow-newdrop">
                <p class="text-[18px] text-custom-black font-semibold">Commission</p>
                <div class="w-full h-fit flow-root">
                    <p class="text-[32px] text-custom-red font-semibold float-right">${{ total.comm }}</p>
                </div>
            </div>

            <!-- Total Override Revenue -->
            <div class="w-full h-[129px] grid p-6 bg-custom-yellow bg-opacity-20 rounded-[4px] shadow-newdrop">
                <p class="text-[18px] text-custom-black font-semibold">Override</p>
                <div class="w-full h-fit flow-root">
                    <p class="text-[32px] text-custom-yellow font-semibold float-right">${{ total.override }}</p>
                </div>
            </div>
        </div>

        <!-- No Commissions for the selected Month -->
        <div v-if="commissions.length == 1 && commissions[0].comm == 0" class="w-full h-fit grid">
            <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'250px'" :height="'250px'" :speed="0.75" class="m-auto" />
        </div>
        <!-- Commission Reports for the Selected Month -->
        <div v-else class="w-full h-fit max-h-[800px] mt-4 grid gap-4 text-[16px] text-custom-black font-medium overflow-y-scroll">
            <div class="w-full h-fit grid grid-cols-6 px-4 border-custom-black border-b-[1px] border-opacity-10">
                <p class="opacity-50">Rocket ID</p>
                <p class="opacity-50 col-span-2">Agency Name</p>
                <p class="opacity-50">Policies</p>
                <p class="opacity-50">Premium</p>
                <p class="opacity-50">Commission</p>
            </div>

            <div v-for="(agency, index) in commissions" :key="index" class="w-full h-[52px] grid grid-cols-6 px-4 border-custom-black border-[1px] border-opacity-10">
                <p class="my-auto">{{ agency.rocket_id }}</p>
                <p class="my-auto col-span-2 truncate pr-4">{{ agency.name }}</p>
                <p class="my-auto">{{ agency.policies }}</p>
                <p class="my-auto truncate">${{ agency.prem }}</p>
                <p class="my-auto truncate">${{ agency.comm }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import NotFoundAnimation from '../../../assets/newNotFound.json'
import Calendar from 'primevue/calendar';
import moment from 'moment';

export default {
    name: "Commissions Reports",
    data(){
        return{
            NotFoundAnimation,
            month: "",
            commissions: [],
            total: {
                policies: 0,
                comm: 0,
                override: 0
            },
            onLoad: true,
        }
    },
    async mounted(){
        this.$emit('loading')

        this.moment = moment
        this.month = moment(Date.now()).format('YYYY-MM')

        await axios.get('/api/react/commissions/'+this.month)
        .then(response => {
            const keys = Object.keys(this.total)
            keys.forEach(key => {
                this.total[key] = response.data[key]
            })

            this.commissions = response.data.commissions
            this.$toast.add({
                severity: 'success',
                summary: 'REACT Commissions Reports',
                detail: response.data.message,
                life: 2500
            })
        })

        this.$emit('loading')
    },
    watch: {
        month: async function(value){
            this.$emit('loading')

            if(this.onLoad){
                this.onLoad = false
            }else{
                await axios.get('/api/react/commissions/'+moment(value).format('YYYY-MM'))
                .then(response => {
                    const keys = Object.keys(this.total)
                    keys.forEach(key => {
                        this.total[key] = response.data[key]
                    })

                    this.commissions = response.data.commissions
                    this.$toast.add({
                        severity: 'success',
                        summary: 'REACT Commissions Reports',
                        detail: response.data.message,
                        life: 2500
                    })
                })
            }

            this.$emit('loading')
        }
    },
    components: {
        Calendar
    }
}
</script>