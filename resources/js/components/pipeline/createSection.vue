<template>
    <div class="w-[604px] h-[403px] grid p-10 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                <p>Create Section</p>
            </div>

            <!-- Close Create Section -->
            <div @click="$emit('close')" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><XMarkIcon class="h-[24px] m-auto text-custom-red" /></div>
        </div>

        <!-- Project Select -->
        <Dropdown v-model="section.project" :options="projects" optionLabel="name" class="w-full h-[48px] flex items-center" >
            <template #value="slotProps">
                <div class="flex gap-2 items-center">
                    <p class="flex items-center gap-2"><span class="text-[16px]  text-custom-black font-medium flex items-center gap-2"><PuzzlePieceIcon class="h-[24px]" />Project:</span> {{ slotProps.value.name }}</p>
                </div>
            </template>
        </Dropdown>

        <!-- Section Name -->
        <InputText :disabled="section.project_id ? false : true" v-model="section.name" placeholder="Section Name *" class="h-[48px] w-full disabled:bg-custom-bg" />

        <!-- Add Section -->
        <div class="w-full h-fit grid gap-2">
            <button @click="createSection()" class="w-full h-[48px] text-white text-[16px] font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Add Section</button>
        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';

import { XMarkIcon, PuzzlePieceIcon } from '@heroicons/vue/24/solid'

export default {
    name: "Create Section",
    props: {
        projects: Array,
        project_id: String
    },
    data(){
        return{
            section: {
                project: {},
                project_id: "",
                name: ""
            }
        }
    },
    async mounted(){
        if(this.project_id != 'my-tasks'){
            this.projects.forEach(project => {
                if(project.id == this.project_id){
                    this.section.project = project
                }
            })
        }
    },
    watch: {
        'section.project': async function(value){
            this.section.project_id = this.section.project.id
        }
    },
    methods: {
        async createSection(){
            await axios.post('/api/pipeline/project/section', this.section)
            .then(response => {
                this.$emit('updateSections')
                this.$emit('close')
            })
        }
    },
    components: {
        InputText,
        Dropdown,
        XMarkIcon,
        PuzzlePieceIcon
    }
}
</script>