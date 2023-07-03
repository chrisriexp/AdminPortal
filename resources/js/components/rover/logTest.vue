<template>
    <!-- Create Task -->
    <div v-if="showTask" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center">
        <CreateTask class="m-auto" @close="$emit('close')" @loading="$emit('loading')" @updateTasks="location.reload()" :projects="projects" :task_desc="error.updates" />
    </div>

    <div v-else class="w-[604px] h-fit max-h-[800px] grid gap-4 p-6 bg-sidebar-bg rounded-[4px] shadow-newdrop overflow-y-scroll">
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                <p>Test Log</p>
            </div>

            <!-- Close -->
            <div @click="$emit('close')" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><Icon :icon="'charm:cross'" height="32" class="text-custom-red m-auto" /></div>
        </div>

        <!-- Update Test Details -->
        <div class="w-full h-fit grid gap-2">
            <Editor v-model="test.log" editorStyle="height: auto; font-size: 16px;" class="w-full" >
                <template v-slot:toolbar>
                    <span class="ql-formats flex items-center h-[30px]">
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

        <div class="w-full h-[48px] grid grid-cols-2 gap-6">
            <button @click="test.success = true" :class="test.success ? 'text-white bg-custom-green' : 'text-custom-green'" class="w-full h-full text-[16px] font-medium rounded-[2px] border-custom-green border-[1px]">Success</button>
            <button @click="test.success = false" :class="!test.success ? 'text-white bg-custom-red' : 'text-custom-red'" class="w-full h-full text-[16px] font-medium rounded-[2px] border-custom-red border-[1px]">Failed</button>
        </div>

        <!-- Update Stage -->
        <div class="w-full h-fit grid gap-2">
            <button @click="updateStage" class="w-full h-[48px] text-white text-[16px] font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Log Test</button>
        </div>
    </div>
</template>

<script>
import { Icon } from '@iconify/vue';
import Editor from 'primevue/editor';

export default {
    name: "ROVER - Log Test Popup",
    props:{
        id: String,
        users: Array
    },
    data(){
        return{
            test: {
                log: {},
                success: false
            }
        }
    },
    methods: {
        async updateStage(){
            this.$emit('loading')

            if(Object.keys(this.test).length == 0){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Update Validation',
                    detail: "You are missing one or more required fields",
                    life: 2500
                })

                this.$emit('loading')
                return
            }

            await axios.post('/api/rover/test/'+this.id, {"test": this.test})
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Error Update',
                    detail: response.data.message,
                    life: 2500
                })

                location.reload()
            })
        }
    },
    components: {
        Icon,
        Editor
    }
}
</script>