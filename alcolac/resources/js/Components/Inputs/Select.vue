<template>
    <label :for="selectName" class="relative form-input focus: border-0 rounded-0 p-0">
        <select @change="$emit('optionSelected', $event.target.value)"
                :name="selectName"
                :id="selectName"
                class="w-full outline-none border border-black cursor-pointer p-2"
                :multiple="multipleSelect">
            <option v-if="placeholderOption" disabled selected>{{placeholderOption}}</option>
            <option v-for="option in options" :key="option.id" :value="option.id" :selected="selectedOption === option.id">
                {{option.name}}
            </option>
        </select>
    </label>
</template>

<script>
    export default {
        props: [
            'selectName',
            'options',
            'placeholderOption',
            'selectedOption',
            'multipleSelect'
        ]
    }
</script>
