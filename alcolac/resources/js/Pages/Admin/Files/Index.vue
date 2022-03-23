<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Files</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl p-12">
          <div class="w-full mt-4">
            <h3 class="clearfix mb-3">
              <form @submit.prevent="addNew">
                <jet-input
                  ref="newFile"
                  type="file"
                  placeholder="Name"
                  class="w-1/2"
                />
                <secondary-button type="submit" :class="'float-right'">
                  Upload File
                </secondary-button>
              </form>
            </h3>
            <table
              class="table-auto w-full border border-gray-500"
              v-if="data.files"
            >
              <thead>
                <tr>
                  <th class="px-4 py-2 border-r border-gray-500">Name</th>
                  <th class="px-4 py-2 border-r border-gray-500">Link</th>
                  <th class="px-4 py-2 border-r border-gray-500">Status</th>
                  <th class="px-4 py-2 border-r border-gray-500">Views</th>
                  <th class="px-4 py-2">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in data.files"
                  :key="item.id"
                  :class="[
                    {
                      'bg-gray-100': index % 2 !== 0,
                      'opacity-50': item.archived === 1,
                    },
                  ]"
                >
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.name }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ data.baseUrl }}file/{{ item.token }}
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    <span v-if="item.archived === 1">Archived</span>
                    <span v-else>Available</span>
                  </td>
                  <td class="border border-gray-500 px-4 py-2 text-center">
                    {{ item.views }}
                  </td>
                  <td
                    class="border border-gray-500 px-4 py-2 text-center"
                    style="width: 200px"
                  >
                    <i
                      class="fa fa-archive text-yellow-400 mr-4 cursor-pointer"
                      v-if="item.archived === 0"
                      @click="archiveItem(item.id, 'close')"
                    ></i>
                    <i
                      class="fa fa-archive text-yellow-400 mr-4 cursor-pointer"
                      v-else
                      @click="archiveItem(item.id, 'open')"
                    ></i>

                    <i
                      class="fa fa-book text-green-400 mr-4 cursor-pointer mr-2"
                      data-toggle="tooltip"
                      data-placement="bottom"
                      title="View File Log"
                      @click="viewfile(item.id)"
                    ></i>

                    <i
                      class="fas fa-trash-alt text-red-900 cursor-pointer"
                      data-toggle="tooltip"
                      data-placement="bottom"
                      title="Delete Admin User"
                      @click="openFileDelete(item.id)"
                    ></i>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <modal
      v-if="showFileDelete"
      @closeModal="showFileDelete = false"
      containerMaxWidth="max-w-3xl"
      class="forpopup"
    >
      <div class="close-btn">
        <a href="#"
          ><em class="fas fa-times" @click.prevent="closepopup"></em
        ></a>
      </div>
      <div class="text-center">
        <h4 class="text-center mb-3" v-html="'Delete File'"></h4>
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
import PrimaryButton from "Component/Buttons/Primary";
import SecondaryButton from "Component/Buttons/Secondary";
import Modal from "Component/Modal";
import JetInput from "Jetstream/Input";


export default {
  components: {
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    Modal,
    JetInput
  },
  props: ["data", "errors", "systemSuccess", "contentTags"],
  data() {
    return {
      showFileDelete:false,
      deleteFileId:0,
    };
  },
  mounted() {},
  methods: {
    closepopup() {
      location.reload();
    },
    addNew() {
      if (!this.$refs.newFile.$el.files[0]) {
        return;
      }
      let form = new FormData();
      form.append(
        "newFile",
        this.$refs.newFile.$el.files[0],
        this.$refs.newFile.$el.files[0].name
      );

      axios
        .post("/admin/api/files/upload", form, {
          headers: { "content-type": "multipart/form-data" },
        })
        .then((result) => {
          result = result.data;
          if (result.systemSuccess) {
            this.data = result.data;
            this.$toast.success(result.systemSuccess, {
              timeout: 3000,
            });
          } else {
            this.$toast.error(result.systemFail, {
              timeout: 3000,
            });
          }
        });

      this.$refs.newFile.value = null;
    },
    archiveItem(id, action) {
      let enableObj = {
        id: id,
        action: action,
      };
      this.$inertia.put("/admin/api/files/archive", enableObj).then(() => {
        this.templates = this.data.templates;
      });
    },
    openFileDelete(id) {
      this.showFileDelete = true;
      // this.deleteFileId = id;
    },
    permanntDelete(id) {
      let enableObj = {
        id: id,
      };
      axios.post("/admin/api/files/delete", enableObj).then((result) => {
        result = result.data;
        if (result.systemSuccess) {
          this.data = result.data;
          this.$toast.success(result.systemSuccess, {
            timeout: 3000,
          });
        } else {
          this.$toast.error(result.systemFail, {
            timeout: 3000,
          });
        }
      });
      this.showFileDelete = false;
    },
    viewfile(id) {
      window.location.href = "/admin/files/filelog/" + id;
    },
  },
};
</script>
