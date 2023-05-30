<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Top Nav -->
    <div class="absolute z-30 w-full">
        <topNav class="fixed z-30" />
    </div>

    <!-- Show Popup -->
    <div v-if="popupValue" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <component :is="showPopup" @loading="loading = !loading" @close="showPopup = null; popupValue = null" class="m-auto"></component>
    </div>


    <div class="w-full h-screen z-20 absolute">
        <div class="w-[305px] h-full grid bg-sidebar-bg z-20 absolute">
            <div class="w-full max-h-[800px] h-fit grid gap-4 mt-[100px] px-6 overflow-y-scroll text-[16px]">
                <div class="w-full h-fit grid justify-items-center text-custom-black opacity-50">
                    <p class="w-fit h-full flex gap-4 items-center text-[24px] font-semibold"><Icon icon="ic:round-settings" height="24" /> Settings</p>
                </div>

                <!-- User Profile -->
                <div v-for="(setting, index) in settingsMenu" :key="index" @click="group = setting.code" :class="[group == setting.code ? 'bg-[#F1F1F1]' : 'opacity-60', setting.code == 'users' ? (role == 'super-admin' ? '' : 'hidden') : '']" class="w-full h-[48px] px-4 flex items-center gap-4 rounded-[4px] font-medium text-[16px] cursor-pointer">
                    <Icon :icon="setting.icon" height="24" />
                    <p>{{ setting.name }}</p>
                </div>
            </div>
        </div>

        <div class="w-full h-full z-10 absolute pt-[110px] pl-[350px] pr-[50px] overflow-y-scroll">
            <div class="w-full h-full">
                <component
                    :is="settingVue"
                    @loading="loading = !loading"
                    @updateEmail="popupValue = 'updateEmail'"
                    @updatePassword="popupValue = 'updatePassword'"
                    @updateUserPassword="popupValue = 'updateUserPassword'"
                    @createUser="popupValue = 'createUser'"
                    @deleteUser="popupValue = 'deleteUser'"
                    @deleteProject="popupValue = 'deleteProject'"
                ></component>
            </div>
        </div>
    </div>
</template>

<script>
import {defineAsyncComponent} from "vue";

import axios from 'axios';
import topNav from '../components/topNav.vue'
import loading from '../components/loading.vue'
import settingsMenu from '../../assets/settings.json'

import { Icon } from '@iconify/vue';

export default {
    name: "Settings",
    data(){
        return{
            role: "",
            loading: false,
            settingsMenu,
            group: "user",
            settingVue: null,
            popupValue: null,
            showPopup: null
        }
    },
    async created(){
        this.loading = true

        this.role = this.$route.meta.role

        this.settingVue = defineAsyncComponent(() => import(`../components/settings/${this.group}.vue`))

        this.loading = false
    },
    async mounted(){
        this.group = this.$route.params.category
    },
    watch: {
        group: async function () {
            this.settingVue = defineAsyncComponent(() => import(`../components/settings/${this.group}.vue`))
            this.$router.replace({params: {category: this.group, id: null}})
        },
        popupValue: async function () {
            this.showPopup = defineAsyncComponent(() => import(`../components/settings/${this.popupValue}.vue`))
        }
    },
    components: {
        topNav,
        loading,
        Icon
    }
}
</script>
