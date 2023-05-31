<template>
    <div class="w-[604px] h-fit p-6 grid gap-4 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div class="w-full h-full flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <p class="text-[32px] truncate text-custom-black font-semibold">Bulk Upload Agents</p>
            </div>

            <div class="w-fit h-full flex items-center float-right">
                <!-- Close Button -->
                <button @click="$emit('close')" class="w-[39px] h-[39px] grid bg-white shadow-newdrop rounded-[2px]">
                    <Icon :icon="'charm:cross'" height="24" class="text-custom-red m-auto" />
                </button>
            </div>
        </div>

        <div class="w-full h-fit flex items-center gap-4">
            <div class="w-fit h-fit">
                <div class="w-[10px] h-[10px] rounded-full bg-custom-red"></div>
            </div>

            <p class="text-[18px] text-custom-black opacity-50">See <span class="text-custom-purple underline">"Help Center"</span> for detailed information on how to format sub agent upload spreadsheets.</p>
        </div>

        <div class=" w-full h-fit grid justify-items-center gap-2 text-[14px] text-custom-black opacity-50">
            <Icon :icon="'mingcute:file-upload-fill'" height="78" />
            <p>xlsx file smaller than 200MB</p>
        </div>

        <!-- Upload Spreadsheet -->
        <input @change="uploadFile($event)" ref="fileUploader" type="file" accept=".xlsx" multiple name="fileUpload" id="fileUpload" class="hidden">
        <button @click="$refs.fileUploader.click()" class="w-[175px] h-[48px] mx-auto text-[16px] text-white font-semibold bg-custom-black rounded-[4px] shadow-newdrop">Browse Files</button>

        <div class="w-full h-fit grid">
            <!-- Add Sub Agents -->
            <button @click="addAgents" class="my-4 w-full h-[48px] text-[16px] text-white font-seimbold bg-custom-purple rounded-[4px] shadow-newdrop">Add Agents</button>
            <!-- Back to Add Menu -->
            <button @click="$emit('back')" class="text-[16px] text-custom-purple font-medium underline">Back To Select Menu</button>
        </div>
    </div>
</template>

<script>
import { Icon } from '@iconify/vue';

export default {
    name: "Add Agent - Bulk",
    data(){
        return{
            file: {}
        }
    },
    methods: {
        async uploadFile (e) {
            this.file = e.target.files[0]
        },
        async addAgents(){
            this.$emit('loading')
            if(!this.file.name){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Bulk Agent Upload',
                    detail: 'Please upload a file before adding agents',
                    life: 2500
                })

                this.$emit('loading')
                return
            }

            const fileHeader = {
                headers: {'content-type': 'multipart/form-data'}
            }

            let fileData = new FormData();
            fileData.append('file', this.file);

            await axios.post('/api/react/sub-agent/upload', fileData, fileHeader)
            .then(response => {
                console.log(response)
                if(response.data.success){
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Bulk Agent Upload',
                        detail: response.data.message,
                        life: 2500
                    })

                    this.$emit('loading')
                    this.$emit('update')
                }
            })
            .catch(error => {
                this.$emit('loading')
                
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
    },
    components: {
        Icon
    }
}
</script>