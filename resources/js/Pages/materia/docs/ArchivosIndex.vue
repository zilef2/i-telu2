<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { reactive, watch, ref, watchEffect, onMounted } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import Back from '@/Components/uiverse/BackButton.vue';
import { useForm } from '@inertiajs/vue3';
import {TrashIcon, XMarkIcon} from "@heroicons/vue/24/solid";

import { formatDate,PrimerasPalabras } from '@/global.ts';

const props = defineProps({
    breadcrumbs: Object,
    title: String,
    numberPermissions: Number,
    archivos: Object,
    materia: Object,
})
const emit = defineEmits(["close"]);

const data = reactive({
    tamanin: '',
    TamanoMAX: 0,
    sumOfpesos: 0,
    visibleFlash:true,
    mostrarForm:true,
})
const form = useForm({
    id: null,
    archivo: null,
    nombre: '',
    peso: 0,
    type: '',
    user_id: '',
    materia_id: '',
    GuardarPDF: false
})


onMounted(() =>{
    setTimeout(function() {
        data.visibleFlash = false
    }, 4000);
    data.TamanoMAX = (1024 * 1024)  * 10; //10MB
    props.archivos.forEach((value, index, array) => {
        data.sumOfpesos += value.peso
    })
})
watchEffect(() => { })


const handleFile =(() => {
    if(form.archivo){
        form.type = form.archivo.type
        if(form.type === "application/pdf"){
            form.peso = (Math.round(form.archivo.size / (1024 * 1024)))
            if(form.peso < data.TamanoMAX){

                // form.archivoAux = event.target.files;
                form.nombre = form.archivo.name.slice(0,-4)
                data.tamanin = "El archivo pesa aproximadamente " + form.peso + " MB";


            }else{
                data.mensajes = 'El peso del archivo supera los 10MB'
            }
        }else{
            data.mensajes = 'El archivo debe ser un PDF'
        }
    }
})

const toggle = () => {
    data.visibleFlash = !data.visibleFlash
}
const create = () => {
    form.materia_id = props.materia.id

    form.post(route('materia.storeArchivos'), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            // form.reset()
        },
        // onError: () => alert(form.errors),
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null,
    })
}
const deletethis = (archivoid) => {
    form.id = archivoid
    if(form.id){

        form.post(route('materia.DeleteArchivos'), {
            preserveScroll: true,
            onSuccess: () => {},
            onError: () => alert(JSON.stringify(form.errors, null, 4)),
            onFinish: () => null,
        })
    }
}
</script>

