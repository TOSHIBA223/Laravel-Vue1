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
                Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl p-12">
                    <div class="w-full mt-4">
                        <h3 class="clearfix mb-3">
                            <jet-nav-link :href="'/admin/users'" :class="'w-full p-4'">Back</jet-nav-link>
                        </h3>
                        <p class="text-lg font-weight-bold mt-1">Showing:
                            <span v-if="pagination.per_page < pagination.total">{{pagination.per_page}}</span>
                            <span v-else>{{pagination.total}}</span>
                            / {{pagination.total}}</p>
                        <table class="table-auto w-full border border-gray-500" v-if="templates">
                            <thead>
                            <tr>
                                <th class="px-4 py-2 border-r border-gray-500">Status</th>
                                <th class="px-4 py-2 border-r border-gray-500">Passed</th>
                                <th class="px-4 py-2 border-r border-gray-500">Date Sent</th>
                                <th class="px-4 py-2 border-r border-gray-500"
                                    v-for="(question, index) in data.questions"
                                    :key="index">
                                {{question}}
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="templates" v-for="(item, index) in templates" :key="item.id"
                                :class="[{'bg-gray-100': index % 2 !== 0, 'opacity-50': item.deleted_at !== null}]">
                                <td class="border border-gray-500 px-4 py-2 text-center">{{item.status}}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center">{{item.passed}}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center">{{item.date}}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center"
                                    v-for="(answer, answerIndex) in item.answers" :key="answerIndex"
                                >{{answer}}</td>
                            </tr>
                            </tbody>
                        </table>

                        <div class="text-center mt-2" v-if="pagination.last_page !== 1">
                            <jet-nav-link
                                v-if="pagination.prev !== null"
                                :href="pagination.first_link"
                                :class="''"><<</jet-nav-link>
                            <span v-else class="text-black-200"><<</span>
                            <jet-nav-link
                                v-if="pagination.prev !== null"
                                :href="pagination.prev"
                                :class="'mr-2 ml-1'"><</jet-nav-link>
                            <span v-else class="text-black-200 mr-2 ml-1"><</span>
                            <span>{{pagination.current_page}} / {{pagination.last_page}}</span>
                            <jet-nav-link
                                v-if="pagination.next !== null"
                                :href="pagination.next"
                                :class="'ml-2 mr-1'">></jet-nav-link>
                            <span v-else class="text-black-200 ml-2 mr-1">></span>
                            <jet-nav-link
                                v-if="pagination.next !== null"
                                :href="pagination.last_link"
                                :class="''">>></jet-nav-link>
                            <span v-else class="text-black-200">>></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </app-layout>
</template>

<script>

import AppLayout from "Layout/AppLayout";
import VueSelect from "Component/Inputs/Select";
import PrimaryButton from "Component/Buttons/Primary";
import Modal from "Component/Modal";
import JetInput from "Jetstream/Input";
import JetInputError from "Jetstream/InputError";
import JetNavLink from "Jetstream/NavLink";

export default {
    components: {
        AppLayout,
        VueSelect,
        PrimaryButton,
        Modal,
        JetInput,
        JetInputError,
        JetNavLink
    },
    props: [
        'data',
        'errors',
        'systemSuccess',
        'contentTags'
    ],
    data() {
        return {
            templates: this.data.decs.data,
            pagination: {
                current_page: this.data.decs.current_page,
                total: this.data.decs.total,
                per_page: this.data.decs.per_page,
                first_link: this.data.decs.first_page_url,
                last_link: this.data.decs.last_page_url,
                last_page: this.data.decs.last_page,
                next: this.data.decs.next_page_url,
                prev: this.data.decs.prev_page_url
            }
        }
    }
}
</script>
