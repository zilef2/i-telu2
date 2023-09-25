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
            <div class="container px-5 mx-auto mb-12">
                <div class="flex flex-nowrap -m-4 text-center">
                    <div class="px-4 lg:w-full">
                        <div class="h-full bg-gray-100 bg-opacity-75 px-8 pt-8 pb-1 rounded-lg overflow-hidden text-center relative">
                            <h1 class="title-font sm:text-xl text-lg font-medium text-gray-900">Nombre del archivo</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container px-5 my-8 mx-auto border border-y-2 border-x-0 border-black">
                <div class="text-center">
                    <h1 class="title-font sm:text-xl text-lg my-5 font-medium text-gray-900"> {{ archivo.nombre }}</h1>
                    <!-- <h1 class="title-font sm:text-lg text-md my-5  text-gray-800">Numero de archivos almacenados {{props.archivos.length}}</h1> -->
                </div>
                <div class="flex flex-wrap -m-4">

                    <!-- {{ props.archivos }} -->
                    <div class="p-4 w-full text-justify">
                        <div  class="h-full bg-gray-100 bg-opacity-75 px-8 pt-16 pb-24 rounded-lg overflow-hidden text-center relative">
                            <!-- <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">Doc {{  (index+1) }}</h2>
                            <h1 class="title-font sm:text-2xl text-xl font-medium text-gray-900 mb-3">{{ archivin.nombre }}</h1>
                            <p class="leading-relaxed mb-3">{{ formatDate(archivin.updated_at) }}</p> -->
                            <p class="leading-relaxed text-justify mb-3">{{ ChatResumen[1] }}</p>
                        </div>
                    </div>
                </div>
                <Back :ruta="'materia.Archivos'" :id1="materia_id" class="my-12 text-center"/>
            </div>
        </section>
    </AuthenticatedLayout>

</template>