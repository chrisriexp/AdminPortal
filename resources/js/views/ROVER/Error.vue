<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Top Nav -->
    <div class="absolute z-30 w-full">
        <topNav class="fixed z-30" :active="'modules'" />
    </div>

    <!-- Delete Comment -->
    <div v-if="showDeleteComment" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <deleteCommentPopup class="m-auto" @close="showDeleteComment = false; commentID = null; commentIndex = null;" @delete="deleteComment" />
    </div>

    <!-- Ready for Updates -->
    <div v-if="showReadyForUpdates" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <readyForUpdatesPopup class="m-auto" @close="readyForUpdatesPopup = false" />
    </div>

    <div v-if="ready" class="w-full h-screen z-20 absolute">
        <!-- Sidebar -->
        <div class="w-[305px] h-full grid bg-sidebar-bg z-20 absolute">
            <div class="w-full max-h-[800px] h-fit grid gap-4 mt-[100px] px-6 overflow-y-scroll text-[16px]">
                <div class="w-fit h-fit m-auto grid gap-4">
                    <p class="text-[24px] text-custom-black font-medium">Status</p>
                    <!-- Status Image -->
                    <img :src="'/images/'+error.status+'.png'" alt="Progress - Debug">
                </div>
            </div>
        </div>

        <div class="w-full h-full z-10 absolute pt-[110px] pb-12 pl-[350px] pr-[50px] overflow-y-scroll">
            <div class="w-full h-fit grid gap-4">
                <!-- Breadcrumb Menu -->
                <div class="w-fit h-fit flex items-center gap-6">
                    <a href="/rover/errors" class="w-[24px] h-[24px] grid bg-white rounded-[2px] shadow-newdrop">
                        <Icon :icon="'material-symbols:arrow-back-rounded'" height="18" class="text-custom-purple m-auto" />
                    </a>

                    <p class="text-[16px] text-custom-black font-medium">ROVER / <span class="text-custom-purple">{{ app_id }}</span></p>
                </div>

                <div class="w-full h-fit flow-root">
                    <div class="w-fit h-full flex items-center float-left">
                        <!-- Application ID and system -->
                        <p class="text-[24px] text-custom-black font-semibold">{{ app_id }} - {{ app_id.substring(0, 3) == 'RFA' ? 'RocketAutomation' : 'RocketMGA' }}</p>
                    </div>

                    <div class="w-fit h-full flex items-center gap-8 float-right">
                        <!-- Error Created Time -->
                        <div class="w-fit h-fit grid text-[16px] text-custom-black">
                            <p class="font-medium opacity-60">Created:</p>
                            <p>{{ moment(error.created_at).format('MMM Do YYYY h:mm a') }}</p>
                        </div>

                        <!-- Error Created Time -->
                        <div v-if="error.fixed" class="w-fit h-fit grid text-[16px] text-custom-black">
                            <p class="font-medium opacity-60">Fixed:</p>
                            <p>{{ moment(error.fixed_date).format('MMM Do YYYY h:mm a') }}</p>
                        </div>
                    </div>
                </div>

                <div class="w-full h-full flow-root">
                    <div class="w-fit max-w-[60%] h-full flex flex-wrap items-center gap-4 text-[16px] text-white font-medium float-left">
                        <!-- Rater -->
                        <p v-if="error.product == 'HOME'" class="px-[10px] bg-custom-black rounded-full">{{ raters[error.carrier.substring(0, 2)].name }}</p>
                        
                        <!-- Carrier -->
                        <p class="px-[10px] bg-custom-black rounded-full">{{ error.product == 'FLOOD' ? carriers[error.carrier].name : carriers[error.carrier.substring(3)].name }}</p>
                    
                        <!-- Product -->
                        <p class="px-[10px] bg-custom-black rounded-full">{{ error.product == 'FLOOD' ? 'Flood' : 'Home' }}</p>

                        <!-- Type -->
                        <p class="px-[10px] bg-custom-black rounded-full">{{ error.type }}</p>
                    </div>

                    <div class="w-[30%] h-full flex items-center float-right">
                        <!-- Assigned to -->
                        <Dropdown v-model="error.assigned" @change="updateAssigned" :options="users" optionLabel="name" class="w-full h-[48px] flex items-center" >
                            <template #value="slotProps">
                                <div class="grid">
                                    <p class="truncate flex items-center gap-2">Assigned to: {{ slotProps.value.name }}</p>
                                </div>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <!-- Error Body -->
                <div class="w-full h-fit grid grid-cols-3 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
                    <div class="w-full h-full col grid bg-sidebar-bg border-r-[1px] border-custom-black border-opacity-10">
                        <div class="w-full h-fit grid">
                            <!-- Errors Fixed -->
                            <p class="text-[24px] text-custom-black font-medium p-6">Errors Fixed {{ fixed }}/<span class="text-custom-red">{{ errors.length }}</span></p>

                            <!-- Error List -->
                            <div v-for="(error, index) in errors" :key="index" @click="$router.replace({params: {carrier: error.carrier}})" :class="error.carrier == $route.params.carrier ? 'bg-white shadow-purple-drop' : ''" class="w-full h-fit py-4 px-6 cursor-pointer rounded-[2px]">
                                <div class="w-full h-full flow-root">
                                    <div class="w-fit max-w-[50%] h-full flex items-center float-left">
                                        <div class="w-full h-fit grid"><p class="truncate text-custom-black text-[16px]">{{ error.product == 'FLOOD' ? carriers[error.carrier].name : carriers[error.carrier.substring(3)].name }}</p></div>
                                    </div>

                                    <div class="w-fit max-w-[40%] h-full flex items-center float-right">
                                        <div class="w-full h-fit grid"><p class="truncate text-custom-red text-[12px]">{{ statusKey[error.status] }}</p></div>
                                    </div>
                                </div>

                                <div class="w-full h-fit grid text-[14px] text-custom-black opacity-60">
                                    <p class="truncate">{{ error.desc }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Information -->
                    <div class="w-full h-fit grid gap-4 col-span-2 p-6">
                        <div class="w-full h-full flow-root">
                            <div class="w-fit h-full flex items-center float-left">
                                <p class="text-[24px] text-custom-black font-medium">Error Description and Notes</p>
                            </div>

                            <!-- Update Stage Button -->
                            <div class="w-fit h-full flex items-center float-right">
                                <button class="py-2 px-6 rounded-[2px] text-[16px] text-white font-semibold bg-custom-black">Ready for Updates</button>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="w-fit h-fit flex items-center gap-4">
                            <!-- Agent Portal Link -->
                            <a :href="'https://agent.rocketflood.com/agent-portal/application-details/'+app_id" target="_blank" class="w-[32px] h-[32px] grid bg-white shadow-newdrop rounded-[2px]">
                                <img src="../../../assets/purpleLogo64.png" alt="Rocket Logo Purple" class="h-[24px] w-[24px] m-auto">
                            </a>

                            <!-- Debugging URL -->
                            <a :href="(carriers[(error.product == 'HOME' ? error.carrier.substring(3): error.carrier)].backend[error.source])+app_id" target="_blank" class="w-[32px] h-[32px] grid bg-white shadow-newdrop rounded-[2px]">
                                <Icon :icon="'codicon:debug-line-by-line'" height="24" class="text-custom-purple m-auto" />
                            </a>
                        </div>

                        <div class="w-full h-fit grid grid-cols-3 gap-4">
                            <!-- Error Description -->
                            <div class="w-full h-fit col-span-2 grid comment">
                                <p class="text-[16px] text-custom-black font-medium">Error Description</p>
                                <Editor readonly v-model="error.desc" id="newTaskDesc" editorStyle="height: auto; font-size: 16px;" class="w-full p-2 text-[16px] opacity-60 bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]" >
                                    <template v-slot:toolbar>
                                        <span class="ql-formats flex items-center">
                                        </span>
                                    </template>
                                </Editor>
                            </div>

                            <!-- Error Description -->
                            <div class="w-full h-fit max-h-[127px] grid overflow-y-scroll">
                                <p class="text-[16px] text-custom-black font-medium">Attachments</p>
                                <div v-if="error.uploads.length == 0" class=" w-full h-full grid">
                                    <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'100px'" :height="'100px'" :speed="0.75" class="m-auto" />
                                </div>
                                <a v-else v-for="(upload, index) in error.uploads" :key="index" :href="'https://rover.rocketflood.com/'+upload.path" target="_blank" class="truncate text-custom-purple underline">{{ upload.name }}</a>
                            </div>
                        </div>

                        <hr>

                        <!-- Comments -->
                        <p class="text-[16px] text-custom-black font-medium">Comments</p>

                        <!-- New Comment -->
                        <div class="w-full h-fit flow-root relative pb-2">
                            <div class="w-3/4 h-fit flex float-left newcomment relative">
                                <!-- New Comment -->
                                <Editor v-model="new_comment" id="newTaskDesc" :class="showCommentToolBar ? '' : 'hide-toolbar'" editorStyle="height: auto; font-size: 16px;" class="w-[90%]" >
                                    <template v-slot:toolbar>
                                        <span class="ql-formats flex items-center h-[30px]">
                                            <button v-tooltip.bottom="'Bold'" class="ql-bold"></button>
                                            <button v-tooltip.bottom="'Italic'" class="ql-italic"></button>
                                            <button v-tooltip.bottom="'Underline'" class="ql-underline"></button>
                                            <button v-tooltip.bottom="'Image'" class="ql-list" value="ordered"></button>
                                            <button v-tooltip.bottom="'Image'" class="ql-list" value="bullet"></button>
                                            <button v-tooltip.bottom="'Link'" class="ql-link"></button>
                                            <button v-tooltip.bottom="'Code Block'" class="ql-code-block"></button>
                                            <button v-tooltip.bottom="'Image'" class="ql-image"></button>
                                            <!-- <button @click="tagUser = !tagUser"><i class="pi pi-at text-[#909090] hover:text-custom-black"></i></button> -->
                                            <!-- <MultiSelect v-if="tagUser" v-model="commentTags" :options="project.members" optionLabel="name" class="w-[300px] h-[26px] text-[12px] flex items-center" /> -->
                                        </span>
                                    </template>
                                </Editor>
                                <div class="w-[48px] h-[48px] grid rounded-full bottom-0 right-4 absolute">
                                    <Icon @click="showCommentToolBar = !showCommentToolBar" :icon="'streamline:interface-edit-write-1-edit-edition-form-pen-text-write'" height="24" class="text-custom-black m-auto cursor-pointer" />
                                </div>
                            </div>

                            <!-- Save Comment -->
                            <button @click="addComment()" class="w-1/5 h-[48px] text-[16px] text-white font-semibold bg-custom-black rounded-[2px] absolute bottom-2 right-0">Save Comment</button>
                        </div>

                        <div class="w-full h-fit grid gap-4 comment">
                            <!-- Existing Comments -->
                            <div v-if="error.comments == 0" class=" w-full h-full grid">
                                <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'200px'" :height="'200px'" :speed="0.75" class="m-auto" />
                            </div>
                            <div v-else v-for="(comment, index) in error.comments" :key="index" class="w-full h-fit p-2 flow root bg-white rounded-[2px] border-[1px] border-custom-black border-opacity-10">
                                <div :class="comment.user == user ? 'w-[90%]' : 'w-full'" class="h-fit grid float-left comment">
                                    <div class="w-full h-fit flow-root">
                                        <!-- User Name -->
                                        <p v-if="comment.user != 'System'" class="text-[16px] text-custom-black font-medium opacity-80 float-left">{{ comment.user }}</p>
                                        <!-- System Comment -->
                                        <p v-else class="mb-2 text-[16px] text-white font-medium px-4 bg-custom-black rounded-full float-left">System</p>
                                        
                                        <!-- Comment Date -->
                                        <p class="text-[12px] text-custom-black opacity-60 float-right">{{ moment(comment.created_at).format("dddd, MMMM Do YYYY, h:mm a") }}</p>
                                    </div>

                                    <!-- Comment -->
                                    <Editor readonly v-model="comment.note" id="newTaskDesc" editorStyle="height: auto; font-size: 14px;" :class="comment.user == 'system' ? 'italic' : ''" class="max-w-[920px] text-[14px] opacity-60 text-custom-black" >
                                        <template v-slot:toolbar>
                                            <span class="ql-formats flex items-center">
                                            </span>
                                        </template>
                                    </Editor>
                                </div>

                                <!-- Comment Menu -->
                                <div v-if="comment.user == user" @click="showDeleteComment = true; commentID = comment.id; commentIndex = index" aria-haspopup="true" :aria-controls="comment.id" class="float-right w-[39px] h-[39px] grid bg-white rounded-[2px] shadow-newdrop cursor-pointer">
                                    <Icon :icon="'fluent:delete-20-filled'" height="24" class="text-custom-red m-auto" />
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
import topNav from '../../components/topNav.vue'
import loading from '../../components/loading.vue'
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Editor from 'primevue/editor';

import { Icon } from '@iconify/vue';

import NotFoundAnimation from '../../../assets/newNotFound.json'
import carriers from '../../../assets/rover_carriers.json'
import raters from '../../../assets/raters.json'
import deleteCommentPopup from '../../components/rover/deleteComment.vue'
import readyForUpdatesPopup from '../../components/rover/readyForUpdates.vue'

import moment from 'moment';

export default {
    name: "ROVER - Error",
    data(){
        return {
            NotFoundAnimation,
            raters,
            carriers,
            loading: true,
            ready: false,
            showDeleteComment: false,
            commentIndex: null,
            commentID: null,
            user: "",
            app_id: "",
            errors: [],
            error: {},
            fixed: 0,
            statusKey: {
                debug: "Debugging",
                update: "Updates in Progress",
                test: "Testing in Progress",
                digiprompt: "DigiPrompt Queue",
                fixed: "Fixed"
            },
            users: [],
            showCommentToolBar: false,
            new_comment: {}
        }
    },
    async created(){
        this.moment = moment
        this.app_id = this.$route.params.app_id

        await axios.get('/api/rover/app/'+this.app_id)
        .then(response => {
            this.user = response.data.user
            this.errors = response.data.errors
            this.fixed = response.data.fixed
            this.users = response.data.users
        })

        await axios.get('/api/rover/error/'+this.app_id+'/'+this.$route.params.carrier)
        .then(response => {
            this.error = response.data.error

            if(this.error.fixed == 0){
                this.error.fixed = false
            }else if(this.error.fixed == 1){
                this.error.fixed = true
            }
        })
        
        this.ready = true
        this.loading = false
    },
    watch: {
        '$route.params.carrier': async function(value){
            this.loading = true

            await axios.get('/api/rover/error/'+this.app_id+'/'+value)
            .then(response => {
                this.error = response.data.error

                if(this.error.fixed == 0){
                    this.error.fixed = false
                }else if(this.error.fixed == 1){
                    this.error.fixed = true
                }
            })

            this.loading = false
        }
    },
    methods: {
        async addComment(){
            this.loading = true 

            if(Object.keys(this.new_comment).length == 0){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Comment Validation',
                    detail: "Please enter a comment.",
                    life: 2500
                })

                this.loading = false 
                return
            }

            await axios.post('/api/rover/comment', {
                "comment": {
                    "note": this.new_comment,
                    "app_id": this.app_id,
                    "carrier": this.$route.params.carrier
                }
            })
            .then(response => {
                this.new_comment = {}
                this.error.comments = response.data.comments
                this.$toast.add({
                    severity: 'success',
                    summary: 'Comment',
                    detail: response.data.message,
                    life: 2500
                })
            })

            this.loading = false 
        },
        async deleteComment(){
            this.showDeleteComment = false
            this.loading = true

            await axios.delete('/api/rover/comment/'+this.commentID)
            .then(response => {
                this.error.comments.splice(this.commentIndex, 1)
                
                this.commentID = null
                this.commentIndex = null
                this.$toast.add({
                    severity: 'success',
                    summary: 'Comment',
                    detail: response.data.message,
                    life: 2500
                })
            })

            this.loading = false 
        },
        async updateAssigned(){
            this.loading = true 

            await axios.put('/api/rover/assigned/'+this.error.id, {
                "user": this.error.assigned,
                "error": {
                    "app": this.app_id,
                    "carrier": this.error.carrier,
                    "carrier_name": this.carriers[(this.error.product == 'HOME' ? this.error.carrier.substring(3) : this.error.carrier)].name
                }
            })
            .then(response => {
                this.error.comments = response.data.comments
                this.$toast.add({
                    severity: 'success',
                    summary: 'Error Update',
                    detail: response.data.message,
                    life: 2500
                })
            })

            this.loading = false 
        }
    },
    components: {
        topNav,
        loading,
        Dropdown,
        InputText,
        Icon,
        Editor,
        deleteCommentPopup,
        readyForUpdatesPopup
    }
}
</script>

<style scoped>
:deep( .newcomment .hide-toolbar .ql-toolbar){
    display: none;
}
:deep( .newcomment .p-editor-container .p-editor-content.ql-snow ){
    border: 1px solid #21212110 !important;
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