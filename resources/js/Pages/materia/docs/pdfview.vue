<script setup>


const props = defineProps({
    archivinid: Number,
    resumen: String,
})

</script>

<template>
    <!-- width="100%" height="600px"  -->
    <!-- <p class="mt-16 mb-4">{{ resumen }}</p> -->
    <embed :src="route('verPdf', archivinid)" type="application/pdf" 
        class="w-full h-screen"
        />
</template>
