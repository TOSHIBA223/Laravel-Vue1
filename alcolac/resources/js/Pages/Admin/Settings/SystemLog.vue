<template>
  <app-layout>
    <transition
      enter-active-class="ease-out duration-300"
      enter-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-200"
      leave-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        class="bg-red-500 p-4 fixed left-0 right-0 top-0 text-center"
        v-if="errors.systemFail"
        @click="errors = false"
      >
        <div class="max-w-xl w-full mx-auto">
          <span class="text-white"> {{ errors.systemFail }} x </span>
        </div>
      </div>
      <div
        class="bg-green-400 p-4 fixed left-0 right-0 top-0 text-center"
        v-if="systemSuccess"
        @click="systemSuccess = false"
      >
        <div class="max-w-xl w-full mx-auto">
          <span class="text-white"> {{ systemSuccess }} x </span>
        </div>
      </div>
    </transition>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Show System Log
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl p-12">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            System Log
          </h2>
          <div class="w-full mt-4">
            <h3 class="clearfix mb-3"></h3>

            <table
              class="table-auto w-full border border-gray-500"
              v-if="templates"
            >
              <thead>
                <tr>
                  <th class="px-4 py-2 border-r border-gray-500">
                    User Name
                  </th>
                  <th class="px-4 py-2 border-r border-gray-500">Message</th>
                  <th class="px-4 py-2 border-r border-gray-500">Date</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(item, index) in templates"
                  :key="item.id"
                  :class="[
                    {
                      'bg-gray-100': index % 2 !== 0,
                      '': item.deleted_at !== null,
                    },
                  ]"
                >
                  <td class="border border-gray-500 px-4 py-2">
                    {{ item.admin.first_name + " " + item.admin.last_name }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2">
                    {{ item.message }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ ShowDate(item.created_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
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
    JetInputError,
  },
  props: ["data", "errors", "systemSuccess", "contentTags", "appUrl"],
  data() {
    return {
      templates: "",
      userName: "",
      addNewOpen: false,
      newTemplate: {},

      newTemplateErrors: {
        name: false,
        content: false,
      },
      showContentOpen: false,
      showContent: "",
    };
  },
  mounted() {
    this.showTemplates();
  },
  updated() {
    this.showTemplates();
  },
  methods: {
    async showTemplates() {
      this.templates = this.data.templates;
      this.userName = this.data.userName;
    },
    addNew() {
      window.location.href = "/admin/declarations";
    },
    ShowDate(date) {
      var d = new Date(date); // UTC time
      return d.toLocaleString("en-GB");
    },

    showContentModel(id) {
      window.location.href = "/admin/roles/assign/" + id;
    },
    showNewTemplate() {
      this.addNewOpen = true;
      this.resetNewTemplateBuilder();
    },
  },
};
</script>
