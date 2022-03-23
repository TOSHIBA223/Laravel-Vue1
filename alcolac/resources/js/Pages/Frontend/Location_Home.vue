<template>
    <frontend-layout >
        <template #header>
        Login with System
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
            Enter your Employee Code and Birth Date to Login
        </p>
        <form @submit.prevent="submit" class="declaration-form-box" >
            <div class="text-center ">
                <jetstream-input v-model="form.employee_code" placeholder="Employee Code" name="employee_code"
                                 :class="{'bg-red-300': validateErrors.form.employee_code}+' declaration-form-control'"  />
                <p v-if="validateErrors.form.employee_code !== null" class="text-red-600 text-sm">{{validateErrors.form.employee_code}}</p>
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
                <secondary-button type="submit" >Check</secondary-button>
            </div>
        </form>
        <p class="text-center text-sm mt-4 notempl">
            Not an employee? <a href="/register">Click</a>
        </p>
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
            'systemSuccess', 'errors','location'
        ],
        data() {
            return {

                form: {
                    employee_code: '',
                    day: 'DD',
                    month: 'MM',
                    year: 'YYYY'
                },

                validateErrors: {
                    form: {
                        employee_code: null, day: null, month: null, year: null
                    }
                }
            }
        },
        mounted() {
        },
        methods: {
            submit()
            { 
                if( this.validate() === true){
                   console.log("@@@@@@@@@@@@@@@",this.form);
                    this.$inertia.post('/qr-code/'+this.location, this.form);
                }
                    
            },
            validate()
            {
                let flag = true;

                if( this.form.employee_code.trim() === '' ) {
                    flag = false;
                    this.validateErrors.form.employee_code = 'employee_code can not be blank';
               /* } else if ( /^(04)\d{8}$/g.test(this.form.phone) === false && /^(\+614)\d{8}$/g.test(this.form.phone) === false) {
                    flag = false;
                    this.validateErrors.form.phone = 'This is not a valid Australian Mobile Number';*/
                } else {
                    this.validateErrors.form.employee_code = null;
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
