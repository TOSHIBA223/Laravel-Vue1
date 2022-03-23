<template>
    <app-layout>

        <transition enter-active-class="ease-out duration-300"
                    enter-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-class="opacity-100"
                    leave-to-class="opacity-0">
            <div class="bg-red-500 p-4 fixed left-0 right-0 top-0 text-center" v-if="errors.systemFail" @click="errors = false">
                <div class="max-w-xl w-full mx-auto">
                    <span class="text-white">
                        {{errors.systemFail}} x
                    </span>
                </div>
            </div>
            <div class="bg-green-400 p-4 fixed left-0 right-0 top-0 text-center" v-if="systemSuccess" @click="systemSuccess = false">
                <div class="max-w-xl w-full mx-auto">
                    <span class="text-white">
                        {{systemSuccess}} x
                    </span>
                </div>
            </div>
        </transition>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Polls
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl p-12">
                    <div class="w-full mt-4">
                        <h3 class="clearfix mb-3">
                            <secondary-button type="button" :class="'float-right'"
                                            @buttonClick="showNewTemplate" v-if="data.permission.perm_create">
                                New Poll
                            </secondary-button>
                        </h3>
                        <table class="table-auto w-full border border-gray-500" v-if="templates">
                            <thead>
                            <tr>
                                <th class="px-4 py-2 border-r border-gray-500">Name</th>
                                <th class="px-4 py-2 border-r border-gray-500">Valid To</th>
                                <th class="px-4 py-2 border-r border-gray-500">Answers</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item, index) in templates" :key="item.id"
                                :class="[{'bg-gray-100': index % 2 !== 0, '': item.deleted_at !== null}]">
                                <td class="border border-gray-500 px-4 py-2 text-center">{{item.name}}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center">{{item.valid_to}}</td>
                                <td class="border border-gray-500 px-4 py-2 text-center">
                                    <a href="#" @click.prevent="showPollData(item.id)" class="text-blue-600">View Answers</a></td>
                                <td class="border border-gray-500 px-4 py-2 text-center">
                                    <i class="fa fa-edit text-green-400 mr-4 cursor-pointer"
                                       @click="editItem(item.id, item.name, JSON.parse(item.fields), item.valid_to, item.description)" v-if="data.permission.perm_update"></i>

                                    <i class="fa fa-share-square text-green-400 mr-4 cursor-pointer"
                                       @click="openSendItem(item.id)"></i>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <modal v-if="addNewOpen" @closeModal="addNewOpen = false" containerMaxWidth="max-w-3xl" scrollBehaviour="overflow-y-auto" class="forpopup">
            <div class=close-btn><a href="#"><i class="fas fa-times" @click.prevent="closepopup"></i></a></div>
            <div class="outer">
                <form @submit.prevent="addNew" class="grid grid-cols-2 gap-x-2 gap-y-4">
                    <div class="col-span-2">
                        <jet-input type="text" id="name" placeholder="Name" v-model="newPoll.name" class="w-full"></jet-input>
                        <jet-input-error :message="newPollErrors.name" />
                        <textarea class="w-full mt-2 border border-black" rows="5"
                                  v-model="newPoll.description"
                                  placeholder="Question Definition"></textarea>
                        <jet-input type="datetime-local"
                                   class="w-full mt-2"
                                   v-model="newPoll.valid_to"
                                   placeholder="Date and Time" />
                    </div>

                    <div class="mt-4 col-span-2">
                        <h4 class="clearfix">Options <button type="button" @click="addOption" class="float-right text-base">+ Add Option</button></h4>
                        <div ref="option-wrapper">
                            <span v-if="newPoll.fields.length === 0">Add an Option</span>
                            <div class="border border-gray-300 position-relative w-full rounded p-4 mb-2"
                                 v-for="(option, index) in newPoll.fields" :key="index">
                                <i class="fa fa-times position-absolute top-2 right-2" @click="deleteOption(index)"></i>
                                <jet-input type="text"
                                           :id="`label-${index}`"
                                           class="w-full mt-2"
                                           v-model="newPoll.fields[index].answer"
                                           placeholder="Answer" />
                            </div>
                        </div>
                    </div>
                </form>

                <secondary-button type="button" @buttonClick="addNew" class="mt-4">
                    Create Poll
                </secondary-button>
            </div>
        </modal>

        <modal v-if="showContentOpen" @closeModal="showContentOpen = false" class="forpopup">
            <div class=close-btn><a href="#"><i class="fas fa-times" @click.prevent="closepopup" ></i></a></div>
            <div class="mb-4">
                <span class="font-weight-bold">{{data.complete_count}} of {{data.total_count}} completed</span>
            </div>
            <canvas id="poll-data-chart" width="400" height="400"></canvas>
        </modal>

        <modal v-if="showPollSend" @closeModal="showPollSend = false" class="forpopup">
            <div class=close-btn><a href="#"><i class="fas fa-times" @click.prevent="closepopup" ></i></a></div>
            <div class="grid grid-cols-3 gap-x-3 align-items-center">

                <label>
                    Locations
                    <ams-select @optionSelected="pollSendData.location"
                                :options="locationOptions"
                                class="w-full"/>
                </label>

                <label>
                    Groups
                <ams-select @optionSelected="pollSendData.group"
                            :options="groupOptions"
                            class="w-full"/>
                </label>

                <secondary-button type="button" @buttonClick="sendItem">
                    Send Poll
                </secondary-button>
            </div>
        </modal>

    </app-layout>
