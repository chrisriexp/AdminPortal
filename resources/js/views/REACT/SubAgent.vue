<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Top Nav -->
    <div class="absolute z-30 w-full">
        <topNav class="fixed z-30" :active="'modules'" />
    </div>

    <!-- View Carrier Codes -->
    <div v-if="carrier_codes" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-40 fixed">
        <viewCarrierCodes class="m-auto" @close="carrier_codes = false" :rocket_id="agency.rocket_id" />
    </div>

    <div class="w-full h-screen z-20 absolute">
        <!-- Sidebar -->
        <div class="w-[305px] h-full grid bg-sidebar-bg z-20 absolute">
            <div class="w-full max-h-[800px] h-fit grid gap-4 mt-[100px] px-6 overflow-y-scroll text-[16px] text-custom-black font-medium">
                <!-- See Sub Agent Report -->
                <button @click="$router.replace({params: {category: 'report'}})" :class="active == 'report' ? 'bg-[#D9D9D9] bg-opacity-30' : ''" class="w-full h-[48px] grid px-6 rounded-[4px]">
                    <div :class="active == 'report' ? '' : 'opacity-60'" class="w-fit h-fit my-auto flex items-center gap-4">
                        <Icon :icon="'heroicons-solid:document-report'" height="24" />
                        <p>Report</p>
                    </div>
                </button>

                <!-- See Sub Agent Statements -->
                <button @click="$router.replace({params: {category: 'statements'}})" :class="active == 'statements' ? 'bg-[#D9D9D9] bg-opacity-30' : ''" class="w-full h-[48px] grid px-6 rounded-[4px]">
                    <div :class="active == 'statements' ? '' : 'opacity-60'" class="w-fit h-fit my-auto flex items-center gap-4">
                        <Icon :icon="'ic:round-insert-drive-file'" height="24" />
                        <p>Statements</p>
                    </div>
                </button>
            </div>
        </div>

        <div class="w-full h-full z-10 absolute pt-[110px] pl-[350px] pr-[50px] overflow-y-scroll">
            <div class="w-full h-full">
                <component
                    :is="view"
                    @loading="loading = !loading"
                    @viewCarrierCodes="viewCarrierCodes"
                ></component>
            </div>
        </div>
    </div>
</template>

<script>
import {defineAsyncComponent} from "vue";

import topNav from '../../components/topNav.vue'
import loading from '../../components/loading.vue'
import { Icon } from '@iconify/vue';

import viewCarrierCodes from '../../components/react/viewCarrierCodes.vue'

export default {
    name: "REACT - Sub Agent",
    data(){
        return{
            loading: true,
            active: "",
            view: null,
            carrier_codes: false,
            agency: {}
        }
    },
    async created(){
        this.active = this.$route.params.category
        this.view = defineAsyncComponent(() => import(`../../components/react/subagent/${this.active}.vue`))

        this.loading = false
    },
    watch: {
        '$route.params.category': async function(value){
            this.active = value
            this.view = defineAsyncComponent(() => import(`../../components/react/subagent/${this.active}.vue`))
        }
    },
    methods: {
        async viewCarrierCodes(data){
            this.agency = data
            this.carrier_codes = true
        }
    },
    components: {
        topNav,
        loading,
        Icon,
        viewCarrierCodes
    }
}
</script>