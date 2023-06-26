<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DatetimeInput from '@/Components/DatetimeInput.vue';

import { useForm } from '@inertiajs/vue3';

import { onMounted, watchEffect, reactive, watch } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    show: Boolean,
    title: String,
    materia: Object,
    carrerasSelect: Object,
    MateriasRequisitoSelect: Object,
})

const data = reactive({
    multipleSelect: false,
})

onMounted(()=>{
})

const emit = defineEmits(["close"]);

const form = useForm({
    nombre: '',
    descripcion: '',
    carrera_id: '',
    requisito1: '',
    requisito2: '',
    requisito3: '',
    cuantosReq: 1,
    cuantosObj: 1,
    objetivo: [],
});

watch(form.objetivo, (newValue, oldValue) => { 
    if (props.show) {

        form.cuantosObj = ((form?.objetivo[0]) !== null) + ((form?.objetivo[1]) !== null) + ((form?.objetivo[2]) !== null)
    }
})

const update = () => {
    form.put(route('materia.update', props.materia?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
            data.multipleSelect = false
        },
        onError: () => null,
        onFinish: () => null,
    })
}

let MateriasRequisitoSelect;
watchEffect(() => {
    if (props.show) {
        form.errors = {}


        MateriasRequisitoSelect = props.MateriasRequisitoSelect?.map(
            materia => {
                return { label: materia.nombre, value: materia.id }
            }
        )
        form.nombre = props.materia?.nombre,
        form.descripcion = props.materia?.descripcion,
        form.carrera_id = props.materia?.carrera_id,
        
        form.requisito1 = props.materia?.req1_materia_id,
        form.requisito2 = props.materia?.req2_materia_id,
        form.requisito3 = props.materia?.req3_materia_id,
        form.cuantosReq = ((props.materia?.requisito1) !== null) + ((props.materia?.requisito2) !== null) + ((props.materia?.requisito3) !== null)

        form.objetivo[0] = props.materia?.objetivo1
        form.objetivo[1] = props.materia?.objetivo2
        form.objetivo[2] = props.materia?.objetivo3
    }
})
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="nombre" :value="lang().label.name" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required
                            :placeholder="lang().placeholder.nombre" :error="form.errors.nombre" />
                        <InputError class="mt-2" :message="form.errors.nombre" />
                    </div>
                    <div>
                        <InputLabel for="descripcion" :value="lang().label.descripcion" />
                        <TextInput id="descripcion" type="text" class="mt-1 block w-full" v-model="form.descripcion" required
                            :placeholder="lang().placeholder.descripcion" :error="form.errors.descripcion" />
                        <InputError class="mt-2" :message="form.errors.descripcion" />
                    </div>
                    <div>
                        <InputLabel for="carrera_id" :value="lang().label.carrera" />
                        <SelectInput id="carrera_id" class="mt-1 block w-full" v-model="form.carrera_id" required :dataSet="carrerasSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.carrera_id" />
                    </div>

                    <div>
                        <InputLabel for="cuantosReq" :value="lang().label.cuantosReq" />
                        <TextInput id="cuantosReq" type="number" min=0 max=3 class="mt-1 block w-full" v-model="form.cuantosReq" required
                            :placeholder="lang().placeholder.cuantosReq" />
                    </div>
                  

                    <div v-if="form.carrera_id && form.cuantosReq > 0">
                        <InputLabel for="requisito1" :value="lang().label.requisito1" />
                        <SelectInput id="requisito1" class="mt-1 block w-full" v-model="form.requisito1" required :dataSet="MateriasRequisitoSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.requisito1" />
                    </div>
                    <div v-if="form.cuantosReq > 1">
                        <InputLabel for="requisito2" :value="lang().label.requisito2" />
                        <SelectInput id="requisito2" class="mt-1 block w-full" v-model="form.requisito2" required :dataSet="MateriasRequisitoSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.requisito2" />
                    </div>
                    <div v-if="form.cuantosReq > 2">
                        <InputLabel for="requisito3" :value="lang().label.requisito3" />
                        <SelectInput id="requisito3" class="mt-1 block w-full" v-model="form.requisito3" required :dataSet="MateriasRequisitoSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.requisito3" />
                    </div>

                    <!-- objetivos -->
                    <div>
                        <InputLabel for="cuantosObj" :value="lang().label.cuantosObj" />
                        <TextInput id="cuantosObj" type="number" min=0 max=3 class="mt-1 block w-full" v-model.number="form.cuantosObj" required
                            :placeholder="lang().placeholder.cuantosObj" />
                    </div>
                    <div v-if="form.cuantosObj > 0" v-for="index in form.cuantosObj">
                        <InputLabel for="" :value="lang().label.objetivo + index" />
                        <TextInput id="objetivo" type="text" min=0 max=3 class="mt-1 block w-full" v-model="form.objetivo[index-1]" required
                            :placeholder="lang().placeholder.objetivo" />
                        <InputError class="mt-2" :message="form.errors.objetivo" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update">
                        {{ form.processing ? lang().button.save + '...' : lang().button.save }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
