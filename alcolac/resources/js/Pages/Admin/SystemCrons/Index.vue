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
        System Crons
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-xl p-12">
          <div class="w-full mt-4">
            <h3 class="clearfix mb-3 declaration-form-box" style="max-width:100%;">

              <!--<secondary-button
                type="button"
                :class="'float-right'"
                @buttonClick="showNewTemplate"
                v-if="data.permission.perm_create"
              >
                New Declaration
              </secondary-button>-->

            </h3>
            <div class="table-responsive">
              <table
                class="table table-bordered table-striped declaration-admin-table"
                v-if="templates"
              >
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Description</th>

                    <th>Cron Run Time</th>
                    <th class="px-4 py-2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item) in templates" :key="item.id">
                    <td>{{ item.name }}</td>
                    <td>{{ item.description }}</td>

                     <td>{{item.cron_times && item.cron_times[item.cron_times.length-1] ? item.cron_times[item.cron_times.length-1].settime:'' }}</td>



                    <td class="text-center">
                      <i
                        class="fa fa-edit text-green-400 mr-4 cursor-pointer"
                        data-toggle="tooltip" data-placement="bottom" title="Update Declaration"
                        @click="
                          editItem(
                            item.id,
                            item.name,
                            item.description,
                            item.email_to,
                            item.success,
                            item.failure
                          )
                        "
                        v-if="data.permission.perm_update"
                      ></i>


                      <i
                        class="fa fa-crown text-green-400 mr-4 cursor-pointer"
                         data-toggle="tooltip" data-placement="bottom" title="instantly Run Cron"
                        @click="openDeclarationSend(item.id,item.sms_template? item.sms_template.id:0)"
                      ></i>
                       <i
                        class="fas fa-cog text-green-400 mr-4 cursor-pointer d-none"
                         data-toggle="tooltip" data-placement="bottom" title="Add Schedule"
                        @click="openDeclarationSetting(item.id)"
                      ></i>


                     <!-- <i v-if="item.deleted_at === null" class="fa fa-minus-circle text-red-500 mr-4 cursor-pointer mr-2" data-toggle="tooltip" data-placement="bottom" title="Delete Declaration"
                          @click="deleteUser(item.id)"></i>

                      <i v-else class="fa fa-plus-circle text-yellow-300 cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enble Declaration"
                                       @click="enableUser(item.id)"></i>-->
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--view template-->
    <modal
      v-if="viewTemplate"
      @closeModal="viewTemplate = false"
      containerMaxWidth="max-w-3xl"
      scrollBehaviour="overflow-y-auto" class="forpopup"
    >
    <div class=close-btn><a href="#"><i class="fas fa-times" @click.prevent="closepopup"></i></a></div>
      <div class="outer">
        <div class="text-center">
          <h4 class="text-center mb-3" v-html="viewDeclaration.name"></h4>
        </div>
        <div class="text-left">
            <label class="custom-dec-form-value">
            SMS Template:
            <span style="font-weight:700;">{{ viewDeclaration.sms_template_name}}</span>
            </label>
        </div>
        <!--form attributes-->
        <div class="declaration-updation-form row">
          <!--validation-->
          <div class="col-sm-6">
           <label class="custom-dec-form-value"> {{ viewDeclaration.name }} normally valid upto:
            <span class="custom-dec-field-value">{{
              viewDeclaration.valid_until == 1
                ? viewDeclaration.valid_until + " Day"
                : viewDeclaration.valid_until + " Days"
            }}</span>
            </label>
          </div>
          <div class="col-sm-6">
            <label class="custom-dec-form-value">
            {{ viewDeclaration.name }} self generated valid upto:
            <span class="custom-dec-field-value">{{
              viewDeclaration.short_valid_until == 1
                ? viewDeclaration.short_valid_until + " Minute"
                : viewDeclaration.short_valid_until + " Minutes"
            }}</span>
            </label>

          </div>
          <div class="col-sm-6">
            <label class="custom-dec-form-value">
            {{ viewDeclaration.name }}'s DOB Validation:
            <span class="custom-dec-field-value">{{ viewDeclaration.dob_validation }}</span>
            </label>
          </div>
          <div class="col-sm-6">
            <label class="custom-dec-form-value">
            {{ viewDeclaration.name }}'s Address Validation:
            <span class="custom-dec-field-value">{{ viewDeclaration.address_validation }}</span>
            </label>
          </div>
          <div class="col-sm-6"
            v-html="
              '<label class=\'custom-dec-form-value\'>'+viewDeclaration.name +
              '\'s success message:<span class=\'custom-dec-field-value\'> ' +
              viewDeclaration.success +
              '</span></label>'
            "
          ></div>

          <div
          class="col-sm-6"
            v-html="
              '<label class=\'custom-dec-form-value\'>'+viewDeclaration.name +
              '\'s fail message:<span class=\'custom-dec-field-value\'> ' +
              viewDeclaration.failure +
              '</span></label>'
            "
          ></div>

          <!--end validation-->
        </div>
        <!--Questions-->

        <div class="w-full ">
          <div class="" v-if="viewDeclaration.fields.length == 0">
            No question available yet.
          </div>
          <template v-else class="">
            <h5 class="que-top-header">Questions</h5>
            <template v-for="(option, index) in viewDeclaration.fields">
              <div class="survey-que-box" :key="index">
                <h3 class="que-title" v-if="option.label">{{
                  option.label
                }}</h3>
                <h3 :class="option.label ? 'que-defination': 'que-defination que-defination-single'">{{ option.definition }}</h3>

                <div
                  class="button-wrapper mt-3 grid grid-cols-2 gap-x-4 declaration-form-box mw-100"
                >
                  <secondary-button >
                    {{ option.success }}
                  </secondary-button>

                  <danger-button >
                    {{ option.failure }}
                  </danger-button>
                </div>
              </div>
            </template>
          </template>
          <!--
