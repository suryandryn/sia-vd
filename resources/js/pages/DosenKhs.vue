<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Input KHS" />
    <AppLayout :breadcrumbs="[{ title: 'Input KHS', href: '#' }]">
        <div class="p-4">
            <h1 class="text-xl font-semibold">Input KHS</h1>
            <p class="mt-2 text-muted-foreground">Form input nilai KHS mahasiswa sedang disiapkan.</p>
        </div>
    </AppLayout>
</template>
