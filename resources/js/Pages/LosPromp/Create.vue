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
import { reactive, watchEffect, onMounted } from 'vue';
import vSelect from "vue-select"; import "vue-select/dist/vue-select.css";

const props = defineProps({
    show: Boolean,
    title: String,
    subUnidadsSelect: Object,
})

const emit = defineEmits(["close"]);

const justNames = [
    'principal',
    // 'teoricaOpractica',
    'clasificacion',
    'tokensAproximados',
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
        },
        // onError: () => alert(form.errors),
        // onError: () => null,
        onError: () => alert(JSON.stringify(form.errors, null, 4)),

        onFinish: () => null,
    })
}

onMounted(() => {
    // form.teoricaOpractica = {'title':'Teorica' , 'value':'teorica'};
    form.clasificacion = {'title':'General' , 'value':'General'};
    form.tokensAproximados = '1';
});

const AdvertenciaPrompLargo = 200;
watchEffect(() => {
    if (props.show) {
        form.errors = {}

        if(form.principal.length > AdvertenciaPrompLargo) form.tokensAproximados = '' + Math.floor(form.principal.length/(AdvertenciaPrompLargo/1.5));
    }
})
// const teoricaOpracticaForSelec = [ { title: 'Teorica', value: 'teorica' }, { title: 'Practica', value: 'practica' } ];
// const clasificacionForSelec = [ 
//     { title: 'General', value: 'General' }, //Expectativas Altas
//     { title: 'Enseñanza Explicita', value: 'Enseñanza Explicita' }
// ];

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="my-6 grid xs:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    <!-- <div v-for="(atributosform, indice) in printForm" :key="indice">
                                                <InputLabel :for="atributosform.label" :value="lang().label[atributosform.label]" />

                        <TextInput :id="atributosform.idd" :type="atributosform.type" class="mt-1 block w-full"
                            v-model="form[atributosform.idd]" required :placeholder="atributosform.label"
                            :error="form.errors[atributosform.idd]" />
                    </div> -->

                    <!-- <div id="SelectVue">
                        <label name="labelSelectVue">{{lang().label.teoricaOpractica}}</label>
                        <v-select :options="teoricaOpracticaForSelec" label="title" v-model="form.teoricaOpractica"></v-select>
                    </div> -->
                    <!-- <div id="SelectVue">
                        <label name="labelSelectVue">{{lang().label.clasificacion}}</label>
                        <v-select :options="clasificacionForSelec" label="title" v-model="form.clasificacion"></v-select>
                    </div> -->
                     <!-- <div>
                        <InputLabel for="teoricaOpractica" :value="lang().label.teoricaOpractica" />
                        <SelectInput id="teoricaOpractica" class="mt-1 block w-full" v-model="form.teoricaOpractica"
                            required :dataSet="teoricaOpracticaForSelec"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.teoricaOpractica" />
                    </div> -->
                    <!-- <div>
                        <InputLabel for="clasificacion" :value="lang().label.clasificacion" />
                        <SelectInput id="clasificacion" class="mt-1 block w-full" v-model="form.clasificacion" required
                            :dataSet="clasificacionForSelec"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.clasificacion" />
                    </div> -->

                    <div>
                        <InputLabel for="tokens" :value="lang().label.tokens" />
                        <TextInput id="tokens" type="number" disabled class="mt-1 block w-full" v-model="form.tokensAproximados" required
                            :placeholder="lang().placeholder.tokens" :error="form.errors.tokensAproximados" 
                            :class="{ 'text-red-800 bg-red-100' : form.principal.length > AdvertenciaPrompLargo}"
                             />
                        <InputError class="mt-2" :message="form.errors.tokensAproximados" />
                    </div>

                    <div class="pt-3 w-full h-full col-span-2 xl:col-span-3">
                        <div class="relative h-full">
                            <label for="message" class="leading-7 text-lg text-gray-900">Redacte el promp</label>
                            <p for="message" class="leading-7 text-lg text-gray-600">Para insertar el tema, la unidad o la asignatura, redondeelos entre parentesis () o corchetes []</p>
                            <!-- <p for="message" class="leading-7 text-lg text-gray-600 font-sans">Por ejemplo: "Genere 3 preguntas del
                                [tema]"
                            </p> -->

                            <textarea v-model="form.principal" id="message" name="message" rows="10" cols="35"
                                class="dark:text-white dark:bg-black h-auto resize-none w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 dark:focus:bg-gray-800 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-6 transition-colors duration-200 ease-in-out"
                                placeholder="Genere 3 preguntas del [tema]"></textarea>
                        </div>
                    </div>
                    <div class="px-2 w-full col-span-2 xl:col-span-3">
                        <div class="relative">
                            <label for="message" class="leading-7 my-1 text-lg text-gray-900">La instruccion necesita un [Tema], las siguientes variables son opcionales</label>
                            <ul class="list-decimal ml-8">
                                <li class="font-medium font-sans text-lg ">[Asignatura]</li>
                                <li class="font-medium font-sans text-lg ">[Unidad]</li>
                            </ul>
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

<style>

[name = "labelSelectVue"],
.muted {
  color: #1b416699;
}

[name = "labelSelectVue"] {
  font-size: 22px;
  font-weight: 600;
}

</style>