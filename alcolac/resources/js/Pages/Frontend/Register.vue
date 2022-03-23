<template>
    <frontend-layout >
        <template #header>
        Not an Employee !
        </template>

        <p v-if="systemSuccess"
           class="font-weight-bold text-green-500">
            {{systemSuccess}}
        </p>

        <p v-if="errors.systemFail"
           class="font-weight-bold text-red-500">
            {{errors.systemFail}}
        </p>

        <p v-else
            class="text-center mb-4">
            Enter your mobile number and Birth Date to Register
        </p>
        <form @submit.prevent="submit" class="declaration-form-box" >
            <div class="text-center ">
                <div class="row justify-center mt-4 mb-4">
                    <div class="col-6">
                        <jetstream-input v-model="form.first_name" placeholder="First Name" name="first_name"
                                 :class="{'bg-red-300': validateErrors.form.first_name}+' declaration-form-control'"  />
                                 <p v-if="validateErrors.form.first_name !== null" class="text-red-600 text-sm">{{validateErrors.form.first_name}}</p>
                    </div>
                    <div class="col-6">
                        <jetstream-input v-model="form.last_name" placeholder="Last Name" name="last_name"
                                 :class="{'bg-red-300': validateErrors.form.last_name}+' declaration-form-control'"  />
                                 <p v-if="validateErrors.form.last_name !== null" class="text-red-600 text-sm">{{validateErrors.form.last_name}}</p>
                    </div>
                </div>
                <div class="row justify-center mt-4 mb-4">
                    <div class="col-6">
                        <jetstream-input v-model="form.email" placeholder="Email" name="email"
                                 :class="{'bg-red-300': validateErrors.form.email}+' declaration-form-control'"  />
                                 <p v-if="validateErrors.form.email !== null" class="text-red-600 text-sm">{{validateErrors.form.email}}</p>
                    </div>
                    <div class="col-6">
                        <jetstream-input v-model="form.phone" placeholder="Phone" name="phone"
                                 :class="{'bg-red-300': validateErrors.form.phone}+' declaration-form-control'"  />
                                 <p v-if="validateErrors.form.phone !== null" class="text-red-600 text-sm">{{validateErrors.form.phone}}</p>
                    </div>
                </div>
                <div class="row justify-center mt-4">
                 <div class="col-4">
                    <select name="day"
                            v-model="form.day"
                            class="declaration-form-control"
                            :class="{'bg-red-300': validateErrors.form.day}"
                             >
                        <option disabled>DD</option>
                        <option v-for="(day, index) in days" :key="index"
                                :value="day">
                            {{day}}
                        </option>
                    </select>
                    <p v-if="validateErrors.form.day !== null" class="text-red-600 text-sm">{{validateErrors.form.day}}</p>
                    </div>
                   <div class="col-4">
                    <select name="month"
                            v-model="form.month"
                            class="declaration-form-control"
                            :class="{'bg-red-300': validateErrors.form.month}"
                             >
                        <option disabled>MM</option>
                        <option v-for="(month, index) in months" :key="index"
                                :value="pad(index + 1, 2)">
                            {{month}}
                        </option>
                    </select>

                    <p v-if="validateErrors.form.month !== null" class="text-red-600 text-sm">{{validateErrors.form.month}}</p>
                    </div>
                    <div class="col-4">
                    <select name="year"
                            v-model="form.year"
                            class="declaration-form-control"
                            :class="{'bg-red-300': validateErrors.form.year}"
                             >
                        <option disabled>YYYY</option>
                        <option v-for="(year, index) in years" :key="index"
                                :value="year">
                            {{year}}
                        </option>
                    </select>

                    <p v-if="validateErrors.form.year !== null" class="text-red-600 text-sm">{{validateErrors.form.year}}</p>
                    </div>
                </div>



            </div>
            <div class="button-wrapper text-center mt-8">
                <secondary-button type="submit" >Save</secondary-button>
            </div>
        </form>
    </frontend-layout>
</template>


<script>

    import FrontendLayout from "../../Layouts/FrontendLayout";
    import JetstreamInput from "../../Jetstream/Input";
    import PrimaryButton from "../../Components/Buttons/Primary";
    import SecondaryButton from "../../Components/Buttons/Secondary";
    import JetDropdown from '../../Jetstream/Dropdown';

    export default {
        components: {
            FrontendLayout,
            JetstreamInput,
            PrimaryButton,
            SecondaryButton,
            JetDropdown
        },
        props: [
            'days', 'months', 'years',
            'systemSuccess', 'errors','systemFail'
        ],
        data() {
            return {
                form: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    day: 'DD',
                    month: 'MM',
                    year: 'YYYY'
                },
                validateErrors: {
                    form: {
                        first_name: null, last_name: null, email: null, phone: null, day: null, month: null, year: null
                    }
                }
            }
        },
        mounted() {
        },
        methods: {
            submit()
            {
                if( this.validate() === true)
                    this.$inertia.post('/register', this.form);
            },
            validate()
            {
                let flag = true;

                if( this.form.first_name.trim() === '') {
                    flag = false;
                    this.validateErrors.form.first_name = 'Please Enter First Name';
                } else {
                    this.validateErrors.form.first_name = null;
                }

                if( this.form.last_name.trim() === '') {
                    flag = false;
                    this.validateErrors.form.last_name = 'Please Enter Last Name';
                } else {
                    this.validateErrors.form.last_name = null;
                }
                if( this.form.email.trim() === '') {
                    flag = false;
                    this.validateErrors.form.email = 'Please Enter Email ID';
                } else {
                    this.validateErrors.form.email = null;
                }

                if( this.form.phone.trim() === '' ) {
                    flag = false;
                    this.validateErrors.form.phone = 'Phone number can not be blank';
               /* } else if ( /^(04)\d{8}$/g.test(this.form.phone) === false && /^(\+614)\d{8}$/g.test(this.form.phone) === false) {
                    flag = false;
                    this.validateErrors.form.phone = 'This is not a valid Australian Mobile Number';*/
                } else {
                    this.validateErrors.form.phone = null;
                }

                if( this.form.day.trim() === '' || this.form.day === 'DD' ) {
                    flag = false;
                    this.validateErrors.form.day = 'Please select a day';
                } else {
                    this.validateErrors.form.day = null;
                }

                if( this.form.month.trim() === '' || this.form.month === 'MM' ) {
                    flag = false;
                    this.validateErrors.form.month = 'Please select a month';
                } else {
                    this.validateErrors.form.month = null;
                }

                if( this.form.year === '' || this.form.year === 'YYYY' ) {
                    flag = false;
                    this.validateErrors.form.year = 'Please select a year';
                } else {
                    this.validateErrors.form.year = null;
                }

                return flag;
            }
        }
    }
</script>
<style scoped>

</style>
