<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Admin Users
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
                New Admin User
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
                  <th class="px-4 py-2 border-r border-gray-500">First Name</th>
                  <th class="px-4 py-2 border-r border-gray-500">Last Name</th>
                  <th class="px-4 py-2 border-r border-gray-500">Email</th>
                  <th class="px-4 py-2 border-r border-gray-500">Role</th>
                  <th class="px-4 py-2">Actions</th>
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
                    {{ item.first_name }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.last_name }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.email }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.roleName }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    <i
                      class="fa fa-eye text-green-400 mr-4 cursor-pointer mr-2"
                      data-toggle="tooltip"
                      data-placement="bottom"
                      title="View Admin User"
                      @click="viewUser(item)"
                      v-if="data.permission.perm_view"
                    ></i>

                    <i
                      class="fa fa-edit text-green-400 mr-4 cursor-pointer"
                      data-toggle="tooltip"
                      data-placement="bottom"
                      title="Update Admin User"
                      @click="editItem(item)"
                      v-if="data.permission.perm_update"
                    ></i>

                    <label v-if="data.permission.perm_delete">
                      <i
                        v-if="item.deleted_at === null"
                        class="
                          fa fa-minus-circle
                          text-red-500
                          mr-4
                          cursor-pointer
                          mr-2
                        "
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Disable Admin User"
                        @click="openUserDelete(item.id)"
                      ></i>

                      <i
                        v-else
                        class="
                          fa fa-plus-circle
                          text-yellow-300
                          cursor-pointer
                          mr-4
                        "
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Enable Admin User"
                        @click="enableUser(item.id)"
                      ></i>

                      <i
                        class="fas fa-trash-alt text-yellow-300 cursor-pointer"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Delete Admin User"
                        @click="openPermanntDelete(item.id)"
                      ></i>
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
          </div>
        </div>
      </div>
    </div>

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
            <h3 class="col-span-3">Details</h3>

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
              <label class="font-weight-bold text-xl w-100">Email</label>
              <jet-input
                type="email"
                id="email"
                placeholder="Email"
                v-model="newUser.email"
                class="w-full"
              ></jet-input>
              <jet-input-error :message="newUserErrors.email" />
            </div>
            <div>
              <label class="font-weight-bold text-xl w-100">Password</label>
              <jet-input
                type="text"
                id="password"
                placeholder="password"
                v-model="newUser.password"
                class="w-full"
              ></jet-input>
              <jet-input-error :message="newUserErrors.password" />
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

            <div>
              <label class="font-weight-bold text-xl w-100">Select Roles</label>
              <vue-select
                selectName="roles"
                :options="roles"
                :selectedOption="newUser.user_role_id"
                placeholderOption="Select a Role"
                class="w-full"
                @optionSelected="roleChanged"
              />
            </div>
          </div>
          <!--<div class="grid grid-cols-3 gap-x-2 gap-y-4  mt-4">
                        <h3 class="col-span-3">Address</h3>
                        <div class="col-span-2">
                            <label class="font-weight-bold text-xl w-100">Address</label>
                            <jet-input type="text" id="address" placeholder="Address" v-model="newUser.address" class="w-full"></jet-input>
                        </div>
                        <div class="">
                            <label class="font-weight-bold text-xl w-100">Postcode</label>
                            <jet-input type="text" id="postcode" placeholder="Postcode" v-model="newUser.post_code" class="w-full"></jet-input>
                        </div>
                        <div class="col-span-2">
                            <label class="font-weight-bold text-xl w-100">Suburb</label>
                            <jet-input type="text" id="suburb" placeholder="Suburb" v-model="newUser.suburb" class="w-full"></jet-input>
                        </div>
                        <div class="">
                            <label class="font-weight-bold text-xl w-100">State</label>
                            <jet-input type="text" id="State" placeholder="State" v-model="newUser.state" class="w-full"></jet-input>
                        </div>
                    </div>-->
        </form>

        <secondary-button type="button" @buttonClick="addNew" class="mt-4">
          Save
        </secondary-button>
      </div>
    </modal>

    <modal
      v-if="viewUserOpen"
      @closeModal="viewUserOpen = false"
      containerMaxWidth="max-w-3xl"
      scrollBehaviour="overflow-y-auto"
      class="forpopup closbtndec"
    >
      <div class="close-btn">
        <a href="#"><i class="fas fa-times" @click.prevent="closepopup"></i></a>
      </div>

      <div class="grid grid-cols-2 mt-4 gap-x-3">
        <h3 class="col-span-3">Details</h3>

        <div class="border border-grey rounded p-2">
          <label class="font-weight-bold text-xl w-100">First Name</label>
          <p class="mb-0">{{ viewSelectedUser.first_name }}</p>
        </div>
        <div class="border border-grey rounded p-2">
          <label class="font-weight-bold text-xl w-100">Last Name</label>
          <p class="mb-0">{{ viewSelectedUser.last_name }}</p>
        </div>
      </div>

      <div class="grid grid-cols-2 mt-4 gap-x-3">
        <div class="border border-grey rounded p-2">
          <label class="font-weight-bold text-xl w-100">Email</label>
          <p class="mb-0">{{ viewSelectedUser.email }}</p>
        </div>
        <div class="border border-grey rounded p-2">
          <label class="font-weight-bold text-xl w-100">Phone</label>
          <p class="mb-0">{{ viewSelectedUser.phone }}</p>
        </div>
      </div>

      <div class="grid grid-cols-2 mt-4 gap-x-3">
        <div class="border border-grey rounded p-2">
          <label class="font-weight-bold text-xl w-100">Role</label>
          <p class="mb-0">{{ viewSelectedUser.roleName }}</p>
        </div>
      </div>

      <!--<div class="grid grid-cols-3 mt-4  gap-x-3">
                <div class="border border-grey rounded p-2">
                    <label class="font-weight-bold text-xl w-100">Date of Birth</label>
                    <p class="mb-0">{{ showDate(viewSelectedUser.dob) }}</p>
                </div>
            </div>-->

      <!--<div class="grid grid-cols-3 gap-x-3 gap-y-3 mt-4">
                <h3 class="col-span-3">Address</h3>
                <div class="col-span-2 border border-grey rounded p-2">
                    <label class="font-weight-bold text-xl w-100">Address</label>
                    <p class="mb-0">{{viewSelectedUser.address}}</p>
                </div>
                <div class="border border-grey rounded p-2">
                    <label class="font-weight-bold text-xl w-100">Post Code</label>
                    <p class="mb-0">{{viewSelectedUser.post_code}}</p>
                </div>
                <div class="col-span-2 border border-grey rounded p-2">
                    <label class="font-weight-bold text-xl w-100">Suburb</label>
                    <p class="mb-0">{{viewSelectedUser.suburb}}</p>
                </div>
                <div class="border border-grey rounded p-2">
                    <label class="font-weight-bold text-xl w-100">State</label>
                    <p class="mb-0">{{viewSelectedUser.state}}</p>
                </div>
            </div>-->
    </modal>

    <modal
      v-if="viewEntryOpen"
      @closeModal="viewEntryOpen = false"
      containerMaxWidth="max-w-7xl"
      scrollBehaviour="overflow-y-auto"
    >
      <p class="text-lg font-weight-bold mt-1">
        Showing:
        <span v-if="decPagination.per_page < decPagination.total">{{
          decPagination.per_page
        }}</span>
        <span v-else>{{ decPagination.total }}</span>
        / {{ decPagination.total }}
      </p>
      <table class="table-auto w-full border border-gray-500" v-if="templates">
        <thead>
          <tr>
            <th class="px-4 py-2 border-r border-gray-500">Status</th>
            <th class="px-4 py-2 border-r border-gray-500">Passed</th>
            <th class="px-4 py-2 border-r border-gray-500">Date Sent</th>
            <th
              class="px-4 py-2 border-r border-gray-500"
              v-for="(question, index) in data.questions"
              :key="index"
            >
              {{ question }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-if="data.decs"
            v-for="(item, index) in data.decs.data"
            :key="item.id"
            :class="[
              {
                'bg-gray-100': index % 2 !== 0,
                'opacity-50': item.deleted_at !== null,
              },
            ]"
          >
            <td class="border border-gray-500 px-4 py-2 text-center">
              {{ item.status }}
            </td>
            <td class="border border-gray-500 px-4 py-2 text-center">
              {{ item.passed === 1 ? "Yes" : "No" }}
            </td>
            <td class="border border-gray-500 px-4 py-2 text-center">
              {{ item.date }}
            </td>
            <td
              class="border border-gray-500 px-4 py-2 text-center"
              v-for="(answer, answerIndex) in item.answerList"
              :key="answerIndex"
            >
              {{ Object.values(answer).includes("No") ? "No" : "Yes" }}
            </td>
          </tr>
        </tbody>
      </table>

      <div class="text-center mt-2" v-if="decPagination.last_page !== 1">
        <jet-nav-link
          v-if="decPagination.prev !== null"
          :href="decPagination.first_link"
          :class="''"
          ><<</jet-nav-link
        >
        <span v-else class="text-black-200"><<</span>
        <jet-nav-link
          v-if="decPagination.prev !== null"
          :href="decPagination.prev"
          :class="'mr-2 ml-1'"
          ><</jet-nav-link
        >
        <span v-else class="text-black-200 mr-2 ml-1"><</span>
        <span>{{ pagination.current_page }} / {{ pagination.last_page }}</span>
        <jet-nav-link
          v-if="decPagination.next !== null"
          :href="decPagination.next"
          :class="'ml-2 mr-1'"
          >></jet-nav-link
        >
        <span v-else class="text-black-200 ml-2 mr-1">></span>
        <jet-nav-link
          v-if="decPagination.next !== null"
          :href="decPagination.last_link"
          :class="''"
          >>></jet-nav-link
        >
        <span v-else class="text-black-200">>></span>
      </div>
    </modal>
    <modal
      v-if="showUserDelete"
      @closeModal="showUserDelete = false"
      containerMaxWidth="max-w-3xl"
      class="forpopup"
    >
      <div class="close-btn">
        <a href="#"
          ><em class="fas fa-times" @click.prevent="closepopup"></em
        ></a>
      </div>
      <div class="text-center">
        <h4 class="text-center mb-3" v-html="'Delete Admin User'"></h4>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <strong
            class="mb-0 text-center"
            style="display: block; font-size: 20px"
          >
            Are you sure you want to be disable?
          </strong>
          </div>
          </div>
      <div class="row d-flex justify-center mt-8">
        <secondary-button type="button" @buttonClick="deleteUser">
          Yes
        </secondary-button>

        <secondary-button type="button" @buttonClick="closepopup">
          No
        </secondary-button>
      </div>
    </modal>
    <modal
      v-if="showPermanntDelete"
      @closeModal="showPermanntDelete = false"
      containerMaxWidth="max-w-3xl"
      class="forpopup"
    >
      <div class="close-btn">
        <a href="#"
          ><em class="fas fa-times" @click.prevent="closepopup"></em
        ></a>
      </div>
      <div class="text-center">
        <h4 class="text-center mb-3" v-html="'Delete Admin User'"></h4>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <strong
            class="mb-0 text-center"
            style="display: block; font-size: 20px"
          >
            Are you sure you want to delete permanntly?
          </strong>
          </div>
          </div>
      <div class="row d-flex justify-center mt-8">
        <secondary-button type="button" @buttonClick="permanntDelete">
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
      declarations: [],
      selectedDeclaration: "",
      templates: this.data.users.data,
      addNewOpen: false,
      locations: [],
      roles: [],
      newUser: {
        id: 0,
        dob: "",
        first_name: "",
        password: "",
        last_name: "",
        groups: "",
        location: null,
        user_role_id: null,
        phone: "",
        email: "",
        employee_code: "",
        address: "",
        suburb: "",
        state: "",
        post_code: "",
      },
      newUserErrors: {
        first_name: false,
        last_name: false,
      },
      viewUserOpen: false,
      showUserDelete:false,
      deleteUserId:0,
      showPermanntDelete:false,
      deletePermanntId:0,
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
    console.log("User Data List:", this.data.users);
    this.setLocations(this.data.locations);
    this.setRoles(this.data.roles);
    this.setDeclarations(this.data.declarations);
    if (document.getElementById("current_page")) {
      document.getElementById("current_page").focus();
    }
  },
  methods: {
    closepopup() {
      location.reload();
    },
    openUserDelete(id) {
      this.showUserDelete = true;
      this.deleteUserId = id;
    },
    deleteUser() {
      if (this.deleteUserId != 0) {
        axios.delete("/admin/api/admin-users/delete/" + this.deleteUserId)
        .then((result) => {
          result = result.data;
          this.templates = result.data.users.data;
          this.showNotification(result);
        });
      }
      this.showUserDelete = false;
    },
    openPermanntDelete(id){
      this.showPermanntDelete = true;
      this.deletePermanntId = id;
    },
    permanntDelete() {
      if (this.deletePermanntId != 0) {
      axios
        .delete("/admin/api/admin-users/permandelete/" + this.deletePermanntId)
        .then((result) => {
          result = result.data;
          this.templates = result.data.users.data;
          this.showNotification(result);
        });
      }
      this.showPermanntDelete = false;
    },
    showDate(date) {
      var date = date;
      if (date) {
        var datearray = date.split("-");
        var newdate = datearray[2] + "/" + datearray[1] + "/" + datearray[0];
        return newdate;
      } else {
        return "";
      }
    },
    changePage() {
      if (this.pagination.current_page > this.pagination.last_page) {
        this.pagination.current_page = this.pagination.last_page;
      } else if (this.pagination.current_page < 1) {
        this.pagination.current_page = 1;
      }

      this.$inertia.visit(
        "/admin/admin-users?page=" + this.pagination.current_page
      );
    },
    searchUsers(string) {
      axios
        .post("/admin/api/admin-users/search", { search: string })
        .then((result) => {
          result = result.data;
          this.refreshPage(result);
        });
    },
    enableUser(id) {
      let enableObj = {
        id: id,
        enable: true,
      };
      axios.put("/admin/api/admin-users/update", enableObj)
      .then((result) => {
        result = result.data;
        this.refreshPage(result);
      });
    },
    refreshPage(result) {
      this.templates = result.data.users.data;
      this.pagination = {
        current_page: result.data.users.current_page,
        total: result.data.users.total,
        per_page: result.data.users.per_page,
        first_link: result.data.users.first_page_url,
        last_link: result.data.users.last_page_url,
        last_page: result.data.users.last_page,
        next: result.data.users.next_page_url,
        prev: result.data.users.prev_page_url,
      };
      this.showNotification(result);
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
    addNew() {
      if (this.validate() === true) {
        if (this.newUser.id === 0)
          axios
            .post("/admin/api/admin-users/create", this.newUser)
            .then((result) => {
              result = result.data;
              this.refreshPage(result);
            });
        else
          axios
            .put("/admin/api/admin-users/update", this.newUser)
            .then((result) => {
              result = result.data;
              this.refreshPage(result);
            });

        this.resetNewTemplateBuilder();
        this.addNewOpen = false;
      }
    },

    viewItem() {
      if (this.selectedDeclaration !== 0)
        this.$inertia
          .post(
            "/admin/api/admin-users/" +
              this.viewSelectedUser.id +
              "/declaration/" +
              this.selectedDeclaration
          )
          .then(() => {
            this.viewEntryOpen = true;
            this.viewUserOpen = false;
            this.decPagination = {
              current_page: this.data.decs.current_page,
              total: this.data.decs.total,
              per_page: this.data.decs.per_page,
              first_link: this.data.decs.first_page_url,
              last_link: this.data.decs.last_page_url,
              last_page: this.data.decs.last_page,
              next: this.data.decs.next_page_url,
              prev: this.data.decs.prev_page_url,
            };
          });
    },
    declarationChanged(declaration) {
      this.selectedDeclaration = declaration;
    },
    locationChanged(location) {
      this.newUser.location = location;
    },
    roleChanged(roll) {
      this.newUser.roll = roll;
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
      let flag = true;

      console.log(this.newUser.valid_until);

      if (this.newUser.first_name.trim() === "") {
        flag = false;
        this.newUserErrors.first_name = "Name can not be blank";
      } else {
        this.newUserErrors.first_name = null;
      }

      if (this.newUser.last_name.trim() === "") {
        flag = false;
        this.newUserErrors.last_name = "Name can not be blank";
      } else {
        this.newUserErrors.last_name = null;
      }

      if (this.newUser.email.trim() === "") {
        flag = false;
        this.newUserErrors.email = "Email can not be blank";
      } else {
        this.newUserErrors.email = null;
      }

      return flag;
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
    editItem(userObj) {
      this.addNewOpen = true;

      let userObjData = {
        id: userObj.id,
        dob: userObj.dob,
        first_name: userObj.first_name,
        last_name: userObj.last_name,
        groups: userObj.groups,
        location: userObj.location,
        user_role_id: userObj.user_role_id,
        phone: userObj.phone,
        employee_code: userObj.employee_code,
        email: userObj.email,
        address: userObj.address ? userObj.address.address : null,
        suburb: userObj.address ? userObj.address.suburb : null,
        state: userObj.address ? userObj.address.state : null,
        post_code: userObj.address ? userObj.address.post_code : null,
      };
      this.newUser = userObjData;
    },
    viewUser(userObj) {
      this.viewSelectedUser = {};
      this.viewUserOpen = true;

      let userObjData = {
        id: userObj.id,
        dob: userObj.dob,
        first_name: userObj.first_name,
        last_name: userObj.last_name,
        groups: userObj.groups,
        location: userObj.location,
        user_role_id: userObj.user_role_id,
        phone: userObj.phone,
        employee_code: userObj.employee_code,
        email: userObj.email,
        address: userObj.address ? userObj.address.address : null,
        suburb: userObj.address ? userObj.address.suburb : null,
        state: userObj.address ? userObj.address.state : null,
        post_code: userObj.address ? userObj.address.post_code : null,
        roleName: userObj.roleName,
      };
      this.viewSelectedUser = userObjData;
    },
    showNewTemplate() {
      this.addNewOpen = true;
      this.resetNewTemplateBuilder();
    },
    getAddress(userAddress) {
      return `${userAddress.address}, ${userAddress.suburb}, ${userAddress.state} ${userAddress.post_code}`;
    },
  },
};
</script>
