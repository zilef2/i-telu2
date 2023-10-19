<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DatetimeInput from '@/Components/DatetimeInput.vue';
import Generando from '@/Components/uiverse/Generando.vue';
import GreenButton from '@/Components/GreenButton.vue';

import { router, Link, useForm } from '@inertiajs/vue3';
// import Checkbox from '@/Components/Checkbox.vue';
import { reactive, watchEffect, onMounted, watch } from 'vue';
import Toast from '@/Components/Toast.vue';

import { NoUnderLines, ContarPalabras } from '@/global.ts';;
import "vue-select/dist/vue-select.css";

const props = defineProps({
    title: String,
    breadcrumbs: Object,

    numberPermissions: Number,
    articulo: Object,

    Selects: Object,
    ValoresGenerarSeccion: Object,
});

// const emit = defineEmits(["close"]);

const data = reactive({
    mostrarLoader: false,
    MensajeFinal: '',
    calificacion: '',
    restarAlToken: 0,
    NumSujerencias: [],
    errorCarrera: [],
    campos: [
        { id: 'nick', etiqueta: 'Titulo del articulo', valor: [] },
        { id: 'Portada', etiqueta: NoUnderLines('Portada'), valor: [] },
        { id: 'Resumen', etiqueta: NoUnderLines('Resumen'), valor: [] },
        { id: 'Palabras_Clave', etiqueta: NoUnderLines('Palabras Clave'), valor: [] },
        { id: 'Introduccion', etiqueta: NoUnderLines('Introduccion'), valor: [] },
        { id: 'Revision_de_la_Literatura', etiqueta: NoUnderLines('Revision de la Literatura'), valor: [] },
        { id: 'Metodologia', etiqueta: NoUnderLines('Metodologia'), valor: [] },
        { id: 'Resultados', etiqueta: NoUnderLines('Resultados'), valor: [] },
        { id: 'Discusion', etiqueta: NoUnderLines('Discusion'), valor: [] },
        { id: 'Conclusiones', etiqueta: NoUnderLines('Conclusiones'), valor: [] },
        { id: 'Agradecimientos', etiqueta: NoUnderLines('Agradecimientos'), valor: [] },
        { id: 'Referencias', etiqueta: NoUnderLines('Referencias'), valor: [] },
        { id: 'Anexos_o_Apendices', etiqueta: NoUnderLines('Anexos_o_Apendices'), valor: [] },
    ],
    universidadid: null,
    carreraid: null,
    materiaid: null,
    tipoTexto: '',
    campoActivo: null,
    calif: null,
})
const form = useForm({
    ...Object.fromEntries(data.campos.map(field => [field.id, []])),

    universidadid: null,
    carreraid: null,
    materiaid: null,
    user_id:0,
    Resumen_integer:0,
    Introduccion_integer:0,
    Discusion_integer:0,
    Conclusiones_integer:0,
    Metodologia_integer:0,

});


function inicializarForm(){
    form.nick[0] = props.articulo.nick
    form.Portada[0] = props.articulo.Portada
    form.Resumen[0] = props.articulo.Resumen
    form.Palabras_Clave[0] = props.articulo.Palabras_Clave
    form.Introduccion[0] = props.articulo.Introduccion
    form.Revision_de_la_Literatura[0] = props.articulo.Revision_de_la_Literatura
    form.Metodologia[0] = props.articulo.Metodologia
    form.Resultados[0] = props.articulo.Resultados
    form.Discusion[0] = props.articulo.Discusion
    form.Conclusiones[0] = props.articulo.Conclusiones
    form.Agradecimientos[0] = props.articulo.Agradecimientos
    form.Referencias[0] = props.articulo.Referencias
    form.Anexos_o_Apendices[0] = props.articulo.Anexos_o_Apendices
    form.user_id = props.articulo.user_id

    let indexUniversidad = props.Selects.opcionesU.findIndex((ele) => ele.value == props.articulo.universidad_id)
    data.universidadid = props.Selects.opcionesU[indexUniversidad]
    
    let indexCarrera = props.Selects.opcionesCarreras[props.articulo.universidad_id].findIndex((ele) => ele.value == props.articulo.carrera_id)
    data.carreraid = props.Selects.opcionesCarreras[props.articulo.universidad_id][indexCarrera]

    let indexMat = props.Selects.opcionesAsignatura[props.articulo.carrera_id].findIndex((ele) => ele.value == props.articulo.materia_id)
    data.materiaid = props.Selects.opcionesAsignatura[props.articulo.carrera_id][indexMat]
    
};

