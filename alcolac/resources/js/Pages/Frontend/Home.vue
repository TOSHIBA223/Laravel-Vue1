<template>
    <frontend-layout>
        <template #header>
                Declaration System
        </template>

        <p v-if="systemSuccess"
           class="font-weight-bold text-green-500">
            {{systemSuccess}}
        </p>

        <p class="text-center mb-4">
            Add your unique code below to access your declaration
        </p>
        <form @submit.prevent="submit" class="declaration-form-box" style="max-width:320px;">
            <div class="text-center">
                <jetstream-input v-model="code" placeholder="Code" name="code" class="declaration-form-control" />
            </div>
            <div class="button-wrapper text-center mt-8">
                <secondary-button type="submit" >Start Now</secondary-button>
            </div>
        </form>
        <p class="text-center text-sm mt-4">
            Can't find your declaration code? <jet-nav-link :href="'/new-declaration'" :class="''">Send me a new code</jet-nav-link>
        </p>
    </frontend-layout>
</template>

<script>

    import FrontendLayout from "../../Layouts/FrontendLayout";
    import JetstreamInput from "../../Jetstream/Input";
    import PrimaryButton from "../../Components/Buttons/Primary";
    import SecondaryButton from "../../Components/Buttons/Secondary";
    import JetNavLink from '../../Jetstream/NavLink';

    export default {
        components: {
            FrontendLayout,
            JetstreamInput,
            PrimaryButton,
            SecondaryButton,
            JetNavLink
        },
        props: [
            'systemSuccess'
        ],
        data() {
            return {
                code: ''
            }
        },
        methods: {
            submit()
            {
                this.$inertia.visit('/dec/' + this.code, {method: 'GET'});
            }
        }
    }
</script>
