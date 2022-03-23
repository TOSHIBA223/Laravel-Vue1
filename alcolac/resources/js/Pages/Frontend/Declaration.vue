<template>
  <frontend-layout>
    <template #header>
      {{ currentTitle }}
    </template>

    <!-- Declaration Expired -->
    <div v-if="status === 'expired'" class="text-center">
      <p class="font-weight-bold text-red-500">
        This declaration has expired. Please create a new code.
      </p>

      <p class="text-center">
        <jet-nav-link href="/new-declaration" class="send-new">
          Send me a new code
        </jet-nav-link>
      </p>
    </div>

    <!-- Declaration Expired -->
    <div v-if="status === 'invalid_token'" class="text-center">
      <h2>It looks like something went wrong.</h2>
      <p class="font-weight-bold text-red-500">
        There dosen't seem to be declaration with that token.
      </p>

      <p class="text-center">Please contact your manager.</p>
    </div>

    <!-- Sunshine Early Access -->
    <div v-if="status === 'sunshine_inactive'" class="text-center">
      <p class="font-weight-bold text-red-500">
        This declaration is not available until 4am. Please come back then to
        complete the declaration.
      </p>
    </div>

    <!-- Completed Declaration -->
    <div v-if="status === 'complete' && !showSubmitted" class="text-center">
      <p class="font-weight-bold text-red-500">
        It looks like you've already completed this declaration. Please contact
        your manager to confirm.
      </p>

      <p class="text-center">
        <jet-nav-link href="/new-declaration" class="send-new">
          Send me a new code
        </jet-nav-link>
      </p>
    </div>

    <!-- Void Declaration -->
    <div v-if="status === 'void'" class="text-center">
      <p class="font-weight-bold text-red-500">
        There is a newer declaration for you to complete. Please use the new
        link that has been sent to you to access your declaration.
      </p>
    </div>

    <span v-if="dobValidationOpen" class="w-full">
      <dob-validation
        :user-id="data.user.id"
        :name="data.user.first_name + ' ' + data.user.last_name"
        :days="data.days"
        :months="data.months"
        :years="data.years"
        :dob-validation="dobValidation"
        @continue="dobValidationFunction"
      />
    </span>

    <span v-if="addressValidationOpen" class="w-full">
      <address-validation
        :user-id="data.user.id"
        :address="data.address"
        :address-list="addressList"
        @continue="swapValidation"
      />
    </span>

    <span v-if="currentQuestion > 0" class="w-full">
      <declaration-item
        v-for="(question, index) in questions"
        :key="index"
        :ques="index + 1"
        :tot="questions.length"
        :field="question"
        v-if="currentQuestion === index + 1"
        @change="nextQuestion"
      />
    </span>

    <span v-if="showReview" class="w-full">
      <review
        :answers="answers"
        :address-validation="addressValidationRequired"
        :current-address="fullAddress"
        :tot="questions.length"
        :new-address="newAddress"
        :old-address="oldaddress"
        @restart="restart"
        @submit="submit"
      />
    </span>

    <span v-if="showSubmitted" class="w-full">
      <submitted
        :message="decPassed === 1 ? template.success : decPassed === 2 ? template.warn : template.failure"
        :success="decPassed"
        :color="
          decPassed === 1 ? template.success_color : decPassed === 2 ? template.warn_color : template.failure_color
        "
        :font="decPassed === 1 ? template.success_font : decPassed === 2 ? template.warn_font : template.failure_font"
      />
    </span>
  </frontend-layout>
</template>

<script>
import FrontendLayout from "../../Layouts/FrontendLayout";
import JetstreamInput from "../../Jetstream/Input";
import PrimaryButton from "../../Components/Buttons/Primary";
import JetNavLink from "../../Jetstream/NavLink";
import DobValidation from "../../Components/Frontend/DobValidation";
import AddressValidation from "../../Components/Frontend/AddressValidation";
import DeclarationItem from "../../Components/Frontend/DeclarationItem";
import Review from "../../Components/Frontend/Review";
import Submitted from "../../Components/Frontend/Submitted";

