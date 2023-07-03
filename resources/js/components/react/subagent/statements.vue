<template>
    <LottieAnimation :animationData="NotFoundAnimation" :renderer="'canvas'" :width="'250px'" :height="'250px'" :speed="0.75" class="m-auto" />
</template>

<script>
import NotFoundAnimation from '../../../../assets/newNotFound.json'

export default {
    name: "Sub Agent - Statements",
    data(){
        return{
            NotFoundAnimation
        }
    }
}
</script>