<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { watchEffect,ref } from 'vue';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    show: Boolean,
    title: String,
    roles: Object,
})
const emit = defineEmits(["close"]);

// VueDatePicker
const formatToVue = (date) => {
  const day = date.getDate();
  const month = date.getMonth() + 1;
  const year = date.getFullYear();

  return `${day}/${month}/${year}`;
}
const flow = ref(['year', 'month', 'calendar']);
let anio = ref(0);

const anioHoy = new Date().getFullYear();
const anio18 = anioHoy - 18;
// VueDatePicker


const form = useForm({
    name: 'alejo pruebas',
    email: 'ajelof22@gmail.com',
    password: '',
    password_confirmation: '',
    role: 'trabajador',

    identificacion: 1152194566,
    sexo: 0,
    // sexo: 'Masculino',
    fecha_nacimiento: anio18+'-12-01T00:00',
    semestre: '1',
    semestre_mas_bajo: 1,
    limite_token_leccion: '3',
    pgrado: 'pregrado',//Bachillerato, pregrado o posgrado

    // name: '',
    // email: '',
    // password: '',
    // password_confirmation: '',
    // role: 'trabajador',

    // sexo:'',
    // fecha_nacimiento:'',
    // semestre:'1',
    // semestre_mas_bajo:1,
    // // limite_token_general:'',
    // limite_token_leccion:'',
    // pgrado: '',//Bachillerato, pregrado o posgrado

})

const create = () => {
    form.post(route('user.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}

watchEffect(() => {
    if (props.show) {

        form.errors = {}
    }
    if(form.fecha_nacimiento)
        anio = parseInt(anioHoy - new Date(form.fecha_nacimiento).getFullYear())
    if(form.semestre)
        form.semestre_mas_bajo = form.semestre
})
//TOSTUDY
const roles = props.roles?.map(role => ({
    label: role.name.replace(/_/g," "),
    value: (role.name)
}))

//very usefull
const sexos = [ { label: 'Masculino', value: 0 }, { label: 'Femenino', value: 1 } ];
const daynames = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'];

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="my-6 space-y-4">
                    <div>
                        <InputLabel for="name" :value="lang().label.name" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required
                            :placeholder="lang().placeholder.name" :error="form.errors.name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="email" :value="lang().label.email" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email"
                            :placeholder="lang().placeholder.email" :error="form.errors.email" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div>
                        <InputLabel for="identificacion" :value="lang().label.identificacion" />
                        <TextInput id="identificacion" type="text" class="mt-1 block w-full" v-model="form.identificacion"
                            :placeholder="lang().placeholder.identificacion" :error="form.errors.identificacion" />
                        <InputError class="mt-2" :message="form.errors.identificacion" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="role" :value="lang().label.role" />
                            <SelectInput id="role" class="mt-1 block w-full" v-model="form.role" required :dataSet="roles">
                            </SelectInput>
                            <InputError class="mt-2" :message="form.errors.role" />
                        </div>
                        <!-- otros campos -->
                        <div>
                            <InputLabel for="sexo" :value="lang().label.sexo" />
                            <SelectInput id="sexo" class="mt-1 block w-full" v-model="form.sexo" required :dataSet="sexos">
                            </SelectInput>
                            <InputError class="mt-2" :message="form.errors.sexo" />
                        </div>

                        <div>
                            <InputLabel for="fecha_nacimiento" :value="lang().label.fecha_nacimiento" />
                            <VueDatePicker :is-24="false" :day-names="daynames" :format="formatToVue" :flow="flow"
                                auto-apply :enable-time-picker="false" id="fecha_nacimiento" class="mt-1 block w-full"
                                v-model="form.fecha_nacimiento" required :placeholder="lang().placeholder.fecha_nacimiento"
                                :error="form.errors.fecha_nacimiento" />
                            <InputError class="mt-2" :message="form.errors.fecha_nacimiento" />
                        </div>

                        <div class="">
                            <InputLabel for="anio" :value="lang().label.anio" />
                            <TextInput id="anio" type="number" disabled class="bg-gray-300 mt-1 block w-full" v-model="anio"
                                placeholder="Años" />
                        </div>
                        <div class="">
                            <InputLabel for="semestre" :value="lang().label.semestre" />
                            <TextInput id="semestre" type="number" class="mt-1 block w-full" v-model="form.semestre"
                                :placeholder="lang().placeholder.semestre" :error="form.errors.semestre" />
                            <InputError class="mt-2" :message="form.errors.semestre" />
                        </div>
                        <div class="">
                            <InputLabel for="semestre_mas_bajo" :value="lang().label.semestre_mas_bajo" />
                            <TextInput id="semestre_mas_bajo" type="number" class="bg-gray-300 mt-1 block w-full"
                                v-model="form.semestre_mas_bajo" disabled
                                :placeholder="lang().placeholder.semestre_mas_bajo"
                                :error="form.errors.semestre_mas_bajo" />
                            <InputError class="mt-2" :message="form.errors.semestre_mas_bajo" />
                        </div>
                        <div>
                            <InputLabel for="limite_token_leccion" :value="lang().label.limite_token_leccion" />
                            <TextInput id="limite_token_leccion" type="number" class="mt-1 block w-full"
                                v-model="form.limite_token_leccion" :placeholder="lang().placeholder.limite_token_leccion"
                                :error="form.errors.limite_token_leccion" />
                            <InputError class="mt-2" :message="form.errors.limite_token_leccion" />
                        </div>
                        <div>
                            <InputLabel for="pgrado" :value="lang().label.pgrado" />
                            <TextInput id="pgrado" type="text" class="mt-1 block w-full" v-model="form.pgrado"
                                :placeholder="lang().placeholder.pgrado" :error="form.errors.pgrado" />
                            <InputError class="mt-2" :message="form.errors.pgrado" />
                        </div>
                    </div>
                    <!-- limite_token_general -->


                    <!-- pass -->
                    <div>
                        <InputLabel for="password" :value="lang().label.password" />
                        <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password"
                            :placeholder="lang().placeholder.password" :error="form.errors.password" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                    <div>
                        <InputLabel for="password_confirmation" :value="lang().label.password_confirmation" />
                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                            v-model="form.password_confirmation" :placeholder="lang().placeholder.password_confirmation"
                            :error="form.errors.password_confirmation" />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
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
