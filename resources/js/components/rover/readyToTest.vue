<template>
    <div class="w-[604px] h-fit max-h-[800px] grid gap-4 p-6 bg-sidebar-bg rounded-[4px] shadow-newdrop overflow-y-scroll">
        <div class="w-full h-fit flow-root">
            <div class="w-fit h-full flex items-center text-[32px] text-custom-black font-semibold float-left">
                <p>Ready for Updates</p>
            </div>

            <!-- Close -->
            <div @click="$emit('close')" class="h-[39px] w-[39px] grid bg-white rounded-[2px] float-right shadow-newdrop cursor-pointer"><Icon :icon="'charm:cross'" height="32" class="text-custom-red m-auto" /></div>
        </div>

        <!-- Assign to -->
        <Dropdown v-model="error.assigned" :options="users" optionLabel="name" class="w-full h-[48px] flex items-center" >
            <template #value="slotProps">
                <div class="grid">
                    <p class="truncate flex items-center gap-2">Assigned to: {{ slotProps.value.name }}</p>
                </div>
            </template>
        </Dropdown>

        <!-- Update Stage -->
        <div class="w-full h-fit grid gap-2">
            <button @click="updateStage" class="w-full h-[48px] text-white text-[16px] font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Update Stage</button>
        </div>
    </div>
</template>

<script>
import { Icon } from '@iconify/vue';
import Dropdown from 'primevue/dropdown';

export default {
    name: "ROVER - Ready to Test Popup",
    props:{
        id: String,
        users: Array
    },
    data(){
        return{
            error: {
                assigned: {},
            }
        }
    },
    methods: {
        async updateStage(){
            this.$emit('loading')

            if(Object.keys(this.error.assigned).length == 0){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Update Validation',
                    detail: "You are missing one or more required fields",
                    life: 2500
                })

                this.$emit('loading')
                return
            }

            await axios.put('/api/rover/stage/'+this.id, {"error": this.error})
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Error Update',
                    detail: response.data.message,
                    life: 2500
                })
                location.reload()
            })
        }
    },
    components: {
        Icon,
        Dropdown
    }
}
</script>