<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>
    
    <!-- Create Project -->
    <div v-if="createProject" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <CreateProject class="m-auto" @close="createProject = false" @updateProjects="updateProjects" />
    </div>

    <!-- Create Section -->
    <div v-if="createSection" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <CreateSection class="m-auto" @close="createSection = false" :projects="projects" :project_id="view" @updateSections="updateTasks = true" />
    </div>

    <!-- Create Task -->
    <div v-if="createTask" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <CreateTask class="m-auto" @close="createTask = false" :projects="projects" :project_id="view" @updateTasks="updateTasks = true" />
    </div>

    <!-- Task View -->
    <div v-if="viewTask" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <TaskView class="m-auto" @close="$router.replace({params: {task: null}}); updateTasks = true" @loading="loading = !loading" />
    </div>

    <div class="absolute z-30 w-full">
        <topNav :active="'pipeline'" @loading="loading = !loading" class="fixed z-40" />
    </div>

    <div v-if="ready" class="w-full h-screen z-20 absolute">
        <div class="w-[250px] h-full grid bg-white dark:bg-custom-light-gray-bg border-r-[1px] border-custom-light dark:border-custom-gray z-20 absolute">
            <div class="w-full max-h-[800px] h-fit grid gap-4 mt-[80px] text-custom-dark-blue dark:text-white font-medium text-[16px] overflow-y-scroll">
                <!-- Create Button -->
                <div class="w-fit h-fit px-6">
                    <button @click="toggle_create" aria-haspopup="true" aria-controls="create_menu" class="w-fit h-[35px] flex items-center gap-2 px-4 text-white bg-custom-dark-blue dark:bg-custom-dark-bg border-[1px] border-transparent dark:border-custom-gray rounded-full"><i class="pi pi-plus"></i>Create</button>
                    <Menu ref="create_menu" id="create_menu" :model="create_items" :popup="true" />
                </div>

                <!-- My Tasks -->
                <div @click="view = 'my-tasks'" :class="view == 'my-tasks' ? 'bg-custom-bg dark:bg-custom-gray dark:bg-opacity-50' : 'hover:bg-custom-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-50' " class="w-full h-fit flex items-center gap-2 px-6 py-2 cursor-pointer">
                    <i class="pi pi-check-circle"></i>
                    <p class="font-semibold">My Tasks</p>
                </div>

                <!-- Projects -->
                <div class="w-full h-fit grid">
                    <p class="px-6 text-custom-light-gray font-semibold">Projects</p>
                    <div class="w-full h-fit grid gap-2">
                        <div @click="view = project.id" v-for="(project, index) in projects" :key="index" :class="view == project.id ? 'bg-custom-bg dark:bg-custom-gray dark:bg-opacity-50' : 'hover:bg-custom-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-50' " class="w-full h-fit flex items-center gap-2 px-8 py-2 text-[14px] hover:bg-custom-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-50 cursor-pointer">
                            <div :class="'bg-['+project.color+']'" class="w-[10px] h-[10px] rounded-[4px]"></div>
                            <div class="w-fit h-fit grid"><p class="truncate">{{ project.name }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full h-full z-10 absolute pt-[80px] pl-[300px] pr-[50px] border overflow-y-scroll">
            <div class="w-full h-full">
                <myTasks v-if="view == 'my-tasks'" :projects="projects" :updateTasks="updateTasks" @loading="loading = !loading" @tasksUpdated="updateTasks = false; loading = false;" />
                <projectView v-else @loading="loading = !loading" @tasksUpdated="updateTasks = false; loading = false;" :project_id="view" :updateTasks="updateTasks" />
            </div>
        </div>
    </div>
</template>

<script>
import topNav from '../components/topNav.vue'
import loading from '../components/loading.vue'

import CreateProject from '../components/pipeline/createProject.vue'
import CreateSection from '../components/pipeline/createSection.vue'
import CreateTask from '../components/pipeline/createTask.vue'

import TaskView from '../components/pipeline/taskView.vue'

import myTasks from '../components/pipeline/myTasks.vue'
import projectView from '../components/pipeline/project.vue'

import Menu from 'primevue/menu';

export default {
    name: "Pipelines",
    data(){
        return{
            updateTasks: false,
            loading: true,
            ready: false,
            create_items: [
                {
                    label: 'Task',
                    icon: 'pi pi-check-circle',
                    command: () => {
                        this.createTask = true
                    }
                },
                {
                    label: 'Project',
                    icon: 'pi pi-list',
                    command: () => {
                        this.createProject = true
                    }
                },
                {
                    label: 'Section',
                    icon: 'pi pi-folder',
                    command: () => {
                        this.createSection = true
                    }
                }
            ],
            createTask: false,
            createProject: false,
            createSection: false,
            view: '',
            viewTask: false,
            selectedProject: '',
            projects: [],
        }
    },
    async created(){
        await axios.get('/api/pipeline/projects')
        .then(response => {
            this.projects = response.data.projects
        })

        this.view = this.$route.params.project

        if(this.$route.params.task){
            this.viewTask = true
        }

        this.loading = false
        this.ready = true
    },
    watch: {
        view: async function(value){
            this.$router.replace({params: {project: value}})
        },
        '$route.params.task': async function(value){
            if(value){
                this.viewTask = true
            }else{
                this.viewTask = false
            }
        }
    },
    methods: {
        toggle_create(event){
            this.$refs['create_menu'].toggle(event);
        },
        async updateProjects(){
            this.loading = true
            this.ready = false

            await axios.get('/api/pipeline/projects')
            .then(response => {
                this.projects = response.data.projects
            })

            this.loading = false
            this.ready = true
        }
    },
    components: {
        topNav,
        loading,
        Menu,
        myTasks,
        CreateProject,
        CreateTask,
        CreateSection,
        projectView,
        TaskView
    }
}
</script>