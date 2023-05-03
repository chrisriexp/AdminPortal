<template>
    <div class="w-full h-screen bg-[#F9F6F0]">
        <div class="grid gap-12 w-fit h-fit mx-auto pt-24 justify-items-center">
            <img src="../../assets/404.svg" alt="404" class="h-[300px]">

            <div class="grid gap-2 justify-items-center">
                <h1 class="text-4xl font-bold text-custom-dark-blue text-center">Oops, page not found.</h1>
                <p class="text-custom-dark-blue text-xl font-medium">The page you were looking for was moved or does not exist</p>
                
                <router-link to="/" class="p-4 mt-6 rounded-lg bg-custom-red text-white uppercase font-bold flex gap-4"><ArrowUturnLeftIcon class="h-6 my-atuo" /> <span class="my-auto">Back to Home</span></router-link>
            </div>
        </div>
    </div>
</template>

<script>
import { ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'

export default{
    name: "NotFound",
    components: {
        ArrowUturnLeftIcon
    }
}
</script>