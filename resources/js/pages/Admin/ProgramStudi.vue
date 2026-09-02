<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { ref, watch } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';

const page = usePage<{ flash: { success?: string; error?: string } }>();

type ProgramStudi = {
    id: number;
    kode_prodi: string;
    nama_prodi: string;
    jenjang: string;
    status_akreditasi: string;
    fakultas?: { nama_fakultas?: string } | null;
    ketuaProgramStudi?: { user?: { name?: string } } | null;
};
type Pagination = { data: ProgramStudi[]; links: { url: string | null; label: string; active: boolean }[]; total: number };

const props = defineProps<{ programStudis: Pagination; search?: string }>();

const search = ref(props.search ?? '');
watch(search, (value) => router.get(route('admin.program-studi.index'), { search: value }, { preserveState: true, preserveScroll: true }));

const remove = (item: ProgramStudi) => {
    if (window.confirm(`Hapus Program Studi ${item.nama_prodi}?`)) {
        router.delete(route('admin.program-studi.destroy', item.id));
    }
};
</script>

<template>
    <Head title="Program Studi" />
    <AppLayout :breadcrumbs="[{ title: 'Program Studi', href: route('admin.program-studi.index') }]">
        <div class="flex flex-col gap-4 p-4">
            <div v-if="page.props.flash?.success" class="rounded border border-green-300 bg-green-50 p-3 text-green-800" role="alert">{{ page.props.flash.success }}</div>
            <div v-if="page.props.flash?.error" class="rounded border border-red-300 bg-red-50 p-3 text-red-800" role="alert">{{ page.props.flash.error }}</div>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h1 class="text-xl font-semibold">Program Studi</h1>
                <div class="flex items-center gap-2">
                    <Input v-model="search" placeholder="Cari kode atau nama program studi" class="w-64" />
                    <Link :href="route('admin.program-studi.create')"><Button>Tambah Program Studi</Button></Link>
                </div>
            </div>
            <div class="overflow-x-auto rounded-lg border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="p-3">Kode Program Studi</th>
                            <th class="p-3">Nama Program Studi</th>
                            <th class="p-3">Fakultas</th>
                            <th class="p-3">Jenjang</th>
                            <th class="p-3">Status Akreditasi</th>
                            <th class="p-3">Kaprodi</th>
                            <th class="p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in props.programStudis.data" :key="item.id" class="border-b last:border-0">
                            <td class="p-3">{{ item.kode_prodi }}</td>
                            <td class="p-3">{{ item.nama_prodi }}</td>
                            <td class="p-3">{{ item.fakultas?.nama_fakultas ?? '-' }}</td>
                            <td class="p-3">{{ item.jenjang }}</td>
                            <td class="p-3">{{ item.status_akreditasi }}</td>
                            <td class="p-3">{{ item.ketuaProgramStudi?.user?.name ?? '-' }}</td>
                            <td class="flex gap-2 p-3">
                                <Link :href="route('admin.program-studi.edit', item.id)" title="Edit" aria-label="Edit">
                                    <Button variant="outline" class="size-8 p-0 text-blue-600 hover:text-blue-700" aria-hidden="true"><Pencil class="size-4" /></Button>
                                </Link>
                                <button type="button" title="Hapus" aria-label="Hapus" @click="remove(item)">
                                    <Button variant="outline" class="size-8 p-0 text-red-600 hover:text-red-700" aria-hidden="true"><Trash2 class="size-4" /></Button>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!props.programStudis.data.length">
                            <td colspan="7" class="p-3 text-center text-muted-foreground">Belum ada data.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <nav v-if="props.programStudis.total > 0" class="flex flex-wrap gap-2" aria-label="Pagination">
                <Link
                    v-for="link in props.programStudis.links"
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
