<template>
    <div>
        <div v-if="addressValidation">
            <div id="new-address"
                 class="text-center"
                 v-if="newAddress && newAddress.address != oldAddress">
                <p>You changed your address from:</p>
                <p class="text-green-500 text-lg mb-0">{{oldAddress}}</p>
                <p class="mb-0">To:</p>
                <p class="text-red-500 text-lg mb-0">{{newAddress.address}}</p>
            </div>
        </div>

        <div v-for="(answer, index) in answers" class="mb-2 mb-md-3 survey-que-box">
            <h3 class="que-title no-bar" v-if="answer.label">{{answer.label}}</h3>
            <h3 class="que-defination mb-2">{{answer.definition}}</h3>
            <p class="font-weight-bold text-lg mb-0"
                >Your Answer <span :class="answer.failed ? 'text-red-500' : 'text-green-500'">{{answer.answer}}</span></p>
        </div>

        <div class="grid grid-cols-2 gap-x-4 declaration-form-box">
            <secondary-button class="w-full" @buttonClick="submit" v-bind:disabled="clicked">
                Submit
            </secondary-button>
            <danger-button class="w-full" @buttonClick="restart" v-bind:disabled="clicked">
                Restart
            </danger-button>
        </div>
    </div>
</template>

<script>
import PrimaryButton from "../Buttons/Primary";
import SecondaryButton from "../Buttons/Secondary";
import DangerButton from "../Buttons/Danger";
export default {
    components: {
        PrimaryButton,
        SecondaryButton,
        DangerButton
    },
    props: [
        'answers',
        'currentAddress',
        'newAddress',
        'addressValidation',
        'tot',
        'oldAddress'
    ],
    data() {
        return{
            formattedAddress: null,
            formattedNewAddress: null,
            clicked: false,

        }
    },
    mounted()
    {
        console.log('Running Review Mounting setup');





    },
    methods: {
        submit()
        {
            if (this.clicked) {
                return;
            }
            this.clicked = true;
            this.$emit('submit');
        },
        restart()
        {
            if (this.clicked) {
                return;
            }
            this.clicked = true;
            this.$emit('restart');
        }
    },
    watch: {
    }
}
</script>
