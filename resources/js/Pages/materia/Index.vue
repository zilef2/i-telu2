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
import { router, usePage, Link } from '@inertiajs/vue3';

import Pagination from '@/Components/Pagination.vue';
import { CursorArrowRippleIcon, ChevronUpDownIcon, EyeIcon, PencilIcon, TrashIcon, UserGroupIcon } from '@heroicons/vue/24/solid';

import Create from '@/Pages/materia/Create.vue';
import Edit from '@/Pages/materia/Edit.vue';
import Delete from '@/Pages/materia/Delete.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import { useForm } from '@inertiajs/vue3';
import {vectorSelect, formatDate, CalcularEdad, CalcularSexo} from '@/global.js';

const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    fromController: Object,
    nombresTabla: Array,
    respuest: String,
    errorMessage: String,
    carrerasSelect: Object,
    MateriasRequisitoSelect: Object,
    UniversidadSelect: Object,
})


const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        selectedUni: props.filters.selectedUni,
        perPage: props.perPage,
        selectedcarr: props.filters.selectedcarr
        
    },
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    generico: null,
    dataSet: usePage().props.app.perpage,

    UniversidadSelect: [], //para filtro (index)
    carrerasDeUSel: [], //para filtro (index) y carrera_id (create)
    MateriasRequisitoSelect: [],
    numeroCarreras: 0,
})
const order = (field) => {
    data.params.field = field.replace(/ /g, "_")

    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}
watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("materia.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 100))

watchEffect(() => {
    // data.numeroCarreras = ((Object.keys(props.carrerasSelect)).length)
    // console.log(data.numeroCarreras)
    // if(data.numeroCarreras > 0){
        data.carrerasDeUSel = props.carrerasSelect?.map(
            carrera => (
                { label: carrera.nombre, value: carrera.id }
            )
        )
        data.carrerasDeUSel.unshift({label: 'Seleccione carrera', value:0})
    // }
})
// console.log(data.params.carrerasDeUSel)
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
    if(data.params.selectedcarr === null) data.params.selectedcarr = 0
    data.UniversidadSelect = vectorSelect(data.UniversidadSelect,props.UniversidadSelect,'una')
    data.carrerasDeUSel = vectorSelect(data.carrerasDeUSel,props.carrerasSelect,'una')
    // data.MateriasRequisitoSelect = vectorSelect(data.MateriasRequisitoSelect,props.MateriasRequisitoSelect,'una')
    console.log("🧈 debu data.MateriasRequisitoSelect:", data.MateriasRequisitoSelect);
})
</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />

        <div v-if="errorMessage" class="">
            <p class="text-xl text-red-500 bg-red-100" v-html="errorMessage"></p>
        </div>
        <div v-else class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton class="rounded-none" @click="data.createOpen = true">
                        {{ lang().button.add }}
                    </PrimaryButton>
                    <Create :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        :carrerasSelect="data.carrerasDeUSel" :MateriasRequisitoSelect="props.MateriasRequisitoSelect" />
                    <Edit :show="data.editOpen" @close="data.editOpen = false" :materia="data.generico" :title="props.title"
                        :carrerasSelect="data.carrerasDeUSel" :MateriasRequisitoSelect="props.MateriasRequisitoSelect" />
                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :materia="data.generico"
                        :title="props.title" />
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" />
                        <div class="bg-gray-100">
                            <!-- <label for="uni" class="mt-2 pl-8">Universidad: </label> -->
                            <SelectInput v-model="data.params.selectedUni" id="uni" :dataSet="data.UniversidadSelect" />
                        </div>
                        <div v-if="data.params.selectedUni != 0" class="bg-gray-100">
                            <!-- <label for="carrer" class="mt-2 pl-8">Carrera: </label> -->
                            <SelectInput v-model="data.params.selectedcarr" id="carrer" :dataSet="data.carrerasDeUSel" />
                        </div>
                        <!-- <DangerButton @click="data.deleteBulkOpen = true" v-show="data.selectedId.length != 0"
                            class="px-3 py-1.5" v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton> -->
                    </div>
                    <TextInput v-model="data.params.search" type="text" class="block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg"
                        :placeholder="lang().placeholder.search" />
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
                                        <span>{{ titulos }}</span>
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
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
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
                                            <Link :href="route('materia.VistaTema', clasegenerica.id)"
                                                v-show="can(['read materia'])" type="button"
                                                class="px-2 -mb-1.5 py-1.5 rounded-none hover:bg-blue-500">
                                            <CursorArrowRippleIcon class="w-4 h-4" />
                                            </Link>
                                            <Link :href="route('materia.AsignarUsers', clasegenerica.id)"
                                                v-show="can(['isAdmin'])" type="button"
                                                class="px-2 -mb-1.5 py-1.5 rounded-none hover:bg-blue-500">
                                            <UserGroupIcon class="w-4 h-4" />
                                            </Link>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (index + 1) }}</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.hijo) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.nombre) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.muchos) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.numRequisitos) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.objetivs) }} </td>

                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.descripcion) }} </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-betwween items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.fromController" :filters="data.params" />
                </div>
            </div>
        </div>




    </AuthenticatedLayout>
</template>
