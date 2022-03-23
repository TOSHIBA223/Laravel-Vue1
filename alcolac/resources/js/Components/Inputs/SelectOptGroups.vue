<template>
    <label :for="selectName" class="relative form-input focus: border-0 rounded-0 p-0">
        <select @change="changeOption($event.target.value)"
                :name="selectName"
                :id="selectName"
                ref="disabledOption"
                class="w-full outline-none border border-black cursor-pointer p-2">
            <option v-if="placeholderOption" value="disabled" disabled selected>{{placeholderOption}}</option>
            <optgroup v-for="(optGroup, index) in options" :key="index"
                :label="index">
                <option v-for="(option, optionIndex) in optGroup" :key="'option-' + optionIndex" :value="'@' + index + '|' + option + '@'">
                    {{option}}
                </option>
            </optgroup>
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
            'resetOnChange'
        ],
        methods: {
            changeOption(value)
            {
                this.$emit('optionSelected', value);
                if(this.resetOnChange === true)
                    this.$refs.disabledOption.value = 'disabled';
            }
        }
    }
</script>
