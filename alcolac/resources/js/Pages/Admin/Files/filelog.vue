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
        Show File Log
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl p-12">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <strong>{{ this.userName }}</strong> File Log
          </h2>
          <div class="w-full mt-4">
            <h3 class="clearfix mb-3">
            </h3>

            <form @submit.prevent="addNew" id="myForm">
              <table
                class="table-auto w-full border border-gray-500"
                v-if="templates"
              >
                <thead>
                  <tr>
                    <th class="px-4 py-2 border-r border-gray-500">
                      File Name
                    </th>
                    <th class="px-4 py-2 border-r border-gray-500">
                      IP Address
                    </th>
                    <th class="px-4 py-2 border-r border-gray-500">ZipCode</th>
                    <th class="px-4 py-2 border-r border-gray-500">
                      Access Date
                    </th>
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
                    <td class="border border-gray-500 px-4 py-2 text-center">
                      {{ item.file_name }}
                    </td>
                    <td class="border border-gray-500 px-4 py-2 text-center">
                      {{ item.ip_address }}
                    </td>
                    <td class="border border-gray-500 px-4 py-2 text-center">
                      {{ item.country_name }}
                    </td>

                    <td class="border border-gray-500 px-4 py-2 text-center">
                      {{ ShowDate(item.created_at) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </form>

            <secondary-button type="button" @buttonClick="addNew" class="mt-4">
              Back
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
    JetInputError,
  },
  props: ["data", "errors", "systemSuccess", "contentTags"],
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
    // console.dir(this.contentTags);
    // console.log(this.data.templates);
    this.showTemplates();
  },
  updated() {
    this.showTemplates();
  },
  methods: {
    async showTemplates() {
      await this.data.templates.forEach(async (item, index) => {
        let questionary = await this.getVal(
          item.answers,
          item.declaration.fields
        );
        console.log("questionary", questionary);
        this.data.templates[index].questionary = questionary;
      });
      this.templates = this.data.templates;
      this.userName = this.data.userName;
    },
    addNew() {
      window.location.href = "/admin/files";
    },
    ShowDate(date) {
      /*var timeStr = date,
            temp = timeStr.split("T")[0].split("-").reverse(),
            newFormat;

            temp[0] = temp.splice(1, 1, temp[0])[0];
            newFormat = temp.join("/");
            if (newFormat.charAt(0) === "0") {
            newFormat = newFormat.slice(1);
            }*/
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
