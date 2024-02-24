<script setup>
import {
  Combobox,
  ComboboxButton,
  ComboboxInput,
  ComboboxOption,
  ComboboxOptions,
  TransitionRoot
} from "@headlessui/vue";
import {CheckIcon, ChevronUpDownIcon} from "@heroicons/vue/24/solid";
import {onMounted, ref} from 'vue';

const props = defineProps({
  nombre: String,
  modelValue: {
      type:Object,
      default: {id:0,name:'Sin datos'}
  },
  filteredPeople: Object,
  ValuesName: {
    type: Array,
    default: []
  }
});
defineEmits(['update:modelValue']);
const input = ref(null);

let query = ref('')

console.log(props.filteredPeople)
</script>


<template>
  <div id="opcinesActividadO" class="mx-4 w-[550px]">
    <label class="dark:text-white mx-1"> {{ props.nombre }}</label>
    <div loquesea="Zcombo" class="inline-flex">
      <Combobox
          :value="modelValue"
          @input="$emit('updateMyProp', $event.target.value)"
      >
        <div class="relative mt-0 w-96">
          <div
              class="relative w-full cursor-pointer overflow-hidden rounded-lg bg-white text-left shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm">
            <ComboboxInput
                class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                :displayValue="(person) => person.name"
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
              <div v-if="props.filteredPeople && props.filteredPeople.length === 0 && query !== ''"
                   class="relative cursor-default select-none px-4 py-2 text-gray-700">
                Sin resultados
              </div>

              <ComboboxOption
                  v-for="person in props.filteredPeople"
                  as="template" :key="person.id" :value="person" v-slot="{ selected, active }">
                <li class="relative cursor-default select-none py-2 pl-10 pr-4"
                    :class="{ 'bg-teal-600 text-white': active, 'text-gray-900': !active, }">
                    <span class="block truncate"
                          :class="{ 'font-medium': selected, 'font-normal': !selected }">
                      {{ person.name }}
                    </span>
                  <span
                      v-if="modelValue && modelValue.id === person.id"
                      class="absolute inset-y-0 left-0 flex items-center pl-3"
                      :class="{ 'text-white': active, 'text-teal-600': !active }"
                  ><CheckIcon class="h-5 w-5" aria-hidden="true"/></span>
                </li>
              </ComboboxOption>
            </ComboboxOptions>
          </TransitionRoot>
        </div>
      </Combobox>
    </div>
  </div>
</template>
