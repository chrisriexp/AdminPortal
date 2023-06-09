<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loadingComponent class="m-auto" />
    </div>

    <!-- Top Nav -->
    <div class="absolute z-30 w-full">
        <topNav class="fixed z-30" :active="'modules'" />
    </div>

    <!-- Incomplete Rocket Appointment Carriers Popup -->
    <div v-if="!incomplete_uips.length == 0" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <incomplete_uips_popup class="m-auto" @close="incomplete_uips = []" @force="incomplete_uips = []; finalizeAgency(true)" :carriers="incomplete_uips" />
    </div>

    <div v-if="ready" class="w-full h-screen z-20 absolute">
        <!-- Sidebar -->
        <div class="w-[305px] h-full grid bg-sidebar-bg z-20 absolute">
            <div class="w-full h-fit max-h-[750px] grid gap-2 mt-[100px] px-6 overflow-y-scroll text-[16px]">
                <div class="w-full h-fit grid gap-4 my-6 text-[#787878] font-medium">
                    <div v-if="general.completed && !general.approved" @click="$router.replace({params: {category: 'approval'}})" :class="category == 'approval' ? 'text-custom-black bg-[#F1F1F1]' : ''" class="w-full h-fit px-4 py-2 flex items-center gap-4 rounded-[4px] hover:bg-[#F1F1F1] cursor-pointer"><Icon icon="icon-park-solid:view-list" height="24" /> Approval Checklist</div>
                    <div @click="$router.replace({params: {category: 'agency'}})" :class="category == 'agency' ? 'text-custom-black bg-[#F1F1F1]' : ''" class="w-full h-fit px-4 py-2 flex items-center gap-4 rounded-[4px] hover:bg-[#F1F1F1] cursor-pointer"><Icon icon="heroicons-solid:collection" height="24" /> Agency</div>
                    <div v-if="general.approved" @click="$router.replace({params: {category: 'carriers'}})" :class="category == 'carriers' ? 'text-custom-black bg-[#F1F1F1]' : ''" class="w-full h-fit px-4 py-2 flex items-center gap-4 rounded-[4px] hover:bg-[#F1F1F1] cursor-pointer"><Icon icon="icon-park-solid:id-card" height="24" /> Carrier Credentials</div>
                    <div @click="$router.replace({params: {category: 'documents'}})" :class="category == 'documents' ? 'text-custom-black bg-[#F1F1F1]' : ''" class="w-full h-fit px-4 py-2 flex items-center gap-4 rounded-[4px] hover:bg-[#F1F1F1] cursor-pointer"><Icon icon="ic:round-insert-drive-file" height="24" /> Documents</div>
                </div>
                
                <!-- Finalize Agency -->
                <button v-if="general.approved && !general.appointed" @click="finalizeAgency(false)" class="w-full h-[48px] my-2 text-[16px] text-white font-semibold bg-custom-black rounded-[4px] shadow-newdrop">
                    <div class="w-fit h-fit m-auto flex items-center gap-4">
                        <Icon icon="fluent-mdl2:completed-solid" height="24" />
                        <p>Finalize Agency</p>
                    </div>
                </button>

                <div class="w-full h-fit grid gap-2 p-4 text-[16px] text-custom-black bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
                    <!-- Training Completed -->
                    <div v-if="general.approved" class="w-full h-full flow-root">
                        <div class="w-fit h-full flex items-center float-left">
                            <p class="font-medium text-custom-purple">MGA Training</p>
                        </div>
                        <div class="w-fit h-full flex items-center float-right">
                            <InputSwitch v-model="general.training_completed" :binary="true" class="h-[24px] flex items-center" />
                        </div>
                    </div>
                    
                    <!-- Created At -->
                    <div>
                        <p class="font-medium">Created:</p>
                        <p>{{ moment(general.created_at).format('MMM Do YYYY h:mm a') }}</p>
                    </div>

                    <!-- Submitted At -->
                    <div>
                        <p class="font-medium text-custom-yellow">Submitted:</p>
                        <p>{{ moment(general.submitted_at).format('MMM Do YYYY h:mm a') }}</p>
                    </div>

                    <!-- Approved At -->
                    <div>
                        <p class="font-medium text-custom-green">Approved:</p>
                        <p>{{ moment(general.approved_at).format('MMM Do YYYY h:mm a') }}</p>
                    </div>

                    <!-- Appointed At -->
                    <div>
                        <p class="font-medium text-custom-purple">Appointed:</p>
                        <p>{{ moment(general.appointed_at).format('MMM Do YYYY h:mm a') }}</p>
                    </div>

                    <!-- Rocket Rep -->
                    <div>
                        <p class="font-medium">Rocket Rep</p>
                        <Dropdown v-model="general.rocket_rep" :options="rocket_reps" optionLabel="name" class="w-full h-[48px]" >
                            <template #value="slotProps">
                                <div class="grid">
                                    <p class="truncate">{{ slotProps.value.name }}</p>
                                </div>
                            </template>
                        </Dropdown>
                    </div>

                    <!-- Follow Up Date -->
                    <div>
                        <p class="font-medium">Follow Up Date</p>
                        <Calendar v-model="general.follow_up_date" showIcon class="h-[48px]" />
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full h-full z-10 absolute pt-[110px] pb-12 pl-[350px] pr-[50px] overflow-y-scroll">
            <!-- Breadcrumb Menu -->
            <div class="w-fit h-fit flex items-center gap-4 mb-6">
                <a href="/onboarding/agents" class="w-[24px] h-[24px] grid bg-white rounded-[2px] shadow-newdrop">
                    <Icon icon="material-symbols:arrow-back-rounded" height="18" class="m-auto text-custom-purple" />
                </a>

                <p class="text-[16px] text-custom-black font-medium">MGA Onboarding Portal / <span class="text-custom-purple">{{ general.agency_name }} - {{ rocket_id }}</span></p>
            </div>

            <component
                :is="view"
                :agency_name=general.agency_name
                :rocket_id=rocket_id
                @loading="loading = !loading"
            ></component>
        </div>
    </div>
