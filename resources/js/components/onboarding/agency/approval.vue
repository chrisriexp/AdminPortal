<template>
    <div class="w-full h-fit grid gap-8">
        <div class="w-full h-full flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <div class="w-fit h-fit grid"><p class="w-fit h-fit truncate max-w-[700px] text-[24px] text-custom-black font-semibold">{{ agency_name }} - {{ rocket_id }}</p></div>
            </div>

            <div class="w-fit h-full flex items-center float-right">
                <!-- Update Agency Button -->
                <button @click="approveAgency" class="w-[223px] h-[48px] text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">
                    <div class="w-fit h-fit m-auto flex items-center gap-4">
                        <Icon icon="wpf:approval" height="24" />
                        <p>Approve Agency</p>
                    </div>
                </button>
            </div>
        </div>

        <div v-if="ready" class="w-full h-fit grid gap-6 p-6 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
            <div class="w-full h-full flow-root">
                <div class="w-fit h-full flex items-center float-left">
                    <div class="w-fit h-fit grid">
                        <p class="text-[24px] text-custom-black font-medium">Application Approval Required!!</p>
                        <p class="text-[16px] text-custom-black opacity-60">Please validate the below items to activate the agency appointment</p>
                    </div>
                </div>
                
                <div class="w-fit h-full flex items-center float-right">
                    <button @click="saveRoster" class="w-[36px] h-[36px] grid text-custom-purple bg-white rounded-[2px] shadow-newdrop"><Icon icon="fluent:save-24-filled" height="24" class="m-auto" /></button>
                </div>
            </div>

            <div class="w-full h-fit grid grid-cols-2 gap-6">
                <!-- CheckList -->
                <div class="w-full h-fit grid gap-6 text-[16px] approval-checklist">
                    <!-- Agency License -->
                    <div class="w-full h-fit grid gap-4 p-4 border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                        <div :class="approvals.license && approvals.license_file ? 'checked' : ''" class="w-fit h-fit flex items-center gap-4">
                            <Checkbox :modelValue="true" disabled :binary="true" />
                            <p :class="approvals.license && approvals.license_file ? 'text-custom-green' : 'text-custom-red'" class="font-medium">Agency License</p>
                        </div>

                        <div class="w-full h-fit grid gap-4 pl-8">
                            <!-- License Number -->
                            <div :class="approvals.license ? 'checked' : ''" class="w-fit h-fit flex items-center gap-4">
                                <Checkbox v-model="approvals.license" :binary="true" />
                                <p :class="approvals.license ? 'text-custom-green' : 'text-custom-red'" class="font-medium w-[200px]">License Number</p>
                                <InputText v-model="data.agency_license" disabled class="w-[125px] h-[32px]" />
                            </div>

                            <!-- License File -->
                            <div :class="approvals.license_file ? 'checked' : ''" class="w-fit h-fit flex items-center gap-4">
                                <Checkbox v-model="approvals.license_file" :binary="true" />
                                <a :href="files.agency_license_file" target="_blank" :class="approvals.license_file ? 'text-custom-green' : 'text-custom-red'" class="font-medium underline w-[200px]">View License File</a>
                            </div>
                        </div>
                    </div>

                    <!-- Agency E&O -->
                    <div class="w-full h-fit grid gap-4 p-4 border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                        <div :class="approvals.eo_exp && approvals.eo_limit && approvals.eo_file ? 'checked' : ''" class="w-fit h-fit flex items-center gap-4">
                            <Checkbox :modelValue="true" disabled :binary="true" />
                            <p :class="approvals.eo_exp && approvals.eo_limit && approvals.eo_file ? 'text-custom-green' : 'text-custom-red'" class="font-medium">Agency E&O</p>
                        </div>

                        <div class="w-full h-fit grid gap-4 pl-8">
                            <!-- E&O Exp -->
                            <div :class="approvals.eo_exp ? 'checked' : ''" class="w-fit h-fit flex items-center gap-4">
                                <Checkbox v-model="approvals.eo_exp" :binary="true" />
                                <p :class="approvals.eo_exp ? 'text-custom-green' : 'text-custom-red'" class="font-medium w-[200px]">E&O Exp Date</p>
                                <InputText :modelValue="moment(data.eo_exp).format('MM/DD/YYYY')" disabled class="w-[125px] h-[32px]" />
                            </div>

                            <!-- E&O Limit -->
                            <div :class="approvals.eo_limit ? 'checked' : ''" class="w-fit h-fit flex items-center gap-4">
                                <Checkbox v-model="approvals.eo_limit" :binary="true" />
                                <p :class="approvals.eo_limit ? 'text-custom-green' : 'text-custom-red'" class="font-medium w-[200px]">E&O Exp Date</p>
                                <InputText v-model="data.eo_limit" disabled class="w-[125px] h-[32px]" />
                            </div>

                            <!-- E&O File -->
                            <div :class="approvals.eo_file ? 'checked' : ''" class="w-fit h-fit flex items-center gap-4">
                                <Checkbox v-model="approvals.eo_file" :binary="true" />
                                <a :href="files.eo" target="_blank" :class="approvals.eo_file ? 'text-custom-green' : 'text-custom-red'" class="font-medium underline w-[200px]">View E&O File</a>
                            </div>
                        </div>
                    </div>

                    <!-- Agreement -->
                    <div class="w-full h-fit grid gap-4 p-4 border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                        <div :class="approvals.agreement ? 'checked' : ''" class="w-fit h-fit flex items-center gap-4">
                            <Checkbox :modelValue="true" disabled :binary="true" />
                            <a :href="files.agreement" target="_blank" :class="approvals.agreement ? 'text-custom-green' : 'text-custom-red'" class="font-medium underline">View Executed Contracts</a>
                        </div>

                        <div class="w-full h-fit grid gap-4 pl-8">
                            <!-- Flow Flood Roster -->
                            <div :class="approvals.agreement ? 'checked' : ''" class="w-fit h-fit flex items-center gap-4">
                                <Checkbox v-model="approvals.agreement" :binary="true" />
                                <p :class="approvals.agreement ? 'text-custom-green' : 'text-custom-red'" class="font-medium w-[200px]">Flow Flood Roster</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roster -->
                <div class="w-full h-fit grid gap-6">
                    <!-- Agency Roster List -->
                    <div class="w-full h-[260px] grid p-4 border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                        <div class="w-full h-fit max-h-[220px] grid gap-2 overflow-y-scroll">
                            <div class="w-full h-full flow-root">
                                <div class="w-fit h-full flex items-center float-left">
                                    <p class="text-[16px] text-custom-black font-medium">Agency Roster</p>
                                </div>

                                <!-- Add Roster Item Button -->
                                <div class="w-fit h-full flex items-center float-right">
                                    <button @click="addtoRoster" class="w-[36px] h-[36px] grid text-white bg-custom-purple rounded-[2px] shadow-newdrop"><Icon icon="ic:round-plus" height="24" class="m-auto" /></button>
                                </div>
                            </div>

                            <!-- Agency Members -->
                            <div v-for="(agent, index) in data.roster" :key="index" class="w-full h-fit flex items-center gap-2 py-[3px]">
                                <!-- Name -->
                                <div class="w-[40%] h-fit flex items-center gap-2">
                                    <p>Name</p>
                                    <InputText v-model="agent.name" class="w-full h-[32px]" />
                                </div>

                                <!-- Email -->
                                <div class="w-[40%] h-fit flex items-center gap-2">
                                    <p>Email</p>
                                    <InputText v-model="agent.email" class="w-full h-[32px]" />
                                </div>

                                <div class="w-[15%] h-fit flex items-center relative">
                                    <!-- Delete From Roster Button  -->
                                    <button @click="removeFromRoster(index)" class="w-[32px] h-[32px] grid text-custom-red rounded-[2px] bg-white shadow-newdrop right-4 absolute"><Icon icon="fluent:delete-20-filled" height="24" class="m-auto" /></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-fit grid">
                        <p class="text-[16px] text-custom-black font-medium">Roster Notes</p>
                        <textarea v-model="data.roster_notes" class="w-full h-[150px] border-[1px] border-custom-black border-opacity-10 focus:border-custom-black focus:border-opacity-10 focus:ring-0"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import InputSwitch from 'primevue/inputswitch';
