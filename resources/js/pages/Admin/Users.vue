<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const page = usePage<{ flash: { success?: string; error?: string } }>();

type User = { id: number; name: string; username: string; email: string; profile?: { nidn?: string | null; nim?: string | null } | null };
type Pagination = { data: User[]; links: { url: string | null; label: string; active: boolean }[]; from: number | null; to: number | null; total: number };

const props = defineProps<{ title: string; type: string; users: Pagination }>();
const remove = (user: User & { id: number }) => {
    if (window.confirm(`Hapus user ${user.name}?`)) {
        router.delete(route(`admin.users.${props.type}.destroy`, user.id));
    }
};
</script>

<template>
    <Head :title="props.title" />
    <AppLayout :breadcrumbs="[{ title: props.title, href: '#' }]">
        <div class="flex flex-col gap-4 p-4">
            <div v-if="page.props.flash?.success" class="rounded border border-green-300 bg-green-50 p-3 text-green-800" role="alert">{{ page.props.flash.success }}</div>
            <div v-if="page.props.flash?.error" class="rounded border border-red-300 bg-red-50 p-3 text-red-800" role="alert">{{ page.props.flash.error }}</div>
            <div class="flex items-center justify-between"><h1 class="text-xl font-semibold">{{ props.title }}</h1><Link v-if="props.type !== 'karyawan'" :href="route(`admin.users.${props.type}.create`)" class="rounded border px-3 py-2 text-sm">Tambah</Link></div>
            <div class="overflow-x-auto rounded-lg border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr><th class="p-3">Nama</th><th class="p-3">ID</th><th class="p-3">Username</th><th class="p-3">Email</th></tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in props.users.data" :key="user.username" class="border-b last:border-0">
                            <td class="p-3">{{ user.name }}</td><td class="p-3">{{ (props.type === 'dosen' ? user.profile?.nidn : user.profile?.nim) ?? '-' }}</td><td class="p-3">{{ user.username }}</td><td class="p-3">{{ user.email }}</td><td class="flex gap-2 p-3"><Link :href="route(`admin.users.${props.type}.edit`, user.id)" class="underline">Edit</Link><button type="button" class="text-destructive underline" @click="remove(user)">Hapus</button></td>
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
