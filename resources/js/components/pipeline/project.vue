<template>
    <div class="w-full h-fit grid">
        <!-- Filter SideBar -->
        <Sidebar v-model:visible="viewFilter" position="right"  class="bg-sidebar-bg">
            <div class="w-full h-fit grid gap-4">
                <p class="text-[24px] text-custom-black font-medium">Filters</p>

                <!-- Sections -->
                <MultiSelect v-model="filter.sections" :options="project.sections" optionLabel="name" placeholder="Section" class="w-[250px] h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="flex gap-2 items-center">
                            <FolderIcon class="h-[16px]" />
                            <p class="text-[16px] text-custom-black font-medium">Sections</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Assigned -->
                <MultiSelect v-model="filter.assigned" :options="project.members" optionLabel="name" placeholder="Assigned" class="w-[250px] h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="flex gap-2 items-center">
                            <UserGroupIcon class="h-[16px]" />
                            <p class="text-[16px] text-custom-black font-medium">Assigned</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Prority -->
                <MultiSelect v-model="filter.priority" :options="project.priority" optionLabel="name" placeholder="Priority" class="w-[250px] h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="flex gap-2 items-center">
                            <ExclamationTriangleIcon class="h-[16px]" />
                            <p class="text-[16px] text-custom-black font-medium">Priority</p>
                        </div>
                    </template>
                    <template #option="slotProps">
                        <div class="flex gap-2 items-center">
                            <p :class="'bg-['+slotProps.option.color+']'" class="text-[14px] text-white font-medium px-4 rounded-full">{{ slotProps.option.name }}</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Progress -->
                <MultiSelect v-model="filter.progress" :options="project.progress" optionLabel="name" placeholder="Progress" class="w-[250px] h-[40px] text-[12px] flex items-center" >
                    <template #value="">
                        <div class="flex gap-2 items-center">
                            <MapPinIcon class="h-[16px]" />
                            <p class="text-[16px] text-custom-black font-medium">Progress</p>
                        </div>
                    </template>
                    <template #option="slotProps">
                        <div class="flex gap-2 items-center">
                            <p :class="'bg-['+slotProps.option.color+']'" class="text-[14px] text-white font-medium px-4 rounded-full">{{ slotProps.option.name }}</p>
                        </div>
                    </template>
                </MultiSelect>

                <!-- Sort -->
                <Dropdown v-model="filter.sort" :options="sortOptions" optionLabel="name" class="w-[250px] h-[40px] flex items-center" >
                    <template #value="slotProps">
                        <div class="flex gap-2 items-center">
                            <p class="flex items-center gap-2"><span class="text-[16px]  text-custom-black font-medium flex items-center gap-2"><i class="pi pi-filter-fill"></i>Sort:</span> {{ slotProps.value.name }}</p>
                        </div>
                    </template>
                </Dropdown>

                <!-- Compelete/Incomplete -->
                <div class="w-fit h-fit flex items-center gap-2 text-[16px]">
                    <p :class="filter.completed ? 'text-custom-black font-medium' : 'text-custom-light-gray'" class="h-fit w-[100px]">{{ filter.completed ? 'Completed' : 'Incomplete' }}</p>
                    <InputSwitch v-model="filter.completed" class="h-[25px] flex items-center" />
                </div>

                <div class="w-full h-fit grid gap-2 my-6">
                    <button @click="runFilter" class="text-white text-[16px] font-medium py-[4px] bg-custom-purple rounded-[4px] shadow-newdrop">Apply Filter</button>
                    <button @click="resetFilter" class="text-[16px] text-custom-purple">Reset Filter</button>
                </div>
            </div>
        </Sidebar>

        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                <p>{{ project.name }}</p>
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
            <p :class="filter.completed ? 'bg-custom-green' : 'bg-custom-red'" class="px-2 bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ completed ? 'Complete' : 'Incomplete' }}</p>
            <p class="text-[14px] text-custom-black">Sort By:</p>
            <p class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ filter.sort.name }}</p>
            <p class="text-[14px] text-custom-black">Sections:</p>
            <p v-for="(section, index) in filter.sections" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ section.name }}</p>
            <p class="text-[14px] text-custom-black">Assigned:</p>
            <p v-for="(member, index) in filter.assigned" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ member.name }}</p>
            <p class="text-[14px] text-custom-black">Priority:</p>
            <p v-for="(priority, index) in filter.priority" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ priority.name }}</p>
            <p class="text-[14px] text-custom-black">Priority:</p>
            <p v-for="(progress, index) in filter.progress" :key="index" class="px-2 bg-custom-black bg-opacity-50 text-white text-[12px] font-medium rounded-full">{{ progress.name }}</p>
            <button @click="resetFilter" class="text-[14px] text-custom-purple underline">Reset Filters</button>
        </div>

        <!-- List, Board Select -->
        <div class="w-full h-fit my-6 flex gap-6 border-b-[1px] border-[#D9D9D9] text-[16px] text-custom-black font-medium">
            <p @click="view = 'list'" :class="view == 'list' ? ' border-b-[3px] border-custom-black' : 'opacity-50'" class="h-full px-6 cursor-pointer">List</p>
            <p @click="view = 'board'" :class="view == 'board' ? ' border-b-[3px] border-custom-black' : 'opacity-50'" class="h-full px-6 cursor-pointer">Board</p>
        </div>

        <!-- Headers -->
        <div v-if="view == 'list' && (tasks.tasks.length > 0 || tasks.sections.length > 0)" class="w-full h-fit grid grid-cols-6 px-6 py-2 text-[16px] text-custom-black font-medium border-b-[1px] border-custom-black border-opacity-10 opacity-50">
            <p class="col-span-2">Task Name</p>
            <p>Assigned</p>
            <p>Due Date</p>
            <p>Priority</p>
            <p>Progress</p>
        </div>

        <!-- No Tasks Found -->
        <div v-if="tasks.tasks.length == 0 && tasks.sections.length == 0" class="w-full h-fit grid">
            <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'250px'" :height="'250px'" :speed="0.75" class="m-auto" />
        </div>

        <div v-else class="w-full h-fit grid">
            <!-- List View -->
            <div v-if="view == 'list'" class="w-full h-fit max-h-[600px] grid gap-4 mt-2 overflow-y-scroll">
                <!-- Tasks with No section -->
                <div @click="$router.replace({params: {task: task.id}})" v-for="(task, index) in tasks.tasks" :key="index" class="w-full h-[52px] flex items-center px-6 bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px] cursor-pointer hover:bg-[#F4F4F4]">
                    <div class="w-full h-fit grid grid-cols-6 text-[16px] text-custom-black font-medium">
                        <p class="col-span-2 truncate pr-4">{{ task.name }}</p>
                        
                        <p class="truncate pr-4">{{ task.assigned.name }}</p>

                        <p :class="moment(task.due_date).isBefore(new Date()) ? 'text-custom-red' : ''" class="truncate pr-4">{{ task.due_date ? moment(task.due_date).format('ddd, MMM DD') : "" }}</p>

                        <p :class="'bg-['+task.priority.color+']'" class="truncate w-fit h-fit px-4 py-[3px] text-[12px] text-white rounded-full">{{ task.priority.name }}</p>

                        <p :class="'bg-['+task.progress.color+']'" class="truncate w-fit h-fit px-4 py-[3px] text-[12px] text-white rounded-full">{{ task.progress.name }}</p>
                    </div>
                </div>

                <!-- Sections and Tasks -->
                <Accordion :multiple="true" :activeIndex="[0]">
                    <AccordionTab v-for="(section, index) in tasks.sections" :key="index" :header="section.name+' ('+section.tasks.length+')'">
                        <div class="w-full h-fit grid gap-4">
                            <div @click="$router.replace({params: {task: task.id}})" v-for="(task, taskIndex) in section.tasks" :key="taskIndex" class="w-full h-[52px] flex items-center px-6 bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px] cursor-pointer hover:bg-[#F4F4F4]">
                                <div class="w-full h-fit grid grid-cols-6 text-[16px] text-custom-black font-medium">
                                    <p class="col-span-2 truncate pr-4">{{ task.name }}</p>

                                    <p class="truncate pr-4">{{ task.assigned.name }}</p>

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
            <div v-if="view == 'board'" class="w-full h-[700px] flex overflow-x-scroll">
                <!-- Tasks with no Section -->
                <div v-if="tasks.tasks.length > 0" style="flex-shrink: 0;" class="w-[355px] h-full grid">
                    <div class="w-full px-4 h-fit max-h-[500px] grid gap-2 overflow-y-scroll">
                        <div class="w-full h-[77px] grid">
                            <p class="my-auto text-[24px] text-custom-black font-medium">No Section</p>
                        </div>
                        
                        <div @click="$router.replace({params: {task: task.id}})" v-for="(task, index) in tasks.tasks" :key="index" class="w-full min-h-[110px] h-fit grid p-4 text-[16px] text-custom-black font-medium bg-sidebar-bg hover:bg-[#F4F4F4] border-[1px] border-custom-black border-opacity-10 rounded-[10px] cursor-pointer">
                            <p class="w-full h-fit">{{ task.name }}</p>

                            <div class="w-full h-fit flow-root">
                                <div class="h-full w-fit flex items-center gap-2 float-right">
                                    <p :class="'bg-['+task.priority.color+']'" class="truncate w-fit h-fit px-2 py-[3px] text-[12px] text-white rounded-full">{{ task.priority.name }}</p>
                                    <p :class="'bg-['+task.progress.color+']'" class="truncate w-fit h-fit px-2 py-[3px] text-[12px] text-white rounded-full">{{ task.progress.name }}</p>
                                </div>
                            </div>

                            <div class="w-full h-fit flow-root">
                                <p class="w-fit max-w-[200px] truncate font-normal float-left">{{ task.assigned.name }}</p>

                                <div class="w-fit h-full flex items-center float-right font-normal">
                                    <p :class="moment(task.due_date).isBefore(new Date()) ? 'text-custom-red' : ''">{{ task.due_date ? moment(task.due_date).format('MMM DD') : '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tasks with Sections -->
                <div v-for="(section, index) in tasks.sections" :key="index" style="flex-shrink: 0;" class="w-[355px] h-full grid" >
                    <div class="w-full h-fit">
                        <div class="w-full h-[77px] grid px-4">
                            <p class="my-auto text-[24px] text-custom-black font-medium">{{ section.name }}</p>
                        </div>

                        <div class="w-full px-4 h-fit max-h-[500px] grid gap-2 mt-2 overflow-y-scroll">
                            <div @click="$router.replace({params: {task: task.id}})" v-for="(task, taskIndex) in section.tasks" :key="taskIndex" class="w-full min-h-[110px] h-fit grid p-4 text-[16px] text-custom-black font-medium bg-sidebar-bg hover:bg-[#F4F4F4] border-[1px] border-custom-black border-opacity-10 rounded-[10px] cursor-pointer">
                                <p class="w-full truncate">{{ task.name }}</p>

                                <div class="w-full h-fit flow-root">
                                    <div class="h-full w-fit flex items-center gap-2 float-right">
                                        <p :class="'bg-['+task.priority.color+']'" class="truncate w-fit h-fit px-2 py-[3px] text-[12px] text-white rounded-full">{{ task.priority.name }}</p>
                                        <p :class="'bg-['+task.progress.color+']'" class="truncate w-fit h-fit px-2 py-[3px] text-[12px] text-white rounded-full">{{ task.progress.name }}</p>
                                    </div>
                                </div>

                                <div class="w-full h-fit flow-root">
                                    <p class="w-fit max-w-[200px] truncate font-normal float-left">{{ task.assigned.name }}</p>

                                    <div class="w-fit h-full flex items-center float-right font-normal">
                                        <p :class="moment(task.due_date).isBefore(new Date()) ? 'text-custom-red' : ''">{{ task.due_date ? moment(task.due_date).format('MMM DD') : '' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'

import MultiSelect from 'primevue/multiselect';
import Dropdown from 'primevue/dropdown';
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';
import Menu from 'primevue/menu';
import Sidebar from 'primevue/sidebar';
import InputSwitch from 'primevue/inputswitch';

import NotFoundAnimation from '../../../assets/newNotFound.json'

import { FolderIcon, UserGroupIcon, ExclamationTriangleIcon, MapPinIcon } from '@heroicons/vue/24/solid'

export default {
    name: "Project View",
    props: {
        project_id: String,
        updateTasks: Boolean
    },
    data(){
        return{
            NotFoundAnimation,
            viewFilter: false,
            view: 'list',
            project: {
                name: "",
                members: [],
                priority: [],
                progress: [],
                sections: [],
                tags: []
            },
            filter: {
                completed: false,
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
            },
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
        toggle_create(event){
            this.$refs['create_menu'].toggle(event);
        },
        async runFilter(){
            await axios.post('/api/pipeline/project/'+this.project_id+'/tasks', this.filter)
            .then(response => {
                this.tasks.tasks = response.data.tasks.tasks
                this.tasks.sections = response.data.tasks.sections

                this.viewFilter = false
            })
        },
        async resetFilter(){
            this.filter.progress = this.project.progress
            this.filter.priority = this.project.priority
            this.filter.assigned = this.project.members
            this.filter.sections = this.project.sections
            this.filter.sort = { name: 'Date Created', code: 'created_at' }
            this.filter.completed = false

            await axios.post('/api/pipeline/project/'+this.project_id+'/tasks', this.filter)
            .then(response => {
                this.tasks.tasks = response.data.tasks.tasks
                this.tasks.sections = response.data.tasks.sections

                this.viewFilter = false
            })
        }
    },
    components: {
        MultiSelect,
        Dropdown,
        Accordion,
        AccordionTab,
        Menu,
        Sidebar,
        FolderIcon,
        UserGroupIcon,
        ExclamationTriangleIcon,
        MapPinIcon,
        InputSwitch
    }
}
</script>
