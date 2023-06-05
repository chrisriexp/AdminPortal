<template>
    <div class="w-full h-fit grid">
        <!-- Filter SideBar -->
        <Sidebar v-model:visible="viewFilter" position="right"  class="bg-sidebar-bg">
            <div class="w-full h-fit grid gap-4">
                <p class="text-[24px] text-custom-black font-medium">Filters</p>

                <!-- Projects -->
                <MultiSelect v-model="selectedPojects" :options="projects" optionLabel="name" placeholder="Projects" class="w-[250px] h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="flex gap-2 items-center">
                            <PuzzlePieceIcon class="h-[16px]" />
                            <p class="text-[16px] text-custom-black font-medium">Projects</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Sort -->
                <Dropdown v-model="sort" :options="sortOptions" optionLabel="name" class="w-[250px] h-[40px] flex items-center" >
                    <template #value="slotProps">
                        <div class="flex gap-2 items-center">
                            <p class="flex items-center gap-2"><span class="text-[16px]  text-custom-black font-medium flex items-center gap-2"><i class="pi pi-filter-fill"></i>Sort:</span> {{ slotProps.value.name }}</p>
                        </div>
                    </template>
                </Dropdown>

                <!-- Compelete/Incomplete -->
                <div class="w-fit h-fit flex items-center gap-2 text-[16px]">
                    <p :class="completed ? 'text-custom-black font-medium' : 'text-custom-light-gray'" class="h-fit w-[100px]">{{ completed ? 'Completed' : 'Incomplete' }}</p>
                    <InputSwitch v-model="completed" class="h-[25px] flex items-center" />
                </div>

                <div class="w-full h-fit grid gap-2 my-6">
                    <button @click="filter" class="text-white text-[16px] font-medium py-[4px] bg-custom-purple rounded-[4px] shadow-newdrop">Apply Filter</button>
                    <button @click="resetFilter" class="text-[16px] text-custom-purple">Reset Filter</button>
                </div>
            </div>
        </Sidebar>

        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                <p>My Tasks</p>
            </div>

            <!-- Create and Filters -->
            <div class="w-fit h-full flex items-center gap-4 float-right">
                <!-- Create Button -->
                <div @click="toggle_create" aria-haspopup="true" aria-controls="create_menu" class="w-[175px] h-[48px] grid bg-custom-purple shadow-customdrop rounded-[4px] cursor-pointer">
                    <p class="w-fit h-fit m-auto flex items-center gap-4 text-white text-[16px] font-semibold"><i class="pi pi-plus"></i>Create</p>
                </div>
                <Menu ref="create_menu" id="create_menu" :model="create_items" :popup="true" />

                <!-- Filter Button -->
                <div @click="viewFilter = true" class="w-[132px] h-[48px] grid bg-custom-black shadow-customdrop rounded-[4px] cursor-pointer">
                    <p class="w-fit h-fit m-auto flex items-center gap-4 text-white text-[16px] font-semibold"><i class="pi pi-filter-fill"></i>Filter</p>
                </div>
            </div>
        </div>

        <!-- Filter Chips -->
        <div class="w-fit max-w-full flex flex-wrap items-center gap-2 mt-6">
            <p class="text-[14px] text-custom-black">Status:</p>
            <p :class="completed ? 'bg-custom-green' : 'bg-custom-red'" class="px-2 bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ completed ? 'Complete' : 'Incomplete' }}</p>
            <p class="text-[14px] text-custom-black">Sort By:</p>
            <p class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ sort.name }}</p>
            <p class="text-[14px] text-custom-black">Projects:</p>
            <p v-for="(project, index) in selectedPojects" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ project.name }}</p>
            <button @click="resetFilter" class="text-[14px] text-custom-purple underline">Reset Filters</button>
        </div>
        
        <!-- List, Board Select -->
        <div class="w-full h-fit my-6 flex gap-6 border-b-[1px] border-[#D9D9D9] text-[16px] text-custom-black font-medium">
            <p @click="view = 'list'" :class="view == 'list' ? ' border-b-[3px] border-custom-black' : 'opacity-50'" class="h-full px-6 cursor-pointer">List</p>
            <p @click="view = 'board'" :class="view == 'board' ? ' border-b-[3px] border-custom-black' : 'opacity-50'" class="h-full px-6 cursor-pointer">Board</p>
        </div>

        <div v-if="tasks.length > 0" class="w-full h-fit grid">
            <!-- List View -->
            <div v-if="view == 'list'" class="w-full h-fit max-h-[600px] grid mt-10 pb-12 overflow-y-scroll">
                <Accordion :multiple="true" :activeIndex="[0]">
                    <AccordionTab v-for="(project, index) in tasks" :key="index" :header="project.name+' ('+project.tasks.length+')'">
                        <div class="w-full h-fit grid gap-4">
                            <div class="w-full h-fit grid grid-cols-5 px-6 py-2 text-[16px] text-custom-black font-medium border-b-[1px] border-custom-black border-opacity-10 opacity-50">
                                <p class="col-span-2">Task Name</p>
                                <p>Due Date</p>
                                <p>Priority</p>
                                <p>Progress</p>
                            </div>
                            
                            <div @click="$router.replace({params: {task: task.id}})" v-for="(task, taskIndex) in project.tasks" :key="taskIndex" class="w-full h-[52px] flex items-center px-6 bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px] cursor-pointer hover:bg-[#F4F4F4]">
                                <div class="w-full h-fit grid grid-cols-5 text-[16px] text-custom-black font-medium">
                                    <p class="col-span-2 truncate pr-4">{{ task.name }}</p>

                                    <p :class="moment(task.due_date).isBefore(new Date()) ? 'text-custom-red' : ''" class="truncate pr-4">{{ task.due_date ? moment(task.due_date).format('ddd, MMM DD') : "" }}</p>

                                    <p :class="'bg-['+task.priority.color+']'" class="truncate w-fit h-fit px-4 py-[3px] text-[12px] text-white rounded-full">{{ task.priority.name }}</p>

                                    <p :class="'bg-['+task.progress.color+']'" class="truncate w-fit h-fit px-4 py-[3px] text-[12px] text-white rounded-full">{{ task.progress.name }}</p>
                                </div>
                            </div>
                        </div>
                    </AccordionTab>
                </Accordion>
            </div>

            <!-- Board View -->
            <div v-if="view == 'board'" class="w-full h-[700px] flex border-custom-black border-opacity-10 rounded-[4px] overflow-x-scroll">
                <div v-for="(project, index) in tasks" :key="index" style="flex-shrink: 0;" :class="index == tasks.length - 1 ? '' : 'border-custom-black border-opacity-20'" class="w-[355px] h-full grid" >
                    <div class="w-full h-fit">
                        <div class="w-full h-[77px] grid px-4">
                            <p class="my-auto text-[24px] text-custom-black font-medium truncate">{{ project.name }}</p>
                        </div>

                        <div class="w-full px-4 h-fit max-h-[500px] grid gap-2 mt-2 overflow-y-scroll">
                            <div @click="$router.replace({params: {task: task.id}})" v-for="(task, taskIndex) in project.tasks" :key="taskIndex" class="w-full h-fit min-h-[84px] grid p-4 text-[16px] text-custom-black font-medium bg-sidebar-bg hover:bg-[#F4F4F4] border-[1px] border-custom-black border-opacity-10 rounded-[10px] cursor-pointer">
                                <p class="w-full h-fit">{{ task.name }}</p>

                                <div class="w-full h-fit flow-root">
                                    <div class="w-fit h-full flex items-center float-left font-normal">
                                        <p :class="moment(task.due_date).isBefore(new Date()) ? 'text-custom-red' : ''">{{ task.due_date ? moment(task.due_date).format('MMM DD') : '' }}</p>
                                    </div>

                                    <div class="h-full w-fit flex items-center gap-2 float-right">
                                        <p :class="'bg-['+task.priority.color+']'" class="truncate w-fit h-fit px-2 py-[3px] text-[12px] text-white rounded-full">{{ task.priority.name }}</p>
                                        <p :class="'bg-['+task.progress.color+']'" class="truncate w-fit h-fit px-2 py-[3px] text-[12px] text-white rounded-full">{{ task.progress.name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Tasks Found -->
        <div v-else class="w-full h-fit grid">
            <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'250px'" :height="'250px'" :speed="0.75" class="m-auto" />
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
import Sidebar from 'primevue/sidebar';

import NotFoundAnimation from '../../../assets/newNotFound.json'

import moment from 'moment';

import { PuzzlePieceIcon } from '@heroicons/vue/24/solid'

export default {
    name: "My Tasks",
    props: {
        projects: Array,
        updateTasks: Boolean
    },
    data(){
        return{
            NotFoundAnimation,
            viewFilter: false,
            view: 'list',
            selectedPojects: [],
            sort: { name: 'Date Created', code: 'created_at' },
            sortOptions: [
                { name: 'Date Created', code: 'created_at' },
                { name: 'Due Date', code: 'due_date' },
                { name: 'Priority', code: 'priority' },
                { name: 'Progress', code: 'progress' }
            ],
            completed: false,
            tasks: [],
            create_items: [
                {
                    label: 'Task',
                    icon: 'pi pi-check-circle',
                    command: () => {
                        this.$emit('create', 'task')
                    }
                },
                {
                    label: 'Project',
                    icon: 'pi pi-list',
                    command: () => {
                        this.$emit('create', 'project')
                    }
                },
                {
                    label: 'Section',
                    icon: 'pi pi-folder',
                    command: () => {
                        this.$emit('create', 'section')
                    }
                }
            ],
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
        toggle_create(event){
            this.$refs['create_menu'].toggle(event);
        },
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

            this.viewFilter = false
            this.$emit('tasksUpdated')
        },
        async resetFilter(){
            this.$emit('loading')

            let project_id = []
            this.selectedPojects = this.projects

            this.selectedPojects.forEach(project => {
                project_id.push(project.id)
            })

            this.sort = { name: 'Date Created', code: 'created_at' }
            this.completed = false

            await axios.post('/api/pipeline/my-tasks', {
                "projects": project_id,
                "sort": this.sort.code,
                'completed': this.completed
            })
            .then(response => {
                this.tasks = response.data.tasks
            })

            this.viewFilter = false
            this.$emit('tasksUpdated')
        }
    },
    components: {
        MultiSelect,
        Dropdown,
        InputSwitch,
        Accordion,
        AccordionTab,
        Menu,
        Sidebar,
        PuzzlePieceIcon
    }
}
</script>
