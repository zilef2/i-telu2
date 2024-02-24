<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

import { useForm } from '@inertiajs/vue3';
import {watchEffect, reactive, ref, computed} from 'vue';
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    TransitionRoot
} from "@headlessui/vue";
import {CheckIcon, ChevronUpDownIcon} from "@heroicons/vue/24/solid";

const props = defineProps({
    show: Boolean,
    title: String,
    subtopico: Object,
    UnidadsSelect: Object,
})

const data = reactive({
    multipleSelect: false,
})
const emit = defineEmits(["close"]);
const justNames = [
    'nombre',
    'descripcion',
    'unidad_id',
    'enum',
    // 'codigo',
    'resultado_aprendizaje',
]
const form = useForm({ ...Object.fromEntries(justNames.map(field => [field, ''])) });
const printForm = [
    { idd: 'enum', label: 'enumTema', type: 'number', value: form.enum },
    { idd: 'nombre', label: 'nombre', type: 'text', value: form.nombre },
    // { idd: 'codigo', label: 'codigo', type: 'text', value: form.codigo },
    { idd: 'resultado_aprendizaje', label: 'resultado_aprendizaje', type: 'text', value: form.resultado_aprendizaje },
    { idd: 'descripcion', label: 'Descripcion (opcional)', type: 'text', value: form.descripcion },
];
watchEffect(() => {
    if (props.show) {
        form.errors = {}
        form.nombre = props.subtopico?.nombre
        form.descripcion = props.subtopico?.descripcion
        form.unidad_id = props.subtopico?.unidad_id
        form.enum = props.subtopico?.enum
        // form.codigo = props.subtopico?.codigo
    }
})

let query = ref('')
let filterCombo = computed(() =>
    query.value === ''
        ? props.UnidadsSelect
        : props.UnidadsSelect.filter((person) =>
            person.name
                .toLowerCase()
                .replace(/\s+/g, '')
                .includes(query.value.toLowerCase().replace(/\s+/g, ''))
        )
)

const update = () => {
    if(
        form.nombre &&
        form.unidad_id &&
        form.enum &&
        form.resultado_aprendizaje
    ){
        form.put(route('subtopico.update', props.subtopico?.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
                data.multipleSelect = false
            },
            onError: () => null,
            onFinish: () => null,
        })
    }else{
        Alert('Faltan campos')
    }

}
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-2 gap-6">
                    <div class="block w-full">
                        <InputLabel value="Unidad" />

                        <div id="opcinesActividadO" class="mx-1 w-[550px]">
                            <div class="inline-flex">
                                <Combobox v-model="form.unidad_id">
                                    <div class="relative mt-1 w-96">
                                        <div class="relative w-full cursor-default overflow-hidden rounded-lg bg-white text-left shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm">
                                            <ComboboxInput
                                                class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                                                :displayValue="(person) => person?.name"
                                                @change="query = $event.target.value"
                                            />
                                            <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                                                <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true"/>
                                            </ComboboxButton>
                                        </div>
                                        <TransitionRoot
                                            leave="transition ease-in duration-100"
                                            leaveFrom="opacity-100"
                                            leaveTo="opacity-0"
                                            @after-leave="query = ''">
                                            <ComboboxOptions
                                                class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md
                                                       bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                                                <div v-if="filterCombo.length === 0 && query !== ''" class="relative cursor-default select-none px-4 py-2 text-gray-700">
                                                    Sin resultados
                                                </div>

                                                <ComboboxOption
                                                    v-for="person in filterCombo"
                                                    as="template" :key="person.id" :value="person" v-slot="{ selected, active }">
                                                    <li class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                        :class="{ 'bg-teal-600 text-white': active, 'text-gray-900': !active, }">
                                                        <span class="block truncate" :class="{ 'font-medium': selected, 'font-normal': !selected }">
                                                          {{ person.name }}
                                                        </span>
                                                        <span
                                                            v-if="form.unidad_id && form.unidad_id.id === person.id"
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3"
                                                            :class="{ 'text-white': active, 'text-teal-600': !active }"
                                                        >
                                                          <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                                        </span>
                                                    </li>
                                                </ComboboxOption>
                                            </ComboboxOptions>
                                        </TransitionRoot>
                                    </div>
                                </Combobox>
                            </div>
                        </div>
                    </div>
                    <div v-for="(atributosform, indice) in printForm" :key="indice">
                        <InputLabel :for="atributosform.label" :value="lang().label[atributosform.label]" />
                        <TextInput :id="atributosform.idd" :type="atributosform.type" class="mt-1 block w-full"
                            v-model="form[atributosform.idd]" :placeholder="atributosform.label"
                            :error="form.errors[atributosform.idd]" />
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
