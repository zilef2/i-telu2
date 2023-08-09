<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { reactive, watch, ref, watchEffect, onMounted } from 'vue';
import pkg from 'lodash';
import { router, useForm, Link, Head } from '@inertiajs/vue3';

import { ReemplazarTildes, textoSinEspaciosLargos }from '@/global.ts';;

const { _, debounce, pickBy } = pkg
const props = defineProps({
    breadcrumbs: Object,
    title: String,
    materia: Object,

    temaSelec: Object,
    subtopicoSelec: Object,
    respuesta1: String,
    limite: Number,

    //EQH
    actionEQH: Number,
    ejemplosRespuesta: String,
    restarAlToken: Number,


    quiz: Boolean,
    quizPregunta: String,
    quizCorrecta: Number,
    quizRespuestas: Array,

    hagapregunta: Boolean,
    HacerlaPregunta: String,
    RespuestaPregunta: String,
    RespuestaUnicaCorrecta: String,
})

//todo: poner un link para devolverse a la materia seleccionada

const data = reactive({
    params: {
        actionEQH: props.actionEQH,
        temaSelec: {},
        subtopicoSelec: {},
        respuesta1: '',
        actionEQH: '',
        //E
        ejemplosRespuesta: '',
        //Q
        // quiz: false,
        // quizPregunta: '',
        // quizCorrecta: '',
        // quizRespuestas: [],
        //H
        hagapregunta: '',
        HacerlaPregunta: '',
        materiaid: '',
        Yarespondio: false,
    },

    // HacerlaPregunta: '',
    chosenRespuesta: -1,
    IntegerRespondioCorrectamente: 0,
    // tipoResSelect: '',
})

