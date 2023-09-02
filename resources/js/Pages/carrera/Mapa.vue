<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { reactive, watch, onMounted, ref } from 'vue';
import pkg from 'lodash';
import { ChevronUpIcon, ChevronUpDownIcon, ArrowSmallRightIcon, PencilIcon, TrashIcon, UserCircleIcon } from '@heroicons/vue/24/solid';

import Back from '@/Components/uiverse/BackButton.vue';

import { PrimerasPalabras, vectorSelect, formatDate, CalcularEdad, CalcularSexo } from '@/global.ts';
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'

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
    Carrera: Object,
    Unidades: Object,
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

onMounted(() => {
})

// const PapaSelect = props.PapaSelect?.map(universidad => ({
//     label: universidad.nombre, value: universidad.id
// }))

</script>

<template>
    <Head :title="props.title"></Head>

    <AuthenticatedLayout>

        <section class="text-gray-600 body-font">
            <div class="container px-5 py-5 mx-auto">
                <div class="flex flex-col text-center w-full mb-20">
                    <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900"> {{props.Carrera.nombre}} </h1>
                    <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                        Visualizar las asignaturas asociadas a la carera 
                    </p>
                </div>
                <!-- unidads -->
                <div class="flex flex-wrap -m-2">
                    <div v-if="props.Carrera.materias_enum.length" v-for="materia in props.Carrera.materias_enum" class="p-2 lg:w-1/3 md:w-1/2 w-full">
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton
                                class="flex w-full justify-between rounded-lg bg-sky-100 px-4 py-2 text-left text-sm font-medium
                                text-sky-900 hover:bg-green-200 focus:outline-none focus-visible:ring focus-visible:ring-sky-500 focus-visible:ring-opacity-75">
                                <div class="h-full flex items-center border-gray-200 border p-4 rounded-lg">
                                    <div class="flex-grow">
                                        <h2 class="text-gray-900 title-font font-medium">{{ materia.enum }} {{ materia.nombre }}</h2>
                                        <p v-if="materia.unidads.length == 0" class="text-gray-500">sin unidades</p>
                                        <p v-if="materia.unidads.length == 1" class="text-gray-500">{{materia.unidads.length}} unidad</p>
                                        <p v-if="materia.unidads.length > 1" class="text-gray-500">{{materia.unidads.length}} unidades</p>
                                    </div>
                                </div>
                                <ChevronUpIcon :class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 text-sky-500" />
                            </DisclosureButton>

                            <DisclosurePanel class="px-4 pt-4 pb-2 text-sm text-gray-500">
                                <ol v-if="materia.unidads.length" class="list-decimal">
                                    <li  v-for="uni in materia.unidads" class="text-lg ml-4">
                                        {{uni.nombre}}
                                    </li>
                                </ol>
                            </DisclosurePanel>
                        </Disclosure>

                    </div>
                </div>
            </div>
            <Back :ruta="'carrera.index'" class="text-center"/>
        </section>
    </AuthenticatedLayout>
</template>
