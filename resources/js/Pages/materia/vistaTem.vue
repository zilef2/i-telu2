<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import SelectInput from '@/Components/SelectInput.vue';

import { reactive, watch, ref, watchEffect, onMounted } from 'vue';
import pkg from 'lodash';
import { router, useForm, Link } from '@inertiajs/vue3';

import vSelect from "vue-select"; import "vue-select/dist/vue-select.css";

// ChevronUpDownIcon, QuestionMarkCircleIcon, EyeIcon, PencilIcon, TrashIcon, UserCircleIcon 
import { CursorArrowRippleIcon } from '@heroicons/vue/24/solid';

import { sinTildes, ReemplazarTildes, textoSinEspaciosLargos } from '@/global.ts';;
import zonalecciones from '@/Pages/materia/zonalecciones.vue';
import VersionEstudiante from '@/Pages/materia/versionEstudiante.vue';

// import { Configuration, OpenAIApi } from "openai";
// const apiKeyZilef = import.meta.env.VITE_GTP_SELECT;
// const headersListos = {
//     'Content-Type': 'application/json',
//     'Authorization': 'Bearer YOUR_API_KEY',
// };
// const configuration = new Configuration({
//     organization: "org-uJVv7YV1xxr1mouu8o2gOjMp",
//     apiKey: apiKeyZilef,
// });
// const openai = new OpenAIApi(configuration);
// const response = await openai.listEngines();
//fin NPM openAI


const { _, debounce, pickBy } = pkg
const props = defineProps({
    elid: Number,
    objetivosCarrera: String,

    title: String,
    breadcrumbs: Object,

    usuario: Object,
    fromController: Object, //unidades
    temaSelec: String,
    subtopicoSelec: Object,//subtema(segun datos generales: Unidad)
    ejercicioSelec: String,


    respuesta: String, // respuesta de gpt
    limite: Number,
    restarAlToken: Number,

    soloEjercicios: Array,
    materia: Object,
    ChosenNivel: String,
    nivelSelect: Object,
    opcion: Number,

    selectedPrompID: Number, // is in data.params
    ListaPromp: Object,
    selectedReasonString: String,


    numberPermissions: Number,

    //practica
    vectorChuleta: Array,
    ArrayPreguntas: Array,
    ArrayRespuestasCorrectas: Array,
    
    //validada por profesor
    notvalidbyteacher: Boolean,
})

const data = reactive({
    params: {
        materiaid: props.materia.id,
        pregunta: ''
    },
    tipoResSelect: '',
    selectedPrompID: props.selectedPrompID,
    temaIDSelected: null,
    temaSelectedName: '',
    subtopSelected: 0,
    temaReal: null,
    SubTopicoIDSelected: null,
    ListaPromp: [],
    RespondioBien: [],
})

onMounted(() => {
    data.nivel = 3
    // data.tipoResSelect = [{ label: 'Practica', value: 'practica' }, { label: 'Teorica', value: 'teorica' }]
    data.tipoResSelect = [{ label: 'Teorica', value: 'teorica' }]
    data.ListaPromp = props.ListaPromp;

    if (props.ArrayPreguntas)
        props.ArrayPreguntas.forEach((value, index) => {
            data.RespondioBien[index] = 0
        })
})

// chatgpt form
const form = useForm({
    nivel: 1,
    pregunta: '', //para la zona libre
    respuestagpt: props.respuesta,
    tipoRes: 'teorica',
    materiaid: 0,
    temaSelec: 0,
    subtopicoSelec: 0,
    respuesta1: '',
    actionEQH: 0,
    
});

watchEffect(() => {
    if (props.ListaPromp) {

        if (data.selectedPrompID == null || typeof (data.selectedPrompID) === 'number') data.selectedPrompID = data.ListaPromp[0]
        data.ListaPromp = props.ListaPromp.filter(item => {
            return item.tipo == form.tipoRes || item.tipo == 'General'
        });
    }
});

watch(() => form.tipoRes, (newX) => {
    data.selectedPrompID = 'Selecciona un promp'
})

const paAbajo = () => { window.scrollTo(0, document.body.scrollHeight); }

const submitToGPT = (ejercicioID) => { //not in leccion estudiante
    form.get(route('materia.VistaTema', [props.elid, ejercicioID, form.nivel, null, null]), {
        preserveScroll: true,
        onSuccess: () => paAbajo(),
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null
    })
}

