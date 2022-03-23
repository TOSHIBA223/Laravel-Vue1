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
                Role Manager
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl p-12">
                    <div class="w-full mt-4">
                        <h3 class="clearfix mb-3">
                            <!--<secondary-button type="button" :class="'float-right'"
                                            @buttonClick="showNewTemplate">
                                New SMS Template
                            </secondary-button>-->
                        </h3>
                        <table class="table-auto w-full border border-gray-500" v-if="templates">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border-r border-gray-500">Type</th>
                                    <th class="px-4 py-2 border-r border-gray-500">Date</th>
                                    <th class="px-4 py-2 border-r border-gray-500">Manage Permission</th>
                                    <!--<th class="px-4 py-2">Actions</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in templates" :key="item.id"
                                    :class="[{'bg-gray-100': index % 2 !== 0, 'opacity-50': item.deleted_at !== null}]">
                                    <td class="border border-gray-500 px-4 py-2 text-center">{{item.name}}</td>
                                    <td class="border border-gray-500 px-4 py-2 text-center">{{item.updated_at}}</td>

                                    <td class="border border-gray-500 px-4 py-2 text-center">
                                        <a href="#" @click.prevent="showContentModel(item.id)" class="text-blue-600">Manage Permission</a>
                                    </td>

                                    <!--<td class="border border-gray-500 px-4 py-2 text-center">
                                        <i class="fa fa-edit text-green-400 mr-4 cursor-pointer"
                                           @click="editItem(item.id, item.name, item.content)"></i>
                                        <i class="fa fa-minus-circle text-red-500 cursor-pointer"
                                           v-if="item.deleted_at === null"
                                            @click="deleteItem(item.id)"></i>
                                        <i class="fa fa-plus-circle text-yellow-300 cursor-pointer"
                                           v-else
                                           @click="enableItem(item.id)"></i>
                                    </td>-->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        <modal v-if="showContentOpen" @closeModal="showContentOpen = false">
           <div class="outer">
                <form @submit.prevent="addNew">
                    <div class="grid grid-cols-3 gap-x-2 gap-y-4 mt-4">
                        <h3 class="col-span-3">Set Permission</h3>


                        <div>
                            <input type="radio" v-model="color" value="1">Yes
                        </div>
                        <div>
                           <input type="radio" v-model="color" value="0">No

                        </div>

                    </div>


                </form>

                <secondary-button type="button" @buttonClick="addNew" class="mt-4">
                    Submit
                </secondary-button>
            </div>
        </modal>


    </app-layout>
</template>

<script>

    import AppLayout from "Layout/AppLayout";
    import VueSelectOptGroup from "Component/Inputs/SelectOptGroups";
    import PrimaryButton from "Component/Buttons/Primary";
    import SecondaryButton from "Component/Buttons/Secondary";
    import Modal from "Component/Modal";
    import JetInput from "Jetstream/Input";
    import JetInputError from "Jetstream/InputError";

    export default {
        components: {
            AppLayout,
            VueSelectOptGroup,
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
            'contentTags'
        ],
        data() {
            return {
                templates: this.data.templates,
                addNewOpen: false,
                newTemplate: {
                    id: 0,
                    name: '',
                    content: '',
                },
                newTemplateErrors: {
                    name: false,
                    content: false
                },
                showContentOpen: false,
                showContent: ''
            }
        },
        mounted() {
            console.dir(this.contentTags);
        },
        methods: {
            addNew()
            {
                if(this.validate() === true) {

                    if (this.newTemplate.id === 0)
                        this.$inertia.post('/admin/api/sms/templates', this.newTemplate).then( () => {this.templates = this.data.templates}) ;
                    else
                        this.$inertia.put('/admin/api/sms/templates', this.newTemplate).then( () => {this.templates = this.data.templates});

                    this.resetNewTemplateBuilder();
                    this.addNewOpen = false
                }
            },
            deleteItem(id)
            {
                this.$inertia.delete('/admin/api/sms/templates/' + id).then( () => {this.templates = this.data.templates});
            },
            enableItem(id)
            {
                let enableObj = {
                    id: id,
                    enable: true
                }
                this.$inertia.put('/admin/api/sms/templates', enableObj).then( () => {this.templates = this.data.templates});
            },
            validate()
            {
                let flag = true;

                if( this.newTemplate.name.trim() === '' ) {
                    flag = false;
                    this.newTemplateErrors.name = 'Name can not be blank';
                } else {
                    this.newTemplateErrors.name = null;
                }

                if( this.newTemplate.content.trim() === '' ) {
                    flag = false;
                    this.newTemplateErrors.content = 'Content can not be blank';
                } else {
                    this.newTemplateErrors.content = null;
                }

                return flag;
            },
            resetNewTemplateBuilder()
            {
                this.newTemplate = {
                    id: 0,
                    name: '',
                    content: '',
                }
            },
            editItem(id, name, content)
            {
                this.newTemplate = {
                    id: id,
                    name: name,
                    content: content
                };
                this.addNewOpen = true;
            },
            contentTagSelected(value)
            {
               this.newTemplate.content = this.newTemplate.content + ' ' + value;
            },
            showContentModel(id)
            {
                window.location.href="/admin/roles/assign/"+id;
            },
            showNewTemplate()
            {
                this.addNewOpen = true;
                this.resetNewTemplateBuilder();
            }
        }
    }
</script>
