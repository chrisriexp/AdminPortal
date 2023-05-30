<template>
    <router-view/>
    <notifications />
    <Toast />
</template>

<script>
import Toast from 'primevue/toast';
import { useDark, useToggle } from '@vueuse/core';

const isDark = useDark();
const toggleDark = useToggle(isDark);

export default {
  name: "App",
  async created(){
    await axios.get('/api/user')
    .then(response => {
      if(response.data.theme == 'dark-mode'){
        this.darkMode = true
        toggleDark(darkMode)
        document.body.classList.add('bg-custom-gray-bg')
      }else{
        document.body.classList.add('bg-white')
      }

      this.$primevue.changeTheme('light-mode', response.data.theme, 'theme-link', () => {});
    })
  },
  data(){
    return{
      darkMode: false
    }
  },
  components: {
    Toast
  }
}
</script>

<style>
/**Font from google font */
#app {
  font-family: 'Montserrat', sans-serif;
}
/* body{
  background-color: #EFF3F6;
} */
@media (-webkit-device-pixel-ratio: 1.10) {
    :root {
      zoom: 0.90;
    }
}
/* Chrome, Safari, Edge, Opera */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
/* Firefox */
input[type="number"] {
    -moz-appearance: textfield;
}

input[type="text"]:focus{
  outline: none !important;
  --tw-ring-color: transparent !important;
}

input:focus{
  outline: none !important;
}

.p-editor-container .p-editor-toolbar {
    background: #fff;
}

.ql-snow .ql-editor pre.ql-syntax{
  color: #5BA3E0 !important;
}
</style>