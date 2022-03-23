<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Declarations
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-xl p-12">
          <div class="w-full mt-4" v-if="!addNewOpen">
            <h3
              class="clearfix mb-3 declaration-form-box"
              style="max-width: 100%"
            >
              <secondary-button
                type="button"
                :class="'float-right'"
                @buttonClick="opensetDeclaration"
                v-if="data.permission.perm_create"
              >
                Set Default Declaration
              </secondary-button>

              <secondary-button
                type="button"
                :class="'float-right'"
                @buttonClick="showNewTemplate"
                v-if="data.permission.perm_create"
              >
                New Declaration
              </secondary-button>
            </h3>
            <div class="table-responsive">
              <table
                class="
                  table table-bordered table-striped
                  declaration-admin-table
                "
                v-if="templates"
              >
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>SMS Template</th>
                    <th>Cron Run Time</th>
                    <th class="px-4 py-2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in templates" :key="item.id">
                    <td>
                      {{ item.name }}
                    </td>
                    <td>
                      {{ item.sms_template ? item.sms_template.name : "-" }}
                    </td>
                    <td>
                      {{
                        item.cron_times &&
                        item.cron_times[item.cron_times.length - 1]
                          ? item.cron_times[item.cron_times.length - 1].settime
                          : ""
                      }}
                    </td>

                    <td class="text-center">
                      <em
                        class="fa fa-edit text-green-400 mr-4 cursor-pointer"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Update Declaration"
                        @click="
                          editItem(
                            item.id,
                            item.sms_template ? item.sms_template.id : 0,
                            item.location_id ? item.location_id : 0,
                            item.name,
                            item.sms_enable,
                            item.pre_sms_template,
                            item.expire_day,
                            item.expire_hour,
                            item.never_expire,
                            item.sms_dob_req,
                            item.sms_address_req,
                            JSON.parse(item.fields),
                            item.dob_validation,
                            item.address_validation,
                            item.csv_upload,
                            item.valid_until,
                            //item.short_valid_until,
                            item.success,
                            item.failure,
                            item.warn,
                            item.success_color,
                            item.success_font,
                            item.failure_color,
                            item.failure_font,
                            item.warn_color,
                            item.warn_font,
                            item.fail_sms_template
                          )
                        "
                        v-if="data.permission.perm_update"
                      ></em>
                      <em
                        class="fa fa-eye text-green-400 mr-4 cursor-pointer"
                        data-toggle="View"
                        data-placement="bottom"
                        title="View Template"
                        @click="
                          viewTemplates(
                            item.id,
                            item.name,
                            item.sms_template ? item.sms_template.name : '-',
                            item.sms_template ? item.sms_template.id : 0,
                            item.sms_enable,
                            item.pre_sms_template,
                            item.expire_day,
                            item.expire_hour,
                            item.never_expire,
                            item.sms_dob_req,
                            item.sms_address_req,
                            JSON.parse(item.fields),
                            item.dob_validation,
                            item.address_validation,
                            item.csv_upload,
                            item.valid_until,
                            // item.short_valid_until,
                            item.success,
                            item.failure,
                            item.warn,
                            item.success_color,
                            item.success_font,
                            item.failure_color,
                            item.failure_font,
                            item.warn_color,
                            item.warn_font,
                            item.fail_sms_template
                          )
                        "
                      ></em>
                      <em
                        class="
                          fa fa-download
                          text-green-400
                          mr-4
                          cursor-pointer
                        "
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Download Template Report"
                        @click="
                          downloadReport(
                            item.id,
                            item.sms_template ? item.sms_template.id : 0
                          )
                        "
                      ></em>
                      <em
                        class="fa fa-crown text-green-400 mr-4 cursor-pointer"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="instantly Run Cron"
                        @click="
                          openDeclarationSend(
                            item.id,
                            item.sms_template ? item.sms_template.id : 0
                          )
                        "
                      ></em>
                      <em
                        class="fas fa-cog text-green-400 mr-4 cursor-pointer"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Add Schedule"
                        @click="openDeclarationSetting(item.id)"
                      ></em>

                      <em
                        class="fa fa-book text-green-400 mr-4 cursor-pointer"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="View Log Declaration"
                        @click="viewlog(item.id)"
                      ></em>

                      <em
                        v-if="item.deleted_at === null"
                        class="
                          fa fa-minus-circle
                          text-red-500
                          mr-4
                          cursor-pointer
                          mr-2
                        "
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Delete Declaration"
                        @click="openDeclarationDelete(item.id)"
                      ></em>

                      <em
                        v-else
                        class="
                          fa fa-plus-circle
                          text-yellow-300
                          mr-4
                          cursor-pointer
                        "
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Enble Declaration"
                        @click="enableUser(item.id)"
                      ></em>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div v-if="addNewOpen">
            <div class="outer">
              <div class="text-center">
                <h4
                  class="text-center mb-3"
                  v-html="
                    newDeclaration && newDeclaration.id
                      ? 'Update Declaration Template'
                      : 'New Declaration Template'
                  "
                ></h4>
              </div>
              <div class="declaration-updation-form">
                <form @submit.prevent="addNew">
                  <ul class="nav nav-tabs nav-justified">
                    <li class="nav-item">
                      <a
                        class="nav-link"
                        @click.prevent="setActive('home')"
                        :class="{ active: isActive('home') }"
                        href="#home"
                        >Basic Details</a
                      >
                    </li>
                    <li class="nav-item">
                      <a
                        class="nav-link"
                        @click.prevent="setActive('smtp')"
                        :class="{ active: isActive('smtp') }"
                        href="#profile"
                        >Pre Verification SMS</a
                      >
                    </li>
                    <li class="nav-item">
                      <a
                        class="nav-link"
                        @click.prevent="setActive('crons')"
                        :class="{ active: isActive('crons') }"
                        href="#contact"
                        >Question Answer section</a
                      >
                    </li>
                  </ul>

                  <div class="tab-content py-3" id="myTabContent">
                    <div
                      class="tab-pane fade"
                      :class="{ 'active show': isActive('home') }"
                      id="home"
                    >
                      <div class="row">
                        <div class="col-sm-6">
                          <label class="mb-2 w-100"
                            >Declaration Template Name
                            <jet-input
                              type="text"
                              id="name"
                              class="declaration-form-control"
                              placeholder="Declaration template name "
                              v-model="newDeclaration.name"
                            ></jet-input
                          ></label>
                          <jet-input-error
                            :message="newDeclarationErrors.name"
                          />
                        </div>

                        <div class="col-sm-6">
                          <label class="mb-2 w-100">
                            Location
                            <vue-select
                              selectName="Location"
                              :options="locationTemplates"
                              :selectedOption="newDeclaration.location_id"
                              placeholderOption="Select Location"
                              class="declaration-form-control"
                              @optionSelected="validLocationTemplate"
                            />
                          </label>
                        </div>

                        <div class="col-sm-6">
                          <label class="mb-2 w-100"
                            >SMS Template
                            <vue-select
                              selectName="smsTemplate"
                              :options="smsTemplates"
                              :selectedOption="newDeclaration.sms_template_id"
                              placeholderOption="Select SMS Template"
                              class="declaration-form-control"
                              @optionSelected="validSMSTemplate"
                            />
                          </label>
                          <jet-input-error
                            :message="newDeclarationErrors.smsTemplates"
                          />
                        </div>

                        <div class="col-sm-6">
                          <div class="custom-select-outer">
                            <label class="mb-2 w-100"
                              >Template Validation
                              <vue-select
                                selectName="valid-until"
                                :options="validSelectOptions"
                                :selectedOption="newDeclaration.valid_until"
                                placeholderOption="How long is this valid for normally?"
                                class="declaration-form-control"
                                @optionSelected="validChange"
                              />
                            </label>
                            <jet-input-error
                              :message="newDeclarationErrors.valid_until"
                            />
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <vue-boolean-check
                            name="dob-validation"
                            id="dob-validation"
                            label="DOB Validation Required?"
                            value="true"
                            :checked="newDeclaration.dob_validation"
                            @changeOption="updateDob"
                          />
                        </div>

                        <div class="col-sm-4">
                          <vue-boolean-check
                            name="csv_upload"
                            id="csv_upload"
                            label="Integrity .CSV Upload"
                            value="true"
                            :checked="newDeclaration.csv_upload"
                            @changeOption="updateCSV"
                          />
                        </div>
                        <div class="col-sm-4">
                          <vue-boolean-check
                            name="address-validation"
                            id="address-validation"
                            label="Address Required?"
                            value="true"
                            :checked="newDeclaration.address_validation"
                            @changeOption="updateAddress"
                          />
                        </div>

                        <div class="col-sm-4">
                          <label class="mb-2 w-100"
                            >Success Message
                            <textarea
                              class="custom-textarea"
                              v-model="newDeclaration.success"
                              placeholder="Success Message"
                              v-bind:style="{
                                color: newDeclaration.successcolor,
                                fontSize: newDeclaration.successfont + 'px',
                              }"
                            ></textarea>
                          </label>
                          <jet-input-error
                            :message="newDeclarationErrors.success"
                          />
                        </div>
                        <div class="col-sm-4">
                          <label class="mb-2 w-100"
                            >Fail Message
                            <textarea
                              class="custom-textarea"
                              v-model="newDeclaration.failure"
                              placeholder="Failure Message"
                              v-bind:style="{
                                color: newDeclaration.failurecolor,
                                fontSize: newDeclaration.failurefont + 'px',
                              }"
                            ></textarea>
                          </label>
                          <jet-input-error
                            :message="newDeclarationErrors.failure"
                          />
                        </div>
                        <div class="col-sm-4">
                          <label class="mb-2 w-100"
                            >Warn Message
                            <textarea
                              class="custom-textarea"
                              v-model="newDeclaration.warn"
                              placeholder="Warn Message"
                              v-bind:style="{
                                color: newDeclaration.warncolor,
                                fontSize: newDeclaration.warnfont + 'px',
                              }"
                            ></textarea>
                          </label>
                          <jet-input-error
                            :message="newDeclarationErrors.warn"
                          />
                        </div>

                        <div class="col-sm-4">
                          <label class="mb-2 w-100">
                            Success Message Color</label
                          >

                          <select
                            v-model="newDeclaration.successcolor"
                            @change="onChangeSuccessMessage($event)"
                            class="
                              w-full
                              outline-none
                              border border-black
                              cursor-pointer
                              p-2
                            "
                            placeholder="Please select"
                          >
                            <option value="">Success Message Color</option>
                            <option value="green">Green</option>
                            <option value="red">Red</option>
                            <option value="yellow">Yellow</option>
                            <option value="pink">pink</option>
                          </select>
                        </div>

                        <div class="col-sm-4">
                          <label class="mb-2 w-100">
                            Failure Message Color</label
                          >

                          <select
                            v-model="newDeclaration.failurecolor"
                            @change="onChangeFailureMessage($event)"
                            class="
                              w-full
                              outline-none
                              border border-black
                              cursor-pointer
                              p-2
                            "
                            placeholder="Please select"
                          >
                            <option value="">Failure Message Color</option>
                            <option value="green">Green</option>
                            <option value="red">Red</option>
                            <option value="yellow">Yellow</option>
                            <option value="pink">pink</option>
                          </select>
                        </div>

                        <div class="col-sm-4">
                          <label class="mb-2 w-100"> Warn Message Color</label>

                          <select
                            v-model="newDeclaration.warncolor"
                            @change="onChangeWarnMessage($event)"
                            class="
                              w-full
                              outline-none
                              border border-black
                              cursor-pointer
                              p-2
                            "
                            placeholder="Please select"
                          >
                            <option value="">Warn Message Color</option>
                            <option value="green">Green</option>
                            <option value="red">Red</option>
                            <option value="yellow">Yellow</option>
                            <option value="pink">pink</option>
                          </select>
                        </div>

                        <div class="col-sm-4">
                          <label class="mb-2 w-100"
                            >Success Message Font(in px)
                            <jet-input
                              type="text"
                              id="successfont"
                              class="declaration-form-control"
                              placeholder="Success font like 10, 20 etc"
                              v-model="newDeclaration.successfont"
                            ></jet-input
                          ></label>
                        </div>

                        <div class="col-sm-4">
                          <label class="mb-2 w-100"
                            >Failure Message Font(in px)
                            <jet-input
                              type="text"
                              id="failurefont"
                              class="declaration-form-control"
                              placeholder="Failure font like 10, 20 etc"
                              v-model="newDeclaration.failurefont"
                            ></jet-input
                          ></label>
                        </div>

                        <div class="col-sm-4">
                          <label class="mb-2 w-100"
                            >Warn Message Font(in px)
                            <jet-input
                              type="text"
                              id="warnfont"
                              class="declaration-form-control"
                              placeholder="Warn font like 10, 20 etc"
                              v-model="newDeclaration.warnfont"
                            ></jet-input
                          ></label>
                        </div>

                        <div class="col-sm-6">
                          <label class="mb-2 w-100"
                            >Failure SMS Template
                            <vue-select
                              selectName="smsTemplate"
                              :options="smsTemplates"
                              :selectedOption="
                                newDeclaration.fail_sms_template_id
                              "
                              placeholderOption="Select Failure SMS Template"
                              class="declaration-form-control"
                              @optionSelected="FailvalidSMSTemplate"
                            />
                          </label>
                          <jet-input-error
                            :message="newDeclarationErrors.smsTemplates"
                          />
                        </div>
                      </div>
                    </div>

                    <div
                      class="tab-pane fade"
                      :class="{ 'active show': isActive('smtp') }"
                      id="smtp"
                    >
                      <div class="row">
                        <div class="col-sm-4">
                          <label class="mb-10 w-100">
                            Please select
                            <select
                              v-model="newDeclaration.smscrondayselect"
                              @change="onChange($event)"
                              class="
                                w-full
                                outline-none
                                border border-black
                                cursor-pointer
                                p-2
                              "
                            >
                              <option value="Disable">Disable</option>
                              <option value="Enable">Enable</option>
                            </select>
                          </label>
                        </div>
                      </div>
                      <div class="row">
                        <div
                          class="col-sm-4"
                          v-if="newDeclaration.smscrondayselect == 'Enable'"
                        >
                          <label class="mb-2 w-100"
                            >Pre Verification SMS Template
                            <vue-select
                              selectName="presmsTemplate"
                              :options="smsTemplates"
                              :selectedOption="
                                newDeclaration.pre_sms_template_id
                              "
                              placeholderOption="Select Pre SMS Template"
                              class="declaration-form-control"
                              @optionSelected="PrevalidSMSTemplate"
                            />
                          </label>
                        </div>
                        <div
                          class="col-sm-4 prenew"
                          v-if="newDeclaration.smscrondayselect == 'Enable'"
                        >
                          <label class="mb-2 w-100"> Expires </label>

                          <select
                            v-model="newDeclaration.expirehours"
                            @change="onChangeExpireHours($event)"
                            class="
                              w-full
                              outline-none
                              border border-black
                              cursor-pointer
                              p-2
                            "
                          >
                            <option value="0">Never</option>
                            <option value="15" selected>15 Min</option>
                            <option value="30">30 Min</option>
                            <option value="60">1 Hour</option>
                            <option value="120">2 Hours</option>
                            <option value="360">6 Hours</option>
                            <option value="720">12 Hours</option>
                            <option value="1440">1 Day</option>
                            <option value="2880">2 Days</option>
                            <option value="10080">7 Days</option>
                          </select>
                        </div>
                      </div>
                      <div class="row">
                        <div
                          class="col-sm-4 pre5"
                          v-if="newDeclaration.smscrondayselect == 'Enable'"
                        >
                          <vue-boolean-check
                            name="sms-dob-req"
                            id="sms-dob-req"
                            label="DOB Validation Required?"
                            value="true"
                            :checked="newDeclaration.sms_dob_req"
                            @changeOption="updatesmsdob"
                          />
                        </div>

                        <div
                          class="col-sm-4 pre6"
                          v-if="newDeclaration.smscrondayselect == 'Enable'"
                        >
                          <vue-boolean-check
                            name="sms-address-req"
                            id="sms-address-req"
                            label="Address Validation Required?"
                            value="true"
                            :checked="newDeclaration.sms_address_req"
                            @changeOption="updatesmsAddress"
                          />
                        </div>
                      </div>
                    </div>

                    <div
                      class="tab-pane fade"
                      :class="{ 'active show': isActive('crons') }"
                      id="crons"
                    >
                      <div class="row">
                        <div class="col-sm-12">
                          <h4 class="d-flex align-items-center">
                            <span class="flex-grow-1 declaration-popup-hdr"
                              >Questions</span
                            >
                            <button
                              class="btn btn-link"
                              type="button"
                              @click="addOption"
                            >
                              + Add Question
                            </button>
                          </h4>

                          <div ref="option-wrapper ">
                            <span v-if="newDeclaration.fields.length === 0"
                              >Add a Question</span
                            >

                            <!--Question Modal-->
                            <div
                              v-for="(option, index) in newDeclaration.fields"
                              :key="index"
                              class="declaration-form-questionary"
                            >
                              <div class="row">
                                <div class="col-sm-12 text-right">
                                  <em
                                    class="fa fa-times"
                                    @click="deleteOption(index)"
                                  ></em>
                                </div>
                                <div class="col-sm-12">
                                  <label class="mb-2 w-100"
                                    >Question Title
                                    <jet-input
                                      type="text"
                                      class="declaration-form-control"
                                      :id="`label-${index}`"
                                      v-model="
                                        newDeclaration.fields[index].label
                                      "
                                      placeholder="Question Title (i.e. Please select True/False from following question)"
                                    />
                                  </label>
                                </div>
                                <div class="col-sm-12">
                                  <label class="mb-2 w-100"
                                    >Question Defination
                                    <textarea
                                      class="custom-textarea"
                                      v-model="
                                        newDeclaration.fields[index].definition
                                      "
                                      placeholder="Question Defination"
                                    ></textarea>
                                    <jet-input-error
                                      :id="'queDef' + index"
                                      class="queDefs"
                                      style="display: none"
                                      :message="'Please insert Question Defination'"
                                    />
                                  </label>
                                </div>

                                <div class="col-sm-4">
                                  <label class="mb-2 w-100"
                                    >Right Answer
                                    <jet-input
                                      type="text"
                                      class="declaration-form-control"
                                      v-model="
                                        newDeclaration.fields[index].success
                                      "
                                    ></jet-input>
                                  </label>
                                  <jet-input-error
                                    :id="'queSuccess' + index"
                                    class="quesSuccesses"
                                    style="display: none"
                                    :message="'Please insert Right Answer'"
                                  />
                                </div>
                                <div class="col-sm-4">
                                  <label class="mb-2 w-100"
                                    >Wrong Answer
                                    <jet-input
                                      type="text"
                                      class="declaration-form-control"
                                      v-model="
                                        newDeclaration.fields[index].failure
                                      "
                                    ></jet-input>
                                  </label>
                                  <jet-input-error
                                    :id="'queFail' + index"
                                    class="queFails"
                                    style="display: none"
                                    :message="'Please insert Wrong Answer'"
                                  />
                                </div>
                                <div class="col-sm-4">
                                  <label class="mb-2 w-100"
                                    >Warn Answer
                                    <jet-input
                                      type="text"
                                      class="declaration-form-control"
                                      v-model="
                                        newDeclaration.fields[index].warn
                                      "
                                    ></jet-input>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <!--End Question modal-->
                            <!--Question Table-->

                            <!--End Question Table-->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="declaration-form-box">
                    <secondary-button
                      cla
                      type="button"
                      @buttonClick="addNewOpen = false"
                      class="mt-4 update-declaration-btn"
                    >
                      Cancel
                    </secondary-button>
                    <secondary-button
                      cla
                      type="button"
                      @buttonClick="addNew"
                      class="mt-4 update-declaration-btn"
                      v-html="
                        newDeclaration && newDeclaration.id ? 'Save' : 'Create'
                      "
                    >
                    </secondary-button>
                  </div>
                </form>
              </div>
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
      scrollBehaviour="overflow-y-auto"
      class="forpopup"
    >
      <div class="close-btn">
        <a href="#"
          ><em class="fas fa-times" @click.prevent="closepopup"></em
        ></a>
      </div>
      <div class="outer">
        <div class="text-center">
          <h4 class="text-center mb-3" v-html="viewDeclaration.name"></h4>
        </div>
        <div class="text-left">
          <label class="custom-dec-form-value">
            SMS Template:
            <span style="font-weight: 700">{{
              viewDeclaration.sms_template_name
            }}</span>
          </label>
        </div>
        <!--form attributes-->
        <div class="declaration-updation-form row">
          <!--validation-->
          <div class="col-sm-6">
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }} normally valid upto:
              <span class="custom-dec-field-value">{{
                viewDeclaration.valid_until == 1
                  ? viewDeclaration.valid_until + " Day"
                  : viewDeclaration.valid_until + " Days"
              }}</span>
            </label>
          </div>
          <!--<div class="col-sm-6">
            <label class="custom-dec-form-value">
            {{ viewDeclaration.name }} self generated valid upto:
            <span class="custom-dec-field-value">{{
              viewDeclaration.short_valid_until == 1
                ? viewDeclaration.short_valid_until + " Minute"
                : viewDeclaration.short_valid_until + " Minutes"
            }}</span>
            </label>

          </div>-->
          <div class="col-sm-6">
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }}'s DOB Validation:
              <span class="custom-dec-field-value">{{
                viewDeclaration.dob_validation
              }}</span>
            </label>
          </div>
          <div class="col-sm-6">
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }}'s Address Validation:
              <span class="custom-dec-field-value">{{
                viewDeclaration.address_validation
              }}</span>
            </label>
          </div>

          <div class="col-sm-6">
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }}'s CSV Upload:
              <span class="custom-dec-field-value">{{
                viewDeclaration.csv_upload
              }}</span>
            </label>
          </div>

          <div
            class="col-sm-6"
            v-html="
              '<label class=\'custom-dec-form-value\'>' +
              viewDeclaration.name +
              '\'s success message:<span class=\'custom-dec-field-value\'> ' +
              viewDeclaration.success +
              '</span></label>'
            "
          ></div>

          <div
            class="col-sm-6"
            v-html="
              '<label class=\'custom-dec-form-value\'>' +
              viewDeclaration.name +
              '\'s fail message:<span class=\'custom-dec-field-value\'> ' +
              viewDeclaration.failure +
              '</span></label>'
            "
          ></div>

          <!--end validation-->
        </div>
        <!--Questions-->

        <h4 v-if="viewDeclaration.sms_enable">Pre Verifiation SMS</h4>
        <div class="row" v-if="viewDeclaration.sms_enable">
          <div class="col-sm-6" v-if="viewDeclaration.sms_enable">
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }}'s enable/Disable:
              <span class="custom-dec-field-value"
                >{{ viewDeclaration.sms_enable }}
              </span>
            </label>
          </div>

          <div class="col-sm-6" v-if="viewDeclaration.expire_day">
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }}'s Expie Days/Hours:
              <span class="custom-dec-field-value"
                >{{ viewDeclaration.expire_day }}
              </span>
            </label>
          </div>

          <div class="col-sm-6" v-if="viewDeclaration.never_expire == 1">
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }}'s Never Expire:
              <span class="custom-dec-field-value">{{ "true" }} </span>
            </label>
          </div>

          <div class="col-sm-6" v-else>
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }}'s Never Expire:
              <span class="custom-dec-field-value">{{ "false" }} </span>
            </label>
          </div>

          <div class="col-sm-6" v-if="viewDeclaration.sms_dob_req == 1">
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }}'s Pre SMS DOB Validation:
              <span class="custom-dec-field-value">{{ "true" }}</span>
            </label>
          </div>

          <div class="col-sm-6" v-else>
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }}'s Pre SMS DOB Validation:
              <span class="custom-dec-field-value">{{ "false" }}</span>
            </label>
          </div>

          <div class="col-sm-6" v-if="viewDeclaration.sms_address_req == 1">
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }}'s Pre SMS Address Validation:
              <span class="custom-dec-field-value">{{ "true" }}</span>
            </label>
          </div>

          <div class="col-sm-6" v-else>
            <label class="custom-dec-form-value">
              {{ viewDeclaration.name }}'s Pre SMS Address Validation:
              <span class="custom-dec-field-value">{{ "false" }}</span>
            </label>
          </div>
        </div>

        <div class="w-full">
          <div class="" v-if="viewDeclaration.fields.length == 0">
            No question available yet.
          </div>
          <template v-else class="">
            <h5 class="que-top-header">Questions</h5>
            <template v-for="(option, index) in viewDeclaration.fields">
              <div class="survey-que-box" :key="index">
                <h3 class="que-title" v-if="option.label">
                  {{ option.label }}
                </h3>
                <h3
                  :class="
                    option.label
                      ? 'que-defination'
                      : 'que-defination que-defination-single'
                  "
                >
                  {{ option.definition }}
                </h3>

                <div
                  class="
                    button-wrapper
                    mt-3
                    grid grid-cols-2
                    gap-x-4
                    declaration-form-box
                    mw-100
                  "
                >
                  <secondary-button>
                    {{ option.success }}
                  </secondary-button>

                  <danger-button>
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

    <modal
      v-if="showDownloadReport"
      @closeModal="showDownloadReport = false"
      containerMaxWidth="max-w-3xl"
      scrollBehaviour="overflow-y-auto"
      class="forpopup"
    >
      <div class="close-btn">
        <a href="#"
          ><em class="fas fa-times" @click.prevent="closepopup"></em
        ></a>
      </div>
      <div class="text-center">
        <h4 class="text-center mb-3" v-html="'Download Report'"></h4>
      </div>
      <form action="/admin/api/declarations/csv" method="POST" class="row">
        <input type="hidden" name="id" :value="downloadId" />
        <input type="hidden" name="_token" :value="data.csrf" />
        <input
          type="hidden"
          name="sms_template_id"
          :value="currentSmsTemplateid"
        />
        <div class="col-sm-4">
          <label class="mb-0"> Start Date </label>
          <jet-input
            type="date"
            class="w-full declaration-form-control"
            v-model="startDate"
            name="startDate"
          />
        </div>
        <div class="col-sm-4">
          <label class="mb-0"> End Date</label>
          <jet-input
            type="date"
            class="w-full declaration-form-control"
            v-model="endDate"
            name="endDate"
          />
        </div>
        <div class="col-sm-4 declaration-form-box generate-button">
          <secondary-button type="submit"> Generate CSV </secondary-button>
        </div>
      </form>
    </modal>
    <!--Dialog Options-->
    <jet-dialog-modal
      :show="dialogOptions"
      closeable="true"
      maxWidth="sm"
      @close="dialogOptions = false"
    >
      <template v-slot:title>
        <h3 class="text-center">Warning message</h3>
      </template>
      <template v-slot:content>
        <div class="text-center">
          Please Add atleast one question in Declaration Template
        </div>
      </template>
    </jet-dialog-modal>
    <!--End Dialog Options -->
    <modal
      v-if="showDeclarationSend"
      @closeModal="showDeclarationSend = false"
      containerMaxWidth="max-w-3xl"
      class="forpopup"
    >
      <div class="close-btn">
        <a href="#"
          ><em class="fas fa-times" @click.prevent="closepopup"></em
        ></a>
      </div>
      <div class="text-center">
        <h4 class="text-center mb-3" v-html="'Run Cron Instantly'"></h4>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <strong
            class="mb-0 text-center"
            style="display: block; font-size: 20px"
          >
            Are you sure you want to run cron Instantly.
          </strong>
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

    <!---Template setting-->
    <modal
      v-if="showDeclarationCrone"
      @closeModal="showDeclarationCrone = false"
      containerMaxWidth="max-w-4xl"
      class="showcronmodel forpopup"
    >
      <div class="close-btn">
        <a href="#"
          ><em class="fas fa-times" @click.prevent="closepopup"></em
        ></a>
      </div>
      <div class="text-center">
        <h4 class="text-center mb-3" v-html="'Save Schedule'"></h4>
      </div>
      <div class="row">
        <div class="col-sm-2">
          <label class="mb-0 col-sm-12"> Set time </label>
          <!--<vue-select
            @optionSelected="cronTime"
            :options="cronTime"
            placeholderOption="Select Crone Time"
           class="w-full declaration-form-control"
          />-->

          <Span class="col-sm-12">
            <input type="time" v-model="selectTime" />
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
          <label class="mb-0"> Cron Enable/Disbale </label>

          <select
            v-model="crondayselect"
            class="w-full outline-none border border-black cursor-pointer p-2"
            placeholder="Please select"
          >
            <option value="" selected>Please select</option>
            <option value="Disable">Disable</option>
            <option value="Enable">Enable</option>
          </select>
        </div>

        <div class="col-sm-4">
          <label class="mb-0"> Select Day </label>
          <multiselect
            v-model="multislect"
            :options="daysOptions"
            @optionSelected="validselectvalday"
            :multiple="true"
            :close-on-select="false"
            :clear-on-select="false"
            :preserve-search="true"
            placeholder="Pick some"
            label="name"
            track-by="name"
            :preselect-first="false"
          ></multiselect>
        </div>

        <div class="col-sm-2 declaration-form-box generate-button setcron">
          <secondary-button type="button" @buttonClick="startCroneItem">
            Save
          </secondary-button>
        </div>
      </div>
    </modal>

    <!---Template setting-->
    <modal
      v-if="setDeclarationDefault"
      @closeModal="setDeclarationDefault = false"
      containerMaxWidth="max-w-4xl"
      class="showcronmodel forpopup"
    >
      <div class="close-btn">
        <a href="#"
          ><em class="fas fa-times" @click.prevent="closepopup"></em
        ></a>
      </div>
      <div class="text-center">
        <h4 class="text-center mb-3" v-html="'Set Default Declaration'"></h4>
      </div>
      <div class="row">
        <div class="col-sm-3">
          <label class="mb-2 w-100"
            >Location
            <jet-input
              type="text"
              class="declaration-form-control"
              :value="colac"
              v-model="selectlocation"
              placeholder="colac"
              readonly="true"
            />
          </label>
        </div>

        <div class="col-sm-4">
          <label class="mb-0"> Select Declaration </label>
          <vue-select
            selectName="declaration"
            :options="declarations"
            :selectedOption="selectedDeclaration"
            placeholderOption="Select a Declaration"
            class="w-full"
            @optionSelected="declarationChanged"
          />
        </div>
      </div>

      <div class="row">
        <div class="col-sm-3">
          <label class="mb-2 w-100"
            >Location
            <jet-input
              type="text"
              class="declaration-form-control"
              :value="sunshine"
              v-model="selectlocation1"
              placeholder="sunshine"
            />
          </label>
        </div>

        <div class="col-sm-4">
          <label class="mb-0"> Select Declaration </label>
          <vue-select
            selectName="declaration"
            :options="declarations"
            :selectedOption="selectedDeclaration1"
            placeholderOption="Select a Declaration"
            class="w-full"
            @optionSelected="declarationChanged1"
          />
        </div>
      </div>
      <br />
      <br />
      <br />
      <div class="row">
        <div class="col-sm-2 declaration-form-box generate-button setcron">
          <secondary-button type="button" @buttonClick="startsetdeclaration">
            Save
          </secondary-button>
        </div>
      </div>
    </modal>

    <modal
      v-if="showDeclarationDelete"
      @closeModal="showDeclarationDelete = false"
      containerMaxWidth="max-w-3xl"
      class="forpopup"
    >
      <div class="close-btn">
        <a href="#"
          ><em class="fas fa-times" @click.prevent="closepopup"></em
        ></a>
      </div>
      <div class="text-center">
        <h4 class="text-center mb-3" v-html="'Delete Declaration'"></h4>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <strong
            class="mb-0 text-center"
            style="display: block; font-size: 20px"
          >
            Are you sure you want to delete?
          </strong>
          </div>
          </div>
      <div class="row d-flex justify-center mt-8">
        <secondary-button type="button" @buttonClick="deleteUser">
          Yes
        </secondary-button>

        <secondary-button type="button" @buttonClick="closepopup">
          No
        </secondary-button>
      </div>
    </modal>

    <!--end template setting-->

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
import Label from "../../../Jetstream/Label.vue";
import Multiselect from "vue-multiselect";
import Index from "../../API/Index.vue";

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

    // ColourPicker,
  },

  props: ["data", "errors", "systemSuccess", "contentTags"],
  data() {
    return {
      activeItem: "home",
      // colour: '#000000',
      templates: this.data.templates,
      defaultDeclaration: this.data.defaultDeclaration,
      selectedDeclaration: "",
      selectedDeclaration1: "",
      selectTime: "",
      multislect: [],

      crondayselect: "Enable",
      smscrondayselect: "Enable",
      selectlocation: "colac",
      selectlocation1: "sunshine",

      addNewOpen: false,
      viewTemplate: false,
      dialogOptions: false,
      newDeclaration: {
        id: 0,
        name: "",
        fields: [],
        dob_validation: false,
        address_validation: false,
        csv_upload: false,
        valid_until: false,
        sms_template_id: false,
        pre_sms_template_id: false,
        smscrondayselect: "Enable",
        expireday: "",
        expirehours: "15",
        never_expire: false,
        sms_dob_req: false,
        sms_address_req: false,
        location_id: false,
        //short_valid_until: false,
        success: "",
        failure: "",
        warn: "",
        successcolor: "",
        successfont: "",
        failurecolor: "",
        failurefont: "",
        warncolor: "",
        warnfont: "",
        fail_sms_template_id: false,
      },
      viewDeclaration: {
        id: 0,
        name: "",
        fields: [],
        sms_template_id: "",
        pre_sms_template_id: "",
        smscrondayselect: "Enable",
        expireday: "",
        expirehours: "",
        never_expire: false,
        sms_dob_req: false,
        sms_address_req: false,
        location_id: "",
        dob_validation: false,
        address_validation: false,
        csv_upload: false,
        valid_until: false,
        //short_valid_until: false,
        success: "",
        failure: "",
        warn: "",
        successcolor: "",
        successfont: "",
        failurecolor: "",
        failurefont: "",
        warncolor: "",
        warnfont: "",
        fail_sms_template_id: "",
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
      cronTime: [
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
      deleteDeclarationId: 0,
      currentSmsTemplateid: 0,
      declarationSendData: {
        location: "all",
        group: "all",
        cronenbale: "",
        days: "",
      },
      showDeclarationSend: false,
      showDeclarationDelete: false,
      showDeclarationCrone: false,
      setDeclarationDefault: false,
      locationOptions: [{ id: "all", name: "All" }],
      groupOptions: [{ id: "all", name: "All" }],
      EnableOptions: ["Enable", "Disbale"],
      daysOptions: [
        { id: "Monday", name: "Monday" },
        { id: "Tuesday", name: "Tuesday" },
        { id: "Wednesday", name: "Wednesday" },
        { id: "Thursday", name: "Thursday" },
        { id: "Friday", name: "Friday" },
        { id: "Saturday", name: "Saturday" },
        { id: "Sunday", name: "Sunday" },
      ],
      smsTemplates: [],
      locationTemplates: [],
    };
  },

  mounted() {
    //console.log(this.data);
    this.setLocationOptions(this.data.locations);
    this.setLocationOptionsnew(this.data.locations);
    this.setGroupOptions(this.data.groups);
    this.setSMSTemplates(this.data.smsTemplates);
    this.setDeclarations(this.data.templates);
  },
  methods: {
    isActive(menuItem) {
      return this.activeItem === menuItem;
    },
    setActive(menuItem) {
      this.activeItem = menuItem;
    },

    setDeclarations(declarations) {
      this.declarations = [];
      declarations.forEach((declaration) => {
        this.declarations.push({
          id: declaration.id,
          name: declaration.name,
        });
      });
    },
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
      let ids = 1;
      locations.forEach((location) => {
        instance.locationTemplates.push({
          name: location.location,
          id: ids,
        });
        ids++;
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
    setSMSTemplates(smsTemplates) {
      const instance = this;
      smsTemplates.forEach((smsTemplate) => {
        instance.smsTemplates.push({
          name: smsTemplate.name,
          id: smsTemplate.id,
        });
      });
    },
    declarationChanged(declaration) {
      this.selectedDeclaration = declaration;
    },
    declarationChanged1(declaration) {
      this.selectedDeclaration1 = declaration;
    },
    onChangeExpireHours(event) {
      this.newDeclaration.expirehours = event.target.value;
    },
    onChangeSuccessMessage(event) {
      this.newDeclaration.successcolor = event.target.value;
    },
    onChangeFailureMessage(event) {
      this.newDeclaration.failurecolor = event.target.value;
    },
    onChangeWarnMessage(event) {
      this.newDeclaration.warncolor = event.target.value;
    },
    onChange(event) {
      this.newDeclaration.smscrondayselect = event.target.value;

      if (event.target.value == "Enable") {
        $(".pre1").show();
        $(".pre2").show();
        $(".pre3").show();
        $(".pre4").show();
        $(".pre5").show();
        $(".pre6").show();
        $(".pre8").show();
        $(".prenew").show();
      } else {
        $(".pre1").hide();
        $(".pre2").hide();
        $(".pre3").hide();
        $(".pre4").hide();
        $(".pre5").hide();
        $(".pre6").hide();
        $(".pre8").hide();
        $(".prenew").hide();
      }
    },
    sendItem() {
      if (this.sendDeclarationId !== 0)
        this.$inertia
          .post("/admin/api/declarations/send", {
            declarationId: this.sendDeclarationId,
            data: this.declarationSendData,
            send_template_id: this.currentSmsTemplateid,
          })
          .then((data) => {
            setTimeout(function () {
              location.reload();
            }, 1000);
          });
    },
    closepopup() {
      location.reload();
    },
    startCroneItem() {
      if (this.sendDeclarationId != 0) {
        this.$inertia
          .post("/admin/api/declarations/setcron", {
            declarationId: this.sendDeclarationId,
            data: this.declarationSendData,
            crondayselect: this.crondayselect,
            multislect: this.multislect,
            time: this.selectTime,
          })
          .then((data) => {
            setTimeout(function () {
              location.reload();
            }, 1000);
          });
      }
    },
    startsetdeclaration() {
      this.$inertia
        .post("/admin/api/declarations/setdeclarationdefault", {
          selectlocation: this.selectlocation,
          selectlocation1: this.selectlocation1,
          selectedDeclaration: this.selectedDeclaration,
          selectedDeclaration1: this.selectedDeclaration1,
        })
        .then((data) => {
          setTimeout(function () {
            location.reload();
          }, 1000);
        });
    },
    validChange(value) {
      this.newDeclaration.valid_until = value;
    },
    validSMSTemplate(value) {
      this.newDeclaration.sms_template_id = value;
    },
    PrevalidSMSTemplate(value) {
      this.newDeclaration.pre_sms_template_id = value;
    },
    FailvalidSMSTemplate(value) {
      this.newDeclaration.fail_sms_template_id = value;
    },
    validLocationTemplate(value) {
      this.newDeclaration.location_id = value;
    },
    validselectval(value) {
      this.declarationSendData.cronenbale = value;
    },
    validselectvalday(value) {
      this.declarationSendData.days = value;
    },
    openDeclarationSend(id, smsTemplateId) {
      this.showDeclarationSend = true;
      this.sendDeclarationId = id;
      this.currentSmsTemplateid = smsTemplateId;
    },
    openDeclarationDelete(id) {
      this.showDeclarationDelete = true;
      this.deleteDeclarationId = id;
    },
    openDeclarationSetting(id) {
      this.showDeclarationCrone = true;
      this.sendDeclarationId = id;
      let templates = this.templates.filter((x) => x.id == id);

      templates.forEach(async (item, index) => {
        this.multislect = [];
        this.selectTime = "";
        this.crondayselect = "";
        await item.cron_times.forEach(async (day) => {
          await this.multislect.push({
            id: day.cronday,
            name: day.cronday,
          });
        });
        this.selectTime = item.cron_times[item.cron_times.length - 1].settime;
        this.crondayselect =
          item.cron_times[item.cron_times.length - 1].cronenbale;
      });

      //multislect
    },

    opensetDeclaration() {
      this.setDeclarationDefault = true;

      this.selectedDeclaration = this.defaultDeclaration[0].declaration_id;

      this.selectedDeclaration1 =
        this.defaultDeclaration[
          this.defaultDeclaration.length - 1
        ].declaration_id;
    },

    downloadReport(id, smsTemplateId) {
      this.downloadId = id;
      this.currentSmsTemplateid = smsTemplateId;
      this.showDownloadReport = true;
    },
    /*validShortChange(value) {
      this.newDeclaration.short_valid_until = value;
    },*/
    showNotification(result) {
      if (result.systemSuccess) {
        this.$toast.success(result.systemSuccess, {
          timeout: 3000,
        });
      } else if (result.systemFailed) {
        this.$toast.success(result.systemFailed, {
          timeout: 3000,
        });
      }
    },
    addNew() {
      // if (this.validate() === true) {

      if (this.newDeclaration.id === 0) {
        axios
          .post("/admin/api/declarations/create", this.newDeclaration)
          .then((result) => {
            this.templates = result.data.data.templates;
            this.showNotification(result.data);
          });
      } else {
        axios
          .put("/admin/api/declarations/update", this.newDeclaration)
          .then((result) => {
            this.templates = result.data.data.templates;
            this.showNotification(result.data);
          });
      }

      this.resetNewTemplateBuilder();
      this.addNewOpen = false;
      // }
    },
    enableUser(id) {
      let enableObj = {
        id: id,
        enable: true,
      };
      axios
        .put("/admin/api/declarations/update", enableObj)
        .then((result) => {
          this.templates = result.data.data.templates;
          this.showNotification(result.data);
        });
    },
    deleteUser() {
      if (this.deleteDeclarationId != 0) {
        axios
          .delete("/admin/api/declarations/delete/" + this.deleteDeclarationId)
          .then((result) => {
            this.templates = result.data.data.templates;
            this.showNotification(result.data);
          });
      }
      this.showDeclarationDelete = false;
    },
    viewlog(id) {
      window.location.href = "/admin/declarations/declog/" + id;
    },
    addOption() {
      let fields = this.newDeclaration.fields;

      this.newDeclaration.fields.push({
        name: "",
        definition: "",
        success: "Yes",
        failure: "No",
        warn: "No",
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
      if (
        this.newDeclaration.smsTemplates == false ||
        this.newDeclaration.smsTemplates == ""
      ) {
        flag = false;
        this.newDeclarationErrors.smsTemplates =
          "Please select a SMS Template for Declaration";
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

      /* if (
        this.newDeclaration.short_valid_until === false ||
        this.newDeclaration.short_valid_until === ""
      ) {
        flag = false;
        this.newDeclarationErrors.short_valid_until =
          "Please select a valid self generated option from the options"; //'Self Generated Valid Until can not be blank';
      } else {
        this.newDeclarationErrors.short_valid_until = false;
      }*/

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
        csv_upload: false,
        valid_until: "",
        //short_valid_until: "",
        sms_template_id: "",
        pre_sms_template_id: "",
        expireday: "",
        expirehours: "15",
        never_expire: false,
        sms_dob_req: false,
        sms_address_req: false,
        location_id: "",
        success: "",
        failure: "",
        warn: "",
        successcolor: "",
        successfont: "",
        failurecolor: "",
        failurefont: "",
        warncolor: "",
        warnfont: "",
        fail_sms_template_id: "",
      };
      this.addOption();
    },
    /****View Template*/
    viewTemplates(
      id,
      name,
      smsTemplateName,
      smsTemplateId,
      smscrondayselect,
      presmsTemplateId,
      expire_day,
      expire_hour,
      never_expire,
      sms_dob_req,
      sms_address_req,
      fields,
      dob_validation,
      address_validation,
      csv_upload,
      valid_until,
      //short_valid_until,
      success,
      failure,
      warn,
      success_color,
      success_font,
      failure_color,
      failure_font,
      warn_color,
      warn_font,
      failsmsTemplateId
    ) {
      this.viewDeclaration = {
        id: id,
        name: name,
        fields: fields,
        sms_template_id: smsTemplateId,
        sms_enable: smscrondayselect,
        pre_sms_template_id: presmsTemplateId,
        expire_day: expire_day,
        expire_hour: expire_hour,
        never_expire: never_expire,
        sms_dob_req: sms_dob_req === 1,
        sms_address_req: sms_address_req === 1,
        location_id: "",
        sms_template_name: smsTemplateName,
        dob_validation: dob_validation === 1,
        address_validation: address_validation === 1,
        csv_upload: csv_upload === 1,
        valid_until: valid_until,
        // short_valid_until: short_valid_until,
        success: success,
        failure: failure,
        warn: warn,
        success_color: success_color,
        success_font: success_font,
        failure_color: failure_color,
        failure_font: failure_font,
        warn_color: warn_color,
        warn_font: warn_font,
        fail_sms_template_id: failsmsTemplateId,
      };
      this.viewTemplate = true;
    },
    hideErrorMessage() {
      this.newDeclarationErrors.smsTemplates = "";
      this.newDeclarationErrors.name = "";
      this.newDeclarationErrors.valid_until = "";
      // this.newDeclarationErrors.short_valid_until = "";
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
      smsTemplateId,
      locationid,
      name,
      smscrondayselect,
      presmsTemplateId,
      expire_day,
      expire_hour,
      never_expire,
      sms_dob_req,
      sms_address_req,
      fields,
      dob_validation,
      address_validation,
      csv_upload,
      valid_until,
      // short_valid_until,
      success,
      failure,
      warn,
      success_color,
      success_font,
      failure_color,
      failure_font,
      warn_color,
      warn_font,
      failsmsTemplateId
    ) {
      this.newDeclaration = {
        id: id,
        name: name,
        sms_template_id: smsTemplateId,
        smscrondayselect: smscrondayselect,
        pre_sms_template_id: presmsTemplateId,
        expireday: expire_day,
        expirehours: expire_hour,
        never_expire: never_expire,
        sms_dob_req: sms_dob_req === 1,
        sms_address_req: sms_address_req === 1,
        location_id: locationid,
        fields: fields,
        dob_validation: dob_validation === 1,
        address_validation: address_validation === 1,
        csv_upload: csv_upload === 1,
        valid_until: valid_until,
        // short_valid_until: short_valid_until,
        success: success,
        failure: failure,
        warn: warn,
        successcolor: success_color,
        successfont: success_font,
        failurecolor: failure_color,
        failurefont: failure_font,
        warncolor: warn_color,
        warnfont: warn_font,
        fail_sms_template_id: failsmsTemplateId,
      };
      this.hideErrorMessage();
      //this.addNewOpen = true;
      this.activeItem = "home";

      if (smscrondayselect == "Enable") {
        $(".pre1").show();
        $(".pre2").show();
        $(".pre3").show();
        $(".pre4").show();
        $(".pre5").show();
        $(".pre6").show();
        $(".pre8").show();
        $(".prenew").show();
      } else {
        $(".pre1").hide();
        $(".pre2").hide();
        $(".pre3").hide();
        $(".pre4").hide();
        $(".pre5").hide();
        $(".pre6").hide();
        $(".pre8").hide();
        $(".prenew").hide();
      }
    },
    showNewTemplate() {
      this.hideErrorMessage();

      this.resetNewTemplateBuilder();
      this.activeItem = "home";
    },

    updateDob(value) {
      this.newDeclaration.dob_validation = value;
    },

    updatenexverExpire(value) {
      this.newDeclaration.never_expire = value;
    },
    updatesmsdob(value) {
      this.newDeclaration.sms_dob_req = value;
    },

    updatesmsAddress(value) {
      this.newDeclaration.sms_address_req = value;
    },

    updateAddress(value) {
      this.newDeclaration.address_validation = value;
    },
    updateCSV(value) {
      this.newDeclaration.csv_upload = value;
    },
  },
};
</script>
