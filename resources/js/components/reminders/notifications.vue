<template>
    <!-- Loading -->
    <div v-if="loading" class="w-full h-full grid bg-[#3F3F3F] bg-opacity-[26%] z-50 absolute ml-[-67px] rounded-[5px]">
        <div class="w-full h-full grid mt-[10%]">
            <LottieAnimation :animationData="LoadingAnimation" :width="'250px'" :height="'250px'" :speed="1" class="m-auto" />
        </div>
    </div>

    <div class="w-full h-fit max-h-[700px] p-4 flex flex-wrap gap-6 overflow-y-scroll">
        <div v-for="(system, index) in systems" :key="index" class="w-[425px] h-[500px] grid gap-4 p-4 border-[1px] border-custom-light dark:border-custom-gray bg-custom-bg dark:bg-custom-dark-bg rounded-[4px]">
            <div class="w-full h-fit flow-root text-custom-light-gray font-medium text-[16px]">
                <p class="float-left">{{ system }} notifications</p>
                <p class="flex items-center gap-2 float-right cursor-pointer">View All<i class="pi pi-arrow-circle-right"></i></p>
            </div>

            <div class="w-full h-[400px] grid overflow-y-scroll">
                <div class="w-full h-fit grid gap-4">
                    <div  @click="read(notification.id, index)" v-for="(notification, notificationIndex) in notifications[system]" :key="notificationIndex" class="w-full h-fit p-2 grid gap-2 text-custom-gray dark:text-white text-[14px] bg-white dark:bg-custom-light-gray-bg border-[1px] border-custom-red rounded-[4px] cursor-pointer">
                        <p class="text-custom-light-gray float-left font-medium">{{ notification.header }}</p>
                        <p class="text-custom-dark-blue dark:text-white">{{ notification.body }}</p>
                        <p class="float-right font-light">{{ moment(notification.created_at).format('dddd, MMMM Do YYYY, h:mm a') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingAnimation from '../../../assets/cpuLoading.json'
import moment from 'moment'

export default {
    name: "Reminder Notifications",
    data(){
        return {
            loading: false,
            moment,
            LoadingAnimation,
            notifications: {},
            systems: []
        }
    },
    async mounted(){
        this.moment = moment

        await axios.get('/api/notifications')
        .then(response => {
            this.systems = Object.keys(response.data.notifications)
            this.notifications = response.data.notifications
        })
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
    }
}
</script>