export default {
  components: {
    FrontendLayout,
    JetstreamInput,
    PrimaryButton,
    JetNavLink,
    DobValidation,
    AddressValidation,
    DeclarationItem,
    Review,
    Submitted,
  },
  props: [
    "data",
    "dobValidation",
    "addressList",
    "newAddress",
    "status",
    "template",
    "decPassed",
    "token",
    "dobSkip",
    "submitted",
  ],
  data() {
    return {
      code: "",
      currentQuestion: 0,
      addressValidationRequired: this.data.declaration.address_validation,
      addressValidationOpen: 0,
      dobValidationOpen: this.data.entry.short_valid == 2 ? 0 : this.data.declaration.dob_validation,
      questions: JSON.parse(this.data.declaration.fields),
      currentTitle: "ALC Employee Verification",
      answers: [],
      showReview: 0,
      showSubmitted: 0,
      failed: 0,
      oldaddress: this.data.address.address,
    };
  },
  methods: {
    submit() {
      this.$inertia
        .post("/declarations/submit", {
          token: this.data.token,
          answers: this.answers,
        })
        .then((data) => {
          this.showSubmitted = 1;
          this.showReview = 0;
        });
    },
    restart() {
      this.answers = [];
      if (this.dobValidation) {
        this.dobValidationOpen = true;
      } else if (this.addressValidationRequired) {
        this.addressValidationOpen = true;
      } else {
        this.currentQuestion = 1;
        this.currentTitle =
          "ALC Employee " + this.data.declaration.name + "Declaration";
        this.addressValidationOpen = false;
      }
      this.showReview = false;
      this.addressValidationOpen = false;
      this.dobValidation = 2;
    },
    nextQuestion(output) {
      this.answers.push({
        name: output.answerName,
        label: output.label,
        definition: output.definition,
        answer: output.answer,
        failed: output.failed,
        warned: output.warned,
      });

      if (output.failed === true) this.failed = 1;

      if (this.currentQuestion === this.questions.length) {
        this.currentQuestion = 0;
        this.showReview = 1;
      } else this.currentQuestion = this.currentQuestion + 1;

      this.currentTitle =
        "ALC Employee " + this.data.declaration.name + " Declaration";
    },

    dobValidationFunction(form) {
      this.dobValidationOpen = 0;
      this.addressValidationOpen = 1;
    },
    swapValidation(closeDob) {
      if (closeDob) this.dobValidationOpen = 0;

      if (this.status === null) {
        if (
          this.dobValidationOpen === 0 &&
          this.addressValidationRequired &&
          !this.addressValidationOpen
        ) {
          this.addressValidationOpen = 1;
        } else if (
          this.dobValidationOpen === 0 &&
          this.addressValidationRequired === 0
        ) {
          this.currentQuestion = 1;
          this.currentTitle =
            "ALC Employee " + this.data.declaration.name + " Declaration";
        } else if (this.addressValidationOpen === 1) {
          this.addressValidationOpen = 0;
          this.currentQuestion = 1;
          this.currentTitle =
            "ALC Employee " + this.data.declaration.name + " Declaration";
        }
      } else {
        this.dobValidationOpen = 0;
        this.addressValidationOpen = 0;
      }
    },
  },
  mounted() {
    this.swapValidation();
  },
  watch: {
    dobValidation(val) {
      if (val === true) {
        this.swapValidation(true);
      }
    },
  },
  computed: {
    fullAddress() {
      if (Object.keys(this.data.address).length) {
        // return `${this.data.address.address}, ${this.data.address.suburb}, ${this.data.address.state} ${this.data.address.post_code}`;
        return `${this.data.address.address}`;
      } else {
        return "--";
      }
    },

    getoldAddress() {
      return `${this.data.address.address}`;
    },
  },
};
</script>