//here can stay  simplificar, ejemplos, quiz o begin lesson JUST STUDENT!!!
const CallStudent = (subtopicoid) => {
    form.get(route('materia.VistaTema', [props.elid, 0, 1, subtopicoid, null]), {
        preserveScroll: true,
        onSuccess: () => paAbajo(),
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null
    })
}


const Paso1PreguntarTema = (subtemaid) => { //boton comenzar leccion
    let TempselectedPrompID = data.selectedPrompID['value']
    if (data.nivel && form.tipoRes && subtemaid) {

        form.get(route('materia.VistaTema', [props.elid, 'explicar', data.nivel, subtemaid, TempselectedPrompID]), {
            preserveScroll: true,
            onSuccess: () => paAbajo(),
            onError: () => null,
            onFinish: () => null
        })
    }
}
const Paso2Ejercicio = (subtemaid) => { //todo2: where is it used???
    let TempselectedPrompID = data.selectedPrompID['value']
    if (TempselectedPrompID != 0 && form.nivel && form.tipoRes && subtemaid) {
    // if ( form.nivel && form.tipoRes && subtemaid) {
        alert('Falta info')
    }
        form.get(route('materia.VistaTema', [props.elid, 'practicar', form.nivel, subtemaid, TempselectedPrompID]), {
            preserveScroll: true,
            onSuccess: () => {
                paAbajo();
            },
            onError: () => null,
            onFinish: () => null
        })
    // } 
}

const respuestaChuleta = (opcionRespuesta, correcta, NumPregunta) => {
    correcta = correcta.trim().toLowerCase().substr(-1)
    if (correcta == 'a') correcta = 2
    if (correcta == 'b') correcta = 3
    if (correcta == 'c') correcta = 4
    if (correcta == 'd') correcta = 5
    if (data.RespondioBien[NumPregunta] == 0)
        data.RespondioBien[NumPregunta] = opcionRespuesta == correcta ? 2 : 1
}

const submitGPTEQH = (action) => {
    form.actionEQH = action

    form.materiaid = props.elid;
    form.temaSelec = props.temaSelec;
    form.subtopicoSelec = props.subtopicoSelec;
    form.respuesta1 = props.respuesta;

    // if(data.selectedPrompID){
    //     if(data.selectedPrompID.value == 0)
        // form.fakepermission = 'estudiante'
    // }

    form.post(route('materia.actionEQH'), {
        preserveScroll: true,
        onSuccess: () => null,
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null
    })
}

