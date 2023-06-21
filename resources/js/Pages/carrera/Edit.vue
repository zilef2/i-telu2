<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DatetimeInput from '@/Components/DatetimeInput.vue';

import { useForm } from '@inertiajs/vue3';
import { watchEffect, reactive } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import SelectInput from '@/Components/SelectInput.vue';


const props = defineProps({
    show: Boolean,
    title: String,
    carrera: Object,
    PapaSelect: Object,
})

const data = reactive({
    multipleSelect: false,
})

const emit = defineEmits(["close"]);

const form = useForm({
    nombre: '',
    descripcion: '',
    universidad_id: 1,
});

const printForm = [
    {idd: 'nombre',label: 'nombre', type:'text', value:form.nombre},
    {idd: 'descripcion',label: 'descripcion', type:'text', value:form.descripcion},
];

const update = () => {
    form.put(route('carrera.update', props.carrera?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
            data.multipleSelect = false
        },
        onError: () => null,
        onFinish: () => null,
    })
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
        form.nombre = props.carrera?.nombre
        form.descripcion = props.carrera?.descripcion
        form.universidad_id = props.carrera?.universidad_id
    }
})
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-2 gap-6">
                    <div v-for="(atributosform, indice) in printForm" :key="indice">
                        <InputLabel :value="atributosform.label" />
                        <TextInput :id="atributosform.idd" :type="atributosform.type" class="mt-1 block w-full"
                            v-model="form[atributosform.idd]" required
                            :placeholder="atributosform.label" :error="form.errors[atributosform.idd]" />
                    </div>

                    <div>
                        <InputLabel for="universidad_id" :value="lang().label.universidad" />
                        <SelectInput id="universidad_id" class="mt-1 block w-full" v-model="form.universidad_id" required :dataSet="PapaSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.universidad_id" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update">
                        {{ form.processing ? lang().button.save + '...' : lang().button.save }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
