<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, ref, watchEffect, onMounted } from 'vue';

import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { router, usePage, Link } from '@inertiajs/vue3';

import Pagination from '@/Components/Pagination.vue';
import { CursorArrowRippleIcon, ChevronUpDownIcon, QuestionMarkCircleIcon, EyeIcon, PencilIcon, TrashIcon, UserGroupIcon } from '@heroicons/vue/24/solid';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import { useForm } from '@inertiajs/vue3';
import { vectorSelect, formatDate, CalcularEdad, CalcularSexo } from '@/global.js';

const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    // filters: Object,
    breadcrumbs: Object,
    // perPage: Number,

    fromController: Object,

    temas : Object,
})

const data = reactive({
    params: {
        // perPage: props.perPage,

    },
    vectorMostrar: []
    // selectedId: [],  
    // multipleSelect: false,
})

const order = (field) => {
    data.params.field = field.replace(/ /g, "_")

    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

const mostrarZona = (subtema) =>{
    for (let i = 0; i < 3; i++) {
        data.vectorMostrar[i] = 0; //todo: falta decir el tamaño de la matriz
    }
    data.vectorMostrar[subtema] = 1
}

watchEffect(() => {
})

onMounted(() => {
    for (let i = 0; i < 3; i++) {
        data.vectorMostrar[i] = 0;
    }
    data.vectorMostrar[0] = 1;
})

</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />

        <section class="text-gray-600 body-font overflow-hidden">
            <!-- <h2 class="text-sm title-font text-gray-500 tracking-widest">Materia</h2> -->
            <h1 class="text-gray-900 text-3xl title-font font-medium mb-4">{{ props.fromController.nombre }} </h1>
            <div v-if="props.temas.length" class="container px-5 py-6 mx-auto">
                <div class="lg:w-full mx-auto flex flex-wrap">
                    <div v-for="(tema,temasIndex) in props.temas" class="lg:w-1/2 w-full lg:pr-10 lg:py-6 mb-6 lg:mb-0">
                        
                        <h1 class="text-gray-900 text-3xl title-font font-medium mb-4">{{ tema.nombre }} </h1>
                        <div v-if="(typeof tema.sub !== 'undefined')" v-for="(subtema, subindex) in tema.sub" class="">
                            <div class="flex mb-4">
                                <!-- //todo: mandar el subtema -->
                                <button @click="mostrarZona(subindex)" :class="{ 'text-indigo-500 border-indigo-500' : data.vectorMostrar[0]}" class="flex-grow border-b-2 border-gray-300 py-2 text-lg px-1">
                                    {{ subtema.nombre }}
                                </button>
                            </div>
                            <p class="leading-relaxed mb-4">Descripcion y objetivos </p>
                            <!-- //todo: objetivos y noseque -->
                            <div class="flex border-t border-gray-200 py-2">
                                <span class="text-gray-800 font-semibold">Ejercicio/Pregunta</span>
                                <span class="ml-auto text-gray-900 font-semibold"># respuestas almacenadas</span>
                            </div>
                            <div v-if="(typeof subtema.ejercicios !== 'undefined')" v-for="ejercicio in subtema.ejercicios" class="flex border-t border-gray-200 py-2">
                                <span class="text-gray-500">{{ ejercicio.nombre }}</span>
                                <span class="ml-auto pl-1 text-gray-900 border-l-2 border-indigo-500 ">5</span>
                            </div>
                        </div>
                        <div class="flex mt-6">
                            <!-- <span class="title-font font-medium text-2xl text-gray-900"># Ejercicios : {{ props.temas.Tsubtema.ejercis.length }}</span> -->
                            <!-- <button class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">
                                Preguntarle a la IA
                            </button> -->
                        </div>
                    </div>
                </div>
            </div>



            <div v-else class="container px-5 py-6 mx-auto">
                <div class="lg:w-full mx-auto flex flex-wrap">
                    <div  class="lg:w-1/2 w-full lg:pr-10 lg:py-6 mb-6 lg:mb-0">
                        
                        <p class="leading-relaxed text-xl mb-4">No hay temas regsitrados para esta materia
                        </p>
                    </div>
                </div>
            </div>
        </section>

    </AuthenticatedLayout>
</template>
