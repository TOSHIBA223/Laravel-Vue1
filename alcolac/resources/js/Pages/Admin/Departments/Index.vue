<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Department
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
                Add New
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
                  <th class="px-4 py-2 border-r border-gray-500">Employee_code</th>
                  <th class="px-4 py-2 border-r border-gray-500">Department</th>
                  <th class="px-4 py-2 border-r border-gray-500">Manager</th>
                  <th class="px-4 py-2 border-r border-gray-500">Location</th>
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
                    {{ item.employee_code }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.department }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.manager }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.location }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">

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
              <label class="font-weight-bold text-xl w-100">Employee Code</label>
              <jet-input
                type="text"
                id="employee_code"
                placeholder="Employee Code"
                v-model="newUser.employee_code"
                class="w-full"
              ></jet-input>
              <jet-input-error :message="newUserErrors.employee_code" />
            </div>
            <div>
              <label class="font-weight-bold text-xl w-100">Department</label>
              <jet-input
                type="text"
                id="department"
                placeholder="Department"
                v-model="newUser.department"
                class="w-full"
              ></jet-input>
              <jet-input-error :message="newUserErrors.department" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-x-2 gap-y-4 mt-4">
            <div>
              <label class="font-weight-bold text-xl w-100">Manager</label>
              <jet-input
                type="text"
                id="manager"
                placeholder="Manager"
                v-model="newUser.manager"
                class="w-full"
              ></jet-input>
              <jet-input-error :message="newUserErrors.manager" />
            </div>

            <div>
              <label class="font-weight-bold text-xl w-100">Location</label>
              <vue-select
                selectName="location"
                :options="resultTemplate"
                :selectedOption="newUser.location"
                placeholderOption="Select Location"
                class="w-full"
                @optionSelected="locationChanged"
              />
            </div>
          </div>
        </form>

        <secondary-button type="button" @buttonClick="addNew" class="mt-4">
          Save
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
      resultTemplate: [
        { id: "colac", name: "colac" },
        { id: "sunshine", name: "sunshine" },
      ],
      declarations: [],
      selectedDeclaration: "",
      templates: this.data.users.data,
      addNewOpen: false,
      locations: [],
      roles: [],
      newUser: {
        id: 0,
        dob: "",
        department:"",
        manager:"",
        groups: "",
        location: "",
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
        employee_code: false,
        department: false,
        manager: false,
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
        axios.delete("/admin/api/departments/delete/" + this.deleteUserId)
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
        .delete("/admin/api/departments/permandelete/" + this.deletePermanntId)
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
            .post("/admin/api/departments/create", this.newUser)
            .then((result) => {
                result = result.data;
                this.templates = result.data.users.data;
              this.refreshPage(result);
            });
        else
          axios
            .put("/admin/api/departments/update", this.newUser)
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
    resultChanged(result) {
      this.newUser.result = result;
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

    //   console.log(this.newUser.valid_until);

      if (this.newUser.employee_code.trim() === "") {
        flag = false;
        this.newUserErrors.employee_code = "employee_code can not be blank";
      } else {
        this.newUserErrors.employee_code = null;
      }

      if (this.newUser.department.trim() === "") {
        flag = false;
        this.newUserErrors.department = "department can not be blank";
      } else {
        this.newUserErrors.department = null;
      }

      if (this.newUser.manager.trim() === "") {
        flag = false;
        this.newUserErrors.manager = "manager can not be blank";
      } else {
        this.newUserErrors.manager = null;
      }
        console.log("Validate function is called.");
      return flag;
    },
    resetNewTemplateBuilder() {
      this.newUser = {
        id: 0,
        location: "",
        phone: "",
        employee_code: "",
        department: "",
        manager: "",
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
