<script setup>
import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import Modal from '@/Components/Modal.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import DatetimeInput from '@/Components/DatetimeInput.vue';
    import { useForm } from '@inertiajs/vue3';
    import Checkbox from '@/Components/Checkbox.vue';
    import { reactive, watchEffect, ref, onMounted } from 'vue';
    import SelectInput from '@/Components/SelectInput.vue';


const props = defineProps({
    show: Boolean,
    title: String,
    carrerasSelect: Object,
    MateriasRequisitoSelect: Object,
})
const emit = defineEmits(["close"]);

const data = reactive({
    // cuantosReq: 1
    MateriasRequisitoSelect: [],
    HayMaterias: 0,
    MensajeError:''
})
const form = useForm({
    enum: '1',
    codigo: '',
    nombre: '',
    descripcion: '',
    carrera_id: '',
    cuantosObj: 2,
    objetivo: [],

})
onMounted(() =>{
    form.nombre = 'materia a'
    form.codigo = 'CODmateria a'
})

let validate = () => {
    let esValido = true
    if(!form.carrera_id) return false
    for (let i = 0; i <form.cuantosObj; i++){
        esValido = esValido && form.objetivo[i] !== '' && (typeof form.objetivo[i] !== 'undefined')
    }
  return esValido
}
const create = () => {
    let BoolValido = validate()
    if(BoolValido) {
        form.post(route('materia.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => null,
            // onError: () => alert(JSON.stringify(form.errors, null, 4)),

            onFinish: () => null,
        })
    }else{
        data.MensajeError = 'falta campos obligatorios'
    }
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
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
                <h2 class="font-serif text-red-800 dark:text-red-200">
                    {{ data.MensajeError }}
                </h2>
                <div class="my-6 grid grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="carrera_id" :value="lang().label.carrera + '*'" />
                        <SelectInput name="carrera_id" class="mt-1 block w-full" v-model="form.carrera_id" required
                         :dataSet="props.carrerasSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.carrera_id" />
                    </div>

                    <div>
                        <InputLabel for="enum" :value="lang().label.enum + '*'" />
                        <TextInput id="enum" type="number" class="mt-1 block w-full" v-model="form.enum" required
                            :placeholder="lang().placeholder.enum" :error="form.errors.enum" />
                        <InputError class="mt-2" :message="form.errors.enum" />
                    </div>
                    <div>
                        <InputLabel for="nombre" :value="lang().label.name + '*'" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required
                            :placeholder="lang().placeholder.nombre" :error="form.errors.nombre" />
                        <InputError class="mt-2" :message="form.errors.nombre" />
                    </div>
                    <div>
                        <InputLabel for="codigo" :value="lang().label.codigo + '*'" />
                        <TextInput id="codigo" type="text" class="mt-1 block w-full" v-model="form.codigo" required
                            :placeholder="lang().placeholder.codigo" :error="form.errors.codigo" />
                        <InputError class="mt-2" :message="form.errors.codigo" />
                    </div>
                    <div>
                        <InputLabel for="descripcion" :value="lang().label.descripcionop" />
                        <TextInput id="descripcion" type="text" class="mt-1 block w-full" v-model="form.descripcion"
                            :placeholder="lang().placeholder.descripcion" :error="form.errors.descripcion" />
                        <InputError class="mt-2" :message="form.errors.descripcion" />
                    </div>



                    <!-- objetivos -->
                    <div>
                        <InputLabel for="cuantosObj" :value="lang().label.cuantosObj" />
                        <TextInput id="cuantosObj" type="number" min=2 max=6 class="mt-1 block w-full" v-model.number="form.cuantosObj" required
                            :placeholder="lang().placeholder.cuantosObj" />
                    </div>
                    <div v-if="form.cuantosObj > 0" v-for="index in form.cuantosObj" class="col-span-2">
                        <InputLabel v-if="index === 1" for="" value="Objetivo general" />
                        <InputLabel v-else for="" :value="lang().label.objetivo_especifico + ' '+(index-1)" />
                        <TextInput id="objetivo" type="text" class="mt-1 block w-full" v-model="form.objetivo[index-1]" required
                            :placeholder="lang().placeholder.objetivo" />
                        <InputError class="mt-2" :message="form.errors.objetivo" />
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
