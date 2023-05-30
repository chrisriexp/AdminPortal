<template>
    <div class="w-[475px] h-[367px] grid p-6 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div class="w-full h-fit flow-root">
            <!-- Close Delete Project -->
            <div @click="$emit('close')" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><Icon :icon="'charm:cross'" height="32" class="m-auto text-custom-red" /></div>
        </div>

        <div class="w-full h-fit grid justify-items-center">
            <Icon :icon="'mdi:error'" height="86" class="text-custom-red" />
        </div>
        
        <div class="w-full h-fit grid gap-2 justify-items-center">
            <p class="text-[24px] text-custom-black font-medium">Delete Project?</p>
            <p class="text-[16px] text-center">You wil not be able to recover this project and it's related items.</p>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-6">
            <button @click="$emit('close')" class="w-full h-[48px] text-[16px] text-custom-black font-semibold border-[1px] border-custom-black rounded-[4px]">Cancel</button>

            <button @click="deleteProject" class="w-full h-[48px] text-[16px] text-white font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Delete</button>
        </div>
    </div>
</template>

<script>
import { Icon } from '@iconify/vue';

export default{
    name: 'Delete Project',
    data(){
        return {
            ready: false,
            id: ""
        }
    },
    async mounted(){
        this.id = this.$route.params.id

        this.ready = true
    },
    methods: {
        async deleteProject() {
            this.$emit('loading')
            await axios.delete('/api/pipeline/project/'+this.id)
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Project',
                    detail: response.data.message,
                    life: 2500
                })

                location.reload()
            })
            .catch(error => {
                this.$emit('loading')
                if(error.response.status == 401){
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Authentication Error',
                        detail: errorItem,
                        life: 2500
                    })
                }
            })
        }
    },
    components: {
        Icon
    }
}
</script>