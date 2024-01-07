<script setup>
import { toRefs } from 'vue';
import { usePage } from '@inertiajs/vue3';

</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
    />
</template>