const PreguntarGPT = (actionEQH) => {
    if(actionEQH == 2) {
        data.IntegerRespondioCorrectamente = 0
        data.Yarespondio = false
    }
    _.cloneDeep(data.params)
    // watch(() => _.cloneDeep(data.params), debounce(() => {
    data.params.actionEQH = actionEQH
    data.params.temaSelec = props.temaSelec
    data.params.subtopicoSelec = props.subtopicoSelec
    data.params.ejemplosRespuesta = props.ejemplosRespuesta;
    data.params.respuesta1 = props.respuesta1;
    data.params.materiaid = props.materia.id;

    // console.log(data.params.materiaid)
    //quiz
    if (data.params.actionEQH == 3) data.params.HacerlaPregunta = ReemplazarTildes(data.params.HacerlaPregunta)

    let params = pickBy(data.params)
    router.post(route("materia.actionEQH"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}

watch(() => data.chosenRespuesta, (newX) => {
    if (data.IntegerRespondioCorrectamente === 0) {
        console.log("🧈 debu props.quizCorrecta:", props.quizCorrecta);
        console.log("🧈 debu data.chosenRespuesta:", data.chosenRespuesta);
        data.IntegerRespondioCorrectamente = props.quizCorrecta == data.chosenRespuesta ? 2 : 1;
        data.Yarespondio = true
    }else{
        //no haga trampa
    }
})

//not using
watchEffect(() => { });
onMounted(() => { 

 })
const form = useForm({ });



const paAbajo = () => { window.scrollTo(0, document.body.scrollHeight); }

</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div v-if="props.limite > 0">
            <section v-if="props.limite > -1 && !form.processing" class="body-font relative">
                <div class="container px-5 pt-2 mx-auto">
                    <Link :href="route('materia.VistaTema', props.materia.id)"
                        class="my-4 border text-left border-sky-700  bg-black text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-600 focus:outline-none focus:shadow-outline">
                        Regresar
                    </Link>
                    <div class="flex flex-col text-center w-full mt-2">
                        <div class="p-1">
                            <h1 class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900 dark:text-white">
                                {{ props.temaSelec.nombre }}</h1>
                            <p class="lg:w-2/3 mx-auto leading-relaxed text-xl text-gray-600 dark:text-white">
                                {{ props.subtopicoSelec.nombre }}
                            </p>
                        </div>


                        <!-- Ejemplos -->
                        <p class="mt-5 text-sm font-sans text-gray-400">Tokens consumidos: {{ props.restarAlToken }} </p>
                        <p class="mb-5 text-sm font-sans text-gray-400">Tokens restantes: {{ props.limite }}</p>
                        <div v-if="props.ejemplosRespuesta"
                            class="w-full mt-6 mx-auto border border-t-2 border-x-0 border-b-0 border-black">
                            <p class="my-5 text-lg font-sans font-extrabold">Ejemplos</p>
                            <div class="text-left">{{ props.ejemplosRespuesta }}</div>
                        </div>



                        <!-- Quiz -->
                        <div v-if="props.quiz " class="flex items-center justify-center p-2">
                            <div class="mx-auto w-full max-w-[550px]">
                                <form action="https://formbold.com/s/FORM_ID" method="POST">
                                    <div class="mb-5">
                                        <label class="mb-3 block text-lg font-medium text-gray-800">
                                            {{ props.quizPregunta }}
                                        </label>
                                        <div class="grid grid-cols-2 space-x-6">
                                            <div v-for="(respuesta, index) in quizRespuestas" :key="index"
                                                class="items-start">
                                                <div class="my-1">
                                                    <input v-if="!data.Yarespondio" :value="index" v-model="data.chosenRespuesta" type="radio"
                                                        name="respuestas" id="respuestasButton" class="h-5 w-5" />
                                                    <label for="radioButton1"
                                                        class="pl-3 text-base font-medium text-gray-800">
                                                        {{ respuesta }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div v-if="data.IntegerRespondioCorrectamente != 0" class='m-12 space-y-6'>
                                    <div v-if="data.IntegerRespondioCorrectamente == 2" class="bg-green-100 border-t border-b border-green-500 text-green-700 px-4 py-3"
                                        role="alert">
                                        <p class="font-bold">¡Correcto!</p>
                                    </div>
                                    <div v-else class="bg-red-100 border-t border-b border-red-500 text-red-700 px-4 py-3"
                                        role="alert">
                                        <p class="font-bold">¡Incorrecto!</p>
                                    </div>
                                    
                                    <p class="text-sm">
                                        {{ props.RespuestaUnicaCorrecta }}
                                    </p>
                                </div>
                            </div>
                        </div>


                        <!-- Hacer Pregunta -->
                        <div v-if="props.hagapregunta" class="w-full mt-6 mx-auto border border-t-2 border-x-0 border-b-0 border-black">
                            <p class="my-5 text-lg font-sans font-extrabold text-left">{{ props.HacerlaPregunta }}</p>
                                <p class="text-left">{{ props.RespuestaPregunta }}</p>
                        </div>



                    </div>
                    <div class="w-full mt-6 mx-auto border border-t-2 border-x-0 border-b-0 border-black">
                        <p class="my-5 text-lg font-sans font-extrabold">Explicacion inicial</p>
                        <p class="text-left">{{ props.respuesta1 }}</p>
                        <div class="w-full mt-1 mx-auto">

                            <div class="w-full flex items-center justify-center bg-gray-100">
                                <div class="w-full mx-auto py-6">
                                    <h1 class="text-xl text-center font-medium mb-6"> Aprender más </h1>
                                    <div class="bg-white px-6 py-4 my-3 w-3/4 mx-auto shadow rounded-md flex items-center">
                                        <div class="w-full text-center mx-auto">
                                            <button type="button" @click="submitGPTEQH(4)"
                                                class="border border-sky-500 bg-sky-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-blue-300 focus:outline-none focus:shadow-outline">
                                                Simplificar
                                            </button>
                                            <button type="button" @click="PreguntarGPT(1)"
                                                class="border border-green-500 bg-green-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-green-600 focus:outline-none focus:shadow-outline">
                                                Examples
                                            </button>
                                            <button type="button" @click="PreguntarGPT(2)"
                                                class="border border-yellow-500 bg-yellow-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-yellow-600 focus:outline-none focus:shadow-outline">
                                                Quiz
                                            </button>
                                            <!-- <button type="button" @click="data.params.actionEQH = 3"
                                                class="border border-teal-500 bg-teal-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-teal-600 focus:outline-none focus:shadow-outline">
                                                Hacer una pregunta
                                            </button> -->
                                            
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-center p-12">
                                        <div class="mx-auto w-1/2">
                                            <div>
                                                <div class="-mx-3 flex flex-wrap">
                                                    <div class="px-3 sm:w-full">
                                                        <div class="mb-5">
                                                            <label for="fName" class="mb-3 block text-xl text-center font-medium text-sky-800">
                                                                ¿Alguna pregunta?
                                                            </label>
                                                            <p class="my-2 text-center">Es su responsabilidad las preguntas que va a desarrollar alrededor del tema.</p>
                                                            <input type="text" name="fName" id="fName"
                                                                placeholder="Pregunte algo relacionado al tema" v-model="data.params.HacerlaPregunta"
                                                                class="w-full rounded-md border border-gray-600 bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mx-5 text-center">
                                                    <button @click="PreguntarGPT(3)"
                                                        class="hover:shadow-form rounded-md bg-[#6A64F1] py-2 px-8 text-center text-base font-semibold text-white outline-none">
                                                        Preguntar
                                                    </button>
                                                    <Link :href="route('materia.VistaTema', props.materia.id)"
                                                        class="my-8  border text-left border-black  bg-black text-white rounded-md px-8 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-600 focus:outline-none focus:shadow-outline">
                                                        Regresar
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
            <div v-else class="w-full flex item-center mt-10">
                <p class="text-red-400 mx-auto text-xl">Limite de tokens</p>
            </div>
        </div>
        <div v-else class="body-font relative">
            <div class="container px-5 pt-6 mx-auto">
                <div class="flex flex-col text-center w-full mt-4">
                    <h1 class="text-2xl font-medium title-font mb-4 text-red-600 ">
                        Limite de tokens
                    </h1>
                </div>
                <Link :href="route('materia.VistaTema', props.materia.id)"
                    class="my-4 border text-left border-sky-700  bg-black text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-600 focus:outline-none focus:shadow-outline">
                    Regresar
                </Link>
            </div>
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
}</style>