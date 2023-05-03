<template>
    <div v-if="ready" class="w-[600px] h-fit grid p-10 bg-custom-bg dark:bg-custom-light-gray-bg rounded-[5px] border-[1px] border-custom-light-gray">
        <div class="w-full h-fit grid gap-4 text-[16px] text-custom-light-gray dark:text-white font-medium">
            <div class="w-full h-fit flex items-center gap-2">
                <p class="w-[200px]">Create Project:</p>
                <InputText id="project_name" v-model="project.name" class="h-[35px] w-full text-" />
            </div>

            <div class="w-full grid">
                <p>Project Members</p>
                <MultiSelect v-model="project.members" display="chip" :options="users" optionLabel="name" class="w-[525px] h-[40px] text-[12px] flex items-center" />
            </div>

            <div class="w-full h-fit grid grid-cols-2 gap-6">
                <div class="w-fit h-fit grid">
                    <p>Project Color</p>
                    <ColorPicker @color-change="updateColor" color="#f80b" :visible-formats="['hex']" class="text-custom-light-gray dark:bg-custom-dark-bg"  />
                </div>

                <div class="w-full h-fit">
                    <div class="w-full h-fit grid gap-4">
                        <button @click="createProject()" class="py-[4px] text-center text-white bg-custom-dark-blue rounded-full dark:bg-custom-dark-bg border-[1px] border-transparent dark:border-custom-gray">Create Project</button>
                        <button @click="$emit('close')" class="py-[4px] text-center hover:bg-custom-light dark:hover:bg-custom-gray dark:hover:bg-opacity-20 rounded-full">Dismiss</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';

import { ColorPicker } from 'vue-accessible-color-picker'

export default{
    name: "Create Project",
    data(){
        return{
            ready: false,
            project: {
                name: "",
                color: "",
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
        updateColor(eventData){
            this.project.color = eventData.cssColor
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

                    this.$emit('close')
                })
            }
        }
    },
    components: {
        InputText,
        MultiSelect,
        ColorPicker
    }
}
</script>