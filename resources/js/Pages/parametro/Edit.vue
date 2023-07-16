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
    parametro: Object,
    subUnidadsSelect: Object,
})

const data = reactive({
    multipleSelect: false,
})

const emit = defineEmits(["close"]);

const justNames = [
    'prompEjercicios',
    'prompObjetivos',
    'NumeroTicketDefecto',
    'pMejoraContinua',
]
const form = useForm({ ...Object.fromEntries(justNames.map(field => [field, ''])) });
// const form = useForm({ ...Object.fromEntries(justNames.map(field =>
//         [field, props.parametro.field]))
//     });

const printForm = [
    // {idd: 'nombre',label: 'nombre', type:'text', value:form.nombre},
    { idd: 'prompEjercicios', label: 'promp para Ejercicios', type: 'area', value: form.prompEjercicios },
    { idd: 'prompObjetivos', label: 'promp para Objetivos', type: 'area', value: form.prompObjetivos },
    { idd: 'NumeroTicketDefecto', label: 'Numero de Tickets por defecto', type: 'number', value: form.NumeroTicketDefecto },
    { idd: 'pMejoraContinua', label: 'promp Ejercicio de Mejora de Continua', type: 'area', value: form.pMejoraContinua },

];

const update = () => {
    form.put(route('parametro.update', props.parametro?.id), {
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
        form.prompEjercicios = props.parametro?.prompEjercicios
        form.prompObjetivos = props.parametro?.prompObjetivos
        form.NumeroTicketDefecto = props.parametro?.NumeroTicketDefecto;
        form.pMejoraContinua = props.parametro?.pMejoraContinua;
    }
})



// const sexos = [ { label: 'Masculino', value: 0 }, { label: 'Femenino', value: 1 } ];

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-3" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-1 gap-6">
                    <div v-for="(atributosform, indice) in printForm" :key="indice">
                        <div v-if="atributosform.type == 'area'" class="">
                            <div class="relative h-fullp-2">
                                <label for="" class="leading-7 text-sm text-gray-600">{{ atributosform.label }} </label>
                                <textarea v-model="form[atributosform.idd]" :id="atributosform.idd"
                                    :name="atributosform.idd" rows="10" cols="35"
                                    class="h-auto resize-none w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 
                                    text-base outline-none text-gray-700 py-1 px-3 leading-6 transition-colors duration-200 ease-in-out"></textarea>
                            </div>
                        </div>
                        <div v-else class="p-2">
                            <InputLabel :for="atributosform.label" :value="atributosform.label" />
                            <TextInput :id="atributosform.idd" :type="atributosform.type" class="mt-1 p-4 block w-full"
                                v-model="form[atributosform.idd]" required :placeholder="atributosform.label"
                                :error="form.errors[atributosform.idd]" />
                        </div>
                    </div>

                    <!-- <div>
                        <InputLabel for="subtopico_id" :value="lang().label.subtopico" />
                        <SelectInput name="subtopico_id" class="mt-1 block w-full" v-model="form.subtopico_id" required
                            :dataSet="subUnidadsSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.subtopico_id" />
                    </div> -->
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
