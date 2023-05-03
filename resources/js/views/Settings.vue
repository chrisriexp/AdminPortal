<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <topNav :active="''" @loading="loading = !loading" />

    <div class="w-full h-full grid grid-cols-5">
        <div class="w-[90%] h-screen grid">
            <div class="w-full h-full bg-white dark:bg-custom-light-gray-bg">
                <p class="flex gap-2 items-center p-4 text-[24px] text-custom-light-gray dark:text-white font-medium border-b-[1px] border-custom-light dark:border-custom-gray"><i style="font-size: 20px" class="pi pi-cog"></i>Settings</p>
                
                <!-- Setting Groups -->
                <ul class="w-full h-fit text-custom-dark-blue dark:text-white text-[16px] font-medium">
                    <li v-for="(setting, index) in settingsMenu" :key="index" @click="group = setting.code" :class="group == setting.code ? 'bg-custom-light-blue dark:bg-custom-gray-bg' : ''" class="w-full py-4 px-10 hover:bg-custom-light-blue dark:hover:bg-custom-gray-bg cursor-pointer">{{ setting.name }}</li>
                </ul>
            </div>
        </div>

        <div class="w-full h-fit grid col-span-4">
            <component @loading="loading = !loading" :is="settingVue"></component>
        </div>
    </div>
</template>

<script>
import {defineAsyncComponent} from "vue";

import axios from 'axios';
import topNav from '../components/topNav.vue'
import loading from '../components/loading.vue'
import settingsMenu from '../../assets/settings.json'

export default {
    name: "Settings",
    data(){
        return{
            loading: false,
            settingsMenu,
            group: "user",
            settings: [],
            settingVue: null
        }
    },
    async created(){
        this.loading = true

        this.settingVue = defineAsyncComponent(() => import(`../components/settings/${this.group}.vue`))

        this.loading = false
    },
    watch: {
        group: async function () {
            this.settingVue = defineAsyncComponent(() => import(`../components/settings/${this.group}.vue`))
        }
    },
    components: {
        topNav,
        loading
    }
}
</script>