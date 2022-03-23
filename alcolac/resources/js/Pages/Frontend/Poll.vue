<template>
    <frontend-layout>
        <template #header>
            {{ data.currentTitle }}
        </template>

        <!-- Declaration Expired -->
        <div v-if="status === 'expired'" class="text-center">
            <p class="font-weight-bold text-red-500">
                This poll has expired.
            </p>
        </div>

        <!-- Completed Declaration -->
        <div v-if="status === 'complete'" class="text-center">
            <p class="font-weight-bold text-red-500">
                It looks like you've already completed this poll
            </p>
        </div>

        <!-- Completed Declaration -->
        <div v-if="status === 'void'" class="text-center">
            <p class="font-weight-bold text-red-500">
                It looks like there is a new poll for you to complete
            </p>
        </div>

        <!-- Completed Declaration -->
        <div v-if="!status" class="text-center">
            <p class="">
                {{data.description}}
            </p>
        </div>

        <poll-data v-if="status === false && !data.showPollData"
                   :poll-questions="data.pollQuestions"
                    @answer="submit"/>

    </frontend-layout>
</template>

<script>

    import FrontendLayout from "../../Layouts/FrontendLayout";
    import PollData from "../../Components/Frontend/PollData";

    export default {
        components: {
            FrontendLayout,
            PollData
        },
        props: [
            'data',
            'status'
        ],
        data() {
            return {
            }
        },
        methods: {
            submit(answer)
            {
                this.$inertia.post('/polls/submit', {token: this.data.token, answer: answer});
            }
        }
    }
</script>
