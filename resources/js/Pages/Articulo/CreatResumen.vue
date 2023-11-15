<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Generando from '@/Components/uiverse/Generando.vue';
import Modal from '@/Components/Modal.vue';
import GreenButton from '@/Components/GreenButton.vue';

import {router, useForm} from '@inertiajs/vue3';
import {onMounted, reactive, watch, watchEffect} from 'vue';

import {ContarPalabras, NoUnderLines} from '@/global.ts';
import "vue-select/dist/vue-select.css";

const props = defineProps({
    show: Boolean,
    title: String,
    numberPermissions: Number,
    ValoresGenerarSeccion: Object,
    HijoSelec: Object,
    CuantasUniversidades: Number,
});

const emit = defineEmits(["close"]);

const data = reactive({
    mostrarLoader: false,
    restarAlToken: 0,
    NumSujerencias: [], //! Aqui se cuenta el numero de veces que se solicito la IA
    errorCarrera: [],
    campos: [
        { id: 'nick', etiqueta: 'Titulo del resumen', valor: [] },
        { id: 'Resumen', etiqueta: NoUnderLines('Resumen'), valor: [] },
    ],
    tipoTexto: '',
    campoActivo: null,
    ErrorFormulario: '',
})
const form = useForm({
    ...Object.fromEntries(data.campos.map(field => [field.id, []])),
    materiaid: 1,
    Portada: 'Resumen',
    Resumen_integer: 0,
    // no se usan, solo son del articulo
    Introduccion_integer: 0,
    Discusion_integer: 0,
    Conclusiones_integer: 0,
    Metodologia_integer: 0,

    universidad_id:0,
    carrera_id:0,
    materia_id:0,
});

onMounted(() => {
    let ele;
    data.campos.forEach(element => {
        ele = element.id
        data.NumSujerencias[ele] = 0
        form.Resumen[0] = 'Texto de ejemplo. Seguido de mas texto de ejemplo'
    });

    if (props.numberPermissions > 9) {
        form.nick[0] = 'Supernova: Cataclismos del Universo'
        form.Resumen[0] = 'Este resumen trata sobre uno de los procesos que mas energia emite del universo, tanto asi, que la luz emitida por una supernova compite con la luz que emite toda la galaxia'
    }
})

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
})

watch(() => data.universidadid, (newX) => {
    if (newX.value !== 0 && props.HijoSelec.carreras[1])
        data.carreraid = props.HijoSelec.carreras[1];
})

// watch(() => data.carreraid, (newX) => {
//     let primerIndice = 0
//     if (props.numberPermissions > 9) primerIndice = 1

//     if (newX.value !== 0)
//         data.materiaid = props.Selects.opcionesAsignatura[newX.value][primerIndice];
// })

function recibirRespuesta(newX){
     if (newX && newX.respuesta) {
         form[data.tipoTexto][1] = newX.respuesta
         form[data.tipoTexto][2] = form[data.tipoTexto][0]
         data.restarAlToken = newX.restarAlToken
         data.NumSujerencias[data.tipoTexto]++;
     }
}

watch(() => props.ValoresGenerarSeccion, (newX) => {
    recibirRespuesta(newX);
})

const OptimizarResumenOIntroduccion = async (elTexto, tipoTexto) => {
    if(elTexto){
        data.errorCarrera = [];
        data.mostrarLoader = true;
        data.tipoTexto = tipoTexto
        const tamanoMinimo = 5

        let TieneSuficientesPalabras = ContarPalabras(elTexto) > tamanoMinimo || elTexto.length > (tamanoMinimo * 5)
        if (TieneSuficientesPalabras) {
            // if (data.materiaid && data.materiaid.value) {

            if(form[data.tipoTexto][2]){
                form[data.tipoTexto][0] = form[data.tipoTexto][2]

            }

            router.reload({
                only: [
                    'ValoresGenerarSeccion',
                ],
                data: {
                    elTexto: elTexto,
                    /*materia: data.materiaid.value,*/
                    materia: 1
                },
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    data.mostrarLoader = false
                },
                onError: () => alert(JSON.stringify(form.errors, null, 4)),
                onFinish: () => {
                    data.mostrarLoader = false
                }
            })
            // } else {
            //     data.errorCarrera[0] = 'Seleccione una asignatura primero';
            // }
        } else {
            data.errorCarrera[tipoTexto] = 'El resumen aun es muy corto';
        }
        data.mostrarLoader = false;
    }
}

