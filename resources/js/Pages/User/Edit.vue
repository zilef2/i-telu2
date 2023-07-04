<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';

const props = defineProps({
    show: Boolean,
    title: String,
    user: Object,
    roles: Object,
})

const emit = defineEmits(["close"]);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',

    sexo:'',
    identificacion:'',
    fecha_nacimiento:'',
    semestre:'',
    semestre_mas_bajo:1,
    limite_token_general:'',
    limite_token_leccion:'',
});

const update = () => {
    form.put(route('user.update', props.user?.id), {
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
        form.name = props.user?.name
        form.email = props.user?.email
        form.role = props.user?.roles == 0 ? '' : props.user?.roles[0].name

        form.identificacion = props.user?.identificacion
        form.sexo = props.user?.sexo
        form.fecha_nacimiento = props.user?.fecha_nacimiento
        form.semestre = props.user?.semestre
        form.semestre_mas_bajo = props.user?.semestre_mas_bajo
        form.limite_token_general = props.user?.limite_token_general
        form.limite_token_leccion = props.user?.limite_token_leccion

        form.errors = {}
    }
})

const roles = props.roles?.map(role => ({ label: role.name, value: role.name }))

const sexos = [ { label: 'Masculino', value: 'Masculino' }, { label: 'Femenino', value: 'Femenino' } ];

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
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
                            <SelectInput id="role" class="mt-1 block w-full" v-model="form.role" required :dataSet="roles"> </SelectInput>
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
                            <VueDatePicker :is-24="false" :day-names="daynames" :format="formatToVue" :flow="flow" auto-apply
                                :enable-time-picker="false" id="fecha_nacimiento"
                                class="mt-1 block w-full" v-model="form.fecha_nacimiento" required
                                :placeholder="lang().placeholder.fecha_nacimiento" :error="form.errors.fecha_nacimiento" />
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
                                v-model="form.semestre_mas_bajo" disabled :placeholder="lang().placeholder.semestre_mas_bajo"
                                :error="form.errors.semestre_mas_bajo" />
                            <InputError class="mt-2" :message="form.errors.semestre_mas_bajo" />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="limite_token_leccion" :value="lang().label.limite_token_leccion" />
                        <TextInput id="limite_token_leccion" type="number" class="mt-1 block w-full"
                            v-model="form.limite_token_leccion" :placeholder="lang().placeholder.limite_token_leccion"
                            :error="form.errors.limite_token_leccion" />
                        <InputError class="mt-2" :message="form.errors.limite_token_leccion" />
                    </div>
                    <!-- limite_token_general -->


                    <!-- pass -->
                    <!-- <div>
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
                    </div> -->
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
