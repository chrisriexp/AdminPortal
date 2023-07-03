<template>
    <!-- Loading -->
    <div :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
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
                <!-- Go to Aub Agents -->
                <router-link :to="{name: 'REACT_SubAgents'}" class="w-full h-[48px] grid text-custom-black hover:text-white hover:bg-custom-black rounded-[4px] border-[1px] border-custom-black">
                    <div class="w-fit h-fit flex items-center gap-4 m-auto">
                        <Icon :icon="'fluent:people-12-filled'" height="24" />
                        <p class="text-[16px] font-semibold">View Sub Agents</p>
                    </div>
                </router-link>
            </div>
        </div>

        <div class="w-full h-full z-10 absolute pt-[110px] pl-[350px] pr-[50px] overflow-y-scroll">
            <div class="w-full h-fit flow-root">
                <div class="w-fit h-fit grid gap-2 text-custom-black float-left">
                    <p class="text-[32px] font-semibold">REACT</p>
                    <p class="text-[16px] opacity-60">Here you can upload carrier commission statements</p>
                </div>
                
                <div class="w-fit h-fit flex items-center gap-6 float-right">
                    <!-- Month Select -->
                    <div class="w-fit h-full flex items-center gap-6">
                        <p class="text-[18px] text-custom-black font-medium opacity-60">Select Month:</p>
                        <Calendar v-model="month" view="month" dateFormat="yy-mm" />
                    </div>

                    <!-- Run Accouting Button -->
                    <button @click="loading = true; runAccounting()" class="w-[254px] h-[48px] text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">
                        <div class="m-auto w-fit h-fit flex items-center gap-4">
                            <Icon :icon="'fluent:calculator-20-filled'" height="24" />
                            <p>Run Accounting</p>
                        </div>
                    </button>
                </div>
            </div>

            <div class="w-full h-fit grid grid-cols-2 mt-12 border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
                <!-- Upload Instructions -->
                <div class="w-full h-full grid p-10 text-custom-black text-[16px] border-r-[1px] border-custom-black border-opacity-10">
                    <p class="text-[24px] font-medium">Instructions</p>

                    <!-- Instruction List -->
                    <div class="w-fit h-fit grid gap-4 text-[20px]">
                        <div class="w-fit h-fit flex items-center gap-4">
                            <div class="w-fit h-fit"><div class="w-[10px] h-[10px] rounded-full bg-custom-red"></div></div>
                            <p class="opacity-50">See <span class="text-custom-purple underline">"Help Center"</span> for details instructions to format the commission statement(s) for upload</p>
                        </div>
                        <div class="w-fit h-fit flex items-center gap-4">
                            <div class="w-fit h-fit"><div class="w-[10px] h-[10px] rounded-full bg-custom-red"></div></div>
                            <p class="opacity-50">Rename statement(s) to carrier name (i.e Palomar.xlsx, WrightNational.xlsx)</p>
                        </div>
                        <div class="w-fit h-fit flex items-center gap-4">
                            <div class="w-fit h-fit"><div class="w-[10px] h-[10px] rounded-full bg-custom-red"></div></div>
                            <p class="opacity-50">Upload commission statement(s) below</p>
                        </div>
                    </div>

                    <div class="w-fit h-fit grid gap-2 justify-items-center mx-auto">
                        <Icon :icon="'mingcute:file-upload-fill'" height="78" class="opacity-50" />
                        <p class="text-[14px] opacity-50">xlsx file(s) smaller than 200MB</p>
                    </div>

                    <div class="w-fit h-fit grid gap-2 justify-items-center mx-auto">
                        <p class="font-medium">Upload carrier commission statement(s) here</p>

                        <!-- Upload Statements -->
                        <input @change="uploadFile($event)" ref="fileUploader" type="file" accept=".xlsx, .csv" multiple name="fileUpload" id="fileUpload" class="hidden">
                        <button @click="$refs.fileUploader.click()" class="w-[176px] h-[48px] text-white font-semibold bg-custom-black rounded-[4px] shadow-newdrop">Browse Files</button>
                    </div>
                </div>

                <!-- Carrier Statements -->
                <div class="w-full h-fit grid gap-4 p-10 text-custom-black text-[16px]">
                    <p class="text-[24px] font-medium">Upload carrier statement(s)</p>

                    <div class="w-full h-fit grid gap-4 carrier-uploads">
                        <div v-for="(carrier, index) in uploaded" :key="index" :class="carrier.exists ? 'carrier-exists' : ''" class="w-full h-[44px] grid px-6 border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                            <div class="w-fit h-fit my-auto flex items-center gap-4">
                                <Checkbox v-if="carrier.exists" disabled v-model="carrier.exists" :binary="true" />
                                <Checkbox v-else disabled v-model="carrier.uploaded" :binary="true" />
                                <p :class="carrier.uploaded ? 'text-custom-green' : 'opacity-50'">{{ carrier.name }} Commission Statement</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import topNav from '../../components/topNav.vue'
