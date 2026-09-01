<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { ref, watch } from 'vue';

const page = usePage<{ flash: { success?: string; error?: string } }>();

type Fakultas = { id: number; kode_fakultas: string; nama_fakultas: string; dekan?: { user?: { name?: string } } | null };
type Pagination = { data: Fakultas[]; links: { url: string | null; label: string; active: boolean }[]; total: number };

const props = defineProps<{ fakultas: Pagination; search?: string }>();

const search = ref(props.search ?? '');
watch(search, (value) => router.get(route('admin.fakultas.index'), { search: value }, { preserveState: true, preserveScroll: true }));

const remove = (item: Fakultas) => {
    if (window.confirm(`Hapus Fakultas ${item.nama_fakultas}?`)) {
        router.delete(route('admin.fakultas.destroy', item.id));
    }
};
</script>

<template>
    <Head title="Fakultas" />
    <AppLayout :breadcrumbs="[{ title: 'Fakultas', href: route('admin.fakultas.index') }]">
        <div class="flex flex-col gap-4 p-4">
            <div v-if="page.props.flash?.success" class="rounded border border-green-300 bg-green-50 p-3 text-green-800" role="alert">{{ page.props.flash.success }}</div>
            <div v-if="page.props.flash?.error" class="rounded border border-red-300 bg-red-50 p-3 text-red-800" role="alert">{{ page.props.flash.error }}</div>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h1 class="text-xl font-semibold">Fakultas</h1>
                <div class="flex items-center gap-2">
                    <Input v-model="search" placeholder="Cari kode atau nama fakultas" class="w-64" />
                    <Link :href="route('admin.fakultas.create')"><Button>Tambah Fakultas</Button></Link>
                </div>
            </div>
            <div class="overflow-x-auto rounded-lg border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="p-3">Kode Fakultas</th>
                            <th class="p-3">Nama Fakultas</th>
                            <th class="p-3">Dekan</th>
                            <th class="p-3">Tanggal Berdiri</th>
                            <th class="p-3">Nomor Telepon</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in props.fakultas.data" :key="item.id" class="border-b last:border-0">
                            <td class="p-3">{{ item.kode_fakultas }}</td>
                            <td class="p-3">{{ item.nama_fakultas }}</td>
                            <td class="p-3">{{ item.dekan?.user?.name ?? '-' }}</td>
                            <td class="p-3">{{ item.tanggal_berdiri ?? '-' }}</td>
                            <td class="p-3">{{ item.no_telp ?? '-' }}</td>
                            <td class="p-3">{{ item.email ?? '-' }}</td>
                            <td class="flex gap-2 p-3">
                                <Link :href="route('admin.fakultas.edit', item.id)" class="underline">Edit</Link>
                                <button type="button" class="text-destructive underline" @click="remove(item)">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="!props.fakultas.data.length">
                            <td colspan="7" class="p-3 text-center text-muted-foreground">Belum ada data.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <nav v-if="props.fakultas.total > 0" class="flex flex-wrap gap-2" aria-label="Pagination">
                <Link
                    v-for="link in props.fakultas.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    preserve-scroll
                    preserve-state
                    class="rounded border px-3 py-1 text-sm"
                    :class="{ 'bg-primary text-primary-foreground': link.active, 'pointer-events-none opacity-50': !link.url }"
                    v-html="link.label"
                />
            </nav>
        </div>
    </AppLayout>
</template>
