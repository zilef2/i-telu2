<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import Generando from '@/Components/uiverse/Generando.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import GreenButton from '@/Components/GreenButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { router, useForm } from '@inertiajs/vue3';
import { reactive, watch, watchEffect, ref, onMounted } from 'vue';
import SelectInput from '@/Components/SelectInput.vue';


const props = defineProps({
    show: Boolean,
    title: String,
    carrerasSelect: Object,
    MateriasRequisitoSelect: Object,
    ValoresGenerarMateria: Object,
})
const emit = defineEmits(["close"]);

const data = reactive({
    // cuantosReq: 1
    MateriasRequisitoSelect: [], //carreras
    HayMaterias: 0,
    contadorRespuesta: 0,
    vacia: '',
    recuerdeCodigo: '',
})
const form = useForm({
    // descripcion: '',
    nombre_mat: '',
    carrera_id: 1,//tempForm
    codigo_mat: '', //tempForm
    objetivo: '',
    nombre_unidad: [[]],
    Cuantas_u: "2",
    Array_nombre_tema: [],
    Cuantas_t: "2",
    Array_RA: [[]],

    //loader
    mostrarLoader: false,
    errorCarrera: false,
})
const create = () => {
    if (form.codigo_mat != '') {
        form.post(route('materia.guardarGenerado'), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            // onError: () => null,
            onError: () => alert(JSON.stringify(form.errors, null, 4)),
            onFinish: () => null,
        })
    } else {
        data.errorCarrera = 'Falta el codigo de la materia';
    }
}
const cerrarForm = () => props.ValoresGenerarMateria = null


const generar = async () => {
    data.errorCarrera = '';

    if (validar()) {
        data.mostrarLoader = true
        console.log("🧈 debu form.Cuantas_u:", form.Cuantas_u);
        router.reload({
            only: [
                'ValoresGenerarMateria',
            ],
            data: {
                unidades: form.Cuantas_u,
                temas: form.Cuantas_t,
                carrera_id: form.carrera_id,
            },
        })
    }
}

const validar = () => {
    if (form.carrera_id == 0) {
        data.errorCarrera = 'Seleccione una carrera primero';
        return false
    }
    let Cunidades = parseInt(form.Cuantas_u);
    let Ctemas = parseInt(form.Cuantas_t);
    if (Cunidades > Ctemas) {
        data.errorCarrera = 'Los temas deben ser mayor a los unidades';
        return false
    }
    if (Cunidades < 1 || Ctemas < 1) {
        data.errorCarrera = 'Debe existir al menos un tema y una unidad';
        return false
    }

    return true
}

