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
const justNames = [
    'principal',
    'teoricaOpractica',
    'clasificacion',
]; const form = useForm({ ...Object.fromEntries(justNames.map(field => [field, ''])) });

// const printForm = [
    // { idd: 'principal', label: 'principal', type: 'text', value: form.principal },
    // { idd: 'teoricaOpractica', label: 'teoricaOpractica', type: 'text', value: form.teoricaOpractica },
    // { idd: 'clasificacion', label: 'clasificacion', type: 'text', value: form.clasificacion },
// ];


const create = () => {
    form.post(route('LosPromp.store'), {
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
const teoricaOpracticaForSelec = [ { label: 'teorica', value: 'teorica' }, { label: 'practica', value: 'practica' } ];
const clasificacionForSelec = [ { label: 'Expectativas Altas', value: 'Expectativas Altas' }, { label: 'Enseñanza Explicita', value: 'Enseñanza Explicita' } ];


</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-1 gap-6">
                    <!-- <div v-for="(atributosform, indice) in printForm" :key="indice">
                        <InputLabel :for="atributosform.label" :value="atributosform.value" />
                        <TextInput :id="atributosform.idd" :type="atributosform.type" class="mt-1 block w-full"
                            v-model="form[atributosform.idd]" required :placeholder="atributosform.label"
                            :error="form.errors[atributosform.idd]" />
                    </div> -->
                    
                    

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
                            <p for="message" class="leading-7 text-lg text-gray-600">Para usar campos variables, redondeelos entre parentesis () o corchetes []</p>
                            <p for="message" class="leading-7 text-lg text-gray-600">Por ejemplo, "Genere 3 preguntas del [tema]"</p>

                            <textarea v-model="form.principal" id="message" name="message" rows="10" cols="35"
                                class="dark:text-white dark:bg-black h-auto resize-none w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 dark:focus:bg-gray-800 focus:bg-white focus:ring-2 focus:ring-indigo-200 
                                text-base outline-none text-gray-700 py-1 px-3 leading-6 transition-colors duration-200 ease-in-out"></textarea>
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
                        @click="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
