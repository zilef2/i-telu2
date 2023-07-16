<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DatetimeInput from '@/Components/DatetimeInput.vue';
import { useForm } from '@inertiajs/vue3';
// import Checkbox from '@/Components/Checkbox.vue';
import { reactive, watchEffect } from 'vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    show: Boolean,
    title: String,
    subUnidadsSelect: Object,

})

const emit = defineEmits(["close"]);

const data = reactive({
    multipleSelect: false,
})
const today = new Date();

const justNames = [
    'nombre',
    'descripcion',
    'subtopico_id',
]
const form = useForm({ ...Object.fromEntries(justNames.map(field => [field, ''])) });
const printForm = [
    { idd: 'nombre', label: 'nombre', type: 'text', value: form.nombre },
    { idd: 'descripcion', label: 'descripcion', type: 'text', value: form.descripcion },
];


const create = () => {
    form.post(route('ejercicio.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
            data.multipleSelect = false
        },
        // onError: () => alert(form.errors),
        // onError: () => null,
        onError: () => alert(JSON.stringify(form.errors, null, 4)),

        onFinish: () => null,
    })
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}

    }
})

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-1 gap-6">
                    <div v-for="(atributosform, indice) in printForm" :key="indice">
                        <InputLabel :for="atributosform.label" :value="atributosform.value" />
                        <TextInput :id="atributosform.idd" :type="atributosform.type" class="mt-1 block w-full"
                            v-model="form[atributosform.idd]" required :placeholder="atributosform.label"
                            :error="form.errors[atributosform.idd]" />
                    </div>


                    <div>
                        <InputLabel for="subtopico_id" :value="lang().label.subtopico" />
                        <SelectInput name="subtopico_id" class="mt-1 block w-full" v-model="form.subtopico_id" required
                            :dataSet="subUnidadsSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.subtopico_id" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
