<template>
    <!-- Loading -->
    <div v-if="loading" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <div class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <div class="w-[1176px] h-[691px] m-auto p-6 grid bg-sidebar-bg rounded-[4px] relative shadow-customdrop">
            <!-- Header -->
            <div class="w-full h-fit grid grid-cols-5">
                <div class="w-full h-full flex items-center">
                    <p class="text-[24px] text-custom-black font-semibold">{{ active == 'tasks' ? 'Reminders' : 'Notifications' }}</p>
                </div>

                <!-- Nav Bar -->
                <div class="w-full h-full col-span-3 flex items-center">
                    <button @click="active = 'tasks'" :class="active == 'tasks' ? 'text-white bg-custom-purple' : 'text-custom-black opacity-50'" class="w-fit h-fit mx-auto flex items-center gap-4 text-[16px] font-medium px-4 py-[5px] rounded-[4px]"><Icon icon="ic:round-menu" height="24" class="m-auto" />Reminder</button>
                    <button @click="active = 'notifications'" :class="active == 'notifications' ? 'text-white bg-custom-purple' : 'text-custom-black opacity-50'" class="w-fit h-fit mx-auto flex items-center gap-4 text-[16px] font-medium px-4 py-[5px] rounded-[4px]"><Icon icon="mdi:bell-notification" height="24" class="m-auto" />Notification</button>
                </div>

                <!-- Close Reminders -->
                <div class="w-full h-full flow-root">
                    <div @click="$emit('close-reminder')" class="w-[39px] h-[39px] grid bg-white shadow-newdrop float-right cursor-pointer">
                        <Icon icon="charm:cross" height="24" class="m-auto text-custom-red" />
                    </div>
                </div>
            </div>

            <div class="w-full h-[570px] grid relative">
                <component @loading="loading = !loading" :is="settingVue"></component>
            </div>
        </div>
    </div>
</template>

<script>
import {defineAsyncComponent} from "vue";
import loading from "../components/loading.vue";
import { Icon } from '@iconify/vue';

export default {
    name: "Reminders",
    data(){
        return{
            loading: false,
            active: "tasks",
            settingVue: null
        }
    },
    async created(){
        this.settingVue = defineAsyncComponent(() => import(`../components/reminders/${this.active}.vue`))
    },
    watch: {
        active: async function () {
            this.settingVue = defineAsyncComponent(() => import(`../components/reminders/${this.active}.vue`))
        }
    },
    components: {
        Icon,
        loading
    }
}
</script>