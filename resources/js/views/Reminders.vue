<template>
    <div class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed p-24">
        <div class="w-full h-full m-auto flex bg-white dark:bg-custom-gray-bg rounded-[5px] relative shadow-customdrop">
            <button @click="$emit('close-reminder')" class="z-30 absolute top-4 left-4 text-custom-gray dark:text-white hover:text-custom-red dark:hover:text-red-500"><XCircleIcon class="h-[32px]" /></button>

            <div class="w-[70px] h-full grid bg-custom-bg dark:bg-custom-dark-bg rounded-tl-[5px] rounded-bl-[5px] z-20">
                <div class="w-fit h-fit m-auto grid gap-6 text-custom-gray dark:text-white">
                    <div @click="active = 'tasks'" :class="active == 'tasks' ? 'bg-custom-dark-blue dark:bg-custom-gray dark:bg-opacity-50 text-white shadow-customdrop' : ''" class="rounded-[4px] hover:bg-custom-dark-blue dark:hover:bg-custom-gray dark:hover:bg-opacity-50 hover:text-white hover:shadow-customdrop cursor-pointer"><QueueListIcon class="h-[24px] m-2" /></div>
                    <div @click="active = 'calendar'" :class="active == 'calendar' ? 'bg-custom-dark-blue dark:bg-custom-gray dark:bg-opacity-50 text-white shadow-customdrop' : ''" class="rounded-[4px] hover:bg-custom-dark-blue dark:hover:bg-custom-gray dark:hover:bg-opacity-50 hover:text-white hover:shadow-customdrop cursor-pointer"><CalendarDaysIcon class="h-[24px] m-2" /></div>
                    <div @click="active = 'notifications'" :class="active == 'notifications' ? 'bg-custom-dark-blue dark:bg-custom-gray dark:bg-opacity-50 text-white shadow-customdrop' : ''" class="rounded-[4px] hover:bg-custom-dark-blue dark:hover:bg-custom-gray dark:hover:bg-opacity-50 hover:text-white hover:shadow-customdrop cursor-pointer"><BellAlertIcon class="h-[24px] m-2" /></div>
                </div>
            </div>

            <div class="w-full h-full grid">
                <tasks v-if="active == 'tasks'" />
                <notifications v-if="active == 'notifications'" />
            </div>
        </div>
    </div>
</template>

<script>
import tasks from '../components/reminders/tasks.vue'
import notifications from '../components/reminders/notifications.vue'

import { QueueListIcon, CalendarDaysIcon, BellAlertIcon, XCircleIcon } from '@heroicons/vue/24/outline'

export default {
    name: "Reminders",
    data(){
        return{
            active: "tasks"
        }
    },
    components: {
        QueueListIcon,
        CalendarDaysIcon,
        BellAlertIcon,
        XCircleIcon,
        tasks,
        notifications
    }
}
</script>