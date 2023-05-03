<template>
    <div class="grid gap-[4px] h-fit">
        <label v-if="!showLabel ? '' : 'hidden'" class="text-[16px] text-custom-light-gray font-medium">{{ label }} <span v-if="required" class="text-custom-red">*</span></label>
        <input
            v-if="date"
            v-model="v$.inputData.$model"
            @change="change($event)"
            type='date'
            :disabled="isdisabled"
            :id="id"
            :class="v$.inputData.$error ? 'inner-border-custom-red focus:inner-border-custom-red' : ''"
            class="w-full h-[{{ height }}] text-[15px] text-custom-gray rounded-[2px] inner-border-[1px] inner-border-custom-light border-none focus:ring-0 disabled:bg-custom-bg"
        >
        <input
            v-else-if="password"
            v-model="v$.inputData.$model"
            @change="change($event)"
            type='password'
            :id="id"
            :placeholder="placeholderText"
            :class="v$.inputData.$error ? 'inner-border-custom-red focus:inner-border-custom-red' : ''"
            class="w-full h-[{{ height }}] text-[15px] text-custom-gray rounded-[2px] inner-border-[1px] inner-border-custom-light border-none focus:ring-0 disabled:bg-custom-bg"
        >
        <input
            v-else-if="email"
            v-model="v$.inputData.$model"
            @change="change($event)"
            type='email'
            :id="id"
            :placeholder="placeholderText"
            :class="v$.inputData.$error ? 'inner-border-custom-red focus:inner-border-custom-red' : ''"
            class="w-full h-[{{ height }}] text-[15px] text-custom-gray rounded-[2px] inner-border-[1px] inner-border-custom-light border-none focus:ring-0 disabled:bg-custom-bg"
        >
        <input
            v-else
            v-model="v$.inputData.$model"
            @change="change($event)"
            type='text'
            :disabled="isdisabled"
            :id="id"
            :placeholder="placeholderText"
            :class="v$.inputData.$error ? 'inner-border-custom-red focus:inner-border-custom-red' : ''"
            class="w-full h-[{{ height }}] text-[15px] text-custom-gray rounded-[2px]  inner-border-[1px] inner-border-custom-light border-none focus:ring-0 disabled:bg-custom-bg"
        >
    </div>
</template>

<script>
import { useVuelidate } from '@vuelidate/core'
import { required, email, helpers} from '@vuelidate/validators'

export default {
    setup() {
        return {
            v$: useVuelidate()
        }
    },
    created() {
        if(this.required){
            let validRules = {}

            if(this.required){
                validRules = {
                    required: helpers.withMessage(`${this.label} is required`, required)
                }

                if(this.email){
                    validRules = {
                        required: helpers.withMessage(`${this.label} is required`, required),
                        email: helpers.withMessage(`Please enter a valid email.`, email)
                    }
                }
            }

            this.valid = validRules
        }

        this.inputData = this.inputValue
    },
    data() {
        return {
            inputData: '',
            valid: {}
        }
    },
    props: {
        id: String,
        showLabel: {
            type: Boolean,
            default: true
        },
        label: String,
        placeholderText: String,
        index: String,
        height: {
            type: String,
            default: '35px'
        },
        inputValue: {
            type: String,
            default: ''
        },
        isdisabled: {
            type: Boolean,
            default: false
        },
        date: {
            type: Boolean,
            default: false
        },
        password: {
            type: Boolean,
            default: false
        },
        required: {
            type: Boolean,
            default: true
        },
        email: {
            type: Boolean,
            default: false
        }
    },
    watch: {
        inputValue(newValue, oldValue) {
            this.inputData = newValue
        }
    },
    methods: {
        change(e){
            let errors = []

            this.v$.inputData.$errors.forEach(error => {
                errors.push(error.$message)
            })

            this.$emit('inputUpdate', e.target.id, this.inputData, errors, this.index)
        }
    },
    validations () {
        return {
            inputData: this.valid,
        }
    },
}
</script>