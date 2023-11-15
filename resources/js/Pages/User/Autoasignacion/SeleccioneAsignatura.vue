<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InfoButton from '@/Components/InfoButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, watchEffect } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import Pagination from '@/Components/Pagination.vue';
import { CheckCircleIcon, CheckBadgeIcon, ChevronUpDownIcon, PencilIcon, TrashIcon, UserCircleIcon } from '@heroicons/vue/24/solid';
import Create from '@/Pages/User/Create.vue';
import Edit from '@/Pages/User/Edit.vue';
import Delete from '@/Pages/User/Delete.vue';
import DeleteBulk from '@/Pages/User/DeleteBulk.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { router, usePage, useForm, Link } from '@inertiajs/vue3';

import { number_format, formatDate, CalcularEdad, CalcularSexo }from '@/global.ts';
import Back from "@/Components/uiverse/BackButton.vue";

;

const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    perPage: Number,
    numberPermissions: Number,
    materias: Object
})
const data = reactive({
    // params: {
    //     search: props.filters.search,
    //     field: props.filters.field,
    //     order: props.filters.order,
    //     perPage: props.perPage,
    // },
    ArchivoNombre: '',
})

const order = (field) => {
    data.params.field = field
    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

function buscarRepetidos(vector) {
    return vector.filter((elemento, index, array) => array.indexOf(elemento) !== index);
}

const removeFromArray = (materiaid) => form.materias.filter(item => item !== materiaid);
const AddMateria = (materiaid) => {

    if(form.materias.includes(materiaid)) {
        form.materias = removeFromArray(materiaid)
        console.log(form.materias)
        console.log('asd')
        console.log(form.materias.includes(materiaid))
    }else{
        form.materias.push(materiaid)
    }
}

const form = useForm({
    materias: [],
})

watchEffect(() => {
})
const create = () => {
    console.log('hola')
    form.post(route('ComprarAsignatura'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}


</script>

<template>
    <Head :title="props.title" />

    <AuthenticatedLayout>
        <div class="space-y-4">
            <div v-if="materias.data.length !== 0" class="bg-white py-6 sm:py-8 lg:py-12">
                <div  class="mx-auto max-w-screen-2xl px-4 md:px-8">
                    <div class="mb-4 flex flex-col items-center md:mb-8 lg:flex-row lg:justify-between">
                        <h2 class="mb-2 text-center text-2xl font-bold text-gray-800 lg:mb-0 lg:text-3xl">Trusted by the best</h2>
                        <p class="max-w-md text-center text-gray-400 lg:text-right">Haga clic en el nombre de la materia para seleccionarla.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 rounded-lg md:grid-cols-3 lg:gap-6">
                        <div v-for="(materia, index) in materias.data" :key="index">
                            <div class="inline-flex h-16 items-center justify-center rounded-lg p-4 text-gray-400 sm:h-32 bg-gray-100 hover:text-blue-200"
                                 :class="{'bg-green-400': form.materias.includes(materia.id)}">
                                <p class="text-lg font-bold text-gray-800 transition duration-100 lg:text-xl mr-6 select-none">
                                    Carrera</p>

                                <p class="text-black text-2xl hover:text-green-500 select-none cursor-pointer" @click="AddMateria(materia.id)">
                                    <!--                                mostrar carrera-->
                                    <!--                                validar que no haya repetidos-->
                                    <!--  if selected then keep blue-->
                                    <!--  funcion para matricular al estudiante en las materias que selecciono -->
                                    {{ materia.nombre }}
                                </p></div>
                        </div>
                    </div>

                </div>
                <PrimaryButton class="m-1" @click="create"> si ome </PrimaryButton>
            </div>
            <div v-else class="mb-4 mx-auto items-center md:mb-8 mt-16">
                <h2 class="mb-2 text-center text-xl font-bold text-gray-800 lg:mb-0 lg:text-3xl">Usted posee todas las materias</h2>
            </div>
            <Back :ruta="'dashboard'" class="my-12 text-center"/>

        </div>
    </AuthenticatedLayout>
</template>
