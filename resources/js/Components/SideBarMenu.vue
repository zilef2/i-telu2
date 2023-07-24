<script setup>
import {
    HomeIcon,
    UserIcon,
    CheckBadgeIcon,
    KeyIcon,
    ShieldCheckIcon,
    ClockIcon,
    PresentationChartLineIcon,
    BanknotesIcon,
} from "@heroicons/vue/24/solid";
import { Link } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import { NoUnderLines} from '@/global.js';


const data = reactive({
    showContent: false,
    showContent2: true
})
const toggleContent = () => {
    data.showContent = !data.showContent
}
const toggleContent2 = () => {
    data.showContent2 = !data.showContent2
}

</script>
<template>
    <div class="text-gray-300 pt-5 pb-20">
        <div class="flex justify-center">
            <div
                class="rounded-full flex items-center justify-center bg-primary text-gray-300 w-12 h-12 text-4xl uppercase">
                <!-- imagen del nombre -->
                {{ $page.props.auth.user.name.match(/(^\S\S?|\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("") }}
            </div>
        </div>
        <div class="text-center py-3 px-4 border-b border-gray-700 dark:border-gray-800">
            <span class="flex items-center justify-center">
                <p class="truncate text-md">{{ $page.props.auth.user.name }}</p>
                <div>
                    <CheckBadgeIcon class="ml-[2px] w-4 h-4" v-show="$page.props.auth.user.email_verified_at" />
                </div>
            </span>
            <span class="block text-xs font-medium truncate">{{ $page.props.auth.user.email }}</span>
            <span class="block text-sm font-medium truncate">{{ NoUnderLines($page.props.auth.user.roles[0].name) }}</span>
        </div>


        <ul class="space-y-2 my-4">
            <li class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('dashboard') }">
                <Link :href="route('dashboard')" class="flex items-center py-2 px-4">
                <HomeIcon class="w-6 h-5" />
                <span class="ml-3">Tablero principal</span>
                </Link>
            </li>
            <!-- <li v-show="can(['read user'])" class="py-2"> <p>{{ lang().label.data }}</p> </li> -->
            <!-- <li v-show="can(['read role', 'read permission'])" class="py-2"> <p>{{ lang().label.access }}</p> </li> -->

            <li v-show="can(['read user'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('user.index') }">
                <Link :href="route('user.index')" class="flex items-center py-2 px-4">
                <UserIcon class="w-6 h-5" />
                <span class="ml-3">{{ lang().label.user }}</span>
                </Link>
            </li>
            <button v-show="can(['isAdmin'])" @click="toggleContent" class="text-blue-500">
                {{ data.showContent ? 'Ocultar roles' : 'Mostrar roles' }}
            </button>

            <li v-if="data.showContent" v-show="can(['isAdmin'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('role.index') }">
                <Link :href="route('role.index')" class="flex items-center py-2 px-4">
                <KeyIcon class="w-6 h-5" />
                <span class="ml-3">{{ lang().label.role }}</span>
                </Link>
            </li>
            <li v-if="data.showContent" v-show="can(['read permission'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('permission.index') }">
                <Link :href="route('permission.index')" class="flex items-center py-2 px-4">
                <ShieldCheckIcon class="w-6 h-5" />
                <span class="ml-3">{{ lang().label.permission }}</span>
                </Link>
            </li>


            <!-- zone parametros -->
            <!-- <li v-show="can(['isAdmin'])" class="py-2"> <p>Parametros</p> </li> -->
            <li v-if="data.showContent" v-show="can(['isAdmin'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('parametro.index') }">
                <Link :href="route('parametro.index')" class="flex items-center py-2 px-4">
                <PresentationChartLineIcon class="w-6 h-5" />
                <span class="ml-3">{{ lang().label.parametros }}</span>
                </Link>
            </li>
<<<<<<< HEAD
            
=======
>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae







            <!-- zone normal -->
        </ul>
        <button @click="toggleContent2" v-show="can(['isAdmin'])" class="text-blue-500">{{ data.showContent2 ?
            'Ocultar universidades' : 'Mostrar universidades' }}</button>
        <ul v-if="data.showContent2" class="space-y-2 my-4">

<<<<<<< HEAD
            <!-- <li v-show="can(['read role', 'read permission'])" class="py-2"> <p>{{ lang().label.universidadCarreras }}</p> </li> -->
            <li v-show="can(['read universidad'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('universidad.index') }">
                <Link :href="route('universidad.index')" class="flex items-center py-1 px-4">
                <PresentationChartLineIcon class="w-6 h-5" />
                <span class="ml-3">{{ lang().label.universidad }}</span>
                </Link>
            </li>
            <!-- <li  -->
            <li v-show="can(['read carrera'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('carrera.index') }">
                <Link :href="route('carrera.index')" class="flex items-center py-1 px-4">
                <PresentationChartLineIcon class="w-6 h-5" />
                <span class="ml-3">{{ lang().label.carrera }}</span>
                </Link>
            </li>
            <li v-show="can(['read materia'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('materia.index') }">
                <Link :href="route('materia.index')" class="flex items-center py-1 px-4">
                <PresentationChartLineIcon class="w-6 h-5" />
                <span class="ml-3">{{ lang().label.materia }} (IA)</span>
                </Link>
            </li>
            <li v-show="can(['read Unidad'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('Unidad.index') }">
                <Link :href="route('Unidad.index')" class="flex items-center py-1 px-4">
                <PresentationChartLineIcon class="w-6 h-5" />
                <span class="ml-3">{{ lang().label.Unidad }}</span>
                </Link>
            </li>
            <li v-show="can(['read subtopico'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('subtopico.index') }">
                <Link :href="route('subtopico.index')" class="flex items-center py-1 px-4">
                <PresentationChartLineIcon class="w-6 h-5" />
                <span class="ml-3">{{ lang().label.subtopico }}</span>
                </Link>
            </li>
            <li v-show="can(['isAdmin']) || can(['profesor']) || can(['coordinador_de_programa']) || can(['coordinador_academico'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('LosPromp.index') }">
                <Link :href="route('LosPromp.index')" class="flex items-center py-2 px-4">
                <PresentationChartLineIcon class="w-6 h-5" />
                <span class="ml-3">{{ lang().label.losPromps }}</span>
                </Link>
            </li>
            <li v-show="can(['read ejercicio'])"
=======
            <!-- <li v-show="can(['read ejercicio'])"
>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('ejercicio.index') }">
                <Link :href="route('ejercicio.index')" class="flex items-center py-1 px-4">
                <PresentationChartLineIcon class="w-6 h-5" />
                <span class="ml-3">{{ lang().label.ejercicio }}</span>
                </Link>
            </li> -->
        </ul>
    </div></template>
