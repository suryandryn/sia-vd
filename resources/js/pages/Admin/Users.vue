<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const page = usePage<{ flash: { success?: string; error?: string } }>();

type User = { id: number; name: string; username: string; email: string; profile?: { nomor_induk?: string | null; nidn?: string | null; nim?: string | null } | null };
type Pagination = { data: User[]; links: { url: string | null; label: string; active: boolean }[]; from: number | null; to: number | null; total: number };

const props = defineProps<{ title: string; type: string; users: Pagination; search?: string }>();

const search = ref(props.search ?? '');
watch(search, (value) => router.get(route(`admin.users.${props.type}`), { search: value }, { preserveState: true, preserveScroll: true }));

const searchPlaceholder: Record<string, string> = {
    dosen: 'Cari nama atau NIDN',
    mahasiswa: 'Cari nama atau NIM',
    karyawan: 'Cari nama atau Nomor Induk',
};
const idLabel: Record<string, string> = { dosen: 'NIDN', mahasiswa: 'NIM', karyawan: 'Nomor Induk' };
const createLabel: Record<string, string> = { dosen: 'Tambah Dosen', mahasiswa: 'Tambah Mahasiswa' };
const idValue = (user: User): string => (user.profile?.nidn ?? user.profile?.nim ?? user.profile?.nomor_induk) ?? '-';

const remove = (user: User) => {
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
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h1 class="text-xl font-semibold">{{ props.title }}</h1>
                <div class="flex items-center gap-2">
                    <Input v-model="search" :placeholder="searchPlaceholder[props.type]" class="w-64" />
                    <Link v-if="props.type !== 'karyawan'" :href="route(`admin.users.${props.type}.create`)"><Button>{{ createLabel[props.type] }}</Button></Link>
                </div>
            </div>
            <div class="overflow-x-auto rounded-lg border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="p-3">No.</th>
                            <th class="p-3">Nama</th>
                            <th class="p-3">{{ idLabel[props.type] }}</th>
                            <th class="p-3">Username</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, index) in props.users.data" :key="user.username" class="border-b last:border-0">
                            <td class="p-3">{{ (props.users.from ?? 0) + index }}</td>
                            <td class="p-3">{{ user.name }}</td>
                            <td class="p-3">{{ idValue(user) }}</td>
                            <td class="p-3">{{ user.username }}</td>
                            <td class="p-3">{{ user.email }}</td>
                            <td class="flex gap-2 p-3">
                                <Link :href="route(`admin.users.${props.type}.edit`, user.id)" title="Edit" aria-label="Edit">
                                    <Button variant="outline" class="size-8 p-0 text-blue-600 hover:text-blue-700" aria-hidden="true"><Pencil class="size-4" /></Button>
                                </Link>
                                <button type="button" title="Hapus" aria-label="Hapus" @click="remove(user)">
                                    <Button variant="outline" class="size-8 p-0 text-red-600 hover:text-red-700" aria-hidden="true"><Trash2 class="size-4" /></Button>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!props.users.data.length"><td colspan="6" class="p-3 text-center text-muted-foreground">Belum ada data.</td></tr>
                    </tbody>
                </table>
            </div>
            <nav v-if="props.users.total > 0" class="flex flex-wrap gap-2" aria-label="Pagination">
                <Link v-for="link in props.users.links" :key="link.label" :href="link.url ?? '#'" preserve-scroll preserve-state class="rounded border px-3 py-1 text-sm" :class="{ 'bg-primary text-primary-foreground': link.active, 'pointer-events-none opacity-50': !link.url }" v-html="link.label" />
            </nav>
        </div>
    </AppLayout>
</template>
