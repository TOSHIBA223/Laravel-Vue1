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
        Send an SMS
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-3 gap-x-8">
        <div class="bg-white overflow-hidden shadow-xl p-12 col-span-2">
          <div class="w-full mt-4">
            <form @submit.prevent="sendSms">
              <div class="grid grid-cols-3 gap-x-2 mb-4">
                <secondary-button
                  :disabled="sendType === 'user'"
                  @buttonClick="changeSendType('user')"
                >
                  Users
                </secondary-button>
                <secondary-button
                  :disabled="sendType === 'group'"
                  @buttonClick="changeSendType('group')"
                >
                  Groups
                </secondary-button>
                <secondary-button
                  :disabled="sendType === 'location'"
                  @buttonClick="changeSendType('location')"
                >
                  Locations
                </secondary-button>
              </div>
              <Select2
                v-model="selectedUsers"
                :options="userOptions"
                v-if="sendType === 'user'"
                :settings="{ multiple: true }"
                class="w-full"
              />

              <ams-select
                @optionSelected="selectGroup"
                :options="groupOptions"
                v-if="sendType === 'group'"
                class="w-full"
              />

              <ams-select
                @optionSelected="selectLocation"
                :options="locationOptions"
                v-if="sendType === 'location'"
                class="w-full"
              />
              <div class="grid grid-cols-2 gap-x-4 mt-4">
                <vue-select-opt-group
                  :options="contentTags"
                  :resetOnChange="true"
                  placeholderOption="Select a Content Tag"
                  @optionSelected="contentTagSelected"
                  class="w-full"
                />
                <ams-select
                  @optionSelected="selectFile"
                  :resetOnChange="true"
                  placeholderOption="Select a File"
                  :options="fileOptions"
                  class="w-full"
                />
                <textarea
                  v-model="message"
                  class="p-2 border w-full col-span-2"
                  placeholder="Add Message Here"
                  rows="10"
                ></textarea>
                <jet-input-error :message="errors.message" />
              </div>
              <secondary-button type="submit" class="mt-4"
                >Send SMS</secondary-button
              >
              <secondary-button
                type="button"
                class="mt-4 float-right"
                @buttonClick="viewSchedule = true"
              >
                Schedule SMS
              </secondary-button>
            </form>
          </div>
        </div>
        <phone
          :message-preview="message"
          :type="sendType"
          :users="selectedUsers"
          :group="selectedGroup"
          :location="selectedLocation"
        />
      </div>
    </div>
    <modal
      v-if="viewSchedule"
      @closeModal="viewSchedule = false"
      containerMaxWidth="max-w-3xl"
      scrollBehaviour="overflow-y-auto"
      class="forpopup closbtndec"
    >
      <div class="close-btn">
        <a href="#"
          ><em class="fas fa-times" @click.prevent="viewSchedule = false"></em
        ></a>
      </div>
      <div class="grid grid-cols-3 mb-2 gap-x-4 mt-4 top-btn-row">
        <jet-input
          type="datetime-local"
          name="scheduleDate"
          v-model="scheduleDate"
          id="scheduleDate"
          placeholder="Schedule Date"
          class="mt-4 float-right"
        ></jet-input>

        <secondary-button type="button" @buttonClick="scheduleSms">
          Schedule
        </secondary-button>
      </div>
    </modal>
  </app-layout>
</template>

<script>
import AppLayout from "Layout/AppLayout";
import VueSelectOptGroup from "Component/Inputs/SelectOptGroups";
import PrimaryButton from "Component/Buttons/Primary";
import SecondaryButton from "Component/Buttons/Secondary";
import AmsSelect from "Component/Inputs/Select";
import Modal from "Component/Modal";
import JetInput from "Jetstream/Input";
import JetInputError from "Jetstream/InputError";
import Phone from "./Phone";
import Select2 from "v-select2-component";

export default {
  components: {
    AppLayout,
    VueSelectOptGroup,
    PrimaryButton,
    SecondaryButton,
    Modal,
    JetInput,
    JetInputError,
    Phone,
    AmsSelect,
    Select2,
  },
  props: ["data", "systemSuccess", "contentTags"],
  data() {
    return {
      sendType: "user",
      selectedUsers: ["all"],
      selectedGroup: "all",
      selectedLocation: "all",
      userOptions: [{ id: "all", text: "All" }],
      locationOptions: [{ id: "all", name: "All" }],
      groupOptions: [{ id: "all", name: "All" }],
      tagOptions: [],
      fileOptions: [],
      errors: { message: false },
      message: "",
      scheduleDate: "",
      viewSchedule: false,
    };
  },
  mounted() {
    console.log(this);
    this.setUserOptions(this.data.users);
    this.setLocationOptions(this.data.locations);
    this.setGroupOptions(this.data.groups);
    this.setFiles(this.data.files);
  },
  methods: {
    contentTagSelected(value) {
      this.message = this.message + " " + value;
    },
    scheduleSms() {
      this.viewSchedule = false;
      this.errors.message = false;

      if (this.message.trim() !== "") {
        let sendTo;
        if (this.sendType === "user") sendTo = this.selectedUsers;

        if (this.sendType === "group") sendTo = this.selectedGroup;

        if (this.sendType === "location") sendTo = this.selectedLocation;

        let sendData = {
          message: this.message,
          type: this.sendType,
          date: this.scheduleDate,
          to: sendTo,
        };
        axios.post("/admin/api/sms/schedule", sendData).then((result) => {
          result = result.data;
          this.showNotification(result);
        });
      } else {
        this.errors.message =
          "Please write your SMS before trying to schedule.";
      }
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
    sendSms() {
      this.errors.message = false;

      if (this.message.trim() !== "") {
        let sendTo;
        if (this.sendType === "user") sendTo = this.selectedUsers;

        if (this.sendType === "group") sendTo = this.selectedGroup;

        if (this.sendType === "location") sendTo = this.selectedLocation;

        let sendData = {
          message: this.message,
          type: this.sendType,
          to: sendTo,
        };
        axios.post("/admin/api/sms/send", sendData).then((result) => {
          result = result.data;
          this.showNotification(result);
        });
      } else {
        this.errors.message = "Please write your SMS before trying to send.";
      }
    },
    selectGroup(group) {
      this.selectedGroup = group;
    },
    selectLocation(location) {
      this.selectedLocation = location;
    },
    changeSendType(type) {
      this.selectedUsers = ["all"];
      this.selectedGroup = "all";
      this.selectedLocation = "all";
      this.sendType = type;
    },
    setUserOptions(users) {
      const instance = this;
      users.forEach((user) => {
        instance.userOptions.push({
          text: `${user.first_name} ${user.last_name}`,
          id: user.id,
        });
      });
    },
    selectFile(value) {
      this.message = this.message + " " + value;
    },
    setLocationOptions(locations) {
      const instance = this;
      locations.forEach((location) => {
        instance.locationOptions.push({
          name: location.location,
          id: location.location,
        });
      });
    },
    setGroupOptions(groups) {
      const instance = this;
      groups.forEach((group) => {
        instance.groupOptions.push({
          id: group.groups,
          name: group.groups,
        });
      });
    },
    setFiles(files) {
      const instance = this;
      files.forEach((file) => {
        instance.fileOptions.push({
          id: `${instance.data.baseUrl}/${encodeURIComponent(file.path)}`,
          name: file.name,
        });
      });
    },
  },
};
</script>
