<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';

type Tugas = { id: number; judul_tugas: string; file?: string[] | null; tenggat_waktu?: string | null; catatan?: string | null; uploader?: { name: string } | null; kelasKuliah?: { kode_kelas: string; mataKuliah?: { nama_matkul: string } | null } | null };
type Submission = { file_jawaban: string[]; nilai?: string | null } | null;
const props = defineProps<{ tugas: Tugas; submission: Submission }>();
const form = useForm<{ file_jawaban: File[] }>({ file_jawaban: [] });
const files = (value?: string[] | null) => value ?? [];
const name = (path: string) => path.split('/').pop() ?? path;
const submit = () => form.post(route('mahasiswa.tugas.pengumpulan.store', props.tugas.id), { forceFormData: true });
</script>
<template>
    <Head :title="props.tugas.judul_tugas" />
    <AppLayout :breadcrumbs="[{ title: 'Tugas', href: route('mahasiswa.tugas') }, { title: props.tugas.judul_tugas, href: '#' }]">
        <div class="min-h-full bg-[#f6f5f4]"><div class="mx-auto flex w-full max-w-[1000px] flex-col gap-4 px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-start justify-between gap-3"><div><h1 class="text-[26px] font-bold text-black">{{ props.tugas.judul_tugas }}</h1><p class="text-sm text-[#615d59]">Detail tugas perkuliahan.</p></div><Link :href="route('mahasiswa.tugas')" class="rounded-lg border border-[#e6e6e6] bg-white px-4 py-2 text-sm font-medium text-black">Kembali</Link></div>
            <section class="rounded-xl border border-[#e6e6e6] bg-white p-6 shadow-sm"><dl class="grid gap-4 sm:grid-cols-2"><div><dt class="text-xs text-[#a39e98]">Kelas</dt><dd class="font-medium">{{ props.tugas.kelasKuliah?.kode_kelas }} — {{ props.tugas.kelasKuliah?.mataKuliah?.nama_matkul ?? '-' }}</dd></div><div><dt class="text-xs text-[#a39e98]">Tenggat</dt><dd class="font-medium">{{ props.tugas.tenggat_waktu ?? '-' }}</dd></div></dl><div class="mt-6 border-t border-[#e6e6e6] pt-5"><h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Catatan</h2><p class="mt-2 whitespace-pre-line text-[15px]">{{ props.tugas.catatan || '-' }}</p></div><div class="mt-6 border-t border-[#e6e6e6] pt-5"><h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">File Tugas</h2><div v-if="files(props.tugas.file).length" class="mt-3 space-y-2"><a v-for="path in files(props.tugas.file)" :key="path" :href="`/storage/${path}`" target="_blank" class="block text-sm text-[#0075de] hover:underline">{{ name(path) }}</a></div><p v-else class="mt-2 text-sm text-[#615d59]">Tidak ada file.</p></div></section>
            <section class="rounded-xl border border-[#e6e6e6] bg-white p-6 shadow-sm"><h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Pengumpulan Jawaban</h2><div v-if="props.submission" class="mt-3 text-sm text-[#615d59]">Jawaban tersimpan: <span v-for="file in props.submission.file_jawaban" :key="file" class="mr-2 text-[#0075de]">{{ name(file) }}</span><span v-if="props.submission.nilai"> · Nilai {{ props.submission.nilai }}</span></div><form class="mt-4 space-y-3" @submit.prevent="submit"><input type="file" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.jpg,.jpeg,.png" @change="form.file_jawaban = Array.from(($event.target as HTMLInputElement).files ?? [])" /><p v-if="form.errors.file_jawaban" class="text-sm text-red-600">{{ form.errors.file_jawaban }}</p><button type="submit" :disabled="form.processing" class="rounded-lg bg-[#0075de] px-4 py-2 text-sm font-medium text-white disabled:opacity-50">{{ props.submission ? 'Ganti Jawaban' : 'Kirim Jawaban' }}</button></form></section>
        </div></div>
    </AppLayout>
</template>
