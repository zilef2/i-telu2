<script setup>
import { reactive, watch, onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { router, usePage, Link } from '@inertiajs/vue3';

import Pagination from '@/Components/Pagination.vue';
import { ChevronUpDownIcon, EyeIcon, PencilIcon, TrashIcon, UserCircleIcon, ArrowSmallRightIcon } from '@heroicons/vue/24/solid';


import Create from '@/Pages/Plan/Create.vue';
import Edit from '@/Pages/Plan/Edit.vue';
import Delete from '@/Pages/Plan/Delete.vue';

import Checkbox from '@/Components/Checkbox.vue';
// import InfoButton from '@/Components/InfoButton.vue';
import GroupButtonsIndex from '@/Components/uiverse/GroupButtonsIndex.vue';
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
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    fromController: Object,
    numberPermissions: Number,
})



const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    generico: null,
    successMessage: '',
    dataSet: usePage().props.app.perpage,
    params2: {
        selectedUniID: 0,
    }

})

const order = (field) => {
    console.log("🧈 debu field:", field);
    data.params.field = field.replace(/ /g, "_")

    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("Plan.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

const irCarrera = (carreraid, cuantas) => {
    if (cuantas > 0) {

        data.params2.selectedUniID = carreraid
        let params = pickBy(data.params2)
        router.get(route("carrera.index"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        })
    } else {
        isOpen.value = true
    }
}

const Beneficios = [
    'Acceso a nuevas instrucciones, Quices para sus estudiantes',
    'Ejercicios personalizados',
    'Modulo de Resolucion y mejora'
];
const BeneVector = [
    [],
    [Beneficios[0]],
    [Beneficios[0], Beneficios[1]],
    [Beneficios[0], Beneficios[1], Beneficios[2]],
]

</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
            </div>
            <div class="bg-white py-6 sm:py-8 lg:py-12">
                <div class="mx-auto max-w-screen-xl px-4 md:px-8">
                    <h2 class="mb-4 text-center text-2xl font-bold text-gray-800 md:mb-8 lg:text-3xl xl:mb-12">Planes disponibles</h2>

                    <div class="mb-6 grid gap-2 grid-cols-1 sm:grid-cols-2 md:mb-8 md:grid-cols-3 lg:gap-8">
                        <!-- plan - start -->
                        <div
                            v-for="plan in fromController" :key="plan.id"
                            class="flex flex-col overflow-hidden rounded-lg border sm:mt-8 hover:bg-gray-50"
                            :class="{'border-sky-700' : plan.id == 2}">
                            <div class="h-2 bg-pink-500"></div>

                            <div class="flex flex-1 flex-col p-6 pt-8">
                                <div class="mb-12">
                                    <div class="mb-2 text-center text-2xl font-bold text-gray-800">{{ plan.nombre }}</div>

                                    <p class="mb-8 px-8 text-center text-gray-500">{{ plan.tipo}}</p>

                                    <div v-for="ben in BeneVector[plan.id]" class="space-y-4">
                                        <!-- check - start -->
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 16 16"> <circle cx="8" cy="8" r="8" fill="currentColor" class="text-gray-300" /> <circle cx="8" cy="8" r="3" fill="currentColor" class="text-gray-500" /> </svg>
                                            <p  class="text-gray-600 text-justify">{{ ben }}</p>
                                        </div>
                                        <!-- check - end -->


                                        <!-- check - start -->
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 16 16"> <circle cx="8" cy="8" r="8" fill="currentColor" class="text-gray-300" /> <circle cx="8" cy="8" r="3" fill="currentColor" class="text-gray-500" /> </svg>
                                            <span class="text-gray-600">Soporte en todo momento</span>
                                        </div>
                                        <!-- check - end -->
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    <a href="#" class="block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base">
                                        $ {{ plan.valor }} 000 / Mes
                                    </a>
                                </div>
                                <div v-if="plan.id == 2" class="mt-auto my-8">
                                    <a href="#"
                                        class="block rounded-lg bg-indigo-400 px-8 py-1 text-center text-sm font-semibold text-white outline-none ring-indigo-800 transition duration-100 hover:bg-gray-300 focus-visible:ring active:text-gray-700">
                                        Mejor Oferta!
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- plan - end -->

                    </div>
                </div>
            </div>
        </div>

        <template>
            <TransitionRoot appear :show="isOpen" as="template">
                <Dialog as="div" @close="closeModal" class="relative z-10">
                    <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0"
                        enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                        <div class="fixed inset-0 bg-black bg-opacity-25" />
                    </TransitionChild>

                    <div class="fixed inset-0 overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center">
                            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                                enter-to="opacity-100 scale-100" leave="duration-200 ease-in"
                                leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                                <DialogPanel
                                    class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                                    <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                                        Sin carreras
                                    </DialogTitle>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Esta Plan no tiene carreras inscritas
                                    </p>
                                </div>

                                <div class="mt-4">
                                    <button type="button"
                                        class="inline-flex justify-center rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                        @click="closeModal">
                                        Listo
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </template>


</AuthenticatedLayout></template>
