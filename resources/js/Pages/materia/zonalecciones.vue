<script setup>
import { reactive, watch, ref, watchEffect, onMounted } from 'vue';
// import { vectorSelect, formatDate, CalcularEdad, CalcularSexo }from '@/global.ts';;

const props = defineProps({
    temaIDSelected: Number,
    ejercicio: Object,
    temaSelectedName: String,
    subtopSelected: Number,
    Unidad: Object,
    nivelSelect: Object,

})
const data = reactive({
    params: {
        // search: props.filters.search,
    },
    nivel: 1,
    pregunta: '',
    respuestagpt: '',
    nivelSelect: '',
})

const emit = defineEmits(["submitGPT"]);
onMounted(() => { })
const preguntarGPT = (ejercicioID) => {
    emit("submitGPT", ejercicioID)
    // <p @click="submitToGPT(ejercicio.id)"
}

watchEffect(() => {
    if (props.temaIDSelected != null) {

        // subtema Array.from({ length: nsubtemas.value }).map((_, index) => index);
    }
})
</script>
<template>
    <div v-if="props.temaIDSelected != null" class="container px-5 py-1 mx-auto">
        <div class="flex flex-col text-center w-full mb-2">
            <!-- <h2 class="text-xs text-indigo-500 tracking-widest font-medium title-font mb-1">Escojer</h2> -->
            <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">Lecciones</h1>
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                Subtopico: <b>{{ props.temaSelectedName }} </b>
            </p>
        </div>

    </div>
    <section v-if="props.Unidad != null" class="text-gray-600 body-font">
        <div class="container px-5 py-3 mx-auto">
            <div class="flex flex-wrap -m-4">
                <!-- <div v-for="(ejercicio, index) in Unidad[props.temaSelected].sub[props.subtopSelected].ejer" -->
                <div v-if="Unidad.sub[(props.subtopSelected)]"
                    v-for="(ejercicio, index) in Unidad.sub[(props.subtopSelected)].ejercicios" :key="index"
                    class="p-4 md:w-1/3">
                    <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                        <div class="p-6">
                            <p @click="preguntarGPT(ejercicio.id)"
                                class="underline text-sky-300 cursor-help leading-relaxed mb-3">
                                {{ ejercicio.nombre }}.
                            </p>
                        </div>
                    </div>
                </div>
                <div v-else class="py-4">
                    <p class="text-xl">Sin unidads</p>
                </div>
            </div>
        </div>
    </section>
</template>

<style>
textarea {
    @apply px-3 py-2 border border-gray-300 rounded-md;
}
</style>