import loading from '../../components/loading.vue'
import { Icon } from '@iconify/vue';
import Checkbox from 'primevue/checkbox';
import Calendar from 'primevue/calendar';

import carriers from '../../../assets/react_carriers.json'
import moment from 'moment';

export default{
    name: "REACT - Upload Statements",
    data(){
        return {
            loading: true,
            carriers,
            month: "",
            uploaded: [],
            onLoad: true,
        }
    },
    async created(){
        this.moment = moment

        this.moment = moment
        this.month = moment(Date.now()).format('YYYY-MM')

        await axios.get('/api/react/commission/check/'+this.month)
        .then(response => {
            this.carriers.forEach(carrier => {
                this.uploaded.push({
                    "name": carrier.name,
                    "code": carrier.code,
                    "file": "",
                    "uploaded": false,
                    "exists": response.data.carriers[carrier.code]
                })
            })
        })

        this.loading = false
    },
    watch: {
        month: async function(value){
            if(this.onLoad){
                this.onLoad = false
            }else{
                this.loading = true

                this.month = this.month = moment(value).format('YYYY-MM')

                await axios.get('/api/react/commission/check/'+this.month)
                .then(response => {
                    this.uploaded = [];
                    this.carriers.forEach(carrier => {
                        this.uploaded.push({
                            "name": carrier.name,
                            "code": carrier.code,
                            "file": "",
                            "uploaded": false,
                            "exists": response.data.carriers[carrier.code]
                        })
                    })
                })

                this.loading = false
            }
        }
    },
    methods: {
        async uploadFile (e) {
            const files = Object.keys(e.target.files)

            files.forEach(file => {
                const name = e.target.files[file].name

                this.uploaded.forEach(carrier => {
                    if(name.toLowerCase().includes(carrier.code)){
                        if(carrier.exists){
                            this.$toast.add({
                                severity: 'warn',
                                summary: 'Commission Upload',
                                detail: 'A '+carrier.name+' commission statement has already been uploaded for this month',
                                life: 2500
                            })
                        }else {
                            carrier.file = e.target.files[file]
                            carrier.uploaded = true
                        }
                    }
                })
            })
        },
        async runAccounting(){
            this.loading = true

            this.uploaded.forEach(async carrier => {
                if(carrier.uploaded){
                    const fileHeader = {
                        headers: {'content-type': 'multipart/form-data'}
                    }

                    let fileData = new FormData();
                    fileData.append('file', carrier.file);
                    fileData.append('month', this.month);
                    fileData.append('type', carrier.code);

                    await axios.post('/api/react/commission/upload/'+this.month, fileData, fileHeader)
                    .then(response => {
                        if(response.data.success){
                            this.$toast.add({
                                severity: 'success',
                                summary: 'Commission Upload',
                                detail: response.data.message,
                                life: 2500
                            })
                        }
                    })
                    .catch(error => {
                        console.log(error.response)
                        if(error.response.status == 400){
                            this.$toast.add({
                                severity: 'warn',
                                summary: 'Commission Upload',
                                detail: error.response.data.message,
                                life: 3000
                            })
                        } else if(error.response.status == 500){
                            this.$toast.add({
                                severity: 'error',
                                summary: 'Commission Upload',
                                detail: "Internal Service Error - Please contact a system admin",
                                life: 3000
                            })
                        }
                    })
                }
            })

            this.loading = false
        }
    },
    components: {
        topNav,
        loading,
        Icon,
        Checkbox,
        Calendar
    }
}
</script>

<style scoped>
:deep( .p-checkbox .p-checkbox-box.p-highlight ){
    background-color: #2FA52D !important;
    border-color: #2FA52D !important;
}
:deep( .carrier-exists .p-checkbox .p-checkbox-box.p-highlight ){
    background-color: #a778e5 !important;
    border-color: #a778e5 !important;
}
</style>