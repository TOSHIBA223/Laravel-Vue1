<template>
    <input class="form-input focus:border-black focus:shadow-none border-black rounded-md shadow-sm"
           :value="value" @input="$emit('input', $event.target.value)" ref="input">
</template>

<script>
    export default {
        props: ['value'],

        methods: {
            focus() {
                this.$refs.input.focus()
            }
        }
    }
</script>

