<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DatetimeInput from '@/Components/DatetimeInput.vue';
import Generando from '@/Components/uiverse/Generando.vue';
import GreenButton from '@/Components/GreenButton.vue';

import { router, Link, useForm } from '@inertiajs/vue3';
// import Checkbox from '@/Components/Checkbox.vue';
import { reactive, watchEffect, onMounted, watch } from 'vue';
import Toast from '@/Components/Toast.vue';

import { NoUnderLines, ContarPalabras } from '@/global.ts';;
import "vue-select/dist/vue-select.css";

const props = defineProps({
    title: String,
    breadcrumbs: Object,

    numberPermissions: Number,
    CalifiConslta: Array,
    CalifiInicial: Object,
    articulo: Object,

    Universidad: Object,
    Carrera: Object,
    Materia: Object,
    ValoresGenerarSeccion: Object,
});

// const emit = defineEmits(["close"]);

const data = reactive({
    mostrarLoader: false,
    MensajeFinal: '',
    calificacionBackend: '',
    restarAlToken: 0,
    NumSujerencias: {
        'Resumen_integer' : props.articulo.Resumen_integer,
        'Introduccion_integer' : props.articulo.Introduccion_integer,
        'Discusion_integer' : props.articulo.Discusion_integer,
        'Conclusiones_integer' : props.articulo.Conclusiones_integer,
        'Metodologia_integer' : props.articulo.Metodologia_integer,
    },
    errorCarrera: [],
    campos: [
        { id: 'nick', etiqueta: 'Titulo del articulo', valor: [] },
        { id: 'Portada', etiqueta: NoUnderLines('Portada'), valor: [] },
        { id: 'Resumen', etiqueta: NoUnderLines('Resumen'), valor: [] },
        { id: 'Resumen_correcciones', etiqueta: NoUnderLines('Resumen_correcciones'), valor: '' },
        { id: 'Palabras_Clave', etiqueta: NoUnderLines('Palabras Clave'), valor: [] },
        { id: 'Introduccion', etiqueta: NoUnderLines('Introduccion'), valor: [] },
        { id: 'Introduccion_correcciones', etiqueta: NoUnderLines('Introduccion_correcciones'), valor: '' },
        { id: 'Revision_de_la_Literatura', etiqueta: NoUnderLines('Revision de la Literatura'), valor: [] },
        { id: 'Metodologia', etiqueta: NoUnderLines('Metodologia'), valor: [] },
        { id: 'Metodologia_correcciones', etiqueta: NoUnderLines('Metodologia_correcciones'), valor: '' },
        { id: 'Resultados', etiqueta: NoUnderLines('Resultados'), valor: [] },
        { id: 'Discusion', etiqueta: NoUnderLines('Discusion'), valor: [] },
        { id: 'Discusion_correcciones', etiqueta: NoUnderLines('Discusion_correcciones'), valor: '' },
        { id: 'Conclusiones', etiqueta: NoUnderLines('Conclusiones'), valor: [] },
        { id: 'Conclusiones_correcciones', etiqueta: NoUnderLines('Conclusiones_correcciones'), valor: '' },
        { id: 'Agradecimientos', etiqueta: NoUnderLines('Agradecimientos'), valor: [] },
        { id: 'Referencias', etiqueta: NoUnderLines('Referencias'), valor: [] },
        { id: 'Anexos_o_Apendices', etiqueta: NoUnderLines('Anexos_o_Apendices'), valor: [] },
    ],
    tipoTexto: '',
    campoActivo: null,
    calificacionDocente: 3,
    EstaCalificado: props.CalifiInicial['docente'] >= 0,
    EstaCalificadoIA: props.CalifiInicial['IA'] >= 0,
    CalifiActual : props.CalifiInicial,
})
console.log(data.EstaCalificado)
console.log(props.calificacionARticulo)
const form = useForm({
    ...Object.fromEntries(data.campos.map(field => [field.id, []])),

    user_id:0,
    Resumen_integer:0,
    Introduccion_integer:0,
    Discusion_integer:0,
    Conclusiones_integer:0,
    Metodologia_integer:0,
    is_correcciones:true
});

