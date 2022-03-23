<template>
    <frontend-layout>
        <template #header>
            Login
        </template>

        <p class="text-center mb-4">
            Admin Dashboard
        </p>
        <form @submit.prevent="submit" class="declaration-form-box">
            <div class="mb-3">
                <jetstream-input v-model="email" placeholder="Email Address" name="email" autocomplete="off" :class="' declaration-form-control'" />
            </div>
            <div class="mb-4">
                <jetstream-input v-model="password" type="password" placeholder="Password" name="password" autocomplete="off"  :class="' declaration-form-control'"/>
            </div>
            <div class="d-flex  justify-content-between align-items-center">
                <div class="btn-box">
                    <secondary-button type="submit" >Login</secondary-button>
                </div>

                <div class="">
                    <jet-nav-link  :href="'/admin/password/reset'" :class="'forget-pass-text'">Forgot Your Password?</jet-nav-link>
                </div>
            </div>
        </form>
        <jet-input-error :message="incorrectIp" />
        <jet-input-error :message="error" />
        <jet-input-success :message="loggedOut" />
    </frontend-layout>
</template>

<script>

import FrontendLayout from "Layout/FrontendLayout";
import JetstreamInput from "Jetstream/Input";
import PrimaryButton from "Component/Buttons/Primary";
import SecondaryButton from "Component/Buttons/Secondary";
import JetNavLink from 'Jetstream/NavLink';
import JetInputError from 'Jetstream/InputError';
import JetInputSuccess from 'Jetstream/InputSuccess';

export default {
    components: {
        FrontendLayout,
        JetstreamInput,
        PrimaryButton,
        SecondaryButton,
        JetNavLink,
        JetInputError,
        JetInputSuccess
    },
    props: [
        'incorrectIp',
        'error',
        'loggedOut'
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
            this.$inertia.post('/admin/login/authenticate', {email: this.email, password: this.password});
        }
    }
}
</script>
