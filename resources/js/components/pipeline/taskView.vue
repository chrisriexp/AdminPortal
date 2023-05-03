<template>
    <div v-if="ready" class="w-full h-full p-36">
        <div class="w-full h-full grid grid-cols-3 gap-6 p-6 text-custom-gray dark:text-white bg-white dark:bg-custom-light-gray-bg border-[1px] border-custom-light dark:border-custom-gray shadow-customdrop rounded-[8px]">
            <div class="w-full h-fit grid gap-4 text-[14px]">
                <!-- Close and Update Buttons -->
                <div class="w-full h-fit grid grid-cols-2 gap-6">
                    <button @click="update()" class="py-[4px] text-white font-medium bg-custom-dark-blue dark:bg-custom-dark-bg border-[1px] border-transparent dark:border-custom-gray rounded-[4px]">Update Task</button>

                    <button @click="$emit('close')" class="py-[4px] text-custom-light-gray font-medium hover:bg-custom-light dark:hover:bg-custom-gray dark:hover:bg-opacity-20 rounded-[4px]">Close</button>
                </div>

                <!-- Mark Task Complete -->
                <button @click="updateComplete()" :class="task.completed ? 'bg-custom-gray' : 'bg-custom-green'" class="w-fit my-4 px-4 py-[4px] text-white font-medium rounded-full">{{ task.completed ? "Mark Task Incomplete" : "Mark Task Completed" }}</button>

                <!-- Task Name -->
                <div class="w-full h-fit flex items-center gap-2">
                    <p class="font-medium w-[125px]">Task Name:</p>
                    <InputText type="text" v-model="task.name" class="w-full h-[40px] rounded-[4px] dark:bg-custom-dark-bg" />
                </div>

                <!-- Task Section -->
                <div class="w-full h-fit flex items-center gap-2">
                    <p class="font-medium w-[125px]">Section:</p>
                    <Dropdown v-model="task.section" :options="project.sections" optionLabel="name" class="w-full h-[40px] flex items-center" />
                </div>

                <!-- Assigned To -->
                <div class="w-full h-fit flex items-center gap-2">
                    <p class="font-medium w-[125px]">Assigned to:</p>
                    <Dropdown v-model="task.assigned" :options="project.members" optionLabel="name" class="w-full h-[40px] flex items-center" />
                </div>

                <!-- Due Date -->
                <div class="w-full h-fit flex items-center gap-2">
                    <p class="font-medium w-[125px]">Due Date:</p>
                    <Calendar showIcon v-model="task.due_date" dateFormat="yy-mm-dd" placeholder="Due Date" class="w-full hidden" />
                </div>

                <!-- Priority -->
                <div class="w-full h-fit flex items-center gap-2">
                    <p class="font-medium w-[125px]">Priority:</p>
                    <Dropdown v-model="task.priority" :options="project.priority" optionLabel="name" class="w-full h-[40px] flex items-center" >
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

                <!-- Progress -->
                <div class="w-full h-fit flex items-center gap-2">
                    <p class="font-medium w-[125px]">Progress:</p>
                    <Dropdown v-model="task.progress" :options="project.progress" optionLabel="name" class="w-full h-[40px] flex items-center" >
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

                <!-- Tags -->
                <div class="w-full h-fit flex items-center gap-2">
                    <p class="font-medium w-[125px]">Tags:</p>
                    <MultiSelect v-model="task.tags" :options="project.tags" optionLabel="name" class="w-[300px] h-[40px] text-[12px] flex items-center" >
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

            <div class="w-full h-full grid">
                <Editor v-model="task.desc" editorStyle="height: 550px" />
            </div>

            <div class="w-full h-[610px] relative">
                <p class="text-[16px] font-medium">Comments</p>
                <div class="w-full h-fit max-h-[400px] mt-6 text-[12px] grid gap-2 overflow-y-scroll ">
                    <div v-for="(comment, index) in comments" :key="index" class="w-full h-fit grid bg-custom-bg dark:bg-custom-dark-bg p-2 rounded-[4px]">
                        <div class="w-full h-fit flow-root">
                            <p class="text-custom-dark-blue dark:text-custom-light-gray font-medium float-left">{{ comment.user }}</p>
                            <p class="float-right font-light">{{ moment(comment.created_at).format("dddd, MMMM Do YYYY, h:mm a") }}</p>
                        </div>
                        
                        <div v-html="comment.desc"></div>
                    </div>
                </div>
                <div class="w-full h-fit grid gap-2 bottom-0 absolute">
                    <Editor v-model="new_comment" id="newComment" editorStyle="height: 75px" class="" >
                        <template v-slot:toolbar>
                            <span class="ql-formats flex items-center">
                                <button v-tooltip.bottom="'Bold'" class="ql-bold"></button>
                                <button v-tooltip.bottom="'Italic'" class="ql-italic"></button>
                                <button v-tooltip.bottom="'Underline'" class="ql-underline"></button>
                                <button v-tooltip.bottom="'Link'" class="ql-link"></button>
                                <button v-tooltip.bottom="'Code Block'" class="ql-code-block"></button>
                                <button v-tooltip.bottom="'Image'" class="ql-image"></button>
                                <button @click="tagUser = !tagUser" class="mt-[-3px] text-[#939489] font-semibold">@</button>
                            </span>
                            <MultiSelect v-if="tagUser" v-model="commentTags" display="chip" :options="project.members" optionLabel="name" class="w-[300px] h-[40px] text-[12px] flex items-center" />
                        </template>
                    </Editor>
                    <button @click="saveComment()" class="py-[3px] bg-custom-dark-blue dark:bg-custom-dark-bg border-[1px] border-transparent dark:border-custom-gray text-white font-medium rounded-full">Save Comment</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';

