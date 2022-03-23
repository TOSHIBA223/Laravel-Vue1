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
                Menus
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl p-12">
                    <div class="w-1/2">
                        <vue-select selectName="menus"
                                   :options="menus"
                                    :selectedOption="selectedMenu.id"
                                    placeholderOption="Select a Menu"
                                    @optionSelected="menuChanged"/>
                    </div>
                    <div class="w-full mt-4" v-if="selectedMenu">
                        <h3 class="clearfix mb-3">
                            {{selectedMenu.name}} Links
                            <secondary-button type="button" :class="'float-right'"
                                            @buttonClick="newItem">
                                New Menu Item
                            </secondary-button>
                        </h3>
                        <table class="table-auto w-full border border-gray-500" v-if="menuItems">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border-r border-gray-500">Name</th>
                                    <th class="px-4 py-2 border-r border-gray-500">Link</th>
                                    <th class="px-4 py-2 border-r border-gray-500">Access Level</th>
                                    <th class="px-4 py-2 border-r border-gray-500">Order</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in menuItems" :key="item.id"
                                    :class="[{'bg-gray-100': index % 2 !== 0, 'opacity-50': item.deleted_at !== null}]">
                                    <td class="border border-gray-500 px-4 py-2 text-center">{{item.name}}</td>
                                    <td class="border border-gray-500 px-4 py-2 text-center">
                                        <span v-if="item.deleted_at !== null" class="text-red-500">
                                            /{{item.link}}
                                        </span>
                                        <a v-else :href="'/admin/' + item.link"
                                           class="inline-flex items-center text-blue-600 hover:underline" target="_blank">
                                            /{{item.link}}
                                        </a>
                                    </td>
                                    <td class="border border-gray-500 px-4 py-2 text-center">{{item.access_level}}</td>
                                    <td class="border border-gray-500 px-4 py-2 text-center">{{item.order}}</td>
                                    <td class="border border-gray-500 px-4 py-2 text-center">
                                        <i class="fa fa-edit text-green-400 mr-4 cursor-pointer"
                                           @click="editItem(item.id, item.name, item.link, item.access_level, item.order)"></i>
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
                <input type="hidden" :value="selectedMenuItem" v-bind="newMenu.id">
                <div class="col-span-2">
                    <jet-input v-model="newMenu.name" type="text" placeholder="Name" class="w-full"/>
                    <jet-input-error :message="newMenuErrors.name" />
                </div>
                <div class="col-span-2">
                    <jet-input v-model="newMenu.link" type="text"  placeholder="Link" class="w-full"/>
                    <jet-input-error :message="newMenuErrors.link" />
                </div>
                <div>
                    <jet-input v-model="newMenu.access_level" type="number" placeholder="Access Level" class="w-full"/>
                    <jet-input-error :message="newMenuErrors.access_level" />
                </div>
                <div>
                    <jet-input v-model="newMenu.order" type="number"  placeholder="Menu Order" class="w-full"/>
                    <jet-input-error :message="newMenuErrors.order" />
                </div>
                <div class="text-center col-span-2 mt-4">
                    <secondary-button type="submit">
                        Create Menu Item
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
            'systemSuccess'
        ],
        data() {
            return {
                selectedMenu: this.data.selectedMenu,
                menuItems: this.data.selectedMenuItems,
                menus: this.data.menus,
                addNewOpen: false,
                selectedMenuItem: 0,
                newMenu: {
                    id: 0,
                    name: '',
                    link: '',
                    access_level: false,
                    order: false,
                    menu_id: false,
                    parent: 0
                },
                newMenuErrors: {
                    name: false,
                    link: false,
                    access_level: false,
                    order: false
                }
            }
        },
        mounted() {
        },
        methods: {
            menuChanged(value)
            {
                this.selectedMenu = value;
            },
            addNew()
            {
                if(this.validate() === true) {
                    this.newMenu.menu_id = this.selectedMenu.id;
                    if (this.newMenu.id === 0)
                        this.$inertia.post('/admin/api/menus/items', this.newMenu).then( () => {this.menuItems = this.data.selectedMenuItems}) ;
                    else
                        this.$inertia.put('/admin/api/menus/items', this.newMenu).then( () => {this.menuItems = this.data.selectedMenuItems});

                    this.resetNewLinkBuilder();
                    this.addNewOpen = false
                }
            },
            deleteItem(id)
            {
                this.$inertia.delete('/admin/api/menus/items/' + this.selectedMenu.id + '/' + id).then( () => {this.menuItems = this.data.selectedMenuItems});
            },
            enableItem(id)
            {
                let enableObj = {
                    id: id,
                    enable: true,
                    menu_id: this.selectedMenu.id
                }
                this.$inertia.put('/admin/api/menus/items', enableObj).then( () => {this.menuItems = this.data.selectedMenuItems});
            },
            validate()
            {
                let flag = true;

                if( this.newMenu.name.trim() === '' ) {
                    flag = false;
                    this.newMenuErrors.name = 'Name can not be blank';
                } else {
                    this.newMenuErrors.name = null;
                }

                if( this.newMenu.link.trim() === '' ) {
                    flag = false;
                    this.newMenuErrors.link = 'Link can not be blank';
                } else {
                    this.newMenuErrors.link = null;
                }

                if( this.newMenu.order === false ) {
                    flag = false;
                    this.newMenuErrors.order = 'Order can not be blank';
                } else {
                    this.newMenuErrors.order = null;
                }

                if( this.newMenu.access_level === false ) {
                    flag = false;
                    this.newMenuErrors.access_level = 'Access Level can not be blank';
                } else {
                    this.newMenuErrors.access_level = null;
                }

                return flag;
            },
            resetNewLinkBuilder()
            {
                this.newMenu = {
                    id: 0,
                    name: '',
                    link: '',
                    access_level: false,
                    order: false,
                    menu_id: false,
                    parent: 0
                };
            },
            editItem(id, name, link, access_level, order)
            {
                this.newMenu = {
                    id: id,
                    name: name,
                    link: link,
                    access_level: access_level,
                    order: order,
                    menu_id: this.selectedMenu.id,
                    parent: 0
                };
                this.addNewOpen = true;
            },
            newItem()
            {
                this.resetNewLinkBuilder();
                this.addNewOpen = true;
                this.selectedMenuItem = 0;
            }
        },
        watch: {
            selectedMenu: function(id) {
                this.$inertia.visit('/admin/api/menus/items/' + id, {method: 'GET'});
            }
        }
    }
</script>