</template>

<script>
import topNav from '../../components/topNav.vue'
import loadingComponent from '../../components/loading.vue'
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import InputSwitch from 'primevue/inputswitch';

import { Icon } from '@iconify/vue';
import rocket_reps from '../../../assets/marketing_reps.json'
import incomplete_uips_popup from '../../components/onboarding/agency/incomplete_uips_popup.vue'

import moment from 'moment';
import {defineAsyncComponent} from "vue";

export default {
    name: "Onboarding Agency",
    data(){
        return{
            rocket_reps,
            loading: true,
            ready: false,
            rocket_id: "",
            category: "agency",
            view: null,
            general: {},
            incomplete_uips: []
        }
    },
    async created(){
        this.moment = moment
        this.rocket_id = this.$route.params.rocket_id
        this.category = this.$route.params.category

        await axios.get('/api/onboarding/agency/'+this.rocket_id+'/general')
        .then(response => {
            this.general = response.data.data.agency
            this.general.follow_up_date = this.general.follow_up_date == null ? null : moment(this.general.follow_up_date).format('YYYY-MM-DD')
        })

        const keys = Object.keys(this.general)
        keys.forEach(key => {
            if(this.general[key] == 1){
                this.general[key] = true
            }else if(this.general[key] == 0){
                this.general[key] = false
            }
        })

        this.ready = true
        this.loading = false

        this.view = defineAsyncComponent(() => import(`../../components/onboarding/agency/${this.category}.vue`))
    },
    watch: {
        '$route.params.category': async function(value){
            this.category = value
            this.view = defineAsyncComponent(() => import(`../../components/onboarding/agency/${value}.vue`))
        },
        'general.rocket_rep': async function(value){
            await this.updateAgency()
        },
        'general.follow_up_date': async function(value){
            await this.updateAgency()
        },
        'general.training_completed': async function(value){
            await this.updateAgency()
        }
    },
    methods: {
        async updateAgency(){
            if(this.ready){
                this.loading = true

                const data = Object.assign({}, this.general)
                data.follow_up_date = data.follow_up_date == null ? null : moment(data.follow_up_date).format('YYYY-MM-DD')

                await axios.put('/api/onboarding/agency/'+this.rocket_id+'/general', {
                    "agency": data
                })
                .then(response => {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Agency',
                        detail: response.data.message,
                        life: 2500
                    })
                })
            }

            this.loading = false
        },
        async finalizeAgency(force){
            this.loading = true

            await axios.get('/api/onboarding/finalize/'+this.rocket_id+'/'+force)
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Agency',
                    detail: response.data.message,
                    life: 2500
                })
                
                location.reload()
            })
            .catch(error => {
                if(error.response.status == 500){
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Internal Service Error',
                        detail: "Pleae contact a system admin",
                        life: 3000
                    })
                }else{
                    this.$toast.add({
                        severity: 'warn',
                        summary: 'Agency',
                        detail: error.response.data.message,
                        life: 3000
                    })

                    if(error.response.status == 412){
                        this.incomplete_uips = error.response.data.incomplete_uips
                    }
                }
            })

            this.loading = false
        }
    },
    components: {
        topNav,
        loadingComponent,
        incomplete_uips_popup,
        Dropdown,
        Icon,
        Calendar,
        InputSwitch
    }
}
</script>