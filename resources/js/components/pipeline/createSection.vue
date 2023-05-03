<template>
    <div class="w-[600px] h-fit grid p-10 bg-custom-bg dark:bg-custom-light-gray-bg rounded-[5px] border-[1px] border-custom-light-gray">
        <div class="w-full h-fit grid gap-4 text-[16px] text-custom-light-gray dark:text-white font-medium">
            <div class="w-full h-fit flex items-center gap-2">
                <p class="w-[200px]">Select Project:</p>
                <Dropdown v-model="section.project" :options="projects" optionLabel="name" class="w-full h-[40px] flex items-center" />
            </div>

            <div class="w-full h-fit flex items-center gap-2">
                <p class="w-[200px]">Section Name:</p>
                <InputText :disabled="section.project.id ? false : true " id="project_name" v-model="section.name" class="h-[40px] w-full text-" />
            </div>

            <div class="w-full h-fit grid grid-cols-2 gap-6">
                <button @click="createSection()" class="py-[4px] text-white font-medium bg-custom-dark-blue dark:bg-custom-dark-bg border-[1px] border-transparent dark:border-custom-gray rounded-[4px]">Create Section</button>

                <button @click="$emit('close')" class="py-[4px] text-custom-light-gray font-medium hover:bg-custom-light dark:hover:bg-custom-gray dark:hover:bg-opacity-20 rounded-[4px]">Discard</button>
            </div>
        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';

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
        Dropdown
    }
}
</script>