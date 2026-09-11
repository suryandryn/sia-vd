<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

type Item = { id: number; judul_materi?: string; judul_tugas?: string; nama_quiz?: string; pertemuan_ke?: number; tenggat_waktu?: string | null; catatan?: string | null };
type Kelas = {
    id: number;
    kode_kelas: string;
    kapasitas: number;
    tahunAkademik?: { tahun: string; semester: string } | null;
    mataKuliah?: { kode_matkul: string; nama_matkul: string; sks: number } | null;
    dosen?: { user?: { name: string } | null } | null;
    jadwals?: { hari: string; jam_mulai: string; jam_akhir: string; ruang?: { kode_ruang: string } | null }[];
    materis?: Item[];
    tugas?: Item[];
    quizzes?: Item[];
};
const props = defineProps<{ kelasKuliah: Kelas }>();
const v = (value: unknown) => value === null || value === undefined || value === '' ? '-' : String(value);
const jam = (value: string) => value.slice(0, 5);
</script>

<template>
    <Head :title="`Detail ${props.kelasKuliah.kode_kelas}`" />
    <AppLayout :breadcrumbs="[{ title: 'Jadwal Kuliah', href: route('mahasiswa.jadwal-kuliah') }, { title: 'Detail Kelas', href: '#' }]">
        <div class="min-h-full bg-[#f6f5f4]"><div class="mx-auto flex w-full max-w-[1000px] flex-col gap-4 px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-start justify-between gap-3"><div><h1 class="text-[26px] font-bold text-black">Detail Kelas Kuliah</h1><p class="text-sm text-[#615d59]">Informasi kelas dan materi perkuliahan.</p></div><Link :href="route('mahasiswa.jadwal-kuliah')" class="rounded-lg border border-[#e6e6e6] bg-white px-4 py-2 text-sm font-medium text-black">Kembali</Link></div>
            <section class="rounded-xl border border-[#e6e6e6] bg-white p-6 shadow-sm"><h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Informasi Kelas</h2><dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"><div><dt class="text-xs text-[#a39e98]">Kode Kelas</dt><dd class="font-medium">{{ v(props.kelasKuliah.kode_kelas) }}</dd></div><div><dt class="text-xs text-[#a39e98]">Mata Kuliah</dt><dd class="font-medium">{{ v(props.kelasKuliah.mataKuliah?.nama_matkul) }}</dd></div><div><dt class="text-xs text-[#a39e98]">Dosen</dt><dd class="font-medium">{{ v(props.kelasKuliah.dosen?.user?.name) }}</dd></div><div><dt class="text-xs text-[#a39e98]">Tahun Ajaran</dt><dd class="font-medium">{{ props.kelasKuliah.tahunAkademik ? `${props.kelasKuliah.tahunAkademik.tahun} ${props.kelasKuliah.tahunAkademik.semester}` : '-' }}</dd></div></dl></section>
            <section v-for="section in [{ title: 'Jadwal', items: props.kelasKuliah.jadwals ?? [] }, { title: 'Materi', items: props.kelasKuliah.materis ?? [] }, { title: 'Tugas', items: props.kelasKuliah.tugas ?? [] }, { title: 'Quiz', items: props.kelasKuliah.quizzes ?? [] }]" :key="section.title" class="rounded-xl border border-[#e6e6e6] bg-white p-6 shadow-sm"><h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">{{ section.title }}</h2><div v-if="section.items.length" class="mt-4 divide-y divide-[#e6e6e6]"> <div v-for="item in section.items" :key="item.id" class="py-3 text-[15px] text-[#31302e]"><template v-if="section.title === 'Jadwal'">{{ item.hari }} · {{ jam(item.jam_mulai) }}–{{ jam(item.jam_akhir) }} · Ruang {{ item.ruang?.kode_ruang ?? '-' }}</template><template v-else><Link v-if="section.title === 'Materi'" :href="route('mahasiswa.materi.show', item.id)" class="font-medium text-[#0075de] hover:underline">{{ item.judul_materi }}</Link><Link v-else-if="section.title === 'Tugas'" :href="route('mahasiswa.tugas.show', item.id)" class="font-medium text-[#0075de] hover:underline">{{ item.judul_tugas }}</Link><template v-else>{{ item.nama_quiz }}</template><span v-if="item.pertemuan_ke"> · Pertemuan {{ item.pertemuan_ke }}</span><span v-if="item.tenggat_waktu"> · Tenggat {{ item.tenggat_waktu }}</span><span v-if="item.catatan" class="block text-sm text-[#615d59]">{{ item.catatan }}</span></template></div></div><p v-else class="mt-4 text-sm text-[#615d59]">Belum ada {{ section.title.toLowerCase() }}.</p></section>
        </div></div>
    </AppLayout>
</template>
