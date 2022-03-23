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
                Set Role Manager
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
                          <form @submit.prevent="addNew" id="myForm">
                        <table class="table-auto w-full border border-gray-500" v-if="templates">
                             <thead>
                                <tr>
                                    <th class="px-4 py-2 border-r border-gray-500">Module</th>
                                    <th class="px-4 py-2 border-r border-gray-500">view</th>
                                    <th class="px-4 py-2 border-r border-gray-500">Create</th>
                                    <th class="px-4 py-2 border-r border-gray-500">Update</th>
                                    <th class="px-4 py-2 border-r border-gray-500">Delete</th>

                                    <!--<th class="px-4 py-2">Actions</th>-->
                                </tr>
                            </thead>

                            <tbody>

                                <tr v-for="(item, index) in templates" :key="item.id"
                                    :class="[{'bg-gray-100': index % 2 !== 0, '': item.deleted_at !== null}]">

                                <td class="border border-gray-500 px-4 py-2 text-center" v-if="item.module_id == '1'">{{ "Employees" }}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center" v-else-if="item.module_id == '2'">{{ "Polls" }}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center" v-else-if="item.module_id == '3'">{{ "Files" }}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center" v-else-if="item.module_id == '4'">{{ "Declarations" }}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center" v-else-if="item.module_id == '5'">{{ "SMS Template" }}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center" v-else-if="item.module_id == '7'">{{ "Role Manager" }}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center" v-else-if="item.module_id == '8'">{{ "Admin User" }}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center" v-else-if="item.module_id == '9'">{{ "Settings" }}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center" v-else-if="item.module_id == '10'">{{ "System Crons" }}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center" v-else-if="item.module_id == '11'">{{ "QR Codes" }}</td>

                                <td class="border border-gray-500 px-4 py-2 text-center" v-else>{{ "Send SMS" }}</td>

                                <input type="hidden" :name="`module[${item.module_id}][id]`" :value="item.id" />

                                <td class="border border-gray-500 px-4 py-2 text-center">
                                    <input type="radio" :name="`module[${item.module_id}][view]`" value="1" :checked="item.perm_view==1" />Yes
                                    <input type="radio" :name="`module[${item.module_id}][view]`" value="0"  :checked="item.perm_view==0"/>No
                                </td>
                                <td class="border border-gray-500 px-4 py-2 text-center">
                                    <input type="radio" :name="`module[${item.module_id}][create]`"  value="1" :checked="item.perm_create==1"/>Yes
                                    <input type="radio" :name="`module[${item.module_id}][create]`" value="0" :checked="item.perm_create==0"/>No
                                </td>
                                <td class="border border-gray-500 px-4 py-2 text-center">
                                    <input type="radio" :name="`module[${item.module_id}][update]`" value="1" :checked="item.perm_update==1"  />Yes
                                    <input type="radio" :name="`module[${item.module_id}][update]`" value="0"  :checked="item.perm_update==0"/>No
                                </td>
                                <td class="border border-gray-500 px-4 py-2 text-center">
                                    <input type="radio" :name="`module[${item.module_id}][delete]`" value="1" :checked="item.perm_delete==1" />Yes
                                    <input type="radio" :name="`module[${item.module_id}][delete]`" value="0" :checked="item.perm_delete==0"/>No
                                </td>

</tr>





                            </tbody>

                        </table>

                         </form>

                <secondary-button type="button" @buttonClick="addNew" class="mt-4" v-if="data.permission.perm_update">
                    Save
                </secondary-button>
                    </div>
                </div>
            </div>
        </div>





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
            'contentTags',

        ],
        data() {
            return {
                templates: this.data.templates,
                addNewOpen: false,
                newTemplate: {
                    role_id:this.data.role_id,
                    module_id: this.data.templates[0].module_id,
                    perm_view:this.data.templates[0].perm_view,
                    perm_create:this.data.templates[0].perm_create,
                    perm_update:this.data.templates[0].perm_update,
                    perm_delete:this.data.templates[0].perm_delete,

                },


                newTemplateErrors: {
                    name: false,
                    content: false
                },
                permission:this.data,
                showContentOpen: false,
                showContent: ''
            }

        },
        mounted() {
           // console.dir(this.contentTags);
             console.log(this.data.templates);
        },
        methods: {

            addNew()
            {

                this.$inertia.post('/admin/roles/savepermisson', $("#myForm").serialize()).then( () => {}) ;


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
