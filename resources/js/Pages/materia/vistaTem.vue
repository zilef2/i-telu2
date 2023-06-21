<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, ref } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { router, usePage } from '@inertiajs/vue3';
import { ChevronUpDownIcon, PencilIcon, EyeIcon, TrashIcon } from '@heroicons/vue/24/solid';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import { useForm } from '@inertiajs/vue3';

const temaSelected = ref(null);
const temaSelectedName = ref('');
const subtopSelected = ref('');

const { _, debounce, pickBy } = pkg
const props = defineProps({
    elid: Number,
    valoresSelect: Object,

    title: String,
    breadcrumbs: Object,
    perPage: Number,

    fromController: Object,

    respuesta: String,
    // respuesta de gpt
    temaSelec: String,
    subtopicoSelec: String,
    ejercicioSelec: String,
    limite: Number,
})

const data = reactive({
    params: {
        perPage: props.perPage,
    },
    generico: null,
})

// chatgpt form
const form = useForm({
    nivel: 1,
    pregunta: ref(''),
    respuestagpt: ref(props.respuesta)
})


const submitToGPT = (temaSelec, subtopicoSelec, ejercicioSelec) => {
    form.get(route('materia.VistaTema', [props.elid, temaSelec, subtopicoSelec, ejercicioSelec]), {
        preserveScroll: true,
        onSuccess: () => { },
        onError: () => alert('asd'),
        // onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null,
    })
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
                    <div class="flex justify-between p-2">
                        <h2 class="mx-auto text-lg">Recuerde utilizar los tokens restantes con precaución</h2>
                    </div>

                    <section class="text-gray-600 body-font">
                        <div class="container px-5 py-8 mx-auto">
                            <div class="flex flex-wrap -m-4">
                                <div v-for="(clasegenerica, index) in fromController" :key="index" class="p-4 md:w-1/3"
                                    :class="index == temaSelected ? 'bg-blue-100' : ''">
                                    <div
                                        class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">

                                        <div class="p-6">
                                            <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">
                                                {{ clasegenerica.mat }}
                                            </h2>
                                            <h1 class="title-font text-lg font-medium text-gray-900 mb-3">{{
                                                clasegenerica.nombre }}</h1>
                                            <p class="leading-relaxed mb-3">{{ clasegenerica.descripcion }}.</p>

                                            <div v-if="clasegenerica.sub.length" class="flex flex-col">
                                                <div v-for="(subtopicos, Jindex) in clasegenerica.sub" :key="Jindex"
                                                    class="flex items-center">
                                                    <!-- <input id="toggle" type="checkbox"
                                                        class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"> -->
                                                    <button
                                                        @click="(temaSelected = index, temaSelectedName = subtopicos.nombre, subtopSelected = Jindex)"
                                                        for="toggle1"
                                                        class="underline text-sky-600 ml-2 hover:text-sky-300">
                                                        {{ subtopicos.nombre }}
                                                    </button>
                                                </div>
                                            </div>
                                            <div v-else class="flex flex-col">
                                                <div class="flex items-center">
                                                    <p class="text-red-500">Sin subtopicos!</p>
                                                </div>
                                            </div>


                                            <div class="flex items-center flex-wrap ">
                                                <!-- <a class="cursor-pointer text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">Elejir tema
                                                    <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M5 12h14"></path> <path d="M12 5l7 7-7 7"></path> </svg>
                                                </a> -->
                                                <span
                                                    class="text-gray-400 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1 border-r-2 border-gray-200">
                                                    <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round"
                                                        viewBox="0 0 24 24">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    {{ props.limite }} token disponibles
                                                </span>
                                                <!-- <span class="text-gray-400 inline-flex items-center leading-none text-sm">
                                                    <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                        <path
                                                            d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z">
                                                        </path>
                                                    </svg>
                                                    21 preguntas
                                                </span> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div v-if="temaSelected != null" class="container px-5 py-1 mx-auto">
                        <div class="flex flex-col text-center w-full mb-2">
                            <!-- <h2 class="text-xs text-indigo-500 tracking-widest font-medium title-font mb-1">Escojer</h2> -->
                            <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">Lecciones</h1>
                            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                                Subtopico: <b>{{ temaSelectedName }} </b>
                            </p>
                        </div>
                    </div>
                    <section v-if="temaSelected != null" class="text-gray-600 body-font">
                        <div class="container px-5 py-3 mx-auto">
                            <div class="flex flex-wrap -m-4">
                                <!-- <div class="p-4 md:w-1/3"> -->
                                <div v-for="(ejercicio, index) in fromController[temaSelected].sub[subtopSelected].ejer"
                                    :key="index" class="p-4 md:w-1/3">
                                    <div
                                        class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                        <div class="p-6">
                                            <p @click="submitToGPT(temaSelectedName, temaSelectedName, ejercicio.nombre)"
                                                class="underline text-sky-300 cursor-help leading-relaxed mb-3">{{
                                                    ejercicio.nombre }}.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <!-- <p @click="submitToGPT( temaSelectedName, temaSelectedName, ejercicio.nombre )" -->
            <section v-if="props.limite > 0" v-show="props.respuesta" class="text-gray-600 body-font relative">
                <div class="container px-5 py-24 mx-auto">
                    <div class="flex flex-col text-center w-full mb-12">
                        <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">{{ temaSelec }}</h1>
                        <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                            {{ subtopicoSelec }}
                        </p>
                    </div>
                    <div class="w-full mx-auto">
                        <form @submit.prevent="submitFormGPT">
                            <div class="flex flex-wrap -m-2">
                                <div class="p-2 w-1/2">
                                    <div class="relative">
                                        <label for="name" class="leading-7 text-sm text-gray-600">Objetivo</label>
                                        <p
                                            class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-800 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ valoresSelect.nombre }}
                                        </p>
                                        <!-- <SelectInput v-model="nivel" :dataSet="props.valoresSelect" class="mt-1 block w-full" /> -->
                                    </div>
                                </div>
                                <div class="p-2 w-1/2">
                                    <div class="relative">
                                        <label for="pregunta" class="leading-7 text-sm text-gray-600">pregunta</label>
                                        <p
                                            class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ ejercicioSelec }}?
                                        </p>
                                    </div>
                                </div>
                                <div class="p-2 w-full h-full">
                                    <div class="relative h-full">
                                        <label for="message" class="leading-7 text-sm text-gray-600">Respuesta </label>
                                        <textarea v-model="form.respuestagpt" id="message" name="message" rows="30"
                                            cols="35"
                                            class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 
                                            text-base outline-none text-gray-700 py-1 px-3 leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                    </div>
                                </div>
                                <div class="p-2 w-full">
                                    <button @click="submitFormGPT"
                                        class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                                        {{ form.processing ? 'Espere...' : 'Preguntar' }}
                                    </button>
                                </div>


                                <div class="p-2 w-full pt-8 mt-8 border-t border-gray-200 text-center">
                                    <a class="text-indigo-500">example@email.com</a>
                                    <p class="leading-normal my-5">plantilla ejemplo
                                        <br>
                                        Colombia
                                    </p>
                                    <span class="inline-flex">
                                        <a class="text-gray-500">
                                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a class="ml-4 text-gray-500">
                                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                                <path
                                                    d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a class="ml-4 text-gray-500">
                                            <svg fill="none" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" class="w-5 h-5"
                                                viewBox="0 0 24 24">
                                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                                <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"></path>
                                            </svg>
                                        </a>
                                        <a class="ml-4 text-gray-500">
                                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                                <path
                                                    d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z">
                                                </path>
                                            </svg>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </section>
            <div v-else class="w-full flex item-center mt-10">
                <p class="text-red-400 mx-auto text-xl">{{ props.respuesta }}</p>
            </div>
        </div>
        <div v-else class="">
            <section class="text-gray-600 body-font">
                <div class="container px-5 py-4 mx-auto" bis_skin_checked="1">
                    <div class="flex flex-col text-center w-full mb-20" bis_skin_checked="1">
                        <h2 class="text-xl text-indigo-500 tracking-widest font-medium title-font mb-1">Sin temas</h2>
                        <!-- <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">Sin temas</h1> -->
                        <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                            La materia seleccionada no tiene temas, por favor, asignele un tema para mostralo aquí...</p>
                    </div>
                </div>
            </section>
        </div>

    </AuthenticatedLayout>
</template>
