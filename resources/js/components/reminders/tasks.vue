<template>
    <!-- Loading -->
    <div v-if="loading" class="w-full h-full grid bg-[#3F3F3F] bg-opacity-[26%] z-50 absolute ml-[-67px] rounded-[5px]">
        <div class="w-full h-full grid mt-[10%]">
            <LottieAnimation :animationData="LoadingAnimation" :width="'250px'" :height="'250px'" :speed="1" class="m-auto" />
        </div>
    </div>

    <!-- Send to Notebook add Title -->
    <div v-if="enterTitle" class="w-full h-full grid bg-[#3F3F3F] bg-opacity-[26%] z-50 absolute ml-[-67px] rounded-[5px]">
        <div class="w-full h-full grid m=auto">
            <div class="w-fit h-fit grid gap-4 m-auto p-4 bg-white dark:bg-custom-light-gray-bg rounded-[4px] border-[1px] border-custom-light dark:border-custom-gray shadow-customdrop">
                <p class="text-center text-[18px] text-custom-gray dark:text-white font-medium">Please enter a title to add this to your Notebooks</p>
                <InputText v-model="notebook_title" type="text" placeholder="Notebook Title" class="w-full h-[30px] rounded-[4px] dark:bg-custom-dark-bg" />

                <div class="w-full h-fit grid grid-cols-2 gap-8">
                    <button @click="sendToNotebook()" class="w-full h-fit px-4 py-[3px] bg-custom-dark-blue dark:bg-custom-gray dark:bg-opacity-50 text-white font-medium rounded-[4px]">Send to Notebook</button>
                    <button @click="enterTitle = false; notebook_title = ''" class="w-full h-fit px-4 py-[3px] text-custom-red dark:text-white hover:bg-custom-light dark:hover:bg-custom-gray dark:hover:bg-opacity-20 font-medium rounded-[4px]">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full h-full grid rounded-tr-[5px] rounded-br-[5px]">
        <div class="w-full h-[50px] flex items-center gap-4 px-6 text-[14px] font-medium shadow-customdrop z-10">
            <button @click="create()" class="flex items-center gap-2 rounded-full bg-custom-dark-blue dark:bg-custom-gray dark:bg-opacity-50 text-white px-2 py-[2px]">Create Task <i class="pi pi-plus"></i></button>
            <InputText v-model="taskName" type="text" placeholder="Task Name" class="w-[300px] h-[25px] text-[16px] rounded-[4px] dark:bg-custom-dark-bg" />
            <div v-if="tasks.length > 0" class="w-fit h-fit flex items-center gap-4">
                <button @click="update('date')" class="flex items-center gap-2 rounded-full text-white px-2 py-[2px] bg-custom-orange"><i class="pi pi-history"></i>Reset Date</button>
                <button @click="update('important')" class="flex items-center gap-2 rounded-full text-white px-2 py-[2px] bg-custom-red dark:bg-red-500"><i class="pi pi-history"></i>{{ type == 'today' ? (tasks[selectedTask].important ? 'Undo Important' : 'Mark Important') : (tasks[selectedDay].tasks[selectedTask].important ? 'Undo Important' : 'Mark Important' ) }}</button>
                <button @click="complete()" class="flex items-center gap-2 rounded-full text-white px-2 py-[2px] bg-custom-green"><i class="pi pi-check-circle"></i>Task Complete</button>
            </div>
        </div>

        <div class="w-full h-full grid m-auto absolute grid-cols-5">
            <div class="w-full h-fit grid gap-8 m-auto col-span-2">
                <div class="w-[500px] h-fit m-auto grid gap-2 p-4 text-custom-dark-blue dark:text-white border-[1px] border-custom-light dark:border-custom-gray dark:bg-custom-dark-bg shadow-newdrop dark:shadow-none rounded-[4px]">
                    <p @click="get('today')" :class="type == 'today' ? 'bg-custom-light-blue dark:bg-custom-gray dark:bg-opacity-30' : ''" class="text-[18px] flex items-center gap-2 p-2 font-medium rounded-[4px] hover:bg-custom-light-blue dark:hover:bg-custom-gray dark:hover:bg-opacity-30 cursor-pointer relative"><i class="pi pi-star"></i>Todays Task <span v-if="type == 'today'" class="right-4 absolute">{{ count }}</span></p>
                    <p @click="get('all')" :class="type == 'all' ? 'bg-custom-light-blue dark:bg-custom-gray dark:bg-opacity-30' : ''" class="text-[18px] flex items-center gap-2 p-2 font-medium rounded-[4px] hover:bg-custom-light-blue dark:hover:bg-custom-gray dark:hover:bg-opacity-30 cursor-pointer relative"><i class="pi pi-bookmark"></i>All Task <span v-if="type == 'all'" class="right-4 absolute">{{ count }}</span></p>
                    <p @click="get('late')" :class="type == 'late' ? 'bg-custom-light-blue dark:bg-custom-gray dark:bg-opacity-30' : ''" class="text-[18px] flex items-center gap-2 p-2 font-medium rounded-[4px] hover:bg-custom-light-blue dark:hover:bg-custom-gray dark:hover:bg-opacity-30 cursor-pointer relative"><i class="pi pi-exclamation-triangle"></i>Late Task <span v-if="type == 'late'" class="right-4 absolute">{{ count }}</span></p>
                </div>

                <div class="w-[500px] h-fit m-auto grid gap-2">
                    <p class="text-[18px] text-custom-dark-blue dark:text-white">Personal Notepad</p>
                    <div class="w-full h-fit grid shadow-newdrop dark:shadow-none rounded-[4px] dark:p-4 dark:bg-custom-dark-bg bg-custom-bg">
                        <Editor v-model="note" @selection-change="updatePersonalNote" editorStyle="height: 200px" />
                    </div>
                    <button @click="enterTitle = true" class="w-fit h-fit flex items-center gap-2 mt-2 px-4 py-[3px] text-custom-dark-blue dark:text-white font-medium bg-custom-bg dark:bg-custom-dark-bg dark:border-custom-gray dark:border-[1px] rounded-full shadow-newdrop dark:shadow-none"><i class="pi pi-send"></i>Send to Notebooks</button>
                </div>
            </div>

            <div class="w-full h-full grid col-span-3">
                <div class="h-fit w-[80%] m-auto ">
                    <LottieAnimation v-if="tasks.length == 0" :animationData="NotFoundAnimation" :width="'250px'" :height="'250px'" :speed="1" class="m-auto" />
                    <div v-else class="w-full h-[400px] grid">
                        <!-- Task Layout for Today's Tasks -->
                        <div v-if="type == 'today'" class="w-full h-fit max-h-[400px] grid gap-4 overflow-y-scroll">
                            <p class="w-fit h-fit flex items-center gap-2 px-4 text-[16px] text-custom-light-gray font-medium"><i class="pi pi-calendar"></i> Today</p>
                            <div @click="selectedTask = index" v-for="(task, index) in tasks" :key="index" :class="task.important ? 'bg-custom-red border-custom-red dark:bg-red-500 dark:border-red-500' : 'bg-custom-dark-blue border-custom-dark-blue dark:bg-custom-dark-bg dark:border-custom-gray'" class="w-full h-[35px] grid border-[1px] rounded-full shadow-newdrop dark:shadow-none bg-opacity-50 text-white px-6 cursor-pointer">
                                <div class="w-fit h-fit my-auto flex items-center gap-4 text-[16px]">
                                    <div :class="[index == selectedTask ? (task.important ? 'bg-custom-red dark:bg-red-400' : 'bg-custom-dark-blue dark:bg-custom-gray') : 'bg-white', task.important ? 'border-custom-red dark:border-red-400' : 'border-custom-dark-blue dark:border-custom-gray']" class="w-[15px] h-[15px] rounded-full border-[4px]"></div>
                                    <p class="grid"><span class="truncate">{{ task.task }}</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Task Layout for Tasks with Dates -->
                        <div v-else class="w-full h-fit max-h-[400px] grid gap-4 overflow-y-scroll">
                            <div v-for="(day, index) in tasks" :key="index" class="w-full h-fit grid gap-4">
                                <p class="w-fit h-fit flex items-center gap-2 px-4 text-[16px] text-custom-light-gray font-medium"><i class="pi pi-calendar"></i> {{ day.day == 'Today' ? day.day : moment(day.day).format('MM/DD/YYYY') }}</p>
                                <div @click="selectedTask = taskIndex; selectedDay = index" v-for="(task, taskIndex) in day.tasks" :key="taskIndex" :class="task.important ? 'bg-custom-red border-custom-red dark:bg-red-500 dark:border-red-500' : 'bg-custom-dark-blue border-custom-dark-blue dark:bg-custom-dark-bg dark:border-custom-gray'" class="w-full h-[35px] grid border-[1px] rounded-full shadow-newdrop dark:shadow-none bg-opacity-50 text-white px-6 cursor-pointer">
                                    <div class="w-fit h-fit my-auto flex items-center gap-4 text-[16px]">
                                        <div :class="[taskIndex == selectedTask && selectedDay == index ? (task.important ? 'bg-custom-red dark:bg-red-400' : 'bg-custom-dark-blue dark:bg-custom-gray') : 'bg-white', task.important ? 'border-custom-red dark:border-red-400' : 'border-custom-dark-blue dark:border-custom-gray']" class="w-[15px] h-[15px] rounded-full border-[4px]"></div>
                                        <p class="grid"><span class="truncate">{{ task.task }}</span></p>
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
import NotFoundAnimation from '../../../assets/notFound.json'
import LoadingAnimation from '../../../assets/cpuLoading.json'
import InputText from 'primevue/inputtext';
import moment from 'moment'

import Editor from 'primevue/editor';

export default {
    name: "Reminder Tasks",
    data(){
        return{
            moment,
            NotFoundAnimation,
            LoadingAnimation,
            loading: false,
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
        this.loading = true
        await axios.get('/api/quick-tasks/'+this.type)
        .then(response => {
            this.tasks = response.data.tasks
            this.count = response.data.count
            this.note = response.data.note
        })
        this.loading = false
    },
    methods: {
        async create(){
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
                this.tasks.unshift(response.data.task)
                this.taskName = ""
            })

            this.selectedTask = 0

            this.loading = false
        },
        async get(value){
            this.loading = true
            await axios.get('/api/quick-tasks/'+value)
            .then(response => {
                this.tasks = response.data.tasks
                this.count = response.data.count
            })

            this.selectedTask = 0
            this.selectedDay = 0
            this.type = value
            this.loading = false
        },
        async complete(){
            this.loading = true
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
            this.loading = false
        },
        async update(type){
            this.loading = true
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
                this.tasks = response.data.tasks
            })
            this.loading = false
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
        Editor
    }
}
</script>