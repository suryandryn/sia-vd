<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Download } from 'lucide-vue-next';

type JadwalShow = {
    id: number;
    hari: string;
    jam_mulai: string;
    jam_akhir: string;
    ruang?: { kode_ruang: string; nama_ruang: string; kapasitas: number } | null;
};

type MateriShow = {
    id: number;
    judul_materi: string;
    pertemuan_ke: number;
    file?: string[] | string | null;
    catatan?: string | null;
    uploader?: { name: string } | null;
};

type TugasShow = {
    id: number;
    judul_tugas: string;
    tenggat_waktu?: string | null;
    file?: string[] | string | null;
    catatan?: string | null;
    uploader?: { name: string } | null;
};

type QuizShow = {
    id: number;
    nama_quiz: string;
    catatan?: string | null;
    waktu_pengerjaan?: number | null;
    tenggat_waktu?: string | null;
    uploader?: { name: string } | null;
};

type KelasKuliahShowProps = {
    id: number;
    kode_kelas: string;
    tahun_ajaran: string;
    kapasitas: number;
    mata_kuliah?: { id: number; kode_matkul: string; nama_matkul: string; sks: number; semester: number; jenis: string; prodi?: { nama_prodi: string; jenjang: string; fakultas?: { nama_fakultas: string } | null } | null } | null;
    mataKuliah?: { id: number; kode_matkul: string; nama_matkul: string; sks: number; semester: number; jenis: string; prodi?: { nama_prodi: string; jenjang: string; fakultas?: { nama_fakultas: string } | null } | null } | null;
    jadwals?: JadwalShow[];
    materis?: MateriShow[];
    tugas?: TugasShow[];
    quizzes?: QuizShow[];
};

const props = defineProps<{ kelasKuliah: KelasKuliahShowProps }>();

const v = (val: unknown): string => {
    if (val === null || val === undefined || val === '') return '-';
    return String(val);
};

const matkul = () => (props.kelasKuliah as any).mataKuliah ?? (props.kelasKuliah as any).mata_kuliah ?? null;
const jam = (time: string) => (time ?? '').slice(0, 5);

const fileName = (path: string | null | undefined) => (path ?? '').split('/').pop() ?? '-';

const filesOf = (file: string[] | string | null | undefined): string[] => {
    if (Array.isArray(file)) return file.filter((value): value is string => typeof value === 'string' && value !== '');
    if (typeof file === 'string' && file !== '') {
        try {
            const parsed: unknown = JSON.parse(file);
            if (Array.isArray(parsed)) return parsed.filter((value): value is string => typeof value === 'string' && value !== '');
        } catch {
            return [file];
        }
        return [file];
    }
    return [];
};

const formatTenggat = (value: string | null | undefined): string => {
    if (!value) return '-';

    const [date, time] = value.replace('T', ' ').split(' ');

    if (!date) return String(value);

    const [year, month, day] = date.split('-');
    const [hour = '00', minute = '00'] = (time ?? '').split(':');

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    return `${day} ${months[Number(month) - 1]} ${year}, ${hour}:${minute}`;
};
</script>

