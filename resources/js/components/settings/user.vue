<template>
    <div class="w-full h-fit grid p-10">
        <div class="w-full h-fit grid p-4 rounded-[6px] bg-white dark:bg-custom-dark-bg">
            <p class="text-[24px] font-medium text-custom-dark-blue dark:text-white">Welcome {{ user.name }}!</p>
            
            <!-- Name -->
            <div class="">

            </div>
        </div>
    </div>
</template>

<script>

export default{
    name: "User Profile Settings",
    data(){
        return{
            user: {}
        }
    },
    async mounted() {
        await axios.get('/api/user')
        .then(response => {
            this.user = response.data
        })

        console.log(this.$route.meta.role)
    },
    methods: {

    }
}
</script>