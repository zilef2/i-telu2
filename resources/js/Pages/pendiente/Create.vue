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
    import "vue-select/dist/vue-select.css";


const props = defineProps({
    show: Boolean,
    title: String,
    PapaSelect: Object,
})

const emit = defineEmits(["close"]);

const data = reactive({
    multipleSelect: false,
})
const today = new Date();

const form = useForm({
    nombre: '',
    descripcion: '',
    universidad_id: 1,
    codigo: '',
    enum: '',
})

const create = () => {
    form.post(route('pendiente.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
            data.multipleSelect = false
        },
        // onError: () => alert(form.errors),
        // onError: () => null,
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
                <div class="my-6 grid grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="universidad_id" :value="lang().label.universidad" />
                        <SelectInput id="universidad_id" class="mt-1 block w-full" v-model="form.universidad_id" required :dataSet="PapaSelect"> </SelectInput>
                        <InputError class="mt-2" :message="form.errors.universidad_id" />
                    </div>
                    <div class="">
                        <InputLabel for="nombre" :value="lang().label.name" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required
                            :placeholder="lang().placeholder.nombre" :error="form.errors.nombre" />
                        <InputError class="mt-2" :message="form.errors.nombre" />
                    </div>
                    <div>
                        <InputLabel for="enum" :value="lang().label.enum2" />
                        <TextInput id="enum" type="number" class="mt-1 block w-full" v-model="form.enum" required
                            :placeholder="lang().placeholder.enum" :error="form.errors.enum" />
                        <InputError class="mt-2" :message="form.errors.enum" />
                    </div>

                    <div>
                        <InputLabel for="codigo" :value="lang().label.codigo" />
                        <TextInput id="codigo" type="text" class="mt-1 block w-full" v-model="form.codigo" required
                            :placeholder="lang().placeholder.codigo" :error="form.errors.codigo" />
                        <InputError class="mt-2" :message="form.errors.codigo" />
                    </div>
                    <!-- <div>
                        <InputLabel for="descripcion" :value="lang().label.descripcion" />
                        <TextInput id="descripcion" type="text" class="mt-1 block w-full" v-model="form.descripcion" required
                            :placeholder="lang().placeholder.descripcion" :error="form.errors.descripcion" />
                        <InputError class="mt-2" :message="form.errors.descripcion" />
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
    </section>
</template>