<template>
    <Head :title="props.title"></Head>
    <transition name="slide-fade">
        <div v-if="$page.props.flash && $page.props.flash.info && data.visibleFlash" class="fixed top-24 right-4 w-8/12 md:w-6/12 lg:w-3/12 z-[100]">
            <div class="flex p-4 justify-between items-center bg-primary rounded-lg">
                <div>
                    <InformationCircleIcon class="h-8 w-8 text-white" fill="currentColor" />
                </div>
                <div class="mx-3 text-sm font-medium text-white" v-html="$page.props.flash.info">
                </div>
                <button @click="toggle" type="button"
                        class="ml-auto bg-white/20 text-white rounded-lg focus:ring-2 focus:ring-white/50 p-1.5 hover:bg-white/30 h-8 w-8 font-sans">
                    <span class="sr-only">Close</span>
                    <XMarkIcon class="w-5 h-5" />
                </button>
            </div>
        </div>
    </transition>


    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <section class="text-gray-600 body-font">
            <div class="container px-5 mx-auto mb-12">
                <div class="flex flex-nowrap -m-4 text-center">
                    <div class="px-4 lg:w-full">
                        <div class="h-full bg-gray-100 bg-opacity-75 px-8 pt-8 pb-1 rounded-lg overflow-hidden text-center relative">
                            <!-- <h1 class="title-font sm:text-2xl text-xl font-medium text-gray-900 mb-3"></h1> -->
                            <h1 @click="data.mostrarForm = !data.mostrarForm" class="title-font sm:text-xl text-lg font-medium text-gray-900 hover:underline cursor-pointer">Guardar documentos, solo PDF</h1>
                        </div>
                    </div>
                </div>
                <form v-if="data.mostrarForm" @submit.prevent="create" enctype='multipart/form-data' class="grid mx-[80px] text-center">
                    <p class="mt-4 w-full">{{ data.tamanin }}</p>
                    <input type="text" v-model="form.nombre" placeholder="Digite el nombre del archivo" class="w-1/2 mx-auto my-1"/>

                    <label for="archivou" class="w-full underline text-sky-700">{{ form.archivo ? form.archivo.name : 'No hay archivo seleccionado' }}</label>
                    <input id="archivou" type="file" @input="form.archivo = $event.target.files[0]" @change="handleFile" accept="application/pdf" class="my-6 w-full hidden"/>

                    <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full">
                        {{ form.progress.percentage }}%
                    </progress>

                    <div class="grid-flow-col gap-8 mt-6">
                        <button v-if="form.archivo && !form.processing" @click="form.GuardarPDF = false" type="submit"
                                class="w-1/3 mx-auto border border-white-500 bg-sky-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-800 focus:outline-none focus:shadow-outline">
                            Preguntar coste
                        </button>
                        <button v-if="form.archivo && !form.processing" type="submit" @click="form.GuardarPDF = true"
                                class="w-1/3 mx-auto border border-white-500 bg-sky-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-800 focus:outline-none focus:shadow-outline">
                            Subir archivo
                        </button>
                    </div>
                </form>
                <Back :ruta="'materia.index'" class="mt-7 text-center"/>
            </div>

            <div class="container px-5 my-4 mx-auto border border-b-2 border-x-0 border-black">
                <div class="text-center">
                    <h1 class="title-font sm:text-2xl text-xl my-5 hover:font-bold text-gray-900 font-sans">{{ materia.nombre }}</h1>
                </div>
                <div class="flex flex-wrap -m-4">

                    <!-- {{ props.archivos }} -->
                    <div v-for="(archivin, index) in props.archivos" :key="index" class="p-2 w-full lg:w-1/2">
                        <div  class="h-full bg-gray-100 bg-opacity-75 px-2 pt-2 pb-12 rounded-lg overflow-hidden text-center relative">
                            <h2 class="tracking-widest text-sm title-font font-medium text-gray-400 mb-1">{{  formatDate(archivin.updated_at) }}</h2>
                            <div class="text-center mx-auto py-1"><h1
                                class="title-font sm:text-2xl text-xl font-medium text-gray-900 h-[6rem] my-1 items-center text-center">
                                {{ PrimerasPalabras(archivin.nombre, 18) }}</h1></div>
                            <div class="grid grid-cols-2">
                                <Link :href="route('vistaPDF', archivin.id)" class="my-1 border-2 border-b-blue-300">
                                    <a class="text-indigo-500 inline-flex items-center">Visualizar archivo
                                        <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M5 12h14"></path> <path d="M12 5l7 7-7 7"></path> </svg>
                                    </a>
                                </Link>
                                <Link :href="route('generarResumen', archivin.id)" class="my-1 border-2 border-b-blue-300">
                                    <a class="text-indigo-500 inline-flex items-center">Resumir archivo
                                        <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M5 12h14"></path> <path d="M12 5l7 7-7 7"></path> </svg>
                                    </a>
                                </Link>
                            </div>
                            <div class="grid grid-cols-2">
                                <Link :href="route('generarResumen', [archivin.id,1])" class="mt-4 mb-1 border-2 border-b-blue-300">
                                    <a class="text-indigo-500 inline-flex items-center">¿Cuál es el tema central o la principal conclusión del documento que se describe en el PDF?
                                    </a>
                                </Link>
                                <Link :href="route('generarResumen', [archivin.id,2])" class="mt-4 mb-1 border-2 border-b-blue-300">
                                    <a class="text-indigo-500 inline-flex items-center">¿Cuáles son los puntos clave o los argumentos principales presentados en el PDF?
                                    </a>
                                </Link>
                            </div>
                            <div class="grid grid-cols-2">
                                <Link :href="route('generarResumen', [archivin.id,3])" class="my-1 border-2 border-b-blue-300">
                                    <a class="text-indigo-500 inline-flex items-center">¿Quiénes son los autores y cuál es su credibilidad en relación con el tema del PDF?
                                    </a>
                                </Link>
                                <Link :href="route('generarResumen', [archivin.id,4])" class="my-1 border-2 border-b-blue-300">
                                    <a class="text-indigo-500 inline-flex items-center">¿Cuál es el contexto o la relevancia del contenido del PDF en el campo o la industria a la que pertenece?
                                    </a>
                                </Link>
                            </div>
                            <div class="grid grid-cols-2">
                                <button @click="deletethis(archivin.id)"
                                        class="col-span-2 mx-auto py-1 w-full justify-center border-b-2 title-font font-medium bg-gray-100 inline-flex items-center leading-none tracking-wider rounded-t"
                                        :class="{ 'border-indigo-500 text-indigo-500': data.mostrarSection === 1 }">
                                    <TrashIcon class="mt-5 h-10 lg:h-8 w-full object-cover object-center hover:text-red-600" />
                                </button>

                            </div>
                            <embed :src="route('verPdf', archivin.id)" type="application/pdf" height="600px" class="w-full col-span-2 mt-8"/>
                        </div>
                    </div>


                </div>
                    <div class="text-center">
                        <h1 class="title-font sm:text-lg text-md my-5  text-gray-800">Numero de archivos almacenados {{props.archivos.length}}</h1>
                        <h1 class="title-font sm:text-lg text-md my-5  text-gray-800">Peso total: {{ data.sumOfpesos }} MB</h1>
                    </div>
                <Back :ruta="'materia.index'" class="my-12 text-center"/>
            </div>
        </section>
    </AuthenticatedLayout>

</template>
