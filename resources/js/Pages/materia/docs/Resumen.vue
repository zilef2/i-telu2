<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { reactive, watch, ref, watchEffect, onMounted } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import { PrimerasPalabras, vectorSelect, formatDate, CalcularEdad, CalcularSexo } from '@/global.ts';;
import Back from '@/Components/uiverse/BackButton.vue';

import { useForm } from '@inertiajs/vue3';


const props = defineProps({
    breadcrumbs: Object,
    title: String,
    numberPermissions: Number,
    ChatResumen: Object,
    archivinid: Number,
    materia_id: Number,
    archivo: Object,
})

const data = reactive({
    tamanin: '',
    TamanoMAX: 0,
    sumOfpesos: 0
})
const form = useForm({
    archivo: null,
    nombre: '',
    peso: 0,
    type: '',
    user_id: '',
    materia_id: '',
})

onMounted(() =>{ })
// const handleFile =(() => { })
watchEffect(() => { })

</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <section class="text-gray-600 body-font">
<!--            <div class="container px-5 mx-auto mb-12">-->
<!--                <div class="flex flex-nowrap -m-4 text-center">-->
<!--                    <div class="px-4 lg:w-full">-->
<!--                        <div class="h-full bg-gray-100 bg-opacity-75 px-8 pt-8 pb-1 rounded-lg overflow-hidden text-center relative">-->
<!--                            <h1 class="title-font sm:text-xl text-lg font-medium text-gray-900">Nombre del archivo</h1>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

            <div class="container px-5 my-8 mx-auto border border-b-2 border-x-0 border-black">
                <div class="text-center">
                    <h1 class="title-font sm:text-xl text-lg my-5 font-medium text-gray-900"> {{ archivo.nombre }}</h1>
                    <!-- <h1 class="title-font sm:text-lg text-md my-5  text-gray-800">Numero de archivos almacenados {{props.archivos.length}}</h1> -->
                </div>
                <div class="flex flex-wrap -m-4">
                    <!-- {{ props.archivos }} -->
                    <div class="p-4 w-full text-justify">
                        <div class="h-full bg-white bg-opacity-75 px-8 pt-16 pb-24 rounded-lg overflow-hidden text-center relative">
                            <p v-if="ChatResumen[2] !== 'NotTokens'" class="leading-relaxed mx-auto max-w-2xl text-lg text-justify mb-3 font-medium">{{ ChatResumen[1] }}</p>
                            <p v-else class="leading-relaxed mx-auto text-red-400 text-lg text-justify mb-3 font-bold">{{ ChatResumen[1] }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2">

                    <Link :href="route('generarResumen', [props.archivo.id,1])" class="mt-4 mb-1 border-2 border-b-blue-300">
                        <a class="text-indigo-500 inline-flex items-center">¿Cuál es el tema central o la principal conclusión del documento que se describe en el PDF?
                        </a>
                    </Link>
                    <Link :href="route('generarResumen', [props.archivo.id,2])" class="mt-4 mb-1 border-2 border-b-blue-300">
                        <a class="text-indigo-500 inline-flex items-center">¿Cuáles son los puntos clave o los argumentos principales presentados en el PDF?
                        </a>
                    </Link>
                    <Link :href="route('generarResumen', [props.archivo.id,3])" class="my-1 border-2 border-b-blue-300">
                        <a class="text-indigo-500 inline-flex items-center">¿Quiénes son los autores y cuál es su credibilidad en relación con el tema del PDF?
                        </a>
                    </Link>
                    <Link :href="route('generarResumen', [props.archivo.id,4])" class="my-1 border-2 border-b-blue-300">
                        <a class="text-indigo-500 inline-flex items-center">¿Cuál es el contexto o la relevancia del contenido del PDF en el campo o la industria a la que pertenece?
                        </a>
                    </Link>

                </div>
                <Back :ruta="'materia.Archivos'" :id1="props.materia_id" class="my-12 text-center"/>
            </div>
        </section>
    </AuthenticatedLayout>

</template>
