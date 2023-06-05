<template>
    <div class="w-full h-fit grid gap-8">
        <!-- File Uploader -->
        <input @change="uploadFile($event)" ref="fileUploader" type="file" accept=".pdf" multiple name="fileUpload" id="fileUpload" class="hidden">
        
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

        <div v-if="ready" class="w-full h-fit grid gap-6 text-[16px] text-custom-black">
            <!-- Custom Packages -->
            <div class="w-full h-fit grid p-6 bg-white rounded-[4px] border-[1px] border-custom-black border-opacity-10">
                <div class="w-full h-fit flex items-center flex-wrap gap-6">
                    <!-- Flow Package -->
                    <div class="w-[23%] h-[48px] flow-root px-4 bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                        <div class="w-fit h-full flex items-center font-medium float-left">
                            <p>Flow Package</p>
                        </div>

                        <div class="w-fit h-full flex items-center gap-4 font-medium float-right">
                            <!-- Download -->
                            <a v-if="files.flow_package != null" :href="files.flow_package" target="_blank"><Icon icon="mingcute:file-download-fill" height="24" class="text-custom-purple" /></a>
                            <Icon v-else icon="mdi:error" height="24" class="text-custom-purple" />

                            <!-- Upload -->
                            <button @click="uploadType = 'flow_package' ; $refs.fileUploader.click()" class="w-fit h-fit"><Icon icon="mingcute:file-upload-fill" height="24" class="text-custom-black" /></button>
                        </div>
                    </div>

                    <!-- Beyond Package -->
                    <div class="w-[23%] h-[48px] flow-root px-4 bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                        <div class="w-fit h-full flex items-center font-medium float-left">
                            <p>Beyond Package</p>
                        </div>

                        <div class="w-fit h-full flex items-center gap-4 font-medium float-right">
                            <!-- Download -->
                            <a v-if="files.beyond_package != null" :href="files.beyond_package" target="_blank"><Icon icon="mingcute:file-download-fill" height="24" class="text-custom-purple" /></a>
                            <Icon v-else icon="mdi:error" height="24" class="text-custom-purple" />

                            <!-- Upload -->
                            <button @click="uploadType = 'beyond_package' ; $refs.fileUploader.click()" class="w-fit h-fit"><Icon icon="mingcute:file-upload-fill" height="24" class="text-custom-black" /></button>
                        </div>
                    </div>

                    <!-- Sterling Package -->
                    <div class="w-[23%] h-[48px] flow-root px-4 bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                        <div class="w-fit h-full flex items-center font-medium float-left">
                            <p>Sterling Package</p>
                        </div>

                        <div class="w-fit h-full flex items-center gap-4 font-medium float-right">
                            <!-- Download -->
                            <a v-if="files.sterling_package != null" :href="files.sterling_package" target="_blank"><Icon icon="mingcute:file-download-fill" height="24" class="text-custom-purple" /></a>
                            <Icon v-else icon="mdi:error" height="24" class="text-custom-purple" />

                            <!-- Upload -->
                            <button @click="uploadType = 'sterling_package' ; $refs.fileUploader.click()" class="w-fit h-fit"><Icon icon="mingcute:file-upload-fill" height="24" class="text-custom-black" /></button>
                        </div>
                    </div>

                    <!-- Dual Package -->
                    <div class="w-[23%] h-[48px] flow-root px-4 bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                        <div class="w-fit h-full flex items-center font-medium float-left">
                            <p>Dual Package</p>
                        </div>

                        <div class="w-fit h-full flex items-center gap-4 font-medium float-right">
                            <!-- Download -->
                            <a v-if="files.dual_package != null" :href="files.dual_package" target="_blank"><Icon icon="mingcute:file-download-fill" height="24" class="text-custom-purple" /></a>
                            <Icon v-else icon="mdi:error" height="24" class="text-custom-purple" />

                            <!-- Upload -->
                            <button @click="uploadType = 'dual_package' ; $refs.fileUploader.click()" class="w-fit h-fit"><Icon icon="mingcute:file-upload-fill" height="24" class="text-custom-black" /></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rocket Agreement -->
            <div class="w-full h-[68px] grid px-6 bg-white rounded-[4px] border-[1px] border-custom-black border-opacity-10">
                <div class="w-full h-fit my-auto grid grid-cols-3">
                    <p class="font-medium">Rocket MGA Agreement</p>

                    <!-- Download -->
                    <div class="w-full h-fit grid">
                        <a v-if="files.agreement != null" :href="files.agreement" target="_blank" class="w-fit h-fit flex items-center gap-4 m-auto text-custom-purple">
                            <Icon icon="mingcute:file-download-fill" height="24" />
                            <p>Download Document</p>
                        </a>
                        <div v-else class="w-fit h-fit flex items-center gap-4 m-auto text-custom-purple">
                            <Icon icon="mdi:error" height="24" />
                            <p>Document Not Found</p>
                        </div>
                    </div>

                    <!-- Upload -->
                    <button @click="uploadType = 'agreement' ; $refs.fileUploader.click()" class="w-full h-fit flow-root">
                        <div class="w-fit h-fit flex items-center gap-4 text-custom-black float-right">
                            <Icon icon="mingcute:file-upload-fill" height="24" />
                            <p>Upload Document</p>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Agency License -->
            <div class="w-full h-fit grid gap-4 p-6 bg-white rounded-[4px] border-[1px] border-custom-black border-opacity-10">
                <div class="w-full h-fit my-auto grid grid-cols-3">
                    <p class="font-medium">Agency License</p>

                    <!-- Download -->
                    <div class="w-full h-fit grid">
                        <a v-if="files.agency_license_file != null" :href="files.agency_license_file" target="_blank" class="w-fit h-fit flex items-center gap-4 m-auto text-custom-purple">
                            <Icon icon="mingcute:file-download-fill" height="24" />
                            <p>Download Document</p>
                        </a>
                        <div v-else class="w-fit h-fit flex items-center gap-4 m-auto text-custom-purple">
                            <Icon icon="mdi:error" height="24" />
                            <p>Document Not Found</p>
                        </div>
                    </div>

                    <!-- Upload -->
                    <button @click="uploadType = 'agency_license_file' ; $refs.fileUploader.click()" class="w-full h-fit flow-root">
                        <div class="w-fit h-fit flex items-center gap-4 text-custom-black float-right">
                            <Icon icon="mingcute:file-upload-fill" height="24" />
                            <p>Upload Document</p>
                        </div>
                    </button>
                </div>

                <div class="w-full h-fit flex items-center">
                    <div class="w-[23%] h-fit gird">
                        <p>License Number</p>
                        <InputText v-model="data.agency_license" class="w-full h-[32px] float-left rounded-[2px]" />
                    </div>
                </div>
            </div>

            <!-- Agent License -->
            <div class="w-full h-fit grid gap-4 p-6 bg-white rounded-[4px] border-[1px] border-custom-black border-opacity-10">
                <div class="w-full h-fit my-auto grid grid-cols-3">
                    <p class="font-medium">Agent License</p>

                    <!-- Download -->
                    <div class="w-full h-fit grid">
                        <a v-if="files.agent_license_file != null" :href="files.agent_license_file" target="_blank" class="w-fit h-fit flex items-center gap-4 m-auto text-custom-purple">
                            <Icon icon="mingcute:file-download-fill" height="24" />
                            <p>Download Document</p>
                        </a>
                        <div v-else class="w-fit h-fit flex items-center gap-4 m-auto text-custom-purple">
                            <Icon icon="mdi:error" height="24" />
                            <p>Document Not Found</p>
                        </div>
                    </div>

                    <!-- Upload -->
                    <button @click="uploadType = 'agent_license_file' ; $refs.fileUploader.click()" class="w-full h-fit flow-root">
                        <div class="w-fit h-fit flex items-center gap-4 text-custom-black float-right">
                            <Icon icon="mingcute:file-upload-fill" height="24" />
                            <p>Upload Document</p>
                        </div>
                    </button>
                </div>

                <div class="w-full h-fit flex items-center flex-wrap gap-6">
                    <div class="w-[23%] h-fit gird">
                        <p>License Number</p>
                        <InputText v-model="data.agent_license" class="w-full h-[32px] float-left rounded-[2px]" />
                    </div>

                    <div class="w-[23%] h-fit gird">
                        <p>License Eff Date</p>
                        <Calendar v-model="data.agent_license_eff" showIcon class="h-[32px]" />
                    </div>

                    <div class="w-[23%] h-fit gird">
                        <p>License Exp Date</p>
                        <InputText v-model="data.agent_license_exp" class="w-full h-[32px] float-left rounded-[2px]" />
                    </div>

                    <div class="w-[23%] h-fit gird">
                        <p>Agent NPN</p>
                        <InputText v-model="data.agent_npn" class="w-full h-[32px] float-left rounded-[2px]" />
                    </div>
                </div>
            </div>

            <!-- Agency E&O -->
            <div class="w-full h-fit grid gap-4 p-6 bg-white rounded-[4px] border-[1px] border-custom-black border-opacity-10">
                <div class="w-full h-fit my-auto grid grid-cols-3">
                    <p class="font-medium">Agent E&O</p>

                    <!-- Download -->
                    <div class="w-full h-fit grid">
                        <a v-if="files.eo != null" :href="files.eo" target="_blank" class="w-fit h-fit flex items-center gap-4 m-auto text-custom-purple">
                            <Icon icon="mingcute:file-download-fill" height="24" />
                            <p>Download Document</p>
                        </a>
                        <div v-else class="w-fit h-fit flex items-center gap-4 m-auto text-custom-purple">
                            <Icon icon="mdi:error" height="24" />
                            <p>Document Not Found</p>
                        </div>
                    </div>

                    <!-- Uplaod -->
                    <button @click="uploadType = 'eo' ; $refs.fileUploader.click()" class="w-full h-fit flow-root">
                        <div class="w-fit h-fit flex items-center gap-4 text-custom-black float-right">
                            <Icon icon="mingcute:file-upload-fill" height="24" />
                            <p>Upload Document</p>
                        </div>
                    </button>
                </div>

                <div class="w-full h-fit flex items-center flex-wrap gap-6">
                    <div class="w-[23%] h-fit gird">
                        <p>Policy Number</p>
                        <InputText v-model="data.eo_policy" class="w-full h-[32px] float-left rounded-[2px]" />
                    </div>

                    <div class="w-[23%] h-fit gird">
                        <p>Exp Date</p>
                        <Calendar v-model="data.eo_exp" showIcon class="h-[32px]" />
                    </div>

                    <div class="w-[23%] h-fit gird">
                        <p>Limit</p>
                        <InputText v-model="data.eo_limit" class="w-full h-[32px] float-left rounded-[2px]" />
                    </div>

                    <div class="w-[23%] h-fit gird">
                        <p>Insurer</p>
                        <InputText v-model="data.eo_insurer" class="w-full h-[32px] float-left rounded-[2px]" />
                    </div>
                </div>
            </div>

            <!-- Agency Logo -->
            <div class="w-full h-[68px] grid px-6 bg-white rounded-[4px] border-[1px] border-custom-black border-opacity-10">
                <div class="w-full h-fit my-auto grid grid-cols-3">
                    <p class="font-medium">Company Logo</p>

                    <!-- Download -->
                    <div class="w-full h-fit grid">
                        <a v-if="files.agency_logo != null" :href="files.agency_logo" target="_blank" class="w-fit h-fit flex items-center gap-4 m-auto text-custom-purple">
                            <Icon icon="mingcute:file-download-fill" height="24" />
                            <p>Download Document</p>
                        </a>
                        <div v-else class="w-fit h-fit flex items-center gap-4 m-auto text-custom-purple">
                            <Icon icon="mdi:error" height="24" />
                            <p>Document Not Found</p>
                        </div>
                    </div>

                    <!-- Upload -->
                    <button @click="uploadType = 'agency_logo' ; $refs.fileUploader.click()" class="w-full h-fit flow-root">
                        <div class="w-fit h-fit flex items-center gap-4 text-custom-black float-right">
                            <Icon icon="mingcute:file-upload-fill" height="24" />
                            <p>Upload Document</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import InputMask from 'primevue/inputmask';
