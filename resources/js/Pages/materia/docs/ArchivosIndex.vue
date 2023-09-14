<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { reactive, watch, ref, watchEffect, onMounted } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import { PrimerasPalabras, vectorSelect, formatDate, CalcularEdad, CalcularSexo } from '@/global.ts';;

import { useForm } from '@inertiajs/vue3';


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
    TamanoMAX: 0

})
const form = useForm({
    archivo: null,
    nombre: '',
    peso: 0,
    type: '',
    user_id: '',
    materia_id: '',
})
const create = () => {
    form.materia_id = props.materia.id

    form.post(route('materia.storeArchivos'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        // onError: () => alert(form.errors),
        onError: () => null,
        // onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null,
    })
}

onMounted(() =>{ 
    data.TamanoMAX = (1024 * 1024)  * 12; //12MB
})

const handleFile =(() => {
    if(form.archivo){
        form.type = form.archivo.type
        if(form.type == "application/pdf"){
            form.peso = (Math.round(form.archivo.size / (1024 * 1024)))
            if(form.peso < data.TamanoMAX){

                // form.archivoAux = event.target.files;
                form.nombre = form.archivo.name.slice(0,-4)
                data.tamanin = "El archivo pesa aproximadamente " + form.peso + " MB";

                
            }else{
                data.mensajes = 'El peso del archivo supera los 12MB'
            }
        }else{
            data.mensajes = 'El archivo debe ser un PDF'
        }
    }
})

watchEffect(() => { })
</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <section class="text-gray-600 body-font">
            <div class="container px-5 mx-auto">
                <div class="flex flex-nowrap -m-4 text-center">
                    <div class="px-4 lg:w-full">
                        <div class="h-full bg-gray-100 bg-opacity-75 px-8 pt-8 pb-1 rounded-lg overflow-hidden text-center relative">
                            <h1 class="title-font sm:text-2xl text-xl font-medium text-gray-900 mb-3">{{ materia.nombre }}</h1>
                            <h1 class="title-font sm:text-xl text-lg font-medium text-gray-900">Guardar documentos (PDF)</h1>
                        </div>
                    </div>
                </div>
                <form @submit.prevent="create" enctype='multipart/form-data' class="grid  mx-[120px] text-center">
                    <p class="my-4 w-full">{{ data.tamanin }}</p>
                    <input type="text" v-model="form.nombre" placeholder="Digite el nombre del archivo" class="w-1/2 mx-auto my-4"/>

                    <label for="archivou" class="w-full underline text-sky-700">{{ form.archivo ? form.archivo.name : 'No hay archivo seleccionado' }}</label>
                    <input id="archivou" type="file" @input="form.archivo = $event.target.files[0]" @change="handleFile" accept="application/pdf" class="my-6 w-full hidden"/>

                    <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full"> 
                     {{ form.progress.percentage }}% 
                    </progress>

                    <button v-if="form.archivo" type="submit" class="w-1/2 mx-auto border border-white-500 bg-sky-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-yellow-600 focus:outline-none focus:shadow-outline">
                        Subir archivo
                    </button>
                </form>
            </div>
            <div class="container px-5 py-4 mx-auto">
                <div class="flex flex-wrap -m-4">
                    <!-- {{ props.archivos }} -->
                    <div class="p-4 lg:w-1/3">
                        <div v-for="(archivin, index) in props.archivos" class="h-full bg-gray-100 bg-opacity-75 px-8 pt-16 pb-24 rounded-lg overflow-hidden text-center relative">
                            <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">Doc {{  (index+1) }}</h2>
                            <h1 class="title-font sm:text-2xl text-xl font-medium text-gray-900 mb-3">{{ archivin.nombre }}</h1>
                            <!-- <p class="leading-relaxed mb-3">Descrip</p> -->
                            <p class="leading-relaxed mb-3">{{ formatDate(archivin.updated_at) }}</p>

                            <Link :href="route('vistaPDF', archivin.id)" class="my-5 pb-20">
                                <a class="text-indigo-500 inline-flex items-center">Visualizar archivo
                                    <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M5 12h14"></path> <path d="M12 5l7 7-7 7"></path> </svg>
                                </a>
                            </Link>
                            <embed :src="route('verPdf', archivin.id)" type="application/pdf" height="600px" class="w-full mt-8"/>
                            <!-- <div class="text-center mt-2 leading-none flex justify-center absolute bottom-0 left-0 w-full py-4">
                                <span
                                    class="text-gray-400 mr-3 inline-flex items-center leading-none text-sm pr-3 py-1 border-r-2 border-gray-200">
                                    <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"> <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path> <circle cx="12" cy="12" r="3"></circle> </svg>1.2K
                                </span>
                                <span class="text-gray-400 inline-flex items-center leading-none text-sm">
                                    <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"> <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"> </path> </svg>6
                                </span>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>

</template>