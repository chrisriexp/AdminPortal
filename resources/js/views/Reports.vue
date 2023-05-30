<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Top Nav -->
    <div class="absolute z-30 w-full">
        <topNav class="fixed z-30" />
    </div>

    <div class="w-full h-screen z-20 absolute">
        <!-- Sidebar -->
        <div class="w-[305px] h-full grid bg-sidebar-bg z-20 absolute">
            <div class="w-full max-h-[800px] h-fit grid gap-4 mt-[100px] px-6 overflow-y-scroll text-[16px]">
                <div class="w-full h-fit grid justify-items-center text-custom-black opacity-50">
                    <p class="w-fit h-full flex gap-4 items-center text-[24px] font-semibold"><Icon icon="heroicons-solid:document-report" height="24" /> Reports</p>
                </div>

                <!-- User Profile -->
                <div v-for="(report, index) in reports_menu" :key="index" @click="$router.replace({params: {category: report.code}})" :class="category == report.code ? 'bg-[#F1F1F1]' : 'opacity-60'" class="w-full h-[48px] px-4 flex items-center gap-4 rounded-[4px] font-medium text-[16px] cursor-pointer">
                    <Icon :icon="report.icon" height="24" />
                    <p>{{ report.name }}</p>
                </div>
            </div>
        </div>

        <div class="w-full h-full z-10 absolute pt-[110px] pl-[350px] pr-[50px] overflow-y-scroll">
            <div class="w-full h-full">
                <component
                    :is="view"
                    @loading="loading = !loading"
                ></component>
            </div>
        </div>
    </div>
</template>

<script>
import {defineAsyncComponent} from "vue";
import topNav from '../components/topNav.vue'
import loading from '../components/loading.vue'
import reports_menu from '../../assets/reports.json'

import { Icon } from '@iconify/vue';

export default {
    name: "Reports",
    data(){
        return{
            role: "",
            loading: true,
            reports_menu,
            category: "",
            view: null,
        }
    },
    async created(){
        this.role = this.$route.meta.role
        this.category = this.$route.params.category

        this.view = defineAsyncComponent(() => import(`../components/reports/${this.category}.vue`))

        this.loading = false
    },
    watch: {
        '$route.params.category': async function (value) {
            this.view = defineAsyncComponent(() => import(`../components/reports/${value}.vue`))
            this.category = value
        },
    },
    components: {
        topNav,
        loading,
        Icon
    }
}
</script>