<template>
    <div class="w-full h-fit grid gap-6 pb-12">
        <!-- Header -->
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <p class="text-[32px] text-custom-black font-semibold">Pipeline</p>
            </div>

            <div class="w-fit h-full flex items-center gap-6 float-right">
                <!-- Delete Project -->
                <button @click="$emit('deleteProject')" class="w-[206px] h-[48px] grid justify-items-center text-[16px] text-white font-semibold bg-custom-black shadow-newdrop rounded-[4px]">
                    <div class="w-fit h-fit flex items-center gap-4 my-auto">
                        <Icon :icon="'fluent:delete-20-filled'" height="24" />
                        <p>Delete Project</p>
                    </div>
                </button>

                <!-- Update Project -->
                <button @click="updateProject" class="w-[245px] h-[48px] grid justify-items-center text-[16px] text-white font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">
                    <div class="w-fit h-fit flex items-center gap-4 my-auto">
                        <Icon :icon="'ic:round-update'" height="24" />
                        <p>Update Project</p>
                    </div>
                </button>
            </div>
        </div>

        <div class="w-fit h-fit grid gap-2">
            <p class="text-[16px] text-custom-black font-medium">Project Select</p>

            <div class="w-fit h-fit flex items-center gap-4">
                <!-- Project Select -->
                <Dropdown v-model="project" :options="projects" optionLabel="name" class="w-[519px] h-[48px] flex items-center text-[16px]" />

                <!-- Project Color -->
                <div @click="show_projectColor = !show_projectColor" :class="'bg-['+project.color+']'" class="w-[36px] h-[36px] rounded-full p-[2px] cursor-pointer relative">
                    <ColorPicker v-if="show_projectColor"
                    class="absolute z-40 mt-12"
                    style="width: 220px;"
                    :theme="theme == 'light-mode' ? 'light' : 'dark'"
                    :color="project.color"
                    :sucker-hide="true"
                    @changeColor="changeColor" />
                </div>
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-8">
            <!-- Project Name -->
            <div class="w-full h-fit grid gap-2">
                <p class="text-[16px] text-custom-black font-medium">Name</p>
                <InputText v-model="project.name" class="w-full h-[48px] text-[16px]" />
            </div>

            <!-- Project Members -->
            <div class="w-full h-fit grid gap-2">
                <p class="text-[16px] text-custom-black font-medium">Members</p>
                <MultiSelect v-model="project.members" :options="users" optionLabel="name" class="w-full h-[48px] text-[16px] flex items-center" />
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-8">
            <!-- Project Sections -->
            <div class="w-full h-fit p-4 grid gap-4 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
                <!-- Header -->
                <div class="w-full h-fit flow-root">
                    <div class="w-fit h-full flex items-center float-left">
                        <p class="text-custom-black text-[24px] font-semibold">Sections</p>
                    </div>

                    <!-- Add Section Button -->
                    <div class="w-fit h-full flex items-center float-right">
                        <button @click="addItem('sectionOptions')" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
                            <Icon :icon="'ic:round-plus'" height="24" class="m-auto text-custom-purple" />
                        </button>
                    </div>
                </div>

                <!-- Project Sections -->
                <div v-for="(section, index) in project.sectionOptions" :key="index" class="w-full h-[52px] px-4 flex items-center bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                    <div class="w-full h-fit flow-root">
                        <div class="w-fit h-full flex items-center gap-4 float-left">
                            <p class="text-[16px] text-custom-black">Name</p>
                            <!-- Folder Name -->
                            <InputText v-model="section.name" placeholder="Folder Name" class="w-[223px] h-[32px] mx-auto rounded-[2px]" />
                        </div>

                        <div class="w-fit h-full grid float-right">
                            <!-- Delete Button -->
                            <button @click="removeItem('sectionOptions', index)" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
                                <Icon :icon="'fluent:delete-20-filled'" height="24" class="m-auto text-custom-red" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Tags -->
            <div class="w-full h-fit p-4 grid gap-4 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
                <!-- Header -->
                <div class="w-full h-fit flow-root">
                    <div class="w-fit h-full flex items-center float-left">
                        <p class="text-custom-black text-[24px] font-semibold">Tags</p>
                    </div>

                    <!-- Add Tag Button -->
                    <div class="w-fit h-full flex items-center float-right">
                        <button @click="addItem('tagOptions')" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
                            <Icon :icon="'ic:round-plus'" height="24" class="m-auto text-custom-purple" />
                        </button>
                    </div>
                </div>

                <!-- Project Tags -->
                <div v-for="(tag, index) in project.tagOptions" :key="index" class="w-full h-[52px] px-4 flex items-center bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
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

                        <div class="w-fit h-full grid float-right">
                            <!-- Delete Button -->
                            <button @click="removeItem('tagOptions', index)" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
                                <Icon :icon="'fluent:delete-20-filled'" height="24" class="m-auto text-custom-red" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-8">
            <!-- Project Progress -->
            <div class="w-full h-fit p-4 grid gap-4 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
                <!-- Header -->
                <div class="w-full h-fit flow-root">
                    <div class="w-fit h-full flex items-center float-left">
                        <p class="text-custom-black text-[24px] font-semibold">Progress</p>
                    </div>

                    <!-- Add Progress Button -->
                    <div class="w-fit h-full flex items-center float-right">
                        <button @click="addItem('progressOptions')" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
                            <Icon :icon="'ic:round-plus'" height="24" class="m-auto text-custom-purple" />
                        </button>
                    </div>
                </div>

                <!-- Project Progress -->
                <div v-for="(item, index) in project.progressOptions" :key="index" class="w-full h-[52px] px-4 flex items-center bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                    <div class="w-full h-fit flow-root">
                        <div class="w-fit h-full flex items-center gap-4 float-left">
                            <p class="text-[16px] text-custom-black">Name</p>
                            <!-- Progress Name -->
                            <InputText v-model="item.name" placeholder="Progress Name" class="w-[223px] h-[32px] mx-auto rounded-[2px]" />
                            <!-- Progress Colors -->
                            <div @click="item.showColorMenu = !item.showColorMenu" :class="'bg-['+item.color+']'" class="w-[24px] h-[24px] rounded-full p-[2px] cursor-pointer">
                                <ColorPicker v-if="item.showColorMenu"
                                    class="absolute z-40 mt-8"
                                    style="width: 220px;"
                                    :theme="theme == 'light-mode' ? 'light' : 'dark'"
                                    :color="item.color"
                                    :sucker-hide="true"
                                    @changeColor="changeProgressColor($event, index)" />
                            </div>
                        </div>

                        <div class="w-fit h-full grid float-right">
                            <!-- Delete Button -->
                            <button @click="removeItem('progressOptions', index)" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
                                <Icon :icon="'fluent:delete-20-filled'" height="24" class="m-auto text-custom-red" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Priority -->
            <div class="w-full h-fit p-4 grid gap-4 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px]">
                <!-- Header -->
                <div class="w-full h-fit flow-root">
                    <div class="w-fit h-full flex items-center float-left">
                        <p class="text-custom-black text-[24px] font-semibold">Priority</p>
                    </div>

                    <!-- Add Priority Button -->
                    <div class="w-fit h-full flex items-center float-right">
                        <button @click="addItem('priorityOptions')" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
                            <Icon :icon="'ic:round-plus'" height="24" class="m-auto text-custom-purple" />
                        </button>
                    </div>
                </div>

                <!-- Project Priority -->
                <div v-for="(priority, index) in project.priorityOptions" :key="index" class="w-full h-[52px] px-4 flex items-center bg-sidebar-bg border-[1px] border-custom-black border-opacity-10 rounded-[2px]">
                    <div class="w-full h-fit flow-root">
                        <div class="w-fit h-full flex items-center gap-4 float-left">
                            <p class="text-[16px] text-custom-black">Name</p>
                            <!-- Priority Name -->
                            <InputText v-model="priority.name" placeholder="Priority Name" class="w-[223px] h-[32px] mx-auto rounded-[2px]" />
                            <!-- Priority Colors -->
                            <div @click="priority.showColorMenu = !priority.showColorMenu" :class="'bg-['+priority.color+']'" class="w-[24px] h-[24px] rounded-full p-[2px] cursor-pointer">
                                <ColorPicker v-if="priority.showColorMenu"
                                class="absolute z-40 mt-8"
                                style="width: 220px;"
                                :theme="theme == 'light-mode' ? 'light' : 'dark'"
                                :color="priority.color"
                                :sucker-hide="true"
                                @changeColor="changePriorityColor($event, index)" />
                            </div>
                        </div>

                        <div class="w-fit h-full grid float-right">
                            <!-- Delete Button -->
                            <button @click="removeItem('priorityOptions', index)" class="w-[36px] h-[36px] grid bg-white rounded-[2px] shadow-newdrop">
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
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab'

