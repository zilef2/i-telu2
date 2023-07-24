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
import { ChevronUpDownIcon, PencilIcon, EyeIcon, TrashIcon } from '@heroicons/vue/24/solid';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import { useForm,router } from '@inertiajs/vue3';
import {sinTildes, ReemplazarTildes} from '@/global.js';


const { _, debounce, pickBy } = pkg


const props = defineProps({
    nuevaPregunta: String,
    respuesta: String,
    filters: Object,

    materia: Object,
    title: String,
    restarAlToken: Number,
    tresEjercicios: Object,
    breadcrumbs: Object,
    limite: Number,
    nivel: String,
    consumio: Number,
})

const data = reactive({
    params: {
        materiaid: props.materia?.id,
        pregunta: '',
        nivel: '',
        restarAlToken:props.restarAlToken
    },
})

// chatgpt form
const form = useForm({
    materia: props.materia,
    pregunta: props.nuevaPregunta,
    restarAlToken: data.params?.restarAlToken + ' Tokens',
    nivel: props.nivel? props.nivel : 'Universitario',
    respuestagpt: props.respuesta
});

watchEffect(() => {
    console.log("🧈 form ", form.restarAlToken);
})
// watch(() => _.cloneDeep(data.params), debounce(() => {
//     let params = pickBy(data.params)
//     router.post(route("materia.masPreguntasPost"), params, {
//         replace: true,
//         preserveState: true,
//         preserveScroll: true,
//     })
// }, 800))


onMounted(() => {
});

const IrPreguntas = () => {
    data.params.pregunta = sinTildes(ReemplazarTildes(form.pregunta))
    data.params.nivel = form.nivel

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
        <div class="">
            <div class="p-2 w-full">
                <!-- <button
                    class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                    {{ form.processing ? 'Espere...' : '' }}
                </button> -->
            </div>

            <section class="text-gray-600 body-font relative">
                <div class="container px-5 py-2 mx-auto">
                    <div class="flex flex-col text-center w-full mb-8">
                        <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">Preguntas generales de <b>{{ props.materia.nombre }}</b></h1>
                        <!-- <p class="lg:w-2/3 mx-auto leading-relaxed text-base"> {{ subtopicoSelec }} </p> -->
                    </div>
                    <div class="w-full mx-auto">
                        <form @submit.prevent="IrPreguntas">
                            <div class="flex flex-wrap -m-2">
                                <div class="p-2 w-1/2">
                                    <div class="relative">
                                        <label for="name" class="leading-7 text-sm text-gray-600">Objetivo</label>
                                        <p v-if="props.materia.TodosObjetivos != ''"
                                            class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-800 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ props.materia.TodosObjetivos }}
                                        </p>
                                        <p v-else
                                            class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-800 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            Sin objetivos
                                        </p>
                                        <!-- <SelectInput v-model="nivel" :dataSet="props.objetivosCarrera" class="mt-1 block w-full" /> -->
                                    </div>
                                </div>

                                <div class="p-2 w-1/4">
                                    <label for="price"
                                        class="block text-sm font-medium leading-6 text-gray-900">Token restantes </label>
                                    <div class="relative mt-2 rounded-md shadow-sm">
                                        <input disabled type="text" name="price" id="price" :value="props.limite"
                                            class="block w-full rounded-md border-0 pl-4 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                    </div>
                                </div>
                                <div class="p-2 w-1/4">
                                    <label for="price"
                                        class="block text-sm font-medium leading-6 text-gray-900">Token restantes </label>
                                    <div class="relative mt-2 rounded-md shadow-sm">
                                        <select id="currency" name="currency" v-model="form.nivel"
                                        class="block w-full rounded-md border-0 pl-4 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option value="Bachiller" >Bachiller</option>
                                            <option selected value="Universitario" >Universitario</option>
                                            <option value="Maestria" >Maestria</option>
                                            <option value="Doctor" >Doctor</option>
                                        </select>
                                            
                                    </div>
                                </div>

                                <div class="p-2 w-full">
                                    <div class="relative">
                                        <label for="pregunta" class="leading-7 text-sm text-gray-600">Pregunta</label>
                                        <input type="text" name="price" id="price" v-model="form.pregunta"
                                            class="block w-full rounded-md border-0 py-1.5 pl-2 pr-20 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            placeholder="Pregunte con fines academicos" />
                                    </div>
                                </div>
                                <div v-show="props.respuestagpt != ''" class="p-2 w-full h-full">
                                    <div class="relative h-full">
                                        <label for="message" class="leading-7 text-sm text-gray-600">Respuesta </label>
                                        <textarea disabled v-model="form.respuestagpt" id="message" name="message" rows="6"
                                            cols="35"
                                            class="h-auto resize-none w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 
                                            text-base outline-none text-gray-700 py-1 px-3 leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                    </div>
                                </div>
                                <div class="p-2 w-full">
                                    <div class="relative">
                                        <label for="restarAlToken" class="leading-7 text-sm text-gray-600">Esta pregunta consumio</label>
                                        <input type="text" name="price" id="price" v-model="form.restarAlToken"
                                            class="block w-full rounded-md border-0 py-1.5 pl-2 pr-20 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            placeholder="Normalmente se consume 1 token" />
                                    </div>
                                </div>


                                <!-- v-if="props.soloEjercicios != 'Sin sugerencias' && props.soloEjercicios != ''" -->
                                <!-- <div class="p-2 w-full h-full">
                                    <label for="message" class="leading-7 text-sm text-gray-600">Sugerencias: </label>
                                    <div v-for="(ejercicioExtra, Jindex) in props.soloEjercicios" :key="Jindex"
                                        class="relative w-full">
                                        <p v-if="Jindex != 0"
                                            @click="submitToGPT(props.temaSelec, props.subtopicoSelec, ejercicioExtra)"
                                            class="w-full bg-gray-100 cursor-pointer bg-opacity-50 rounded-md border-b-2 border-indigo-400 focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ ejercicioExtra }}
                                        </p>
                                    </div>
                                </div> -->
                                <div class="p-2 w-full">
                                    <button @click="IrPreguntas" class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                                        {{ form.processing ? 'Espere...' : 'Preguntar' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

            </div>
        </section>
    </div>
</AuthenticatedLayout></template>
<style>textarea {
    @apply px-3 py-2 border border-gray-300 rounded-md;
}</style>
