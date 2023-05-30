<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Nav Bar -->
    <div class="absolute z-40 w-full">
        <topNav :active="'notebooks'" @loading="loading = !loading" class="fixed z-40" />
    </div>

    <!-- Create Folder -->
    <div v-if="createFolder" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <div class="w-[604px] h-[331px] grid p-6 m-auto bg-sidebar-bg rounded-[4px] shadow-nwedrop">
            <div class="w-full h-fit flow-root">
                <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                    <p>Create Folder</p>
                </div>

                <!-- Close Create Folder -->
                <div @click="()=>{createFolder = false; newFolderName = ''}" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><XMarkIcon class="h-[24px] m-auto text-custom-red" /></div>
            </div>

            <!-- Folder Name -->
            <InputText v-model="newFolderName" placeholder="Folder Name" class="h-[48px] w-full" />
        
            <!-- Add Folder -->
            <div class="w-full h-fit grid gap-2">
                <button @click="function(){if (newFolderName == ''){$toast.add({severity: 'warn',summary:'Folder Name',detail:'Please enter a name to create folder.',life:2500})}else{newFolder()}}" class="w-full h-[48px] text-white text-[16px] font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Add Folder</button>
            </div>
        </div>
    </div>

    <!-- Share Notebook -->
    <div v-if="shareNotebook" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <div class="w-[604px] h-[331px] grid p-6 m-auto bg-sidebar-bg rounded-[4px] shadow-nwedrop">
            <div class="w-full h-fit flow-root">
                <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                    <p>Share Notebook</p>
                </div>

                <!-- Close Create Folder -->
                <div @click="shareNotebook = false" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><XMarkIcon class="h-[24px] m-auto text-custom-red" /></div>
            </div>

            <!-- Folder Name -->
            <MultiSelect v-model="notes[selectedNote].shared" display="chip" :options="members" optionLabel="name" class="w-[560px] h-[48px] text-[12px] flex items-center" />
        
            <!-- Add Folder -->
            <div class="w-full h-fit grid gap-2">
                <button @click="updateShare()" class="w-full h-[48px] text-white text-[16px] font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Update Share List</button>
            </div>
        </div>
    </div>

    <div v-if="ready" class="w-full h-screen z-30 absolute">
        <!-- Folders -->
        <div class="w-[305px] h-full px-8 pt-[150px] bg-sidebar-bg z-30 absolute">
            <div class="w-full h-fit grid gap-4 text-[16px] text-[#787878] font-medium">
                <div @click="selectFolder('favorites')" :class="selectedFolder == 'favorites' ? 'text-custom-black bg-[#F1F1F1]' : ''" class="w-full h-fit px-4 py-2 flex items-center gap-4 rounded-[4px] hover:bg-[#F1F1F1] cursor-pointer"><StarIcon class="h-[24px]" /> Favorite</div>
                <div @click="selectFolder('shared')" :class="selectedFolder == 'shared' ? 'text-custom-black bg-[#F1F1F1]' : ''" class="w-full h-fit px-4 py-2 flex items-center gap-4 rounded-[4px] hover:bg-[#F1F1F1] cursor-pointer"><ShareIcon class="h-[24px]" /> Shared With Me</div>
                <div @click="selectFolder('notebooks')" :class="selectedFolder == 'notebooks' ? 'text-custom-black bg-[#F1F1F1]' : ''" class="w-full h-fit px-4 py-2 flex items-center gap-4 rounded-[4px] hover:bg-[#F1F1F1] cursor-pointer"><BookOpenIcon class="h-[24px]" /> Notebooks</div>
                
                <div v-if="folders.length > 0" class="w-full h-fit pl-12 grid">
                    <div @click="selectFolder(folder)" v-for="(folder, index) in folders" :key="index" :class="selectedFolder == folder ? 'text-custom-black bg-[#F1F1F1]' : ''" class="w-full h-fit px-4 py-2 flex items-center gap-4 rounded-[4px] hover:bg-[#F1F1F1] cursor-pointer">
                        <BookmarkIcon class="h-[24px] w-[24px]" />
                        <div class="grid h-fit w-full"><p class="truncate">{{ folder }}</p></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full h-full grid pl-[350px] pt-[110px] pr-[40px] pb-[25px] relative">
            <div class="w-full h-fit grid gap-8">
                <div class="w-full h-fit flow-root">
                    <div class="w-fit h-full grid float-left">
                        <p class="my-auto text-[32px] text-custom-black font-semibold">Notes</p>
                    </div>

                    <div class="w-fit h-full flex items-center gap-8 float-right">
                        <!-- Favorite -->
                        <div v-if="notes.length > 0 && selectedFolder != 'shared'" class="w-fit h-fit flex items-center gap-2 text-custom-yellow text-[16px] font-medium notebook-favorite">
                            <Checkbox v-model="notes[selectedNote].favorite" :binary="true" />
                            <p>Favorite</p>
                        </div>

                        <!-- Important -->
                        <div v-if="notes.length > 0 && selectedFolder != 'shared'" class="w-fit h-fit flex items-center gap-2 text-custom-red text-[16px] font-medium notebook-important">
                            <Checkbox v-model="notes[selectedNote].important" :binary="true" />
                            <p>Important</p>
                        </div>

                        <!-- Sort Filter -->
                        <Dropdown v-model="filter" :options="filter_items" optionLabel="name" placeholder="Select a Country" class="h-[48px] w-[288px]">
                            <template #value="slotProps">
                                <div v-if="slotProps.value" class="flex align-items-center">
                                    <div class="flex items-center gap-2 text-custom-black opacity-80 text-[16px]"><i class="pi pi-filter-fill"></i><span class="font-medium">Sort By:</span> {{ slotProps.value.name }}</div>
                                </div>
                                <span v-else>
                                    {{ slotProps.placeholder }}
                                </span>
                            </template>
                        </Dropdown>

                        <!-- Create Button -->
                        <button @click="createToggle" aria-haspopup="true" aria-controls="create_menu" class="h-[48px] w-[175px] grid justify-items-center text-[16px] text-white font-semibold bg-custom-purple shadow-customdrop rounded-[4px]"><span class="my-auto flex items-center gap-4"><i class="pi pi-plus"></i>Create</span></button>
                        <Menu ref="create_menu" id="create_menu" :model="create_items" :popup="true" class="w-[200px]" />
                    </div>
                </div>

                <!-- Notes -->
                <div v-if="notes.length > 0" class="w-full h-fit max-h-[700px] grid gap-8 overflow-y-scroll">
                    <div v-for="(note, index) in notes" :key="index" class="w-full h-fit">
                        <!-- If Note is not selected -->
                        <div v-if="selectedNote != index" @click="selectedNote = index" class="w-full h-[109px] flow-root p-6 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px] cursor-pointer">
                            <div class="w-fit h-full grid float-left text-custom-black">
                                <!-- Note Title -->
                                <div class="w-fit h-fit flex items-center gap-2">
                                    <i v-if="note.important" class="pi pi-flag-fill text-custom-red"></i>
                                    <p class="text-[24px] font-medium">{{ note.title }}</p>
                                    <i v-if="note.favorite" class="pi pi-star-fill text-custom-yellow"></i>
                                </div>
                                <!-- Body Preview -->
                                <p class="truncate max-w-[500px] opacity-50">{{ note.body ? note.body.replace(/(<([^>]+)>)/ig, "") : '' }}</p>
                            </div>

                            <div class="w-fit h-full flex items-center gap-12 float-right">
                                <!-- Note Updated at -->
                                <div class="w-fit h-fit grid text-custom-black opacity-50">
                                    <p>Updated:</p>
                                    <p>{{ moment(note.updated_at).format('ddd MM/DD/YYYY, h:mm a') }}</p>
                                </div>

                                <!-- Share and Delete Button -->
                                <div @click="shareNotebook = true" class="w-[48px] h-[48px] grid text-custom-black bg-white rounded-[2px] shadow-newdrop cursor-pointer"><ShareIcon class="h-[24px] m-auto" /></div>
                                <div v-if="selectedFolder != 'shared'" class="w-[48px] h-[48px] grid text-custom-red bg-white rounded-[2px] shadow-newdrop cursor-pointer"><TrashIcon class="h-[24px] m-auto" /></div>
                            </div>
                        </div>

                        <!-- If Note is selected -->
                        <div v-else class="w-full h-fit grid gap-4 p-6 bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[4px] relative">
                            <div class="w-full h-fit flow-root">
                                <!-- Note Title -->
                                <div class="w-fit h-full flex items-center gap-2 float-left note-title">
                                    <InputText v-model="note.title" class="h-[48p] w-[448px]" />
                                </div>

                                <div class="float-right w-fit h-full flex items-center gap-8">
                                    <!-- Note Shared By -->
                                    <div v-if="selectedFolder == 'shared'" class="w-fit h-fit grid text-custom-black opacity-50">
                                        <p>Shared By:</p>
                                        <p>{{ note.owner }}</p>
                                    </div>

                                    <!-- Note Created at -->
                                    <div class="w-fit h-fit grid text-custom-black opacity-50">
                                        <p>Created:</p>
                                        <p>{{ moment(note.created_at).format('ddd MM/DD/YYYY, h:mm a') }}</p>
                                    </div>
                                    
                                    <!-- Note Updated at -->
                                    <div class="w-fit h-fit grid text-custom-black opacity-50">
                                        <p>Updated:</p>
                                        <p>{{ moment(note.updated_at).format('ddd MM/DD/YYYY, h:mm a') }}</p>
                                    </div>

                                    <!-- Share Notebook -->
                                    <div v-if="selectedFolder != 'shared'" @click="shareNotebook = true" class="w-[48px] h-[48px] grid text-custom-black bg-white rounded-[2px] shadow-newdrop cursor-pointer"><ShareIcon class="h-[24px] m-auto" /></div>
                                </div>
                            </div>

                            <hr>

                            <div class="w-full h-fit flow-root">
                                <div class="w-fit max-w-[400px] h-fit flex flex-wrap items-center gap-2 float-left">
                                    <!-- Favorite -->
                                    <p v-if="note.favorite" class="w-fit h-fit flex items-center gap-2 text-white text-[12px] py-[3px] px-4 rounded-full bg-custom-yellow"><i class="pi pi-star"></i> Favorites</p>
                                    <!-- Important -->
                                    <p v-if="note.important" class="w-fit h-fit flex items-center gap-2 text-white text-[12px] py-[3px] px-4 rounded-full bg-custom-red"><i class="pi pi-flag"></i> Important</p>
                                    <!-- Tags -->
                                    <div v-for="(tag, tagIndex) in note.tags" :key="tagIndex" :class="'bg-['+tag.color+']'" class="w-fit h-fit flex items-center gap-2 text-white text-[12px] py-[3px] px-4 rounded-full"><i class="pi pi-tag"></i> {{ tag.name }}</div>
                                </div>

                                <div class="w-fit h-fit flex gap-8 float-right">
                                    <!-- Folder -->
                                    <div v-if="selectedFolder != 'shared'" class="w-fit h-fit grid gap-2">
                                        <div class="h-fit w-fit flex items-center gap-2 text-custom-black">
                                            <BookmarkIcon class="h-[24px]"/>
                                            <Dropdown v-model="notes[selectedNote].folder" :options="folders" class="h-[33px] w-[200px] flex items-center" />
                                        </div>
                                    </div>

                                    <!-- Tags -->
                                    <div v-if="selectedFolder != 'shared'" class="w-fit h-fit grid gap-2">
                                        <MultiSelect v-model="notes[selectedNote].tags" :options="tags" optionLabel="name" placeholder="Tags" class="w-[117px] h-[33px] text-[12px] flex items-center" >
                                            <template #value>
                                                <div class="py-2 flex items-center gap-2">
                                                    <i class="pi pi-tags"></i>
                                                    <p>Tags</p>
                                                </div>
                                            </template>
                                            <template #option="slotProps">
                                                <div class="flex gap-2 items-center">
                                                    <div :class="'bg-['+slotProps.option.color+']'" class="px-4 rounded-full text-white">{{ slotProps.option.name }}</div>
                                                </div>
                                            </template>
                                        </MultiSelect>
                                    </div>

                                    <!-- Save and Delete Note -->
                                    <div v-if="selectedFolder != 'shared'" @click="()=>{notes[selectedNote].folder = 'notebooks'; saveNote()}" class="w-[48px] h-[48px] grid text-custom-black bg-white rounded-[2px] shadow-newdrop cursor-pointer"><FolderMinusIcon class="h-[24px] m-auto" /></div>
                                    <div @click="saveNote(false, false)" class="w-[48px] h-[48px] grid text-custom-black bg-white rounded-[2px] shadow-newdrop cursor-pointer"><i class="pi pi-save text-[24px] m-auto"></i></div>
                                    <div v-if="selectedFolder != 'shared'" @click="deleteNote()" class="w-[48px] h-[48px] grid text-custom-red bg-white rounded-[2px] shadow-newdrop cursor-pointer"><TrashIcon class="h-[24px] m-auto" /></div>
                                </div>
                            </div>

                            <!-- Note Body -->
                            <Editor v-model="notes[selectedNote].body" editorStyle="height: 255px" />
                        </div>
                    </div>
                </div>

                <!-- No Notebooks Found -->
                <div v-else class=" w-full h-full grid">
                    <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'250px'" :height="'250px'" :speed="0.75" class="m-auto" />
                </div>
            </div>
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
import Dropdown from 'primevue/dropdown';
import Checkbox from 'primevue/checkbox';

