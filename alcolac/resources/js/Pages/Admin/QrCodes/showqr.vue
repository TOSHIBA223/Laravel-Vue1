<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        RAT Result
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl p-12">
          <div class="w-full mt-4">
            <h3 class="clearfix mb-3">
              <jet-input
                type="text"
                id="search"
                placeholder="Search"
                @input="searchUsers"
                class="w-1/2"
              ></jet-input>
              <secondary-button
                type="button"
                :class="'float-right'"
                @buttonClick="showNewTemplate"
                v-if="data.permission.perm_create"
              >
                Add New Result
              </secondary-button>
            </h3>
            <p class="text-lg font-weight-bold mt-1">
              Showing:
              <span v-if="pagination.per_page < pagination.total">{{
                pagination.per_page
              }}</span>
              <span v-else>{{ pagination.total }}</span>
              / {{ pagination.total }}
            </p>
            <table
              class="table-auto w-full border border-gray-500"
              v-if="templates"
            >
              <thead>
                <tr>
                  <th class="px-4 py-2 border-r border-gray-500 text-center">
                    Employee Code
                  </th>
                  <th class="px-4 py-2 border-r border-gray-500 text-center">
                    First Name
                  </th>
                  <th class="px-4 py-2 border-r border-gray-500 text-center">
                    Last Name
                  </th>
                  <th class="px-4 py-2 border-r border-gray-500 text-center">
                    Phone
                  </th>
                  <!-- <th class="px-4 py-2 border-r border-gray-500 text-center">
                    IP Address
                  </th>
                  <th class="px-4 py-2 border-r border-gray-500 text-center">
                    URL
                  </th> -->
                  <th class="px-4 py-2 border-r border-gray-500 text-center">
                    Result
                  </th>
                  <th class="px-4 py-2 border-r border-gray-500 text-center">
                    Date
                  </th>
                  <th class="px-4 py-2 border-r border-gray-500 text-center">
                    Actions
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
                    {{ item.employee_code }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.first_name }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.last_name }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.phone }}
                  </td>
                  <!-- <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.ip_address }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.location }}
                  </td> -->
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.result }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ showDate(item.created_at) }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    <em
                      class="fa fa-edit text-green-400 mr-4 cursor-pointer"
                      data-toggle="tooltip"
                      data-placement="bottom"
                      title="Update Result"
                      @click="editItem(item)"
                      v-if="data.permission.perm_update"
                    ></em>

                    <label v-if="data.permission.perm_delete">
                      <em
                        class="fas fa-trash-alt text-red-600 cursor-pointer"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Delete Result"
                        @click="deleteItem(item.id)"
                      ></em>
                    </label>
                  </td>
                </tr>
              </tbody>
            </table>

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
              <span
                >{{ pagination.current_page }} /
                {{ pagination.last_page }}</span
              >
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
          </div>
        </div>
      </div>
    </div>

    <modal
      v-if="editResultOpen"
      @closeModal="editResultOpen = false"
      containerMaxWidth="max-w-3xl"
      scrollBehaviour="overflow-y-auto"
      class="forpopup"
    >
      <div class="close-btn">
        <a href="#"
          ><em class="fas fa-times" @click.prevent="closepopup"></em
        ></a>
      </div>
      <div class="outer">
        <form @submit.prevent="editResult">
          <div class="grid grid-cols-2 gap-x-2 gap-y-4 mt-4">
            <h3 class="col-span-3">Details</h3>

            <div>
              <label class="font-weight-bold text-xl w-100">RAT Result</label>
              <vue-select
                selectName="result"
                :options="resultTemplate"
                :selectedOption="newUser.result"
                placeholderOption="Select a Result"
                class="w-full"
                @optionSelected="resultChanged"
              />
            </div>
          </div>
        </form>

        <secondary-button type="button" @buttonClick="editResult" class="mt-4">
          Save
        </secondary-button>
      </div>
    </modal>

    <modal
      v-if="addNewOpen"
      @closeModal="addNewOpen = false"
      containerMaxWidth="max-w-3xl"
      scrollBehaviour="overflow-y-auto"
      class="forpopup"
    >
      <div class="close-btn">
        <a href="#"><i class="fas fa-times" @click.prevent="closepopup"></i></a>
      </div>
      <div class="outer">
        <form @submit.prevent="addNew">
          <div class="grid grid-cols-2 gap-x-2 gap-y-4 mt-4">
            <h3 class="col-span-3">New Result</h3>
            <div>
              <label class="font-weight-bold text-xl w-100"
                >Employee Code</label
              >
              <jet-input
                type="text"
                id="employee_code"
                placeholder="Employee Code"
                v-model="newUser.employee_code"
                class="w-full"
                @input="getEmployeeData"
              ></jet-input>
              <jet-input-error :message="newUserErrors.employee_code" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-x-2 gap-y-4 mt-4">
            <div>
              <label class="font-weight-bold text-xl w-100">First Name</label>
              <jet-input
                type="text"
                id="first_name"
                placeholder="First Name"
                v-model="newUser.first_name"
                class="w-full"
              ></jet-input>
              <jet-input-error :message="newUserErrors.first_name" />
            </div>
            <div>
              <label class="font-weight-bold text-xl w-100">Last Name</label>
              <jet-input
                type="text"
                id="last_name"
                placeholder="Last Name"
                v-model="newUser.last_name"
                class="w-full"
              ></jet-input>
              <jet-input-error :message="newUserErrors.last_name" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-x-2 gap-y-4 mt-4">
            <div>
              <label class="font-weight-bold text-xl w-100">Phone</label>
              <jet-input
                type="text"
                id="phone"
                placeholder="Phone"
                v-model="newUser.phone"
                class="w-full"
              ></jet-input>
              <jet-input-error :message="newUserErrors.phone" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-x-2 gap-y-4 mt-4">
            <div>
              <label class="font-weight-bold text-xl w-100">RAT Result</label>
              <vue-select
                selectName="result"
                :options="resultTemplate"
                placeholderOption="Select a Result"
                class="w-full"
                @optionSelected="resultChanged"
              />
            </div>
          </div>
        </form>

        <secondary-button type="button" @buttonClick="addNew" class="mt-4">
          Save
        </secondary-button>
      </div>
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
import JetNavLink from "Jetstream/NavLink";

