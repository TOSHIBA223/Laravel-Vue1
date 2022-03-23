<template>
    <div>
        <div class="grid grid-cols-3 gap-x-4">
            <div v-for="(question, index) in pollQuestions"
                 :key="index">
                <secondary-button v-if="index % 2 === 0"
                                class="w-full"
                                @buttonClick="$emit('answer', question.answer)">
                    {{ question.answer }}
                </secondary-button>
                <secondary-button v-else
                                class="w-full"
                              @buttonClick="$emit('answer', question.answer)">
                    {{ question.answer }}
                </secondary-button>
            </div>
        </div>
    </div>
</template>

<script>
import PrimaryButton from "../Buttons/Primary";
import SecondaryButton from "../Buttons/Secondary";
export default {
    components: {
        PrimaryButton,
        SecondaryButton
    },
    props: [
        'pollQuestions'
    ]
}
</script>
