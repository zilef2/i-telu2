<script setup>
import { reactive, watch, ref, watchEffect, onMounted } from 'vue';
import { router, useForm, Link } from '@inertiajs/vue3';

import pkg from 'lodash';

import { vectorSelect, formatDate, CalcularEdad, CalcularSexo } from '@/global.ts';;

const { _, debounce, pickBy } = pkg
const props = defineProps({
    fromController: Object, //unidades
    filters: Object,

    respuesta: String, // respuesta de gpt
    temaSelec: String,
    subtopicoSelec: Object,//subtema
    usuario: Object,
    materia: Object,
    numberPermissions: Number,
    restarAlToken: Number,
    limite: Number,

    respuestaEQH: String,
    laRespuesta: String,
    notvalidbyteacher: Boolean,
})

const data = reactive({
    params: {
        // search: props.filters.search,
        actionEQH: 0,
        laRespuesta: '',
    },
    nivel: 1,
    pregunta: '',
    respuestagpt: '',
    nivelSelect: '',
})


const emit = defineEmits([
    "EstudianteToGPT",
    "formNext"
]);

const EstudianteGPT = (subtopicoid) => {
    emit("EstudianteToGPT", subtopicoid)
}
// const formNext = (accion) => {
//     emit("formNext", accion)
// }


const form = useForm({
    pregunta: '',
    respuestagpt: props.respuesta,
    temaSelec: '',
    subtopicoSelec: '',
    respuesta1: '',
    actionEQH: 0,
    materiaid: props.materia.id,
});

const submitGPTEQH = (action) => {
    form.actionEQH = action
    form.post(route('materia.actionEQH'), {
        preserveScroll: true,
        onSuccess: () => null,
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null
    })
}

onMounted(() => { })
watchEffect(() => {

    form.temaSelec = props.temaSelec
    form.subtopicoSelec = props.subtopicoSelec
    form.respuesta1 = props.respuesta
})

</script>
<template>
    <div>
        <!-- {{ props.users }} -->
        <div>
            <div class="space-y-4">
                <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="container px-5 py-2 mx-auto">
                        <div class="flex flex-col text-center w-full mb-1">
                            <h3 class="block mx-auto text-lg text-gray-900 dark:text-white">Materia: <b>
                                    {{ props.materia.nombre }}</b></h3>
                            <p class="text-sky-600 dark:text-white">
                                <b class="text-sky-500">{{ props.limite }}</b>
                                Token disponibles
                            </p>
                        </div>
                    </div>

                    <!-- temas y subtemas -->
                    <section class="text-gray-600 body-font">
                        <div class="container px-5 py-8 mx-auto">
                            <div class="flex flex-wrap -m-4">
                                <div v-for="(clasegenerica, index) in fromController" :key="index"
                                    class="p-4 w-full md:w-1/2 xl:1/3"
                                    :class="clasegenerica.id == data.temaIDSelected ? 'bg-blue-100 dark:bg-gray-200' : ''">
                                    <div
                                        class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                        <div class="px-4 py-1 my-2">
                                            <h2
                                                class="tracking-widest text-md title-font font-medium text-gray-400 mb-1 pb-1">
                                                {{ clasegenerica.nombre }}
                                            </h2>

                                            <div v-if="clasegenerica.sub.length" class=" mt-4">
                                                <div v-for="(subtopicos, Jindex) in clasegenerica.sub" :key="Jindex"
                                                    class="grid grid-cols-2 gap-2 items-center">
                                                    <p class="text-center dark:text-white text-gray-800 ml-2 hover:text-sky-300">
                                                        {{ subtopicos.nombre }}
                                                    </p>
                                                    <button @click="(EstudianteGPT(subtopicos.id))"
                                                        class="mx-auto text-white bg-indigo-500 border-0 px-0.5 my-3 focus:outline-none hover:bg-indigo-900 rounded text-lg">
                                                        {{ form.processing ? '...' : 'Comenzar Lección' }}
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

            <div v-if="props.limite > 0">
                <div v-if="props.respuesta != ''">

                    <section v-if="props.limite > -1 && !form.processing" class="body-font relative">
                        <div class="container px-5 pt-6 mx-auto">
                            <div class="flex flex-col text-center w-full mt-4">
                                <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900 dark:text-white">
                                    {{ temaSelec.nombre }}
                                </h1>
                                <p class="lg:w-2/3 mx-auto leading-relaxed text-xl text-gray-600 dark:text-white">
                                    {{ subtopicoSelec.nombre }}
                                </p>
                                <p class="lg:w-2/3 mx-auto leading-relaxed text-base text-gray-500 dark:text-white my-4">
                                    Tokens consumidos: {{ props.restarAlToken }}
                                </p>
                            </div>
                            <div class="w-full flex mt-6 mx-auto">

                                <div class="flex-none w-14 ..." :class="{'w-44' : props.respuesta.length > 500}"> </div>
                                <div class="grow ...">
                                    <p class="text-justify font-sans">{{ props.respuesta }} </p>
                                    <p class="mb-4 mt-12 text-center text-lg font-sans">
                                        ¡Recuerde que esto es un mensaje generado por Inteligencia artificial, Por favor
                                        verifique que los resultados sean consistentes!
                                    </p>
                                </div>
                                <div class="flex-none w-14 ..." :class="{'w-44' : props.respuesta.length > 500}"> </div>
                            </div>
                            <div v-if="props.notvalidbyteacher" class="w-full flex mt-6 mx-auto">
                                <div class="w-full mt-1 mx-auto">

                                    <div class="w-full flex items-center justify-center dark:bg-gray-800 bg-gray-100">
                                        <div class="w-full mx-auto py-6">
                                            <h1 class="text-xl text-center font-bold mb-6"> Aprender más </h1>
                                            <div
                                                class="bg-white dark:bg-gray-600 px-6 py-4 my-3 w-3/4 mx-auto shadow rounded-md flex items-center">
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
                        </div>
                    </section>
                </div>
                <div v-else class="w-full flex item-center mt-10">
                    <p class="text-red-400 mx-auto text-xl">{{ props.respuesta }}</p>
                </div>
            </div>
            <div v-else class="body-font relative">
                <div class="container px-5 pt-6 mx-auto">
                    <div class="flex flex-col text-center w-full mt-4">
                        <h1 class="text-2xl font-medium title-font mb-4 text-red-600 ">
                            {{ props.respuesta }}
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
textarea {
    @apply px-3 py-2 border border-gray-300 rounded-md;
}</style>