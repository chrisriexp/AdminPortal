<template>
    <div class="w-full h-fit grid gap-6 pb-12">
        <!-- Header -->
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <p class="text-[32px] text-custom-black font-semibold">Notebooks</p>
            </div>

            <div class="w-fit h-full flex items-center float-right">
                <!-- Update Notebooks -->
                <button @click="updateNotebooks" class="w-[275px] h-[48px] grid justify-items-center text-[16px] text-white font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">
                    <div class="w-fit h-fit flex items-center gap-4 my-auto">
                        <Icon :icon="'ic:round-update'" height="24" />
                        <p>Update Notebooks</p>
                    </div>
                </button>
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-6">
            <!-- Notebook Tags -->
            <div class="w-full h-fit p-4 grid gap-4 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
                <!-- Header -->
                <div class="w-full h-fit flow-root">
                    <div class="w-fit h-full flex items-center float-left">
                        <p class="text-custom-black text-[24px] font-semibold">Tags</p>
                    </div>

                    <!-- Add Tag Button -->
                    <div class="w-fit h-full flex items-center float-right">
                        <button @click="addItem('tags')" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
                            <Icon :icon="'ic:round-plus'" height="24" class="m-auto text-custom-purple" />
                        </button>
                    </div>
                </div>

                <!-- Notebook Tags -->
                <div v-for="(tag, index) in notebooks.tags" :key="index" class="w-full h-[52px] px-4 flex items-center bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                    <div class="w-full h-fit flow-root">
                        <div class="w-fit h-full flex items-center gap-4 float-left">
                            <p class="text-[16px] text-custom-black">Name</p>
                            <!-- Tag Name -->
                            <InputText v-model="tag.name" placeholder="Tag Name" class="w-[223px] h-[32px] mx-auto rounded-[2px]" />
                            <!-- Tag Colors -->
                            <div @click="tag.showColorMenu = !tag.showColorMenu" :class="'bg-['+tag.color+']'" class="w-[24px] h-[24px] rounded-full p-[2px] cursor-pointer">
                                <ColorPicker v-if="tag.showColorMenu"
                                class="absolute z-40 mt-8"
                                style="width: 220px;"
                                :theme="theme == 'light-mode' ? 'light' : 'dark'"
                                :color="tag.color"
                                :sucker-hide="true"
                                @changeColor="changeTagColor($event, index)" />
                            </div>
                        </div>

                        <div class="w-fit h-full flex items-center gap-4 float-right">
                            <!-- Delete Tag -->
                            <button @click="removeItem('tags', index)" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
                                <Icon :icon="'fluent:delete-20-filled'" height="24" class="m-auto text-custom-red" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notebook Folders -->
            <div class="w-full h-fit p-4 grid gap-4 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
                <!-- Header -->
                <div class="w-full h-fit flow-root">
                    <div class="w-fit h-full flex items-center float-left">
                        <p class="text-custom-black text-[24px] font-semibold">Folder</p>
                    </div>

                    <!-- Add Folder Button -->
                    <div class="w-fit h-full flex items-center float-right">
                        <button @click="addItem('folders')" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
                            <Icon :icon="'ic:round-plus'" height="24" class="m-auto text-custom-purple" />
                        </button>
                    </div>
                </div>

                <!-- Notebook Folders -->
                <div v-for="(folder, index) in notebooks.folders" :key="index" class="w-full h-[52px] px-4 flex items-center bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                    <div class="w-full h-fit flow-root">
                        <div class="w-fit h-full flex items-center gap-4 float-left">
                            <p class="text-[16px] text-custom-black">Name</p>
                            <!-- Folder Name -->
                            <InputText v-model="notebooks.folders[index]" placeholder="Folder Name" class="w-[223px] h-[32px] mx-auto rounded-[2px]" />
                        </div>

                        <div class="w-fit h-full grid float-right">
                            <!-- Delete Button -->
                            <button @click="removeItem('folders', index)" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
                                <Icon :icon="'fluent:delete-20-filled'" height="24" class="m-auto text-custom-red" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import { ColorPicker } from 'vue-color-kit';
import 'vue-color-kit/dist/vue-color-kit.css';

import { Icon } from '@iconify/vue';

export default {
    name: "Notbooks Settings",
    data(){
        return{
            theme: "",
            notebooks: {
                folders: [],
                tags: []
            }
        }
    },
    async mounted(){
        this.$emit('loading')

        await axios.post('/api/notebooks/settings')
        .then(response => {
            this.theme =response.data.theme
            this.notebooks.folders = response.data.folders
            this.notebooks.tags = response.data.tags
        })

        this.$emit('loading')
    },
    methods: {
        changeTagColor(color, index, e){
            this.notebooks.tags[index].color = color.hex
        },
        async removeItem(type, index){
            if(type == 'folders'){
                console.log(this.notebooks.folders[index])
                await axios.delete('/api/notebooks/settings/folder/'+this.notebooks.folders[index])
                .then(response => {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Notebooks',
                        detail: response.data.message,
                        life: 2500
                    })
                })
            }

            if(type == 'tags' && this.notebooks.tags[index].id != ''){
                await axios.delete('/api/notebooks/settings/tags/'+this.notebooks.tags[index].id)
                .then(response => {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Notebooks',
                        detail: response.data.message,
                        life: 2500
                    })
                })
            }

            this.notebooks[type].splice(index, 1)
        },
        addItem(type){
            if(type == 'tags'){
                this.notebooks.tags.push({
                    'id': '',
                    'name': 'Untitled',
                    'color': '#ED760E'
                })
            }
            else if(type == 'folders') {
                this.notebooks.folders.push("Untitled Folder")
            }
        },
        async updateNotebooks(){
            this.$emit('loading')

            await axios.post('/api/notebooks/settings', {"update": this.notebooks})
                .then(response => {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Notebooks',
                        detail: response.data.message,
                        life: 2500
                    })
                })

            this.$emit('loading')
        }
    },
    components: {
        InputText,
        ColorPicker,
        Icon
    }
}
</script>
