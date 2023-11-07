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
    universidadid: props.Selects.opcionesU[0],
    carreraid: null,
    materiaid: null,
    tipoTexto: '',
    campoActivo: null,
})
const form = useForm({
    ...Object.fromEntries(data.campos.map(field => [field.id, []])),

    universidadid: null,
    carreraid: null,
    materiaid: null,
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

    data.universidadid = props.Selects.opcionesU[props.articulo.universidad_id]
    data.carreraid = props.Selects.opcionesCarreras[props.articulo.universidad_id][props.articulo.carrera_id]

    let indexMat = props.Selects.opcionesAsignatura[props.articulo.carrera_id].findIndex((ele) => ele.value == props.articulo.materia_id)
    console.log("🧈 debu indexMat:", indexMat);
    data.materiaid = props.Selects.opcionesAsignatura[props.articulo.carrera_id][indexMat]
    console.log("🧈 debu props.Selects.opcionesAsignatura[props.articulo.carrera_id]:", props.Selects.opcionesAsignatura[props.articulo.carrera_id]);

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
        form[data.tipoTexto][1] = newX.respuesta
        form[data.tipoTexto][2] = form[data.tipoTexto][0]
        data.restarAlToken = newX.restarAlToken
        data.NumSujerencias[data.tipoTexto]++;
        console.log("🧈 debu data.NumSujerencias:", data.NumSujerencias);

    }

})

const scrollToBottom = () => {
    window.scrollTo({
        top: document.body.scrollHeight - 10,
        behavior: 'smooth'
      });
}
const scrollToTop = () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
}

const OptimizarResumenOIntroduccion = async (elTexto, tipoTexto) => {
    console.log("🧈 debu tipoTexto:", elTexto);
    data.errorCarrera = [];
    data.mostrarLoader = true;
    data.tipoTexto = tipoTexto
    const tamanoMinimo = 10
    if(elTexto){
        let TieneSuficientesPalabras = ContarPalabras(elTexto) > tamanoMinimo || elTexto.length > (tamanoMinimo * 5)
        if (TieneSuficientesPalabras) {
            if (data.materiaid && data.materiaid.value) {
                form[data.tipoTexto][0] = form[data.tipoTexto][2] ? form[data.tipoTexto][2] : form[data.tipoTexto][0]
                router.reload({
                    only: [
                        'ValoresGenerarSeccion',
                    ],
                    data: {
                        elTexto: elTexto,
                        materia: data.materiaid.value,
                        tipoTexto: tipoTexto,
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
        } else {
            data.errorCarrera[tipoTexto] = 'El texto a perfeccionar es muy corto';
        }
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
                        <h1 v-if="!form.nick"
                            class="mt-4 text-2xl font-semibold tracking-wide text-center text-gray-800 capitalize md:text-3xl dark:text-white">
                            Nuevo articulo </h1>
                        <h1 v-else
                            class="mt-4 text-2xl font-semibold tracking-wide text-center text-gray-800 capitalize md:text-3xl dark:text-white">
                            {{form.nick[0]}}
                        </h1>
                        <p class="my-6 text-gray-500 font-bold dark:text-gray-400">
                            Este asistente, concebido con la finalidad de optimizar la argumentación, coherencia y cohesión de su disertación, se erige como una herramienta de apoyo sin la intención de reemplazar su ejercicio de crítica argumentativa. Le insto cordialmente a compartir su texto, y con el mayor esmero, le ofreceremos valiosas sugerencias.
                        </p>

                    </div>
                    <div class="text-center">
                        <button type="button" @click="scrollToBottom"
                            class="w-22 hover:bg-green-500 item-center px-6 py-2 mt-4 mx-8 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-sky-800 rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Ir al final ↓
                        </button>


                        <Link :href="route('Articulo.index')"
                        class="w-22 hover:bg-gray-600 item-center px-6 py-2 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-black rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Regresar
                        </Link>
                    </div>

                    <div class="flex items-center mt-6">
                        <p v-if="data.errorCarrera[0]" class="text-red-500 dark:text-red-200 underline">
                            {{ data.errorCarrera[0] }}</p>
                    </div>
                    <div class="flex text-center mt-6">
                        <p class="text-gray-500 text-xl font-bold dark:text-gray-400">A que asignatura pertenecerá el articulo</p>
                    </div>
                    <div class="mt-2 grid grid-cols-3 gap-8">
                        <div id="opciones2U" class="mt-2 w-full">
                            <label name=""> </label>
                            <v-select :options="props.Selects.opcionesU" label="title"
                                v-model="data.universidadid"></v-select>
                        </div>
                        <div v-if="data.universidadid" id="carrera" class="mt-2 w-full">
                            <v-select :options="props.Selects.opcionesCarreras[data.universidadid.value]" label="title"
                                v-model="data.carreraid"></v-select>
                        </div>
                        <div v-if="data.carreraid" id="asignatura" class="mt-2 w-full">
                            <v-select :options="props.Selects.opcionesAsignatura[data.carreraid.value]" label="title"
                                v-model="data.materiaid"></v-select>
                        </div>
                    </div>
                    <div class="flex items-center mt-6">
                        <p v-if="data.restarAlToken && data.restarAlToken != 0" class="text-sky-600 dark:text-gray-400">Se
                            consumió: {{ data.restarAlToken }} token</p>
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
                                        {{ form[campo.id][1] }}
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


                        <!-- si esta sin revisar por la ia-->
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

                        <div v-if="campo.id === 'Resumen' || campo.id === 'Introduccion' || campo.id === 'Metodologia' || campo.id === 'Discusion' || campo.id === 'Conclusiones'"
                            class="">
                            <div class="flex items-center my-2">
                                <p v-if="data.errorCarrera[0]" class="text-red-500 dark:text-red-200 underline">
                                    {{ data.errorCarrera[0] }}</p>
                            </div>
                            <div class="flex items-center mt-2">
                                <p v-if="data.restarAlToken && data.restarAlToken != 0" class="text-sky-600 text-lg dark:text-gray-600">Se
                                    consumió: {{ data.restarAlToken }} token</p>
                            </div>

                            <GreenButton
                                :class="{ 'opacity-25': data.mostrarLoader }" :disabled="data.mostrarLoader"
                                @click="OptimizarResumenOIntroduccion(form[campo.id][2] ? form[campo.id][2] : form[campo.id][0], campo.id)"
                                class="ml-3 mt-1 px-10 py-3 outline outline-offset-2 ring-2 ring-green-700">
                                    {{ data.mostrarLoader ? 'Revisando...' : 'Revisar' }}
                            </GreenButton>
                            <div class="mt-8">
                                <Generando v-if="data.mostrarLoader" />
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
