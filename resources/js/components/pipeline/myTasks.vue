<template>
    <div class="w-full h-fit grid">
        <!-- Filter Ribbon -->
        <div class="w-full h-[60px] px-6 flex items-center gap-4 text-[14px] font-medium bg-white dark:bg-custom-light-gray-bg border-[1px] border-custom-light dark:border-custom-gray rounded-[10px]">
            <button @click="filter" class="w-fit h-fit flex items-center gap-2 px-4 py-[5px] text-white bg-custom-dark-blue dark:bg-custom-dark-bg rounded-full">Filter <i class="pi pi-filter"></i></button>
        
            <div class="w-fit h-fit flex items-center gap-2">
                <p>Projects</p>
                <MultiSelect v-model="selectedPojects" display="chip" :options="projects" optionLabel="name" class="w-[300px] h-[40px] text-[12px] flex items-center" >
                    <template #option="slotProps">
                        <div class="flex gap-2 items-center">
                            <div :class="'bg-['+slotProps.option.color+']'" class="w-[10px] h-[10px] rounded-[4px]"></div>
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
            </div>

            <div class="w-fit h-fit flex items-center gap-2">
                <p>Sort By</p>
                <Dropdown v-model="sort" :options="sortOptions" optionLabel="name" class="w-[200px] h-[40px] flex items-center" />
            </div>

            <div class="w-fit h-fit flex items-center gap-2">
                <p :class="completed ? '' : 'text-custom-light-gray'" class="h-fit w-[100px]">{{ completed ? 'Completed' : 'Incomplete' }}</p>
                <InputSwitch v-model="completed" class="h-[25px]" />
            </div>
        </div>
        
        <div class="w-full h-fit grid mt-10">
            <Accordion :multiple="true" :activeIndex="[0]">
                <AccordionTab v-for="(project, index) in tasks" :key="index" :header="project.name+' ('+project.tasks.length+')'">
                    <div v-for="(task, taskIndex) in project.tasks" :key="taskIndex" class="w-full h-fit flex items-center gap-2 my-2 group">
                        <!-- Task -->
                        <div @click="$router.replace({params: {task: task.id}})" class="w-full h-[50px] grid grid-cols-5 px-4 bg-custom-bg dark:bg-custom-dark-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-50 rounded-full text-[14px] text-custom-dark-blue dark:text-white font-medium border-[1px] border-custom-light dark:border-custom-gray cursor-pointer">
                            <div class="w-full h-fit m-auto font-semibold col-span-2">
                                <p class="truncate">{{ task.name }}</p>
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
import MultiSelect from 'primevue/multiselect';
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';
import Menu from 'primevue/menu';

import moment from 'moment';

export default {
    name: "My Tasks",
    props: {
        projects: Array,
        updateTasks: Boolean
    },
    data(){
        return{
            selectedPojects: [],
            sort: { name: 'Date Created', code: 'created_at' },
            sortOptions: [
                { name: 'Date Created', code: 'created_at' },
                { name: 'Due Date', code: 'due_date' },
                { name: 'Priority', code: 'priority' },
                { name: 'Progress', code: 'progress' }
            ],
            completed: false,
            tasks: []
        }
    },
    async mounted() {
        this.$emit('loading')
        this.moment = moment

        this.selectedPojects = this.projects
        let project_id = []

        this.selectedPojects.forEach(project => {
            project_id.push(project.id)
        })

        await axios.post('/api/pipeline/my-tasks', {
            "projects": project_id,
            "sort": this.sort.code,
            'completed': this.completed
        })
        .then(response => {
            this.tasks = response.data.tasks
        })

        this.$emit('loading')
    },
    watch: {
        updateTasks: async function(value){
            if(value == true){
                this.$emit('loading')

                let project_id = []

                this.selectedPojects.forEach(project => {
                    project_id.push(project.id)
                })

                await axios.post('/api/pipeline/my-tasks', {
                    "projects": project_id,
                    "sort": this.sort.code,
                    'completed': this.completed
                })
                .then(response => {
                    this.tasks = response.data.tasks
                })

                this.$emit('tasksUpdated')
            }
        }
    },
    methods: {
        async filter(){
            this.$emit('loading')

            let project_id = []

            this.selectedPojects.forEach(project => {
                project_id.push(project.id)
            })

            await axios.post('/api/pipeline/my-tasks', {
                "projects": project_id,
                "sort": this.sort.code,
                'completed': this.completed
            })
            .then(response => {
                this.tasks = response.data.tasks
            })

            this.$emit('tasksUpdated')
        }
    },
    components: {
        MultiSelect,
        Dropdown,
        InputSwitch,
        Accordion,
        AccordionTab,
        Menu
    }
}
</script>