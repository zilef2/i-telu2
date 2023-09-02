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
import { CursorArrowRippleIcon, ChevronUpDownIcon, QuestionMarkCircleIcon, EyeIcon, PencilIcon, TrashIcon, UserCircleIcon } from '@heroicons/vue/24/solid';

import Create from '@/Pages/materia/Create.vue';
import Edit from '@/Pages/materia/Edit.vue';
import Delete from '@/Pages/materia/Delete.vue';
import generarTodo from '@/Pages/materia/generarTodo.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import superButton from '@/Components/uiverse/superButton.vue';
import { useForm } from '@inertiajs/vue3';
import { PrimerasPalabras, vectorSelect, formatDate, CalcularEdad, CalcularSexo } from '@/global.ts';;

const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    fromController: Object,
    nombresTabla: Object,
    respuest: String,
    errorMessage: String,
    carrerasSelect: Object,
    MateriasRequisitoSelect: Object,
    UniversidadSelect: Object,
    numberPermissions: Number,
    ValoresGenerarMateria: Object,
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
    generarOpen: false,
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
    data.carrerasDeUSel = props.carrerasSelect?.map(
        carrera => (
            { label: carrera.nombre, value: carrera.id }
        )
    )
    data.carrerasDeUSel.unshift({ label: 'Seleccione carrera', value: 0 })
    // }
})
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

onMounted(() => {
    if (typeof data.params.selectedcarr === 'undefined' || data.params.selectedcarr === null) data.params.selectedcarr = 0

    data.UniversidadSelect = vectorSelect(data.UniversidadSelect, props.UniversidadSelect, 'una')
    data.carrerasDeUSel = vectorSelect(data.carrerasDeUSel, props.carrerasSelect, 'una')
    // data.MateriasRequisitoSelect = vectorSelect(data.MateriasRequisitoSelect,props.MateriasRequisitoSelect,'una')
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
                    <PrimaryButton class="rounded-none" @click="data.createOpen = true" v-if="can(['create materia'])">
                        {{ lang().button.add }}
                    </PrimaryButton>

                    <PrimaryButton class="rounded-none" @click="data.generarOpen = true" v-if="can(['create materia'])">
                        Generar Materia
                    </PrimaryButton>
                    <generarTodo :show="data.generarOpen" @close="data.generarOpen = false" :title="props.title"
                        v-if="can(['create materia'])" :carrerasSelect="data.carrerasDeUSel" :ValoresGenerarMateria="props.ValoresGenerarMateria"
                        :MateriasRequisitoSelect="props.MateriasRequisitoSelect" />


                    <Create :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        v-if="can(['create materia'])" :carrerasSelect="data.carrerasDeUSel"
                        :MateriasRequisitoSelect="props.MateriasRequisitoSelect" />
                    <Edit :show="data.editOpen" @close="data.editOpen = false" :materia="data.generico" :title="props.title"
                        v-if="can(['update materia'])" :carrerasSelect="data.carrerasDeUSel"
                        :MateriasRequisitoSelect="props.MateriasRequisitoSelect" />
                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :materia="data.generico"
                        v-if="can(['delete materia'])" :title="props.title" />
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div v-if="props.numberPermissions > 1" class="flex space-x-2">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" />
                        <DangerButton v-if="can(['delete materia'])" @click="data.deleteBulkOpen = true"
                            v-show="data.selectedId.length != 0" class="px-3 py-1.5"
                            v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton>

                        <!-- filters -->
                        <div class="bg-gray-100">
                            <SelectInput v-model="data.params.selectedUni" id="uni" :dataSet="data.UniversidadSelect" />
                        </div>
                        <div v-if="data.params.selectedUni != 0 && props.numberPermissions > 1" class="bg-gray-100">
                            <SelectInput v-model="data.params.selectedcarr" id="carrer" :dataSet="data.carrerasDeUSel" />
                        </div>

                    </div>
                    <TextInput v-if="can(['create materia'])" v-model="data.params.search" type="text"
                        class="block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg" :placeholder="lang().placeholder.search" />
                </div>

                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900 text-left">
                                <!-- <th class="px-2 py-4 text-center">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th> -->
                                <th v-for="(titulos, indiceN) in nombresTabla[0]" :key="indiceN"
                                    v-on:click="order(nombresTabla[2][indiceN])"
                                    class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center">
                                        <span>{{ titulos }}</span>
                                        <ChevronUpDownIcon v-if="nombresTabla[2][indiceN] !== null" class="w-4 h-4" />
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(clasegenerica, index) in fromController.data" :key="index"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">
                                <!-- <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                    <input type="checkbox" @change="select" :value="clasegenerica.id"
                                        v-model="data.selectedId"
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary" />
                                </td> -->
                                <td v-if="numberPermissions > 2" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div class="flex justify-start items-center">
                                        <div class="rounded-md overflow-hidden">
                                            <InfoButton type="button"
                                                @click="(data.editOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                                <PencilIcon class="w-4 h-4" />
                                            </InfoButton>
                                            <DangerButton v-if="props.numberPermissions > 3" type="button"
                                                @click="(data.deleteOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.delete">
                                                <TrashIcon class="w-4 h-4" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
                                <td v-if="numberPermissions > 2" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div class="flex justify-start items-center ml-6">
                                        <div class="rounded-md overflow-hidden">
                                            <Link :href="route('materia.AsignarUsers', clasegenerica.id)">
                                                <InfoButton type="button" class="py-1.5 rounded-none"
                                                    v-tooltip="lang().tooltip.inscribir">
                                                    <UserCircleIcon class="w-7 h-7 px-0.5" />
                                                </InfoButton>
                                            </Link>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div v-if="clasegenerica.cuantoshijos != 0" class="flex justify-start items-center">
                                        <div class="rounded-md overflow-hidden">
                                            <superButton type="button" class="rounded-lg"
                                                :ruta="'materia.VistaTema'"
                                                :id1="clasegenerica.id"
                                                :texto="'Estudiar'">
                                            </superButton>
                                        </div>
                                    </div>
                                    <div v-else class="flex justify-start items-center">
                                        <div class="rounded-md overflow-hidden">
                                            Sin información
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-sm text-gay-600">{{ (clasegenerica.enum) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 underline text-sky-700">
                                    <Link :href="route('materia.show', clasegenerica.id)">
                                    {{ (clasegenerica.nombre) }}
                                    </Link>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-sm text-gay-600">{{
                                    (clasegenerica.codigo) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-sm text-gay-600">{{ (clasegenerica.papa) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-sm text-gay-600">{{
                                    (clasegenerica.cuantoshijos) }} </td>
                                <td v-if="props.numberPermissions > 2" class="whitespace-nowrap py-4 px-2 sm:py-3">{{
                                    (clasegenerica.muchos) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.objetivs) }} </td>

                                <td class="whitespace-wrap break-words text-sm py-4 px-0">{{
                                    PrimerasPalabras(clasegenerica.descripcion, 11) }} </td>
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
