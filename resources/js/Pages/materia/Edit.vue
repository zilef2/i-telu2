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

const emit = defineEmits(["close"]);

const form = useForm({
    nombre: '',
    descripcion: '',
    carrera_id: '',
    cuantosObj: props.materia?.objetivs,
    codigo: '',
    enum: 0,
    objetivo: [],
});

watch(form.objetivo, (newValue, oldValue) => { 
    if (props.show) {
        form.cuantosObj = 
            (typeof (form?.objetivo[0]) !== 'undefined') +
            (typeof (form?.objetivo[1]) !== 'undefined') +
            (typeof (form?.objetivo[2]) !== 'undefined') +
            (typeof (form?.objetivo[3]) !== 'undefined') +
            (typeof (form?.objetivo[4]) !== 'undefined')
    }
})

const update = () => {

    // if(form.objetivo.length == form.cuantosObj){
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
    // }else{
    //     alert('Hay objetivos vacios o erroneos');
    // }

}

watchEffect(() => {
    if (props.show) {
        form.errors = {}

        form.nombre = props.materia?.nombre
        form.descripcion = props.materia?.descripcion
        form.carrera_id = props.materia?.carrera_id
        form.cuantosObj = props.materia?.objetivs
        form.enum = props.materia?.enum
        form.codigo = props.materia?.codigo
        
        props.materia?.objetivos.forEach((element,indexe) => {
            
            form.objetivo[indexe] = props.materia?.objetivos[indexe].nombre
        });
        // form.objetivo[1] = props.materia?.objetivo2
        // form.objetivo[2] = props.materia?.objetivo3
    }
})

//not used
onMounted(()=>{ })

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-2 gap-8">
                    <div>
                        <InputLabel for="enum" :value="lang().label.enum" />
                        <TextInput id="enum" type="number" class="mt-1 block w-full" v-model="form.enum" required
                            :placeholder="lang().placeholder.enum" :error="form.errors.enum" />
                        <InputError class="mt-2" :message="form.errors.enum" />
                    </div>
                    <div>
                        <InputLabel for="nombre" :value="lang().label.name" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required
                            :placeholder="lang().placeholder.nombre" :error="form.errors.nombre" />
                        <InputError class="mt-2" :message="form.errors.nombre" />
                    </div>
                    <div>
                        <InputLabel for="codigo" :value="lang().label.codigo" />
                        <TextInput id="codigo" type="text" class="mt-1 block w-full" v-model="form.codigo" required
                            :placeholder="lang().placeholder.codigo" :error="form.errors.codigo" />
                        <InputError class="mt-2" :message="form.errors.codigo" />
                    </div>
                    <div>
                        <InputLabel for="descripcion" :value="lang().label.descripcion" />
                        <TextInput id="descripcion" type="text" class="mt-1 block w-full" v-model="form.descripcion" required
                            :placeholder="lang().placeholder.descripcion" :error="form.errors.descripcion" />
                        <InputError class="mt-2" :message="form.errors.descripcion" />
                    </div>
                    <div>
                        <InputLabel for="carrera_id" :value="lang().label.carrera" />
                        <SelectInput id="carrera_id" class="mt-1 block w-full" v-model="form.carrera_id" required :dataSet="props.carrerasSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.carrera_id" />
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
