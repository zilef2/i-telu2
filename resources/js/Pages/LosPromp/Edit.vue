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
    LosPromp: Object,
})

const data = reactive({
    multipleSelect: false,
})

const emit = defineEmits(["close"]);

const justNames = [
    'principal',
    'teoricaOpractica',
    'clasificacion',
]
const form = useForm({ ...Object.fromEntries(justNames.map(field => [field, ''])) });

const update = () => {
    form.put(route('LosPromp.update', props.LosPromp?.id), {
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
        form.principal = props.LosPromp?.principal
        form.teoricaOpractica = props.LosPromp?.teoricaOpractica
        form.clasificacion = props.LosPromp?.clasificacion
    }
})

const teoricaOpracticaForSelec = [ { label: 'teorica', value: 'teorica' }, { label: 'practica', value: 'practica' } ];
const clasificacionForSelec = [ 
    { label: 'General', value: 'General' }, //Expectativas Altas
    { label: 'Enseñanza Explicita', value: 'Enseñanza Explicita' }
];

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-1 gap-6">
                    <div>
                        <InputLabel for="teoricaOpractica" :value="lang().label.teoricaOpractica" />
                        <SelectInput id="teoricaOpractica" class="mt-1 block w-full" v-model="form.teoricaOpractica" required :dataSet="teoricaOpracticaForSelec"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.teoricaOpractica" />
                    </div>
                    <div>
                        <InputLabel for="clasificacion" :value="lang().label.clasificacion" />
                        <SelectInput id="clasificacion" class="mt-1 block w-full" v-model="form.clasificacion" required :dataSet="clasificacionForSelec"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.clasificacion" />
                    </div>

                    <div class="p-2 w-full h-full">
                        <div class="relative h-full">
                            <label for="message" class="leading-7 text-lg text-gray-900">Principal</label>

                            <textarea v-model="form.principal" id="message" name="message" rows="10" cols="35"
                                class="dark:text-white dark:bg-black h-auto resize-none w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 dark:focus:bg-gray-800 focus:bg-white focus:ring-2 focus:ring-indigo-200 
                                text-base outline-none text-gray-700 py-1 px-3 leading-6 transition-colors duration-200 ease-in-out"></textarea>
                        </div>
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