<template>
    <Head :title="`Detail ${props.kelasKuliah.kode_kelas}`" />
    <AppLayout :breadcrumbs="[{ title: 'Kelas Kuliah', href: route('dosen.kelas-kuliah.index') }, { title: props.kelasKuliah.kode_kelas, href: '#' }]">
        <div class="min-h-full bg-[#f6f5f4]">
            <div class="mx-auto flex w-full max-w-[1000px] flex-col gap-4 px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="space-y-1">
                        <h1 class="text-[26px] font-bold leading-[1.23] tracking-[-0.625px] text-black">Detail Kelas Kuliah</h1>
                        <p class="max-w-xl text-sm leading-5 text-[#615d59]">Ringkasan kode kelas, tahun ajaran, mata kuliah, jadwal, materi, tugas, dan quiz.</p>
                    </div>
                    <Link :href="route('dosen.kelas-kuliah.index')"><Button variant="outline" class="rounded-lg border-[#e6e6e6] bg-white text-black hover:bg-white">Kembali</Button></Link>
                </div>

                <section class="rounded-xl border border-[#e6e6e6] bg-white p-6 shadow-[0_0.175px_1.041px_rgba(0,0,0,0.01),0_0.8px_2.925px_rgba(0,0,0,0.02)]">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Informasi Kelas</h2>
                    <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">Kode Kelas</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(props.kelasKuliah.kode_kelas) }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">Tahun Ajaran</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(props.kelasKuliah.tahun_ajaran) }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">Kapasitas</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(props.kelasKuliah.kapasitas) }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-xl border border-[#e6e6e6] bg-white p-6 shadow-[0_0.175px_1.041px_rgba(0,0,0,0.01),0_0.8px_2.925px_rgba(0,0,0,0.02)]">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Mata Kuliah</h2>
                    <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">Kode Mata Kuliah</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(matkul()?.kode_matkul) }}</dd>
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">Nama Mata Kuliah</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(matkul()?.nama_matkul) }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">SKS</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(matkul()?.sks) }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">Semester</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(matkul()?.semester) }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">Jenis</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(matkul()?.jenis) }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">Program Studi</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(matkul()?.prodi?.nama_prodi) }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">Fakultas</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(matkul()?.prodi?.fakultas?.nama_fakultas) }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-xl border border-[#e6e6e6] bg-white p-6 shadow-[0_0.175px_1.041px_rgba(0,0,0,0.01),0_0.8px_2.925px_rgba(0,0,0,0.02)]">
                    <div class="space-y-1">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Jadwal</h2>
                        <p class="text-sm leading-5 text-[#615d59]">Hari, jam, dan ruang untuk kelas ini.</p>
                    </div>
                    <div class="mt-4 overflow-hidden rounded-xl border border-[#e6e6e6]">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-[#e6e6e6] bg-[#f6f5f4]">
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Hari</th>
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Jam</th>
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Ruang</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e6e6e6]">
                                    <tr v-for="jadwal in props.kelasKuliah.jadwals ?? []" :key="jadwal.id" class="transition-colors hover:bg-[#f6f5f4]/60">
                                        <td class="px-4 py-3 text-[15px] font-medium leading-5 text-black">{{ v(jadwal.hari) }}</td>
                                        <td class="px-4 py-3 text-[15px] leading-5 text-[#31302e]">{{ jam(jadwal.jam_mulai) }}–{{ jam(jadwal.jam_akhir) }}</td>
                                        <td class="px-4 py-3 text-[15px] leading-5 text-[#31302e]">
                                            <span class="block">{{ v(jadwal.ruang?.kode_ruang) }} — {{ v(jadwal.ruang?.nama_ruang) }}</span>
                                        </td>
                                    </tr>
                                    <tr v-if="!(props.kelasKuliah.jadwals ?? []).length">
                                        <td colspan="3" class="px-4 py-10 text-center">
                                            <div class="mx-auto max-w-sm rounded-xl border border-dashed border-[#e6e6e6] bg-[#f6f5f4] px-6 py-6">
                                                <p class="text-sm font-medium text-black">Belum ada jadwal</p>
                                                <p class="mt-1 text-sm leading-5 text-[#615d59]">Jadwal kelas ini belum ditentukan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-[#e6e6e6] bg-white p-6 shadow-[0_0.175px_1.041px_rgba(0,0,0,0.01),0_0.8px_2.925px_rgba(0,0,0,0.02)]">
                    <div class="space-y-1">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Materi</h2>
                        <p class="text-sm leading-5 text-[#615d59]">Bahan ajar per pertemuan untuk kelas ini.</p>
                    </div>
                    <div class="mt-4 overflow-hidden rounded-xl border border-[#e6e6e6]">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-[#e6e6e6] bg-[#f6f5f4]">
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Pertemuan</th>
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Judul Materi</th>
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Berkas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e6e6e6]">
                                    <tr v-for="materi in props.kelasKuliah.materis ?? []" :key="materi.id" class="transition-colors hover:bg-[#f6f5f4]/60">
                                        <td class="px-4 py-3 text-[15px] font-medium leading-5 text-black">Pertemuan {{ v(materi.pertemuan_ke) }}</td>
                                        <td class="px-4 py-3 text-[15px] leading-5 text-[#31302e]">
                                            <span class="block font-medium text-black">{{ v(materi.judul_materi) }}</span>
                                            <span v-if="materi.catatan" class="mt-0.5 block max-w-md truncate text-sm text-[#615d59]" :title="String(materi.catatan)">{{ materi.catatan }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-[15px] leading-5 text-[#31302e]">
                                            <ul v-if="filesOf(materi.file).length" class="space-y-1">
                                                <li v-for="path in filesOf(materi.file)" :key="path">
                                                    <a :href="`/storage/${path}`" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-[#0075de] hover:underline">
                                                        <Download class="size-4" />{{ fileName(path) }}
                                                    </a>
                                                </li>
                                            </ul>
                                            <span v-else>-</span>
                                        </td>
                                    </tr>
                                    <tr v-if="!(props.kelasKuliah.materis ?? []).length">
                                        <td colspan="3" class="px-4 py-10 text-center">
                                            <div class="mx-auto max-w-sm rounded-xl border border-dashed border-[#e6e6e6] bg-[#f6f5f4] px-6 py-6">
                                                <p class="text-sm font-medium text-black">Belum ada materi</p>
                                                <p class="mt-1 text-sm leading-5 text-[#615d59]">Materi kelas ini belum tersedia.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-[#e6e6e6] bg-white p-6 shadow-[0_0.175px_1.041px_rgba(0,0,0,0.01),0_0.8px_2.925px_rgba(0,0,0,0.02)]">
                    <div class="space-y-1">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Tugas</h2>
                        <p class="text-sm leading-5 text-[#615d59]">Daftar tugas beserta tenggat waktu untuk kelas ini.</p>
                    </div>
                    <div class="mt-4 overflow-hidden rounded-xl border border-[#e6e6e6]">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-[#e6e6e6] bg-[#f6f5f4]">
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Judul Tugas</th>
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Tenggat Waktu</th>
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Berkas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e6e6e6]">
                                    <tr v-for="tugas in props.kelasKuliah.tugas ?? []" :key="tugas.id" class="transition-colors hover:bg-[#f6f5f4]/60">
                                        <td class="px-4 py-3 text-[15px] leading-5 text-[#31302e]">
                                            <span class="block font-medium text-black">{{ v(tugas.judul_tugas) }}</span>
                                            <span v-if="tugas.catatan" class="mt-0.5 block max-w-md truncate text-sm text-[#615d59]" :title="String(tugas.catatan)">{{ tugas.catatan }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-[15px] leading-5 text-[#31302e]">{{ formatTenggat(tugas.tenggat_waktu) }}</td>
                                        <td class="px-4 py-3 text-[15px] leading-5 text-[#31302e]">
                                            <ul v-if="filesOf(tugas.file).length" class="space-y-1">
                                                <li v-for="path in filesOf(tugas.file)" :key="path">
                                                    <a :href="`/storage/${path}`" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-[#0075de] hover:underline">
                                                        <Download class="size-4" />{{ fileName(path) }}
                                                    </a>
                                                </li>
                                            </ul>
                                            <span v-else>-</span>
                                        </td>
                                    </tr>
                                    <tr v-if="!(props.kelasKuliah.tugas ?? []).length">
                                        <td colspan="3" class="px-4 py-10 text-center">
                                            <div class="mx-auto max-w-sm rounded-xl border border-dashed border-[#e6e6e6] bg-[#f6f5f4] px-6 py-6">
                                                <p class="text-sm font-medium text-black">Belum ada tugas</p>
                                                <p class="mt-1 text-sm leading-5 text-[#615d59]">Tugas kelas ini belum tersedia.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-[#e6e6e6] bg-white p-6 shadow-[0_0.175px_1.041px_rgba(0,0,0,0.01),0_0.8px_2.925px_rgba(0,0,0,0.02)]">
                    <div class="space-y-1">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Quiz</h2>
                        <p class="text-sm leading-5 text-[#615d59]">Daftar quiz beserta durasi dan tenggat waktu untuk kelas ini.</p>
                    </div>
                    <div class="mt-4 overflow-hidden rounded-xl border border-[#e6e6e6]">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-[#e6e6e6] bg-[#f6f5f4]">
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Nama Quiz</th>
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Durasi</th>
                                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Tenggat Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e6e6e6]">
                                    <tr v-for="quiz in props.kelasKuliah.quizzes ?? []" :key="quiz.id" class="transition-colors hover:bg-[#f6f5f4]/60">
                                        <td class="px-4 py-3 text-[15px] leading-5 text-[#31302e]">
                                            <span class="block font-medium text-black">{{ v(quiz.nama_quiz) }}</span>
                                            <span v-if="quiz.catatan" class="mt-0.5 block max-w-md truncate text-sm text-[#615d59]" :title="String(quiz.catatan)">{{ quiz.catatan }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-[15px] leading-5 text-[#31302e]">{{ quiz.waktu_pengerjaan ? `${quiz.waktu_pengerjaan} menit` : '-' }}</td>
                                        <td class="px-4 py-3 text-[15px] leading-5 text-[#31302e]">{{ formatTenggat(quiz.tenggat_waktu) }}</td>
                                    </tr>
                                    <tr v-if="!(props.kelasKuliah.quizzes ?? []).length">
                                        <td colspan="3" class="px-4 py-10 text-center">
                                            <div class="mx-auto max-w-sm rounded-xl border border-dashed border-[#e6e6e6] bg-[#f6f5f4] px-6 py-6">
                                                <p class="text-sm font-medium text-black">Belum ada quiz</p>
                                                <p class="mt-1 text-sm leading-5 text-[#615d59]">Quiz kelas ini belum tersedia.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