const create = () => {
    form.Resumen_integer = data.NumSujerencias['Resumen']

    form.universidad_id = data.universidadid
    form.carrera_id = data.carreraid
    form.materia_id = data.materiaid

    console.log(data.materiaid)
    if(data.materiaid && data.materiaid.value !== 0){
        data.ErrorFormulario = ''
        form.post(route('Articulo.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => alert(JSON.stringify(form.errors, null, 4)),
            onFinish: () => null,
        })
    }else{
        data.ErrorFormulario = 'No hay materia seleccionada'
    }

}
</script>

<template>
    <section class="space-y-4">
        <Modal :show="props.show" @close="emit('close')" maxWidth="3xl">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Nuevo resumen
                </h2>
                <div v-if="data.errorCarrera[0]" class="flex items-center mt-6">
                    <p class="text-red-500 dark:text-red-200 underline">
                        {{ data.errorCarrera[0] }}</p>
                </div>
                <div v-if="data.ErrorFormulario !== ''" class="flex items-center mt-6">
                    <p class="text-red-500 dark:text-red-200 underline">
                        {{ data.ErrorFormulario }}</p>
                </div>
                <div class="flex text-center mt-6">
                    <p class="text-gray-500 dark:text-gray-400">A que asignatura pertenecerá el resumen</p>
                </div>
                <div class="mt-2 grid grid-cols-3 gap-2">
                    <div id="opciones2U" class="mt-2 w-full">
                        <v-select :options="props.HijoSelec.universidades" label="title"
                                  v-model="data.universidadid"></v-select>
                    </div>
                    <div v-if="data.universidadid && data.universidadid.value !== 0" id="carrera" class="mt-2 w-full">
                        <v-select :options="props.HijoSelec.carreras" label="title"
                                  v-model="data.carreraid"></v-select>
                    </div>
                    <div v-if="data.carreraid && data.carreraid.value !== 0" id="asignatura" class="mt-2 w-full">
                        <v-select :options="props.HijoSelec.materias" label="title"
                                  v-model="data.materiaid"></v-select>
                    </div>
                </div>
                <div class="flex items-center mt-6">
                    <p v-if="data.restarAlToken && data.restarAlToken !== 0" class="text-sky-600 dark:text-gray-400">Se consumió: {{ data.restarAlToken }} token</p>
                </div>

                <div v-for="(campo) in data.campos" :key="campo.id">
                    <div v-if="data.NumSujerencias[campo.id]" class="grid grid-cols-2 gap-8">
                        <div class="">
                            <label :for="campo.id" class="text-gray-500 text-xl font-bold dark:text-gray-400 mb-2">{{campo.etiqueta }}</label>
                            <div class="relative rounded-md shadow-sm">
                                <textarea :id="campo.id + '0'" @focus="data.campoActivo = campo.id" rows="8" cols="33"
                                    @blur="data.campoActivo = null" v-model="form[campo.id][0]" disabled
                                    class="block w-full px-5 py-3 mt-2 bg-gray-50 text-gray-700 placeholder-gray-400 border border-gray-200
                                            rounded-lg dark:placeholder-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700 focus:border-blue-400
                                            dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 text-justify" />
                                <div v-if="data.campoActivo === campo.id && form[campo.id] && form[campo.id][0] === ''"
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center cursor-progress text-gray-400">
                                    Puede preguntar a la IA, haciendo click en Generar o Refinar
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <label :for="campo.id" class="text-gray-500 text-xl font-bold dark:text-gray-400 mb-2">Sugerencia {{ campo.etiqueta }}</label>
                            <div class="relative rounded-md shadow-sm select-none">
                                <div v-if="form[campo.id] && form[campo.id][1]"
                                    class="block w-full px-5 py-3 mt-2 text-white font-sans bg-black border border-sky-600 select-none
                                            rounded-lg dark:placeholder-gray-600 dark:bg-gray-200 dark:text-gray-800 dark:border-gray-700 focus:border-blue-400
                                            dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 text-justify">
                                    {{ form[campo.id][1] }}
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label :for="campo.id" class="text-gray-500 text-xl font-bold dark:text-gray-400 mb-2">{{ campo.etiqueta }} Final</label>
                            <div class="relative rounded-md shadow-sm">
                                <textarea :id="campo.id + '2'" @focus="data.campoActivo = campo.id" rows="6" cols="33"
                                    @blur="data.campoActivo = null" v-model="form[campo.id][2]"
                                    placeholder="Teniendo en cuenta la sugerencia de la IA..." @keyup.enter="OptimizarResumenOIntroduccion(form[campo.id][2] ? form[campo.id][2] : form[campo.id][0], campo.id)"
                                    class="block w-full px-5 py-3 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200
                                            rounded-lg dark:placeholder-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-400
                                            dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 text-justify" />
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="">
                            <label :for="campo.id"
                                class="rounded-2xl px-10 text-gray-500 text-xl font-bold dark:text-gray-400 shadow-sm bg-gradient-to-r from-gray-50 via-gray-100 to-sky-100 mb-2">
                                {{ campo.etiqueta }}
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <textarea :id="campo.id" @focus="data.campoActivo = campo.id" rows="4" cols="33"
                                    @blur="data.campoActivo = null" v-model="form[campo.id][0]" @keyup.enter="OptimizarResumenOIntroduccion(form[campo.id][2] ? form[campo.id][2] : form[campo.id][0], campo.id)"
                                    class="block w-full px-5 py-3 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200
                                            rounded-lg dark:placeholder-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-400
                                            dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40" />
                                <div v-if="data.campoActivo === campo.id && form[campo.id][0] === ''"
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center cursor-progress text-gray-400">
                                    Puede preguntar a la IA, haciendo click en Generar o Refinar
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="campo.id === 'Resumen' || campo.id === 'Introduccion' || campo.id === 'Metodologia' || campo.id === 'Discusion' || campo.id === 'Conclusiones'"
                        class="">
                        <div class="flex items-center my-2">
                            <p v-if="data.errorCarrera[0]" class="text-red-500 dark:text-red-200 underline">
                                {{ data.errorCarrera[0] }}</p>
                        </div>
                        <div class="flex items-center mt-2">
                            <p v-if="data.restarAlToken && data.restarAlToken !== 0"
                                class="text-sky-600 text-lg dark:text-gray-600">Se
                                consumió: {{ data.restarAlToken }} token</p>
                        </div>
                        <GreenButton v-if="props.CuantasUniversidades > 0"
                            :class="{ 'opacity-25': data.mostrarLoader }" :disabled="data.mostrarLoader"
                            @click="OptimizarResumenOIntroduccion(form[campo.id][2] ? form[campo.id][2] : form[campo.id][0], campo.id)"
                            class="ml-3 mt-1 px-10 py-3 outline outline-offset-2 ring-2 ring-green-700">
                            {{ data.mostrarLoader ? 'Revisando...' : 'Revisar' }}
                        </GreenButton>
                        <div class="mt-8">
                            <Generando v-if="data.mostrarLoader" />
                        </div>

                        <div class="flex items-center mt-6">
                            <p v-if="data.errorCarrera[campo.id]" class="text-red-500 dark:text-red-200 underline">
                                {{ data.errorCarrera[campo.id] }}</p>
                        </div>

                    </div>

                    <hr class="border-2 border-sky-100 my-8">
                </div>

                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}</SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }} resumen
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
