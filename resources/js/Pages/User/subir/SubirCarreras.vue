<script setup>
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, onMounted, watchEffect } from 'vue';
import pkg from 'lodash';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

import { DocumentArrowUpIcon, ArrowUpCircleIcon, ArrowDownCircleIcon } from '@heroicons/vue/24/solid';
import '@vuepic/vue-datepicker/dist/main.css'


const { _, debounce, pickBy } = pkg
const props = defineProps({
    UniversidadSelect: Object,
    flash: Object,
});

const data = reactive({
    UniversidadSelect: null,
    isOpen: false,
    tooltipSettings: {
        content: '',
        shown: false, // Always show the tooltip
        triggers: [], // No triggers to hide the tooltip
    },
})

const form_carreras = useForm({
    archivo_componente_carreras: null,
    universidadID: 0,
});

watchEffect(() => {
    // form_carreras.archivo_componente_carreras
})

function uploadCarreras() {
    console.log("🧈 debu form_carreras.universidadID:", form_carreras.universidadID);
    if (form_carreras.universidadID === null || form_carreras.universidadID == 0) {
        data.tooltipSettings.content = 'Seleccione universidad'
    } else {
        form_carreras.post(route('user.uploadCarreras'), {
            preserveScroll: true,
            onSuccess: () => {
                // form_carreras.reset()
                // data.respuesta = $page.props.flash.success
            },
            onError: () => null,
            onFinish: () => {
                data.tooltipSettings.shown = false
            },
        });
    }
}


</script>
<template>
    <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
        <DocumentArrowUpIcon class="mt-5 h-12 lg:h-20 w-full object-cover object-center" />

        <div class="p-6">
            <h3 class="title-font text-lg font-medium text-gray-900 mb-3">Inscribir carreras </h3>
            <p class="leading-relaxed mb-3"> Seleccione la universidad donde va a matricular las carreras</p>

            <form @submit.prevent="uploadCarreras" id="form_componente_carreras">
                <div>
                    <InputLabel for="universidadID" :value="lang().label.carrera" />
                    <SelectInput id="universidadID" class="mt-1 block w-full" v-model="form_carreras.universidadID" required
                        :dataSet="props.UniversidadSelect">
                    </SelectInput>
                    <InputError class="mt-2" :message="form_carreras.errors.universidadID" />
                </div>
                <div class="mt-6">
                    <label for="filecarreras" class=" text-white font-bold py-2 px-4 rounded bg-sky-500"
                        :class="{ 'bg-gray-600': form_carreras.archivo_componente_carreras }">
                        Seleccionar Archivo
                    </label>
                    <input id="filecarreras" type="file" @input="form_carreras.archivo_componente_carreras = $event.target.files[0]"
                        accept="application/vnd.openxmlform_carrerasats-officedocument.spreadsheetml.sheet"
                        class="opacity-0 relative inset-0 w-full my-1 cursor-pointer" />
                    <p v-if="form_carreras.archivo_componente_carreras" class="w-full my-2 text-green-600">
                        {{ form_carreras.archivo_componente_carreras.name }}</p>
                </div>

                <progress v-if="form_carreras.progress" :value="form_carreras.progress.percentage" max="100" class="bg-sky-200 my-2">
                    {{ form_carreras.progress.percentage }}%
                </progress>

                <div v-if="form_carreras.archivo_componente_carreras" class="w-auto">
                    <Button v-show="can(['create user'])" :disabled="form_carreras.archivo_componente_carreras == null"
                        :class="{ 'bg-sky-500': form_carreras.archivo_componente_carreras !== null }"
                        class="w-32 rounded-none my-4 px-4 py-2 text-white">
                        {{ lang().button.subir }} archivo
                    </Button>
                </div>
                <div class="w-auto text-red-600 text-lg my-4">
                    {{ data.tooltipSettings.content }}
                </div>
            </form>



            <h2 class="text-xl text-gray-900 dark:text-white">El formato requiere las siguientes columnas</h2>
            <ul class="list-decimal my-4 mx-5">
                <li class="text-lg">Nombre de la carrera</li>
                <li class="text-lg">Código de la carrera</li>
            </ul>
            <div class="p-4 max-w-md mx-auto">
                <p class="">Ejemplo:</p>
                <div class="relative overflow-hidden">
                    <img src="/Carreras.png" alt="imagen excel matriculas"
                        class="rounded-lg transition-transform duration-500 transform hover:scale-110">
                </div>
            </div>
        </div>
    </div>
</template>

