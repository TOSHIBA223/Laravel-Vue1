<template>
    <label :for="name">
        <span v-if="!placement">{{label}}</span>
        <input type="checkbox"
               :name="name"
               :class="classNames"
               :checked="checked"
               @change="$emit('changeOption', $event.target.checked)">
        <span v-if="placement">{{label}}</span>
    </label>
</template>

<script>

    export default {
        props: [
            'label',
            'name',
            'labelPosition',
            'checked',
            'classNames'
        ],
        computed: {
            placement() {
                return this.labelPosition === 'right';
            }
        }
    }
</script>
