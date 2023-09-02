<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-emerald-600 dark:bg-gray-800 border border-sky-300 dark:border-white
         rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest shadow-sm
          hover:bg-emerald-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-800
           disabled:opacity-25 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>