import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'

import { Icon } from '@iconify/vue';

export default{
    name: "Pipeline Settings",
    data(){
        return{
            user_id: "",
            show_projectColor: false,
            projects: [],
            project: {},
            users: [],
            theme: "",
            suckerCanvas: null,
            suckerArea: [],
            isSucking: false
        }
    },
    async mounted() {
        this.$emit('loading')
        await axios.post('/api/pipeline/settings')
        .then(response => {
            this.projects = response.data.projects
            this.user_id = response.data.user_id
            this.users = response.data.users
            this.theme = response.data.theme
        })

        this.projects.forEach(project => {
            let memberArr = [] 

            project.members.forEach(member => {
                this.users.forEach(user => {
                    if(user.id == member){
                        memberArr.push(user)
                    }
                })
            })

            project.members = memberArr
        })

        this.project = this.projects[0]
        this.$emit('loading')
    },
    watch: {
        project: async function (value){
            this.project.priorityOptions.forEach(priority => {
                priority.showColorMenu = false
            })

            this.project.progressOptions.forEach(item => {
                item.showColorMenu = false
            })

            this.project.tagOptions.forEach(item => {
                item.showColorMenu = false
            })

            this.$router.replace({params: {id: value.id}})
        }
    },
    methods: {
        changeColor(color){
            this.project.color = color.hex
        },
        changePriorityColor(color, index, e){
            this.project.priorityOptions[index].color = color.hex
        },
        changeProgressColor(color, index, e){
            this.project.progressOptions[index].color = color.hex
        },
        changeTagColor(color, index, e){
            this.project.tagOptions[index].color = color.hex
        },
        addItem(item){
            let data = {
                "id": "",
                "project_id": this.project.id,
                "name": "Untitled",
                "color": "#5E2129"
            }


            this.project[item].push(data)
        },
        async removeItem(item, index){
            if(this.project[item][index].id != ""){
                await axios.delete('/api/pipeline/settings/'+item+'/'+this.project[item][index].id)
                .then(response => {
                    console.log(response)
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Project',
                        detail: response.data.message,
                        life: 2500
                    })
                })
            }

            this.project[item].splice(index, 1)
        },
        async updateProject(){
            this.$emit('loading')
            
            let data = Object.assign({}, this.project)
            let memberIds = []

            data.members.forEach(member => {
                memberIds.push(member.id)
            })

            data.members = memberIds

            await axios.post('/api/pipeline/settings', {
                "update": data
            })
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Project',
                    detail: response.data.message,
                    life: 2500
                })
            })

            this.$emit('loading')
        }
    },
    components: {
        Dropdown,
        InputText,
        MultiSelect,
        ColorPicker,
        Accordion,
        AccordionTab,
        Icon
    }
}
</script>
