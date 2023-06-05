<template>
    <div class="w-full h-full grid overflow-y-scroll">
        <!-- Notifications Quick View -->
        <div v-if="view == 'all'" class="w-full h-fit grid grid-cols-2 gap-6 p-2">
            <!-- Left Side Systems -->
            <div class="w-full h-fit grid gap-4">
                <div v-for="(system, index) in systems" :key="index" class="w-full h-fit grid col gap-6">
                    <div v-if="index % 2 == 0 ? true : false" class="w-full h-fit grid gap-4 p-4 text-custom-black bg-white rounded-[4px] shadow-newdrop">
                        <div class="w-full h-full flow-root">
                            <!-- System Name -->
                            <div class="w-fit h-full flex items-center gap-4 float-left">
                                <Icon :icon="systemIcons[system]" height="24" class="text-custom-purple" />
                                <p class="text-[24px] font-medium">{{ names[system] }}</p>
                            </div>

                            <!-- View all system Notifications -->
                            <div class="w-fit h-full flex items-center float-right">
                                <button @click="view = system" class="w-fit h-fit  text-[16px] text-custom-purple flex items-center gap-4">View all <Icon :icon="'material-symbols:arrow-back-rounded'" height="20" class="text-custom-purple rotate-180" /></button>
                            </div>
                        </div>

                        <div v-for="(notification, notificationIndex) in notifications[system].slice(0,3)" :key="notificationIndex" class="w-full h-fit grid gap-2 p-4 pb-10 pl-6 bg-opacity-[8%] shadow-newdrop rounded-[2px] relative" :class="notification.type == 'success' ? 'bg-custom-green' : (notification.type == 'error' ? 'bg-custom-red' : 'bg-custom-orange')">
                            <div class="h-full w-[6px] rounded-tl-[2px] rounded-bl-[2px] left-0 absolute" :class="notification.type == 'success' ? 'bg-custom-green' : (notification.type == 'error' ? 'bg-custom-red' : 'bg-custom-orange')"></div>

                            <div class="w-fit h-fit flex items-center gap-4">
                                <Icon :icon="notificationIcons[notification.type]" height="24" :class="notification.type == 'success' ? 'text-custom-green' : (notification.type == 'error' ? 'text-custom-red' : 'text-custom-orange')" />
                                <p class="text-[16px] font-medium">{{ notification.header }}</p>
                            </div>

                            <p class="text-[14px]">{{ notification.body }}</p>

                            <p class="text-[12px] opacity-50 right-4 bottom-4 absolute">{{ moment(notification.created_at).format('ddd MMM Do YYYY, h:mm a') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side Systems -->
            <div class="w-full h-fit grid gap-4">
                <div v-for="(system, index) in systems" :key="index" class="w-full h-fit grid col gap-6">
                    <div v-if="index % 2 == 0 ? false : true" :class="index == 1 ? 'mt-[-15px]' : ''" class="w-full h-fit grid gap-4 p-4 text-custom-black bg-white rounded-[4px] shadow-newdrop">
                        <div class="w-full h-full flow-root">
                            <!-- System Name -->
                            <div class="w-fit h-full flex items-center gap-4 float-left">
                                <Icon :icon="systemIcons[system]" height="24" class="text-custom-purple" />
                                <p class="text-[24px] font-medium">{{ names[system] }}</p>
                            </div>

                            <!-- View all system Notifications -->
                            <div class="w-fit h-full flex items-center float-right">
                                <button @click="view = system" class="w-fit h-fit  text-[16px] text-custom-purple flex items-center gap-4">View all <Icon :icon="'material-symbols:arrow-back-rounded'" height="20" class="text-custom-purple rotate-180" /></button>
                            </div>
                        </div>

                        <div v-for="(notification, notificationIndex) in notifications[system].slice(0,3)" :key="notificationIndex" class="w-full h-fit grid gap-2 p-4 pb-10 pl-6 bg-opacity-[8%] shadow-newdrop rounded-[2px] relative" :class="notification.type == 'success' ? 'bg-custom-green' : (notification.type == 'error' ? 'bg-custom-red' : 'bg-custom-orange')">
                            <div class="h-full w-[6px] rounded-tl-[2px] rounded-bl-[2px] left-0 absolute" :class="notification.type == 'success' ? 'bg-custom-green' : (notification.type == 'error' ? 'bg-custom-red' : 'bg-custom-orange')"></div>

                            <div class="w-fit h-fit flex items-center gap-4">
                                <Icon :icon="notificationIcons[notification.type]" height="24" :class="notification.type == 'success' ? 'text-custom-green' : (notification.type == 'error' ? 'text-custom-red' : 'text-custom-orange')" />
                                <p class="text-[16px] font-medium">{{ notification.header }}</p>
                            </div>

                            <p class="text-[14px]">{{ notification.body }}</p>

                            <p class="text-[12px] opacity-50 right-4 bottom-4 absolute">{{ moment(notification.created_at).format('ddd MMM Do YYYY, h:mm a') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Notifications View -->
        <div v-else class="w-full h-fit grid gap-4">
            <div class="w-fit h-fit flex items-center gap-6">
                <button @click="view = 'all'" class="w-[24px] h-[24px] grid bg-white rounded-[2px] shadow-newdrop"><Icon :icon="'material-symbols:arrow-back-rounded'" height="18" class="text-custom-purple m-auto" /></button>
                <p class="text-[16px] font-medium text-custom-black">Notification/<span class="text-custom-purple">{{ names[view] }}</span></p>
            </div>
        
            <div class="w-fll h-fit grid gap-4 p-4 bg-white rounded-[4px] border-[1px] border-custom-black border-opacity-10">
                <div v-for="(notification, notificationIndex) in notifications[view]" :key="notificationIndex" class="w-full h-fit grid gap-2 p-4 pb-10 pl-6 bg-opacity-[8%] shadow-newdrop rounded-[2px] relative" :class="notification.type == 'success' ? 'bg-custom-green' : (notification.type == 'error' ? 'bg-custom-red' : 'bg-custom-orange')">
                    <div class="h-full w-[6px] rounded-tl-[2px] rounded-bl-[2px] left-0 absolute" :class="notification.type == 'success' ? 'bg-custom-green' : (notification.type == 'error' ? 'bg-custom-red' : 'bg-custom-orange')"></div>

                    <div class="w-fit h-fit flex items-center gap-4">
                        <Icon :icon="notificationIcons[notification.type]" height="24" :class="notification.type == 'success' ? 'text-custom-green' : (notification.type == 'error' ? 'text-custom-red' : 'text-custom-orange')" />
                        <p class="text-[16px] font-medium">{{ notification.header }}</p>
                    </div>

                    <p class="text-[14px]">{{ notification.body }}</p>

                    <p class="text-[12px] opacity-50 right-4 bottom-4 absolute">{{ moment(notification.created_at).format('ddd MMM Do YYYY, h:mm a') }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingAnimation from '../../../assets/cpuLoading.json'
import moment from 'moment'
import { Icon } from '@iconify/vue';

export default {
    name: "Reminder Notifications",
    data(){
        return {
            moment,
            LoadingAnimation,
            notifications: {},
            systems: [],
            view: 'all',
            systemIcons: {
                'system': 'gala:settings',
                'pipeline': 'icon-park-solid:connection-point',
                'notebooks': 'icon-park-solid:notes',
                'react': 'heroicons-solid:document-report',
                'onboarding': 'solar:documents-line-duotone'
            },
            names: {
                'system': 'System',
                'pipeline': 'Pipeline',
                'notebooks': 'Notebooks',
                'react': "REACT",
                'onboarding': "MGA Onboarding"
            },
            notificationIcons: {
                'info': 'mdi:information-variant-circle',
                'success': 'material-symbols:check-circle-rounded',
                'error': 'mdi:error'
            }
        }
    },
    async mounted(){
        this.$emit('loading')
        this.moment = moment

        await axios.get('/api/notifications')
        .then(response => {
            this.systems = Object.keys(response.data.notifications)
            this.notifications = response.data.notifications
        })
        this.$emit('loading')
    },
    methods: {
        async read(id){
            await axios.get('/api/notification/read/'+id)
            .then(response => {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Notification',
                    detail: response.data.message,
                    life: 2500
                })
            })
        }
    },
    components: {
        Icon
    }
}
</script>