</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>

        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div v-if="props.numberPermissions > 1" class="">
            <div v-if="fromController.length != 0" class="">
                <div class="space-y-4">
                    <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="container px-5 py-2 mx-auto">
                            <div class="flex flex-col text-center w-full mb-1">
                                <h2
                                    class="block mx-auto text-lg text-gray-900 dark:text-white tracking-widest font-medium title-font mb-1">
                                    Recuerde utilizar los tokens restantes con precaución
                                </h2>
                                <!-- <h3 class="block mx-auto text-md text-gray-700">Nivel: <b>{{ props.usuario.pgrado }}</b></h3> -->
                                <h3 class="block mx-auto text-lg text-gray-900 dark:text-white">Materia: <b>{{
                                    props.materia.nombre }}</b></h3>

                                <p class="text-sky-600 dark:text-white"><b class="text-sky-500">{{ props.limite }}</b> token disponibles</p>
                            </div>
                        </div>


                        <div class="grid grid-cols-2 gap-6 mx-16 py-1">
                            <div class="">
                                <p class="mt-8">Seleccione el nivel de la respuesta</p>
                                <SelectInput v-model="data.nivel" :dataSet="props.nivelSelect"
                                    class="mt-1 mb-4 block w-full" />
                            </div>
                            <div class="">
                                <p class="mt-8">Seleccione el tipo de respuesta</p>
                                <SelectInput v-model="form.tipoRes" :dataSet="data.tipoResSelect"
                                    class="mt-1 mb-4 block w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-6 mx-16 py-1">
                            <div class="">
                                <div id="SelectVue">
                                    <label name="labelSelectVue">Seleccione la instruccion para la respuesta</label>
                                    <v-select :options="data.ListaPromp" label="title"
                                        v-model="data.selectedPrompID"></v-select>
                                </div>
                                <!-- <SelectInput v-model="data.selectedPrompID" :dataSet="data.ListaPromp" class="mt-1 mb-4 block w-full" /> -->
                            </div>
                        </div>


                        <!-- temas y subtemas -->
                        <section class="text-gray-600 body-font">
                            <div class="container px-5 py-8 mx-auto">
                                <div class="flex flex-wrap -m-4">
                                    <div v-for="(clasegenerica, index) in fromController" :key="index" class="p-4 w-full sm:w-1/2 lg:w-1/3"
                                        :class="clasegenerica.id == data.temaIDSelected ? 'bg-blue-100 dark:bg-gray-200' : ''">
                                        <div
                                            class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">

                                            <div class="px-4 py-1 my-2">
                                                <h2
                                                    class="tracking-widest text-md title-font font-medium text-gray-400 mb-1 pb-1">
                                                    {{ clasegenerica.nombre }}
                                                </h2>
                                                <!-- <p class="leading-relaxed mb-3">{{ clasegenerica.descripcion }}</p> -->

                                                <div v-if="clasegenerica.sub.length" class=" mt-4">
                                                    <div v-for="(subtopicos, Jindex) in clasegenerica.sub" :key="Jindex"
                                                        class="grid grid-cols-2 gap-2 items-center">
                                                        <!-- <input id="toggle" type="checkbox"
                                                            class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"> -->
                                                        <VTooltip>
                                                            <button
                                                                @click="(
                                                                    data.temaReal = clasegenerica,
                                                                    data.temaIDSelected = clasegenerica.id,
                                                                    data.temaSelectedName = subtopicos.nombre,
                                                                    data.subtopSelected = Jindex,
                                                                    data.SubTopicoIDSelected = subtopicos.id,
                                                                    paAbajo()
                                                                )"
                                                                for="toggle1"
                                                                class="underline text-center text-sky-600 ml-2 hover:text-sky-300">
                                                                {{ subtopicos.nombre }}
                                                            </button>
                                                            <template #popper>
                                                                ¡Quiz!
                                                            </template>
                                                        </VTooltip>
                                                        <button v-if="form.tipoRes == 'teorica'"
                                                            @click="Paso1PreguntarTema(subtopicos.id)"
                                                            class="mx-auto text-white bg-indigo-500 border-0 px-0.5 my-3 focus:outline-none hover:bg-indigo-900 rounded text-lg">
                                                            {{ form.processing ? '...' : 'Leccion' }}
                                                        </button>
                                                        <button v-else @click="Paso2Ejercicio(subtopicos.id)"
                                                            class="mx-auto text-white bg-indigo-500 border-0 px-0.5 my-3 focus:outline-none hover:bg-indigo-900 rounded text-lg">
                                                            {{ form.processing ? '...' : 'Practicar' }}
                                                        </button>
                                                    </div>

                                                </div>
                                                <div v-else class="flex flex-col">
                                                    <div class="flex items-center">
                                                        <p class="text-gray-300 text-sm">¡Sin subtopicos!</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-8">
                                    <Link :href="route('materia.index')"
                                        class="text-center my-4 border border-sky-700  bg-black text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-600 focus:outline-none focus:shadow-outline">
                                        Regresar
                                    </Link>
                                </div>
                            </div>
                        </section>



                        <zonalecciones :temaIDSelected="data.temaIDSelected" :ejercicio="data.ejercicio"
                            :subtopSelected="data.subtopSelected" :temaSelectedName="data.temaSelectedName"
                            :Unidad="data.temaReal" :nivelSelect="props.nivelSelect" @submitGPT="(n) => submitToGPT(n)" />

                    </div>
                </div>
                <div class="p-2 w-full">
                    <button
                        class="flex mx-auto text-sky-800 bg-indigo-200 border-0 py-1 px-8 focus:outline-none rounded text-lg">
                        {{ form.processing ? 'Por favor, espere...' : '' }}
                    </button>
                </div>




                <section v-if="props.option != 4" class="">

                    <div v-if="props.respuesta !== 'Limite de tokens'">
                        <div v-if="props.respuesta != ''">
                            <section v-if="props.limite > -1 && !form.processing" class="body-font relative">
                                <div class="container px-5 pt-6 mx-auto">
                                    <div class="flex flex-col text-center w-full mt-4">
                                        <h1
                                            class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900 dark:text-white">
                                            {{ temaSelec }}</h1>
                                        <p class="lg:w-2/3 mx-auto leading-relaxed text-xl text-gray-600 dark:text-white">
                                            {{ subtopicoSelec.nombre }}
                                        </p>
                                        <!-- <p v-if="props.ChosenNivel"
                                            class="lg:w-2/3 mx-auto leading-relaxed text-black dark:text-white underline">
                                            Nivel seleccionado: {{ props.ChosenNivel }}
                                        </p> -->
                                        <p v-if="props.ChosenNivel"
                                            class="lg:w-2/3 mx-auto leading-relaxed text-black dark:text-white">
                                            Tokens consumidos {{ props.restarAlToken }}
                                        </p>
                                    </div>
                                    <div v-if="props.opcion == 2 || props.opcion == 3" class="w-full mt-6 mx-auto">
                                        <form @submit.prevent="submitFormGPT">
                                            <div class="flex flex-wrap -m-2">
                                                <div class="p-2 w-1/2">
                                                    <div v-if="form.respuestagpt != 'Unidad no seleccionado'"
                                                        class="relative">
                                                        <label for="name"
                                                            class="leading-7 dark:text-white text-sm text-gray-600">Objetivo</label>
                                                        <p v-if="Object.keys(props.objetivosCarrera).length !== 0"
                                                            class="dark:text-white w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-800 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                            {{ props.objetivosCarrera }}
                                                        </p>
                                                        <p v-else
                                                            class="dark:text-white w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-800 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                            La asignatura no tiene objetivos
                                                        </p>
                                                        <!-- <SelectInput v-model="nivel" :dataSet="props.objetivosCarrera" class="mt-1 block w-full" /> -->
                                                    </div>
                                                </div>
                                                <div v-if="props.opcion == 2" class="p-2 w-1/2">
                                                    <div class="relative">
                                                        <label for="pregunta" class="dark:text-white leading-7 text-sm text-gray-600">
                                                            Resultado de aprendizaje
                                                        </label>
                                                        <p
                                                            class="dark:text-white w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                            {{ subtopicoSelec.resultado_aprendizaje != null ?
                                                                subtopicoSelec.resultado_aprendizaje : 'Sin resultado' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div v-if="props.opcion == 2" class="p-2 w-full">
                                                    <div class="relative">
                                                        <label for="Instruccion"
                                                            class="dark:text-white leading-7 text-sm text-gray-600">Instruccion</label>
                                                        <p
                                                            class="dark:text-white w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                            {{ props.selectedReasonString }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- *******************   el textarea con la respuesta *********************-->
                                                <div v-if="props.opcion == 3" class="p-2 w-1/2">
                                                    <div class="relative">
                                                        <label for="pregunta"
                                                            class="leading-7 text-sm text-gray-600 dark:text-white">Pregunta</label>
                                                        <p
                                                            class="dark:text-gray-700 w-full dark:bg-gray-300 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                            {{ props.ejercicioSelec }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div v-if="props.opcion != 4" class="p-2 w-full h-full">
                                                    <div class="relative h-full">
                                                        <label for="message"
                                                            class="leading-7 text-sm text-gray-600">Respuesta 
                                                        </label>
                                                        <textarea v-model="form.respuestagpt" id="message" name="message"
                                                            rows="20" cols="45"
                                                            class="dark:text-white dark:bg-black h-auto resize-none w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 dark:focus:bg-gray-800 focus:bg-white focus:ring-2 focus:ring-indigo-200 
                                                            text-base outline-none text-gray-700 py-1 px-3 leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="w-full flex mt-6 mx-auto">
                                                <div class="w-full mt-1 mx-auto">

                                                    <div class="w-full flex items-center justify-center dark:bg-gray-800 bg-gray-100">
                                                        <div class="w-full mx-auto py-6">
                                                            <h1 class="text-xl text-center font-bold mb-6"> Aprender más </h1>
                                                            <div
                                                                class="bg-white dark:bg-gray-600 px-6 py-4 my-3 w-3/4 mx-auto shadow text-center rounded-md items-center">
                                                                <div class="w-full text-center mx-auto">
                                                                    <button type="button" @click="submitGPTEQH(4)" 
                                                                        class="border border-sky-500 bg-sky-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-blue-300 focus:outline-none focus:shadow-outline">
                                                                        Simplificar
                                                                    </button>
                                                                    <button type="button" @click="submitGPTEQH(1)"
                                                                        class="border border-green-500 bg-green-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-green-600 focus:outline-none focus:shadow-outline">
                                                                        Ejemplos
                                                                    </button>
                                                                    <button type="button" @click="submitGPTEQH(2)"
                                                                        class="border border-yellow-500 bg-yellow-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-yellow-600 focus:outline-none focus:shadow-outline">
                                                                        Quiz
                                                                    </button>
                                                                    <!-- <button type="button" @click="submitGPTEQH(3)"
                                                                        class="border border-teal-500 bg-teal-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-teal-600 focus:outline-none focus:shadow-outline">
                                                                        Hacer una pregunta
                                                                    </button> -->
                                                                </div>
                                                                <div class="w-full text-center my-3">
                                                                    <Link :href="route('materia.index')"
                                                                        class="my-4 border text-left border-sky-700  bg-black text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-600 focus:outline-none focus:shadow-outline">
                                                                        Regresar
                                                                    </Link>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div v-else class="w-full flex item-center mt-10">
                            <p class="text-red-400 mx-auto text-xl">{{ props.respuesta }}</p>
                        </div>
                    </div>
                    <div v-else class="w-full flex item-center mt-10">
                        <p class="text-red-500 mx-auto text-xl">{{ props.respuesta }}</p>
                    </div>
                </section>

                <div v-if="props.opcion == 4" class="w-full mt-6 mx-auto">
                    <form @submit.prevent="respuestaChuleta">
                        <div class="flex flex-wrap -m-2">
                            <h3 class="text-center">Tokens utilizados {{ props.restarAlToken }}</h3>
                            <div class="p-2 w-full grid grid-cols-1 md:grid-cols-2 gap-2 border-t-2 border-gray-600/20">
                                <ul v-for="(SelecionMultiple, index) in props.ArrayPreguntas"
                                    class="list-item border-x-inherit border-black">

                                    <!-- <label for="name" class="leading-7 dark:text-white text-sm text-gray-600">{{ props.vectorChuleta[0] }}</label> -->
                                    <li v-for="(posibleRespuesta, jindex) in SelecionMultiple">
                                        <p @click="respuestaChuleta(jindex, props.vectorChuleta[props.ArrayRespuestasCorrectas[index]], index)"
                                            class="dark:text-white w-full bg-gray-100 bg-opacity-50 cursor-pointer focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-800 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                                            :class="{ 'hover:bg-green-200': jindex != 0 && jindex != 1 }, { 'font-semibold text-lg': jindex == 1 }">
                                            {{ props.vectorChuleta[posibleRespuesta] }}
                                            ~{{ jindex }} ~
                                        </p>
                                    </li>
                                    <p v-if="data.RespondioBien[index] == 1"
                                        class="dark:text-white w-full bg-gray-100 bg-opacity-50 hover:bg-green-200 cursor-pointer focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-800 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                        {{ props.vectorChuleta[props.ArrayRespuestasCorrectas[index]] }}
                                    </p>
                                    <!-- <p> ~{{data.RespondioBien}} ~ </p> -->
                                    <!-- <p> ~{{props.vectorChuleta[props.ArrayRespuestasCorrectas[index]]}} ~ </p> -->
                                    <p v-if="data.RespondioBien[index] == 2"
                                        class="text-lg text-green-500 bg-green-50 text-center">Correcto!</p>
                                    <p v-if="data.RespondioBien[index] == 1"
                                        class="text-lg text-red-500 bg-red-50 text-center">Incorrecto!</p>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div v-else class="">
                <section class="text-gray-600 body-font">
                    <div class="container px-5 py-4 mx-auto">
                        <div class="flex flex-col text-center w-full mb-20">
                            <h2 class="text-xl text-indigo-500 tracking-widest font-medium title-font mb-1">Sin unidades
                            </h2>
                            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                                La materia seleccionada no tiene unidades...
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>



        <div v-if="props.numberPermissions == 1" class="">

            <VersionEstudiante :fromController="props.fromController" :respuesta="props.respuesta"
                :temaSelec="props.temaSelec" :subtopicoSelec="props.subtopicoSelec" :usuario="props.usuario"
                :materia="props.materia" :numberPermissions="props.numberPermissions" :restarAlToken="props.restarAlToken"
                :notvalidbyteacher="props.notvalidbyteacher"
                :limite="props.limite" @EstudianteToGPT="(subtopicoid) => CallStudent(subtopicoid)" />
        </div>

    </AuthenticatedLayout>
</template>
<style>
    textarea {
        @apply px-3 py-2 border border-gray-300 rounded-md;
    }

    [name="labelSelectVue"],
    .muted {
        color: #1b416699;
    }

    [name="labelSelectVue"] {
        font-size: 22px;
        font-weight: 600;
    }
</style>