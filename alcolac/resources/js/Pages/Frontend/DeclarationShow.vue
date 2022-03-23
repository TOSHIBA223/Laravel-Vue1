<template>
    <frontend-layout>
        <template #header>
            {{data.name}} Entries
        </template>
        <div class="mb-4">
            <span class="font-weight-bold">{{data.complete_count}} of {{data.total_count}} completed</span>
        </div>
        <canvas id="poll-data-chart" width="400" height="400"></canvas>
    </frontend-layout>
</template>

<script>

import FrontendLayout from "../../Layouts/FrontendLayout";
import Chart from "chart.js";

export default {
    components: {
        FrontendLayout
    },
    props: [
        'data',
        'pollId'
    ],
    mounted() {
        this.buildPoll()
    },
    methods: {
        buildPoll()
        {
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
        }
    }
}
</script>
