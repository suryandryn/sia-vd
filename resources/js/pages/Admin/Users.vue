<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

type User = { name: string; username: string; email: string; nomor_induk: string | null };
type Pagination = { data: User[]; links: { url: string | null; label: string; active: boolean }[]; from: number | null; to: number | null; total: number };

const props = defineProps<{ title: string; type: string; users: Pagination }>();
</script>

<template>
    <Head :title="props.title" />
    <AppLayout :breadcrumbs="[{ title: props.title, href: '#' }]">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between"><h1 class="text-xl font-semibold">{{ props.title }}</h1><Link v-if="props.type !== 'karyawan'" :href="route(`admin.users.${props.type}.create`)" class="rounded border px-3 py-2 text-sm">Tambah</Link></div>
            <div class="overflow-x-auto rounded-lg border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr><th class="p-3">Nama</th><th class="p-3">Nomor Induk</th><th class="p-3">Username</th><th class="p-3">Email</th></tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in props.users.data" :key="user.username" class="border-b last:border-0">
                            <td class="p-3">{{ user.name }}</td><td class="p-3">{{ user.nomor_induk ?? '-' }}</td><td class="p-3">{{ user.username }}</td><td class="p-3">{{ user.email }}</td><td class="p-3"><Link :href="route(`admin.users.${props.type}.edit`, user.id)" class="underline">Edit</Link></td>
                        </tr>
                        <tr v-if="!props.users.data.length"><td colspan="3" class="p-3 text-center text-muted-foreground">Belum ada data.</td></tr>
                    </tbody>
                </table>
            </div>
            <nav v-if="props.users.total > 0" class="flex flex-wrap gap-2" aria-label="Pagination">
                <Link v-for="link in props.users.links" :key="link.label" :href="link.url ?? '#'" preserve-scroll preserve-state class="rounded border px-3 py-1 text-sm" :class="{ 'bg-primary text-primary-foreground': link.active, 'pointer-events-none opacity-50': !link.url }" v-html="link.label" />
            </nav>
        </div>
    </AppLayout>
</template>
