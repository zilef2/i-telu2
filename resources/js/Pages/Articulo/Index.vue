<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, onMounted } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { Head, router, usePage, Link } from '@inertiajs/vue3';

import Pagination from '@/Components/Pagination.vue';
import { ChevronUpDownIcon, PencilIcon, TrashIcon, CheckBadgeIcon } from '@heroicons/vue/24/solid';

import Toast from '@/Components/Toast.vue';

import Edit from '@/Pages/Articulo/Edit.vue';
import Delete from '@/Pages/Articulo/Delete.vue';
import DeleteBulk from '@/Pages/Articulo/DeleteBulk.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import { XMarkIcon, CheckCircleIcon, ExclamationCircleIcon, InformationCircleIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/solid';

import { slugTOhumano }from '@/global.ts';

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
    dataSet: usePage().props.app.perpage,
    isVisible:false,
})


const order = (field) => {
    console.log("🧈 debu field:", field);
    data.params.field = field.replace(/ /g, "_")

    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("Articulo.index"), params, {
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
    // if(typeof data.params.selectedUnidadID === 'undefined' || data.params.selectedUnidadID === null) data.params.selectedUnidadID = 0
    // data.UnidadsSelect = vectorSelect(data.UnidadsSelect, props.UnidadsSelect, 'una Unidad',true)
    notification()
});

function toggle() {
    data.isVisible = false;
}

function notification(newVal) {
    data.isVisible = true;

    setTimeout(() => {
        data.isVisible = false;
    }, 3000);
}

