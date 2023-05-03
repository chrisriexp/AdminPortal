<template>
    <div class="w-full h-fit grid overflow-x-scroll">
        <!-- Filter Ribbon -->
        <div class="w-full h-[60px] px-6 flex items-center gap-2 text-[12px] font-medium bg-white dark:bg-custom-light-gray-bg border-[1px] border-custom-light dark:border-custom-gray rounded-[10px]">
            <button @click="runFilter()" class="w-fit h-fit flex items-center gap-2 px-4 py-[5px] text-white bg-custom-dark-blue dark:bg-custom-dark-bg rounded-full">Filter <i class="pi pi-filter"></i></button>
            
            <div class="w-fit h-fit flex items-center gap-2">
                <p>Sections</p>
                <MultiSelect v-model="filter.sections" :options="project.sections" optionLabel="name" class="w-[150px] h-[35px] text-[12px] flex items-center" />
            </div>

            <div class="w-fit h-fit flex items-center gap-2">
                <p>Assigned</p>
                <MultiSelect v-model="filter.assigned" :options="project.members" optionLabel="name" class="w-[150px] h-[35px] text-[12px] flex items-center" />
            </div>

            <div class="w-fit h-fit flex items-center gap-2">
                <p>Priority</p>
                <MultiSelect v-model="filter.priority" :options="project.priority" optionLabel="name" class="w-[150px] h-[35px] text-[12px] flex items-center" >
                    <template #option="slotProps">
                        <div class="flex gap-2 items-center">
                            <div :class="'bg-['+slotProps.option.color+']'" class="px-4 rounded-full text-white">{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
            </div>

            <div class="w-fit h-fit flex items-center gap-2">
                <p>Progress</p>
                <MultiSelect v-model="filter.progress" :options="project.progress" optionLabel="name" class="w-[150px] h-[35px] text-[12px] flex items-center" >
                    <template #option="slotProps">
                        <div class="flex gap-2 items-center">
                            <div :class="'bg-['+slotProps.option.color+']'" class="px-4 rounded-full text-white">{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
            </div>

            <div class="w-fit h-fit flex items-center gap-2">
                <p>Sort By</p>
                <Dropdown v-model="filter.sort" :options="sortOptions" optionLabel="name" class="w-[150px] h-[35px] text-[12px] flex items-center" />
            </div>
        </div>

        <!-- Task -->
        <div class="w-full h-fit grid gap-2 mt-6">
            <p class="my-4 text-custom-dark-blue dark:text-white text-[20px] font-semibold">{{ project.name }}</p>
            <div @click="$router.replace({params: {task: task.id}})" v-for="(task, index) in tasks.tasks" :key="index" class="w-full h-[50px] grid grid-cols-6 px-4 bg-white dark:bg-custom-dark-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-50 rounded-full text-[14px] text-custom-dark-blue dark:text-white font-medium border-[1px] border-custom-light dark:border-custom-gray cursor-pointer">
                <div class="w-full h-fit m-auto font-semibold col-span-2">
                    <p class="truncate">{{ task.name }}</p>
                </div>

                <div class="w-full h-fit m-auto flex items-center gap-2">
                    <p class="font-semibold text-custom-light-gray">Assigned:</p>
                    <p class="truncate">{{ task.assigned.name }}</p>
                </div>

                <div class="w-full h-fit m-auto flex items-center gap-2">
                    <p class="font-semibold text-custom-light-gray">Due Date:</p>
                    <p class="truncate">{{ task.due_date ? moment(task.due_date).format('MM/DD/YYYY') : "" }}</p>
                </div>

                <div class="w-full h-fit m-auto flex items-center gap-2">
                    <p class="font-semibold text-custom-light-gray">Priority:</p>
                    <p :class="'bg-['+task.priority.color+']'" class="truncate w-fit h-fit px-2 py-[3px] text-white rounded-full">{{ task.priority.name }}</p>
                </div>

                <div class="w-full h-fit m-auto flex items-center gap-2">
                    <p class="font-semibold text-custom-light-gray">Progress:</p>
                    <p :class="'bg-['+task.progress.color+']'" class="truncate w-fit h-fit px-2 py-[3px] text-white rounded-full">{{ task.progress.name }}</p>
                </div>
            </div>

            <Accordion :multiple="true" :activeIndex="[0]">
                <AccordionTab v-for="(section, index) in tasks.sections" :key="index" :header="section.name+' ('+section.tasks.length+')'">
                    <div v-for="(task, taskIndex) in section.tasks" :key="taskIndex" class="w-full h-fit flex items-center gap-2 my-2 group">
                        <!-- Task -->
                        <div @click="$router.replace({params: {task: task.id}})" class="w-full h-[50px] grid grid-cols-6 px-4 bg-custom-bg dark:bg-custom-dark-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-50 rounded-full text-[14px] text-custom-dark-blue dark:text-white font-medium border-[1px] border-custom-light dark:border-custom-gray cursor-pointer">
                            <div class="w-full h-fit m-auto font-semibold col-span-2">
                                <p class="truncate">{{ task.name }}</p>
                            </div>

                            <div class="w-full h-fit m-auto flex items-center gap-2">
                                <p class="font-semibold text-custom-light-gray">Assigned:</p>
                                <p class="truncate">{{ task.assigned.name }}</p>
                            </div>

                            <div class="w-full h-fit m-auto flex items-center gap-2">
                                <p class="font-semibold text-custom-light-gray">Due Date:</p>
                                <p class="truncate">{{ task.due_date ? moment(task.due_date).format('MM/DD/YYYY') : "" }}</p>
                            </div>

                            <div class="w-full h-fit m-auto flex items-center gap-2">
                                <p class="font-semibold text-custom-light-gray">Priority:</p>
                                <p :class="'bg-['+task.priority.color+']'" class="truncate w-fit h-fit px-2 py-[3px] text-white rounded-full">{{ task.priority.name }}</p>
                            </div>

                            <div class="w-full h-fit m-auto flex items-center gap-2">
                                <p class="font-semibold text-custom-light-gray">Progress:</p>
                                <p :class="'bg-['+task.progress.color+']'" class="truncate w-fit h-fit px-2 py-[3px] text-white rounded-full">{{ task.progress.name }}</p>
                            </div>
                        </div>
                    </div>
                </AccordionTab>
            </Accordion>
        </div>
    </div>
</template>

<script>
import moment from 'moment'

import MultiSelect from 'primevue/multiselect';
import Dropdown from 'primevue/dropdown';
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';

export default {
    name: "Project View",
    props: {
        project_id: String,
        updateTasks: Boolean
    },
    data(){
        return{
            project: {
                name: "",
                members: [],
                priority: [],
                progress: [],
                sections: [],
                tags: []
            },
            filter: {
                sections: [],
                assigned: [],
                priority: [],
                progress: [],
                tags: [],
                sort: { name: 'Date Created', code: 'created_at' }
            },
            sortOptions: [
                { name: 'Date Created', code: 'created_at' },
                { name: 'Due Date', code: 'due_date' },
                { name: 'Priority', code: 'priority' },
                { name: 'Progress', code: 'progress' },
                { name: 'Name', code: 'name' }
            ],
            tasks: {
                tasks:[],
                sections: []
            }
        }
    },
    async mounted() {
        this.$emit('loading')
        this.moment = moment

        await axios.get('/api/pipeline/project/'+this.project_id+'/details')
        .then(response => {
            const projectKeys = Object.keys(this.project)

            projectKeys.forEach(key => {
                this.project[key] = response.data[key]
                this.filter[key] = this.project[key]
            })

            this.filter.assigned = this.project.members
        })

        await axios.post('/api/pipeline/project/'+this.project_id+'/tasks', this.filter)
        .then(response => {
            this.tasks.tasks = response.data.tasks.tasks
            this.tasks.sections = response.data.tasks.sections
        })

        this.$emit('loading')
    },
    watch: {
        project_id: async function(value){
            this.$emit('loading')

            await axios.get('/api/pipeline/project/'+value+'/details')
            .then(response => {
                const projectKeys = Object.keys(this.project)

                projectKeys.forEach(key => {
                    this.project[key] = response.data[key]
                    this.filter[key] = this.project[key]
                })

                this.filter.assigned = this.project.members
            })

            await axios.post('/api/pipeline/project/'+value+'/tasks', this.filter)
            .then(response => {
                this.tasks.tasks = response.data.tasks.tasks
                this.tasks.sections = response.data.tasks.sections
            })

            this.$emit('loading')
        },
        updateTasks: async function(value){
            if(value == true){
                this.$emit('loading')

                await axios.get('/api/pipeline/project/'+this.project_id+'/details')
                .then(response => {
                    const projectKeys = Object.keys(this.project)

                    projectKeys.forEach(key => {
                        this.project[key] = response.data[key]
                    })
                })

                await axios.post('/api/pipeline/project/'+this.project_id+'/tasks', this.filter)
                .then(response => {
                    this.tasks.tasks = response.data.tasks.tasks
                    this.tasks.sections = response.data.tasks.sections
                })

                this.$emit('tasksUpdated')
            }
        }
    },
    methods: {
        async runFilter(){
            await axios.post('/api/pipeline/project/'+this.project_id+'/tasks', this.filter)
            .then(response => {
                this.tasks.tasks = response.data.tasks.tasks
                this.tasks.sections = response.data.tasks.sections
            })
        }
    },
    components: {
        MultiSelect,
        Dropdown,
        Accordion,
        AccordionTab
    }
}
</script>