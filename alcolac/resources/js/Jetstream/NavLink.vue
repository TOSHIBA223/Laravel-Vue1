<template>
    <inertia-link :href="href" :class="classes"  @click="AddActive(1)">
        <slot></slot>
    </inertia-link>
</template>

<script>
    export default {
        props: ['href', 'active'],

        computed: {
            classes() {
                return this.active
                            ? 'inline-flex items-center active'
                            : 'inline-flex items-center text-blue-600 hover:underline'
            }
        },
          methods:{
        AddActive(val){
            console.log(val);
        }
    }

    }
</script>
