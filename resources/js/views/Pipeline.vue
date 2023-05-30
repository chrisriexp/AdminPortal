<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>
    
    <!-- Create Project -->
    <div v-if="create.project" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <CreateProject class="m-auto" @close="create.project = false" @updateProjects="updateProjects" @loading="loading = !loading" />
    </div>

    <!-- Create Section -->
    <div v-if="create.section" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <CreateSection class="m-auto" @close="create.section = false" :projects="projects" :project_id="view" @updateSections="updateTasks = true" />
    </div>

    <!-- Create Task -->
    <div v-if="create.task" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <CreateTask class="m-auto" @close="create.task = false" :projects="projects" :project_id="view" @updateTasks="updateTasks = true" @loading="loading = !loading" />
    </div>

    <!-- Task View -->
    <div v-if="viewTask" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <TaskView class="m-auto" @close="$router.replace({params: {task: null}}); updateTasks = true" @loading="loading = !loading" />
    </div>

    <div class="absolute z-30 w-full">
        <topNav :active="'pipeline'" @loading="loading = !loading" class="fixed z-40" />
    </div>

    <div v-if="ready" class="w-full h-screen z-20 absolute">
        <div class="w-[305px] h-full grid bg-sidebar-bg z-20 absolute">
            <div class="w-full max-h-[800px] h-fit grid gap-4 mt-[120px] px-6 overflow-y-scroll text-[16px]">
                <!-- My Tasks -->
                <div @click="view = 'my-tasks'" :class="view == 'my-tasks' ? 'bg-[#F1F1F1] text-custom-black' : 'opacity-60'" class="w-full h-[48px] p-4 flex items-center gap-4 hover:bg-[#F1F1F1] rounded-[4px] cursor-pointer">
                    <ClipboardDocumentListIcon class="h-[24px]" />
                    <p class="font-medium">My Tasks</p>
                </div>

                <div :class="view != 'my-tasks' ? '' : 'opacity-60'" class="w-full h-[48px] p-4 flex items-center gap-4 rounded-[4px] text-custom-black">
                    <PuzzlePieceIcon class="h-[24px]" />
                    <p class="font-medium">Projects</p>
                </div>

                <!-- Projects -->
                <div class="w-full h-fit grid gap-2 pl-8 text-[14px]">
                    <div @click="view = project.id" v-for="(project, index) in projects" :key="index" :class="view == project.id ? 'bg-[#F1F1F1] text-custom-black' : ''" class="w-full h-[48px] p-4 flex items-center gap-4 hover:bg-[#F1F1F1] rounded-[4px] cursor-pointer">
                        <div class="w-[10] h-fit"><div :class="'bg-['+project.color+']'" class="w-[10px] h-[10px] rounded-[4px]"></div></div>
                        <div :class="view == project.id ? '' : 'opacity-60'" class="w-full h-fit grid"><p class="font-medium truncate">{{ project.name }}</p></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full h-full z-10 absolute pt-[110px] pl-[350px] pr-[50px]">
            <div class="w-full h-full">
                <myTasks v-if="view == 'my-tasks'" :projects="projects" :updateTasks="updateTasks" @loading="loading = !loading" @tasksUpdated="updateTasks = false; loading = false;" @create="(value)=>{create[value] = true}" />
                <projectView v-else @loading="loading = !loading" @tasksUpdated="updateTasks = false; loading = false;" :project_id="view" :updateTasks="updateTasks" @create="(value)=>{create[value] = true}" />
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

import { ClipboardDocumentListIcon, PuzzlePieceIcon } from '@heroicons/vue/24/solid'

export default {
    name: "Pipelines",
    data(){
        return{
            updateTasks: false,
            loading: false,
            ready: false,
            create: {
                task: false,
                project: false,
                section: false,
            },
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
        TaskView,
        ClipboardDocumentListIcon,
        PuzzlePieceIcon
    }
}
</script>