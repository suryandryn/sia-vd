<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{ title: string }>();
</script>

<template>
    <Head :title="title" />
    <AppLayout :breadcrumbs="[{ title, href: '#' }]">
        <div class="p-4">
            <h1 class="text-xl font-semibold">{{ title }}</h1>
            <p class="mt-2 text-muted-foreground">Halaman {{ title }} sedang disiapkan.</p>
        </div>
    </AppLayout>
</template>
