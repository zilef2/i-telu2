<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { reactive, watch, onMounted,ref, computed } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { router, usePage } from '@inertiajs/vue3';

import Pagination from '@/Components/Pagination.vue';
import { CheckIcon, ChevronUpDownIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/solid';

import Create from '@/Pages/subtopico/Create.vue';
import Edit from '@/Pages/subtopico/Edit.vue';
import Delete from '@/Pages/subtopico/Delete.vue';
import DeleteBulk from '@/Pages/subtopico/DeleteBulk.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import {
    Combobox,
    ComboboxInput,
    ComboboxButton,
    ComboboxOptions,
    ComboboxOption,
    TransitionRoot
} from '@headlessui/vue';
import ZCombobox from "@/Components/headless/ZCombobox.vue";

const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    fromController: Object,

    UnidadsSelect: Array,
    numberPermissions: Number,
})

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        selectedAA: props.filters.selectedAA,
        perPage: props.perPage,
    },
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    generico: null,
    dataSet: usePage().props.app.perpage,
    UnidadsSelect: null,
    stringDesdePHP: '',
})

watch(() => data.params.selectedAA, debounce(() => {
    router.get(route("subtopico.index"), data.params.selectedAA, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 1))
// <!--<editor-fold desc="Order - clonedeep selects">-->
const order = (field) => {
    data.params.field = field.replace(/ /g, "_")
    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("subtopico.index"), params, {
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
// <!--</editor-fold>-->

onMounted(() =>{
    data.params.selectedAA = props.UnidadsSelect[0]
})


let query = ref('')
let filterCombo = computed(() =>
    query.value === ''
        ? props.UnidadsSelect
        : props.UnidadsSelect.filter((person) =>
            person.name
                .toLowerCase()
                .replace(/\s+/g, '')
                .includes(query.value.toLowerCase().replace(/\s+/g, ''))
        )
)

console.log('filterCombo:',filterCombo)
</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="w-fit overflow-hidden rounded-lg">
                    <PrimaryButton class="rounded-none" @click="data.createOpen = true" v-show="can(['create subtopico'])">
                        {{ lang().button.add }}
                    </PrimaryButton>
                    <Create :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        v-if="can(['create subtopico'])" :UnidadsSelect="filterCombo" />
                    <Edit :show="data.editOpen" @close="data.editOpen = false" :subtopico="data.generico"
                        v-if="can(['update subtopico'])" :title="props.title" :UnidadsSelect="props.UnidadsSelect" />
                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :subtopico="data.generico"
                        v-if="can(['delete subtopico'])" :title="props.title" />
                    <DeleteBulk :show="data.deleteBulkOpen"
                        @close="data.deleteBulkOpen = false, data.multipleSelect = false, data.selectedId = []"
                        :selectedId="data.selectedId" :title="props.title" />
                </div>
            </div>
            <div class="relative bg-white shadow dark:bg-gray-800 sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
<!--                        <SelectInput v-model="data.params.selectedUnidadID" :dataSet="data.dataSet" />-->

                        <div id="opcinesActividadO" class="mx-4 w-[550px]">
                            <label class="dark:text-white mx-6"> Filtro unidades</label>

                            <div loquesea="chuchito" class="inline-flex">
                                <Combobox v-model="data.params.selectedAA">
                                    <div class="relative mt-1 w-96">
                                        <div class="relative w-full cursor-default overflow-hidden rounded-lg bg-white text-left shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm">
                                            <ComboboxInput
                                                class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                                                :displayValue="(person) => person?.name"
                                                @change="query = $event.target.value"
                                            />
                                            <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                                                <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true"/>
                                            </ComboboxButton>
                                        </div>
                                        <TransitionRoot
                                            leave="transition ease-in duration-100"
                                            leaveFrom="opacity-100"
                                            leaveTo="opacity-0"
                                            @after-leave="query = ''">
                                            <ComboboxOptions
                                                class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md
                                                       bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                                                <div v-if="filterCombo.length === 0 && query !== ''" class="relative cursor-default select-none px-4 py-2 text-gray-700">
                                                    Sin resultados
                                                </div>

                                                <ComboboxOption
                                                    v-for="person in filterCombo"
                                                    as="template" :key="person.id" :value="person" v-slot="{ selected, active }">
                                                    <li class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                        :class="{ 'bg-teal-600 text-white': active, 'text-gray-900': !active, }">
                                                        <span class="block truncate" :class="{ 'font-medium': selected, 'font-normal': !selected }">
                                                          {{ person.name }}
                                                        </span>
                                                        <span
                                                            v-if="data.params.selectedAA && data.params.selectedAA.id === person.id"
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3"
                                                            :class="{ 'text-white': active, 'text-teal-600': !active }"
                                                        >
                                                          <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                                        </span>
                                                    </li>
                                                </ComboboxOption>
                                            </ComboboxOptions>
                                        </TransitionRoot>
                                    </div>
                                </Combobox>
                            </div>
                        </div>
                        <DangerButton @click="data.deleteBulkOpen = true" v-show="data.selectedId.length !== 0"
                            class="px-3 py-1.5" v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="h-5 w-5" />
                        </DangerButton>
                        <!-- filters -->
<!--                        <div class="bg-gray-100">-->

<!--                            <SelectInput v-model="data.params.selectedUnidadID" id="uni" :dataSet="data.UnidadsSelect" />-->
<!--                        </div>-->
                    </div>
                    <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search" type="text"
                        class="block w-3/6 rounded-lg md:w-2/6 lg:w-1/6" :placeholder="lang().placeholder.search" />
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="border-t border-gray-200 text-sm uppercase dark:border-gray-700">
                            <tr class="text-left dark:bg-gray-900">
                                <th class="px-2 py-4 text-center">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th>
                                <th v-if = "props.numberPermissions > 1" class="cursor-pointer px-2 py-4 hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex items-center justify-between">
                                        <span>
                                            Acciones
                                        </span>
                                        <ChevronUpDownIcon class="h-4 w-4" />
                                    </div>
                                </th>
                                <th v-on:click="order('enum')"
                                    class="cursor-pointer px-2 py-4 hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex items-center justify-between">
                                        <span>
                                            #
                                        </span>
                                        <ChevronUpDownIcon class="h-4 w-4" />
                                    </div>
                                </th>
                                <th v-on:click="order('nombre')"
                                    class="cursor-pointer px-2 py-4 hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex items-center justify-between">
                                        <span>
                                            Nombre
                                        </span>
                                        <ChevronUpDownIcon class="h-4 w-4" />
                                    </div>
                                </th>
                                <!-- <th v-on:click="order('codigo')"
                                    class="cursor-pointer px-2 py-4 hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex items-center justify-between">
                                        <span>
                                            Código
                                        </span>
                                        <ChevronUpDownIcon class="h-4 w-4" />
                                    </div>
                                </th> -->

                                <th v-if = "props.numberPermissions > 1"
                                    v-on:click="order('resultado_aprendizaje')"
                                    class="cursor-pointer px-2 py-4 hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex items-center justify-between">
                                        <span>
                                            Resultado aprendizaje
                                        </span>
                                        <ChevronUpDownIcon class="h-4 w-4" />
                                    </div>
                                </th>
                                <th v-on:click="order('nombre')"
                                    class="cursor-pointer px-2 py-4 hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex items-center justify-between">
                                        <span>
                                            {{ lang().label.Unidad }}
                                        </span>
                                        <ChevronUpDownIcon class="h-4 w-4" />
                                    </div>
                                </th>
                                <th v-on:click="order('observaciones')"
                                    class="cursor-pointer px-2 py-4 hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex items-center justify-between">
                                        <span>
                                            Observaciones
                                        </span>
                                        <ChevronUpDownIcon class="h-4 w-4" />
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(clasegenerica, index) in fromController.data" :key="index"
                                class="border-t border-gray-200 hover:bg-gray-200/30 dark:border-gray-700 hover:dark:bg-gray-900/20">
                                <td class="whitespace-nowrap px-2 py-4 text-center sm:py-3">
                                    <input type="checkbox" @change="select" :value="clasegenerica.id"
                                        v-model="data.selectedId"
                                        class="rounded border-gray-300 shadow-sm text-primary focus:ring-primary/80 dark:text-primary dark:border-gray-700 dark:bg-gray-900 dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-primary dark:focus:ring-offset-gray-800" />
                                </td>
                                <td v-if = "props.numberPermissions > 1" class="whitespace-nowrap px-2 py-4 sm:py-3">
                                    <div class="flex items-center justify-start">
                                        <div class="overflow-hidden rounded-md">
                                            <InfoButton type="button"
                                                @click="(data.editOpen = true), (data.generico = clasegenerica)"
                                                class="rounded-none px-2 py-1.5" v-tooltip="lang().tooltip.edit">
                                                <PencilIcon class="h-4 w-4" />
                                            </InfoButton>
                                            <DangerButton type="button"
                                                @click="(data.deleteOpen = true), (data.generico = clasegenerica)"
                                                class="rounded-none px-2 py-1.5" v-tooltip="lang().tooltip.delete">
                                                <TrashIcon class="h-4 w-4" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-2 py-4 sm:py-3">{{ (clasegenerica.enum) }} </td>
                                <td class="whitespace-nowrap px-2 py-4 sm:py-3">{{ (clasegenerica.nombre) }} </td>
                                <!-- <td class="whitespace-nowrap px-2 py-4 sm:py-3">{{ (clasegenerica.codigo) }} </td> -->
                                <td v-if = "props.numberPermissions > 1" class="whitespace-nowrap px-2 py-4 sm:py-3">{{ (clasegenerica.resultado_aprendizaje) }} </td>
                                <td class="whitespace-nowrap px-2 py-4 sm:py-3">{{ (clasegenerica.hijo) }} </td>
                                <td class="whitespace-nowrap px-2 py-4 sm:py-3">{{ (clasegenerica.descripcion) }} </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center border-t border-gray-200 p-2 justify-betwween dark:border-gray-700">
                    <Pagination :links="props.fromController" :filters="data.params" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
