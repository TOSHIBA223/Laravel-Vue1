<template>
    <button :type="type"
            @click="$emit('buttonClick', $event.target.value)"
            class="inline-flex bg-green-400 hover:bg-green-600 justify-center items-center px-6 py-3 border border-transparent font-semibold text-lg text-white tracking-widest hover:opacity-50 active:opacity-50 focus:outline-none transition ease-in-out duration-300">
        <slot></slot>
    </button>
</template>

<script>
    export default {
        props: {
            type: {
                type: String,
                default: 'submit',
            },
        }
    }
</script>
