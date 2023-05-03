<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <topNav :active="'dashboard'" @loading="loading = !loading" />
</template>

<script>
import axios from 'axios';
import topNav from '../components/topNav.vue'
import loading from '../components/loading.vue'

export default{
    name: "Dashboard",
    data(){
        return {
            loading: true,
        }
    },
    async created(){
        await axios.get('/api/user')
        .then(response => {

        })

        this.loading = false
    },
    components: {
        topNav,
        loading
    }
}
</script>