-->
        </div>
        <!--End Questions-->
        <!--end form attributes-->
      </div>
    </modal>
    <!--end view template-->




    <!--End Dialog Options -->
    <modal
      v-if="showDeclarationSend"
      @closeModal="showDeclarationSend = false"
      containerMaxWidth="max-w-3xl" class="forpopup"
    >
    <div class=close-btn><a  href="#" ><i class="fas fa-times" @click.prevent="closepopup"></i></a></div>
    <div class="text-center">
          <h4 class="text-center mb-3" v-html="'Run Cron Instantly'"></h4>
        </div>
      <div class="row">
        <div class="col-sm-12">
        <strong class="mb-0 text-center" style="display:block;font-size:20px;">
          Are you sure you want to run cron Instantly.   </strong>
          <!--<vue-select
            @optionSelected="declarationSendData.location"
            :options="locationOptions"
           class="w-full declaration-form-control"
          />-->
        </div>
 <!--<div class="col-sm-4">
        <label class="mb-0">
          Groups </label>
          <vue-select
            @optionSelected="declarationSendData.group"
            :options="groupOptions"
            class="w-full declaration-form-control"
          />

 </div>-->
 </div>
 <div class="row d-flex justify-center mt-8">
        <secondary-button type="button" @buttonClick="sendItem">
          Yes
        </secondary-button>

        <secondary-button type="button" @buttonClick="closepopup">
          No
        </secondary-button>

      </div>
    </modal>



    <modal
      v-if="addNewOpen"
      @closeModal="addNewOpen = false"
      containerMaxWidth="max-w-3xl"
      scrollBehaviour="overflow-y-auto" class="forpopup"
    >
    <div class=close-btn><a href="#"><i class="fas fa-times" @click.prevent="closepopup"></i></a></div>
      <div class="outer">
        <div class="text-center">
          <h4
            class="text-center mb-3"
            v-html="
              newDeclaration && newDeclaration.id
                ? 'Update System Crons'
                : 'New Declaration Template'
            "
          ></h4>
        </div>
        <div class="declaration-updation-form">
          <form @submit.prevent="addNew">
            <div class="row">




              <div class="col-sm-12">
                <label class="mb-2 w-100"
                  >Name
                  <jet-input
                    type="text"
                    id="name"
                    class="declaration-form-control"
                    placeholder="Declaration template name "
                    v-model="newDeclaration.name"
                  ></jet-input
                ></label>
                <jet-input-error :message="newDeclarationErrors.name" />
              </div>


               <div class="col-sm-12">
                <label class="mb-2 w-100"
                  >Description
                  <jet-input
                    type="text"
                    id="description"
                    class="declaration-form-control"
                    placeholder="Declaration description name "
                    v-model="newDeclaration.description"
                  ></jet-input
                ></label>
                <jet-input-error :message="newDeclarationErrors.name" />
              </div>
              
              <div class="col-sm-12">
                <label class="mb-2 w-100"
                  >Email Send To
                  <jet-input
                    type="email"
                    id="email"
                    class="declaration-form-control"
                    placeholder="Email Send To"
                    v-model="newDeclaration.email_to"
                  ></jet-input
                ></label>
                <jet-input-error :message="newDeclarationErrors.name" />
              </div>


            </div>


          </form>
        </div>
        <div class="declaration-form-box">
          <secondary-button
            cla
            type="button"
            @buttonClick="addNew"
            class="mt-4"
            v-html="
              newDeclaration && newDeclaration.id
                ? 'Update Declaration'
                : 'Create Declaration'
            "
          >
          </secondary-button>
        </div>
      </div>
    </modal>

  <!---Template setting-->
   <modal
      v-if="showDeclarationCrone"
      @closeModal="showDeclarationCrone = false"
      containerMaxWidth="max-w-4xl" class="showcronmodel forpopup"
    >
    <div class=close-btn><a href="#"><i class="fas fa-times" @click.prevent="closepopup" ></i></a></div>
    <div class="text-center">
          <h4 class="text-center mb-3" v-html="'Save Schedule'"></h4>
        </div>
      <div class="row">
        <div class="col-sm-2">
        <label class="mb-0 col-sm-12">
          Set time   </label>
          <!--<vue-select
            @optionSelected="cronTime"
            :options="cronTime"
            placeholderOption="Select Crone Time"
           class="w-full declaration-form-control"
          />-->

          <Span class="col-sm-12">
          <input type="time" v-model="selectTime"/>
          </Span>
        </div>
    <!--<div class="col-sm-2">
            <label class="mb-0">
            Location </label>
            <vue-select
                @optionSelected="declarationSendData.location"
                :options="locationOptions"
                class="w-full declaration-form-control"
            />

    </div>-->

 <div class="col-sm-3">
        <label class="mb-0">
          Cron Enable/Disbale </label>



          <select v-model="crondayselect" class="w-full outline-none border border-black cursor-pointer p-2" placeholder="Please select">
            <option value="" selected>Please select</option>
            <option value="Enable">Enable</option>
            <option value="Disable"> Disable</option>

        </select>

 </div>

