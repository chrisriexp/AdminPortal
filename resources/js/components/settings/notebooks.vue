<template>
    <div class="w-full h-fit grid p-10">
        <div class="w-full h-fit grid p-4 rounded-[6px] bg-white dark:bg-custom-dark-bg">
            <div class="w-fit h-fit mb-8 flex items-center gap-4">
                <button @click="updateNotebooks" class="px-4 py-[3px] text-[16px] text-white font-bold bg-custom-dark-blue dark:bg-custom-light-gray-bg rounded-[4px]">Update Notebooks</button>
            </div>

            <div class="w-full h-fit grid grid-cols-2 gap-6">
                <div class="w-full h-fit">
                    <div class="text-[16px] w-full h-fit flow-root">
                        <p class="float-left">Tags</p>
                        <button @click="addItem('tags')" class="float-right flex items-center gap-2"><i class="pi pi-plus"></i>Add Tag</button>
                    </div>

                    <div v-for="(tag, index) in notebooks.tags" :key="index" class="w-full h-fit flex items-center gap-4 my-2 px-6 py-2 bg-custom-bg dark:bg-custom-light-gray-bg rounded-[4px]">
                        <i @click="removeItem('tags', index)" class="pi pi-trash text-[16px] hover:text-custom-red cursor-pointer"></i>
                        <p>Name:</p>
                        <InputText v-model="tag.name" class="w-[300px] h-[25px] text-[14px]" />
                        <div @click="tag.showColorMenu = !tag.showColorMenu" :class="'bg-['+tag.color+']'" class="w-[25px] h-[25px] rounded-[4px] cursor-pointer"></div>
                        <ColorPicker
                            v-if="tag.showColorMenu"
                            class="absolute z-40 mt-96 ml-48"
                            style="width: 220px;"
                            :theme="theme == 'light-mode' ? 'light' : 'dark'"
                            :color="tag.color"
                            :sucker-hide="true"
                            @changeColor="changeTagColor($event, index)"
                        />
                    </div>
                </div>

                <div class="w-full h-fit">
                    <div class="text-[16px] w-full h-fit flow-root">
                        <p class="float-left">Folders</p>
                        <button @click="addItem('folders')" class="float-right flex items-center gap-2"><i class="pi pi-plus"></i>Add Folder</button>
                    </div>

                    <div v-for="(folder, index) in notebooks.folders" :key="index" class="w-full h-fit flex items-center gap-4 my-2 px-6 py-2 bg-custom-bg dark:bg-custom-light-gray-bg rounded-[4px]">
                        <i @click="removeItem('folders', index)" class="pi pi-trash text-[16px] hover:text-custom-red cursor-pointer"></i>
                        <p>Name:</p>
                        <InputText v-model="notebooks.folders[index]" class="w-[300px] h-[25px] text-[14px]" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'

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
        await axios.post('/api/notebooks/settings')
        .then(response => {
            this.theme =response.data.theme
            this.notebooks.folders = response.data.folders
            this.notebooks.tags = response.data.tags
        })

        console.log(this.notebooks)
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
            .then(response => [
                console.log(response)
            ])

            this.$emit('loading')
        }
    },
    components: {
        InputText,
        ColorPicker
    }
}
</script>