import Checkbox from 'primevue/checkbox';
import { Icon } from '@iconify/vue';

import moment from 'moment';

export default {
    name: "MGA Onboarding - Agency Approval",
    props: {
        agency_name: String,
        rocket_id: String
    },
    data(){
        return{
            ready: false,
            data: {},
            approvals: {},
            files: {},
            onload: true
        }
    },
    async mounted(){
        this.moment = moment

        await axios.get('/api/onboarding/agency/'+this.rocket_id+'/approval')
        .then(response => {
            this.data = response.data.data.agency
            this.approvals = response.data.data.approvals
            this.files = response.data.data.files
        })
        .catch(error => {
            if(error.response.status == 500){
                this.$toast.add({
                    severity: 'error',
                    summary: 'Internal Service Error',
                    detail: "Please contact a system admin",
                    life: 2500
                })
            }
        })

        const keys = Object.keys(this.approvals)
        keys.forEach(key => {
            if(this.approvals[key] == 1){
                this.approvals[key] = true
            }else if(this.approvals[key] == 0){
                this.approvals[key] = false
            }
        })
        
        this.ready = true
    },
    watch: {
        approvals: {
            async handler(value){
                if(this.ready){
                    if(this.onload){
                        this.onload = false
                    }else{
                        await axios.put('/api/onboarding/agency/'+this.rocket_id+'/approval', {
                            "approvals": value
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
                }
            },

            deep: true
        }
    },
    methods: {
        addtoRoster(){
            this.data.roster.push({
                "name": "",
                "email": "",
                "role": "agent",
            })
        },
        removeFromRoster(index){
            this.data.roster.splice(index, 1)
        },
        async saveRoster(){
            this.$emit('loading')

            await axios.put('/api/onboarding/agency/'+this.rocket_id+'/approval', {
                "agency": this.data
            })
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Agency',
                    detail: response.data.message,
                    life: 2500
                })
            })

            this.$emit('loading')
        },
        async approveAgency(){
            this.$emit('loading')

            await axios.get('/api/onboarding/approve/'+this.rocket_id)
            .then(response => {
                console.log(response)
                this.$toast.add({
                    severity: 'success',
                    summary: 'Agency Approval',
                    detail: response.data.message,
                    life: 2500
                })

                location.reload()
            })
            .catch(error => {
                this.$emit('loading')
                if(error.response.status == 400 || error.response.status == 401){
                    this.$toast.add({
                        severity: 'warn',
                        summary: 'Agency Approval',
                        detail: error.response.data.message,
                        life: 2500
                    })
                }
                if(error.response.status == 500){
                    this.$toast.add({
                        severity: 'warn',
                        summary: 'Internal Service Error',
                        detail: 'Please contact a system admin',
                        life: 2500
                    })
                }
            })
        }
    },
    components: {
        InputSwitch,
        InputText,
        Icon,
        Checkbox
    }
}
</script>


<style scoped>
:deep( .approval-checklist .checked .p-checkbox:not(.p-checkbox-disabled) .p-checkbox-box.p-highlight){
    background-color: #2FA52D !important;
    border-color: #2FA52D !important;
}
:deep( .approval-checklist .checked .p-checkbox .p-checkbox-box ){
    border-color: #2FA52D !important;
}
:deep( .approval-checklist .checked .p-checkbox .p-checkbox-box.p-highlight ){
    border-color: #2FA52D !important;
    background-color: #2FA52D !important;
}

:deep( .approval-checklist .p-checkbox:not(.p-checkbox-disabled) .p-checkbox-box.p-highlight){
    background-color: #F42D2D !important;
    border-color: #F42D2D !important;
}
:deep( .approval-checklist .p-checkbox .p-checkbox-box ){
    border-color: #F42D2D !important;
}
:deep( .approval-checklist .p-checkbox .p-checkbox-box.p-highlight ){
    border-color: #F42D2D !important;
    background-color: #F42D2D !important;
}
</style>