onMounted(() => {
    let ele;
    data.campos.forEach(element => {
        ele = element.id
        data.NumSujerencias[ele] = 0
    });

    inicializarForm()
})

watchEffect(() => {
})

// watch(() => data.universidadid, (newX) => {
//     if (newX.value !== 0)
//         data.carreraid = props.Selects.opcionesCarreras[newX.value][1];
// })

// watch(() => data.carreraid, (newX) => {
//     if (newX.value !== 0)
//         data.materiaid = props.Selects.opcionesAsignatura[newX.value][1];
// })

watch(() => props.ValoresGenerarSeccion, (newX) => {
    // data.NumSujerencias[data.tipoTexto] = true
    console.log("🧈 debu newX:", newX);
    if (newX && newX.respuesta) {
        data.calificacion = newX.respuesta
        // form[data.tipoTexto][2] = form[data.tipoTexto][0]
        // data.restarAlToken = newX.restarAlToken
        // data.NumSujerencias[data.tipoTexto]++;
        // console.log("🧈 debu data.NumSujerencias:", data.NumSujerencias);
    }

})

const scrollToBottom = () => {
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
      });
}
const scrollToTop = () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
}
//aquiiii hay que actualizar la tabla calificacion
const CalificarArticulo = async (notaManual = null) => {

    data.errorCarrera = [];
    data.mostrarLoader = true;
    form.universidadid = props.articulo.universidad_id
    form.carreraid = props.articulo.carrera_id
    form.materiaid = props.articulo.materia_id

    if (data.materiaid && data.materiaid.value) {
        router.reload({
            only: [
                'ValoresGenerarSeccion',
            ],
            data: {
                elformulario: form,
                articuloid: props.articulo.id,
                notaManual: notaManual,
            },
        }, {
            preserveScroll: true,
            onSuccess: () => {
            },
            onError: () => alert(JSON.stringify(form.errors, null, 4)),
            onFinish: () => {
                data.mostrarLoader = false
            }
        })
    } else {
        data.errorCarrera[0] = 'Seleccione una asignatura primero';
    }
    data.mostrarLoader = false;
}

const update = () => {
    form.Resumen_integer = data.NumSujerencias['Resumen']
    form.Introduccion_integer = data.NumSujerencias['Introduccion']
    form.Discusion_integer = data.NumSujerencias['Discusion']
    form.Conclusiones_integer = data.NumSujerencias['Conclusiones']
    form.Metodologia_integer = data.NumSujerencias['Metodologia']

    form.universidadid = data.universidadid
    form.carreraid = data.carreraid
    form.materiaid = data.materiaid

    form.put(route('Articulo.update',props.articulo.id), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            form.reset()
            data.MensajeFinal = 'Articulo guardado correctamente'
        },
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null,
    })
}

</script>

