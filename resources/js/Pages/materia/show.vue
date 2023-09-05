<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { reactive, watch, ref, watchEffect, onMounted } from 'vue';
import pkg from 'lodash';
import { CursorArrowRippleIcon, ChevronUpDownIcon, QuestionMarkCircleIcon, EyeIcon, PencilIcon, TrashIcon, UserCircleIcon } from '@heroicons/vue/24/solid';
import superButton from '@/Components/uiverse/superButton.vue';
import { vectorSelect, formatDate, CalcularEdad, CalcularSexo } from '@/global.ts';;

const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    // filters: Object,
    breadcrumbs: Object,
    // perPage: Number,

    fromController: Object, //materia

    unidads: Object,
    objetivos: Object,
})

const data = reactive({
    params: {
        // perPage: props.perPage,
    },
    vectorMostrar: []
    // selectedId: [],  
})

const order = (field) => {
    data.params.field = field.replace(/ /g, "_")

    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

const mostrarZona = (subtema) => {
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
            <h1 class="text-xl title-font font-medium text-center my-1 text-zinc-600">Contenido de</h1>
            <h1 class="text-sky-700 text-3xl title-font text-center font-medium mb-4">{{ props.fromController.nombre }} </h1>
            <h1 class="text-gray-900 text-lg title-font text-justify font-medium mb-4">{{ props.fromController.descripcion }} </h1>
            <h1 class="text-sky-700 text-2xl title-font font-medium text-center mt-10 mb-5">Objetivos </h1>
            <ol class="list-decimal mb-12">
                <li v-for="objetivo in props.objetivos" class="text-gray-900 text-xl title-font font-medium mb-4"> - {{
                    objetivo.nombre
                }} </li>
            </ol>

            <p class="text-center">
                Si desea iniciar con sus estudios presione click 
                <div class="rounded-md overflow-hidden">
                    <superButton type="button" class="rounded-lg"
                        :ruta="'materia.VistaTema'"
                        :id1="props.fromController.id"
                        :texto="'Aqui'">
                    </superButton>
                </div> 
            </p>

            <div v-if="props.unidads.length" class="lg:w-4/5 w-full mx-auto overflow-auto">
                <table class="table-auto w-full text-left whitespace-no-wrap">
                    <thead>
                        <tr>
                            <th
                                class="px-4 py-3 title-font tracking-wider font-bold text-gray-900 text-sm bg-gray-200 rounded-tl rounded-bl">
                                Unidade de aprendizaje
                            </th>
                            <th class="px-4 py-3 title-font tracking-wider font-bold text-gray-900 text-sm bg-gray-200">
                                Contenidos
                            </th>
                            <th class="px-4 py-3 title-font tracking-wider font-bold text-gray-900 text-sm bg-gray-200">
                                Resultado de aprendizaje
                            </th>
                            <!-- <th class="w-10 title-font tracking-wider font-bold text-gray-900 text-sm bg-gray-200 rounded-tr rounded-br"> </th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(Unidad, temasIndex) in props.unidads"
                            class="lg:w-1/2 w-full lg:pr-10 lg:py-6 mb-6 lg:mb-0 border-1 border-b border-gray-900">
                            <td class="px-4 py-3">{{ Unidad.nombre }}</td>

                            <td v-if="(typeof Unidad.sub !== 'undefined')" class="text-justify">
                                <ul class="list-decimal">
                                    <li v-for="(subtema, subindex) in Unidad.sub" class="py-3">
                                        {{ subtema.nombre }}
                                    </li>
                                </ul>
                            </td>
                            <td v-if="(typeof Unidad.sub !== 'undefined')">
                                <ul class="list-inside">
                                    <li v-for="(subtema, subindex) in Unidad.sub" class="px-4 py-3">{{
                                        subtema.resultado_aprendizaje }}</li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
            <p v-else class="text-xl text-ellipsis text-orange-800">
                No hay unidades registrados para esta materia
            </p>
            <!-- todo: ir a la IA -->
            <!-- <div class="flex pl-4 mt-4 lg:w-2/3 w-full mx-auto">
                <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">Learn More
                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"></path>
                    </svg>
                </a>
                <button
                    class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">Button</button>
            </div> -->
        </section>
        <div class="text-center mt-8">
            <Link :href="route('materia.index')"
                class="text-center my-4 border border-sky-700  bg-black text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-600 focus:outline-none focus:shadow-outline">
                Regresar
            </Link>
        </div>
    </AuthenticatedLayout>
</template>
