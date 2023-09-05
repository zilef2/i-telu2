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
import { router, usePage } from '@inertiajs/vue3';

import Pagination from '@/Components/Pagination.vue';
import { ChevronUpDownIcon, PencilIcon, TrashIcon, ArrowSmallRightIcon } from '@heroicons/vue/24/solid';

import Create from '@/Pages/Unidad/Create.vue';
import Edit from '@/Pages/Unidad/Edit.vue';
import Delete from '@/Pages/Unidad/Delete.vue';
import DeleteBulk from '@/Pages/Unidad/DeleteBulk.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';

import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue'
const isOpen = ref(false)
function closeModal() { isOpen.value = false }

import { vectorSelect, formatDate, CalcularEdad, CalcularSexo }from '@/global.ts';;


const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    fromController: Object,
    nombresTabla: Array,
    MateriasSelect: Object,
    numberPermissions: Number,
    selectedMatID: Object,
    MateriasSelect: Object,
    showCarrera: Object,
})

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
        selectedMatID: props.filters.selectedMatID,

    },
    params2: {
        selectedUnidadID: 0
    },
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    generico: null,
    MateriasSelect: null,
    dataSet: usePage().props.app.perpage,
})

const IrTemas = (carreraid, cuantas) => {
    if (cuantas > 0) {

        data.params2.selectedUnidadID = carreraid
        let params = pickBy(data.params2)
        router.get(route("subtopico.index"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        })
    } else {
        isOpen.value = true
    }
}


const order = (field) => {
    console.log("🧈 debu field:", field);

    data.params.field = field.replace(/ /g, "_")

    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("Unidad.index"), params, {
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
    if (props.fromController?.data.length == data.selectedId.length) {
        data.multipleSelect = true
    } else {
        data.multipleSelect = false
    }
}
onMounted(() =>{
    if(typeof data.params.selectedMatID === 'undefined') data.params.selectedMatID = 0
    if(data.params.selectedMatID === null) data.params.selectedMatID = 0

    data.MateriasSelect = vectorSelect(data.MateriasSelect,props.MateriasSelect,'una materia')
})

// data.MateriasSelect = vectorSelect(data.MateriasSelect, props.MateriasSelect, 'una')

</script>

<template>
    <Head :title="props.title"></Head>

    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton class="rounded-none" @click="data.createOpen = true" v-show="can(['create Unidad'])">
                        {{ lang().button.add }}
                    </PrimaryButton>
                    <Create :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        v-if="can(['create Unidad'])" :MateriasSelect="data.MateriasSelect" />
                    <Edit :show="data.editOpen" @close="data.editOpen = false" :Unidad="data.generico" :title="props.title"
                        v-if="can(['update Unidad'])" :MateriasSelect="data.MateriasSelect" />
                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :Unidad="data.generico"
                        v-if="can(['delete Unidad'])" :title="props.title" />
                    <DeleteBulk :show="data.deleteBulkOpen"
                        @close="data.deleteBulkOpen = false, data.multipleSelect = false, data.selectedId = []"
                        :selectedId="data.selectedId" :title="props.title" />
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" />
                        <DangerButton @click="data.deleteBulkOpen = true" v-show="data.selectedId.length != 0"
                            class="px-3 py-1.5" v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton>
                        <div class="bg-gray-100">
                            <SelectInput v-model="data.params.selectedMatID" id="uni" :dataSet="data.MateriasSelect" />
                            <small v-if="showCarrera" class="mx-3 bg-white dark:bg-black"> {{ showCarrera.nombre }}</small>
                        </div>
                    </div>
                    <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search" type="text"
                        class="block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg" :placeholder="lang().placeholder.search" />
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900 text-left">
                                <th class="px-2 py-4 text-center">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th>
                                <th v-for="(titulos, indiceN) in nombresTabla[0]" :key="indiceN"
                                    v-on:click="order(nombresTabla[2][indiceN])"
                                    class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div v-if="nombresTabla[2][indiceN] !== null" class="flex justify-between items-center">
                                        <span>{{ titulos }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                    <div v-else class="flex justify-between items-center">
                                        <span>{{ titulos }} </span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(clasegenerica, index) in fromController.data" :key="index"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                    <input type="checkbox" @change="select" :value="clasegenerica.id"
                                        v-model="data.selectedId"
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary" />
                                </td>
                                <td v-if="numberPermissions > 1" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div class="flex justify-start items-center">
                                        <div class="rounded-md overflow-hidden">
                                            <InfoButton type="button"
                                                @click="(data.editOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                                <PencilIcon class="w-4 h-4" />
                                            </InfoButton>
                                            <DangerButton type="button"
                                                @click="(data.deleteOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.delete">
                                                <TrashIcon class="w-4 h-4" />
                                            </DangerButton>
                                            <InfoButton type="button"
                                                @click="IrTemas(clasegenerica.id, clasegenerica.cuantosTemas)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="'Ver temas'">
                                                <ArrowSmallRightIcon class="w-4 h-4" />
                                            </InfoButton>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.enum) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.nombre) }} </td>
                                <!-- <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.codigo) }} </td> -->
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.hijo) }} </td>
                                <!-- <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.descripcion) }} </td> -->
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
                                        Sin temas
                                    </DialogTitle>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Esta unidad no tiene temas inscritos
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
