<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { reactive, watch, onMounted, ref } from 'vue';
import pkg from 'lodash';
import { ChevronUpIcon, ChevronUpDownIcon, ArrowSmallRightIcon, PencilIcon, TrashIcon, UserCircleIcon } from '@heroicons/vue/24/solid';

import Back from '@/Components/uiverse/BackButton.vue';

import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { PrimerasPalabras, vectorSelect, formatDate, CalcularEdad, CalcularSexo } from '@/global.ts';

import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue'
const isOpen = ref(false)
function closeModal() { isOpen.value = false }

const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    breadcrumbs: Object,
    user: Object,
    roles: Object,
    medida: Object,
    numberPermissions: Number,
    countmedida: Number,
    subtopicos: Object,
})

const data = reactive({
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    generico: null,
})

const FirstElements = (Colection) => {
    let tamano = Colection.length
    if(tamano > 3){
        return Colection.slice(0,3)
    }else{
        return Colection
    }
}

onMounted(() => { })
</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>

        <section class="text-gray-600 body-font">
            <div class="container px-5 py-5 mx-auto">
                <div class="flex flex-col text-center w-full mb-12">
                    <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900"> {{props.user.name}} </h1>
                    <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                        Registro de estudio del estudiante
                    </p>
                    <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                        El estudiante ha hecho {{ props.countmedida }} consultas
                    </p>
                </div>


                <!-- seeing -->
                <!-- <p v-for="ele in props.medida">
                    {{ ele }}

                    <br>
                    <br>
                    {{ ele[0].pregunta }}
                    <br>
                    <br>
                </p> -->
                <!-- {{ props.medida }}   -  - -  -->

                <!-- unidads -->
                <div class="flex flex-wrap -m-2">

                    <!-- <div v-if="props.countmedida" v-for="medidx in props.medida" class="p-2 xl:w-1/3 md:w-1/2 w-full">
                        De <b>{{ medidx[0].subtopico.nombre }}</b> se hicieron {{medidx.length}} consultas:
                        <ol class="p-1 list-decimal">
                            <li v-for="medidaControl in FirstElements(medidx)" class="my-1">
                                <p>{{ PrimerasPalabras(medidaControl.respuesta_guardada,20) }}</p>
                                <p>{{ medidaControl.RazonNOSubtopico != null ? medidaControl.RazonNOSubtopico : '' }}</p>
                            </li>
                            <p  v-if="medidx.length > 3" class="font-bold">Muchas mas...</p>
                        </ol>
                    </div> -->

                     <div v-if="props.countmedida" v-for="medidx in props.medida" class="p-2 xl:w-1/4 lg:w-1/3 md:w-1/2 w-full">
                            <Disclosure as="div" class="mt-2" v-slot="{ open }">
                                <DisclosureButton
                                    class="flex w-full justify-between rounded-lg bg-sky-100 px-4 py-2 text-left text-sm font-medium
                                    text-sky-900 hover:bg-blue-200 focus:outline-none focus-visible:ring focus-visible:ring-sky-500 focus-visible:ring-opacity-75">
                                    <div class="h-full flex items-center border-gray-200 border p-4 rounded-lg">
                                        <div class="flex-grow">
                                            <h2 v-if="medidx[0].subtopico" class="text-gray-900 title-font font-medium">
                                                De <b>{{ medidx[0].subtopico.nombre }}</b> se hicieron {{medidx.length}} consultas:
                                            </h2>
                                        </div>
                                    </div>
                                    <ChevronUpIcon :class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 text-sky-500" />
                                </DisclosureButton>

                                <DisclosurePanel class="px-4 pt-4 pb-2 text-sm text-gray-500">
                                    <ol class="p-1 list-decimal">
                                        <li v-for="medidaControl in FirstElements(medidx)" class="my-1">
                                            <p>{{ PrimerasPalabras(medidaControl.respuesta_guardada,20) }}</p>
                                            <p>{{ medidaControl.RazonNOSubtopico != null ? medidaControl.RazonNOSubtopico : '' }}</p>
                                        </li>
                                        <p  v-if="medidx.length > 3" class="font-bold">Muchas mas...</p>
                                    </ol>
                                </DisclosurePanel>
                            </Disclosure>
                        </div>
                </div>
            </div>
            <Back :ruta="'VerTiemposEstudiantes'" class="text-center mt-6"/>
        </section>
    </AuthenticatedLayout>
</template>
