<template>
    <div class="w-full h-fit grid p-10">
        <div class="w-full h-fit grid p-4 rounded-[6px] bg-white dark:bg-custom-dark-bg">
            <div class="w-full h-fit flow-root">
                <!-- Name -->
                <div class="w-fit h-fit float-left flex items-center gap-2">
                    <p>Select Project: </p>
                    <Dropdown v-model="project" :options="projects" optionLabel="name" class="w-[300px] h-[35px] flex items-center text-[14px]" />
                </div>

                <!-- Save Information Delete Project -->
                <div v-if="project.id" class="w-fit h-fit flex items-center gap-4 float-right">
                    <button @click="updateProject" class="text-[14px] h-[35px] w-fit px-4 bg-custom-dark-blue dark:bg-custom-light-gray-bg text-white rounded-[4px] border-[1px]">Update Projects</button>
                    <button class="text-[14px] h-[35px] w-fit px-4 bg-custom-gray dark:bg-custom-light-gray-bg text-white rounded-[4px]">Delete Projects</button>
                </div>
            </div>

            <div v-if="project.id" class="w-full h-fit mt-8 grid grid-cols-2 gap-8 text-[14px] text-custom-dark-blue dark:text-white">
                <div class="w-full h-fit grid gap-4">
                    <!-- Project Name -->
                    <div class="w-fit h-fit flex items-center gap-2">
                        <p class="w-[150px]">Name</p>
                        <InputText v-model="project.name" class="w-[300px] h-[35px] text-[14px]" />
                    </div>
                    <!-- Project Color -->
                    <div class="w-fit h-fit flex items-center gap-2">
                        <p class="w-[150px]"> Color</p>
                        <!-- <ColorPicker @color-change="updateColor" :color="project.color" :visible-formats="['hex']" class="text-custom-light-gray dark:bg-custom-dark-bg"  /> -->
                        <div @click="show_projectColor = !show_projectColor" :class="'bg-['+project.color+']'" class="w-[30px] h-[30px] rounded-[4px] cursor-pointer"></div>
                        <ColorPicker
                        v-if="show_projectColor"
                        class="absolute z-40 mt-96 ml-48"
                        style="width: 220px;"
                        :theme="theme == 'light-mode' ? 'light' : 'dark'"
                        :color="project.color"
                        :sucker-hide="true"
                        @changeColor="changeColor"
                        />
                    </div>

                    <!-- Project Members -->
                    <div class="w-fit h-fit flex items-center gap-2">
                        <p class="w-[150px]">Members</p>
                        <MultiSelect v-model="project.members" :options="users" optionLabel="name" class="w-[300px] h-[35px] text-[14px] flex items-center" />
                    </div>
                </div>

                <div class="w-full h-fit">
                    <div class="text-[16px] w-full h-fit flow-root">
                        <p class="float-left">Sections</p>
                        <button @click="addItem('sectionOptions')" class="float-right flex items-center gap-2"><i class="pi pi-plus"></i>Add Section</button>
                    </div>

                    <div v-for="(section, index) in project.sectionOptions" :key="index" class="w-full h-fit flex items-center gap-4 my-2 px-6 py-2 bg-custom-bg dark:bg-custom-light-gray-bg rounded-[4px]">
                        <i @click="removeItem('sectionOptions', index)" class="pi pi-trash text-[16px] hover:text-custom-red cursor-pointer"></i>
                        <p>Name:</p>
                        <InputText v-model="section.name" class="w-[300px] h-[25px] text-[14px]" />
                    </div>
                </div>
            </div>
            
            <hr class="my-6">

            <div v-if="project.id" class="w-full h-fit grid grid-cols-2 gap-8 text-[14px] text-custom-dark-blue dark:text-white">
                <div class="w-full h-fit grid gap-6">
                    <div class="w-full h-fit">
                        <div class="text-[16px] w-full h-fit flow-root">
                            <p class="float-left">Priorites</p>
                            <button @click="addItem('priorityOptions')" class="float-right flex items-center gap-2"><i class="pi pi-plus"></i>Add Priority</button>
                        </div>

                        <div v-for="(priority, index) in project.priorityOptions" :key="index" class="w-full h-fit flex items-center gap-4 my-2 px-6 py-2 bg-custom-bg dark:bg-custom-light-gray-bg rounded-[4px]">
                            <i @click="removeItem('priorityOptions', index)" class="pi pi-trash text-[16px] hover:text-custom-red cursor-pointer"></i>
                            <p>Name:</p>
                            <InputText v-model="priority.name" class="w-[300px] h-[25px] text-[14px]" />
                            <div @click="priority.showColorMenu = !priority.showColorMenu" :class="'bg-['+priority.color+']'" class="w-[25px] h-[25px] rounded-[4px] cursor-pointer"></div>
                            <ColorPicker
                                v-if="priority.showColorMenu"
                                class="absolute z-40 mt-96 ml-48"
                                style="width: 220px;"
                                :theme="theme == 'light-mode' ? 'light' : 'dark'"
                                :color="priority.color"
                                :sucker-hide="true"
                                @changeColor="changePriorityColor($event, index)"
                            />
                        </div>
                    </div>

                    <div class="w-full h-fit">
                        <div class="text-[16px] w-full h-fit flow-root">
                            <p class="float-left">Progress</p>
                            <button @click="addItem('progressOptions')" class="float-right flex items-center gap-2"><i class="pi pi-plus"></i>Add Progress</button>
                        </div>

                        <div v-for="(item, index) in project.progressOptions" :key="index" class="w-full h-fit flex items-center gap-4 my-2 px-6 py-2 bg-custom-bg dark:bg-custom-light-gray-bg rounded-[4px]">
                            <i @click="removeItem('progressOptions', index)" class="pi pi-trash text-[16px] hover:text-custom-red cursor-pointer"></i>
                            <p>Name:</p>
                            <InputText v-model="item.name" class="w-[300px] h-[25px] text-[14px]" />
                            <div @click="item.showColorMenu = !item.showColorMenu" :class="'bg-['+item.color+']'" class="w-[25px] h-[25px] rounded-[4px] cursor-pointer"></div>
                            <ColorPicker
                                v-if="item.showColorMenu"
                                class="absolute z-40 mt-96 ml-48"
                                style="width: 220px;"
                                :theme="theme == 'light-mode' ? 'light' : 'dark'"
                                :color="item.color"
                                :sucker-hide="true"
                                @changeColor="changeProgressColor($event, index)"
                            />
                        </div>
                    </div>
                </div>

                <div class="w-full h-fit">
                    <div class="text-[16px] w-full h-fit flow-root">
                        <p class="float-left">Tags</p>
                        <button @click="addItem('tagOptions')" class="float-right flex items-center gap-2"><i class="pi pi-plus"></i>Add Tag</button>
                    </div>

                    <div v-for="(tag, index) in project.tagOptions" :key="index" class="w-full h-fit flex items-center gap-4 my-2 px-6 py-2 bg-custom-bg dark:bg-custom-light-gray-bg rounded-[4px]">
                        <i @click="removeItem('tagOptions', index)" class="pi pi-trash text-[16px] hover:text-custom-red cursor-pointer"></i>
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
            </div>
        </div>
    </div>
</template>

<script>
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';
// import { ColorPicker } from 'vue-accessible-color-picker'
import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'

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
    async mounted(){
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
        AccordionTab
    }
}
</script>