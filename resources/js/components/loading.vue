<template>
  <div class="w-fit h-fit grid">
    <LottieAnimation :animationData="LoadingAnimation" :width="'250px'" :height="'250px'" :speed="1" class="h-screen m-auto" />
  </div>
</template>

<script>
import LoadingAnimation from '../../assets/cpuLoading.json'

export default {
  name: "loading",
  data(){
    return {
      LoadingAnimation
    }
  }
}
</script>