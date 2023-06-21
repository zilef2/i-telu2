<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, ref, watchEffect } from 'vue';

import pkg from 'lodash';
import { router, usePage, Link } from '@inertiajs/vue3';

import { CheckCircleIcon } from '@heroicons/vue/24/solid';


import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

import { useForm } from '@inertiajs/vue3';

const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    breadcrumbs: Object,

    filters: Object,

    usuariosPorInscribir: Object,
    universidad: Object,
    inscritos: Object,
})


const data = reactive({
    params: {
        search: props.filters.search
    },
    selectedId: [],
    multipleSelect: false,
})

const form = useForm({
    selectedId: [],
    universidadid: props.universidad?.id,
})

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("universidad.AsignarUsers",props.universidad.id), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

watchEffect(() => {
})


const inscribirSubmit = () => {
    form.post(route('universidad.SubmitAsignarUsers'), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            // data.mensajeForm = "listo!"
            form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}

// data.UniversidadSelect = props.UniversidadSelect?.map(
//     universidad => (
//         { label: universidad.nombre, value: universidad.id }
//     )
// )
// data.UniversidadSelect.unshift({ label: 'Seleccione una opcion', value: 0 })
</script>

<template>
    <Head :title="props.title"></Head>

<AuthenticatedLayout>
    <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />

    <section class="text-gray-600 body-font">
        <div class="container px-5 py-1 mx-auto">
            <div class="flex flex-col text-center w-full mb-12">
                <h1 class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900">Matricular a la universidad</h1>
                <p class="w-full mx-auto leading-relaxed text-base">
                    Estudiantes que no pertenecen a <b>{{ universidad?.nombre }}</b>
                </p>
                <TextInput v-model="data.params.search" type="text" class="my-4 mx-auto block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg"
                        placeholder="Nombre, correo" />

                <div class="flex space-x-2 text-center mx-auto">
                    <InfoButton @click="inscribirSubmit"
                        v-show="form.selectedId.length != 0 && can(['delete user'])" class="px-3 py-1.5"
                        v-tooltip="lang().tooltip.delete_selected">
                        <CheckCircleIcon class="w-5 h-5" />
                    </InfoButton>
                </div>
            </div>
            <div class="flex flex-wrap -m-2">
                <div v-if="props.usuariosPorInscribir" v-for="(user, index) in props.usuariosPorInscribir" :key="index" 
                    class="p-1 xl:w-1/4 lg:w-1/2 md:w-1/2 w-full border-gray-200 border rounded-lg">
                    
                    <div class="h-full flex items-center">
                        <img alt="team"
                            class="w-16 h-16 bg-gray-100 object-cover object-center flex-shrink-0 rounded-full mr-4"
                            src="https://dummyimage.com/80x80">
                        <div class="mx-0.5 px-2">
                            <!-- <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" class="mx-0 p-1 h-8 w-8"/> -->
                            <input type="checkbox" @change="select" v-model="form.selectedId" :value="user.id"
                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary" />
                        </div>
                        <div class="flex-grow">
                            <h2 class="text-gray-900 title-font font-medium">{{ user.name }}</h2>
                            <small class="text-xs text-gray-500 title-font font-medium">{{ user.email }}</small>
                        </div>
                    </div>
                    
                </div>
                <div v-else class="p-1 w-full border-gray-200 border rounded-lg text-center">
                    <p class="w-full mx-auto leading-relaxed text-base text-red-500 bg-red-50">
                        No existen usuarios con este criterio
                    </p>
                </div>
            </div>

            <div v-if="props.inscritos.length > 0" class="flex flex-wrap my-8">
                <div class="p-4 xl:w-1/3 sm:w-1/2 w-full">
                    <h2 class="font-medium title-font tracking-widest text-gray-900 mb-4 text-xl text-center sm:text-left">Inscritos</h2>
                    <nav class="flex flex-col sm:items-start sm:text-left text-center items-center -mb-1 space-y-2.5">
                        <p v-for="(user, index) in inscritos" :key="index">
                            <span class="bg-indigo-100 text-indigo-500 w-4 h-4 mr-2 rounded-full inline-flex items-center justify-center">
                            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" class="w-3 h-3" viewBox="0 0 24 24"> <path d="M20 6L9 17l-5-5"></path> </svg>
                            </span>{{ user.name }} ({{ user.email }})
                        </p>
                    </nav>
                </div>
            </div>
        </div>
    </section>

</AuthenticatedLayout></template>