import Calendar from 'primevue/calendar';
import { Icon } from '@iconify/vue';

import moment from 'moment';

export default {
    name: "MGA Onboarding - Agency Documents",
    props: {
        agency_name: String,
        rocket_id: String
    },
    data(){
        return{
            ready: false,
            data: {},
            files: {},
            uploadType: ""
        }
    },
    async mounted(){
        this.moment = moment
        this.$emit('loading')

        await axios.get('/api/onboarding/agency/'+this.rocket_id+'/documents')
        .then(response => {
            this.data = response.data.data.agency
            this.files = response.data.data.files
            this.data.agent_license_eff = moment(this.data.agent_license_eff).format('MM/DD/YYYY')
            this.data.agent_license_exp = moment(this.data.agent_license_exp).format('MM/DD/YYYY')
            this.data.eo_exp = moment(this.data.eo_exp).format('MM/DD/YYYY')
        })

        console.log(this.files)
        
        this.ready = true
        this.$emit('loading')
    },
    methods: {
        async updateAgency(){
            const dates = ['agent_license_eff', 'agent_license_exp', 'eo_exp']
            const data = Object.assign({}, this.data)

            dates.forEach(date => {
                if(data[date] != 'Invalid date'){
                    data[date] = moment(data[date]).format('YYYY-MM-DD')
                }else{
                    data[date] = null
                }
            })

            await axios.put('/api/onboarding/agency/'+this.rocket_id+'/documents', {
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
        },
        async uploadFile (e) {
            const file = e.target.files[0]

            //Upload file
            const fileHeader = {
                headers: {'content-type': 'multipart/form-data', 'token': '27b00fca-4d9e-4e28-85ce-54f16af26c0b'}
            }

            let fileData = new FormData();
            fileData.append('file', file);
            fileData.append('type', this.uploadType);
            fileData.append('rocket_id', this.rocket_id);
            
            await axios.post('https://onboarding.rocketmga.com/api/admin-portal/agency/admin-upload', fileData, fileHeader)
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Agency',
                    detail: response.data.message,
                    life: 2500
                })
                location.reload()
            })
        }
    },
    components: {
        Dropdown,
        InputText,
        InputMask,
        Calendar,
        Icon,
    }
}
</script>