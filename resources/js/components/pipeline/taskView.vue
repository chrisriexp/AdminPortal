<template>
    <div class="w-fit h-fit grid gap-6 p-10 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div v-if="ready" class="w-fit h-fit grid gap-6">
            <div class="w-full h-fit flow-root">
                <div class="w-fit h-full flex items-center gap-6 text-[32px] text-custom-black font-semibold float-left">
                    <p>Task View</p>

                    <!-- Completion Status -->
                    <div :class="task.completed ? 'bg-custom-green border-custom-green text-custom-green' : 'bg-custom-black border-custom-black text-custom-black'" class="w-fit h-fit flex items-center gap-2 px-4 py-[3px] text-[14px] bg-opacity-20 border-[1px] rounded-[4px]">
                        <CheckBadgeIcon class="h-[24px]" />
                        <p>{{ task.completed ? 'Task Completed' : 'Task Incomplete' }}</p>
                    </div>
                </div>

                <div class="w-fit h-full flex items-center gap-4 float-right">
                    <!-- Task Options -->
                    <div @click="taskToggle" aria-haspopup="true" aria-controls="task_menu" class="h-[39px] w-[39px] grid bg-white rounded-[2px] shadow-newdrop cursor-pointer"><EllipsisVerticalIcon class="h-[24px] m-auto text-custom-purple" /></div>
                    <Menu ref="task_menu" id="task_menu" :model="task_menu" :popup="true" class="w-fit" />

                    <!-- Close Create Task -->
                    <div @click="$emit('close')" class="h-[39px] w-[39px] grid bg-white rounded-[2px] shadow-newdrop cursor-pointer"><XMarkIcon class="h-[24px] m-auto text-custom-red" /></div>
                </div>
            </div>

            <!-- Task Information -->
            <div class="w-fit h-[500px] grid gap-6 overflow-y-scroll">
                <div class="w-fit h-fit grid grid-cols-2 gap-4">
                    <div class="w-[540px] h-fit grid gap-4">
                        <!-- Name -->
                        <InputText v-model="task.name" placeholder="Task Name *" class="min-h-[48px] h-fit w-full" />

                        <!-- Section -->
                        <Dropdown v-model="task.section" :options="project.sections" optionLabel="name" placeholder="Section" class="w-full h-[48px] flex items-center" >
                            <template #value="slotProps">
                                <div class="flex gap-2 items-center">
                                    <p class="flex items-center gap-2"><span class="text-[16px] opacity-80 text-custom-black  flex items-center gap-2"><FolderIcon class="h-[24px]" />Section:</span> {{ task.section_id ? slotProps.value.name : ""}}</p>
                                </div>
                            </template>
                        </Dropdown>

                        <!-- Assgined To -->
                        <Dropdown v-model="task.assigned" :options="project.members" optionLabel="name" placeholder="Assigned To" class="w-full h-[48px] flex items-center" >
                            <template #value="slotProps">
                                <div class="flex gap-2 items-center">
                                    <p class="flex items-center gap-2"><span class="text-[16px] opacity-80 text-custom-black  flex items-center gap-2"><FolderIcon class="h-[24px]" />Assigned To:</span> {{ Object.keys(task.assigned).length > 0 ? slotProps.value.name : ""}}</p>
                                </div>
                            </template>
                        </Dropdown>

                        <!-- Due Date -->
                        <Calendar showIcon v-model="task.due_date" dateFormat="yy-mm-dd" placeholder="Due Date" class="w-full h-[48px]" />

                        <!-- Priority -->
                        <Dropdown v-model="task.priority" :options="project.priority" optionLabel="name" class="w-full h-[48px] flex items-center" >
                            <template #value="slotProps">
                                <div class="flex gap-2 items-center">
                                    <p class="flex items-center gap-2"><span class="text-[16px] opacity-80 text-custom-black  flex items-center gap-2"><ExclamationTriangleIcon class="h-[24px]" />Priority:</span> <span :class="'bg-['+slotProps.value.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ slotProps.value.name }}</span></p>
                                </div>
                            </template>
                            <template #option="slotProps">
                                <div class="flex align-items-center">
                                    <div :class="'bg-['+slotProps.option.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ slotProps.option.name }}</div>
                                </div>
                            </template> 
                        </Dropdown>

                        <!-- Progress -->
                        <Dropdown v-model="task.progress" :options="project.progress" optionLabel="name" class="w-full h-[48px] flex items-center" >
                            <template #value="slotProps">
                                <div class="flex gap-2 items-center">
                                    <p class="flex items-center gap-2"><span class="text-[16px] opacity-80 text-custom-black  flex items-center gap-2"><MapPinIcon class="h-[24px]" />Progress:</span> <span :class="'bg-['+slotProps.value.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ slotProps.value.name }}</span></p>
                                </div>
                            </template>
                            <template #option="slotProps">
                                <div class="flex align-items-center">
                                    <div :class="'bg-['+slotProps.option.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ slotProps.option.name }}</div>
                                </div>
                            </template> 
                        </Dropdown>

                        <!-- Tags -->
                        <MultiSelect v-model="task.tags" :options="project.tags" optionLabel="name" placeholder="Projects" class="w-full h-[48px] flex items-center" >
                            <template #value="">
                                <div class="flex gap-2 items-center">
                                    <p class="flex items-center gap-2"><span class="text-[16px] opacity-80 text-custom-black  flex items-center gap-2"><TagIcon class="h-[24px]" />Tags</span></p>
                                </div>
                            </template>

                            <template #option="slotProps">
                                <div class="flex gap-2 items-center">
                                    <div :class="'bg-['+slotProps.option.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ slotProps.option.name }}</div>
                                </div>
                            </template>
                        </MultiSelect>

                        <div class="w-full h-fit flex items-center gap-4 flex-wrap">
                            <p v-for="(tag, index) in task.tags" :key="index" style="user-select: none;" :class="'bg-['+tag.color+']'" class="px-4 text-[14px] rounded-full text-white">{{ tag.name }}</p>
                        </div>
                    </div>

                    <!-- Task Body -->
                    <Editor v-model="task.desc" id="newTaskDesc" editorStyle="height: 408px" class="w-[540px]" />
                </div>

                <div class="w-full h-fit grid grid-cols-2 gap-4 mb-6">
                    <!-- File Uploads -->
                    <div class="w-full h-fit grid gap-2">
                        <div class="w-full h-fit flow-root">
                            <div class="w-fit h-fit flex items-center float-left">
                                <p class="text-[16px] text-custom-black font-medium">Files Uploaded</p>
                            </div>

                            <div class="w-[39px] h-[39px] grid float-right rounded-[2px] shadow-newdrop bg-white cursor-pointer">
                                <Icon icon="mingcute:file-upload-fill" height="24" class="m-auto" />
                            </div>
                        </div>

                        <!-- File List -->
                        <div class="w-full h-[160px] p-4 grid gap-4 overflow-y-scroll bg-white border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                            <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'125px'" :height="'125px'" :speed="0.75" class="m-auto" />
                        </div>
                    </div>

                    <!-- Notebooks Attached -->
                    <div class="w-full h-fit grid gap-2">
                        <div class="w-full h-fit flow-root">
                            <div class="w-fit h-fit flex items-center float-left">
                                <p class="text-[16px] text-custom-black font-medium">Notebooks Attached</p>
                            </div>

                            <div class="w-[39px] h-[39px] grid float-right rounded-[2px] shadow-newdrop bg-white cursor-pointer">
                                <Icon icon="eva:attach-fill" height="24" class="m-auto" />
                            </div>
                        </div>

                        <!-- Notebooks List -->
                        <div class="w-full h-[160px] p-4 grid gap-4 overflow-y-scroll bg-white border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                            <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'125px'" :height="'125px'" :speed="0.75" class="m-auto" />
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class=" w-full h-fit grid gap-4">
                    <p class="text-[16px] text-custom-black font-medium">Comments</p>

                    <div class="w-full h-fit grid gap-4">
                        <!-- Comments -->
                        <div v-for="(comment, index) in comments" :key="index" class="w-full h-fit p-2 flow root bg-white rounded-[2px] border-[1px] border-custom-black border-opacity-10">
                            <div class="w-[90%] h-fit grid float-left comment">
                                <div class="w-full h-fit flow-root">
                                    <!-- User Name -->
                                    <p v-if="comment.user != 'system'" class="text-[16px] text-custom-black font-medium opacity-80 float-left">{{ comment.user }}</p>
                                    <!-- System Comment -->
                                    <p v-else class="mb-2 text-[16px] text-white font-medium px-4 bg-custom-black rounded-full float-left">System</p>
                                    
                                    <!-- Comment Date -->
                                    <p class="text-[12px] text-custom-black opacity-60 float-right">{{ moment(comment.created_at).format("dddd, MMMM Do YYYY, h:mm a") }}</p>
                                </div>

                                <!-- Comment -->
                                <Editor readonly v-model="comment.desc" id="newTaskDesc" editorStyle="height: auto" :class="comment.user == 'system' ? 'italic' : ''" class="max-w-[920px]" >
                                    <template v-slot:toolbar>
                                        <span class="ql-formats flex items-center">
                                        </span>
                                        <MultiSelect v-if="tagUser" v-model="commentTags" :options="project.members" optionLabel="name" class="w-[300px] h-[25px] text-[12px] flex items-center" />
                                    </template>
                                </Editor>
                            </div>

                            <!-- Comment Menu -->
                            <div v-if="comment.user == name" @click="commentToggle($event, comment.id, index)" aria-haspopup="true" :aria-controls="comment.id" class="float-right w-[39px] h-[39px] grid bg-white rounded-[2px] shadow-newdrop cursor-pointer">
                                <EllipsisVerticalIcon class="h-[24px] m-auto text-custom-purple" />
                            </div>
                            <Menu v-if="comment.user == name" :ref="comment.id" :id="comment.id" :model="comment_menu" :popup="true" class="w-[200px]" />
                        </div>
                    </div>

                    <div class="w-full h-fit flow-root relative">
                        <div class="w-3/4 h-fit flex float-left newcomment relative">
                            <!-- New Comment -->
                            <Editor v-model="new_comment" id="newTaskDesc" :class="showCommentToolBar ? '' : 'hide-toolbar'" class="w-[90%]" >
                                <template v-slot:toolbar>
                                    <span class="ql-formats flex items-center h-[30px]">
                                        <button v-tooltip.bottom="'Bold'" class="ql-bold"></button>
                                        <button v-tooltip.bottom="'Italic'" class="ql-italic"></button>
                                        <button v-tooltip.bottom="'Underline'" class="ql-underline"></button>
                                        <button v-tooltip.bottom="'Link'" class="ql-link"></button>
                                        <button v-tooltip.bottom="'Code Block'" class="ql-code-block"></button>
                                        <button v-tooltip.bottom="'Image'" class="ql-image"></button>
                                        <button @click="tagUser = !tagUser"><i class="pi pi-at text-[#909090] hover:text-custom-black"></i></button>
                                        <MultiSelect v-if="tagUser" v-model="commentTags" :options="project.members" optionLabel="name" class="w-[300px] h-[26px] text-[12px] flex items-center" />
                                    </span>
                                </template>
                            </Editor>
                            <div class="w-[48px] h-[48px] grid rounded-full bottom-0 right-10 absolute">
                                <Icon @click="showCommentToolBar = !showCommentToolBar" :icon="'streamline:interface-edit-write-1-edit-edition-form-pen-text-write'" height="24" class="text-custom-black m-auto cursor-pointer" />
                            </div>
                        </div>

                        <!-- Save Comment -->
                        <button @click="saveComment()" class="w-1/4 h-[48px] text-[16px] text-white font-semibold bg-custom-black rounded-[2px] bottom-0 absolute float-right">Save Comment</button>
                    </div>
                </div>
            </div>

            <!-- Update Task -->
            <div class="w-full h-fit grid gap-2">
                <button @click="update()" class="w-full h-[48px] text-white text-[16px] font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Update Task</button>
            </div>
        </div>

        <div v-else class="w-fit h-[600px] grid grid-cols-2 gap-4"><div class="w-[540px] h-full grid"></div><div class="w-[540px] h-full grid"></div></div>
    </div>
