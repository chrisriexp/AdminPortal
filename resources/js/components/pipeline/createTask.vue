<template>
    <div class="w-[1200px] h-fit grid grid-cols-2 gap-10 p-10 bg-custom-bg dark:bg-custom-light-gray-bg rounded-[5px] border-[1px] border-custom-light-gray">
        <div class="w-full h-fit grid gap-4 text-[16px] text-custom-light-gray dark:text-white font-medium">
            <div class="w-full h-fit grid grid-cols-2 gap-6">
                <button @click="createTask()" class="py-[4px] text-white font-medium bg-custom-dark-blue dark:bg-custom-dark-bg border-[1px] border-transparent dark:border-custom-gray rounded-[4px]">Create Task</button>

                <button @click="$emit('close')" class="py-[4px] text-custom-light-gray font-medium hover:bg-custom-light dark:hover:bg-custom-gray dark:hover:bg-opacity-20 rounded-[4px]">Discard</button>
            </div>
            
            <Dropdown v-model="task.project" :options="projects" optionLabel="name" placeholder="Select Project *" class="w-full h-[40px] flex items-center" />
            
            <InputText :disabled="task.project.name ? false : true" id="project_name" v-model="task.name" placeholder="Task Name *" class="h-[40px] w-full text-" />

            <Dropdown :disabled="task.project.name ? false : true" v-model="task.section" :options="sections" optionLabel="name" placeholder="Select Section" class="w-full h-[40px] flex items-center" />

            <div class="w-full h-fit grid grid-cols-2 gap-6">
                <div class="w-full h-fit grid gap-4">
                    <Dropdown :disabled="task.project.name ? false : true" v-model="task.assigned" :options="members" optionLabel="name" placeholder="Assigned to" class="w-full h-[40px] flex items-center" />
                    <Calendar :disabled="task.project.name ? false : true" showIcon v-model="task.due_date" dateFormat="yy-mm-dd" placeholder="Due Date" class="w-full hidden" />
                </div>

                <div class="w-full h-fit grid gap-4">
                    <Dropdown :disabled="task.project.name ? false : true" v-model="task.priority" :options="priorityOptions" optionLabel="name" placeholder="Priority" class="w-full h-[40px] flex items-center" >
                        <template #value="slotProps">
                            <div v-if="slotProps.value" class="flex align-items-center">
                                <div :class="'bg-['+slotProps.value.color+']'" class="px-4 rounded-full text-white">{{ slotProps.value.name }}</div>
                            </div>
                            <span v-else>
                                {{ slotProps.placeholder }}
                            </span>
                        </template>
                        
                        <template #option="slotProps">
                            <div class="flex align-items-center">
                                <div :class="'bg-['+slotProps.option.color+']'" class="px-4 rounded-full text-white">{{ slotProps.option.name }}</div>
                            </div>
                        </template> 
                    </Dropdown>


                    <Dropdown :disabled="task.project.name ? false : true" v-model="task.progress" :options="progressOptions" optionLabel="name" placeholder="Progress" class="w-full h-[40px] flex items-center" >
                        <template #value="slotProps">
                            <div v-if="slotProps.value" class="flex align-items-center">
                                <div :class="'bg-['+slotProps.value.color+']'" class="px-4 rounded-full text-white">{{ slotProps.value.name }}</div>
                            </div>
                            <span v-else>
                                {{ slotProps.placeholder }}
                            </span>
                        </template>
                        
                        <template #option="slotProps">
                            <div class="flex align-items-center">
                                <div :class="'bg-['+slotProps.option.color+']'" class="px-4 rounded-full text-white">{{ slotProps.option.name }}</div>
                            </div>
                        </template> 
                    </Dropdown>
                </div>
            </div>
            
            <div class="w-full h-fit flex items-center gap-2">
                <p>Tags:</p>
                <MultiSelect :disabled="task.project.name ? false : true" v-model="task.tags" display="chip" :options="tags" optionLabel="name" class="w-[400px] h-[40px] text-[12px] flex items-center" >
                    <template #option="slotProps">
                        <div class="flex gap-2 items-center">
                            <div :class="'bg-['+slotProps.option.color+']'" class="px-4 rounded-full text-white">{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
            </div>

            <div class="w-full h-fit flex flex-wrap items-center gap-4">
                <p v-for="(tag, index) in task.tags" :key="index" :class="'bg-['+tag.color+']'" class="w-fit h-fit px-4 text-[14px] font-bold text-white rounded-full">{{ tag.name }}</p>
            </div>
        </div>

        <Editor :class="task.project.name ? '' : 'hidden'" v-model="task.desc" id="newTaskDesc" editorStyle="height: 500px" />
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import MultiSelect from 'primevue/multiselect';
import Calendar from 'primevue/calendar';
import Editor from 'primevue/editor';

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

            await axios.post('/api/pipeline/project/task', this.task)
            .then(response => {
                this.$emit('updateTasks')
                this.$emit('close')
            })
        }
    },
    components: {
        InputText,
        Dropdown,
        MultiSelect,
        Calendar,
        Editor
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
    height: 40px;
    border-radius: 4px;
    border-color: #dedede !important;
}
</style>