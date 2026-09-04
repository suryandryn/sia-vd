<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <p v-if="message" class="text-xs leading-tight text-red-600 dark:text-red-500">
        {{ message }}
    </p>
</template>