export default {
  components: {
    AppLayout,
    VueSelect,
    PrimaryButton,
    SecondaryButton,
    Modal,
    JetInput,
    JetInputError,
    JetNavLink,
  },
  props: ["data", "errors", "systemSuccess", "contentTags"],
  data() {
    return {
      resultTemplate: [
        { id: "negative", name: "negative" },
        { id: "positive", name: "positive" },
      ],
      declarations: [],
      url: window.location.origin,
      selectedDeclaration: "",
      templates: this.data.users.data,
      token: this.data.token,
      addNewOpen: false,
      editResultOpen: false,
      locations: [],
      roles: [],
      newUser: {
        id: 0,
        result: "negative",
      },
      newUserErrors: {
        area_name: false,
      },
      viewUserOpen: false,
      viewSelectedUser: {},
      pagination: {
        current_page: this.data.users.current_page,
        total: this.data.users.total,
        per_page: this.data.users.per_page,
        first_link: this.data.users.first_page_url,
        last_link: this.data.users.last_page_url,
        last_page: this.data.users.last_page,
        next: this.data.users.next_page_url,
        prev: this.data.users.prev_page_url,
      },
      permission: this.data,
      decPagination: {},
      viewEntryOpen: false,
    };
  },
  mounted() {
    this.setLocations(this.data.locations);
    this.setRoles(this.data.roles);
    this.setDeclarations(this.data.declarations);
  },
  methods: {
    closepopup() {
      this.addNewOpen = false;
      this.editResultOpen = false;
    },

    deleteItem(id) {
      axios.delete("/admin/api/qr-checks/delete/" + id).then((result) => {
        this.templates = result.data.data.users.data;
        this.pagination = {
          current_page: result.current_page,
          total: result.total,
          per_page: result.per_page,
          first_link: result.first_page_url,
          last_link: result.last_page_url,
          last_page: result.last_page,
          next: result.next_page_url,
          prev: result.prev_page_url,
        };
        this.showNotification(result.data);
      });
    },

    getEmployeeData(string) {
      axios
        .post("/admin/api/employees/search_employee", {
          search: string,
        })
        .then((result) => {
          if (result.data.data == null || result.data.data.users.data.length == 0) {
            return;
          }
          let user = result.data.data.users.data[0];
          this.newUser.first_name = user.first_name;
          this.newUser.last_name = user.last_name;
          this.newUser.phone = user.phone;
        });
    },

    searchUsers(string) {
      axios
        .post("/admin/api/qr-checks/search", {
          search: string,
          token: this.token,
        })
        .then((result) => {
          console.log(result);
          result = result.data.data.users;
          this.templates = result.data;
          this.pagination = {
            current_page: result.current_page,
            total: result.total,
            per_page: result.per_page,
            first_link: result.first_page_url,
            last_link: result.last_page_url,
            last_page: result.last_page,
            next: result.next_page_url,
            prev: result.prev_page_url,
          };
        });
    },
    addNew() {
      axios.post("/admin/api/qr-checks/create", this.newUser).then((result) => {
        this.templates = result.data.data.users.data;
        this.showNotification(result.data);
      });
      this.addNewOpen = false;
    },
    editResult() {
      axios.put("/admin/api/qr-checks/update", this.newUser).then((result) => {
        this.templates = result.data.data.users.data;
        this.showNotification(result.data);
      });
      this.editResultOpen = false;
    },

    declarationChanged(declaration) {
      this.selectedDeclaration = declaration;
    },
    locationChanged(location) {
      this.newUser.location = location;
    },
    resultChanged(result) {
      this.newUser.result = result;
    },
    setRoles(roles) {
      this.roles = [];
      roles.forEach((role) => {
        this.roles.push({
          id: role.id,
          name: role.name,
        });
      });
    },
    setLocations(locations) {
      this.locations = [];
      locations.forEach((location) => {
        this.locations.push({
          id: location.location,
          name: location.location,
        });
      });
    },
    setDeclarations(declarations) {
      this.declarations = [];
      declarations.forEach((declaration) => {
        this.declarations.push({
          id: declaration.id,
          name: declaration.name,
        });
      });
    },
    validate() {
      return true;
    },
    editItem(item) {
      this.editResultOpen = true;

      let data = {
        id: item.id,
        result: item.result,
      };
      this.newUser = data;
    },
    showNotification(result) {
      if (result.systemSuccess) {
        this.$toast.success(result.systemSuccess, {
          timeout: 3000,
        });
      } else if (result.systemFailed) {
        this.$toast.success(result.systemFailed, {
          timeout: 3000,
        });
      }
    },
    showDate(date) {
      var d = new Date(date); // UTC time
      return d.toLocaleString("en-GB");
    },
    showNewTemplate() {
      this.addNewOpen = true;
      this.resetNewTemplateBuilder();
    },
    resetNewTemplateBuilder() {
      this.newUser = {
        id: 0,
        email: "",
        dob: "",
        first_name: "",
        last_name: "",
        groups: "",
        location: null,
        user_role_id: null,
        phone: "",
        employee_code: "",
        address: "",
        suburb: "",
        state: "",
        post_code: "",
      };
    },
  },
};
</script>
