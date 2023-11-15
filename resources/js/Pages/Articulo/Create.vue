<script setup>
import Generando from '@/Components/uiverse/Generando.vue';
import GreenButton from '@/Components/GreenButton.vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import { reactive, watchEffect, onMounted, watch } from 'vue'
import Toast from '@/Components/Toast.vue'
import { NoUnderLines, ContarPalabras } from '@/global.ts';
import "vue-select/dist/vue-select.css";

const props = defineProps({
    title: String,
    HijoSelec: Object,

    numberPermissions: Number,

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
    universidadid: props.HijoSelec.universidades[1],
    carreraid: props.HijoSelec.carreras[1],
    materiaid: props.HijoSelec.materias[1],
    tipoTexto: '',
    campoActivo: null,

})
const dataTime = reactive({
    endTime: {},
    startTime: {},
    tiempoEscritura: {},
    index:-1
})

const form = useForm({
    ...Object.fromEntries(data.campos.map(field => [field.id, []])),

    universidadid: 1,
    carreraid: 1,
    materiaid: 1,
    Resumen_integer:0,
    Introduccion_integer:0,
    Discusion_integer:0,
    Conclusiones_integer:0,
    Metodologia_integer:0,
    isArticulo:true,

});


onMounted(() => {
    terminarEscritura(1)
    let ele;
    data.campos.forEach(element => {
        ele = element.id
        data.NumSujerencias[ele] = 0
    });

    if (props.numberPermissions > 9) {
        form.nick[0] = 'Enanas Rojas: Estrellas Dominantes en el Universo'
        form.Portada[0] = 'Este artículo proporciona una visión integral de las enanas rojas y su influencia en la comprensión de la astronomía y la astrobiología contemporáneas.'
        form.Resumen[0] = 'Este artículo universitario examina el papel fundamental que las enanas rojas desempeñan en el cosmos. Estas estrellas de baja masa y longevidad excepcional, constituyen la mayoría de las estrellas en nuestra galaxia'
        form.Palabras_Clave[0] = 'enanas rojas, cosmos ,masa baja'
        form.Introduccion[0] = 'asd'
        form.Revision_de_la_Literatura[0] = 'asd'
        form.Metodologia[0] = 'asd'
        form.Resultados[0] = 'asd'
        form.Discusion[0] = 'asd'
        form.Conclusiones[0] = 'asd'
        form.Agradecimientos[0] = 'asd'
        form.Referencias[0] = 'asd'
        form.Anexos_o_Apendices[0] = 'asd'
    }
})


const imprimirPagina = () => {
    // this.isPrintable = true;
    window.print(); // Esto abrirá el diálogo de impresión
    // this.isPrintable = false; // Restaura el valor original después de imprimir
}

watchEffect(() => {})


//zona blur textareas
const empezarEscritura = (inde) => {
    dataTime.startTime[inde] = new Date();
    data.campoActivo = inde
}



//por conveccion: cuando inde = 1, es que acaba de entrar a la pagina,
// cuando inde = 2 es que ya termino de redactar el articulo

const terminarEscritura = (inde) => {
    data.mostrarLoader = true
    data.campoActivo = null
    dataTime.index = inde

    if(typeof dataTime.startTime[inde] === 'undefined') dataTime.startTime[inde] = new Date();

    dataTime.endTime[inde] = new Date();
    dataTime.tiempoEscritura[inde] = dataTime.endTime[inde] - dataTime.startTime[inde]; // Tiempo en milisegundos

    if(typeof dataTime.startTime[inde] !== 'undefined')
        router.post('/guardarTiempoUser', dataTime, {
            preserveScroll: true,
            onSuccess: () => {
                data.mostrarLoader = true
            },
            onError: () => alert(JSON.stringify(dataTime.errors, null, 4)),
            onFinish: () => {
                data.mostrarLoader = false
            }
        })
}

watch(() => data.universidadid, (newX) => {
    data.carreraid = props.HijoSelec.carreras[1]
})

watch(() => data.carreraid, (newX) => {})

function recibirRespuesta(newX){
    if (newX && newX.respuesta) {
        form[data.tipoTexto][1] = newX.respuesta
        form[data.tipoTexto][2] = form[data.tipoTexto][0]
        data.restarAlToken = newX.restarAlToken
        data.NumSujerencias[data.tipoTexto]++;

    }
}

