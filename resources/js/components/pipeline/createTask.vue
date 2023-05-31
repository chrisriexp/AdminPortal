<template>
    <div class="w-fit h-fit grid gap-6 p-10 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                <p>Create Task</p>
            </div>

            <!-- Close Create Task -->
            <div @click="$emit('close')" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><XMarkIcon class="h-[24px] m-auto text-custom-red" /></div>
        </div>

        <!-- Task Informatio -->
        <div class="w-full h-fit grid grid-cols-2 gap-4">
            <div class="w-[540px] h-full grid">
                <!-- Project Select -->
                <Dropdown v-model="task.project" :options="projects" optionLabel="name" class="w-full h-[48px] flex items-center" >
                    <template #value="slotProps">
                        <div class="grid">
                            <p class="flex items-center gap-2 truncate"><span class="text-[16px] opacity-80 text-custom-black  flex items-center gap-2"><PuzzlePieceIcon class="h-[24px]" />Project:</span> {{ slotProps.value.name }}</p>
                        </div>
                    </template>
                </Dropdown>

                <!-- Task Name -->
                <InputText :disabled="task.project_id ? false : true" v-model="task.name" placeholder="Task Name *" class="h-[48px] w-full" />

                <!-- Section Select -->
                <Dropdown :disabled="task.project_id ? false : true" v-model="task.section" :options="sections" optionLabel="name" class="w-full h-[48px] flex items-center disabled:bg-custom-bg" >
                    <template #value="slotProps">
                        <div class="flex gap-2 items-center">
                            <p class="flex items-center gap-2"><span class="text-[16px] opacity-80 text-custom-black  flex items-center gap-2"><FolderIcon class="h-[24px]" />Section:</span> {{ slotProps.value.name }}</p>
                        </div>
                    </template>
                </Dropdown>

                <!-- Assigned To -->
                <Dropdown :disabled="task.project_id ? false : true" v-model="task.assigned" :options="members" optionLabel="name" class="w-full h-[48px] flex items-center disabled:bg-custom-bg" >
                    <template #value="slotProps">
                        <div class="flex gap-2 items-center">
                            <p class="flex items-center gap-2"><span class="text-[16px] opacity-80 text-custom-black  flex items-center gap-2"><UserCircleIcon class="h-[24px]" />Assigned To:</span> {{ slotProps.value.name }}</p>
                        </div>
                    </template>
                </Dropdown>

                <Calendar :disabled="task.project.name ? false : true" showIcon v-model="task.due_date" dateFormat="yy-mm-dd" placeholder="Due Date" class="w-full h-[48px]" />

                <!-- Priority -->
                <Dropdown :disabled="task.project_id ? false : true" v-model="task.priority" :options="priorityOptions" optionLabel="name" class="w-full h-[48px] flex items-center disabled:bg-custom-bg" >
                    <template #value="slotProps">
                        <div class="flex gap-2 items-center">
                            <p class="flex items-center gap-2"><span class="text-[16px] opacity-80 text-custom-black  flex items-center gap-2"><ExclamationTriangleIcon class="h-[24px]" />Priority:</span> <span :class="'bg-['+slotProps.value.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ slotProps.value.name }}</span></p>
                        </div>
                    </template>
                    <template #option="slotProps">
                        <div class="flex align-items-center">
                            <div :class="'bg-['+slotProps.option.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ slotProps.option.name }}</div>
                        </div>
                    </template> 
                </Dropdown>

                <!-- Progress -->
                <Dropdown :disabled="task.project_id ? false : true" v-model="task.progress" :options="progressOptions" optionLabel="name" class="w-full h-[48px] flex items-center disabled:bg-custom-bg" >
                    <template #value="slotProps">
                        <div class="flex gap-2 items-center">
                            <p class="flex items-center gap-2"><span class="text-[16px] opacity-80 text-custom-black  flex items-center gap-2"><MapPinIcon class="h-[24px]" />Progress:</span> <span :class="'bg-['+slotProps.value.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ slotProps.value.name }}</span></p>
                        </div>
                    </template>
                    <template #option="slotProps">
                        <div class="flex align-items-center">
                            <div :class="'bg-['+slotProps.option.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ slotProps.option.name }}</div>
                        </div>
                    </template> 
                </Dropdown>

                <!-- Tags -->
                <MultiSelect :disabled="task.project_id ? false : true" v-model="task.tags" :options="tags" optionLabel="name" placeholder="Projects" class="w-full h-[48px] flex items-center" >
                    <template #value="">
                        <div class="flex gap-2 items-center">
                            <p class="flex items-center gap-2"><span class="text-[16px] opacity-80 text-custom-black  flex items-center gap-2"><TagIcon class="h-[24px]" />Tags</span></p>
                        </div>
                    </template>

                    <template #option="slotProps">
                        <div class="flex gap-2 items-center">
                            <div :class="'bg-['+slotProps.option.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
            </div>

            <Editor v-model="task.desc" id="newTaskDesc" editorStyle="height: 408px" class="w-[540px]" />
        </div>

        <div class="w-fit max-w-[540px] h-fit max-h-[80px] flex flex-wrap gap-2 overflow-y-scroll">
            <p v-for="(tag, index) in task.tags" :key="index" style="user-select: none;" :class="'bg-['+tag.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ tag.name }}</p>
        </div>

        <!-- Add Section -->
        <div class="w-full h-fit grid gap-2">
            <button @click="createTask()" class="w-full h-[48px] text-white text-[16px] font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Create Task</button>
        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import MultiSelect from 'primevue/multiselect';
