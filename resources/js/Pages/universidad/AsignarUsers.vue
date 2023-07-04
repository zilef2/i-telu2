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

    universidad: Object,

    inscritos: Object,
    usuariosPorInscribir: Object,
    
    profesinscritos: Object,
    profesPorInscribir: Object,
})
console.log("🧈 debu inscritos:", props.inscritos);
console.log("🧈 debu inscrito2s:", props.usuariosPorInscribir);


const data = reactive({
    params: {
        search: props.filters.search
    },
    selectedId: [],
    multipleSelect: false,
})

const form = useForm({
    selectedId: [],
    toEraseId: [],
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

const toEraseIdSubmit = () => {
    form.post(route('universidad.toEraseId'), {
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
        <div class="py-1 grid justify-items-center mx-6">
            <div class="flex flex-col text-center w-full mb-12">
                <h1 class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900">Matricular a la universidad</h1>
                <p class="w-full mx-auto leading-relaxed text-base">
                    Estudiantes que no pertenecen a <b>{{ universidad?.nombre }}</b>
                </p>
                <!-- <TextInput v-model="data.params.search" type="text" class="my-4 mx-auto block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg"
                        placeholder="Nombre, correo" /> -->

                <div class="flex space-x-2 text-center mx-auto">
                    <InfoButton @click="inscribirSubmit"
                        v-show="form.selectedId.length != 0 && can(['delete user'])" class="px-3 py-1.5"
                        v-tooltip="lang().tooltip.delete_selected">
                        <CheckCircleIcon class="w-5 h-5" /> Inscribir
                    </InfoButton>
                    <DangerButton @click="toEraseIdSubmit"
                        v-show="form.toEraseId.length != 0 && can(['delete user'])" class="px-3 py-1.5"
                        v-tooltip="lang().tooltip.delete_selected">
                        <CheckCircleIcon class="w-5 h-5" /> Desvincular
                    </DangerButton>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 border-b-2 pb-4 border-sky-300">
                
                <div class="flex flex-wrap -m-2">
                    <h2 class="w-full text-xl font-bold">Estudiantes por inscribir</h2>
                    <div v-if="props.usuariosPorInscribir.length" v-for="(user, index) in props.usuariosPorInscribir" :key="index"
                        class="p-1 lg:w-1/2 md:w-1/2 w-full border-gray-200 border rounded-lg">
                
                        <div class="h-full flex items-center">
                            <!-- <img alt="team"
                                class="w-16 h-16 bg-gray-100 object-cover object-center flex-shrink-0 rounded-full mr-4"
                                src="https://dummyimage.com/80x80"> -->
                                <div class="rounded-full flex items-center justify-center bg-primary text-gray-300 w-12 h-12 text-4xl uppercase">
                                    {{ user.name.match(/(^\S\S?|\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("") }}
                                </div>
                            <div class="mx-0.5 px-2">
                                <!-- <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" class="mx-0 p-1 h-8 w-8"/> -->
                                <input type="checkbox" @change="select" v-model="form.selectedId" :value="user.id"
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary" />
                            </div>
                            <div class="flex-grow">
                                <h2 class="text-gray-900 title-font font-medium">{{ user.name }}</h2>
                                <small class="text-xs text-gray-500 title-font font-medium">{{ user.identificacion }}</small>
                            </div>
                        </div>
                
                    </div>
                    <div v-else class="p-1 w-full border-gray-200 border rounded-lg text-center">
                        <p class="w-full mx-auto leading-relaxed text-base text-red-400">
                            No hay mas estudiantes que no pertenescan a la {{ universidad?.nombre }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap -m-2">
                    <h2 class="w-full text-xl font-bold">Estudiantes Inscritos</h2>
                    <div  v-if="props.inscritos.length > 0" v-for="(user, index) in props.inscritos" :key="index"
                        class="p-1 lg:w-1/2 md:w-1/2 w-full">
                
                        <div class="h-full flex items-center">
                            <!-- <img alt="team"
                                class="w-16 h-16 bg-gray-100 object-cover object-center flex-shrink-0 rounded-full mr-4"
                                src="https://dummyimage.com/80x80"> -->
                                <div class="rounded-full flex items-center justify-center bg-primary text-gray-300 w-12 h-12 text-4xl uppercase">
                                    {{ user.name.match(/(^\S\S?|\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("") }}
                                </div>
                            <div class="mx-0.5 px-2">
                                <!-- <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" class="mx-0 p-1 h-8 w-8"/> -->
                                <input type="checkbox" @change="select" v-model="form.toEraseId" :value="user.id"
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary" />
                            </div>
                            <div class="flex-grow">
                                <h2 class="text-gray-900 title-font font-medium">{{ user.name }}</h2>
                                <small class="text-xs text-gray-500 title-font font-medium">{{ user.identificacion }}</small>
                            </div>
                        </div>
                
                    </div>
                    <div v-else class="p-1 w-full border-gray-200 border rounded-lg text-center">
                        <p class="w-full mx-auto leading-relaxed text-base text-red-400">
                            {{ universidad?.nombre }} no tiene estudiantes
                        </p>
                    </div>
                </div>
            </div>
          








            <!-- profesores -->

            <div class="grid grid-cols-2 gap-8 border-b-2 pb-4 border-sky-300 my-12">
                
                <div class="flex flex-wrap -m-2">
                    <h2 class="w-full text-xl font-bold">Profesores por inscribir</h2>
                    <div v-if="props.profesPorInscribir.length" v-for="(user, index) in props.profesPorInscribir" :key="index"
                        class="p-1 lg:w-1/2 md:w-1/2 w-full border-gray-200 border rounded-lg">
                
                        <div class="h-full flex items-center">
                            <!-- <img alt="team"
                                class="w-16 h-16 bg-gray-100 object-cover object-center flex-shrink-0 rounded-full mr-4"
                                src="https://dummyimage.com/80x80"> -->
                                <div class="rounded-full flex items-center justify-center bg-primary text-gray-300 w-12 h-12 text-4xl uppercase">
                                    {{ user.name.match(/(^\S\S?|\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("") }}
                                </div>
                            <div class="mx-0.5 px-2">
                                <!-- <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" class="mx-0 p-1 h-8 w-8"/> -->
                                <input type="checkbox" @change="select" v-model="form.selectedId" :value="user.id"
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary" />
                            </div>
                            <div class="flex-grow">
                                <h2 class="text-gray-900 title-font font-medium">{{ user.name }}</h2>
                                <small class="text-xs text-gray-500 title-font font-medium">{{ user.identificacion }}</small>
                            </div>
                        </div>
                
                    </div>
                    <div v-else class="p-1 w-full border-gray-200 border rounded-lg text-center">
                        <p class="w-full mx-auto leading-relaxed text-base">
                            <b class="text-red-400">No hay mas </b> profesores que no pertenescan a la {{ universidad?.nombre }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap -m-2">
                    <h2 class="w-full text-xl font-bold">Profesores Inscritos</h2>
                    <div  v-if="props.profesinscritos.length > 0" v-for="(user, index) in props.profesinscritos" :key="index"
                        class="p-1 lg:w-1/2 md:w-1/2 w-full rounded-lg">
                
                        <div class="h-full flex items-center">
                            <!-- <img alt="team"
                                class="w-16 h-16 bg-gray-100 object-cover object-center flex-shrink-0 rounded-full mr-4"
                                src="https://dummyimage.com/80x80"> -->
                                <div class="rounded-full flex items-center justify-center bg-primary text-gray-300 w-12 h-12 text-4xl uppercase">
                                    {{ user.name.match(/(^\S\S?|\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("") }}
                                </div>
                            <div class="mx-0.5 px-2">
                                <!-- <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" class="mx-0 p-1 h-8 w-8"/> -->
                                <input type="checkbox" @change="select" v-model="form.toEraseId" :value="user.id"
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary" />
                            </div>
                            <div class="flex-grow">
                                <h2 class="text-gray-900 title-font font-medium">{{ user.name }}</h2>
                                <small class="text-xs text-gray-500 title-font font-medium">{{ user.identificacion }}</small>
                            </div>
                        </div>
                
                    </div>
                    <div v-else class="p-1 w-full border-gray-200 border rounded-lg text-center">
                        <p class="w-full mx-auto leading-relaxed text-base text-red-400">
                            {{ universidad?.nombre }} no tiene Profesores
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>

</AuthenticatedLayout></template>