</template>

<script>
import moment from 'moment';

import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import MultiSelect from 'primevue/multiselect';
import Editor from 'primevue/editor';
import Menu from 'primevue/menu';
import { Icon } from '@iconify/vue';

import NotFoundAnimation from '../../../assets/newNotFound.json'

import { XMarkIcon, EllipsisVerticalIcon, FolderIcon, UserCircleIcon, ExclamationTriangleIcon, MapPinIcon, TagIcon, ArrowUpOnSquareStackIcon, CheckBadgeIcon } from '@heroicons/vue/24/solid'

export default {
    name: "Task View",
    data(){
        return{
            NotFoundAnimation,
            ready: false,
            showCommentToolBar: false,
            name: "",
            task_id: "",
            project: {},
            task: {},
            new_comment: {},
            comments: [],
            tagUser: false,
            commentTags: [],
            selected_comment: {id: "", index: null},
            comment_menu: [
                {
                    label: 'Delete',
                    icon: 'pi pi-trash',
                    command: async () => {
                        await axios.delete('/api/pipeline/comment/'+this.selected_comment.id)
                        .then(response => {
                            this.$toast.add({
                                severity: 'success',
                                summary: 'Comment',
                                detail: response.data.message,
                                life: 2500
                            })

                            this.comments.splice(this.selected_comment.index, 1)

                            this.selected_comment.id = ""
                            this.selected_comment.index = null
                        })
                    }
                }
            ],
            task_menu: [
                {
                    label:() => {
                        const value = this.task.completed ? "Mark Incomplete" : "Mark Completed"
                        return value
                    },
                    icon: 'pi pi-check-circle',
                    command: async () => {
                        this.updateComplete()
                    }
                },
                {
                    label: 'Delete',
                    icon: 'pi pi-trash',
                    command: async () => {
                        this.ready = false
                        this.$emit('loading')

                        await axios.delete('/api/pipeline/task/'+this.task_id)
                        .then(response => {
                            this.$toast.add({
                                severity: 'success',
                                summary: 'Task',
                                detail: response.data.message,
                                life: 2500
                            })
                            
                            this.$emit('loading')
                            this.$emit('close')
                        })
                    }
                }
            ]
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

            this.comments = response.data.comments.reverse()
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
        },
        'task.assigned': async function(value){
            
        }
    },
    methods: {
        taskToggle(event){
            this.$refs['task_menu'].toggle(event);
        },
        commentToggle(event, id, index){
            this.selected_comment.id = id
            this.selected_comment.index = index
            this.$refs[id][0].toggle(event);
        },
        async updateComplete(){
            this.ready = false
            this.$emit('loading')

            await axios.get('/api/pipeline/task/'+this.task_id+'/complete')
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Task Update',
                    detail: response.data.message,
                    life: 2500
                })
            })

            await axios.get('/api/pipeline/task/'+this.task_id)
            .then(response => {
                this.task = response.data.task
                if(this.task.due_date){
                    this.task.due_date = moment(this.task.due_date).format('YYYY-MM-DD')
                }

                this.comments = response.data.comments.reverse()
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

            this.$emit('loading')

            this.commentTags.forEach(user => {
                this.new_comment = this.new_comment+'<br/><p style="color: #a778e5; font-weight: 600;"> @'+user.name+"</p>"
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
                this.comments = response.data.comments.reverse()
                this.commentTags = []
            })

            this.$emit('loading')
        }
    },
    components: {
        InputText,
        Dropdown,
        Calendar,
        MultiSelect,
        Editor,
        XMarkIcon,
        EllipsisVerticalIcon,
        FolderIcon,
        UserCircleIcon,
        ExclamationTriangleIcon,
        MapPinIcon,
        TagIcon,
        Menu,
        ArrowUpOnSquareStackIcon,
        Icon,
        CheckBadgeIcon,
        Icon
    }
}
</script>

<style scoped>
:deep( .newcomment .hide-toolbar .ql-toolbar){
    display: none;
}
:deep( .newcomment .p-editor-container .p-editor-content.ql-snow ){
    border: 1px solid #21212110 !important;
    min-height: 48px !important;
    max-height: 80px !important;
}

:deep( .comment .p-editor-toolbar ){
    display: none !important;
}
:deep( .comment .p-editor-container .p-editor-content .ql-editor ){
    background-color: white !important;
}
:deep( .comment .p-editor-container .p-editor-content.ql-snow ){
    border: none !important;
}

:deep( .comment .ql-editor ){
    padding: 0px !important;
}

</style>
