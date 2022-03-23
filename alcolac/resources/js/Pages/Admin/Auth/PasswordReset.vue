<template>
    <frontend-layout>
        <template #header>
            Reset Password
        </template>

        <p class="text-center mb-4">
            Enter your email below
        </p>
        <form @submit.prevent="submit" class="declaration-form-box mx-auto" style="max-width:400px;">
            <div class=" mb-3 mb-md-4">
                <jetstream-input v-model="email" placeholder="Email Address" name="email" autocomplete="off" :class="'declaration-form-control'" />
            </div>
            <div class="d-block text-center">
                <secondary-button class="w-full" type="submit" >Reset Password</secondary-button>
            </div>
        </form>
        <jet-input-error :message="errors.email" />
    </frontend-layout>
</template>

<script>

import FrontendLayout from "../../../Layouts/FrontendLayout";
import JetstreamInput from "../../../Jetstream/Input";
import PrimaryButton from "../../../Components/Buttons/Primary";
import SecondaryButton from "../../../Components/Buttons/Secondary";
import JetInputError from 'Jetstream/InputError';

export default {
    components: {
        FrontendLayout,
        JetstreamInput,
        PrimaryButton,
        SecondaryButton,
        JetInputError
    },
    props: [
        'errors'
    ],
    data() {
        return {
            email: '',
            password: ''
        }
    },
    methods: {
        submit()
        {
            this.$inertia.post('/admin/password/reset', {email: this.email});
        }
    }
}
</script>
