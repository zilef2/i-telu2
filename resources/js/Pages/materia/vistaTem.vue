<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import SelectInput from '@/Components/SelectInput.vue';

import { reactive, watch, ref, watchEffect, onMounted } from 'vue';
import pkg from 'lodash';
import { router, useForm, Link } from '@inertiajs/vue3';

import { sinTildes, ReemplazarTildes, textoSinEspaciosLargos } from '@/global.js';
import zonalecciones from '@/Pages/materia/zonalecciones.vue';

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
    fromController: Object, //materia
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
})

const data = reactive({
    params: {
        materiaid: props.materia.id,
        
        pregunta: ''
    },
    selectedPrompID: props.selectedPrompID,
    temaIDSelected: null,
    temaSelectedName: '',
    subtopSelected: '',
    temaReal: null,
    SubTopicoIDSelected: null,
})
onMounted(() => {
    data.nivel = 3
})

// chatgpt form
const form = useForm({
    nivel: 1,
    pregunta: '',
    respuestagpt: props.respuesta
});

watchEffect(() => {

})

const paAbajo = () => {
    window.scrollTo(0, document.body.scrollHeight);
}

//todo: examples, quiz, ask a question
const submitToGPT = (ejercicioID) => {
    form.get(route('materia.VistaTema', [props.elid, ejercicioID, form.nivel, null,null]), {
        preserveScroll: true,
        onSuccess: () => { },
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null,
    })
}

// const Paso2PreguntarTema = (ejercicioID) => {
const Paso1PreguntarTema = (subtemaid) => { //y traer su introduccion
    if(data.selectedPrompID && form.nivel && subtemaid){
        form.get(route('materia.VistaTema', [props.elid, 'explicar', form.nivel, subtemaid,data.selectedPrompID]), {
            preserveScroll: true,
            onSuccess: () => { },
            onError: () => null,
            onFinish: () => null,
        })
    }else{
        alert('Falta informacion')
    }
}

const IrPreguntas = (pregunta) => {
    pregunta = sinTildes(ReemplazarTildes(pregunta))
    data.params.pregunta = pregunta

    let params = pickBy(data.params)

    router.get(route('materia.masPreguntas', params, {
        onSuccess: () => null,
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null,
    }))

}

// fin chatgpt form