onMounted(() => { })
watchEffect(() => {
    if (props.show) {
        form.errors = {}

        data.contadorRespuesta = 1
        if (props.ValoresGenerarMateria != null) {
            // data.contadorRespuesta = 2

            form.nombre_mat = props?.ValoresGenerarMateria['respuesta'][data.contadorRespuesta]
            data.contadorRespuesta++ // 2
            form.objetivo = props?.ValoresGenerarMateria['respuesta'][data.contadorRespuesta]
            data.contadorRespuesta++ // 4

            let uni = parseInt(props.ValoresGenerarMateria['Cuantas_unidades'])
            console.log("🧈 debu uni:", uni);
            let tema = parseInt(props.ValoresGenerarMateria['Cuantas_temas'])
            console.log("🧈 debu tema:", tema);
            for (let index = 0; index < uni; index++) {
                form.nombre_unidad[index] = props?.ValoresGenerarMateria['respuesta'][data.contadorRespuesta]
                data.contadorRespuesta++ // 3

                form.Array_nombre_tema[index] = []
                form.Array_RA[index] = []
                for (let jindex = 0; jindex < tema; jindex++) {
                    form.Array_nombre_tema[index][jindex] = props?.ValoresGenerarMateria['respuesta'][data.contadorRespuesta]
                    data.contadorRespuesta++ // 5
                    form.Array_RA[index][jindex] = props?.ValoresGenerarMateria['respuesta'][data.contadorRespuesta]
                    data.contadorRespuesta++ // 6
                }
            }
            console.log("🧈 debu form.Array_nombre_tema:", form.Array_nombre_tema);

            setTimeout(() => {
                data.mostrarLoader = false;
                data.errorCarrera = ''
                if (props.ValoresGenerarMateria['funciono'])
                    data.recuerdeCodigo = 'Recuerde digitar el código de la carrera'
                else
                    data.recuerdeCodigo = 'Hubo error en la generacion de texto. intente otra vez'
            }, 800);
        }
    }
})
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form id="generarTodo" class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Generación de {{ props.title }}
                    <!-- {{ props?.ValoresGenerarMateria }} -->
                </h2>
                <div v-for=" (ele) in props?.ValoresGenerarMateria">
                    <section v-for=" (el, ind) in ele">
                        <p v-if="ind < 3" class="text-sky-500">{{ ind }}.-. {{ el }} </p>
                        <p v-else-if="ind < (3 + parseInt(form.Cuantas_u))" class="text-sky-900">{{ ind }}.-. {{ el }} </p>
                        <p v-else class="text-green-800">{{ ind }}.-. {{ el }} </p>
                        <br>
                    </section>
                </div>
                <div class="my-6 grid grid-cols-1 sm:grid-cols-8 md:grid-cols-8 gap-6">


                    <div class="sm:col-span-8 md:col-span-4">
                        <InputLabel for="carrera_id" :value="lang().label.carrera" />
                        <SelectInput name="carrera_id" class="mt-1 block w-full" v-model="form.carrera_id" required
                            :dataSet="props.carrerasSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.carrera_id" />
                        <p class="mt-2 text-red-500 text-lg"> {{ data.errorCarrera }}</p>
                    </div>
                    <div class="sm:col-span-8 md:col-span-2">
                        <InputLabel for="Cuantas_u" :value="lang().label.Cuantas_unidades" />
                        <TextInput name="Cuantas_u" type="number" min="1" class="mt-1 block w-full"
                            v-model="form.Cuantas_u"> </TextInput>
                        <InputError class="mt-2" :message="form.errors.Cuantas_u" />
                    </div>
                    <div class="sm:col-span-8 md:col-span-2">
                        <InputLabel for="Cuantas_t" :value="lang().label.Cuantas_temas" />
                        <TextInput name="Cuantas_t" type="number" min="1" class="mt-1 block w-full"
                            v-model="form.Cuantas_t"> </TextInput>
                        <InputError class="mt-2" :message="form.errors.Cuantas_t" />
                    </div>

                </div>

                <!-- cosas generadas -->
                <div class="my-6 grid grid-cols-1 sm:grid-cols-6 gap-6">

                    <div class="col-span-6">
                        <InputLabel for="nombre_mat" :value="lang().label.nombre_mat" />
                        <TextInput id="nombre_mat" type="text" v-model="form.nombre_mat" required
                            :placeholder="lang().placeholder.nombre_mat" :error="form.errors.nombre_mat"
                            :class="props?.ValoresGenerarMateria ? 'bg-gray-50' : 'bg-gray-300 invisible'"
                            class="text-center text-2xl mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.nombre_mat" />
                    </div>

                    <div class="col-span-6">
                        <InputLabel for="" :value="lang().label.objetivo" />
                        <TextInput id="objetivo" type="text" v-model="form.objetivo" required
                            :placeholder="lang().placeholder.objetivo"
                            :class="props?.ValoresGenerarMateria ? 'bg-gray-50' : 'bg-gray-300 invisible'"
                            class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.objetivo" />
                    </div>
                </div>


                <div v-if="props?.ValoresGenerarMateria && props?.ValoresGenerarMateria.funciono"
                    v-for="(uni, unikey) in form.nombre_unidad.length" :key="unikey"
                    class="my-6 grid grid-cols-1 sm:grid-cols-6 gap-6">
                    <div class="col-span-6">
                        <InputLabel :for="'nombre_unidad' + unikey"
                            :value="' ---- ' + lang().label.nombre_unidad + ' ' + (unikey + 1) + ' ---- '"
                            class="text-xl my-2 text-center" />
                        <TextInput :id="'nombre_unidad' + unikey" type="text" v-model="form.nombre_unidad[unikey]" required
                            :placeholder="lang().placeholder.nombre_unidad"
                            :class="props?.ValoresGenerarMateria ? 'bg-gray-50' : 'bg-gray-300 disabled'"
                            class="mt-1 block w-full text-center" />
                        <InputError class="mt-2" :message="form.errors.nombre_unidad" />
                    </div>

                    <div v-for="(tema, tkey) in form.Array_nombre_tema[unikey]" :key="tkey" class="col-span-3">
                        <InputLabel for="Array_nombre_tema"
                            :value="lang().label.Array_nombre_tema + ' ' + ((unikey * form.Array_nombre_tema.length) + (tkey + 1))" />
                        <TextInput id="Array_nombre_tema" type="text" class="mt-1 block w-full"
                            v-model="form.Array_nombre_tema[unikey][tkey]" required
                            :placeholder="lang().placeholder.Array_nombre_tema" />
                        <InputError class="mt-2" :message="form.errors.Array_nombre_tema" />

                        <InputLabel for="Array_RA" :value="lang().label.Array_RA" class="mt-3" />
                        <TextInput id="Array_RA" type="text" class="mt-2 block w-full" v-model="form.Array_RA[unikey][tkey]"
                            required :placeholder="lang().placeholder.Array_RA" />
                        <InputError class="mt-2" :message="form.errors.Array_RA" />
                    </div>

                </div>
                <div class="col-span-2 sm:col-span-4 w-full md:w-1/2">
                    <InputLabel for="codigo_mat" :value="lang().label.codigoCar" />
                    <TextInput id="codigo_mat" type="text" class="mt-1 block w-full border-x-2 border-sky-500"
                        v-model="form.codigo_mat" required placeholder="Por favor escriba el codigo_mat"
                        :error="form.errors.codigo_mat" />
                    <InputError class="mt-2" :message="form.errors.codigo_mat" />
                </div>

                <p v-if="data.recuerdeCodigo" class="text-lg">{{ data.recuerdeCodigo }}</p>

                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close'), form.reset(), cerrarForm()"> {{
                        lang().button.close }} </SecondaryButton>

                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="create">
                        {{ form.processing ? 'Guardar' + '...' : 'Guardar' }}
                    </PrimaryButton>

                    <GreenButton class="ml-3" :class="{ 'opacity-25': data.mostrarLoader }" :disabled="data.mostrarLoader"
                        @click="generar">
                        {{ data.mostrarLoader ? + '...' : 'Generar' }}
                    </GreenButton>
                    <Generando v-if="data.mostrarLoader" />
                </div>
            </form>
    </Modal>
</section></template>
