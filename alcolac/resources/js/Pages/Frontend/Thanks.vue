<template>
  <frontend-layout>
    <template #header> Thanks </template>

    <p v-if="systemSuccess" class="font-weight-bold text-green-500">
      {{ systemSuccess }}
    </p>

    <p class="text-center mb-4" v-if="alreadyCheckedIn">
      You are already checked in.
    </p>
  </frontend-layout>
</template>

<script>
import FrontendLayout from "../../Layouts/FrontendLayout";
import JetstreamInput from "../../Jetstream/Input";
import PrimaryButton from "../../Components/Buttons/Primary";
import JetNavLink from "../../Jetstream/NavLink";

export default {
  components: {
    FrontendLayout,
    JetstreamInput,
    PrimaryButton,
    JetNavLink,
  },
  props: ["systemSuccess", "alreadyCheckedIn"],
  data() {
    return {
      code: "",
    };
  },
  methods: {
    submit() {
      this.$inertia.visit("/dec/" + this.code, { method: "GET" });
    },
  },
};
</script>
