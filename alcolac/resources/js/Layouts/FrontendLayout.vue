<template>
    <div class="min-h-screen w-full bg-gray-100 flex justify-center align-items-md-center">
        <!-- Page Content -->
        <div class="max-w-4xl w-full flex flex-wrap m-auto mt-0 md:flex-no-wrap shadow-md border rounded overflow-hidden">
            <main class="order-2  declaration-new-form-section md:order-1  w-full md:w-4/6 bg-white py-5 px-6 flex flex-col justify-center items-center">
                <header class="text-center">
                    <h2 class="font-semibold text-4xl">
                        <slot name="header"></slot>
                    </h2>
                </header>
                <slot></slot>
            </main>
            <div class="order-1 md:order-2 declaration-logo-cal w-full md:w-2/6 bg-gray-50 p-3 p-md-5  border-l flex justify-content-center">
                <jet-nav-link :href="'/'" :class="''">
                    <img src="/media/company/LambCo.png" />
                </jet-nav-link>
            </div>
        </div>
    </div>
</template>

<script>

    import JetNavLink from '../Jetstream/NavLink';
    export default {
        components: {
            JetNavLink,
        },

        methods: {
        },

        computed: {
            path() {
                return window.location.pathname
            }
        }
    }
</script>
