<script setup>
import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import Modal from '@/Components/Modal.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { useForm } from '@inertiajs/vue3';
    // import Checkbox from '@/Components/Checkbox.vue';
    import { reactive, watchEffect } from 'vue';
const props = defineProps({
    show: Boolean,
    title: String,
})

const emit = defineEmits(["close"]);

const data = reactive({
    multipleSelect: false,
})

const form = useForm({
    // fecha_ini: '2018-06-07T01:00',
    fecha_ini: '',
    fecha_fin: '',
    horas_trabajadas: '',
    observaciones: '',
})

const create = () => {
    form.post(route('Reportes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
            data.multipleSelect = false
        },
        onError: () =>{
            // alert(JSON.stringify(form.errors, null, 4));
            null
        },
        onFinish: () => null,
    })
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
    if( Date.parse(form.fecha_ini) > Date.parse(form.fecha_fin) ){
        form.horas = 0
        // form.horas_trabajadas = form.fecha_fin.substr(1,3);
    }else{
        form.horas_trabajadas = parseInt((Date.parse(form.fecha_fin) - Date.parse(form.fecha_ini))/(3600*1000) );
    }
    //#TOASK: validate if hours is not superior than 24
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
                        <InputLabel for="fecha_ini" :value="lang().label.fecha_ini" />
                        <input type="datetime-local" id="fecha_ini" 
                            v-model="form.fecha_ini" required
                            name="fecha_fin" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel for="fecha_fin" :value="lang().label.fecha_fin" />
                        <input type="datetime-local" id="fecha_fin" 
                            v-model="form.fecha_fin" required
                            name="fecha_fin" class="mt-1 block w-full">
                    </div>

                    <div>
                        <InputLabel for="horas_trabajadas" :value="lang().label.horas_trabajadas" />
                        <TextInput id="horas_trabajadas" type="number" class="mt-1 block w-full" v-model="form.horas_trabajadas" disabled
                            :placeholder="lang().placeholder.horas_trabajadas" :error="form.errors.horas_trabajadas" />
                        <InputError class="mt-2" :message="form.errors.horas_trabajadas" />
                    </div>
                </div>
                <div class="my-6 ">
                    <InputLabel for="observaciones" :value="lang().label.observaciones" />
                        <textarea
                            id="observaciones" type="text" v-model="form.observaciones"
                            class="mt-1 block w-full rounded-md shadow-sm dark:bg-black dark:text-white placeholder:text-gray-400 placeholder:dark:text-gray-400/50"
                            cols="30" rows="3" :error="form.errors.observaciones">
                        </textarea>
                    <InputError class="mt-2" :message="form.errors.observaciones" />
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
