<template>
    <frontend-layout>
        <template #header>
            Access Code
        </template>

        <p class="text-center mb-4">
            Admin Dashboard
        </p>
        <form @submit.prevent="submit" class="declaration-form-box mw-400" >
            <div class="mb-3 mb-4">
                <jetstream-input v-model="token" placeholder="Access Code" name="token" autocomplete="off" :class="'declaration-form-control'" />
            </div>
            
            <div class="btn-box w-full"> 
                    <secondary-button class="w-full" type="submit" >Verify</secondary-button>
            </div>
            
        </form>
        <jet-input-error :message="error" />
    </frontend-layout>
</template>

<script>

import FrontendLayout from "Layout/FrontendLayout";
import JetstreamInput from "Jetstream/Input";
import PrimaryButton from "Component/Buttons/Primary";
import SecondaryButton from "Component/Buttons/Secondary";
import JetNavLink from 'Jetstream/NavLink';
import JetInputError from 'Jetstream/InputError';

export default {
    components: {
        FrontendLayout,
        JetstreamInput,
        PrimaryButton,
        SecondaryButton,
        JetNavLink,
        JetInputError
    },
    props: [
        'error'
    ],
    data() {
        return {
            token: '',
        }
    },
    methods: {
        submit()
        {
            this.$inertia.post('/admin/login/finalise', {token: this.token});
        }
    }
}
</script>