</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />

        <transition name="slide-fade">
            <div v-if="$page.props.flash && $page.props.flash.success != '' && $page.props.flash.success != null && data.isVisible" class="fixed top-4 right-4 w-8/12 md:w-7/12 lg:w-3/12 z-[100]">
                <div class="flex p-4 justify-between items-center bg-green-600 rounded-lg">
                    <div>
                        <CheckCircleIcon class="h-8 w-8 text-white" fill="currentColor" />
                    </div>
                    <div class="mx-3 text-sm font-medium text-white" v-html="$page.props.flash.success">
                    </div>
                    <button @click="toggle" type="button"
                        class="ml-auto bg-white/20 text-white rounded-lg focus:ring-2 focus:ring-white/50 p-1.5 hover:bg-white/30 h-8 w-8">
                        <span class="sr-only">Close</span>
                        <XMarkIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </transition>

        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <div class="flex-inline">
                        <Link :href="route('Articulo.create')" class="items-center py-2 px-4 rounded-lg">
                            <PrimaryButton class="rounded-lg" >
                                {{ lang().button.add }}
                            </PrimaryButton>
                        </Link>
                        <!-- <Link :href="route('Articulo.edit')" class="items-center py-2 px-4 rounded-lg">
                            <PrimaryButton class="rounded-lg" >
                                {{ lang().button.edit }}
                            </PrimaryButton>
                        </Link> -->
                    </div>
                    <!-- <Edit :show="data.editOpen" @close="data.editOpen = false" :articulo="data.generico"
                        v-if="can(['update Articulo'])" :title="props.title" /> -->
                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :articulo="data.generico"
                        v-if="can(['delete Articulo'])" :title="props.title" />
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
                        <!-- filters -->
                        <!-- <div class="bg-gray-100">
                            <SelectInput v-model="data.params.selectedUnidadID" id="uni" :dataSet="data.UnidadsSelect" />
                        </div> -->
                    </div>
                    <!-- <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search" type="text"
                        class="block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg" :placeholder="lang().placeholder.search" /> -->
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900 text-left">
                                <th class="px-2 py-4 text-center">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th>
                                <th v-if="props.numberPermissions > 1"
                                    class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center">
                                        <span> Acciones </span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th v-on:click="order('nick')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> Nombre </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>

                                <th v-on:click="order('version')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('version')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Portada')" class="px-2 py-4 w-64 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Portada')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('user_id')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Autor')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th  class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Calificacion')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Resumen')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Resumen')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Palabras_Clave')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Palabras_Clave')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Introduccion')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Introduccion')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Revision_de_la_Literatura')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Revision_de_la_Literatura')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Metodologia')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Metodologia')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Resultados')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Resultados')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Discusion')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Discusion')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Conclusiones')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Conclusiones')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Agradecimientos')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Agradecimientos')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Referencias')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Referencias')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                <th v-on:click="order('Anexos_o_Apendices')" class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center"> <span> {{slugTOhumano('Anexos_o_Apendices')}} </span> <ChevronUpDownIcon class="w-4 h-4" /> </div>
                                </th>
                                

                                <!-- <th v-if="props.numberPermissions > 1" v-on:click="order('resultado_aprendizaje')"
                                    class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center">
                                        <span>
                                            Resultado aprendizaje
                                        </span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th> -->
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
                                <td v-if="props.numberPermissions > 1" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div class="flex justify-start items-center">
                                        <div class="rounded-md overflow-hidden">
                                            <a type="button"
                                                :href="route('Articulo.edit', clasegenerica.id)"
                                                class="inline-flex items-center px-3 py-2 mx-1 bg-primary dark:bg-primary border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-primary/80 dark:hover:bg-primary/90 focus:bg-primary/80 dark:focus:bg-primary/80 active:bg-primary dark:active:bg-primary/80 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-primary transition ease-in-out duration-150 disabled:bg-primary/80"
                                                 v-tooltip="lang().tooltip.edit">
                                                <PencilIcon class="w-4 h-4" />
                                            </a>
                                            <a type="button"
                                                :href="route('Articulo.revisar', clasegenerica.id)"
                                                class="inline-flex items-center px-3 py-2 mx-1 bg-green-600 dark:bg-primary border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-primary/80 dark:hover:bg-primary/90 focus:bg-primary/80 dark:focus:bg-primary/80 active:bg-primary dark:active:bg-primary/80 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-primary transition ease-in-out duration-150 disabled:bg-primary/80"
                                                 v-tooltip="'Revisar'">
                                                <CheckBadgeIcon class="w-4 h-4" />
                                            </a>
                                            <DangerButton type="button"
                                                @click="(data.deleteOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none mx-1" v-tooltip="lang().tooltip.delete">
                                                <TrashIcon class="w-4 h-4" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
                                    <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.nick) }} </td>
                                    <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.version) }} </td>
                                    <td class="whitespace-nowrap w-full py-4 px-2 sm:py-3">{{ PrimerasPalabra(clasegenerica.Portada) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ (clasegenerica.hijo) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ (clasegenerica.cal) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ PrimerasPalabra(clasegenerica.Resumen) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ (clasegenerica.Palabras_Clave) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ PrimerasPalabra(clasegenerica.Introduccion) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ PrimerasPalabra(clasegenerica.Revision_de_la_Literatura) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ PrimerasPalabra(clasegenerica.Metodologia) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ PrimerasPalabra(clasegenerica.Resultados) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ PrimerasPalabra(clasegenerica.Discusion) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ PrimerasPalabra(clasegenerica.Conclusiones) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ PrimerasPalabra(clasegenerica.Agradecimientos) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ PrimerasPalabra(clasegenerica.Referencias) }} </td>
                                    <td class="flex-wrap w-full py-4 px-2 sm:py-3">{{ PrimerasPalabra(clasegenerica.Anexos_o_Apendices) }} </td>
                                <!-- <td v-if="props.numberPermissions > 1" class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.resultado_aprendizaje) }} </td> -->
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


<style>
/*
    Enter and leave animations can use different
    durations and timing functions.
    */
.slide-fade-enter-active {
    transition: all 0.9s ease-out;
}

.slide-fade-leave-active {
    transition: all 2.8s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateX(15px);
    opacity: 0;
}
</style>