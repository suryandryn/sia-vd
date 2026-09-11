<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    type: 'tugas' | 'materi' | 'quiz';
    items: { data: any[]; links: any[]; total: number };
}>();

const labels = { tugas: 'Tugas', materi: 'Materi', quiz: 'Quiz' };
const title = labels[props.type];
const itemTitle = (item: any) => item.judul_tugas ?? item.judul_materi ?? item.nama_quiz;
</script>

<template>
    <Head :title="title" />
    <AppLayout :breadcrumbs="[{ title, href: route(`mahasiswa.${props.type}`) }]">
        <div class="min-h-full bg-[#f6f5f4]">
            <div class="mx-auto flex w-full max-w-[1000px] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
                <div class="space-y-1">
                    <h1 class="text-[26px] font-bold leading-[1.23] tracking-[-0.625px] text-black">{{ title }}</h1>
                    <p class="text-sm leading-5 text-[#615d59]">Daftar {{ title.toLowerCase() }} dari kelas yang Anda ambil.</p>
                </div>
                <div class="overflow-hidden rounded-xl border border-[#e6e6e6] bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="border-b border-[#e6e6e6] bg-[#f6f5f4]">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Nama</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Kelas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e6e6e6]">
                                <tr v-for="item in props.items.data" :key="item.id">
                                    <td class="px-4 py-3 text-[15px] text-black">
                                        <Link v-if="props.type === 'materi'" :href="route('mahasiswa.materi.show', item.id)" class="font-medium text-[#0075de] hover:underline">{{ itemTitle(item) }}</Link>
                                        <Link v-else-if="props.type === 'tugas'" :href="route('mahasiswa.tugas.show', item.id)" class="font-medium text-[#0075de] hover:underline">{{ itemTitle(item) }}</Link>
                                        <template v-else>{{ itemTitle(item) }}</template>
                                    </td>
                                    <td class="px-4 py-3 text-[15px] text-[#615d59]">
                                        {{ item.kelas_kuliah?.kode_kelas ?? '-' }} — {{ item.kelas_kuliah?.mata_kuliah?.nama_matkul ?? item.kelas_kuliah?.mataKuliah?.nama_matkul ?? '' }}
                                    </td>
                                </tr>
                                <tr v-if="!props.items.data.length">
                                    <td colspan="3" class="px-4 py-16 text-center text-sm text-[#615d59]">Belum ada {{ title.toLowerCase() }}.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <nav v-if="props.items.total > 0" class="flex flex-wrap gap-2">
                    <a v-for="link in props.items.links" :key="link.label" :href="link.url ?? '#'" class="rounded-lg border px-3 py-1.5 text-sm" :class="link.active ? 'border-[#0075de] bg-[#0075de] text-white' : 'border-[#e6e6e6] bg-white'" v-html="link.label" />
                </nav>
            </div>
        </div>
    </AppLayout>
</template>
