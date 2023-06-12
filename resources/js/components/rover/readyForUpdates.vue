<template>
    <!-- Create Task -->
    <div v-if="showTask" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center">
        <CreateTask class="m-auto" @close="$emit('close')" @loading="$emit('loading')" @updateTasks="location.reload()" :projects="projects" :task_desc="error.updates" />
    </div>

    <div v-else class="w-[604px] h-fit max-h-[800px] grid gap-4 p-6 bg-sidebar-bg rounded-[4px] shadow-newdrop overflow-y-scroll">
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                <p>Ready for Updates</p>
            </div>

            <!-- Close -->
            <div @click="$emit('close')" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><Icon :icon="'charm:cross'" height="32" class="text-custom-red m-auto" /></div>
        </div>

        <!-- Error Cause -->
        <div class="w-full h-fit grid">
            <p class="text-[16px] text-custom-black font-medium">Cause of Error</p>
            <Editor v-model="error.cause" editorStyle="height: auto; font-size: 16px;" class="w-full hide-toolbar border-t-[1px]" >
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

        <!-- Updates to Implement -->
        <div class="w-full h-fit grid">
            <p class="text-[16px] text-custom-black font-medium">Updates to Implement</p>
            <Editor v-model="error.updates" editorStyle="height: auto; font-size: 16px;" class="w-full" >
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

        <!-- Assign to -->
        <Dropdown v-model="error.assigned" :options="users" optionLabel="name" class="w-full h-[48px] flex items-center" >
            <template #value="slotProps">
                <div class="grid">
                    <p class="truncate flex items-center gap-2">Assigned to: {{ slotProps.value.name }}</p>
                </div>
            </template>
        </Dropdown>

        <!-- Create Asana Task -->
        <div class="w-full h-[52px] flow-root px-6 rounded-[4px] bg-[#F1F1F1] border-custom-black border-opacity-10 border-[1px]">
            <div class="w-fit h-full flex items-center float-left">
                <p class="text-[16px] text-custom-black font-medium">Create Pipeline Task</p>
            </div>

            <!-- Input Switch -->
            <div class="w-fit h-full flex items-center float-right">
                <InputSwitch v-model="createTask" :binary="true" class="h-[24px] flex items-center" />
            </div>
        </div>

        <!-- Update Stage -->
        <div class="w-full h-fit grid gap-2">
            <button @click="updateStage" class="w-full h-[48px] text-white text-[16px] font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Update Stage</button>
        </div>
    </div>
</template>

<script>
import { Icon } from '@iconify/vue';
import Editor from 'primevue/editor';
import InputSwitch from 'primevue/inputswitch';
import Dropdown from 'primevue/dropdown';

import CreateTask from '../pipeline/createTask.vue'

export default {
    name: "ROVER - Ready for Updates Popup",
    props:{
        id: String,
        users: Array
    },
    data(){
        return{
            error: {
                cause: {},
                updates: {},
                assigned: {},
            },
            createTask: false,
            showTask: false,
            projects: []
        }
    },
    methods: {
        async updateStage(){
            this.$emit('loading')

            if(Object.keys(this.error.cause).length == 0 || Object.keys(this.error.updates).length == 0 || Object.keys(this.error.assigned).length == 0){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Update Validation',
                    detail: "You are missing one or more required fields",
                    life: 2500
                })

                this.$emit('loading')
                return
            }

            await axios.put('/api/rover/stage/'+this.id, {"error": this.error})
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Error Update',
                    detail: response.data.message,
                    life: 2500
                })
            })

            if(this.createTask){
                this.$emit('loading')

                await axios.get('/api/pipeline/projects')
                .then(response => {
                    this.projects = response.data.projects
                })

                this.showTask = true
            }else{
                location.reload()
            }
        }
    },
    components: {
        Icon,
        Editor,
        InputSwitch,
        CreateTask,
        Dropdown
    }
}
</script>

<style scoped>
:deep( .hide-toolbar .ql-toolbar){
    display: none;
}
</style>