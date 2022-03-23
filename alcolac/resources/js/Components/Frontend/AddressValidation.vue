<template>
  <div>
    <div v-if="!showAddressCreation" class="text-center">
      <p class="text-center text-base">Is this your current address?</p>
      <p class="text-center text-lg font-weight-bold">
        {{ fullAddress }}
      </p>
      <div class="row text-center">
        <div class="col-sm-6 declaration-form-box mw-100">
          <secondary-button
            type="button"
            class="w-full mb-2"
            @buttonClick="$emit('continue')"
          >
            Yes
          </secondary-button>
        </div>
        <div class="col-sm-6 declaration-form-box mw-100">
          <button
            type="button"
            class="w-full mb-2"
            @click="showAddressCreation = true"
            style="
              background: url('/media/company/stone-bg.jpg') 50% center
                no-repeat;
              color: #fff;
            "
          >
            Change Address
          </button>
        </div>
      </div>
    </div>

    <div v-if="showAddressCreation" class="declaration-form-box mx-auto">
      <p class="text-center text-base">Update your address below</p>
      <div class="text-center">
        <jetstream-input
          type="text"
          class="declaration-form-control"
          @input="setAddressEntry"
          :v-model="addressEntry"
          :value="addressEntry"
          placeholder="Enter Address"
        />
      </div>
      <div v-if="internalAddressList !== null">
        <div
          v-for="(address, index) in internalAddressList"
          class="cursor-pointer px-4 py-2 hover:bg-blue-400 hover:text-white"
          :key="index"
          @click="setAddress(address.address.result.formatted_address)"
          :class="{ 'bg-gray-100': index % 2 }"
        >
          {{ address.address.result.formatted_address }}
        </div>
      </div>

      <secondary-button
        type="button"
        class="w-full mt-4"
        @buttonClick="updateAddress"
      >
        Update Address
      </secondary-button>
    </div>
    <div class="verify-text">Verification Step 2/2</div>
  </div>
</template>

<script>
import PrimaryButton from "../Buttons/Primary";
import SecondaryButton from "../Buttons/Secondary";
import JetstreamInput from "../../Jetstream/Input";
export default {
  components: {
    PrimaryButton,
    SecondaryButton,
    JetstreamInput,
  },
  props: ["userId", "address", "addressList"],
  data() {
    return {
      showAddressCreation: false,
      addressEntry: "",
      internalAddressList: this.addressList,
      isClickedAddress: false,
    };
  },
  mounted() {
    // this.$emit('continue');
  },
  methods: {
    setAddress(address) {
      this.isClickedAddress = true;
      this.addressEntry = address;

      this.internalAddressList = null;
    },
    setAddressEntry(address) {
      this.addressEntry = address;
    },
    updateAddress() {
      this.showAddressCreation = false;
      this.$inertia
        .post("/update-address", {
          address: this.addressEntry,
          id: this.userId,
        })
        .then((data) => {
        });
    },
  },
  watch: {
    addressEntry: function (val) {
      if (this.isClickedAddress) {
          this.isClickedAddress = false;
          return;
      }
      axios.post("/find-address", { partialAddress: val }).then((response) => {
        this.internalAddressList = response.data;
      });
    },
  },
  computed: {
    fullAddress() {
      // this.showAddressCreation = true;
      //  return 'You do not have a complete address in our system. Please enter it now.';

      if (!Object.keys(this.address).length) {
        this.showAddressCreation = false;
        return "You do not have a complete address in our system. Please enter it now.";
      } else {
        if (this.address.suburb != null) {
          return `${this.address.address}, ${this.address.suburb}, ${this.address.state} ${this.address.post_code}`;
        } else {
          return `${this.address.address}`;
        }
        //return `${this.address.address}, ${this.address.suburb}, ${this.address.state} ${this.address.post_code}`;
      }
    },
  },
};
</script>
