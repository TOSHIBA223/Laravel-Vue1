<template>
    <app-layout>

        <transition enter-active-class="ease-out duration-300"
                    enter-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-class="opacity-100"
                    leave-to-class="opacity-0">
            <div class="bg-red-500 p-4 fixed left-0 right-0 top-0 text-center" v-if="errors.systemFail" @click="errors = false">
                <div class="max-w-xl w-full mx-auto">
                    <span class="text-white">
                        {{errors.systemFail}} x
                    </span>
                </div>
            </div>
            <div class="bg-green-400 p-4 fixed left-0 right-0 top-0 text-center" v-if="systemSuccess" @click="systemSuccess = false">
                <div class="max-w-xl w-full mx-auto">
                    <span class="text-white">
                        {{systemSuccess}} x
                    </span>
                </div>
            </div>
        </transition>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                IP Whitelist
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl p-12">
                    <div class="w-full mt-4">
                        <h3 class="clearfix mb-3">
                            <secondary-button type="button" :class="'float-right'"
                                            @buttonClick="showNewTemplate">
                                New IP Address
                            </secondary-button>
                        </h3>
                        <table class="table-auto w-full border border-gray-500" v-if="whitelist">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border-r border-gray-500">IP Address</th>
                                    <th class="px-4 py-2 border-r border-gray-500">Subnet</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in whitelist" :key="item.id"
                                    :class="[{'bg-gray-100': index % 2 !== 0, 'opacity-50': item.deleted_at !== null}]">
                                    <td class="border border-gray-500 px-4 py-2 text-center">{{item.ip_address}}</td>
                                    <td class="border border-gray-500 px-4 py-2 text-center">
                                        {{item.subnet}}
                                    </td>
                                    <td class="border border-gray-500 px-4 py-2 text-center">
                                        <i class="fa fa-edit text-green-400 mr-4 cursor-pointer"
                                           @click="editItem(item.id, item.ip_address, item.subnet)"></i>
                                        <i class="fa fa-minus-circle text-red-500 cursor-pointer"
                                           v-if="item.deleted_at === null"
                                            @click="deleteItem(item.id)"></i>
                                        <i class="fa fa-plus-circle text-yellow-300 cursor-pointer"
                                           v-else
                                           @click="enableItem(item.id)"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <modal v-if="addNewOpen" @closeModal="addNewOpen = false">
            <form @submit.prevent="addNew" class="grid grid-cols-2 gap-x-2 gap-y-4">
                <input type="hidden" v-bind="newWhitelist.id">
                <div class="col-span-1 clear-both">
                    <jet-input v-model="newWhitelist.ip_address" type="text" placeholder="IP Address" class="w-full"/>
                    <jet-input-error :message="newWhitelistErrors.ip_address" />
                </div>
                <div class="col-span-1">
                    <jet-input v-model="newWhitelist.subnet" type="number" placeholder="Subnet" class="w-full"/>
                </div>
                <div class="text-center col-span-2 mt-4">
                    <secondary-button type="submit">
                        Add IP
                    </secondary-button>
                </div>
            </form>
        </modal>
    </app-layout>
</template>

<script>

    import AppLayout from "Layout/AppLayout";
    import VueSelect from "Component/Inputs/Select";
    import PrimaryButton from "Component/Buttons/Primary";
    import SecondaryButton from "Component/Buttons/Secondary";
    import Modal from "Component/Modal";
    import JetInput from "Jetstream/Input";
    import JetInputError from "Jetstream/InputError";

    export default {
        components: {
            AppLayout,
            VueSelect,
            PrimaryButton,
            SecondaryButton,
            Modal,
            JetInput,
            JetInputError
        },
        props: [
            'data',
            'errors',
            'systemSuccess',
        ],
        data() {
            return {
                whitelist: this.data.whitelist,
                addNewOpen: false,
                newWhitelist: {
                    id: 0,
                    ip_address: '',
                    subnet: '',
                },
                newWhitelistErrors: {
                    ip_address: false
                }
            }
        },
        mounted() {
        },
        methods: {
            addNew()
            {
                this.newWhitelist.subnet = this.newWhitelist.subnet === '' ? 0 : this.newWhitelist.subnet;
                if(this.validate() === true) {

                    if (this.newWhitelist.id === 0)
                        this.$inertia.post('/admin/api/ip-whitelist/create', this.newWhitelist).then( () => {this.whitelist = this.data.whitelist}) ;
                    else
                        this.$inertia.put('/admin/api/ip-whitelist/update', this.newWhitelist).then( () => {this.whitelist = this.data.whitelist});

                    this.resetNewTemplateBuilder();
                    this.addNewOpen = false
                }
            },
            deleteItem(id)
            {
                this.$inertia.delete('/admin/api/ip-whitelist/delete/' + id).then( () => {this.whitelist = this.data.whitelist});
            },
            enableItem(id)
            {
                let enableObj = {
                    id: id,
                    enable: true
                }
                this.$inertia.put('/admin/api/ip-whitelist/update', enableObj).then( () => {this.whitelist = this.data.whitelist});
            },
            validate()
            {
                let flag = true;

                if( this.newWhitelist.ip_address.trim() === '' ) {
                    flag = false;
                    this.newWhitelistErrors.ip_address = 'IP Address can not be blank';
                } else {
                    this.newWhitelistErrors.ip_address = null;
                }

                return flag;
            },
            resetNewTemplateBuilder()
            {
                this.newWhitelist = {
                    id: 0,
                    ip_address: '',
                    subnet: '',
                }
            },
            editItem(id, ip_address, subnet)
            {
                this.newWhitelist = {
                    id: id,
                    ip_address: ip_address,
                    subnet: subnet
                };
                this.newWhitelist.subnet = this.newWhitelist.subnet === '' ? 0 : this.newWhitelist.subnet;
                this.addNewOpen = true;
            },
            contentTagSelected(value)
            {
               console.log(value);
            },
            showNewTemplate()
            {
                this.addNewOpen = true;
                this.resetNewTemplateBuilder();
            }
        }
    }
</script>
