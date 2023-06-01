<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <topNav :active="'dashboard'" @loading="loading = !loading" />

    <div v-if="ready" class="w-full h-fit grid gap-8 p-10">
        <div class="w-full h-fit grid gap-2">
            <p class="text-[32px] text-custom-black font-semibold">Welcome to your dashboard {{ name }},</p>
            <p class="text-[16px] text-custom-black opacity-50">Here you can access some high level information from all systems,</p>
        </div>

        <div class="w-full h-fit grid grid-cols-3 gap-6">
            <div class="w-full h-fit grid border-[1px] border-custom-black border-opacity-10 bg-sidebar-bg rounded-[4px]">
                <div class="w-full h-[77px] grid p-6 border-b-[1px] border-custom-black border-opacity-10">
                    <p class="w-fit h-fit my-auto text-[24px] text-custom-black font-semibold">Personal Notepad</p>
                </div>

                <!-- Personal Notes -->
                <Editor v-model="data.note" @selection-change="updatePersonalNote" editorStyle="height: 283px" >
                    <template v-slot:toolbar>
                        <span class="ql-formats flex items-center">
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

            <div class="w-full h-fit grid col-span-2 border-[1px] border-custom-black border-opacity-10 bg-sidebar-bg rounded-[4px]">
                <div class="w-full h-[77px] flow-root p-6 border-b-[1px] border-custom-black border-opacity-10">
                    <div class="w-fit h-full flex items-center float-left">
                        <p class="w-fit h-fit my-auto text-[24px] text-custom-black font-semibold">Quick Tasks</p>
                    </div>

                    <div class="w-fit h-full flex items-center gap-6 float-right">
                        <div v-if="data.quickTasks.length > 0" class="w-fit h-fit mx-auto flex items-center gap-2 important-checkbox">
                            <Checkbox @click="updateImportant" v-model="data.quickTasks[currentTask].important" :binary="true" />
                            <p class="text-[16px] text-custom-red font-medium">Important</p>
                        </div>                        

                        <InputText v-model="taskName" placeholder="Task Name" class="w-[300px] h-[48px] mx-auto rounded-[2px]" />

                        <!-- Create Task Button -->
                        <button @click="createQuickTask()" class="w-[175px] h-[48px] text-white text-[16px] font-semibold bg-custom-purple shadow-newdrop rounded-[4px]"><div class="w-fit mx-auto flex gap-4 items-center"><Icon icon="ic:round-plus" height="24" /><p>Create</p></div></button>
                    </div>
                </div>

                <div v-if="data.quickTasks.length > 0" class="w-full h-fit p-6 grid gap-4">
                    <div @click="currentTask = index" v-for="(task, index) in data.quickTasks" :key="index" :class="[task.important ? 'border-custom-red text-custom-red' : 'border-custom-black text-custom-black', currentTask == index ? (task.important ? 'text-white bg-custom-red' : 'bg-[#F4F4F4] text-custom-black') : 'bg-white text-custom-black']" class=" w-full h-[52px] flex items-center gap-4 px-6 text-[16px] font-medium border-[1px] border-opacity-10 rounded-[2px] cursor-pointer">
                        <Icon @click="currentTask = index; completeTask()" icon="material-symbols:check-circle-rounded" height="24" class="hover:text-custom-green" />
                        <p>{{ task.task }}</p>
                    </div>
                </div>

                <div v-else class="w-full h-fit">
                        <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'200px'" :height="'200px'" :speed="0.75" class="m-auto" />
                    </div>
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-3 gap-6">
            <!-- Onboarding Information -->
            <div class="w-full h-fit grid col-span-2 border-[1px] border-custom-black border-opacity-10 bg-sidebar-bg rounded-[4px]">
                <div class="w-full h-[77px] grid p-6 border-b-[1px] border-custom-black border-opacity-10">
                    <p class="w-fit h-fit my-auto text-[24px] text-custom-black font-semibold">Onboarding Portal</p>
                </div>

                <div class="w-full h-fit p-6 grid gap-6">
                    <p class="text-[20px] text-custom-black font-semibold">Overview</p>

                    <div class="w-full h-fit grid grid-cols-3 gap-6">
                        <div class="w-full h-fit grid gap-4">
                            <!-- Agents Onboarding -->
                            <div class="w-full h-[129px] grid p-6 bg-custom-purple bg-opacity-20 rounded-[4px] shadow-newdrop">
                                <p class="text-[16px] text-custom-black font-semibold">Agents Onboarding</p>
                                <div class="w-full h-fit flow-root">
                                    <p class="text-[32px] text-custom-purple font-semibold float-right">{{ data.onboarding.data.onboarding }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="w-full h-fit grid gap-4">
                            <!-- Agents Under Review -->
                            <div class="w-full h-[129px] grid p-6 bg-custom-red bg-opacity-20 rounded-[4px] shadow-newdrop">
                                <p class="text-[16px] text-custom-black font-semibold">Agents Under Review</p>
                                <div class="w-full h-fit flow-root">
                                    <p class="text-[32px] text-custom-red font-semibold float-right">{{ data.onboarding.data.under_review }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="w-full h-fit grid gap-4">
                            <!-- Agents in training -->
                            <div class="w-full h-[129px] grid p-6 bg-custom-yellow bg-opacity-20 rounded-[4px] shadow-newdrop">
                                <p class="text-[16px] text-custom-black font-semibold">Agents In Training</p>
                                <div class="w-full h-fit flow-root">
                                    <p class="text-[32px] text-custom-yellow font-semibold float-right">{{ data.onboarding.data.in_training }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-full flow-root">
                        <div class="w-fit h-full flex items-center float-left"><p class="text-[20px] text-custom-black font-semibold">Agents Onboarding</p></div>
                        <div class="w-fit h-full flex items-center float-right"><a href="/onboarding/agents" class="flex items-center gap-4 text-custom-purple text-[16px]">View all <Icon :icon="'material-symbols:arrow-back-rounded'" height="20" class="rotate-180" /></a></div>
                    </div>

                    <div class="w-full h-fit px-4 grid grid-cols-4 text-[16px] text-custom-black font-medium border-b-[1px] border-custom-black border-opacity-10">
                        <p class="opacity-50">Agency Name</p>
                        <p class="opacity-50">Agent Name</p>
                        <p class="opacity-50">Email</p>
                        <p class="opacity-50">Stage</p>
                    </div>

                    <!-- Recent Agencys Onboarding -->
                    <a v-for="(agency, index) in data.onboarding.data.agents" :key="index" :href="'https://onboarding.rocketmga.com/admin/agency/'+agency.rocket_id" target="_blank" class="w-full h-[49px] grid border-[1px] border-custom-black border-opacity-10 bg-white rounded-[2px]">
                        <div class="w-full h-fit my-auto px-4 grid grid-cols-4">
                            <div class="w-full h-fit grid">
                                <p class="truncate pr-4">{{ agency.agency_name }}</p>
                            </div>
                            <div class="w-full h-fit grid">
                                <p class="truncate pr-4">{{ agency.agent_name }}</p>
                            </div>
                            <div class="w-full h-fit grid">
                                <p class="truncate pr-4">{{ agency.email }}</p>
                            </div>
                            <div class="w-full h-fit grid">
                                <p class="truncate pr-4 text-custom-purple">{{ agency.onboarding_stage == 'incomplete' ? 'Incomplete Paperwork' : (agency.onboarding_stage == 'completed' ? 'Paperwork Submitted' : (agency.onboarding_stage == 'approved' ? 'Agency Approved' : 'Agency Appointed')) }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Pipeline -->
            <div class="w-full h-fit border-[1px] border-custom-black border-opacity-10 bg-sidebar-bg rounded-[4px]">
                <div class="w-full h-[77px] grid p-6 border-b-[1px] border-custom-black border-opacity-10">
                    <p class="w-fit h-fit my-auto text-[24px] text-custom-black font-semibold">Pipelines</p>
                </div>

                <div class="w-full h-fit p-6 grid gap-6">
                    <p class="text-[20px] text-custom-black font-semibold">Overview</p>

                    <div class="w-full h-fit grid grid-cols-2 gap-6">
                        <!-- Total Tasks -->
                        <div class="w-full h-[129px] grid p-6 bg-custom-purple bg-opacity-20 rounded-[4px] shadow-newdrop">
                            <p class="text-[16px] text-custom-black font-semibold">Total Tasks</p>
                            <div class="w-full h-fit flow-root">
                                <p class="text-[32px] text-custom-purple font-semibold float-right">{{ data.pipeline.total }}</p>
                            </div>
                        </div>
                        
                        <!-- Due Today -->
                        <div class="w-full h-[129px] grid p-6 bg-custom-red bg-opacity-20 rounded-[4px] shadow-newdrop">
                            <p class="text-[16px] text-custom-black font-semibold">Due Today</p>
                            <div class="w-full h-fit flow-root">
                                <p class="text-[32px] text-custom-red font-semibold float-right">{{ data.pipeline.dueToday }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-full flow-root">
                        <div class="w-fit h-full flex items-center float-left"><p class="text-[20px] text-custom-black font-semibold">Recent Tasks</p></div>
                        <div class="w-fit h-full flex items-center float-right"><a href="/pipeline/my-tasks" class="flex items-center gap-4 text-custom-purple text-[16px]">View all <Icon :icon="'material-symbols:arrow-back-rounded'" height="20" class="rotate-180" /></a></div>
                    </div>

                    <div v-if="data.pipeline.recent.length > 0" class="w-full h-fit grid gap-6">
                        <div class="w-full h-fit px-4 grid grid-cols-3 text-[16px] text-custom-black font-medium border-b-[1px] border-custom-black border-opacity-10">
                            <p class="opacity-50 col-span-2">Task Name</p>
                            <p class="opacity-50">Due Date</p>
                        </div>

                        <!-- Recent Tasks -->
                        <a v-for="(task, index) in data.pipeline.recent" :key="index" :href="'/pipeline/'+task.project_id+'/'+task.id" class="w-full h-[49px] grid border-[1px] border-custom-black border-opacity-10 bg-white rounded-[2px]">
                            <div class="w-full h-fit my-auto px-4 grid grid-cols-3">
                                <div class="w-full h-fit grid col-span-2">
                                    <p class="truncate pr-4">{{ task.name }}</p>
                                </div>
                                <div class="w-full h-fit grid">
                                    <p :class="moment(task.due_date).isBefore(new Date()) ? 'text-custom-red' : ''" class="truncate pr-4">{{ moment(task.due_date).format('ddd, MMM DD') }}</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div v-else class="w-full h-fit">
                        <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'200px'" :height="'200px'" :speed="0.75" class="m-auto" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Rover -->
        <div class="w-full h-fit border-[1px] border-custom-black border-opacity-10 bg-sidebar-bg rounded-[4px]">
            <div class="w-full h-[77px] grid p-6 border-b-[1px] border-custom-black border-opacity-10">
                <p class="w-fit h-fit my-auto text-[24px] text-custom-black font-semibold">ROVER</p>
            </div>

            <div class="w-full h-fit p-6 grid grid-cols-3 gap-6">
                <div class="w-full h-fit grid gap-4 col-span-2">
                    <div class="w-full h-full flow-root">
                        <div class="w-fit h-full flex items-center float-left"><p class="text-[20px] text-custom-black font-semibold">Recent Errors</p></div>
                        <div class="w-fit h-full flex items-center float-right"><a href="/rover/errors" class="flex items-center gap-4 text-custom-purple text-[16px]">View all <Icon :icon="'material-symbols:arrow-back-rounded'" height="20" class="rotate-180" /></a></div>
                    </div>

                    <div class="w-full h-fit px-4 grid grid-cols-4 text-[16px] text-custom-black font-medium border-b-[1px] border-custom-black border-opacity-10">
                        <p class="opacity-50">Application ID</p>
                        <p class="opacity-50">Carrier</p>
                        <p class="opacity-50">Error Type</p>
                        <p class="opacity-50">Stage</p>
                    </div>

                    <!-- Recent Errors Submitted -->
                    <a v-for="(error, index) in data.rover.data.recent" :key="index" :href="'https://rover.rocketflood.com/task/'+error.app_id+'/'+error.carrier" target="_blank" class="w-full h-[49px] grid border-[1px] border-custom-black border-opacity-10 bg-white rounded-[2px]">
                        <div class="w-full h-fit my-auto px-4 grid grid-cols-4">
                            <div class="w-full h-fit grid">
                                <p class="truncate pr-4">{{ error.app_id }}</p>
                            </div>
                            <div class="w-full h-fit grid">
                                <p class="truncate pr-4">{{ error.product == "HOME" ? carriers[error.carrier.substring(3)].name : carriers[error.carrier].name }}</p>
                            </div>
                            <div class="w-full h-fit grid">
                                <p class="truncate pr-4">{{ error.type }}</p>
                            </div>
                            <div class="w-full h-fit grid">
                                <p class="truncate pr-4 text-custom-purple">{{ error.status == 'debug' ? 'Debugging' : (error.status == 'update' ? 'Updates in Progress' : (error.status == 'test' ? 'Testing in Progress' : (error.status == 'digiprompt' ? 'Digiprompt Queue' : 'Fixed'))) }}</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="w-full h-fit grid gap-4">
                    <p class="text-[20px] text-custom-black font-semibold">Overview</p>

                    <!-- Todays Errors -->
                    <div class="w-full h-[71px] px-4 grid rounded-[2px] bg-custom-purple bg-opacity-20 shadow-newdrop">
                        <div class="w-full h-full flow-root">
                            <div class="w-fit h-full flex items-center float-left">
                                <p class="text-[16px] text-custom-black font-semibold">Today's Errors</p>
                            </div>

                            <div class="w-fit h-full flex items-center float-right">
                                <p class="text-[32px] text-custom-purple font-semibold">{{ data.rover.data.today }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Errors -->
                    <div class="w-full h-[71px] px-4 grid rounded-[2px] bg-custom-red bg-opacity-20 shadow-newdrop">
                        <div class="w-full h-full flow-root">
                            <div class="w-fit h-full flex items-center float-left">
                                <p class="text-[16px] text-custom-black font-semibold">Pending Errors</p>
                            </div>

                            <div class="w-fit h-full flex items-center float-right">
                                <p class="text-[32px] text-custom-red font-semibold">{{ data.rover.data.pending }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fixed Errors -->
                    <div class="w-full h-[71px] px-4 grid rounded-[2px] bg-custom-yellow bg-opacity-20 shadow-newdrop">
                        <div class="w-full h-full flow-root">
                            <div class="w-fit h-full flex items-center float-left">
                                <p class="text-[16px] text-custom-black font-semibold">Fixed Errors</p>
                            </div>

                            <div class="w-fit h-full flex items-center float-right">
                                <p class="text-[32px] text-custom-yellow font-semibold">{{ data.rover.data.fixed }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import topNav from '../components/topNav.vue'
import loading from '../components/loading.vue'

import InputText from 'primevue/inputtext';
import Editor from 'primevue/editor';
import Checkbox from 'primevue/checkbox';

import { Icon } from '@iconify/vue';
import moment from 'moment';

import carriers from '../../assets/rover_carriers.json'
import NotFoundAnimation from '../../assets/newNotFound.json'

export default{
    name: "Dashboard",
    data(){
        return {
            NotFoundAnimation,
            carriers,
            loading: true,
            ready: false,
            taskName: "",
            currentTask: 0,
            name: "",
            data: {}
        }
    },
    async created(){
        this.moment = moment

        await axios.get('/api/dashboard')
        .then(response => {
            this.name = response.data.name
            this.data = response.data.data
        })

        this.data.quickTasks.forEach(task => {
            if(task.important == 0){
                task.important = false
            }else if(task.important == 1){
                task.important = true
            }
        })

        this.ready = true
        this.loading = false
    }, 
    methods: {
        async updatePersonalNote(){
            await axios.put('/api/quick-task/personal-note', {"note": this.data.note})
        },
        async updateImportant(){
            this.loading = true

            await axios.put('/api/quick-task/'+this.data.quickTasks[this.currentTask].id+'/important')
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Task',
                    detail: response.data.message,
                    life: 2500
                })
            })

            this.loading = false
        },
        async createQuickTask(){
            this.loading = true

            if(!this.taskName){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Task',
                    detail: "Please enter a task name to create a new task.",
                    life: 2500
                })

                this.loading = false
                return
            }

            await axios.post('/api/quick-task', {"task": this.taskName})
            .then(response => {
                this.taskName = ""
            })

            await axios.get('/api/dashboard')
            .then(response => {
                this.data = response.data.data
            })

            this.loading = false
        },
        async completeTask(){
            this.loading = true

            await axios.delete('/api/quick-task/'+this.data.quickTasks[this.currentTask].id)
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Task',
                    detail: response.data.message,
                    life: 2500
                })

                this.data.quickTasks.splice(this.currentTask, 1)
            })
            
            this.currentTask = 0

            this.loading = false
        }
    },
    components: {
        topNav,
        loading,
        Icon,
        Editor,
        Checkbox,
        InputText
    }
}
</script>

<style scoped>
:deep( .important-checkbox .p-checkbox:not(.p-checkbox-disabled) .p-checkbox-box.p-highlight){
    background-color: #F42D2D !important;
    border-color: #F42D2D !important;
}
:deep( .important-checkbox .p-checkbox .p-checkbox-box ){
    border-color: #F42D2D !important;
}
</style>