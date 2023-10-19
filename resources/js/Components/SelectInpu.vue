<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
    thelabel: String,
    error: {
        type: String,
        default: null,
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div>
        <InputLabel :for="modelValue" :value="lang().label[props.thelabel]" />
        <SelectInput :id="modelValue"
            class="mt-1 block w-full" 
            :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input" 
            required 
            :dataSet="PapaSelect"
        />
        <InputError class="mt-2" :message="form.errors.modelValue" />
    </div>
</template>
