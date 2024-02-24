<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import {watchEffect, reactive, ref, computed} from 'vue';
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    TransitionRoot
} from "@headlessui/vue";
import {CheckIcon, ChevronUpDownIcon} from "@heroicons/vue/24/solid";
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


let query = ref('')
let filterCombo = computed(() =>
    query.value === ''
        ? props.UnidadsSelect
        : props.UnidadsSelect.filter((person) =>
            person.name
                .toLowerCase()
                .replace(/\s+/g, '')
                .includes(query.value.toLowerCase().replace(/\s+/g, ''))
        )
)

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
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
                      <div class="block w-full">
                          <InputLabel value="Unidad *" />

                          <div id="opcinesActividadO" class="mx-1 w-[550px]">
                              <div class="inline-flex">
                                  <Combobox v-model="form.unidad_id">
                                      <div class="relative mt-1 w-96">
                                          <div class="relative w-full cursor-default overflow-hidden rounded-lg bg-white text-left shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm">
                                              <ComboboxInput
                                                  class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                                                  :displayValue="(person) => person?.name"
                                                  @change="query = $event.target.value"
                                              />
                                              <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                                                  <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true"/>
                                              </ComboboxButton>
                                          </div>
                                          <TransitionRoot
                                              leave="transition ease-in duration-100"
                                              leaveFrom="opacity-100"
                                              leaveTo="opacity-0"
                                              @after-leave="query = ''">
                                              <ComboboxOptions
                                                  class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md
                                                       bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                                                  <div v-if="filterCombo.length === 0 && query !== ''" class="relative cursor-default select-none px-4 py-2 text-gray-700">
                                                      Sin resultados
                                                  </div>

                                                  <ComboboxOption
                                                      v-for="person in filterCombo"
                                                      as="template" :key="person.id" :value="person" v-slot="{ selected, active }">
                                                      <li class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                          :class="{ 'bg-teal-600 text-white': active, 'text-gray-900': !active, }">
                                                        <span class="block truncate" :class="{ 'font-medium': selected, 'font-normal': !selected }">
                                                          {{ person.name }}
                                                        </span>
                                                          <span
                                                              v-if="form.unidad_id && form.unidad_id.id === person.id"
                                                              class="absolute inset-y-0 left-0 flex items-center pl-3"
                                                              :class="{ 'text-white': active, 'text-teal-600': !active }"
                                                          >
                                                          <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                                        </span>
                                                      </li>
                                                  </ComboboxOption>
                                              </ComboboxOptions>
                                          </TransitionRoot>
                                      </div>
                                  </Combobox>
                              </div>
                          </div>
                      </div>
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
                        <InputLabel for="resultado_aprendizaje" :value="lang().label.resultado_aprendizaje + '*'" />
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
