import './bootstrap'
import {createApp} from 'vue'
import App from './App.vue'
import router from './router.js'
import '../css/tailwind.css'
import '../css/app.css'

// Notifications
import Notifications from '@kyvg/vue3-notification'

// Lottie
import Vue3Lottie from 'vue3-lottie'
import 'vue3-lottie/dist/style.css'

// Prime Vue
import PrimeVue from 'primevue/config'
import 'primeicons/primeicons.css';
import ToastService from 'primevue/toastservice';

createApp(App).use(router).use(PrimeVue).use(ToastService).use(Notifications, { name: "alert" }).use(Vue3Lottie, { name: 'LottieAnimation' }).mount("#app")