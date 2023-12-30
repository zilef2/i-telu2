<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, onMounted, ref } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { router, usePage, Link } from '@inertiajs/vue3';

import Pagination from '@/Components/Pagination.vue';
import { ChevronUpDownIcon, ArrowSmallRightIcon, PencilIcon, TrashIcon, UserCircleIcon } from '@heroicons/vue/24/solid';

import Create from '@/Pages/pendiente/Create.vue';
import Edit from '@/Pages/pendiente/Edit.vue';
import Delete from '@/Pages/pendiente/Delete.vue';
import {TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle,} from '@headlessui/vue';

import { number_format,formatDate } from '@/global.ts';
import InfoButton from "@/Components/InfoButton.vue";
const isOpen = ref(false)
function closeModal() { isOpen.value = false }

const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    fromController: Object,
    // PapaSelect: Object,
    nombresTabla: Array,
    numberPermissions: Number,

})

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        pasaitos: props.filters.pasaitos,
        perPage: props.perPage,
        selectedUniID: props.filters.selectedUniID,
    },
    params2: {
        // selectedUni:0,
        selectedcarr:0,
    },
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    generico: null,
    dataSet: usePage().props.app.perpage,
})

const order = (field) => {
    if(field)
        data.params.field = field.replace(/ /g, "_")

    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("pendiente.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.fromController?.data.forEach((generico) => {
            data.selectedId.push(generico.id)
        })
    }
}
const select = () => {
    if (props.fromController?.data.length === data.selectedId.length) {
        data.multipleSelect = true
    } else {
        data.multipleSelect = false
    }
}

onMounted(() => {
})

</script>

<template>
    <Head :title="props.title"></Head>

    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton class="rounded-none" @click="data.createOpen = true" v-if="can(['create pendiente'])">
                        {{ lang().button.add }}
                    </PrimaryButton>
                    <PrimaryButton class="rounded-none" @click="data.params.pasaitos = !data.params.pasaitos">
                        Buscar Peticiones de +2 meses
                    </PrimaryButton>
                    <Create :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        v-if="can(['create pendiente'])" :PapaSelect="PapaSelect" />
                    <Edit :show="data.editOpen" @close="data.editOpen = false" :pendiente="data.generico" :title="props.title"
                        v-if="can(['update pendiente'])" :PapaSelect="PapaSelect" />
                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :pendiente="data.generico"
                        v-if="can(['delete pendiente'])" :title="props.title" />
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" id="perpag"/>
                        <DangerButton @click="data.deleteBulkOpen = true" v-show="data.selectedId.length != 0"
                            class="px-3 py-1.5" v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton>
                    </div>

                    <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search" type="text" id="buscar"
                        class="block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg" :placeholder="lang().placeholder.search" />
                </div>


                <!-- --  -- -- tabla --  -- --  -->
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900 text-left">
                                <th class="px-8 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex items-center text-center">
                                        <span class="text-center">Accion</span>
                                    </div>
                                </th>
                                <th v-for="(titulos, indiceN) in nombresTabla[0]" :key="indiceN"
                                    v-on:click="order(nombresTabla[2][indiceN])"
                                    class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div v-if="nombresTabla[2][indiceN] !== null" class="flex items-center">
                                        <span class="text-center">{{ titulos }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                    <div v-else class="flex items-center text-center">
                                        <span class="text-center">{{ titulos }}</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(clasegenerica, index) in fromController.data" :key="index"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20"
                                :class="index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-800' : ''">

                                <td v-if="numberPermissions > 2" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div class="flex justify-start items-center ml-6">
                                        <div class="rounded-md overflow-hidden">
                                            <Link :href="route('pendiente.AceptarUsers', clasegenerica.id)">
                                                <InfoButton type="button" class="py-1.5 rounded-none"
                                                            v-tooltip="lang().label.aceptar">
                                                    <UserCircleIcon class="w-7 h-7 px-0.5" />
                                                </InfoButton>
                                            </Link>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica?.nombre_user) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ formatDate(clasegenerica?.fecha_peticion) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ formatDate(clasegenerica?.fecha_aprovacion) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ number_format(clasegenerica?.valorTotal,0,true) }} </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-betwween items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.fromController" :filters="data.params" />
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
                                        Sin Asignaturas
                                    </DialogTitle>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Esta pendiente no tiene Asignaturas/materias inscritas
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
    </AuthenticatedLayout>
</template>
