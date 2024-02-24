<script setup>
import { reactive, watch, onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import pkg from 'lodash';
import { router, usePage, Link } from '@inertiajs/vue3';

import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue'
const isOpen = ref(true)
function closeModal() { isOpen.value = false }

const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    fromController: Object,
    numberPermissions: Number,
    SuPlan: String,
    YaTienePlan: Boolean,
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
    generico: null,
    successMessage: '',
    dataSet: usePage().props.app.perpage,
    params2: {
        selectedUniID: 0,
    }

})

const form = useForm({
    planid: 0,
})

const create = (planid) => {
    form.planid = planid
    form.post(route('ComprarPlan'), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            // form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}

const order = (field) => {
    console.log("🧈 debu field:", field);
    data.params.field = field.replace(/ /g, "_")

    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("Plan.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))


const NumeroCuenta = 123456

const copyToClipboard = () =>  {
    const content = NumeroCuenta; // Obtener el contenido del span
    const textarea = document.createElement('textarea');
    textarea.value = content;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'absolute';
    textarea.style.left = '-9999px'; // Mover el textarea fuera de la vista
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy'); // Copiar el contenido al portapapeles
    document.body.removeChild(textarea); // Eliminar el textarea
    alert('¡Numero de cuenta copiada!');
}

</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="bg-white pb-6 sm:pb-8 lg:pb-12">
            <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
                <header class="mb-8 flex items-center justify-between border-b py-4 md:mb-12 md:py-8 xl:mb-16">
                    <!-- logo - start -->
                    <a v-if="props.YaTienePlan" href="/" class="inline-flex items-center gap-2.5 text-2xl font-bold text-black md:text-3xl" aria-label="logo">
                        <svg width="95" height="94" viewBox="0 0 95 94" class="h-auto w-6 text-indigo-500" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M96 0V47L48 94H0V47L48 0H96Z" />
                        </svg>
                        Recuerde usted ya tiene pendiente un plan. Será notificado con un correo electrónico
                    </a>
                    <a v-else href="/" class="inline-flex items-center gap-2.5 text-2xl font-bold text-black md:text-3xl" aria-label="logo">
                        <svg width="95" height="94" viewBox="0 0 95 94" class="h-auto w-6 text-indigo-500" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M96 0V47L48 94H0V47L48 0H96Z" />
                        </svg>
                        Su plan esta pendiente por revisar. Será notificado con un correo electrónico
                    </a>
                    <!-- logo - end -->
                </header>

                <section class="flex flex-col items-center">
                    <!-- notice - start -->
                    <div class="flex items-center gap-2 rounded border bg-gray-50 p-2 text-gray-500">
                        <span class="mt-0.5 rounded-full bg-indigo-100 px-2 py-1 text-xs font-semibold leading-none text-indigo-800"
                              @click="copyToClipboard">
                            Copiar
                        </span>

                        <span class="text-sm">{{NumeroCuenta}}</span>

                        <a href="#" class="text-sm font-bold text-indigo-500 transition duration-100 hover:text-indigo-600 active:text-indigo-700"></a>
                    </div>
                    <!-- notice - end -->

                    <div class="flex max-w-xl flex-col items-center pb-0 pt-8 text-center sm:pb-16 lg:pb-32 lg:pt-32">
                        <p class="mb-4 font-semibold text-indigo-500 md:mb-6 md:text-lg xl:text-xl"></p>

                        <h1 class="mb-8 text-4xl font-bold text-black sm:text-5xl md:mb-12 md:text-6xl">Navega por el océano del saber</h1>

                        <p class="mb-8 leading-relaxed text-gray-500 md:mb-12 xl:text-lg">Asistido por la inteligencia artificial</p>

<!--                        <div class="flex w-full flex-col gap-2.5 sm:flex-row sm:justify-center">-->
<!--                            <a href="#" class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base">Start now</a>-->
<!--                            <a href="#" class="inline-block rounded-lg border bg-white px-8 py-3 text-center text-sm font-semibold text-gray-500 outline-none ring-indigo-300 transition duration-100 hover:bg-gray-100 focus-visible:ring active:bg-gray-200 md:text-base">Take tour</a>-->
<!--                        </div>-->
                    </div>
                </section>
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
                                    <DialogTitle v-if="props.YaTienePlan" as="h3" class="text-lg font-medium leading-6 text-gray-900">
                                        Activacion de plan para IntelU
                                    </DialogTitle>
                                    <DialogTitle v-else as="h3" class="text-xl font-medium leading-6 text-gray-900">
                                        Recuerde que usted ya tiene un plan pendiente por aprobacion
                                    </DialogTitle>
                                    <div class="mt-2">
                                        <p v-html="props.SuPlan" class="text-lg text-gray-600"></p>
                                </div>

                                <div class="mt-4">
                                    <button type="button"
                                        class="inline-flex justify-center rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                        @click="closeModal">
                                        Entendido
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </template>


</AuthenticatedLayout></template>