import Calendar from 'primevue/calendar';
import Editor from 'primevue/editor';

import { XMarkIcon, PuzzlePieceIcon, FolderIcon, UserCircleIcon, ExclamationTriangleIcon, MapPinIcon, TagIcon } from '@heroicons/vue/24/solid'

export default {
    name: "Create Task",
    props: {
        projects: Array,
        project_id: String
    },
    data(){
        return{
            task: {
                name: "",
                project_id: "",
                project: {},
                section: {},
                section_id: "",
                assigned: {},
                due_date: "",
                priority: {},
                progress: {},
                tags: [],
                desc: {}
            },
            sections: [],
            members: [],
            priorityOptions: [],
            progressOptions: [],
            tags: []
        }
    },
    async mounted(){
        if(this.project_id != 'my-tasks'){
            this.projects.forEach(project => {
                if(project.id == this.project_id){
                    this.task.project = project
                }
            })
        }
    },
    watch: {
        'task.project': async function(value){
            this.task.project_id = this.task.project.id

            await axios.get('/api/pipeline/project/'+this.task.project.id+'/details')
            .then(response => {
                this.sections = response.data.sections
                this.members = response.data.members
                this.priorityOptions = response.data.priority
                this.progressOptions = response.data.progress
                this.tags = response.data.tags
            })

            this.task.section = {}
            this.task.assigned = {}
            this.task.priority = {}
            this.task.progress = {}
            this.task.tags = []
        },
        'task.section': async function(value){
            this.task.section_id = this.task.section.id
        }
    },
    methods: {
        async createTask(){
            if(!this.task.project.name){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Task',
                    detail: "Please select a Project for this task.",
                    life: 2500
                })
                return
            }

            if(!this.task.name){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Task',
                    detail: "Please enter a name for this task.",
                    life: 2500
                })
                return
            }

            this.$emit('loading')
            await axios.post('/api/pipeline/project/task', this.task)
            .then(response => {
                this.$emit('updateTasks')
                this.$emit('loading')
                this.$emit('close')
            })
        }
    },
    components: {
        InputText,
        Dropdown,
        MultiSelect,
        Calendar,
        Editor,
        XMarkIcon,
        PuzzlePieceIcon,
        FolderIcon,
        UserCircleIcon,
        ExclamationTriangleIcon,
        MapPinIcon,
        TagIcon
    }
}
</script>

<style>
.p-calendar{
    height: 40px;
}

.p-calendar-w-btn .p-datepicker-trigger {
    color: #939498;
    border-color: #dedede;
}

.p-calendar .p-inputtext{
    height: 48x;
    border-radius: 4px;
    border-color: #dedede !important;
}
</style>