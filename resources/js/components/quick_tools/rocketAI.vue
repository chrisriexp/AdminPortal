<template>
    <div class="w-full h-full grid">
        <p class="mt-4 text-center text-[20px] text-custom-black font-medium opacity-60">Help develop our system AI by interacting with it as much as possible..try it now!!</p>
    </div>

    <div class="w-full h-[400px] grid p-6 bg-white border-[1px] border-custom-black border-opacity-10 rounded-[4px] overflow-y-scroll">
        <div class="w-full h-fit grid gap-2">
            <div v-for="(message, index) in data" :key="index" class="w-full h-fit flow-root">
                <p :class="message.type == 'prompt' ? 'float-right text-custom-black bg-sidebar-bg inner-border-[1px] inner-border-custom-black ' : 'float-left text-white bg-custom-purple'" class="w-fit h-fit p-2 text-[16px] font-medium rounded-[4px]">{{ message.text }}</p>
            </div>
        </div>
    </div>

    <div class="w-full h-fit flex items-center gap-8">
        <textarea v-model="prompt" name="prompt" placeholder="Enter prompt.." style="height: 80px; resize: none;" class="w-[80%] h-[48px] max-h-[80px] border-[1px] border-custom-black border-opacity-10 rounded-[4px] focus:ring-0 focus:border-custom-black focus:border-opacity-10 cursor-normal"></textarea>

        <button @click="runPrompt" class="w-[20%] h-[48px] text-[16px] text-white font-semibold bg-custom-purple rounded-[4px] shadow-newdrop">Ask DUZAL..</button>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';

export default {
    name: "Rocket AI",
    data(){
        return{
            prompt: "",
            data: []
        }
    },
    methods: {
        async runPrompt(){
            this.$emit('loading')
            if(this.prompt == ""){
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Prompt',
                    detail: "Please enter a prompt",
                    life: 2500
                })
                
                this.$emit('loading')
                return
            }

            await axios.post('/api/open-ai/quick-tools', {"prompt": this.prompt, "history": this.data})
            .then(response => {
                console.log(response)
                
                response.data.data.forEach(item => {
                    this.data.push(item)
                })

                this.prompt = ""
                this.$emit('loading')
            })
            .catch(error => {
                this.$emit('loading')

                if(error.response.status == 500){
                    this.$toast.add({
                        severity: 'error',
                        summary: 'AI Error',
                        detail: "Internal Server Erorr - Please contact a system admin",
                        life: 3000
                    })
                }
            })
        }
    },
    components: {
        InputText
    }
}
</script>