</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>

        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
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
                            <h3 class="block mx-auto text-lg text-gray-900 dark:text-white">Materia: <b>{{ props.materia.nombre }}</b></h3>

                            <p class="text-sky-600 dark:text-white"><b class="text-sky-500">{{ props.limite }}</b> token disponibles</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 mx-16 py-1">
                        <div class="">
                            <p class="mt-8">Seleccione la instruccion para la respuesta</p>
                            <SelectInput v-model="data.selectedPrompID" :dataSet="props.ListaPromp" class="mt-1 mb-4 block w-full" />
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-6 mx-16 py-1">
                        <div class="">
                            <p class="mt-8">Seleccione el nivel de la respuesta</p>
                            <SelectInput v-model="data.nivel" :dataSet="props.nivelSelect" class="mt-1 mb-4 block w-full" />
                        </div>
                    </div>


                    <!-- temas y subtemas -->
                    <section class="text-gray-600 body-font">
                        <div class="container px-5 py-8 mx-auto">
                            <div class="flex flex-wrap -m-4">
                                <div v-for="(clasegenerica, index) in fromController" :key="index" class="p-4 md:w-1/3"
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
                                                        <button @click="(
                                                            data.temaReal = clasegenerica,
                                                            data.temaIDSelected = clasegenerica.id,
                                                            data.temaSelectedName = subtopicos.nombre,
                                                            data.subtopSelected = Jindex,
                                                            data.SubTopicoIDSelected = subtopicos.id,
                                                            paAbajo()
                                                        )" for="toggle1"
                                                            class="underline text-center text-sky-600 ml-2 hover:text-sky-300">
                                                            {{ subtopicos.nombre }}
                                                        </button>
                                                        <template #popper>
                                                            ¡Quiz!
                                                        </template>
                                                    </VTooltip>
                                                    <button @click="Paso1PreguntarTema(subtopicos.id)"
                                                        class="mx-auto text-white bg-indigo-500 border-0 px-0.5 my-3 focus:outline-none hover:bg-indigo-900 rounded text-lg">
                                                        {{ form.processing ? 'Por favor, espere...' : 'Comenzar leccion' }}
                                                    </button>

                                                </div>

                                            </div>
                                            <div v-else class="flex flex-col">
                                                <div class="flex items-center">
                                                    <p class="text-red-500">Sin subtopicos!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                    class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                    {{ form.processing ? 'Por favor, espere...' : '' }}
                </button>
            </div>






            <!-- button esperee -->

            <div v-if="props.respuesta !== 'Limite de tokens'">
                <div v-if="props.respuesta != ''">
                    <section v-if="props.limite > -1 && !form.processing" class="body-font relative">
                        <div class="container px-5 pt-6 mx-auto">
                            <div class="flex flex-col text-center w-full mt-4">
                                <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900 dark:text-white">
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
                            <div class="w-full mt-6 mx-auto">
                                <form @submit.prevent="submitFormGPT">
                                    <div class="flex flex-wrap -m-2">
                                        <div class="p-2 w-1/2">
                                            <div v-if="form.respuestagpt != 'Unidad no seleccionado'" class="relative">
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
                                                <label for="pregunta"
                                                    class="dark:text-white leading-7 text-sm text-gray-600">Resultado de aprendizaje</label>
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
                                                <label for="pregunta" class="leading-7 text-sm text-gray-600 dark:text-white">Pregunta</label>
                                                <p
                                                    class="dark:text-gray-700 w-full dark:bg-gray-300 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                    {{ props.ejercicioSelec }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="p-2 w-full h-full">
                                            <div class="relative h-full">
                                                <label for="message" class="leading-7 text-sm text-gray-600">Respuesta </label>
                                                <textarea v-model="form.respuestagpt" id="message" name="message" rows="10" cols="35"
                                                    class="dark:text-white dark:bg-black h-auto resize-none w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 dark:focus:bg-gray-800 focus:bg-white focus:ring-2 focus:ring-indigo-200 
                                                    text-base outline-none text-gray-700 py-1 px-3 leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                            </div>
                                        </div>
                                        <div v-if="props.soloEjercicios != 'Sin sugerencias' && props.soloEjercicios != ''"
                                            class="p-2 w-full h-full">
                                            <label for="message" class="leading-7 text-sm text-gray-600">Sugerencias:
                                            </label>
                                            <div v-for="(ejercicioExtra, Jindex) in props.soloEjercicios" :key="Jindex"
                                                class="relative w-full">

                                                <!-- :href="route('materia.masPreguntas', [id=>props.elid, pregunta=> sinTildes(ReemplazarTildes(ejercicioExtra))])" -->
                                                <Link @click="IrPreguntas(ejercicioExtra)" type="button"
                                                    class="w-full bg-gray-100 cursor-pointer bg-opacity-50 rounded-md border-b-2 border-indigo-400 focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <CursorArrowRippleIcon class="w-4 h-4" />
                                                {{ textoSinEspaciosLargos(ejercicioExtra) }}
                                                </Link>
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
        </div>
        <div v-else class="">
            <section class="text-gray-600 body-font">
                <div class="container px-5 py-4 mx-auto">
                    <div class="flex flex-col text-center w-full mb-20">
                        <h2 class="text-xl text-indigo-500 tracking-widest font-medium title-font mb-1">Sin unidades</h2>
                        <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                            La materia seleccionada no tiene unidades, por favor, asignele un Unidad para mostralo aquí...
                        </p>
                    </div>
                </div>
            </section>
        </div>

    </AuthenticatedLayout>
</template>
<style>
textarea {
    @apply px-3 py-2 border border-gray-300 rounded-md;
}</style>
