<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { router, usePage } from '@inertiajs/vue3';

import Pagination from '@/Components/Pagination.vue';
import { ChevronUpDownIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/solid';

import Create from '@/Pages/LosPromp/Create.vue';
import Edit from '@/Pages/LosPromp/Edit.vue';
import Delete from '@/Pages/LosPromp/Delete.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
// import { vectorSelect, formatDate, CalcularEdad, CalcularSexo }from '@/global.ts';;


const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    fromController: Object,
    // limitePromps: Boolean, //si es falso, no puede crear nuevo promp
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
    dataSet: usePage().props.app.perpage,
})

const order = (field) => {
    data.params.field = field.replace(/ /g, "_")
    
    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("LosPromp.index"), params, {
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

</script>

<template>
    <Head :title="props.title"></Head>

    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <!-- v-if="limitePromps" -->
                    <PrimaryButton 
                        class="rounded-none" @click="data.createOpen = true" v-show="can(['create LosPromp'])">
                        {{ lang().button.add }}
                    </PrimaryButton>
                    <Create :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        v-if="can(['create LosPromp'])" />
                    <Edit :show="data.editOpen" @close="data.editOpen = false" :LosPromp="data.generico"
                        v-if="can(['update LosPromp'])" :title="props.title" />
                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :LosPromp="data.generico"
                        v-if="can(['delete LosPromp'])" :title="props.title" />
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
                    </div>
                    <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search" type="text"
                        class="block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg" :placeholder="lang().placeholder.search" />
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full table-auto">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900 text-left">
                                <th class="px-2 py-4 text-center">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center">
                                        <span>
                                            Acciones
                                        </span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center">
                                        <span>
                                            #
                                        </span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>

                                <th v-on:click="order('teoricaOpractica')"
                                    class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center">
                                        <span>
                                            Teorica o practica
                                        </span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th v-on:click="order('principal')"
                                    class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center">
                                        <span>
                                            Principal
                                        </span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(clasegenerica, index) in fromController.data" :key="index"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20  h-28">
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
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 w-12 sm:py-3">{{ (index + 1) }}</td>
                                <td class="whitespace-nowrap py-4 px-2 w-12 sm:py-3">{{ (clasegenerica.teoricaOpractica) }} </td>
                                <!-- <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.clasificacion) }} </td> -->
                                <td class=" py-4 px-2 w-full flex flex-wrap sm:py-3">
                                    {{ (clasegenerica.principal) }}
                                </td>
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