function inicializarForm(){
    form.nick[0] = props.articulo.nick
    form.Portada[0] = props.articulo.Portada
    form.Resumen[0] = props.articulo.Resumen
    form.Palabras_Clave[0] = props.articulo.Palabras_Clave
    form.Introduccion[0] = props.articulo.Introduccion
    form.Revision_de_la_Literatura[0] = props.articulo.Revision_de_la_Literatura
    form.Metodologia[0] = props.articulo.Metodologia
    form.Resultados[0] = props.articulo.Resultados
    form.Discusion[0] = props.articulo.Discusion
    form.Conclusiones[0] = props.articulo.Conclusiones
    form.Agradecimientos[0] = props.articulo.Agradecimientos
    form.Referencias[0] = props.articulo.Referencias
    form.Anexos_o_Apendices[0] = props.articulo.Anexos_o_Apendices
    form.user_id = props.articulo.user_id
}

onMounted(() => {
    console.log(data.NumSujerencias)

    inicializarForm()
})
watchEffect(() => {})
watch(() => props.ValoresGenerarSeccion, (newX) => {
    data.mostrarLoader = true;
    // console.log("🧈 debu newX:", newX);
    if (newX && newX.respuesta) {
        data.calificacionBackend = newX.respuesta
        data.CalifiActual = props.CalifiConslta
        //todo: newX.restarAlToken

            // data.mostrarLoader = false;
        window.location.reload();
    }
})

const scrollToBottom = () => {
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
      });
}
const scrollToTop = () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
}

const CalificarArticulo = (notaManual = null) => {

    data.errorCarrera = [];
    data.mostrarLoader = true;
    // data.laNotaEsManual = notaManual !== null;

    router.reload({
        only: [
            'ValoresGenerarSeccion',
            'CalifiConslta',
        ],
        data: {
            elformulario: form,
            articuloid: props.articulo.id,
            notaManual: notaManual,
        },
    }, {
        preserveScroll: true,
        onSuccess: () => {data.mostrarLoader = true;},
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => {data.mostrarLoader = true;}
    })

}

const update = () => {

    form.is_correcciones = true

    form.put(route('Articulo.update',props.articulo.id), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            form.reset()
            data.MensajeFinal = 'Articulo guardado correctamente'
        },
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null,
    })
}

</script>