import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import MultiSelect from 'primevue/multiselect';
import Editor from 'primevue/editor';

export default {
    name: "Task View",
    data(){
        return{
            ready: false,
            name: "",
            task_id: "",
            project: {},
            task: {},
            new_comment: {},
            comments: [],
            tagUser: false,
            commentTags: []
        }
    },
    async mounted(){
        this.moment = moment
        this.$emit('loading')

        await axios.get('/api/user')
        .then(response => {
            this.name = response.data.name
        })
        
        this.task_id = this.$route.params.task

        await axios.get('/api/pipeline/task/'+this.task_id)
        .then(response => {
            this.task = response.data.task
            if(this.task.due_date){
                this.task.due_date = moment(this.task.due_date).format('YYYY-MM-DD')
            }

            this.comments = response.data.comments
        })

        await axios.get('/api/pipeline/project/'+this.task.project_id+'/details')
        .then(response => {
            this.project = response.data
        })

        this.project.sections.forEach(section => {
            if(section.id == this.task.section_id){
                this.task.section = section
            }
        })

        this.$emit('loading')
        this.ready = true
    },
    watch: {
        'task.section': async function(value){
            this.task.section_id = value.id
        }
    },
    methods: {
        async updateComplete(){
            this.ready = false
            this.$emit('loading')

            await axios.get('/api/pipeline/task/'+this.task_id+'/complete')
            .then(response => {
                this.task = response.data.task
                this.$toast.add({
                    severity: 'success',
                    summary: 'Task Update',
                    detail: response.data.message,
                    life: 2500
                })
            })

            this.project.sections.forEach(section => {
                if(section.id == this.task.section_id){
                    this.task.section = section
                }
            })

            this.ready = true
            this.$emit('loading')
        },
        async update(){
            this.ready = false
            this.$emit('loading')

            await axios.put('/api/pipeline/task/'+this.task_id, this.task)
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Task Update',
                    detail: "Task update successful.",
                    life: 2500
                })
                
                this.task = response.data.task
                if(this.task.due_date){
                    this.task.due_date = moment(this.task.due_date).format('YYYY-MM-DD')
                }
            })

            this.project.sections.forEach(section => {
                if(section.id == this.task.section_id){
                    this.task.section = section
                }
            })

            this.ready = true
            this.$emit('loading')
        },
        async saveComment(){
            if(Object.keys(this.new_comment).length == 0){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Task Comments',
                    detail: "Please enter a comment.",
                    life: 2500
                })
                return
            }

            this.ready = false
            this.$emit('loading')

            this.commentTags.forEach(user => {
                this.new_comment = this.new_comment+"<p style='color: #5080C7; font-weight: 600;'> @"+user.name+"</p>"
            })
            
            await axios.post('/api/pipeline/comment', {
                "task_id": this.task_id,
                "user": this.name,
                "desc": this.new_comment,
                "tags": this.commentTags,
                "task_name": this.task.name,
                "project_name": this.project.name
            })
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Task Comments',
                    detail: "Comment added successfully.",
                    life: 2500
                })

                this.new_comment = ""
                this.comments = response.data.comments
            })

            this.ready = true
            this.$emit('loading')
        }
    },
    components: {
        InputText,
        Dropdown,
        Calendar,
        MultiSelect,
        Editor
    }
}
</script>