<div class="col-sm-4">
        <label class="mb-0">
         Select Day </label>
<multiselect v-model="multislect"
    :options="daysOptions"
    @optionSelected="validselectvalday"
    :multiple="true"
    :close-on-select="false"
    :clear-on-select="false"
    :preserve-search="true"
    placeholder="Pick some"
    label="name"
    track-by="name"
    :preselect-first="false"></multiselect>
</div>




  <div class="col-sm-2 declaration-form-box generate-button setcron">
        <secondary-button type="button" @buttonClick="startCroneItem">
          Save
        </secondary-button>
  </div>
      </div>
    </modal>
  <!--end template setting-->
  </app-layout>
</template>


<script>
import AppLayout from "Layout/AppLayout";
import VueSelect from "Component/Inputs/Select";
import VueBooleanCheck from "Component/Inputs/BooleanCheck";
import PrimaryButton from "Component/Buttons/Primary";
import SecondaryButton from "Component/Buttons/Secondary";
import Modal from "Component/Modal";
import JetInput from "Jetstream/Input";
import JetInputError from "Jetstream/InputError";
import { FormBuilder } from "v-form-builder";
import DangerButton from "Component/Buttons/Danger";
import JetLabel from "Jetstream/Input";
import JetDialogModal from "Jetstream/DialogModal";
import Label from '../../../Jetstream/Label.vue';