<template>
    <Toast :flash="$page.props.flash" />

    <section class="space-y-1 flex self-center">
        <div class="flex-none w-14"> . </div>
        <div class="grow mx-1 md:mx-12 xl:mx-20 text-center p-8">
            <form @submit.prevent="create" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="grid grid-cols-1 gap-6">
                    <div class="container flex flex-col items-center justify-center px-6 mx-auto">
                        <div class="flex justify-center mx-auto">
                            <img class="w-auto h-7 sm:h-8" src="https://merakiui.com/images/logo.svg" alt="">
                        </div>

                        <div v-if="data.universidadid" class="mt-1">
                            <p class="text-gray-500 text-center text-xl font-bold dark:text-gray-400">{{ data.universidadid.title }}</p>
                        </div>
                        <div class="">
                            <p v-if="data.carreraid && data.materiaid" id="opciones2U" class="w-full">
                                {{data.carreraid.title}}, {{data.materiaid.title}}
                            </p>
                        </div>
                        <p class="my-1 text-gray-500 dark:text-gray-400">
                            {{ props.Universidad.nombre }}
                        </p>
                        <p class="my-1 text-gray-500 dark:text-gray-400">
                            {{ props.Carrera.nombre }}
                        </p>
                        <p class="my-1 text-gray-500 dark:text-gray-400">
                            {{ props.Materia.nombre }}
                        </p>
                        <h1 v-if="!form.nick"
                            class="mt-4 text-2xl font-semibold tracking-wide text-center text-gray-800 capitalize md:text-3xl dark:text-white">
                            Nuevo articulo </h1>
                        <h1 v-else
                            class="mt-4 text-2xl font-semibold tracking-wide text-center text-gray-800 capitalize md:text-3xl dark:text-white">
                            {{form.nick[0]}}
                        </h1>
                    </div>
                    <div v-if="!data.mostrarLoader" class="items-center self-center">

                        <button type="button" @click="scrollToBottom"
                            class="w-22 hover:bg-green-500 item-center px-6 py-2 mt-4 mx-8 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-sky-800 rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Ir al final ↓
                        </button>
                        <Link :href="route('Articulo.index')"
                        class="w-22 hover:bg-gray-600 item-center px-6 py-2 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-black rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Regresar
                        </Link>
                    </div>

                    <!--IA-->
                    <div v-if="data.CalifiActual['IA'] === -1" class="text-center inline-flex gap-4 mx-auto">
                        <label for="" class="mx-6 text-center">La IA revisará el articulo y pondrá una nota a este articulo</label>
                        <GreenButton
                            :class="{ 'opacity-25': data.mostrarLoader }" :disabled="data.mostrarLoader"
                            @click="CalificarArticulo()"
                            class="self-center text-center ml-3 px-10 py-2 outline outline-offset-2 ring-2 ring-green-700">
                            {{ data.mostrarLoader ? 'Calificando...' : 'Calificar Con IA' }}
                        </GreenButton>
                    </div>
                    <div v-else class="">
                        <div class="bg-white pb-1 sm:pb-8 lg:pb-2">
                            <div class="relative flex flex-wrap bg-gray-100 px-4 py-3 sm:flex-nowrap sm:items-center sm:justify-center sm:gap-3 sm:pr-8 md:px-8">
                                <div class="order-1 mb-2 inline-block w-11/12 max-w-screen-sm text-sm text-black sm:order-none sm:mb-0 sm:w-auto md:text-base">
                                    Nota de la IA:
                                </div>
                                <a href="#" class="order-last inline-block w-full whitespace-nowrap rounded-lg bg-indigo-600 px-4 py-2 text-center text-xs font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-700 focus-visible:ring active:bg-indigo-800 sm:order-none sm:w-auto md:text-sm">
                                    {{ data.CalifiActual['IA'] }}
                                </a>
                            </div>
                            <div v-if="data.CalifiActual['ModelCalificacionIA']" class="relative flex flex-wrap bg-gray-100 px-4 py-3 sm:flex-nowrap sm:items-center sm:justify-center sm:gap-3 sm:pr-8 md:px-8">
                                <div class="order-1 mb-2 inline-block w-11/12 max-w-screen-sm text-sm text-black sm:order-none sm:mb-0 sm:w-auto md:text-base">
                                    {{ data.CalifiActual['ModelCalificacionIA']['argumentoIA'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--docente-->
                    <div v-if="data.CalifiActual['docente'] === -1" class="mx-auto self-center inline-flex gap-10">
                        <div v-if="!data.mostrarLoader" class="">
                            <div class="text-center mx-auto">
                                <label for="" class="mx-6 text-center">Coloque una nota a este articulo</label>
                                <input type="number" step="0.1" min="0" max="5" v-model="data.calificacionDocente"
                                       class="w-20 rounded border bg-gray-50 pl-5 py-1 text-gray-800 font-bold outline-none ring-indigo-300 transition duration-100 focus:ring">
                                <GreenButton v-if="data.calificacionDocente !== ''"
                                    :class="{ 'opacity-25': data.mostrarLoader }" :disabled="data.mostrarLoader"
                                    @click="CalificarArticulo(data.calificacionDocente)"
                                    class="ml-3 px-10 py-2 outline outline-offset-2 ring-2 ring-green-700">
                                        {{ data.mostrarLoader ? 'Calificando...' : 'Calificar Manualmente' }}
                                </GreenButton>
                            </div>
                        </div>
                    </div>
                    <div v-else class="">
                        <div class="bg-white pb-6 sm:pb-8 lg:pb-6">
                            <div class="relative flex flex-wrap bg-gray-100 px-4 py-3 sm:flex-nowrap sm:items-center sm:justify-center sm:gap-3 sm:pr-8 md:px-8">
                                <div class="order-1 mb-2 inline-block w-11/12 max-w-screen-sm text-sm text-black sm:order-none sm:mb-0 sm:w-auto md:text-base">
                                    Nota del docente:
                                </div>
                                <a href="#" class="order-last inline-block w-full whitespace-nowrap rounded-lg bg-indigo-600 px-4 py-2 text-center text-xs font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-700 focus-visible:ring active:bg-indigo-800 sm:order-none sm:w-auto md:text-sm">
                                    {{ data.CalifiActual['docente'] }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div v-if="data.mostrarLoader" class="mt-2"> <Generando /> </div>
                    <div v-if="data.errorCarrera[0]" class="flex items-center mt-2">
                        <p  class="text-red-500 dark:text-red-200 underline">
                            {{ data.errorCarrera[0] }}</p>
                    </div>

                    <div v-if="data.calificacionBackend !== ''" class="">
                        <label for="calificacion" class="text-gray-500 text-xl font-bold dark:text-gray-400 mb-2">
                            Calificación (IA)
                        </label>
                        <div v-if="data.calificacionBackend !== 'Se guardo la nota'" class="relative rounded-md shadow-sm select-none">
                            <div id="calificacion"
                                class="block w-full px-5 py-3 mt-2 text-black font-sans bg-white
                                    dark:bg-gray-200 dark:text-gray-800 dark:border-gray-700 focus:border-blue-400
                                    dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 text-justify" >
                                {{ data.calificacionBackend}}
                            </div>
                        </div>
                    </div>


                    <div v-for="(campo) in data.campos" :key="campo.id">
<!--                        aquiiiiii: asegurarse que esta guardando las correciones,
                            ademas no esta trayendo las correciones ya guardadas-->

                        <div >
                            <div class="">
                                <label :for="campo.id" class="rounded-2xl px-10 text-gray-500 text-xl font-bold dark:text-gray-400 shadow-sm bg-gradient-to-r from-gray-50 via-gray-100 to-sky-100 mb-2">
                                    {{ campo.etiqueta }}
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <textarea :id="campo.id" @focus="data.campoActivo = campo.id" rows="4" cols="33"
                                      v-model="form[campo.id][0]"
                                      :disabled="!(campo.id).endsWith('correcciones')"
                                      :class="{ 'opacity-75 bg-gray-200': !(campo.id).endsWith('correcciones') }"
                                      class="block w-full px-5 py-3 mt-2 border rounded-xl
                                        placeholder-gray-400      text-gray-700      border-gray-200
                                        dark:placeholder-gray-600 dark:text-gray-300 dark:border-gray-700
                                        focus:border-blue-400"
                                    />
                                </div>
                            </div>
                        </div>

                        <hr class="border-2 border-sky-100 my-8">
                    </div>

                    <div class="flex gap-12 text-center">
                        <button @click="update"
                            class="w-1/3 item-center px-6 py-3 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-sky-500 rounded-lg hover:bg-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Actualizar
                        </button>
                        <button type="button" @click="scrollToTop"
                            class="w-1/3 hover:bg-green-500 item-center px-6 py-3 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-sky-800 rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Ir al Inicio</button>

                        <Link :href="route('Articulo.index')"
                            class="w-1/3 item-center px-6 py-3 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-black rounded-lg hover:bg-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                                Regresar
                        </Link>
                    </div>
                </div>
            </form>
        </div>
        <div class="flex-none w-14"> . </div>
    </section>

</template>
