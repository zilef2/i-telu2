<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DatetimeInput from '@/Components/DatetimeInput.vue';
import { useForm } from '@inertiajs/vue3';
// import Checkbox from '@/Components/Checkbox.vue';
import { reactive, watchEffect, computed } from 'vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    show: Boolean,
    title: String,
    MateriasSelect: Object,
})

const emit = defineEmits(["close"]);

const data = reactive({
    toolGenerar: 'Gastara un token',
    mensajeGeenrar: 'Generar sub topicos',
    respuesta: '',
})

const today = new Date();



const form = useForm({
    nombre: '',
    descripcion: '',
    materia_id: 0,

    nsubtemas: 0,
    subtema: ['', '', '', '', ''],
    resultAprendizaje: ['', '', '', '', ''],

    enum: '',
    codigo: '',
})

// form.subtema = computed(() => {
// });


const create = () => {
    form.post(route('Unidad.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        // onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onError: () => null,
        onFinish: () => null,
    })
}

const incrementCounter = (val) => {
    if (val > 0)
        form.nsubtemas = (form.nsubtemas < 5) ? form.nsubtemas += val : form.nsubtemas;
    else
        form.nsubtemas = (form.nsubtemas > 0) ? form.nsubtemas += val : 0;
};

const generateTemas = async () => {
    if (form.nombre) {
        if (form.nsubtemas > 0) {
            // try {
            form.post('/gpt/temasCreate', {
                preserveScroll: true,
                onSuccess: () => {
                    console.log(res);
                },
                onError: () => console.log(console.error('fallo')),
                onFinish: () => null,
            });
            // } catch (error) {
            //     console.error(error);
            // }
        } else {
            data.mensajeGeenrar = 'El numero de subtopicos debe ser mayor'
        }
    } else {
        data.mensajeGeenrar = 'Digite un nombre del Unidad!'
    }
};

watchEffect(() => {
    if (props.show) {
        form.errors = {}
        // subtema Array.from({ length: nsubtemas.value }).map((_, index) => index);
    }
})
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <h2 class="font-serif text-gray-800 dark:text-gray-100">
                    {{ lang().LongTexts.markObligatory }}
                </h2>
                <p v-if="form.errors.length">
                    <b>Por favor, corrija el(los) siguiente(s) error(es):</b>
                <ul>
                    <li v-for="error in errors" class="text-red-400 bg-red-50">{{ error }}</li>
                </ul>
                </p>
                <div class="my-6 grid sm:grid-cols-5 xs:grid-cols-1 gap-6">
                    <div class="col-span-2">
                        <InputLabel for="materia_id" :value="lang().label.materia + '*'" />
                        <SelectInput name="materia_id" class="mt-1 block w-full" v-model="form.materia_id" required
                            :dataSet="MateriasSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.materia_id" />
                    </div>
                    <div>
                        <InputLabel for="enum" :value="lang().label.enumUnidad" />
                        <TextInput id="enum" type="number" class="mt-1 block w-full" v-model="form.enum" required
                            :placeholder="lang().placeholder.enum" :error="form.errors.enum" />
                        <InputError class="mt-2" :message="form.errors.enum" />
                    </div>
                    <div class="col-span-2">
                        <InputLabel for="nombre" :value="lang().label.name + '*'" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required
                            :placeholder="lang().placeholder.nombre" :error="form.errors.nombre" />
                        <InputError class="mt-2" :message="form.errors.nombre" />
                    </div>
                    <!-- <div>
                        <InputLabel for="codigo" :value="lang().label.codigo" />
                        <TextInput id="codigo" type="text" class="mt-1 block w-full" v-model="form.codigo" required
                            :placeholder="lang().placeholder.codigo" :error="form.errors.codigo" />
                        <InputError class="mt-2" :message="form.errors.codigo" />
                    </div>
                    <div>
                        <InputLabel for="descripcion" :value="lang().label.descripcion" />
                        <TextInput id="descripcion" type="text" class="mt-1 block w-full" v-model="form.descripcion"
                            required :placeholder="lang().placeholder.descripcion" :error="form.errors.descripcion" />
                        <InputError class="mt-2" :message="form.errors.descripcion" />
                    </div> -->


                    <div class="col-span-5">
                        <InputLabel for="materia_id" value="numero de subtopicos" />
                        <PrimaryButton @click="incrementCounter(1)" name="subir" class="px-auto m-1 w-10" type="button"> + </PrimaryButton>
                        <PrimaryButton @click="incrementCounter(-1)" name="subir" class="px-auto m-1 w-10" type="button"> - </PrimaryButton>

                        <TextInput disabled id="descripcion" type="number" class="mt-1 w-24 ml-3" v-model="form.nsubtemas"
                            required :placeholder="lang().placeholder.descripcion" :error="form.errors.descripcion" />
                    </div>

                    <!-- <div>
                        <PrimaryButton v-tooltip="{ content: data.toolGenerar, html: true }" :triggers="['click']" type="button" 
                            @click="generateTemas">{{ data.mensajeGeenrar }}</PrimaryButton>
                    </div> -->

                    <div v-for="(subte, index) in form.nsubtemas" :key="index" class="flex gap-8 col-span-5">
                        <div class="w-full">
                            <InputLabel for="stema" :value="lang().label.stema + ' ' + (index + 1)" />
                            <TextInput id="stema" type="text" class="mt-1 block w-full" v-model="form.subtema[index]"
                                :placeholder="lang().placeholder.stema" :error="form.errors.stema" />
                            <InputError class="mt-2" :message="form.errors.stema" />
                        </div>

                        <div class="w-full">
                            <InputLabel for="resultAprendizaje" :value="lang().label.resultAprendizaje + ' ' + (index + 1)" />
                            <TextInput id="resultAprendizaje" type="text" class="mt-1 block w-full"
                                v-model="form.resultAprendizaje[index]" :placeholder="lang().placeholder.resultAprendizaje"
                                :error="form.errors.resultAprendizaje" />
                            <InputError class="mt-2" :message="form.errors.resultAprendizaje" />
                        </div>
                    </div>

                    <!-- <div>
                        <InputLabel for="respuesta" value="respuesta" />
                        <TextInput id="respuesta" type="text" class="mt-1 block w-full" v-model="data.respuesta" required
                            :placeholder="lang().placeholder.respuesta" />
                    </div> -->

                </div>

                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section></template>
