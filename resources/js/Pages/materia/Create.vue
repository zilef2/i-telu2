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
    HayMaterias: 0
})
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

})
const create = () => {
    form.post(route('materia.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        // onError: () => alert(form.errors),
        // onError: () => null,
        onError: () => alert(JSON.stringify(form.errors, null, 4)),

        onFinish: () => null,
    })
}

onMounted(() =>{ })
watchEffect(() => {
    if (props.show) {
        form.errors = {}
        if(form.carrera_id){
            // data.MateriasRequisitoSelect = { label: 'No hay materias en esta carrera', value: 0 }
            data.MateriasRequisitoSelect = props.MateriasRequisitoSelect?.map(
                materia => {
                    if(form.carrera_id == materia.carrera_id){
                        return { label: materia.nombre, value: materia.id }
                    }else{
                        return { label: 'Seleccione una', value: 0 }
                    }
                }
            )
            data.MateriasRequisitoSelect = data.MateriasRequisitoSelect.filter(function(materia){
                return materia.value !== 0;
            })
        }


        if(data.MateriasRequisitoSelect){
            data.HayMaterias = data.MateriasRequisitoSelect.length
        }else{
            data.HayMaterias = 0
        }

        data.cuantosReq = parseInt(data.cuantosReq)
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
                <div class="my-6 grid grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="nombre" :value="lang().label.name" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required
                            :placeholder="lang().placeholder.nombre" :error="form.errors.nombre" />
                        <InputError class="mt-2" :message="form.errors.nombre" />
                    </div>
                    <div>
                        <InputLabel for="descripcion" :value="lang().label.descripcion" />
                        <TextInput id="descripcion" type="text" class="mt-1 block w-full" v-model="form.descripcion"
                            :placeholder="lang().placeholder.descripcion" :error="form.errors.descripcion" />
                        <InputError class="mt-2" :message="form.errors.descripcion" />
                    </div>
                    <div>
                        <InputLabel for="carrera_id" :value="lang().label.carrera" />
                        <SelectInput name="carrera_id" class="mt-1 block w-full" v-model="form.carrera_id" required
                         :dataSet="props.carrerasSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.carrera_id" />
                    </div>
                    

                    <div>
                        <InputLabel for="cuantosReq" :value="lang().label.cuantosReq" />
                        <TextInput id="cuantosReq" type="number" min=0 max=3 class="mt-1 block w-full" v-model="form.cuantosReq" required
                            :placeholder="lang().placeholder.cuantosReq" />
                    </div>
                  

                    <div v-if="form.carrera_id && data.HayMaterias != 0 && form.cuantosReq > 0">
                        <InputLabel for="requisito1" :value="lang().label.requisito1" />
                        <SelectInput id="requisito1" class="mt-1 block w-full" v-model="form.requisito1" required
                            :dataSet="data.MateriasRequisitoSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.requisito1" />
                    </div>
                    <div v-if="form.cuantosReq > 1 && data.HayMaterias">
                        <InputLabel for="requisito2" :value="lang().label.requisito2" />
                        <SelectInput id="requisito2" class="mt-1 block w-full" v-model="form.requisito2" required :dataSet="data.MateriasRequisitoSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.requisito2" />
                    </div>
                    <div v-if="form.cuantosReq > 2 && data.HayMaterias">
                        <InputLabel for="requisito3" :value="lang().label.requisito3" />
                        <SelectInput id="requisito3" class="mt-1 block w-full" v-model="form.requisito3" required :dataSet="data.MateriasRequisitoSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.requisito3" />
                    </div>
                    <!-- todo: van mas requisitos? -->

                    <div v-if="!data.HayMaterias && form.carrera_id">
                        <InputLabel for="requisito1" value="Actualmente no hay materias!" class="text-red-500 bg-red-100" />
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
                        @click="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
