<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    ruta: String,
    id1:{
        type:Number,
        default:null
    } ,
    id2:{
        type:Number,
        default:0
    } ,
    texto:String
});
</script>

<template>
    <div v-if="id1">
        <Link :href="route(ruta, id1)"
            class="text-center my-4 border border-sky-700  bg-black text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-600 focus:outline-none focus:shadow-outline">
            Atrás
        </Link>
    </div>
    <div v-else>
        <Link :href="route(ruta)" class="text-center my-4 border border-sky-700  bg-black text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-600 focus:outline-none focus:shadow-outline">
            Atrás
        </Link>
    </div>
</template>