import Multiselect from 'vue-multiselect';
import Index from '../../API/Index.vue';
// CSS
export default {
  components: {
    AppLayout,
    VueSelect,
    PrimaryButton,
    SecondaryButton,
    DangerButton,
    Modal,
    JetInput,
    JetDialogModal,
    JetLabel,
    JetInputError,

    FormBuilder,
    VueBooleanCheck,
    Label,
    Index,
    Multiselect,

  },
  props: ["data", "errors", "systemSuccess", "contentTags"],
  data() {
    return {
      templates: this.data.templates,
      selectTime:'',
      multislect: [],
      crondayselect:'Enable',
      addNewOpen: false,
      viewTemplate: false,
      dialogOptions: false,
      newDeclaration: {
        id: 0,
        name: "",
        description:"",
        email_to:"",
        fields: [],

        success: "",
        failure: "",
      },
      viewDeclaration: {
        id: 0,
        name: "",
        description:"",
        fields: [],

        success: "",
        failure: "",
      },
      newDeclarationErrors: {
        name: false,
        content: false,
      },
      validSelectOptions: [
        {
          id: "1",
          name: "1 Day",
        },
        {
          id: "2",
          name: "2 Days",
        },
        {
          id: "3",
          name: "3 Days",
        },
        {
          id: "4",
          name: "4 Days",
        },
        {
          id: "5",
          name: "5 Days",
        },
      ],
      cronTime:[
        {
          id: "5",
          name: "5 Minutes",
        },
        {
          id: "10",
          name: "10 Minutes",
        },
        {
          id: "15",
          name: "15 Minutes",
        },
        {
          id: "20",
          name: "20 Minutes",
        },
        {
          id: "25",
          name: "25 Minutes",
        },
        {
          id: "30",
          name: "30 Minutes",
        },
      ],
      validShortSelectOptions: [
        {
          id: "5",
          name: "5 Minutes",
        },
        {
          id: "10",
          name: "10 Minutes",
        },
        {
          id: "15",
          name: "15 Minutes",
        },
        {
          id: "20",
          name: "20 Minutes",
        },
        {
          id: "25",
          name: "25 Minutes",
        },
        {
          id: "30",
          name: "30 Minutes",
        },
      ],
      showDownloadReport: false,
      downloadId: 0,

      startDate: false,
      endDate: false,
      sendDeclarationId: 0,
      currentSmsTemplateid:0,
      declarationSendData: {
        location: "all",
        group: "all",
        cronenbale:"",
        days:""
      },
      showDeclarationSend: false,
      showDeclarationCrone:false,
      locationOptions: [{ id: "all", name: "All" }],
      groupOptions: [{ id: "all", name: "All" }],
      EnableOptions: ["Enable","Disbale"],
      daysOptions:[{ id: "Monday", name: "Monday"},{id: "Tuesday", name: "Tuesday"},{id: "Wednesday", name: "Wednesday"}
      ,{id: "Thursday", name: "Thursday"},{id: "Friday", name: "Friday"},{id: "Saturday", name: "Saturday"},{id: "Sunday", name: "Sunday"}],
      smsTemplates:[],
      locationTemplates:[]
    };
  },

  mounted() {
    //console.log(this.data);
    this.setLocationOptions(this.data.locations);
    this.setLocationOptionsnew(this.data.locations);
    this.setGroupOptions(this.data.groups);
    this.setSMSTemplates(this.data.smsTemplates);

  },
  methods: {
    setLocationOptions(locations) {
      const instance = this;
      locations.forEach((location) => {
        instance.locationOptions.push({
          name: location.name,
          id: location.id,
        });
      });
    },
    setLocationOptionsnew(locations) {
      const instance = this;
      locations.forEach((location) => {
        instance.locationTemplates.push({
          name: location.location,
          id: location.id,
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
    setSMSTemplates(smsTemplates){
      const instance =this;
      smsTemplates.forEach(smsTemplate =>{
        instance.smsTemplates.push({
          name:smsTemplate.name,
          id: smsTemplate.id
        });
      });
    },
    sendItem() {
      if (this.sendDeclarationId !== 0)
        this.$inertia.post("/admin/api/system-crons/send", {
          declarationId: this.sendDeclarationId,
          data: this.declarationSendData,
          send_template_id: this.currentSmsTemplateid
        }).then(data=>{
          setTimeout(function(){ location.reload() }, 1000);
        });;
    },
    closepopup(){
      location.reload();
    },
    startCroneItem(){
      if(this.sendDeclarationId!=0)
      {
      this.$inertia.post("/admin/api/system-crons/setcron",{
          declarationId: this.sendDeclarationId,
          data: this.declarationSendData,
          crondayselect:this.crondayselect,
          multislect:this.multislect,
          time: this.selectTime,
          }).then(data=>{
          setTimeout(function(){ location.reload() }, 1000);
        });
      }
    },
    validChange(value) {
      this.newDeclaration.valid_until = value;
    },
    validSMSTemplate(value){
      this.newDeclaration.sms_template_id = value;
    },
    validLocationTemplate(value){
      this.newDeclaration.location_id = value;
    },
    validselectval(value){
      this.declarationSendData.cronenbale = value;


    },
    validselectvalday(value){
      this.declarationSendData.days = value;

    },
    openDeclarationSend(id,smsTemplateId) {
      this.showDeclarationSend = true;
      this.sendDeclarationId = id;
      this.currentSmsTemplateid =smsTemplateId;
    },
    openDeclarationSetting(id){
      this.showDeclarationCrone = true;
      this.sendDeclarationId = id;
      let templates = this.templates.filter(x=>x.id==id);

      templates.forEach(async (item,index)=>{
          this.multislect=[];
          this.selectTime='';
          this.crondayselect='';
          await item.cron_times.forEach(async(day)=>{
              await this.multislect.push({
                 "id": day.cronday,
                 "name":day.cronday
              });


          });
          this.selectTime =item.cron_times[item.cron_times.length-1].settime;
          this.crondayselect = item.cron_times[item.cron_times.length-1].cronenbale;



      });


      //multislect

    },
    downloadReport(id,smsTemplateId) {
      this.downloadId = id;
      this.currentSmsTemplateid =smsTemplateId;
      this.showDownloadReport = true;
    },
    validShortChange(value) {
      this.newDeclaration.short_valid_until = value;
    },
    addNew() {

        if (this.newDeclaration.id === 0)
          this.$inertia
            .post("/admin/api/system-crons/create", this.newDeclaration)
            .then(() => {
              this.templates = this.data.templates;
            });
        else
          this.$inertia
            .put("/admin/api/system-crons/update", this.newDeclaration)
            .then(() => {
              this.templates = this.data.templates;
            });

        this.resetNewTemplateBuilder();
        this.addNewOpen = false;

    },
    enableUser(id)
        {
            let enableObj = {
                id: id,
                enable: true
            }
            this.$inertia.put('/admin/api/declarations/update', enableObj).then( () => {
                     this.templates = this.data.templates;
            });


        },
        deleteUser(id)
        {
            this.$inertia.delete('/admin/api/declarations/delete/' + id).then( () => {
                this.templates = this.data.templates;
            }) ;


        },
    addOption() {
      let fields = this.newDeclaration.fields;

      this.newDeclaration.fields.push({
        name: "",
        definition: "",
        success: "Yes",
        failure: "No",
        label: "",
      });
    },
    deleteOption(index) {
      let fields = this.newDeclaration.fields;
      fields.splice(index, 1);
    },
    validate() {
      let flag = true;
        /***SMS Template */
        if(this.newDeclaration.smsTemplates==false||this.newDeclaration.smsTemplates=="")
        {
          flag=false;
          this.newDeclarationErrors.smsTemplates="Please select a SMS Template for Declaration";
        }
        /***END SMS Template */
      if (this.newDeclaration.name.trim() === "") {
        flag = false;
        this.newDeclarationErrors.name = "Please fill name field";
      } else {
        this.newDeclarationErrors.name = null;
      }

      if (
        this.newDeclaration.valid_until === false ||
        this.newDeclaration.valid_until === ""
      ) {
        flag = false;
        this.newDeclarationErrors.valid_until =
          "Please select a valid template duration from the options";
      } else {
        this.newDeclarationErrors.valid_until = false;
      }

      if (
        this.newDeclaration.short_valid_until === false ||
        this.newDeclaration.short_valid_until === ""
      ) {
        flag = false;
        this.newDeclarationErrors.short_valid_until =
          "Please select a valid self generated option from the options"; //'Self Generated Valid Until can not be blank';
      } else {
        this.newDeclarationErrors.short_valid_until = false;
      }


      /****Successs Template*/

      if (this.newDeclaration.success === "") {
        flag = false;
        this.newDeclarationErrors.success = "Please insert message for success"; //'Self Generated Valid Until can not be blank';
      } else {
        this.newDeclarationErrors.success = false;
      }
      /****Fail Template*/
      if (this.newDeclaration.failure === "") {
        flag = false;
        this.newDeclarationErrors.failure = "Please insert message for fail"; //'Self Generated Valid Until can not be blank';
      } else {
        this.newDeclarationErrors.failure = false;
      }
      /****Check Questions */
      if (this.newDeclaration.fields.length == 0) {
        flag = false;
        this.dialogOptions = true;
      }

      for (let index = 0; index < this.newDeclaration.fields.length; index++) {
        if (this.newDeclaration.fields[index].definition.trim() == "") {
          flag = false;
          $("#queDef" + index).show();
        } else {
          $("#queDef" + index).hide();
        }
       // console.log(this.newDeclaration.fields[index]);
        if (this.newDeclaration.fields[index].name.trim() == "") {
          flag = false;
          $("#queName" + index).show();
        } else {
          $("#queName" + index).hide();
        }

        if (this.newDeclaration.fields[index].success.trim() == "") {
          flag = false;
          $("#queSuccess" + index).show();
        } else {
          $("#queSuccess" + index).hide();
        }
        if (this.newDeclaration.fields[index].failure.trim() == "") {
          flag = false;
          $("#queFail" + index).show();
        } else {
          $("#queFail" + index).hide();
        }
      }
      /****End Test questions */

      return flag;
    },
    resetNewTemplateBuilder() {
      this.newDeclaration = {
        id: 0,
        name: "",
        fields: [],
        dob_validation: false,
        address_validation: false,
        valid_until: "",
        short_valid_until: "",
        sms_template_id:"",
        location_id:"",
        success: "",
        failure: "",
      };
      this.addOption();
    },
    /****View Template*/
    viewTemplates(
      id,
      name,
      smsTemplateName,
      smsTemplateId,
      fields,
      dob_validation,
      address_validation,
      valid_until,
      short_valid_until,
      success,
      failure
    ) {
      this.viewDeclaration = {
        id: id,
        name: name,
        fields: fields,
        sms_template_id:smsTemplateId,
        location_id:"",
        sms_template_name:smsTemplateName,
        dob_validation: dob_validation === 1,
        address_validation: address_validation === 1,
        valid_until: valid_until,
        short_valid_until: short_valid_until,
        success: success,
        failure: failure,
      };
      this.viewTemplate = true;
    },
    hideErrorMessage() {
      this.newDeclarationErrors.smsTemplates="";
      this.newDeclarationErrors.name = "";
      this.newDeclarationErrors.valid_until = "";
      this.newDeclarationErrors.short_valid_until = "";
      this.newDeclarationErrors.success = "";
      this.newDeclarationErrors.failure = "";
      $(".qname").hide();
      $(".queDefs").hide();
      $(".quesSuccesses").hide();
      $(".queFails").hide();
      this.addNewOpen = true;
    },
    /****End View Template */
    editItem(
      id,
      name,
      description,
      email_to,
      success,
      failure
    ) {

      this.newDeclaration = {
        id: id,
        name: name,
        description: description,
        email_to: email_to,

        success: success,
        failure: failure,
      };
      this.hideErrorMessage();
      //this.addNewOpen = true;
    },
    showNewTemplate() {
      this.hideErrorMessage();

      this.resetNewTemplateBuilder();
    },

    updateDob(value) {
      this.newDeclaration.dob_validation = value;
    },
    updateAddress(value) {
      this.newDeclaration.address_validation = value;
    },
  },
};
</script>
