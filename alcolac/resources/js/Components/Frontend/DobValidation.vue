<template>
  <div>
    <p v-if="!userDobValidation" class="text-red-600 text-sm text-center">
      Your date of birth does not match our records. Please contact your manager
      immediately, or try again.
    </p>
    <p v-else class="text-center">
      Hi {{ name }},<br />So we can verify your details please confirm your Date
      of Birth.
    </p>
    <form @submit.prevent="submit" class="declaration-form-box">
      <div class="text-center">
        <div class="row mt-4">
          <div class="col-4">
            <select
              name="day"
              v-model="form.day"
              class="declaration-form-control"
            >
              <option disabled>DD</option>
              <option v-for="(day, index) in days" :key="index" :value="day">
                {{ day }}
              </option>
            </select>
            <p v-if="errors.form.day !== null" class="text-red-600 text-sm">
              {{ errors.form.day }}
            </p>
          </div>
          <div class="col-4">
            <select
              name="month"
              v-model="form.month"
              class="declaration-form-control"
            >
              <option disabled>MM</option>
              <option
                v-for="(month, index) in months"
                :key="index"
                :value="pad(index + 1, 2)"
              >
                {{ month }}
              </option>
            </select>
            <p v-if="errors.form.month !== null" class="text-red-600 text-sm">
              {{ errors.form.month }}
            </p>
          </div>
          <div class="col-4">
            <select
              name="year"
              v-model="form.year"
              class="declaration-form-control"
            >
              <option disabled>YYYY</option>
              <option v-for="(year, index) in years" :key="index" :value="year">
                {{ year }}
              </option>
            </select>
            <p v-if="errors.form.year !== null" class="text-red-600 text-sm">
              {{ errors.form.year }}
            </p>
          </div>
        </div>
      </div>
      <div class="button-wrapper text-center mt-8">
        <secondary-button @click="submit">Verify</secondary-button>
      </div>
    </form>

    <div class="verify-text">Verification Step 1/2</div>
  </div>
</template>

<script>
import PrimaryButton from "../Buttons/Primary";
import SecondaryButton from "../Buttons/Secondary";
export default {
  components: {
    PrimaryButton,
    SecondaryButton,
  },
  props: ["userId", "name", "days", "months", "years", "dobValidation"],
  data() {
    return {
      userDobValidation: "",
      form: {
        day: "DD",
        month: "MM",
        year: "YYYY",
        userId: this.userId,
      },
      errors: {
        form: {
          day: null,
          month: null,
          year: null,
        },
      },
    };
  },
  mounted() {
    this.userDobValidation = this.dobValidation;
  },
  methods: {
    submit() {
      let vm = this;
      if (this.validate() === true)
        axios.post("/declarations/dob-validation", vm.form).then((data) => {
          if (data && data.data.fail) {
            vm.userDobValidation = false;
            return false;
          } else {
            this.$emit("continue", vm.form);
          }
        });
    },
    validate() {
      let flag = true;

      if (this.form.day.trim() === "" || this.form.day === "DD") {
        flag = false;
        this.errors.form.day = "Please select a day";
      } else {
        this.errors.form.day = null;
      }

      if (this.form.month.trim() === "" || this.form.month === "MM") {
        flag = false;
        this.errors.form.month = "Please select a month";
      } else {
        this.errors.form.month = null;
      }

      if (this.form.year === "" || this.form.year === "YYYY") {
        flag = false;
        this.errors.form.year = "Please select a year";
      } else {
        this.errors.form.year = null;
      }

      return flag;
    },
  },
};
</script>
