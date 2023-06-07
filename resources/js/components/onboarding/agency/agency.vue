<template>
    <div class="w-full h-fit grid gap-8">
        <div class="w-full h-full flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <div class="w-fit h-fit grid"><p class="w-fit h-fit truncate max-w-[700px] text-[24px] text-custom-black font-semibold">{{ agency_name }} - {{ rocket_id }}</p></div>
            </div>

            <div class="w-fit h-full flex items-center float-right">
                <!-- Update Agency Button -->
                <button @click="updateAgency" class="w-[223px] h-[48px] text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">
                    <div class="w-fit h-fit m-auto flex items-center gap-4">
                        <Icon icon="ic:round-update" height="24" />
                        <p>Update Agency</p>
                    </div>
                </button>
            </div>
        </div>

        <div v-if="ready" class="w-full h-fit grid gap-6 p-6 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
            <p class="text-[24px] text-custom-black font-medium">Agency Information</p>

            <img :src="`/images/onboarding-${data.stage}.png`" alt="Onboarding Stage">

            <div class="w-full h-fit grid grid-cols-3 gap-6 text-[16px] text-custom-black">
                <!-- Agency Information -->
                <div class="w-full h-fit grid gap-4 col-span-2">
                    <div class="w-full h-fit grid grid-cols-2 gap-6">
                        <!-- Principle Agency -->
                        <div class="w-full h-fit grid">
                            <p class="font-medium">Principle Agent</p>
                            <InputText v-model="data.agent_name" class="w-full h-[48px]" />
                        </div>

                        <!-- DBA Name -->
                        <div class="w-full h-fit grid">
                            <p class="font-medium">DBA Name</p>
                            <InputText v-model="data.dba_name" class="w-full h-[48px]" />
                        </div>
                    </div>

                    <div class="w-full h-fit grid grid-cols-2 gap-6">
                        <!-- Tax ID -->
                        <div class="w-full h-fit grid">
                            <p class="font-medium">Tax ID</p>
                            <InputMask v-model="data.agency_tax_id" mask="99-9999999" class="w-full h-[48px]" />
                        </div>

                        <!-- Agency Type -->
                        <div class="w-full h-fit grid">
                            <p class="font-medium">Agency Type</p>
                            <Dropdown v-model="data.agency_type" :options="agency_types" optionLabel="name" class="w-full h-[48px]" >
                                <template #value="slotProps">
                                    <div class="grid">
                                        <p class="truncate">{{ slotProps.value.name }}</p>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <div class="w-full h-fit grid grid-cols-2 gap-6">
                        <!-- Phone -->
                        <div class="w-full h-fit grid">
                            <p class="font-medium">Phone</p>
                            <InputMask v-model="data.phone" mask="(999) 999-9999" class="w-full h-[48px]" />
                        </div>

                        <!-- Email -->
                        <div class="w-full h-fit grid">
                            <p class="font-medium">Email</p>
                            <InputText v-model="data.email" class="w-full h-[48px]" />
                        </div>
                    </div>

                    <!--Google Autocomplete-->
                    <div class="w-full h-fit grid">
                        <p class="font-medium">Address Autocomplete</p>
                        <vue-google-autocomplete
                        ref="addressAutocomplete"
                        id="map"
                        classname="form-control"
                        placeholder="Search Address"
                        v-on:placechanged="getAddressData"
                        country="us"
                        class="w-full h-[48px] text-[16px] text-custom-black rounded-[4px] border-[1px] border-[#d3dbe3] focus:border-custom-purple"
                        >
                        </vue-google-autocomplete>
                    </div>

                    <div class="w-full h-fit grid grid-cols-2 gap-6">
                        <!-- Address 1 -->
                        <div class="w-full h-fit grid">
                            <p class="font-medium">Address 1</p>
                            <InputText v-model="data.address1" class="w-full h-[48px]" />
                        </div>

                        <!-- Address 2 -->
                        <div class="w-full h-fit grid">
                            <p class="font-medium">Address 2</p>
                            <InputText v-model="data.address2" class="w-full h-[48px]" />
                        </div>
                    </div>

                    <div class="w-full h-fit grid grid-cols-3 gap-6">
                        <!-- City -->
                        <div class="w-full h-fit grid">
                            <p class="font-medium">City</p>
                            <InputText v-model="data.city" class="w-full h-[48px]" />
                        </div>

                        <!-- State -->
                        <div class="w-full h-fit grid">
                            <p class="font-medium">State</p>
                            <InputMask v-model="data.state" mask="aa" class="w-full h-[48px]" />
                        </div>

                        <!-- Zip -->
                        <div class="w-full h-fit grid">
                            <p class="font-medium">Zip</p>
                            <InputMask v-model="data.zip" mask="99999" class="w-full h-[48px]" />
                        </div>
                    </div>
                </div>

                <!-- Agency Notes -->
                <div class="w-full h-fit grid">
                    <p class="font-medium">Agency Notes</p>
                    <Editor v-model="data.note" @selection-change="updatePersonalNote" editorStyle="height: 283px" >
                        <template v-slot:toolbar>
                            <span class="ql-formats flex items-center">
                                <button v-tooltip.bottom="'Bold'" class="ql-bold"></button>
                                <button v-tooltip.bottom="'Italic'" class="ql-italic"></button>
                                <button v-tooltip.bottom="'Underline'" class="ql-underline"></button>
                                <button v-tooltip.bottom="'Image'" class="ql-list" value="ordered"></button>
                                <button v-tooltip.bottom="'Image'" class="ql-list" value="bullet"></button>
                                <button v-tooltip.bottom="'Link'" class="ql-link"></button>
                                <button v-tooltip.bottom="'Code Block'" class="ql-code-block"></button>
                                <button v-tooltip.bottom="'Image'" class="ql-image"></button>
                            </span>
                        </template>
                    </Editor>
                </div>
            </div>
            
            <hr>

            <!-- Rocket Carrier Dispositions -->
            <div class="w-full h-fit grid gap-4">
                <p class="text-custom-black text-[16px] font-medium">Rocket Carriers</p>

                <div class="w-full h-fit flex items-center flex-wrap gap-6">
                    <div v-for="(carrier, index) in carriers" :key="index" class="w-[23%] h-[48px] flow-root p-2 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                        <div class="w-fit h-full flex items-center float-left">
                            <p class="text-custom-black text-[16px]">{{ carrier.name }}</p>
                        </div>

                        <div class="w-fit h-full flex items-center float-right">
                            <InputSwitch v-model="data[carrier.code]" :binary="true" class="h-[24px] flex items-center" />
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="w-full h-fit grid grid-cols-2 gap-6 text-[16px] text-custom-black">
                <!-- Existing Appointments -->
                <div class="w-full h-[404px] grid p-4 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                    <div class="w-full h-fit grid gap-4">
                        <div class="w-full h-full flow-root">
                            <div class="w-fit h-full flex items-center float-left">
                                <p class="font-medium">Existing Carrier Appointments</p>
                            </div>

                            <!-- Add Carrier Button -->
                            <div class="w-fit h-full flex items-center float-right">
                                <button @click="addCarrier" class="w-[48px] h-[48px] grid bg-custom-purple rounded-[4px] shadow-newdrop"><Icon icon="ic:round-plus" height="24" class="m-auto text-white" /></button>
                            </div>
                        </div>

                        <div class="w-full h-fit max-h-[300px] grid gap-2 overflow-y-scroll">
                            <div v-for="(carrier, index) in data.carriers" :key="index" class="w-full h-fit flow-root">
                                <!-- Carrier Input -->
                                <InputText v-model="carrier.name" class="w-[80%] h-[48px] float-left" />

                                <!-- Delete Carrier Button -->
                                <button @click="removeCarrier(index)" class="w-[48px] h-[48px] grid bg-white rounded-[4px] border-[1px] border-custom-black border-opacity-10 shadow-newdrop float-right"><Icon icon="fluent:delete-20-filled" height="22" class="m-auto text-custom-red" /></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointed States -->
                <div class="w-full h-[404px] grid p-4 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                    <div class="w-full h-fit grid gap-4">
                        <div class="w-full h-full flow-root">
                            <div class="w-fit h-full flex items-center float-left">
                                <p class="font-medium">Appointed States</p>
                            </div>

                            <!-- Add State Button -->
                            <div class="w-fit h-full flex items-center float-right">
                                <button @click="addState" class="w-[48px] h-[48px] grid bg-custom-purple rounded-[4px] shadow-newdrop"><Icon icon="ic:round-plus" height="24" class="m-auto text-white" /></button>
                            </div>
                        </div>

                        <div class="w-full h-fit max-h-[300px] grid gap-2 overflow-y-scroll">
                            <div v-for="(state, index) in data.additional_states" :key="index" class="w-full h-fit flow-root">
                                <!-- State Dropdown -->
                                <Dropdown v-model="data.additional_states[index]"  :options="states" optionLabel="name" class="w-[80%] h-[48px] float-left" >
                                    <template #value="slotProps">
                                        <div class="grid">
                                            <p class="truncate">{{ slotProps.value.name }}</p>
                                        </div>
                                    </template>
                                </Dropdown>

                                <!-- Delete State Button -->
                                <button @click="removeState(index)" class="w-[48px] h-[48px] grid bg-white rounded-[4px] border-[1px] border-custom-black border-opacity-10 shadow-newdrop float-right"><Icon icon="fluent:delete-20-filled" height="22" class="m-auto text-custom-red" /></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import InputMask from 'primevue/inputmask';
