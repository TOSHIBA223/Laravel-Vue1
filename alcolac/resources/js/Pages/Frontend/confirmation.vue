<template>
  <frontend-layout>
    <template #header> Result of Rapid Antigen Test </template>

    <p class="text-center mb-4">
      <secondary-button
        type="button"
        @buttonClick="submitNegative"
        class="mt-4"
      >
        Negative
      </secondary-button>
      <danger-button
        class="mt-4"
        @buttonClick="submitPositive"
      >
        Positive
      </danger-button>
    </p>
  </frontend-layout>
</template>

<script>
import FrontendLayout from "../../Layouts/FrontendLayout";
import JetstreamInput from "../../Jetstream/Input";
import PrimaryButton from "../../Components/Buttons/Primary";
import DangerButton from "../../Components/Buttons/Danger";
import JetNavLink from "../../Jetstream/NavLink";
import SecondaryButton from "Component/Buttons/Secondary";

export default {
  components: {
    FrontendLayout,
    JetstreamInput,
    PrimaryButton,
    JetNavLink,
    SecondaryButton,
    DangerButton
  },
  props: ["systemSuccess"],
  data() {
    return {
      code: "",
    };
  },
  methods: {
    submitNegative() {
      this.$inertia.visit("/qr-codes/submit?result=negative", {
        method: "GET",
      });
    },
    submitPositive() {
      this.$inertia.visit("/qr-codes/submit?result=positive", {
        method: "GET",
      });
    },
  },
};
</script>