watch(() => props.ValoresGenerarSeccion, (newX) => {
    // data.NumSujerencias[data.tipoTexto] = true
    recibirRespuesta(newX);
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
    data.errorCarrera = [];
    data.mostrarLoader = true;
    data.tipoTexto = tipoTexto
    const tamanoMinimo = 10
    terminarEscritura(tipoTexto)


    let TieneSuficientesPalabras = elTexto && (ContarPalabras(elTexto) > tamanoMinimo || elTexto.length > (tamanoMinimo * 5))
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
                    data.mostrarLoader = false
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
    data.mostrarLoader = false;
}

const create = () => {
    form.Resumen_integer = data.NumSujerencias['Resumen']
    form.Introduccion_integer = data.NumSujerencias['Introduccion']
    form.Discusion_integer = data.NumSujerencias['Discusion']
    form.Conclusiones_integer = data.NumSujerencias['Conclusiones']
    form.Metodologia_integer = data.NumSujerencias['Metodologia']

    form.universidadid = data.universidadid
    form.carreraid = data.carreraid
    form.materiaid = data.materiaid

    if(data.materiaid && data.materiaid.value !== 0) {
        data.errorCarrera[0] = ''

        terminarEscritura(2)

        form.post(route('Articulo.store'), {
            preserveScroll: true,
            onSuccess: () => {
                // emit("close")
                form.reset()
                data.MensajeFinal = 'Articulo guardado correctamente'
            },
            onError: () => alert(JSON.stringify(form.errors, null, 4)),
            onFinish: () => null,
        })
    }else{
        data.errorCarrera[0] = 'No hay materia seleccionada'
    }
}
</script>


<template>
    <Toast v-if="$page.props.flash" :flash="$page.props.flash" />
    <section class="space-y-1 flex self-center">
        <div class="flex-none w-1 md:w-14"> . </div>
        <div class="grow mx-0 sm:mx-1 md:mx-12 xl:mx-24 text-center p-1 sm:p-8">
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
                        <p class="my-2 text-gray-500 font-bold dark:text-gray-400">
                            Este asistente, concebido con la finalidad de optimizar la argumentación, coherencia y cohesión de su disertación, se erige como una herramienta de apoyo sin la intención de reemplazar su ejercicio de crítica argumentativa. Le insto cordialmente a compartir su texto, y con el mayor esmero, le ofreceremos valiosas sugerencias.
                        </p>

                    </div>
                    <div class="text-center grid grid-cols-1 sm:flex mx-auto">
                        <button type="button" @click="scrollToBottom"
                            class="px-6 py-2 mt-4 mx-6 w-22 xs:w-8 xs:text-xs xs:h-16 hover:bg-green-500 item-center text-md font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-sky-800 rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                         Ir al final ↓</button>

                        <button type="button" @click="imprimirPagina"
                            class="px-6 py-2 mt-4 mx-6 w-22 xs:w-8 xs:text-xs xs:h-16 hover:bg-green-500 item-center  text-md font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-sky-800 rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                         Imprimir</button>

                        <Link :href="route('Articulo.index')" id="universidadSelecs3"
                        class="px-6 py-2 mt-4 mx-6 w-22 xs:w-8 xs:text-xs h-12 hover:bg-gray-600 item-center text-md font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-black rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Regresar
                        </Link>
                    </div>

                    <div v-if="data.errorCarrera[0]" class="flex items-center mt-6">
                        <p class="text-red-500 dark:text-red-200 underline">
                            {{ data.errorCarrera[0] }}</p>
                    </div>
                    <div id="universidadSelecs2" class="flex text-center mt-6">
                        <p class="text-gray-500 text-xl font-bold dark:text-gray-400">A que asignatura pertenecerá el articulo</p>
                    </div>
                    <div id="universidadSelecs" class="mt-2 text-center grid grid-cols-1 sm:grid-cols-3 sm:gap-6 mx-auto">

                        <div id="opciones2U" class="mt-2 w-full">
                            <label name=""> </label>
                            <v-select :options="props.HijoSelec.universidades" label="title"
                                v-model="data.universidadid"></v-select>
                        </div>
                        <div v-if="data.universidadid && data.universidadid.value !== 0" id="carrera" class="mt-2 w-full">
                            <v-select :options="props.HijoSelec.carreras" label="title"
                                v-model="data.carreraid"></v-select>
                        </div>
                        <div v-if="data.carreraid && data.carreraid.value !== 0" id="asignatura" class="mt-2 w-full">
                            <v-select :options="props.HijoSelec.materias" label="title"
                                v-model="data.materiaid"></v-select>
                        </div>
                    </div>
                    <div id="tokensConsumidos" class="flex items-center mt-6">
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
                                    <div v-if="data.campoActivo === campo.id && form[campo.id] && form[campo.id][0] == ''"
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center cursor-progress text-gray-400">
                                        Puede preguntar a la IA, haciendo click en Generar o Refinar
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <label :for="campo.id" class="text-gray-500 text-xl font-bold dark:text-gray-400 mb-2">Sugerencia de la IA: {{ campo.etiqueta }}</label>
                                <div class="relative rounded-md shadow-sm select-none">
                                    <div v-if="form[campo.id] && form[campo.id][1]"
                                        class="block w-full px-5 py-3 mt-2 font-sans
                                         border border-sky-600 select-none rounded-lg
                                        focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 text-justify
                                        bg-sky-100 text-black
                                        dark:placeholder-gray-600 dark:bg-gray-200 dark:text-gray-800 dark:border-gray-700 dark:focus:border-blue-400
                                        ">
                                        {{ form[campo.id][1] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <label :for="campo.id" class="text-gray-500 text-xl font-bold dark:text-gray-400 mb-2">{{ campo.etiqueta }} Final</label>
                                <div class="relative rounded-md shadow-sm">
                                    <textarea :id="campo.id + '2'"
                                        rows="6" cols="33"
                                        @focus="empezarEscritura(campo.id)" @blur="terminarEscritura(campo.id)"
                                        v-model="form[campo.id][2]"
                                        placeholder="Teniendo en cuenta la sugerencia de la IA..."
                                        class="px-5 py-3 mt-2 block w-full  text-gray-700 placeholder-gray-400 bg-white border border-gray-200
                                        rounded-lg focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 text-justify
                                        dark:placeholder-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 dark:focus:border-blue-400"/>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="">
                                <label :for="campo.id" class="rounded-2xl px-10 text-gray-500 text-xl font-bold dark:text-gray-400 shadow-sm bg-gradient-to-r from-gray-50 via-gray-100 to-sky-100 mb-2">
                                    {{ campo.etiqueta }}
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <textarea :id="campo.id"
                                        rows="5" cols="33"
                                        @focus="empezarEscritura(campo.id)" @blur="terminarEscritura(campo.id)"
                                        v-model="form[campo.id][0]"
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

                    <div v-if="data.errorCarrera[0]" class="flex items-center mt-6">
                        <p class="text-red-500 dark:text-red-200 underline text-xl">
                            {{ data.errorCarrera[0] }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:flex gap-12 text-center items-center">
                        <button @click="create"
                            class="w-5/6 sm:w-1/3 xs:mx-auto sm:mx-1 item-center px-6 py-3 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-sky-500 rounded-lg hover:bg-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Guardar
                        </button>
                        <button type="button" @click="scrollToTop"
                            class="w-5/6 sm:w-1/3 xs:mx-auto sm:mx-1 hover:bg-green-500 item-center px-6 py-3 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-sky-800 rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Ir al Inicio</button>

                        <Link :href="route('Articulo.index')" id="irIndex"
                            class="w-5/6 sm:w-1/3 xs:mx-auto sm:mx-1 item-center px-6 py-3 mt-4 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-black rounded-lg hover:bg-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                                Regresar
                        </Link>
                    </div>
                </div>
            </form>
        </div>
        <div class="flex-none w-0 md:w-14"> . </div>
    </section>




    <section id="recuerdeome" class="bg-gray-100 dark:bg-gray-100 my-8">
        <div class="flex flex-col items-center justify-center px-6 mx-auto">
            <div class="flex justify-center mx-auto">
                <img class="w-auto h-7 sm:h-8" src="https://merakiui.com/images/logo.svg" alt="">
            </div>

            <!-- <h1 class="mt-4 text-2xl font-semibold tracking-wide text-center text-gray-800 capitalize md:text-3xl dark:text-white"> Nuevo articulo </h1>h1 -->

            <div class="flex items-center mt-6">
                <p class="text-gray-500 dark:text-gray-400">¡Recuerde!</p>
            </div>
            <div class="w-full max-w-md mx-auto mt-0">
                <form>
                    <div>
                        <label class="block mb-2 text-sm text-gray-600 dark:text-gray-200">Recuerde</label>
                    </div>
                <p class="mt-6 text-gray-500 dark:text-gray-400 text-justify">
                    El presente escrito será sometido a un riguroso proceso de escrutinio por parte del docente designado para la presente actividad académica. Este proceso de evaluación implicará un análisis exhaustivo de los argumentos presentados, así como una revisión detallada de la coherencia y cohesión del texto. Asimismo, se llevará a cabo una evaluación crítica de la pertinencia de las fuentes utilizadas y la profundidad del análisis realizado. El docente, en su calidad de experto en la materia, aplicará un enfoque analítico riguroso para garantizar que el contenido de este artículo cumpla con los estándares académicos establecidos por la institución educativa.
                </p>
            </form>
        </div>
    </div>
</section>
</template>

<style scoped>
@media print {
    button, Link,
    #recuerdeome,
    #irIndex,
    #universidadSelecs,
    #universidadSelecs2,
    #universidadSelecs3,
    #tokensConsumidos
    {
        display: none;
    }
}
</style>
