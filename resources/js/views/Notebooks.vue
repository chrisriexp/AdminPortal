<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <topNav :active="'notebooks'" @loading="loading = !loading" class="fixed" />

    <!-- Create folder -->
    <div v-if="createFolder" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <div class="w-fit h-fit m-auto px-8 py-6 grid gap-4 justify-items-center bg-white dark:bg-custom-dark-bg border-[1px] border-custom-dark-blue dark:border-custom-gray shadow-newdrop rounded-[4px]">
            <p class="text-[18px] text-custom-gray dark:text-white text-center">Enter name below to create a new folder.</p>
            <InputText v-model="newFolderName" type="text" placeholder="Folder Name" class="w-full h-[35px] dark:bg-custom-light-gray-bg rounded-[4px]" />

            <div class="w-full grid grid-cols-2 gap-4">
                <button @click="function(){if (newFolderName == ''){$toast.add({severity: 'warn',summary:'Folder Name',detail:'Please enter a name to create folder.',life:2500})}else{newFolder()}}" class="text-center w-full gap-2 py-[3px] text-[14px] text-white font-medium bg-custom-dark-blue dark:bg-custom-gray dark:bg-opacity-50 rounded-[2px]">Create Folder</button>
                <button @click="()=>{createFolder = false; newFolderName = ''}" class="text-center w-full gap-2 py-[3px] text-[14px] text-custom-gray dark:text-custom-bg font-medium rounded-[2px] hover:bg-custom-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-20">Dismiss</button>
            </div>
        </div>
    </div>
    
    <!-- Share Notebook -->
    <div v-if="shareNotebook" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <div class="w-fit h-fit m-auto px-8 py-6 grid gap-4 justify-items-center bg-white dark:bg-custom-dark-bg border-[1px] border-custom-dark-blue dark:border-custom-gray shadow-newdrop rounded-[4px]">
            <p class="text-[18px] text-custom-gray dark:text-white text-center">Select users to share this notebook with.</p>
            <MultiSelect v-model="notes[selectedNote].shared" display="chip" :options="members" optionLabel="name" class="w-[400px] h-[40px] text-[12px] flex items-center" />

            <div class="w-full grid grid-cols-2 gap-4">
                <button @click="updateShare()" class="text-center w-full gap-2 py-[3px] text-[14px] text-white font-medium bg-custom-dark-blue dark:bg-custom-gray dark:bg-opacity-50 rounded-[2px]">Update Share</button>
                <button @click="shareNotebook = false" class="text-center w-full gap-2 py-[3px] text-[14px] text-custom-gray dark:text-custom-bg font-medium rounded-[2px] hover:bg-custom-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-20">Dismiss</button>
            </div>
        </div>
    </div>
    
    <!-- Quick Menu -->
    <div class="w-full h-fit flow-root px-24 py-2 bg-white dark:bg-custom-light-gray-bg">
        <div class="h-full w-fit flex items-center gap-2 float-left text-custom-dark-blue dark:text-white">
            <!-- Create Button -->
            <button @click="createToggle" aria-haspopup="true" aria-controls="create_menu" class="h-fit w-fit flex gap-2 items-center px-2 py-[5px] text-[14px] bg-custom-dark-blue dark:bg-custom-dark-bg text-white font-medium border-[1px] border-custom-dark-blue dark:border-custom-gray rounded-[5px]">Create <i class="pi pi-plus"></i></button>
            <Menu ref="create_menu" id="create_menu" :model="create_items" :popup="true" class="w-[200px]" />

            <!-- Move to -->
            <button v-if="notes.length > 0 && selectedFolder != 'shared'" @click="moveToggle" aria-haspopup="true" aria-controls="move_menumove_menu" class="h-fit w-fit flex gap-2 items-center px-2 py-[5px] text-[14px] font-medium border-[1px] border-transparent hover:bg-custom-bg hover:border-custom-light dark:hover:bg-opacity-20 dark:hover:bg-custom-gray dark:hover:border-custom-dark-bg rounded-[5px]">Move to <i class="pi pi-folder-open"></i></button>
            <Menu ref="move_menu" id="move_menu" :model="folder_items" :popup="true" class="w-fit" />

            <!-- Share -->
            <button @click="shareNotebook = true" v-if="notes.length > 0 && selectedFolder != 'shared'" class="h-fit w-fit flex gap-2 items-center px-2 py-[5px] text-[14px] font-medium hover:bg-custom-bg border-[1px] border-transparent hover:border-custom-light dark:hover:bg-custom-gray dark:hover:bg-opacity-20 dark:hover:border-custom-dark-bg rounded-[5px]">Share <i class="pi pi-share-alt"></i></button>
            
            <!-- Tags -->
            <div v-if="notes.length > 0 && selectedFolder != 'shared'" class="w-fit h-fit flex items-center gap-2">
                <p class="text-[14px] font-medium flex items-center gap-2">Tags <i class="pi pi-tag"></i></p>
                <MultiSelect v-model="notes[selectedNote].tags" :options="tags" optionLabel="name" class="w-[200px] h-[30px] text-[12px] flex items-center" >
                    <template #option="slotProps">
                        <div class="flex gap-2 items-center">
                            <div :class="'bg-['+slotProps.option.color+']'" class="px-4 rounded-full text-white">{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
            </div>

            <!-- Favorites -->
            <button v-if="notes.length > 0 && selectedFolder != 'shared'" @click="saveNote(true, false)" class="h-fit w-fit flex gap-2 items-center px-2 py-[5px] text-[14px] font-medium border-[1px] border-transparent rounded-[5px]"><i class="pi pi-star-fill text-custom-orange"></i> {{ notes[selectedNote].favorite ? 'Remove from Favorites' : 'Add to Favorites' }}</button>
        
            <!-- Important -->
            <button v-if="notes.length > 0 && selectedFolder != 'shared'" @click="saveNote(false, true)" class="h-fit w-fit flex gap-2 items-center px-2 py-[5px] text-[14px] font-medium border-[1px] border-transparent rounded-[5px]"><i class="pi pi-flag-fill text-custom-red dark:text-red-500"></i> {{ notes[selectedNote].important ? 'Undo Important' : 'Mark Important' }}</button>
        </div>

        <div v-if="notes.length > 0" class="h-full w-fit flex items-center gap-2 float-right text-custom-dark-blue dark:text-white">
            <!-- Remove Folder -->
            <button v-if="selectedFolder != 'shared'" @click="()=>{notes[selectedNote].folder = 'notebooks'; saveNote()}" class="h-fit w-fit flex gap-2 items-center px-2 py-[5px] text-[14px] text-custom-gray dark:text-white font-medium border-[1px] border-transparent rounded-[5px]"><i class="pi pi-history"></i>Reset Note Folder</button>
            
            <!-- Save Note -->
            <button @click="saveNote(false, false)" class="h-fit w-fit flex gap-2 items-center px-2 py-[5px] text-[14px] font-medium hover:bg-custom-bg dark:hover:bg-transparent hover:text-custom-blue border-[1px] border-transparent hover:border-custom-blue rounded-[5px]"><i class="pi pi-save"></i>Save Note</button>

            <!-- Delete Note -->
            <button v-if="selectedFolder != 'shared'" @click="deleteNote()" class="h-fit w-fit flex gap-2 items-center px-2 py-[5px] text-[14px] font-medium hover:bg-custom-bg dark:hover:bg-transparent hover:text-custom-red dark:hover:text-red-500 border-[1px] border-transparent hover:border-custom-red dark:hover:border-red-500 rounded-[5px]"><i class="pi pi-trash"></i>Delete Note</button>
        </div>
    </div>

    <div v-if="ready" class="w-full h-full grid gap-8 grid-cols-4 p-6">
        <!-- Folders -->
        <div class="w-full bg-white dark:bg-custom-light-gray-bg h-screen m-[-25px] grid">
            <ul class="grid gap-2 h-fit text-[16px] text-custom-gray dark:text-white font-medium p-4">
                <li @click="selectFolder('favorites')" :class="selectedFolder == 'favorites' ? 'bg-custom-bg dark:bg-custom-gray dark:bg-opacity-50' : ''" class="cursor-pointer w-full p-2 hover:bg-custom-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-50 rounded-[4px]"><i class="pi pi-star"></i> Favorites</li>
                <li @click="selectFolder('shared')" :class="selectedFolder == 'shared' ? 'bg-custom-bg dark:bg-custom-gray dark:bg-opacity-50' : ''" class="cursor-pointer w-full p-2 hover:bg-custom-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-50 rounded-[4px]"><i class="pi pi-users"></i> Shared With Me</li>
                <li @click="selectFolder('notebooks')" :class="selectedFolder == 'notebooks' ? 'bg-custom-bg dark:bg-custom-gray dark:bg-opacity-50' : ''" class="cursor-pointer w-full p-2 hover:bg-custom-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-50 rounded-[4px]"><i class="pi pi-inbox"></i> Notebooks</li>
                <ul v-if="folders.length > 0" class="ml-12 grid gap-2 h-fit max-h-[600px] font-normal overflow-y-scroll">
                    <li @click="selectFolder(folder)" v-for="(folder, index) in folders" :key="index" :class="selectedFolder == folder ? 'bg-custom-bg dark:bg-custom-gray dark:bg-opacity-50' : ''" class="truncate cursor-pointer w-full p-2 hover:bg-custom-bg dark:hover:bg-custom-gray dark:hover:bg-opacity-50 rounded-[4px]"><i class="pi pi-folder"></i> {{ folder }}</li>
                </ul>
            </ul>
        </div>

        <!-- No Notes Created Yet -->
        <div v-if="notes.length == 0" class="w-full h-fit grid col-span-3 py-12">
            <div class="mx-auto grid w-fit h-fit px-8 py-4 bg-white dark:bg-custom-dark-bg rounded-[4px] border-[1px] border-custom-light dark:border-custom-gray">
                <p class="text-[24px] text-custom-dark-blue dark:text-white text-center font-semibold">No Notebooks Found..</p>
                <p class="text-[18px] text-custom-gray dark:text-custom-bg">Please create a new notebook to view more information</p>
                <LottieAnimation :animationData="NotFoundAnimation" :width="'250px'" :height="'250px'" :speed="1" class="m-auto" />
            </div>
        </div>

        <!-- Note List -->
        <div v-else class="w-full h-fit grid bg-white dark:bg-custom-dark-bg rounded-[4px] border-[1px] border-custom-light dark:border-custom-gray">
            <!-- Filter -->
            <div class="w-full h-[50px] flow-root">
                <div @click="updateOrder" class="w-fit h-fit flex gap-2 items-center p-4 text-[14px] text-custom-dark-blue dark:text-white font-medium cursor-pointer float-left" style="user-select: none;">
                    <p>Order By: </p>
                    <i v-if="descOrder" class="pi pi-sort-alpha-down"></i>
                    <i v-else class="pi pi-sort-alpha-up-alt"></i>
                </div>

                <div class="h-fit float-right ">
                    <p @click="filterToggle" aria-haspopup="true" aria-controls="filter_menu" class="w-fit h-full flex items-center gap-2 text-[14px] text-custom-dark-blue dark:text-white font-medium float-right p-4 cursor-pointer" style="user-select: none;"><FunnelIcon class="h-[20px]" />Filter: {{ filter == 'important' ? 'Important' : (filter == 'title' ? 'Title' : (filter == 'created_at' ? 'Created Date' : 'Updated Date')) }}</p>
                    <Menu ref="filter_menu" id="filter_menu" :model="filter_items" :popup="true" class="w-[200px]" />
                </div>
            </div>

            <div id="notesList" class="w-full h-fit max-h-[700px] grid overflow-y-scroll">
                <!-- List of Notes -->
                <div @click="selectedNote = index" v-for="(note, index) in notes" :key="index" :class="[index == selectedNote ? 'bg-custom-bg dark:bg-custom-gray dark:bg-opacity-50' : 'bg-white dark:bg-custom-gray-bg', selectedFolder != 'shared' && note.favorite ? 'inner-border-custom-orange inner-border-[1px]' : '']" class="w-full h-fit grid p-4 text-custom-light-gray dark:text-white text-[14px] border-b-[1px] border-t-[1px] border-custom-light dark:border-custom-gray cursor-pointer">
                    <!-- Note Title -->
                    <p class="h-fit flex items-center gap-2 truncate text-custom-dark-blue dark:text-white font-medium"><i v-if="selectedFolder != 'shared' && note.important" class="pi pi-flag-fill text-custom-red dark:text-red-500"></i>{{ note.title }}</p>
                    
                    <div class="w-full h-fit grid grid-cols-2">
                        <!-- Note Body -->
                        <p class="truncate">{{ note.body ? note.body.replace(/(<([^>]+)>)/ig, "") : '' }}</p>
                        <!-- Note Update Date -->
                        <div class="w-full flow-root">
                            <p class="float-right text-[12px]">{{ moment(note.updated_at).format("MMMM Do YYYY, h:mm a") }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Note Details -->
        <div v-if="notes.length > 0" class="w-full h-fit grid gap-2 col-span-2 p-4 bg-white dark:bg-custom-dark-bg rounded-[4px] border-[1px] border-custom-light dark:border-custom-gray text-[16px]">
            <div class="w-full h-fit grid my-2">
                <!-- Note Details -->
                <p class="text-custom-light-gray font-medium">Notebook Details</p>
                <div class="w-full grid grid-cols-2 text-[14px] text-custom-gray dark:text-white">
                    <div class="w-full h-fit grid mt-2">
                        <p><span class="font-medium">Updated at:</span> {{ moment(notes[selectedNote].updated_at).format("dddd, MMMM Do YYYY, h:mm a") }}</p>
                        <p><span class="font-medium">Created at:</span> {{ moment(notes[selectedNote].created_at).format("dddd, MMMM Do YYYY, h:mm a") }}</p>
                    </div>

                    <div class="w-full h-fit grid mt-2 font-medium">
                        <p v-if="selectedFolder != 'shared'" class="flex gap-2 items-center"><i class="pi pi-folder"></i>{{ notes[selectedNote].folder == 'notebooks' ? 'No Folder' : notes[selectedNote].folder }}</p>
                        <div class="w-full h-fit flex items-center gap-2">
                            <p v-if="selectedFolder != 'shared' && notes[selectedNote].favorite" class="text-white bg-custom-orange px-2 rounded-full">Favorites</p>
                            <p v-if="selectedFolder != 'shared' && notes[selectedNote].important" class="text-white bg-custom-red dark:bg-red-500 px-2 rounded-full">Important</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notebook Tags -->
            <div class="w-full h-fit flex flex-wrap items-center gap-4">
                <p v-for="(tag, index) in notes[selectedNote].tags" :key="index" :class="'bg-['+tag.color+']'" class="px-4 text-white font-medium text-[14px] rounded-full">{{ tag.name }}</p>
            </div>

            <!-- Note Title -->
            <div class="w-full h-fit grid">
                <p class="text-custom-light-gray font-medium">Notebook Title:</p>
                <InputText v-model="notes[selectedNote].title" type="text" placeholder="Title" class="w-full h-[40px] text-[18px] rounded-[4px] dark:bg-custom-light-gray-bg" />
            </div>
            <Editor v-model="notes[selectedNote].body" editorStyle="height: 500px" />
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment'
import topNav from '../components/topNav.vue'
import loading from '../components/loading.vue'

import InputText from 'primevue/inputtext';
import Editor from 'primevue/editor';
import Menu from 'primevue/menu';
import InputSwitch from 'primevue/inputswitch';
import MultiSelect from 'primevue/multiselect';

import NotFoundAnimation from '../../assets/notFound.json'

import { TrashIcon, BookmarkSquareIcon, FunnelIcon } from '@heroicons/vue/24/outline'

export default{
    name: "Notebooks",
    data(){
        return {
            moment,
            NotFoundAnimation,
            ready: false,
            loading: true,
            selectedNote: 0,
            selectedFolder: "notebooks",
            shareNotebook: false,
            notes: [],
            folders: [],
            tags: [],
            createFolder: false,
            newFolderName: "",
            descOrder: true,
            filter: "updated_at",
            filter_items: [
                {
                    label: 'Sort by',
                    items: [
                        {
                            label: 'Important',
                            key: 'important',
                            command: () => {
                                this.loading = true
                                this.filter = "important"
                                this.loading = false
                            }
                        },
                        {
                            label: 'Title',
                            key: 'title',
                            command: () => {
                                this.loading = true
                                this.filter = "title"
                                this.loading = false
                            }
                        },
                        {
                            label: 'Created Date',
                            key: 'created',
                            command: () => {
                                this.loading = true
                                this.filter = "created_at"
                                this.loading = false
                            }
                        },
                        {
                            label: 'Updated Date',
                            key: 'updated',
                            command: () => {
                                this.loading = true
                                this.filter = "updated_at"
                                this.loading = false
                            }
                        }
                    ]
                }
            ],
            create_items: [
                {
                    label: 'Notebook',
                    key: 'notebook',
                    icon: 'pi pi-book',
                    command: async () => {
                        this.loading = true

                        await axios.get('/api/notebook/new')
                        .then(response => {
                            this.notes.push(response.data.notebook)
                        })

                        this.selectedNote = this.notes.length - 1

                        this.loading = false
                    }
                },
                {
                    label: 'Folder',
                    key: 'folder',
                    icon: 'pi pi-folder',
                    command: async () => {
                        this.createFolder = true
                    }
                }
            ],
            folder_items: [],
            members: []
        }
    },
    async created(){
        this.moment = moment

        await axios.get('/api/notebooks/'+this.filter)
        .then(response => {
            this.notes = response.data.notebook
            if(response.data.folders && typeof response.data.folders == 'object' && response.data.folders.length > 0){
                this.folders = response.data.folders
            }

            this.tags = response.data.tags
        })

        if(this.folders.length > 0){
            this.folders.forEach(folder => {
                this.folder_items.push({
                    label: folder,
                    icon: 'pi pi-folder',
                    command: async () => {
                        await axios.put('/api/notebook/update/'+this.notes[this.selectedNote].id, {"folder": folder})
                        .then(response => {
                            this.notes[this.selectedNote] = response.data.notebook

                            this.$toast.add({
                                severity: 'success',
                                summary: 'Notebook',
                                detail: response.data.message,
                                life: 2500
                            })
                        })
                    }
                })
            })
        }

        await axios.get('/api/member-list')
        .then(response => {
            this.members = response.data.users

            this.notes.forEach(note => {
                if(note.shared){
                    let shareList = []

                    note.shared.forEach(user => {
                        this.members.forEach(member => {
                            if(member.id == user){
                                shareList.push(member)
                            }
                        })
                    })

                    note.shared = shareList
                }
            })
        })

        this.ready = true
        this.loading = false
    },
    watch: {
        filter: async function(value){
            this.loading = true

            await axios.get('/api/notebooks/folder/'+this.selectedFolder+'/'+value)
            .then(response => {
                this.selectedNote = 0
                this.notes = response.data.notebooks
            })

            this.loading = false
        }
    },
    methods: {
        async updateShare(){
            this.loading = true

            await axios.post('/api/notebook/'+this.notes[this.selectedNote].id+'/share', {"users": this.notes[this.selectedNote].shared})
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Notebook',
                    detail: response.data.message,
                    life: 2500
                })
            })

            this.shareNotebook = false

            this.loading =  false
        },
        async saveNote(favorite, important){
            this.loading = true

            if(favorite){
                this.notes[this.selectedNote].favorite = !this.notes[this.selectedNote].favorite
            }

            if(important){
                this.notes[this.selectedNote].important = !this.notes[this.selectedNote].important
            }

            await axios.put('/api/notebook/update/'+this.notes[this.selectedNote].id, this.notes[this.selectedNote])
            .then(response => {
                this.notes[this.selectedNote] = response.data.notebook

                this.$toast.add({
                    severity: 'success',
                    summary: 'Notebook',
                    detail: response.data.message,
                    life: 2500
                })
            })

            this.loading = false
        },
        async deleteNote(){
            this.loading = true

            await axios.delete('/api/notebook/'+this.notes[this.selectedNote].id)
            .then(response => {
                this.notes.splice(this.selectedNote, 1)

                this.$toast.add({
                    severity: 'success',
                    summary: 'Notebook',
                    detail: response.data.message,
                    life: 2500
                })

                this.selectedNote = 0
            })

            this.loading = false
        },
        filterToggle(event){
            this.$refs['filter_menu'].toggle(event);
        },
        createToggle(event){
            this.$refs['create_menu'].toggle(event);
        },
        moveToggle(event){
            this.$refs['move_menu'].toggle(event);
        },
        async updateOrder(){
            this.loading = true
            this.descOrder = !this.descOrder
            this.notes = this.notes.reverse()
            this.loading = false
        },
        async selectFolder(folder){
            this.loading = true
            this.selectedNote = 0

            await axios.get('/api/notebooks/folder/'+folder+'/'+this.filter)
            .then(response => {
                this.selectedFolder = folder
                this.notes = response.data.notebooks
            })

            this.notes.forEach(note => {
                if(note.shared){
                    let shareList = []

                    note.shared.forEach(user => {
                        this.members.forEach(member => {
                            if(member.id == user){
                                shareList.push(member)
                            }
                        })
                    })

                    note.shared = shareList
                }
            })

            this.loading = false
        },
        async newFolder(){
            this.loading = true
            this.createFolder = false

            await axios.post('/api/notebooks/folder/new', {"name": this.newFolderName})
            .then(response => {
                this.newFolderName = ""
                this.folders = response.data.folders
                this.$toast.add({
                    severity: 'success',
                    summary: 'Folder',
                    detail: response.data.message,
                    life: 2500
                })
            })

            this.folder_items = []

            this.folders.forEach(folder => {
                this.folder_items.push({
                    label: folder,
                    icon: 'pi pi-folder',
                    command: async () => {
                        await axios.put('/api/notebook/update/'+this.notes[this.selectedNote].id, {"folder": folder})
                        .then(response => {
                            this.notes[this.selectedNote] = response.data.notebook
                        })
                    }
                })
            })

            this.loading = false
        }
    },
    components: {
        topNav,
        loading,
        InputText,
        Editor,
        Menu,
        InputSwitch,
        TrashIcon,
        BookmarkSquareIcon,
        FunnelIcon,
        MultiSelect
    }
}
</script>

<style scoped>
#notesList::-webkit-scrollbar-track {
    border-radius: 100vh;
    background: rgb(241, 240, 240);
}
</style>