<template>
    <div>
        <h3 :class="'text-center font-weight-bold '+color+' '+font+'px'"  v-bind:style="{ 'color': color, 'font-size':font+'px' }"
            >
            {{message}}
        </h3>
        <p class="text-center">Having issues with this declaration? Please contact your manager or HR for assistance.</p>
    </div>
</template>

<script>
import PrimaryButton from "../Buttons/Primary";
import DangerButton from "../Buttons/Danger";
export default {
    components: {
        PrimaryButton,
        DangerButton
    },
    props: [
        'message',
        'success',
        'color',
        'font'
    ],
    data() {
        return{
            formattedAddress: null,
            formattedNewAddress: null
        }
    },
    mounted()
    {
    },
    methods: {
    },
    watch: {
    }
}
</script>
