<template>
    <!-- Send to Notebook add Title -->
    <div v-if="enterTitle" class="w-full h-full grid z-30 absolute">
        <div class="w-[604px] h-[303px] p-6 bg-sidebar-bg grid m-auto rounded-[4px] border-[1px] border-custom-black border-opacity-10 shadow-newdrop">
            <div class="w-full h-fit flow-root">
                <div class="w-fit h-full flex items-center float-left">
                    <p class="text-[24px] text-custom-black font-semibold">Send to Notebook</p>
                </div>

                <!-- Close Send to Notebooks -->
                <div class="w-[39px] h-[39px] grid bg-white shadow-newdrop float-right cursor-pointer">
                    <Icon @click="enterTitle = false; notebook_title = ''" icon="charm:cross" height="24" class="m-auto text-custom-red" />
                </div>
            </div>

            <!-- Notebook Name -->
            <InputText v-model="notebook_title" placeholder="Notebook Title" class="w-full h-[48px] rounded-[2px]" />

            <!-- Send to Notebooks -->
            <button @click="sendToNotebook()" class="w-full h-[48px] text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">Send to Notebooks</button>
        </div>
    </div>


    <div class="w-full h-full grid grid-cols-3 gap-8 z-20">
        <div class="w-full h-full grid">
            <!-- Task Menu -->
            <div class="w-full h-fit my-auto border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                <!-- Todays Tasks -->
                <div @click="get('today')" :class="type == 'today' ? 'bg-[#F4F4F4]' : 'opacity-60'" class="h-[56px] w-full px-6 text-cusomt-black flow-root cursor-pointer">
                    <div class="w-fit h-full flex items-center gap-4 float-left">
                        <Icon icon="ic:round-star" height="24" class="m-auto" />
                        <p class="font-medium text-[16px]">Today's Reminders</p>
                    </div>

                    <div class="w-fit h-full flex items-center float-right">
                        <p v-if="type == 'today'" class="text-[20px] font-medium">{{ count }}</p>
                    </div>
                </div>

                <!-- All Tasks -->
                <div @click="get('all')" :class="type == 'all' ? 'bg-[#F4F4F4]' : 'opacity-60'" class="h-[56px] w-full px-6 text-cusomt-black flow-root cursor-pointer">
                    <div class="w-fit h-full flex items-center gap-4 float-left">
                        <Icon icon="mingcute:task-fill" height="24" class="m-auto" />
                        <p class="font-medium text-[16px]">All Reminders</p>
                    </div>

                    <div class="w-fit h-full flex items-center float-right">
                        <p v-if="type == 'all'" class="text-[20px] font-medium">{{ count }}</p>
                    </div>
                </div>

                <!-- Late Tasks -->
                <div @click="get('late')" :class="type == 'late' ? 'bg-[#F4F4F4]' : 'opacity-60'" class="h-[56px] w-full px-6 text-cusomt-black flow-root cursor-pointer">
                    <div class="w-fit h-full flex items-center gap-4 float-left">
                        <Icon icon="bxs:error" height="24" class="m-auto" />
                        <p class="font-medium text-[16px]">Late Reminders</p>
                    </div>

                    <div class="w-fit h-full flex items-center float-right">
                        <p v-if="type == 'late'" class="text-[20px] font-medium">{{ count }}</p>
                    </div>
                </div>
            </div>

            <div class="w-full h-fit grid gap-4 my-auto">
                <!-- Personal Notes -->
                <Editor v-model="note" @selection-change="updatePersonalNote" editorStyle="height: 283px" >
                    <template v-slot:toolbar>
                        <span class="ql-formats flex items-center">
                            <button v-tooltip.bottom="'Bold'" class="ql-bold"></button>
                            <button v-tooltip.bottom="'Italic'" class="ql-italic"></button>
                            <button v-tooltip.bottom="'Underline'" class="ql-underline"></button>
                            <button v-tooltip.bottom="'Link'" class="ql-link"></button>
                            <button v-tooltip.bottom="'Code Block'" class="ql-code-block"></button>
                            <button v-tooltip.bottom="'Image'" class="ql-image"></button>
                        </span>
                    </template>
                </Editor>

                <!-- Send to Notebook -->
                <button @click="enterTitle = true" class="w-fit h-fit flex items-center gap-4 px-4 py-2 text-[16px] text-white font-semibold bg-custom-black rounded-[4px]"><Icon icon="mingcute:send-plane-fill" height="24" /> Send to Notebook</button>
            </div>
        </div>

        <!-- Reminders -->
        <div class="w-full h-full col-span-2 grid">
            <div class="w-full h-fit grid gap-4">
                <div class="w-full h-fit flex items-center">
                    <!-- Important Indicator -->
                    <div v-if="tasks.length > 0" class="w-fit h-fit mx-auto flex items-center gap-2 important-checkbox">
                        <Checkbox @click="update('important')" v-if="type == 'today'" v-model="tasks[selectedTask].important" :binary="true" />
                        <Checkbox @click="update('important')" v-else v-model="tasks[selectedDay].tasks[selectedTask].important" :binary="true" />

                        <p class="text-[16px] text-custom-red font-medium">Important</p>
                    </div>

                    <InputText v-model="taskName" placeholder="Task Name" class="w-[400px] h-[48px] mx-auto rounded-[2px]" />

                    <button @click="create()" class="flex items-center h-[48px] gap-4 px-8 text-white text-[16px] font-semibold bg-custom-purple rounded-[4px] shadow-newdrop"><Icon icon="ic:round-plus" height="24" />Create</button>
                </div>
                
                <!-- Tasks For Today -->
                <div v-if="type == 'today'" class="w-full h-fit max-h-[500px] grid gap-4 overflow-y-scroll">
                    <p class="w-fit h-fit flex items-center gap-2 px-4 text-[16px] text-custom-light-gray font-medium"><i class="pi pi-calendar"></i> Today</p>
                    <div @click="selectedTask = index" v-for="(task, index) in tasks" :key="index" :class="[task.important ? 'border-custom-red text-custom-red' : 'border-custom-black text-custom-black', selectedTask == index ? (task.important ? 'text-white bg-custom-red' : 'bg-[#F4F4F4] text-custom-black') : 'bg-white text-custom-black']" class=" w-full h-[52px] flex items-center gap-4 px-6 text-[16px] font-medium border-[1px] border-opacity-10 rounded-[2px] cursor-pointer">
                        <Icon @click="selectedTask = index; complete()" icon="material-symbols:check-circle-rounded" height="24" class="hover:text-custom-green" />
                        <p>{{ task.task }}</p>
                    </div>
                </div>

                <!-- Tasks with Multiple Days -->
                <div v-else class="w-full h-fit max-h-[500px] grid gap-4 overflow-y-scroll">
                    <div v-for="(day, index) in tasks" :key="index" class="w-full h-fit grid gap-4">
                        <p class="w-fit h-fit flex items-center gap-2 px-4 text-[16px] text-custom-light-gray font-medium"><i class="pi pi-calendar"></i> {{ day.day == 'Today' ? day.day : moment(day.day).format('MM/DD/YYYY') }}</p>
                        <div @click="selectedTask = taskIndex; selectedDay = index" v-for="(task, taskIndex) in day.tasks" :key="taskIndex" :class="[task.important ? 'border-custom-red text-custom-red' : 'border-custom-black text-custom-black', (selectedTask == taskIndex && selectedDay == index ? (task.important ? 'text-white bg-custom-red' : 'bg-[#F4F4F4] text-custom-black') : 'text-custom-black bg-white')]" class=" w-full h-[52px] flex items-center gap-4 px-6 text-[16px] font-medium border-[1px] border-opacity-10 rounded-[2px] cursor-pointer">
                            <Icon @click="selectedTask = taskIndex; selectedDay = index; complete()" icon="material-symbols:check-circle-rounded" height="24" class="hover:text-custom-green" />
                            <p>{{ task.task }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import NotFoundAnimation from '../../../assets/notFound.json'
import LoadingAnimation from '../../../assets/cpuLoading.json'
import InputText from 'primevue/inputtext';
import moment from 'moment'

import { Icon } from '@iconify/vue';

import Checkbox from 'primevue/checkbox';
import Editor from 'primevue/editor';

export default {
    name: "Reminder Tasks",
    data(){
        return{
            moment,
            NotFoundAnimation,
            LoadingAnimation,
            type: "today",
            selectedDay: 0,
            selectedTask: 0,
            tasks: [],
            taskName: "",
            note: {},
            count: '',
            enterTitle: false,
            notebook_title: ""
        }
    },
    async created(){
        this.moment = moment
    },
    async mounted(){
        this.$emit('loading')
        await axios.get('/api/quick-tasks/'+this.type)
        .then(response => {
            this.tasks = response.data.tasks
            this.count = response.data.count
            this.note = response.data.note
        })

        this.tasks.forEach(task => {
            if(task.important == 1){
                task.important = true
            }else if(task.important == 0){
                task.important = false
            }
        })

        this.$emit('loading')
    },
    methods: {
        async create(){
            this.$emit('loading')
            if(!this.taskName){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Task',
                    detail: "Please enter a task name to create a new task.",
                    life: 2500
                })

                this.$emit('loading')
                return
            }

            await axios.post('/api/quick-task', {"task": this.taskName})
            .then(response => {
                this.taskName = ""
            })

            await axios.get('/api/quick-tasks/'+this.type)
            .then(response => {
                this.tasks = response.data.tasks
                this.count = response.data.count
            })

            if(this.type == 'today'){
                this.tasks.forEach(task => {
                    if(task.important == 1){
                        task.important = true
                    }else if(task.important == 0){
                        task.important = false
                    }
                })
            }else{
                this.tasks.forEach(day => {
                    day.tasks.forEach(task => {
                        if(task.important == 1){
                            task.important = true
                        }else if(task.important == 0){
                            task.important = false
                        }
                    })
                })
            }

            this.selectedTask = 0
            this.selectedDay = 0

            this.$emit('loading')
        },
        async get(value){
            this.$emit('loading')
            await axios.get('/api/quick-tasks/'+value)
            .then(response => {
                this.tasks = response.data.tasks
                this.count = response.data.count
            })

            if(value == 'today'){
                this.tasks.forEach(task => {
                    if(task.important == 1){
                        task.important = true
                    }else if(task.important == 0){
                        task.important = false
                    }
                })
            }else{
                this.tasks.forEach(day => {
                    day.tasks.forEach(task => {
                        if(task.important == 1){
                            task.important = true
                        }else if(task.important == 0){
                            task.important = false
                        }
                    })
                })
            }

            this.selectedTask = 0
            this.selectedDay = 0
            this.type = value
            this.$emit('loading')
        },
        async complete(){
            this.$emit('loading')
            await axios.delete(this.type == 'today' ? '/api/quick-task/'+this.tasks[this.selectedTask].id : '/api/quick-task/'+this.tasks[this.selectedDay].tasks[this.selectedTask].id)
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Task',
                    detail: response.data.message,
                    life: 2500
                })

                if(this.type == 'today'){
                    this.tasks.splice(this.selectedTask, 1)
                }else{
                    this.tasks[this.selectedDay].tasks.splice(this.selectedTask, 1)
                }
                
            })

            this.selectedTask = 0
            this.$emit('loading')
        },
        async update(type){
            this.$emit('loading')
            await axios.put(this.type == 'today' ? '/api/quick-task/'+this.tasks[this.selectedTask].id+'/'+type : '/api/quick-task/'+this.tasks[this.selectedDay].tasks[this.selectedTask].id+'/'+type)
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Task',
                    detail: response.data.message,
                    life: 2500
                })
            })

            await axios.get('/api/quick-tasks/'+this.type)
            .then(response => {
                console.log(response)
                this.tasks = response.data.tasks
            })

            if(this.type == 'today'){
                this.tasks.forEach(task => {
                    if(task.important == 1){
                        task.important = true
                    }else if(task.important == 0){
                        task.important = false
                    }
                })
            }else{
                this.tasks.forEach(day => {
                    day.tasks.forEach(task => {
                        if(task.important == 1){
                            task.important = true
                        }else if(task.important == 0){
                            task.important = false
                        }
                    })
                })
            }

            this.$emit('loading')
        },
        async sendToNotebook(){
            await axios.post('/api/notebook/personal-note', {"body": this.note, "title": this.notebook_title})
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Notebook',
                    detail: response.data.message,
                    life: 2500
                })


                this.enterTitle = false
                this.notebook_title = ""
                this.note = ""
            })
        },
        async updatePersonalNote(){
            await axios.put('/api/quick-task/personal-note', {"note": this.note})
        }
    },
    components: {
        InputText,
        Editor,
        Icon,
        Checkbox
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