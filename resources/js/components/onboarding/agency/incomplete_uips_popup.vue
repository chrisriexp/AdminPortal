<template>
    <div class="w-[604px] h-[501px] grid p-10 bg-sidebar-bg rounded-[4px] shadow-newdrop">
        <p class="text-[32px] text-custom-black font-semibold">Incomplete Carrier UIP(s)</p>

        <p class="text-[18px] text-custom-black">Below is the list of Rocket MGA carrier appointments that are currently missing UIPs, please enter the carrier UIP(s) before finalizing.</p>
    
        <div class="ml-8 w-full h-fit grid gap-2 text-custom-purple text-[16px] font-medium">
            <div v-for="(carrier, index) in carriers" :key="index" class="w-fit h-fit flex items-center gap-4">
                <div><div class="w-[10px] h-[10px] rounded-[3px] bg-custom-purple"></div></div>
                <p>{{ carrier }}</p>
            </div>
        </div>

        <div class="w-full h-fit grid grid-cols-3 gap-6">
            <button @click="$emit('force')" class="w-full h-[48px] col-span-2 text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">Finalize Anyways</button>
            <button @click="$emit('close'); $router.replace({params: {category: 'carriers'}})" class="w-full h-[48px] text-[16px] text-white font-semibold bg-custom-black rounded-[4px] shadow-newdrop">Enter UIP(s)</button>
        </div>
    </div>
</template>

<script>
import { XMarkIcon } from '@heroicons/vue/24/solid'

export default {
    name: "Incomplete Rocket Carrier UIPs",
    props: {
        carriers: Array
    },
    components: {
        XMarkIcon
    }
}
</script>