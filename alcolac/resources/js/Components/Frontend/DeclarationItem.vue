<template>
  <div class="w-full survey-que-box">
    <!--<h4 class="text-base font-weight-bold text-center my-4">{{field.label}}</h4>-->

    <h3 class="que-title" v-bind:order="ques" v-if="field.label">
      {{ field.label }}
    </h3>
    <h3
      :class="
        field.label ? 'que-defination' : 'que-defination que-defination-single'
      "
    >
      {{ field.definition }}
    </h3>
    <div
      class="button-wrapper mt-3 grid grid-cols-2 gap-x-4 declaration-form-box"
    >
      <secondary-button
        @buttonClick="
          $emit('change', {
            answerName: field.name,
            answer: field.success,
            label: field.label,
            definition: field.definition,
            failed: false,
            warned: false,
          })
        "
      >
        {{ field.success }}
      </secondary-button>

      <danger-button
        v-if="field.failure"
        @buttonClick="
          $emit('change', {
            answerName: field.name,
            answer: field.failure,
            label: field.label,
            definition: field.definition,
            failed: true,
            warned: false,
          })
        "
      >
        {{ field.failure }}
      </danger-button>

      <danger-button
        v-if="field.warn"
        @buttonClick="
          $emit('change', {
            answerName: field.name,
            answer: field.warn,
            label: field.label,
            definition: field.definition,
            warned: true,
            failed: false,
          })
        "
      >
        {{ field.warn }}
      </danger-button>
    </div>
    <!-- <div class="verify-text">Declaration Step {{ ques }}/{{tot}}</div> -->
  </div>
</template>

<script>
import PrimaryButton from "../Buttons/Primary";
import SecondaryButton from "../Buttons/Secondary";
import DangerButton from "../Buttons/Danger";
export default {
  components: {
    PrimaryButton,
    SecondaryButton,
    DangerButton,
  },
  props: ["field", "ques", "tot"],
};
</script>