<template>
    <Toast :flash="$page.props.flash" />

    <section class="space-y-1 flex self-center">
        <div class="flex-none w-14"> . </div>
        <div class="grow mx-1 md:mx-12 xl:mx-20 text-center p-8">
            <form @submit.prevent="create" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="grid grid-cols-1 gap-6">
                    <div class="container flex flex-col items-center justify-center px-6 mx-auto">
                        <div class="flex justify-center mx-auto">
                            <img class="w-auto h-7 sm:h-8" src="https://merakiui.com/images/logo.svg" alt="">
                        </div>

                        <div v-if="data.universidadid" class="mt-1">
                            <p class="text-gray-500 text-center text-xl font-bold dark:text-gray-400">{{ data.universidadid.title }}</p>
                        </div>
                        <div class="">
                            <p v-if="data.carreraid && data.materiaid" id="opciones2U" class="w-full"> 
                                {{data.carreraid.title}}, {{data.materiaid.title}}
                            </p>
                        </div>

                        <h1 v-if="!form.nick"
                            class="mt-4 text-2xl font-semibold tracking-wide text-center text-gray-800 capitalize md:text-3xl dark:text-white">
                            Nuevo articulo </h1>
                        <h1 v-else
                            class="mt-4 text-2xl font-semibold tracking-wide text-center text-gray-800 capitalize md:text-3xl dark:text-white">
                            {{form.nick[0]}}
                        </h1>
                        <p class="my-6 text-gray-500 font-bold dark:text-gray-400">
                            Este asistente ha sido diseñado con el propósito de mejorar la argumentación, la coherencia y la cohesión de su discurso. Se presenta como una herramienta de apoyo que no busca sustituir su habilidad para realizar críticas argumentativas. Le animo de manera cordial a redactar su texto y, con el máximo cuidado, le proporcionaremos sugerencias valiosas.
                        </p>
                    </div>
                    <div v-if="!data.mostrarLoader" class="text-center">
                        <GreenButton 
                            :class="{ 'opacity-25': data.mostrarLoader }" :disabled="data.mostrarLoader"
                            @click="CalificarArticulo()"
                            class="ml-3 px-10 py-2 outline outline-offset-2 ring-2 ring-green-700">
                                {{ data.mostrarLoader ? 'Calificando...' : 'Calificar Con IA' }}
                        </GreenButton>

                        
                            
                        <button type="button" @click="scrollToBottom"
                            class="w-22 hover:bg-green-500 item-center px-6 py-2 mt-4 mx-8 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-sky-800 rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Ir al final ↓
                        </button>
                        <Link :href="route('Articulo.index')"
                        class="w-22 hover:bg-gray-600 item-center px-6 py-2 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-black rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Regresar
                        </Link>
                    </div>
                    <div v-if="!data.mostrarLoader" class="self-center inline-flex gap-10">
                        <div class="text-center mx-auto">
                            <label for="" class="mx-6 text-center">Coloque una nota a este articulo</label>
                            <input type="number" step="0.5" min="0" max="5" v-model="data.calif">
                            <GreenButton v-if="data.calif != ''"
                                :class="{ 'opacity-25': data.mostrarLoader }" :disabled="data.mostrarLoader"
                                @click="CalificarArticulo(data.calif)"
                                class="ml-3 px-10 py-2 outline outline-offset-2 ring-2 ring-green-700">
                                    {{ data.mostrarLoader ? 'Calificando...' : 'Calificar Manualmente' }}
                            </GreenButton>
                        </div>
                    </div>
                    <div v-if="data.mostrarLoader" class="mt-2"> <Generando /> </div>

                    <div class="flex items-center mt-2">
                        <p v-if="data.errorCarrera[0]" class="text-red-500 dark:text-red-200 underline">
                            {{ data.errorCarrera[0] }}</p>
                    </div>

                    <!-- calificacion -->
                    <div v-if="data.calificacion != ''" class="">
                        <label for="calificacion" class="text-gray-500 text-xl font-bold dark:text-gray-400 mb-2">
                            Calificación
                        </label>
                        <div v-if="data.calificacion != 'Se guardo la nota'" class="relative rounded-md shadow-sm select-none">
                            <div id="calificacion"
                                class="block w-full px-5 py-3 mt-2 text-black font-sans bg-white
                                    dark:bg-gray-200 dark:text-gray-800 dark:border-gray-700 focus:border-blue-400
                                    dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 text-justify" >
                                {{ data.calificacion}}
                            </div>
                        </div>
                        <div v-else class="relative rounded-md shadow-sm self-center items-center mx-auto">
                            <div id="calificacion"
                                class="text-center bg-green-200 w-full px-5 py-3 mt-2 text-black font-sans 
                                    dark:bg-gray-200 dark:text-gray-800 dark:border-gray-700 focus:border-blue-400
                                    dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40" >
                                {{ data.calificacion}}
                            </div>
                        </div>
                    </div>

                   
                    

                    <div v-for="(campo) in data.campos" :key="campo.id">
                        <div v-if="data.NumSujerencias[campo.id]" class="grid grid-cols-2 gap-8">
                            <div class="">
                                <label :for="campo.id" class="text-gray-500 text-xl font-bold dark:text-gray-400 mb-2">{{ campo.etiqueta }}</label>
                                <div class="relative rounded-md shadow-sm">
                                    <textarea :id="campo.id + '0'" @focus="data.campoActivo = campo.id" rows="8" cols="33"
                                        @blur="data.campoActivo = null" v-model="form[campo.id][0]" disabled
                                        class="block w-full px-5 py-3 mt-2 bg-gray-50 text-gray-700 placeholder-gray-400 border border-gray-200
                                        rounded-lg dark:placeholder-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700 focus:border-blue-400
                                         dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 text-justify" />
                                    <div v-if="data.campoActivo === campo.id && form[campo.id][0] == ''"
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center cursor-progress text-gray-400">
                                        Puede preguntar a la IA, haciendo click en Generar o Refinar
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <label :for="campo.id" class="text-gray-500 text-xl font-bold dark:text-gray-400 mb-2">Sugerencia {{
                                    campo.etiqueta }}</label>
                                <div class="relative rounded-md shadow-sm select-none">
                                    <div :id="campo.id + '1'"
                                        class="block w-full px-5 py-3 mt-2 text-white font-sans bg-black border border-sky-600 select-none
                                        rounded-lg dark:placeholder-gray-600 dark:bg-gray-200 dark:text-gray-800 dark:border-gray-700 focus:border-blue-400
                                         dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 text-justify" >
                                        {{ form[campo.id][1][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <label :for="campo.id" class="text-gray-500 text-xl font-bold dark:text-gray-400 mb-2">{{ campo.etiqueta
                                }} Final</label>
                                <div class="relative rounded-md shadow-sm">
                                    <textarea :id="campo.id + '2'" @focus="data.campoActivo = campo.id" rows="6" cols="33"
                                        @blur="data.campoActivo = null" v-model="form[campo.id][2]"
                                        placeholder="Teniendo en cuenta la sugerencia de la IA..."
                                        class="block w-full px-5 py-3 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200
                                        rounded-lg dark:placeholder-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-400
                                         dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 text-justify" />
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="">
                                <label :for="campo.id" class="rounded-2xl px-10 text-gray-500 text-xl font-bold dark:text-gray-400 shadow-sm bg-gradient-to-r from-gray-50 via-gray-100 to-sky-100 mb-2">
                                    {{ campo.etiqueta }}
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <textarea :id="campo.id" @focus="data.campoActivo = campo.id" rows="4" cols="33"
                                        @blur="data.campoActivo = null" v-model="form[campo.id][0]"
                                        class="block w-full px-5 py-3 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200
                                        rounded-lg dark:placeholder-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-400
                                         dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40" />
                                    <div v-if="data.campoActivo === campo.id && form[campo.id][0] == ''"
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center cursor-progress text-gray-400">
                                        Puede preguntar a la IA, haciendo click en Generar o Refinar
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="campo.id == 'Resumen' || campo.id == 'Introduccion' || campo.id == 'Metodologia' || campo.id == 'Discusion' || campo.id == 'Conclusiones'"
                            class="">
                            <div class="flex items-center my-2">
                                <p v-if="data.errorCarrera[0]" class="text-red-500 dark:text-red-200 underline">
                                    {{ data.errorCarrera[0] }}</p>
                            </div>
    
                            <div class="flex items-center mt-6">
                                <p v-if="data.errorCarrera[campo.id]" class="text-red-500 dark:text-red-200 underline">
                                    {{ data.errorCarrera[campo.id] }}</p>
                            </div>
                        </div>

                        <hr class="border-2 border-sky-100 my-8">
                    </div>

                    <div class="flex gap-12 text-center">
                        <button @click="update"
                            class="w-1/3 item-center px-6 py-3 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-sky-500 rounded-lg hover:bg-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Actualizar
                        </button>
                        <button type="button" @click="scrollToTop" 
                            class="w-1/3 hover:bg-green-500 item-center px-6 py-3 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-sky-800 rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Ir al Inicio</button>

                        <Link :href="route('Articulo.index')"
                            class="w-1/3 item-center px-6 py-3 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-black rounded-lg hover:bg-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                                Regresar
                        </Link>
                    </div>
                </div>
            </form>
        </div>
        <div class="flex-none w-14"> . </div>
    </section>

</template>
