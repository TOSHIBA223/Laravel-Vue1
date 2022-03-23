<template>
  <app-layout>
    <!-- <transition
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
        class="
          bg-green-400
          p-4
          fixed
          left-0
          right-0
          top-0
          text-center
          forsenddec
        "
        v-if="systemSuccess"
        @click="systemSuccess = false"
      >
        <div class="max-w-xl w-full mx-auto">
          <span class="text-white"> {{ systemSuccess }} x </span>
        </div>
      </div>
    </transition> -->
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        QR Code List
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl p-12">
          <div class="w-full mt-4">
            <h3 class="clearfix mb-3">
              <!--<jet-input type="text" id="search" placeholder="Search" @input="searchUsers" class="w-1/2"  ></jet-input>-->
              <secondary-button
                type="button"
                :class="'float-right'"
                @buttonClick="showNewTemplate"
                v-if="data.permission.perm_create"
              >
                New QR Code
              </secondary-button>

              <secondary-button
                type="button"
                :class="'float-right'"
                @buttonClick="showAllQRCheckin"
                v-if="data.permission.perm_create"
              >
                View All QR Checkin
              </secondary-button>

              <secondary-button
                type="button"
                :class="'float-right'"
                @buttonClick="showAllQREmp"
                v-if="data.permission.perm_create"
              >
                View New contractor
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
                    Area Name
                  </th>
                  <th class="px-4 py-2 border-r border-gray-500 text-center">
                    URL
                  </th>
                  <th class="px-4 py-2 border-r border-gray-500 text-center">
                    Status
                  </th>

                  <th class="px-4 py-2 text-center">Actions</th>
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
                    {{ item.area_name }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.qr_url }}
                  </td>
                  <td
                    class="border border-gray-500 px-4 py-2 text-center"
                    v-if="item.status == 1"
                  >
                    {{ "Active" }}
                  </td>
                  <td
                    class="border border-gray-500 px-4 py-2 text-center"
                    v-else
                  >
                    {{ "In-active" }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    <i
                      class="fa fa-eye text-green-400 mr-4 cursor-pointer mr-2"
                      data-toggle="tooltip"
                      data-placement="bottom"
                      title="View QR Code"
                      @click="viewUser(item)"
                      v-if="data.permission.perm_view"
                    ></i>

                    <!--<i class="fa fa-edit text-green-400 mr-4 cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Update QR Code"
                                       @click="editItem(item)" v-if="data.permission.perm_update"></i>-->

                    <i
                      class="fa fa-book text-green-400 mr-4 cursor-pointer mr-2"
                      data-toggle="tooltip"
                      data-placement="bottom"
                      title="View QR Checkin"
                      @click="viewCheckin(item.area_name)"
                      v-if="data.permission.perm_view"
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
                        title="Disable QR Code"
                        @click="openUserDelete(item.id)"
                      ></i>

                      <i
                        v-else
                        class="fa fa-plus-circle text-yellow-300 cursor-pointer mr-4"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Enble QR Code"
                        @click="enableUser(item.id)"
                      ></i>

                      <i
                        class="fas fa-trash-alt text-yellow-300 cursor-pointer"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Delete QR Code"
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
              <label class="font-weight-bold text-xl w-100">Area Name</label>
              <jet-input
                type="text"
                id="area_name"
                placeholder="Area Name"
                v-model="newUser.area_name"
                @keydown="nameKeydown($event)"
                class="w-full"
              ></jet-input>
              <jet-input-error :message="newUserErrors.area_name" />
            </div>
          </div>
        </form>

        <secondary-button type="button" @buttonClick="addNew()" class="mt-4">
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

        <label class="font-weight-bold text-xl w-100"
          >Area Name(Building Name)</label
        >
        <div class="border border-grey rounded p-2">
          <p class="mb-0">{{ viewSelectedUser.area_name }}</p>
        </div>
      </div>

      <div class="grid grid-cols-2 mt-4 gap-x-3">
        <label class="font-weight-bold text-xl w-100">QR URL</label>
        <div class="border border-grey rounded p-2">
          <p class="mb-0">{{ viewSelectedUser.qr_url }}</p>
        </div>
      </div>

      <div class="grid grid-cols-2 mt-4 gap-x-3">
        <label class="font-weight-bold text-xl w-100">QR Image</label>
        <div class="border border-grey rounded p-2">
          <p class="mb-0" :id="'print-image' + viewSelectedUser.id">
            <img :src="url + '/images/' + viewSelectedUser.qr_image + '.png'" />
          </p>
        </div>
      </div>

      <secondary-button
        type="button"
        @buttonClick="print(viewSelectedUser.id)"
        class="mt-4"
      >
        Print QR Image
      </secondary-button>

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
        <h4 class="text-center mb-3" v-html="'Delete QrCode'"></h4>
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
        <h4 class="text-center mb-3" v-html="'Delete Qr Code'"></h4>
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
      url: window.location.origin,
      selectedDeclaration: "",
      templates: this.data.users.data,
      addNewOpen: false,
      locations: [],
      roles: [],
      newUser: {
        id: 0,
        area_name: "",
      },
      newUserErrors: {
        area_name: false,
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
    this.setLocations(this.data.locations);
    this.setRoles(this.data.roles);
    this.setDeclarations(this.data.declarations);
  },
  methods: {
    closepopup() {
      location.reload();
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

    nameKeydown(e) {
      console.log("sdfds");
      if (/^\W$/.test(e.key)) {
        e.preventDefault();
      }
    },
    openUserDelete(id) {
      this.showUserDelete = true;
      this.deleteUserId = id;
    },
    deleteUser() {
      if (this.deleteUserId != 0) {
        axios.delete("/admin/api/qr-codes/delete/" + this.deleteUserId)
        .then((result) => {
          result = result.data;
          this.templates = result.data.users.data;
          this.showNotification(result);
        });
      }
      this.showUserDelete = false;
    },
    viewCheckin(areanname) {
      window.location.href = "/admin/qr-codes/showqr/" + areanname;
    },
    openPermanntDelete(id){
      this.showPermanntDelete = true;
      this.deletePermanntId = id;
    },
    permanntDelete(id) {
      if (this.deletePermanntId != 0) {
      axios
        .delete("/admin/api/qr-codes/permandelete/" + this.deletePermanntId )
        .then((result) => {
          result = result.data;
          this.templates = result.data.users.data;
          this.showNotification(result);
        });
      }
      this.showPermanntDelete = false;
    },
    print(id) {
      const prtHtml = document.getElementById("print-image" + id).innerHTML;

      const WinPrint = window.open(
        "",
        "",
        "left=30px,top=30px,width=500px,height=400px,toolbar=0,scrollbars=0,status=0"
      );
      WinPrint.document.write(`<!DOCTYPE html>
            <html>

            <body>
                ${prtHtml}
            </body>
            </html>`);

      WinPrint.document.close();
      WinPrint.focus();
      WinPrint.print();
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
    searchUsers(string) {
      this.$inertia
        .post("/admin/api/admin-users/search", { search: string })
        .then(() => {
          this.templates = this.data.users.data;
          this.pagination = {
            current_page: this.data.users.current_page,
            total: this.data.users.total,
            per_page: this.data.users.per_page,
            first_link: this.data.users.first_page_url,
            last_link: this.data.users.last_page_url,
            last_page: this.data.users.last_page,
            next: this.data.users.next_page_url,
            prev: this.data.users.prev_page_url,
          };
        });
    },
    enableUser(id) {
      let enableObj = {
        id: id,
        enable: true,
      };
      axios.put("/admin/api/qr-codes/update", enableObj)
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

    addNew() {
      if (this.validate() === true) {
        if (this.newUser.id === 0)
          this.$inertia
            .post("/admin/api/qr-codes/create", this.newUser)
            .then(() => {
              this.templates = this.data.users.data;
            });
        else
          this.$inertia
            .put("/admin/api/qr-codes/update", this.newUser)
            .then(() => {
              this.templates = this.data.users.data;
            });

        this.resetNewTemplateBuilder();
        this.addNewOpen = false;
      }
    },

    viewItem() {
      if (this.selectedDeclaration !== 0)
        this.$inertia
          .post(
            "/admin/api/qr-codes/" +
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
    showAllQRCheckin() {
      window.location.href = "qr-checks";
    },
    showAllQREmp() {
      window.location.href = "qr-codes/contractor/new";
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

      var format = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;

      if (!this.newUser.area_name) {
        flag = false;
        this.newUserErrors.area_name = "Area name can not be blank";
      } else {
        this.newUserErrors.area_name = null;
      }

      var chekk = format.test(this.newUser.area_name);

      if (format.test(this.newUser.area_name)) {
        flag = false;
        this.newUserErrors.area_name =
          "Space and special character not allowed";
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
        area_name: userObj.area_name,
        qr_url: userObj.qr_url,
      };
      this.newUser = userObjData;
    },
    viewUser(userObj) {
      this.viewSelectedUser = {};
      this.viewUserOpen = true;

      let userObjData = {
        id: userObj.id,
        area_name: userObj.area_name,
        qr_image: userObj.qr_image,
        qr_url: userObj.qr_url,
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
