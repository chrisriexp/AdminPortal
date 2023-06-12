<template>
    <div class="w-[475px] h-[367px] grid p-6 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div class="w-full h-fit grid justify-items-center">
            <Icon :icon="'mdi:error'" height="86" class="text-custom-red" />
        </div>
        
        <div class="w-full h-fit grid gap-2 justify-items-center">
            <p class="text-[24px] text-custom-black font-medium">Delete Comment?</p>
            <p class="text-[16px] text-center">You wil not be able to recover this comment.</p>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-6">
            <button @click="$emit('close')" class="w-full h-[48px] text-[16px] text-custom-black font-semibold border-[1px] border-custom-black rounded-[4px]">Cancel</button>

            <button @click="$emit('delete')" class="w-full h-[48px] text-[16px] text-white font-semibold bg-custom-purple shadow-newdrop rounded-[4px]">Delete</button>
        </div>
    </div>
</template>

<script>
import { Icon } from '@iconify/vue';

export default{
    name: 'Delete Project',
    components: {
        Icon
    }
}
</script>