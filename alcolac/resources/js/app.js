require('./bootstrap');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import { VueFormBuilderPlugin } from 'v-form-builder';
import Toast from "vue-toastification";
import vSelect from 'vue-select';

// install now
Vue.use(VueFormBuilderPlugin)
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.component('v-select', vSelect);

const options = {
    draggable: false
}
Vue.use(Toast, options);

const app = document.getElementById('app');

new Vue({
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);

Vue.mixin({
    methods: {
        pad(n, width) {
            n = n + '';
            return n.length >= width ? n : new Array(width - n.length + 1).join(0) + n;
        }
    }
})