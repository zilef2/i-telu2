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
    subtema: ['','','',''],
})

form.subtema = computed(() => {
});

// const onSubmit = () => {
//     validate().then((isValid) => {
//         if (isValid) {
//             // Handle form submission
//         }
//     });
// };

const create = () => {
    form.post(route('tema.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null,
    })
}

const incrementCounter = (val) => {
    if(val > 0)
        form.nsubtemas = (form.nsubtemas < 3) ? form.nsubtemas += val : form.nsubtemas;
    else
        form.nsubtemas = (form.nsubtemas > 0) ? form.nsubtemas+=val : 0;
};

const generateTemas = async () => {
    if(form.nombre){
        if(form.nsubtemas > 0){
            // try {
                form.post('/gpt/temasCreate', {
                    preserveScroll: true,
                    onSuccess: () => {
                        console.log( res);
                    },
                    onError: () => console.log(console.error('fallo')),
                    onFinish: () => null,
                });
            // } catch (error) {
            //     console.error(error);
            // }
        }else{
            data.mensajeGeenrar = 'El numero de subtopicos debe ser mayor'
        }
    }else{
        data.mensajeGeenrar = 'Digite un nombre del tema!'
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
                <div class="my-6 grid sm:grid-cols-2 xs:grid-cols-1 gap-6">
                    <div>
                        <InputLabel for="nombre" :value="lang().label.name" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required
                            :placeholder="lang().placeholder.nombre" :error="form.errors.nombre" />
                        <InputError class="mt-2" :message="form.errors.nombre" />
                    </div>
                    <div>
                        <InputLabel for="descripcion" :value="lang().label.descripcion" />
                        <TextInput id="descripcion" type="text" class="mt-1 block w-full" v-model="form.descripcion"
                            required :placeholder="lang().placeholder.descripcion" :error="form.errors.descripcion" />
                        <InputError class="mt-2" :message="form.errors.descripcion" />
                    </div>
                    <div>
                        <InputLabel for="materia_id" :value="lang().label.materia" />
                        <SelectInput name="materia_id" class="mt-1 block w-full" v-model="form.materia_id" required
                            :dataSet="MateriasSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.materia_id" />
                    </div>






                    <div>
                        <InputLabel for="materia_id" value="numero de subtopicos" />
                        <PrimaryButton @click="incrementCounter(1)" name="subir" class="px-auto m-1 w-10" type="button"> + </PrimaryButton>
                        <PrimaryButton @click="incrementCounter(-1)" name="subir" class="px-auto m-1 w-10" type="button"> - </PrimaryButton>
                        <TextInput disabled id="descripcion" type="number" class="mt-1 w-1/2 ml-3" v-model="form.nsubtemas"
                            required :placeholder="lang().placeholder.descripcion" :error="form.errors.descripcion" />
                            <!-- todo: invalidar los cambios por el usuarios, mas que solo el disabled -->
                    </div>

                    <div>
                        <PrimaryButton v-tooltip="{ content: data.toolGenerar, html: true }" :triggers="['click']" type="button" 
                            @click="generateTemas">{{ data.mensajeGeenrar }}</PrimaryButton>
                    </div>

                    <div v-for="index in form.nsubtemas" :key="index">
                        <InputLabel for="stema" :value="lang().label.stema" />
                        <TextInput id="stema" type="text" class="mt-1 block w-full" v-model="form.subtema"
                            :placeholder="lang().placeholder.stema" :error="form.errors.stema" />
                        <InputError class="mt-2" :message="form.errors.stema" />
                    </div>

                    <div>
                        <InputLabel for="respuesta" value="respuesta" />
                        <TextInput id="respuesta" type="text" class="mt-1 block w-full" v-model="data.respuesta" required
                            :placeholder="lang().placeholder.respuesta" />
                    </div>

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
    </section>
</template>
