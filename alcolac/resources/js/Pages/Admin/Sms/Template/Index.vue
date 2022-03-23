<template>
    <app-layout>

        <!-- <transition enter-active-class="ease-out duration-300"
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
        </transition> -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                SMS Templates
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl p-12">
                    <div class="w-full mt-4">
                        <h3 class="clearfix mb-3">
                            <secondary-button type="button" :class="'float-right'"
                                            @buttonClick="showNewTemplate">
                                New SMS Template
                            </secondary-button>
                        </h3>
                        <table class="table-auto w-full border border-gray-500" v-if="templates">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border-r border-gray-500">Name</th>
                                    <th class="px-4 py-2 border-r border-gray-500">Content</th>
                                    <th class="px-4 py-2 border-r border-gray-500">Assignable</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in templates" :key="item.id"
                                    :class="[{'bg-gray-100': index % 2 !== 0, 'opacity-50': item.deleted_at !== null}]">
                                    <td class="border border-gray-500 px-4 py-2 text-center">{{item.name}}</td>
                                    <td class="border border-gray-500 px-4 py-2 text-center">
                                        <a href="#" @click.prevent="showContentModel(item.content)" class="text-blue-600">View Content</a>
                                    </td>
                                    <td class="border border-gray-500 px-4 py-2 text-center">
                                        {{item.assignable === true ? 'Yes' : 'No'}}
                                    </td>
                                    <td class="border border-gray-500 px-4 py-2 text-center">
                                        <i class="fa fa-edit text-green-400 mr-4 cursor-pointer"
                                           @click="editItem(item.id, item.name, item.content)"></i>
                                        <i class="fa fa-minus-circle text-red-500 cursor-pointer"
                                           v-if="item.deleted_at === null"
                                            @click="openSmsTemplateDelete(item.id)"></i>
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

        <modal v-if="addNewOpen" @closeModal="addNewOpen = false" class="forpopup">
             <div class=close-btn><a href="#"><i class="fas fa-times" @click.prevent="closepopup" ></i></a></div>
            <form @submit.prevent="addNew" class="grid grid-cols-2 gap-x-2 gap-y-4">
                <input type="hidden" v-bind="newTemplate.id">
                <div class="col-span-2 clear-both">
                    <jet-input v-model="newTemplate.name" type="text" placeholder="Name" class="w-1/2"/>
                    <jet-input-error :message="newTemplateErrors.name" />
                    <vue-select-opt-group selectName="content-tags"
                                :options="contentTags"
                                :class="'float-right rounded-sm'"
                                :resetOnChange="true"
                                placeholderOption="Select a Content Tag"
                                @optionSelected="contentTagSelected"/>
                </div>
                <div class="col-span-2">
                    <textarea class="w-full border border-black rounded-sm h-64 p-4" v-model="newTemplate.content" placeholder="Content">

                    </textarea>
                    <jet-input-error :message="newTemplateErrors.content" />
                </div>
                <div class="text-center col-span-2 mt-4">
                    <secondary-button type="submit">
                        Save
                    </secondary-button>
                </div>
            </form>
        </modal>

        <modal v-if="showContentOpen" @closeModal="showContentOpen = false" class="forpopup">
             <div class=close-btn><a href="#"><i class="fas fa-times" @click.prevent="closepopup" ></i></a></div>
            <pre class="font-sans whitespace-pre-line">{{showContent}}</pre>
        </modal>
        <modal
        v-if="showSmsTemplateDelete"
        @closeModal="showSmsTemplateDelete = false"
        containerMaxWidth="max-w-3xl"
        class="forpopup"
        >
        <div class="close-btn">
            <a href="#"
            ><em class="fas fa-times" @click.prevent="closepopup"></em
            ></a>
        </div>
        <div class="text-center">
            <h4 class="text-center mb-3" v-html="'Delete SmsTemplate'"></h4>
        </div>
        <div class="row">
            <div class="col-sm-12">
            <strong
                class="mb-0 text-center"
                style="display: block; font-size: 20px"
            >
                Are you sure you want to delete?
            </strong>
            </div>
            </div>
        <div class="row d-flex justify-center mt-8">
            <secondary-button type="button" @buttonClick="deleteItem">
            Yes
            </secondary-button>

            <secondary-button type="button" @buttonClick="closepopup">
            No
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
                showSmsTemplateDelete:false,
                deleteSmsTemplateId:0,
                newTemplate: {
                    id: 0,
                    name: '',
                    content: '',
                    success:"",
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
            closepopup(){
                location.reload();
            },
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
            openSmsTemplateDelete(id) {
            this.showSmsTemplateDelete = true;
            this.deleteSmsTemplateId = id;
            },
            // deleteItem(id)
            // {
            //     this.$inertia.delete('/admin/api/sms/templates/' + id).then( () => {this.templates = this.data.templates});
            // },
            deleteItem() {
                if (this.deleteSmsTemplateId !=0) {
                axios
                    .delete("/admin/api/sms/templates/" + this.deleteSmsTemplateId)
                    .then((result) => {
                        this.templates = result.data.data.templates;
                        this.showNotification(result.data);
                    });
                }
                this.showSmsTemplateDelete = false;
            },
            enableItem(id)
            {
                let enableObj = {
                    id: id,
                    enable: true
                }
                axios
                    .put('/admin/api/sms/templates', enableObj)
                    .then((result) => {
                        this.templates = result.data.data.templates;
                        this.showNotification(result.data);
                });
            },
            showNotification(result) {
                if (result.systemSuccess) {
                    this.$toast.success(result.systemSuccess, {
                    timeout: 3000,
                    });
                } else if (result.systemFailed) {
                    this.$toast.success(result.systemFailed, {
                    timeout:  3000,
                    });
                }
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
            editItem(id, name, content,success)
            {
                this.newTemplate = {
                    id: id,
                    name: name,
                    content: content,
                    success:success
                };
                this.addNewOpen = true;
            },
            contentTagSelected(value)
            {
               this.newTemplate.content = this.newTemplate.content + ' ' + value;
            },
            showContentModel(content)
            {
                this.showContent = content;
                this.showContentOpen = true;
            },
            showNewTemplate()
            {
                this.addNewOpen = true;
                this.resetNewTemplateBuilder();
            }
        }
    }
</script>
