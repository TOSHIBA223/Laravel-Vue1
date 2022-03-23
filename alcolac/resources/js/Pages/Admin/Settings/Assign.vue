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
        Setting Manager
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl p-12">
          <div class="w-full mt-4">
            <form
              @submit.prevent="addNew"
              id="myForm1"
              enctype="multipart/form-data"
              class="border border-gray-500"
            >
              <div id="app">
                <div class="container">
                  <br />

                  <ul class="nav nav-tabs nav-justified">
                    <li class="nav-item">
                      <a
                        class="nav-link"
                        @click.prevent="setActive('home')"
                        :class="{ active: isActive('home') }"
                        href="#home"
                        >General Settings</a
                      >
                    </li>
                    <li class="nav-item">
                      <a
                        class="nav-link"
                        @click.prevent="setActive('smtp')"
                        :class="{ active: isActive('smtp') }"
                        href="#smtp"
                        >SMTP Details</a
                      >
                    </li>
                    <li class="nav-item">
                      <a
                        class="nav-link"
                        @click.prevent="setActive('smb')"
                        :class="{ active: isActive('smb') }"
                        href="#smb"
                        >SMB Details</a
                      >
                    </li>
                    <li class="nav-item">
                      <a
                        class="nav-link"
                        @click.prevent="setActive('sms')"
                        :class="{ active: isActive('sms') }"
                        href="#sms"
                        >SMS Details</a
                      >
                    </li>
                    <li class="nav-item">
                      <a
                        class="nav-link"
                        @click.prevent="setActive('address')"
                        :class="{ active: isActive('address') }"
                        href="#address"
                        >Address Reset</a
                      >
                    </li>
                  </ul>

                  <div class="tab-content py-3" id="myTabContent">
                    <div
                      class="tab-pane fade"
                      :class="{ 'active show': isActive('home') }"
                      id="home"
                    >
                      <table
                        class="table-auto w-full border border-gray-500"
                        v-if="templates"
                      >
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
                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-if="
                                item.id == 22 ||
                                item.id == 6 ||
                                item.id == 7 ||
                                item.id == 21
                              "
                            >
                              {{ item.name }}
                            </td>

                            <input
                              type="hidden"
                              :name="`module[${item.id}][id]`"
                              :value="item.id"
                            />

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-if="item.id == '6'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-else-if="item.id == '22'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-else-if="item.id == '7'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <div
                      class="tab-pane fade"
                      :class="{ 'active show': isActive('smtp') }"
                      id="smtp"
                    >
                      <table
                        class="table-auto w-full border border-gray-500"
                        v-if="templates"
                      >
                        <tbody>
                          <tr
                            v-for="(item) in templates"
                            :key="item.id"
                            :class="[
                              {
                                'bg-gray-100': true,
                                '': item.deleted_at !== null,
                              },
                            ]"
                          >
                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-if="
                                item.name == 'SMTP HOST' ||
                                item.name == 'SMTP Port' ||
                                item.name == 'SMTP Username' ||
                                item.name == 'SMTP Password' ||
                                item.name == 'From Email' ||
                                item.name == 'From Name'
                              "
                            >
                              {{ item.name }}
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-if="item.name == 'SMTP HOST'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-if="item.name == 'SMTP Port'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-else-if="item.name == 'SMTP Username'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-else-if="item.name == 'SMTP Password'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-else-if="item.name == 'From Email'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-else-if="item.name == 'From Name'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div
                      class="tab-pane fade"
                      :class="{ 'active show': isActive('smb') }"
                      id="smb"
                    >
                      <table
                        class="table-auto w-full border border-gray-500"
                        v-if="templates"
                      >
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
                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-if="
                                item.name == 'SMB Host' ||
                                item.name == 'SMB Username' ||
                                item.name == 'SMB Password'
                              "
                            >
                              {{ item.name }}
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-if="item.name == 'SMB Host'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-else-if="item.name == 'SMB Username'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-else-if="item.name == 'SMB Password'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div
                      class="tab-pane fade"
                      :class="{ 'active show': isActive('sms') }"
                      id="sms"
                    >
                      <table
                        class="table-auto w-full border border-gray-500"
                        v-if="templates"
                      >
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
                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-if="
                                item.name == 'SMS PUBLIC API Key' || 
                                item.name == 'SMS SECRET API Key' || 
                                item.name == 'Sender ID'
                              "
                            >
                              {{ item.name }}
                            </td>

                            <input
                              type="hidden"
                              :name="`module[${item.id}][id]`"
                              :value="item.id"
                            />

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-if="item.name == 'SMS PUBLIC API Key'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>
                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-else-if="item.name == 'SMS SECRET API Key'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>

                            <td
                              class="
                                border border-gray-500
                                px-4
                                py-2
                                text-center
                              "
                              v-else-if="item.name == 'Sender ID'"
                            >
                              <input
                                type="text"
                                :name="`module[${item.id}][view]`"
                                :value="item.value"
                              />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div
                      class="tab-pane fade"
                      :class="{ 'active show': isActive('address') }"
                      id="address"
                    >
                      <div class="row mt-4">
                        <div class="col-2"></div>
                        <div class="col-4">
                          <label for="addressInterval"
                            >Reset Interval (Days)</label
                          >
                          <select
                            :name="`module[30][view]`"
                            :value="resetInterval ? resetInterval.value : 30"
                            id="addressInterval"
                            class="declaration-form-control"
                          >
                            <option
                              v-for="(day, index) in 90"
                              :key="index"
                              :value="day"
                            >
                              {{ day }}
                            </option>
                          </select>
                        </div>
                        <div class="col-4">
                          <label for="nextDate">Reset Date</label>
                          <jet-input
                            type="datetime-local"
                            :name="`module[31][view]`"
                            :value="resetDate.value"
                            id="resetDate"
                            placeholder="Reset Date"
                            class="w-full"
                          ></jet-input>
                        </div>
                        <div class="col-2"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>

            <secondary-button
              type="button"
              @buttonClick="addNew"
              class="mt-4"
              v-if="data.permission.perm_update"
            >
              Save
            </secondary-button>
            <secondary-button
              type="button"
              @buttonClick="testSMTP"
              class="mt-4 float-right"
              v-if="isActive('smtp')"
            >
              Test SMTP
            </secondary-button>
            <secondary-button
              type="button"
              @buttonClick="testSMB"
              class="mt-4 float-right"
              v-if="isActive('smb')"
            >
              Test SMB
            </secondary-button>
            <secondary-button
              type="button"
              @buttonClick="resetAddress"
              class="mt-4 float-right"
              v-if="isActive('address')"
            >
              Reset Now
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
import Modal from "Component/Modal";
import JetInput from "Jetstream/Input";
import JetInputError from "Jetstream/InputError";
import SecondaryButton from "Component/Buttons/Secondary";
import Label from "../../../Jetstream/Label.vue";

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
      templates: this.data.templates,
      addNewOpen: false,
      activeItem: "home",
      newTemplate: {
        role_id: this.data.role_id,
        module_id: this.data.templates[0].module_id,
        perm_view: this.data.templates[0].perm_view,
        perm_create: this.data.templates[0].perm_create,
        perm_update: this.data.templates[0].perm_update,
        perm_delete: this.data.templates[0].perm_delete,
      },

      newTemplateErrors: {
        name: false,
        content: false,
      },
      permission: this.data,
      showContentOpen: false,
      showContent: "",
      resetInterval: this.data.templates.find(element => element.id == '30'),
      resetDate: this.data.templates.find(element => element.id == '31')
    };
  },
  mounted() {
  },
  methods: {
    resetAddress() {
      this.$inertia.post("/admin/settings/reset_address", {}).then(() => {
        console.log("Reset Address Completed");
        this.resetDate = this.data.templates.find(element => element.id == '31');
        this.templates = this.data.templates;
      });
    },
    testSMTP() {
      axios.post("/admin/settings/testsmtp", {}).then((data) => {
        console.log("SMTP Test Completed", data);
      });
    },
    testSMB() {
      axios.post("/admin/settings/testsmb", {}).then(() => {
        console.log("SMB Test Completed");
      });
    },
    addNew() {
      let form = new FormData();
      this.$inertia
        .post("/admin/settings/savesetting", $("#myForm1").serialize())
        .then(() => {
          this.resetInterval = this.data.templates.find(element => element.id == '30');
          this.resetDate = this.data.templates.find(element => element.id == '31');
          this.templates = this.data.templates;
        });
    },
    isActive(menuItem) {
      return this.activeItem === menuItem;
    },
    setActive(menuItem) {
      this.activeItem = menuItem;
    },
    deleteItem(id) {
      this.$inertia.delete("/admin/api/sms/templates/" + id).then(() => {
        this.templates = this.data.templates;
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
    showDate(date) {
      var d = new Date(date); // UTC time
      return d.toLocaleString("en-GB");
    },
  },
};
</script>
