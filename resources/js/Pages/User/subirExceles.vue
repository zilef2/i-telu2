<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';

import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, onMounted, watchEffect } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

import { DocumentArrowUpIcon, ArrowUpCircleIcon, ArrowDownCircleIcon } from '@heroicons/vue/24/solid';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import SubirCarreras from '@/Pages/User/subir/SubirCarreras.vue';


import { PrimerasPalabras, vectorSelect, formatDate, CalcularEdad, CalcularSexo } from '@/global.ts';


const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    // fromController: Object,
    breadcrumbs: Object,
    numUsuarios: Number,
    UniversidadSelect: Object,
    flash: Object,

})

onMounted(() => {
    if (props.flash.warning) {
        data.warnn = props.flash.warning
    }
});



const data = reactive({
    UniversidadSelect: null,
    isOpen: false,
    tooltipSettings: {
        content: '',
        shown: false, // Always show the tooltip
        triggers: [], // No triggers to hide the tooltip
    },
    tooltipSettings2: {
        content: '',
        shown: false, // Always show the tooltip
        triggers: [], // No triggers to hide the tooltip
    },
})
const showTooltip = () => {
    data.isOpen = true; // Function to show the tooltip
    console.log("🧈 debu data.isOpen:", data.isOpen);
}


const form = useForm({
    archivo1: null,
    archivo2_matricular: null,
    universidadID: 0,
});

watchEffect(() => {
    // form_carreras.archivo_componente_carreras
})

function uploadFileestudiantes() {
    form.post(route('user.uploadEstudiantes'), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            form.reset()
            // data.respuesta = $page.props.flash.success
        },
        onError: () => null,
        onFinish: () => null,
    });
}

function uploadestudiantesUniversidad() {
    console.log("🧈 debu form.universidadID:", form.universidadID);
    if (form.universidadID === null || form.universidadID == 0) {
        data.tooltipSettings2.content = 'Seleccione universidad'
    } else {
        form.post(route('user.uploadUniversidad'), {
            preserveScroll: true,
            onSuccess: () => {
                // emit("close")
                form.reset()
                // data.respuesta = $page.props.flash.success
            },
            onError: () => null,
            onFinish: () => {
                data.tooltipSettings2.shown = false

            },
        });
    }
}