</template>

<script>

import AppLayout from "Layout/AppLayout";
import PrimaryButton from "Component/Buttons/Primary";
import SecondaryButton from "Component/Buttons/Secondary";
import Modal from "Component/Modal";
import JetInput from "Jetstream/Input";
import JetInputError from "Jetstream/InputError";
import AmsSelect from "Component/Inputs/Select";
import Chart from 'chart.js';

export default {
    components: {
        AppLayout,
        PrimaryButton,
        SecondaryButton,
        Modal,
        JetInput,
        JetInputError,
        AmsSelect
    },
    props: [
        'data',
        'errors',
        'systemSuccess',
        'contentTags'
    ],
    data() {
        return {
            templates: this.data.templates,
            addNewOpen: false,
            showContentOpen: false,
            locationOptions: [{id: 'all', name: 'All'}],
            groupOptions: [{id: 'all', name: 'All'}],
            newPoll: {
                id: 0,
                name: '',
                fields: [],
                valid_to: '',
                description: ''
            },
            newPollErrors: {
                name: false,
            },
            pollAnswers: null,
            sendPollId: 0,
            pollSendData: {
                location: 'all',
                group: 'all'
            },
            showPollSend: false
        }
    },
    mounted() {
        this.setLocationOptions(this.data.locations);
        this.setGroupOptions(this.data.groups);
    },
    methods: {
         closepopup(){
      location.reload();
    },
        setLocationOptions(locations)
        {
            const instance = this;
            locations.forEach( location => {
                instance.locationOptions.push({
                    name: location.name,
                    id: location.id
                });
            });
        },
        setGroupOptions(groups)
        {
            const instance = this;
            groups.forEach( group => {
                instance.groupOptions.push(
                    {
                        id: group.groups,
                        name: group.groups,
                    });
            });
        },
        addNew()
        {
            if(this.validate() === true) {

                if (this.newPoll.id === 0)
                    this.$inertia.post('/admin/api/polls/create', this.newPoll).then( () => {this.templates = this.data.templates}) ;
                else
                    this.$inertia.put('/admin/api/polls/update', this.newPoll).then( () => {this.templates = this.data.templates});

                this.resetNewTemplateBuilder();
                this.addNewOpen = false
            }
        },
        openSendItem(id)
        {
            this.showPollSend = true;
            this.sendPollId = id;
        },
        sendItem()
        {
            if(this.sendPollId !== 0) {
                this.$inertia.post('/admin/api/polls/send', {id: this.sendPollId, data: this.pollSendData});
            }
        },
        addOption()
        {
            let fields = this.newPoll.fields;

            this.newPoll.fields.push({
                answer: '',
            });
        },
        deleteOption(index)
        {
            let fields = this.newPoll.fields;

            fields.splice(index, 1);
        },
        validate()
        {
            let flag = true;

          //  console.log(this.newPoll.valid_until);

            if( this.newPoll.name.trim() === '' ) {
                flag = false;
                this.newPollErrors.name = 'Name can not be blank';
            } else {
                this.newPollErrors.name = null;
            }

            return flag;
        },
        resetNewTemplateBuilder()
        {
            this.newPoll = {
                id: 0,
                name: '',
                fields: [],
                valid_to: '',
                description: ''
            }
        },
        editItem(id, name, fields, valid_to, description)
        {

            console.log(valid_to.replace(' ', 'T'));
            this.newPoll = {
                id: id,
                name: name,
                fields: fields,
                valid_to: valid_to.replace(' ', 'T'),
                description: description
            };
            this.addNewOpen = true;
        },
        showNewTemplate()
        {
            this.addNewOpen = true;
            this.resetNewTemplateBuilder();
        },
        showPollData(id)
        {
            this.showContentOpen = true;
            this.pollAnswers = null;
            this.buildPoll(id);
        },
        updateDob(value)
        {
            this.newPoll.dob_validation = value;
        },
        updateAddress(value)
        {
            this.newPoll.address_validation = value;
        },
        buildPoll(id)
        {
            console.log('build the poll');
            this.$inertia.post('/admin/api/polls/data', {id: id}).then( () => {
                console.log(this.data);

                const poll_answers = this.data.poll_answers,
                    user_answers = this.data.user_answers;

                let bar_labels = [];
                poll_answers.forEach((answer) => {
                    bar_labels.push(answer.answer);
                });

                let data_points = [];
                Object.keys(user_answers).forEach(key => {
                    data_points.push(user_answers[key]);
                });

                let ctx = document.getElementById('poll-data-chart'),
                    chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: bar_labels,
                            datasets: [{
                                label: '# of Votes',
                                data: data_points,
                                backgroundColor: [
                                    'crimson',
                                    'green',
                                    'blue',
                                    'orange',
                                    'purple'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                                legend: {
                                display: false
                            },
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        precision:0,
                                    }
                                }]
                            },
                        },
                    });
            })
        }
    }
}
</script>
