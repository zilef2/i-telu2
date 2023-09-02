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
import { useForm } from '@inertiajs/vue3';

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
        // perPage: props.perPage,
    },
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
})

const form = useForm({
    archivo1: null,
});

const ReadPDF = () =>{
    form.post(route('leyendopdf.read'), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            // form.reset()
            // data.respuesta = $page.props.flash.success
        },
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null,
    });
}


watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("LosPromp.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

</script>

<template>
    <Head :title="props.title"></Head>

    <AuthenticatedLayout>
        <div class="space-y-4">
            <body class="bg-gray-100">
                <div class="max-w-lg mx-auto my-10 bg-white rounded-lg shadow-md p-5">
                    <h2 class="text-center text-2xl font-semibold mt-3">Leer pdf</h2>
                    <p class="text-center text-gray-600 mt-1">Software Engineer</p>
                    <div class="flex justify-center mt-5">
                        <button @click="ReadPDF()" class="text-blue-500 hover:text-blue-700 mx-3">Subir PDF</button>
                        <!-- <a href="#" class="text-blue-500 hover:text-blue-700 mx-3">LinkedIn</a>
                        <a href="#" class="text-blue-500 hover:text-blue-700 mx-3">GitHub</a> -->
                    </div>
                    <div class="mt-5">
                        <h3 class="text-xl font-semibold">Bio</h3>
                        <p class="text-gray-600 mt-2">John is a software engineer with over 10 years of experience in
                            developing web and mobile applications. He is skilled in JavaScript, React, and Node.js.
                            asd
                        </p>

                    </div>
                </div>
            </body>

        </div>
    </AuthenticatedLayout>
</template>