import NotFoundAnimation from '../../assets/newNotFound.json'

import { BookmarkSquareIcon, FunnelIcon, FolderMinusIcon } from '@heroicons/vue/24/outline'
import { StarIcon, ShareIcon, BookOpenIcon, BookmarkIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/solid'

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
            filter: {name: "Created Date", code: 'created_at'},
            filter_items: [
                {name: "Important", code:'important'},
                {name: "Title", code:'title'},
                {name: "Created Date", code: 'created_at'},
                {name:  "Updated Date", code: "updated_at"}
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

        await axios.get('/api/notebooks/'+this.filter.code)
        .then(response => {
            this.notes = response.data.notebook
            if(response.data.folders && typeof response.data.folders == 'object' && response.data.folders.length > 0){
                this.folders = response.data.folders
            }

            this.notes.forEach(note => {
                if(note.important == 1){
                    note.important = true
                } else if(note.important == 0){
                    note.important = false
                }

                if(note.favorite == 1){
                    note.favorite = true
                } else if(note.favorite == 0){
                    note.favorite = false
                }
            })

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

            await axios.get('/api/notebooks/folder/'+this.selectedFolder+'/'+value.code)
            .then(response => {
                console.log(response)
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

            await axios.get('/api/notebooks/folder/'+folder+'/'+this.filter.code)
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

                if(note.important == 1){
                    note.important = true
                } else if(note.important == 0){
                    note.important = false
                }

                if(note.favorite == 1){
                    note.favorite = true
                } else if(note.favorite == 0){
                    note.favorite = false
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
        MultiSelect,
        StarIcon,
        ShareIcon,
        BookOpenIcon,
        BookmarkIcon,
        Dropdown,
        Checkbox,
        XMarkIcon,
        FolderMinusIcon
    }
}
</script>

<style scoped>
#notesList::-webkit-scrollbar-track {
    border-radius: 100vh;
    background: rgb(241, 240, 240);
}

:deep( .notebook-favorite .p-checkbox:not(.p-checkbox-disabled) .p-checkbox-box.p-highlight){
    background-color: #FFC15E !important;
    border-color: #FFC15E !important;
}
:deep( .notebook-favorite .p-checkbox .p-checkbox-box ){
    border-color: #FFC15E !important;
}

:deep( .notebook-important .p-checkbox:not(.p-checkbox-disabled) .p-checkbox-box.p-highlight){
    background-color: #F42D2D !important;
    border-color: #F42D2D !important;
}

:deep( .notebook-important .p-checkbox .p-checkbox-box ){
    border-color: #F42D2D !important;
}

:deep( .note-title .p-inputtext ){
    background-color: white !important;
    border-radius: 2px;
    color: #212121;
    font-size: 20px;
}

</style>