<template>
  <frontend-layout>
    <template #header>
      <h2>Is this you?</h2>
    </template>

    <br>
    <h4>Name: {{ employee_name }}</h4>
    <h4>DOB: {{ employee_dob }}</h4>

    <p class="text-center"></p>
    <div class="button-wrapper text-center mt-8">
      <secondary-button type="button" @buttonClick="submitResult" class="mt-4">
        Yes
      </secondary-button>
      <secondary-button type="button" @buttonClick="goBack" class="mt-4">
        No
      </secondary-button>
    </div>
  </frontend-layout>
</template>

<script>
import FrontendLayout from "../../Layouts/FrontendLayout";
import JetstreamInput from "../../Jetstream/Input";
import PrimaryButton from "../../Components/Buttons/Primary";
import SecondaryButton from "Component/Buttons/Secondary";
import JetNavLink from "../../Jetstream/NavLink";

export default {
  components: {
    FrontendLayout,
    JetstreamInput,
    PrimaryButton,
    JetNavLink,
    SecondaryButton,
  },
  props: ["employee_name", "employee_dob"],
  data() {
    return {
      code: "",
    };
  },
  methods: {
    goBack() {
      window.history.back();
    },
    submitResult() {
      this.$inertia.visit("/qr-codes/confirmation" + this.code, { method: "GET" });
    },
  },
};
</script>