data.UniversidadSelect = vectorSelect(data.UniversidadSelect, props.UniversidadSelect, 'una')
// const downloadExcel = () => { window.open('users/export/' + form.quincena + '/' + (form.fecha_ini.month) + '/' + form.fecha_ini.year, '_blank') }
</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">

            <div v-if="$page.props.flash.warning" class="px-2 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <div class="flex max-w-screen-xl shadow-lg rounded-lg">
                        <div class="bg-yellow-600 py-4 px-2 rounded-l-lg flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="fill-current text-white"
                                width="20" height="20">
                                <path fill-rule="evenodd"
                                    d="M8.22 1.754a.25.25 0 00-.44 0L1.698 13.132a.25.25 0 00.22.368h12.164a.25.25 0 00.22-.368L8.22 1.754zm-1.763-.707c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0114.082 15H1.918a1.75 1.75 0 01-1.543-2.575L6.457 1.047zM9 11a1 1 0 11-2 0 1 1 0 012 0zm-.25-5.25a.75.75 0 00-1.5 0v2.5a.75.75 0 001.5 0v-2.5z">
                                </path>
                            </svg>
                        </div>
                        <div
                            class="px-8 py-6 bg-white rounded-r-lg flex justify-between items-center w-full border border-l-transparent border-gray-200">
                            <div>
                                {{ $page.props.flash.warning ? $page.props.flash.warning : '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section class="text-gray-600 body-font">
                    <div class="container px-1 py-12 mx-auto">
                        <div v-if="can(['create user'])" class="flex flex-wrap -m-4">
                            <!-- user estudiantes -->
                            <div class="p-2 lg:w-1/2 w-full xl:w-1/3">
                                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                    <DocumentArrowUpIcon class="mt-5 h-12 lg:h-24 w-full object-cover object-center" />

                                    <div class="p-3">
                                        <h3 class="title-font text-lg font-medium text-gray-900 mb-3">Subir estudiantes</h3>
                                        <p class="leading-relaxed mb-3"> Este formulario solo permite cargar estudiantes</p>

                                        <form @submit.prevent="uploadFileestudiantes" id="upload"
                                            class="flex flex-col text-center">
                                            <div class="w-full">
                                                <div class="mt-6">
                                                    <label v-if="form.archivo1" for="fileInput"
                                                        class="bg-sky-300 text-white font-bold py-2 px-4 rounded">
                                                        Seleccionar Archivo
                                                    </label>
                                                    <label v-else for="fileInput"
                                                        class="bg-sky-500 text-white font-bold py-2 px-4 rounded">
                                                        Seleccionar archivo
                                                    </label>
                                                    <input id="fileInput" type="file"
                                                        @input="form.archivo1 = $event.target.files[0]"
                                                        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                                        class="opacity-0 relative inset-0 w-full my-1 cursor-pointer" />
                                                    <p v-if="form.archivo1" class="w-full my-2 text-green-600">
                                                        {{ form.archivo1.name }}</p>
                                                </div>
                                                <progress v-if="form.progress" :value="form.progress.percentage" max="100"
                                                    class="bg-sky-200">
                                                    {{ form.progress.percentage }}%
                                                </progress>
                                            </div>

                                            <div class="w-auto">
                                                <!-- <VDropdown :triggers="['none']" v-tooltip="data.tooltipSettings"></VDropdown> -->
                                                <Button v-show="can(['create user'])" :disabled="form.archivo1 == null"
                                                    :class="{ 'bg-sky-500': form.archivo1 !== null }"
                                                    class="w-32 rounded-none my-4 px-4 py-2 text-white">
                                                    {{ lang().button.subir }}
                                                </Button>
                                            </div>
                                        </form>



                                        <h2 class="text-xl text-gray-900 dark:text-white">El formato necesita las siguientes
                                            columnas</h2>
                                        <ul class="list-decimal my-6 mx-5">
                                            <li class="text-lg">Nombre</li>
                                            <li class="text-lg">Correo: <small class="text-lg">Cada correo debe ser
                                                    unico</small></li>
                                            <li class="text-lg">Identificacion: <small class="text-lg">Debe ser un
                                                    numero</small></li>
                                            <li class="text-lg">Sexo: <small class="text-lg">Femenino, masculino u
                                                    otro</small></li>
                                            <li class="text-lg">Fecha de nacimiento</li>
                                            <li class="text-lg">Semestre</li>
                                            <li class="text-lg">Nivel: <small class="text-lg">Primaria, bachillerato,
                                                    tecnologia, profesional,especializacion,maestría,doctorado</small></li>
                                            <li class="text-lg">Cargo: <small class="text-lg">estudiante, profesor</small>
                                            </li>
                                        </ul>

                                        <div class="flex items-center flex-wrap my-6">
                                            <a class="text-gray-500 inline-flex items-center md:mb-2 lg:mb-0">Numero de
                                                Usuarios: </a>
                                            <span
                                                class="text-gray-400 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1 border-r-2 border-gray-200">
                                                <svg class="w-1 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                {{ props.numUsuarios }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- user_universidad -->
                            <div class="p-4 lg:w-1/2 w-full xl:w-1/3">
                                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                    <DocumentArrowUpIcon class="mt-5 h-12 lg:h-20 w-full object-cover object-center" />

                                    <div class="p-6">
                                        <h3 class="title-font text-lg font-medium text-gray-900 mb-3">Matricular estudiantes </h3>
                                        <p class="leading-relaxed mb-3">Seleccione la universidad de los estudiantes</p>

                                        <form @submit.prevent="uploadestudiantesUniversidad" id="upload2">
                                            <div>
                                                <InputLabel for="universidadID" :value="lang().label.carrera" />
                                                <SelectInput id="universidadID" class="mt-1 block w-full"
                                                    v-model="form.universidadID" required :dataSet="data.UniversidadSelect">
                                                </SelectInput>
                                                <InputError class="mt-2" :message="form.errors.universidadID" />
                                            </div>
                                            <div class="mt-6">
                                                <label v-if="form.archivo2_matricular" for="file_matricular_user"
                                                    class="bg-sky-300 text-white font-bold py-2 px-4 rounded">
                                                    Seleccionar Archivo
                                                </label>
                                                <label v-else for="file_matricular_user"
                                                    class="bg-sky-500 text-white font-bold py-2 px-4 rounded">
                                                    Seleccionar archivo
                                                </label>
                                                <input id="file_matricular_user" type="file"
                                                    @input="form.archivo2_matricular = $event.target.files[0]"
                                                    accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                                    class="opacity-0 relative inset-0 w-full my-1 cursor-pointer" />
                                                <p v-if="form.archivo2_matricular" class="w-full my-2 text-green-600">
                                                    {{ form.archivo2_matricular.name }}</p>
                                            </div>

                                            <progress v-if="form.progress" :value="form.progress.percentage" max="100"
                                                class="bg-sky-200 my-2">
                                                {{ form.progress.percentage }}%
                                            </progress>

                                            <div v-if="form.archivo2_matricular" class="w-auto">
                                                <Button v-show="can(['create user'])"
                                                    :disabled="form.archivo2_matricular == null"
                                                    :class="{ 'bg-sky-500': form.archivo2_matricular !== null }"
                                                    class="w-32 rounded-none my-4 px-4 py-2 text-white">
                                                    {{ lang().button.subir }} archivo
                                                </Button>
                                            </div>
                                            <div class="w-auto text-red-600 text-lg my-4">
                                                {{ data.tooltipSettings2.content }}
                                            </div>
                                        </form>

                                        <h2 class="text-xl text-gray-900 dark:text-white">El formato requiere las siguientes
                                            columnas</h2>
                                        <ul class="list-decimal my-4 mx-5">
                                            <li class="text-lg">usuario: del estudiante a inscribir</li>
                                            <li class="text-lg">codigo de la carrera</li>
                                            <li class="text-lg">codigo de la materia</li>
                                        </ul>

                                        <div class="p-4 max-w-md mx-auto">
                                            <p class="">Ejemplo:</p>
                                            <div class="relative overflow-hidden">
                                                <img src="/EXCELmatricularEstudiantes.png" alt="imagen excel matriculas"
                                                    class="rounded-lg transition-transform duration-500 transform hover:scale-110">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="p-4 lg:w-1/2 w-full xl:w-1/3">
                                <SubirCarreras
                                 :UniversidadSelect="data.UniversidadSelect"
                                 :flash="props.flash"
                                  />
                            </div>
                            <!-- materias masivo -->
                        </div>
                    </div>
            </section>
        </div>
    </div>
</AuthenticatedLayout></template>
