<template>
    <div v-if="ready" class="w-[604px] h-[501px] grid p-10 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                <p>Create Project</p>
            </div>

            <!-- Close Create Project -->
            <div @click="$emit('close')" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><XMarkIcon class="h-[24px] m-auto text-custom-red" /></div>
        </div>

        <!-- Project Name -->
        <InputText v-model="project.name" placeholder="Project Name *" class="h-[48px] w-full" />

        <!-- Project Members -->
        <MultiSelect v-model="project.members" :options="users" optionLabel="name" placeholder="Members" class="w-full h-[48px] text-[12px] flex items-center" >
            <template #value="">
                <div class="flex gap-2 items-center">
                    <UserGroupIcon class="h-[16px]" />
                    <p class="text-[16px] font-medium">Members</p>
                </div>
            </template>
        </MultiSelect>

        <!-- Project Colors -->
        <div class="w-full h-fit flow-root text-[16px] text-custom-black font-medium">
            <div class="w-fit h-fit grid gap-2 float-left">
                <p class="opacity-80">Project Colors</p>
                
                <div class="w-fit h-fit flex items-center gap-2">
                    <!-- Red -->
                    <div @click="project.color = '#F42D2D'" :class="project.color == '#F42D2D' ? 'border-[#F42D2D]' : 'border-transparent'" class="w-fit h-fit p-[2px] border-[1px] rounded-full cursor-pointer"> <div class="w-[32px] h-[32px] bg-[#F42D2D] rounded-full"></div> </div>
                    <!-- Orange -->
                    <div @click="project.color = '#F48D2D'" :class="project.color == '#F48D2D' ? 'border-[#F48D2D]' : 'border-transparent'" class="w-fit h-fit p-[2px] border-[1px] rounded-full cursor-pointer"> <div class="w-[32px] h-[32px] bg-[#F48D2D] rounded-full"></div> </div>
                    <!-- Green -->
                    <div @click="project.color = '#6DF42D'" :class="project.color == '#6DF42D' ? 'border-[#6DF42D]' : 'border-transparent'" class="w-fit h-fit p-[2px] border-[1px] rounded-full cursor-pointer"> <div class="w-[32px] h-[32px] bg-[#6DF42D] rounded-full"></div> </div>
                    <!-- Aqua -->
                    <div @click="project.color = '#2DDCF4'" :class="project.color == '#2DDCF4' ? 'border-[#2DDCF4]' : 'border-transparent'" class="w-fit h-fit p-[2px] border-[1px] rounded-full cursor-pointer"> <div class="w-[32px] h-[32px] bg-[#2DDCF4] rounded-full"></div> </div>
                    <!-- Blue -->
                    <div @click="project.color = '#2D65F4'" :class="project.color == '#2D65F4' ? 'border-[#2D65F4]' : 'border-transparent'" class="w-fit h-fit p-[2px] border-[1px] rounded-full cursor-pointer"> <div class="w-[32px] h-[32px] bg-[#2D65F4] rounded-full"></div> </div>
                    <!-- Purple -->
                    <div @click="project.color = '#9C2DF4'" :class="project.color == '#9C2DF4' ? 'border-[#9C2DF4]' : 'border-transparent'" class="w-fit h-fit p-[2px] border-[1px] rounded-full cursor-pointer"> <div class="w-[32px] h-[32px] bg-[#9C2DF4] rounded-full"></div> </div>
                    <!-- Pink -->
                    <div @click="project.color = '#F42DB0'" :class="project.color == '#F42DB0' ? 'border-[#F42DB0]' : 'border-transparent'" class="w-fit h-fit p-[2px] border-[1px] rounded-full cursor-pointer"> <div class="w-[32px] h-[32px] bg-[#F42DB0] rounded-full"></div> </div>
                </div>
            </div>

            <div class="w-fit h-full grid gap-2 justify-items-center float-right">
                <p class="opacity-80">More Colors</p>
                <img @click="showColorMenu = !showColorMenu" src="../../../assets/color-wheel.svg" alt="Color Wheel" class="h-[38px] w-[38px] cursor-pointer">
                <ColorPicker
                    v-if="showColorMenu"
                    class="absolute z-40 mt-20 ml-48"
                    style="width: 220px;"
                    :color="project.color"
                    :sucker-hide="true"
                    @changeColor="changeColor"
                />
            </div>
        </div>

        <!-- Add Project -->
        <div class="w-full h-fit grid gap-2">
            <button  @click="createProject()" class="w-full h-[48px] text-white text-[16px] font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Create Project</button>
        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';

import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'
import { XMarkIcon, UserGroupIcon } from '@heroicons/vue/24/solid'

export default{
    name: "Create Project",
    data(){
        return{
            ready: false,
            showColorMenu: false,
            project: {
                name: "",
                color: "#F42D2D",
                members: []
            },
            users: []
        }
    },
    async mounted(){
        await axios.get('/api/member-list')
        .then(response => {
            this.users = response.data.users

            response.data.users.forEach(user => {
                if(user.id == response.data.id){
                    this.project.members.push(user)
                }
            })
        })

        this.ready = true
    },
    methods: {
        changeColor(color){
            this.project.color = color.hex
        },
        async createProject(){
            let valid = true

            if(!this.project.name){
                this.valid = false
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Project',
                    detail: "Please enter a project name.",
                    life: 2500
                })
                return
            }

            if(this.project.members.length == 0){
                this.valid = false
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Project',
                    detail: "Please select at least one member for this project.",
                    life: 2500
                })
                return
            }

            if(valid){
                this.$emit('loading')
                let member_list = []

                this.project.members.forEach(member => {
                    member_list.push(member.id)
                })

                this.project.members = member_list

                await axios.post('/api/pipeline/project', this.project)
                .then(response => {
                    this.$emit('updateProjects')

                    this.$toast.add({
                        severity: 'success',
                        summary: 'Project',
                        detail: response.data.message,
                        life: 2500
                    })

                    this.$emit('loading')
                    this.$emit('close')
                })
            }
        }
    },
    components: {
        InputText,
        MultiSelect,
        ColorPicker,
        XMarkIcon,
        UserGroupIcon
    }
}
</script>