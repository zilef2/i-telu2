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
import { reactive, watchEffect } from 'vue';

import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    show: Boolean,
    title: String,
    UnidadsSelect: Object,
})

const emit = defineEmits(["close"]);

const data = reactive({
    multipleSelect: false,
})
const today = new Date();

const form = useForm({
    nombre: '',
    enum: '',
    codigo: '',
    descripcion: '',
    resultado_aprendizaje: '',
    unidad_id: 0,
})
const create = () => {
    form.post(route('subtopico.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
            data.multipleSelect = false
        },
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null,
    })
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
                <div class="my-6 grid xs:grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="unidad_id" :value="lang().label.materia + '*'" />
                        <SelectInput name="unidad_id" class="mt-1 block w-full" v-model="form.unidad_id" required
                        :dataSet="UnidadsSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.unidad_id" />
                    </div>
                    
                    <div>
                        <InputLabel for="enum" :value="lang().label.enumTema" />
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
                        <InputLabel for="resultado_aprendizaje" :value="lang().label.resultado_aprendizaje" />
                        <TextInput id="resultado_aprendizaje" type="text" class="mt-1 block w-full" v-model="form.resultado_aprendizaje"
                            required :placeholder="lang().placeholder.resultado_aprendizaje" :error="form.errors.resultado_aprendizaje" />
                        <InputError class="mt-2" :message="form.errors.resultado_aprendizaje" />
                    </div>
                    
                    <div>
                        <InputLabel for="descripcion" :value="lang().label.descripcion" />
                        <TextInput id="descripcion" type="text" class="mt-1 block w-full" v-model="form.descripcion"
                            required :placeholder="lang().placeholder.descripcion" :error="form.errors.descripcion" />
                        <InputError class="mt-2" :message="form.errors.descripcion" />
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
