<template>
    <div class="w-[604px] h-fit p-6 grid gap-4 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <div class="w-full h-full flow-root">
            <div class="w-fit h-full flex items-center float-left">
                <p class="text-[32px] truncate text-custom-black font-semibold">Select Type</p>
            </div>

            <div class="w-fit h-full flex items-center float-right">
                <!-- Close Button -->
                <button @click="$emit('close')" class="w-[39px] h-[39px] grid bg-white shadow-newdrop rounded-[2px]">
                    <Icon :icon="'charm:cross'" height="24" class="text-custom-red m-auto" />
                </button>
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-2 gap-8">
            <!-- Bulk Upload -->
            <button @click="$emit('bulk')" class="w-full h-[48px] grid inner-border-[1px] inner-border-custom-purple rounded-[4px] shadow-newdrop">
                <div class="w-fit h-fit flex items-center gap-4 m-auto text-[16px] text-custom-purple font-semibold">
                    <Icon :icon="'mingcute:file-upload-fill'" height="24" />
                    <p>Bulk Upload</p>
                </div>
            </button>

            <!-- Single Entry -->
            <button @click="$emit('single')" class="w-full h-[48px] grid bg-custom-purple rounded-[4px] shadow-newdrop">
                <div class="w-fit h-fit flex items-center gap-4 m-auto text-[16px] text-white font-semibold">
                    <Icon :icon="'ic:round-plus'" height="24" />
                    <p>Single Entry</p>
                </div>
            </button>
        </div>
    </div>
</template>

<script>
import { Icon } from '@iconify/vue';

export default {
    name: "Add Agent Select",
    components: {
        Icon
    }
}
</script>