import Editor from 'primevue/editor';
import InputSwitch from 'primevue/inputswitch';
import { Icon } from '@iconify/vue';

import VueGoogleAutocomplete from "vue-google-autocomplete";

import agency_types from '../../../../assets/onboarding_agency_types.json'
import states from '../../../../assets/states.json'
import carriers from '../../../../assets/mga_carriers.json'

export default {
    name: "MGA Onboarding - Agency Information",
    props: {
        agency_name: String,
        rocket_id: String
    },
    data(){
        return{
            ready: false,
            carriers,
            states,
            agency_types,
            data: {}
        }
    },
    async mounted(){
        await axios.get('/api/onboarding/agency/'+this.rocket_id+'/agency')
        .then(response => {
            this.data = response.data.data.agency
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

        this.carriers.forEach(carrier => {
            if(this.data[carrier.code] == 0){
                this.data[carrier.code] = false
            }else if(this.data[carrier.code] == 1){
                this.data[carrier.code] = true
            }
        })
        
        this.ready = true
    },
    methods: {
        async removeCarrier(index){
            this.data.carriers.splice(index, 1)
        },
        async addCarrier(){
            this.data.carriers.push({name: ""})
        },
        async removeState(index){
            this.data.additional_states.splice(index, 1)
        },
        async addState(){
            this.data.additional_states.push({name: "", code: ""})
        },
        async updateAgency(){
            this.$emit('loading')

            const data = Object.assign({}, this.data)
            data.agency_tax_id = data.agency_tax_id.replace('-', '')
            data.phone = data.phone.replace(/[^\d]/g, "")

            await axios.put('/api/onboarding/agency/'+this.rocket_id+'/agency', {
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

            this.$emit('loading')
        },
        getAddressData: function (addressData, placeResultData, map) {
            this.data.address1 = `${addressData.street_number} ${addressData.route}`
            this.data.city = addressData.locality
            this.data.state = addressData.administrative_area_level_1
            this.data.zip = addressData.postal_code

            if(addressData.subpremise === undefined){
                this.data.address2 = ''
            } else {
                this.data.address2 = addressData.subpremise
            }

            this.data.address = this.data.address1 + " " + this.data.address2 + ", " + this.data.city + ", " + this.data.state + " " + this.data.zip
        },
    },
    components: {
        Dropdown,
        InputText,
        InputMask,
        Icon,
        Editor,
        InputSwitch,
        VueGoogleAutocomplete
    }
}
</script>