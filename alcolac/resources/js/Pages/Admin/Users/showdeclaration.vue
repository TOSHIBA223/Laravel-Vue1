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
        Show Employee Declaration
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl p-12">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <strong>{{ this.userName }}</strong> Declaration
          </h2>
          <div class="w-full mt-4">
            <h3 class="clearfix mb-3">
              <!--<secondary-button type="button" :class="'float-right'"
                                            @buttonClick="showNewTemplate">
                                New SMS Template
                            </secondary-button>-->
            </h3>

            <p class="text-lg font-weight-bold mt-1">
              Showing:
              <span v-if="pagination.per_page < pagination.total">{{
                pagination.per_page
              }}</span>
              <span v-else>{{ pagination.total }}</span>
              / {{ pagination.total }}
            </p>
            <form @submit.prevent="addNew" id="myForm">
              <table
                class="table-auto w-full border border-gray-500"
                v-if="templates"
              >
                <thead>
                  <tr>
                    <th class="px-4 py-2 border-r border-gray-500">
                      Declaration Name
                    </th>
                    <th class="px-4 py-2 border-r border-gray-500">Status</th>
                    <th class="px-4 py-2 border-r border-gray-500">Passed</th>
                    <th
                      class="px-4 py-2 border-r border-gray-500"
                      style="width: 50%"
                    >
                      Answers
                    </th>
                    <th class="px-4 py-2 border-r border-gray-500">
                      Created Date
                    </th>
                    <th class="px-4 py-2 border-r border-gray-500">
                      Completed Date
                    </th>

                    <!--<th class="px-4 py-2">Actions</th>-->
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
                      {{ item.declaration.name }}
                    </td>

                    <td
                      class="border border-gray-500 px-4 py-2 text-center"
                      v-if="item.complete == 1"
                    >
                      {{ "Complete" }}
                    </td>
                    <td
                      class="border border-gray-500 px-4 py-2 text-center"
                      v-else
                    >
                      {{ "Incomplete" }}
                    </td>

                    <td
                      class="border border-gray-500 px-4 py-2 text-center"
                      v-if="item.complete == 1 && item.passed == 1"
                    >
                      {{ "Passed" }}
                    </td>
                    <td
                      class="border border-gray-500 px-4 py-2 text-center"
                      v-if="item.complete == 1 && item.passed == 0"
                    >
                      {{ "Failed" }}
                    </td>
                    <td
                      class="border border-gray-500 px-4 py-2 text-center"
                      v-if="item.complete == 0 && item.passed == 0"
                    >
                      {{ "" }}
                    </td>

                    <td
                      class="border border-gray-500 px-4 py-2 cursor-pointer"
                      v-on:click="showDetail(item)"
                    >
                      <div
                        class="col-sm-12 mb-2"
                        v-if="
                          item.showDetailAnswers == true &&
                          item.questionary != null
                        "
                      >
                        <div
                          class="col-sm-12 mb-2"
                          v-for="(que, index) in item.questionary"
                          in
                          :key="index"
                        >
                          <div class="col-sm-12">
                            <strong>{{ que.title }}: </strong> {{ que.que }}
                          </div>
                          <div class="col-sm-12">
                            <strong>Your Answer: </strong>{{ que.ans }}
                          </div>
                        </div>
                      </div>
                      <div
                        class="col-sm-12 mb-2"
                        v-if="
                          item.showDetailAnswers == false &&
                          item.questionary != null
                        "
                      >
                        <div
                          class="col-sm-12 mb-2"
                          v-for="(que, index) in item.questionary"
                          in
                          :key="index"
                        >
                          <div class="col-sm-12">
                            <strong>{{ que.title }}: </strong> {{ que.ans }}
                          </div>
                        </div>
                      </div>
                    </td>

                    <td class="border border-gray-500 px-4 py-2 text-center">
                      {{ ShowDate(item.created_at) }}
                    </td>

                    <td
                      class="border border-gray-500 px-4 py-2 text-center"
                      v-if="item.complete == 1"
                    >
                      {{ ShowDate(item.updated_at) }}
                    </td>
                    <td
                      class="border border-gray-500 px-4 py-2 text-center"
                      v-else
                    >
                      {{ "" }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </form>
            <div class="text-center mt-2" v-if="pagination.last_page !== 1">
              <jet-nav-link
                v-if="pagination.prev !== null"
                :href="pagination.first_link"
                :class="''"
                ><<</jet-nav-link
              >
              <span v-else class="text-black-200"><<</span>
              <jet-nav-link
                v-if="pagination.prev !== null"
                :href="pagination.prev"
                :class="'mr-2 ml-1'"
                ><</jet-nav-link
              >
              <span v-else class="text-black-200 mr-2 ml-1"><</span>
              <jet-input
                type="text"
                v-model="pagination.current_page"
                class="w-12 text-center"
                id="current_page"
                @input="changePage"
              ></jet-input>
              <span> / {{ pagination.last_page }}</span>
              <jet-nav-link
                v-if="pagination.next !== null"
                :href="pagination.next"
                :class="'ml-2 mr-1'"
                >></jet-nav-link
              >
              <span v-else class="text-black-200 ml-2 mr-1">></span>
              <jet-nav-link
                v-if="pagination.next !== null"
                :href="pagination.last_link"
                :class="''"
                >>></jet-nav-link
              >
              <span v-else class="text-black-200">>></span>
            </div>

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
import JetNavLink from "Jetstream/NavLink";

export default {
  components: {
    AppLayout,
    VueSelectOptGroup,
    PrimaryButton,
    SecondaryButton,
    Modal,
    JetInput,
    JetInputError,
    JetNavLink,
  },
  props: ["data", "errors", "systemSuccess", "contentTags", "user_id"],
  data() {
    return {
      templates: this.data.templates.data,
      userName: "",
      addNewOpen: false,
      newTemplate: {},

      newTemplateErrors: {
        name: false,
        content: false,
      },
      showContentOpen: false,
      showContent: "",
      pagination: {
        current_page: this.data.templates.current_page,
        total: this.data.templates.total,
        per_page: this.data.templates.per_page,
        first_link: this.data.templates.first_page_url,
        last_link: this.data.templates.last_page_url,
        last_page: this.data.templates.last_page,
        next: this.data.templates.next_page_url,
        prev: this.data.templates.prev_page_url,
      },
    };
  },
  mounted() {
    this.showTemplates();
    document.getElementById("current_page").focus();
  },
  updated() {
    // this.showTemplates();
  },
  methods: {
    async showTemplates() {
      await this.data.templates.data.forEach(async (item, index) => {
        let questionary = await this.getVal(
          item.answers,
          item.declaration.fields
        );
        this.data.templates.data[index].questionary = questionary;
        this.data.templates.data[index].showDetailAnswers = false;
      });
      this.templates = this.data.templates.data;
      this.userName = this.data.userName;
    },
    addNew() {
      window.location.href = "/admin/employees";
    },
    showDetail(item) {
      item.showDetailAnswers = !item.showDetailAnswers;
      this.$forceUpdate();
    },
    ShowDate(date) {
      var d = new Date(date); // UTC time
      return d.toLocaleString("en-GB", { timeZone: "Australia/Sydney" });
    },
    changePage() {
      if (this.pagination.current_page > this.pagination.last_page) {
        this.pagination.current_page = this.pagination.last_page;
      } else if (this.pagination.current_page < 1) {
        this.pagination.current_page = 1;
      }

      this.$inertia.visit(
        "/admin/employees/showdeclaration/" + this.user_id + "?page=" + this.pagination.current_page
      );
    },
    getVal(vall, question) {
      let data = JSON.parse(vall);
      if (!data) {
        return null;
      }
      let questions = JSON.parse(question);
      let newData = new Array();

      questions.forEach((item, index) => {
        newData.push({
          que: item.definition,
          ans:
            data && data[index] && data[index][item.name]
              ? data[index][item.name]
              : "",
          title: item.label,
        });
      });

      return newData;
    },
    deleteItem(id) {
      this.$inertia.delete("/admin/api/sms/templates/" + id).then(async () => {
        //this.templates = this.data.templates
        await this.data.templates.forEach(async (item, index) => {
          let questionary = await this.getVal(
            item.answers,
            item.declaration.fields
          );
          this.data.templates[index].questionary = questionary;
        });
        this.templates = this.data.templates;
        // console.log(this.templates);
      });
    },
    enableItem(id) {
      let enableObj = {
        id: id,
        enable: true,
      };
      this.$inertia.put("/admin/api/sms/templates", enableObj).then(() => {
        this.templates = this.data.templates;
      });
    },
    validate() {
      let flag = true;

      if (this.newTemplate.name.trim() === "") {
        flag = false;
        this.newTemplateErrors.name = "Name can not be blank";
      } else {
        this.newTemplateErrors.name = null;
      }

      if (this.newTemplate.content.trim() === "") {
        flag = false;
        this.newTemplateErrors.content = "Content can not be blank";
      } else {
        this.newTemplateErrors.content = null;
      }

      return flag;
    },
    resetNewTemplateBuilder() {
      this.newTemplate = {
        id: 0,
        name: "",
        content: "",
      };
    },
    editItem(id, name, content) {
      this.newTemplate = {
        id: id,
        name: name,
        content: content,
      };
      this.addNewOpen = true;
    },
    contentTagSelected(value) {
      this.newTemplate.content = this.newTemplate.content + " " + value;
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
