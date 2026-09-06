<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type KelasKuliahShowProps = {
    id: number;
    kode_kelas: string;
    tahun_ajaran: string;
    kapasitas: number;
    dosen?: { id: number; nidn: string; jabatan_fungsional?: string; user?: { name: string } | null } | null;
    mata_kuliah?: { id: number; kode_matkul: string; nama_matkul: string; sks: number; semester: number; jenis: string; prodi?: { nama_prodi: string; jenjang: string; fakultas?: { nama_fakultas: string } | null } | null } | null;
    mataKuliah?: { id: number; kode_matkul: string; nama_matkul: string; sks: number; semester: number; jenis: string; prodi?: { nama_prodi: string; jenjang: string; fakultas?: { nama_fakultas: string } | null } | null } | null;
};

const props = defineProps<{ kelasKuliah: KelasKuliahShowProps }>();

const v = (val: unknown): string => {
    if (val === null || val === undefined || val === '') return '-';
    return String(val);
};

const dosen = () => (props.kelasKuliah as any).dosen ?? null;
const matkul = () => (props.kelasKuliah as any).mataKuliah ?? (props.kelasKuliah as any).mata_kuliah ?? null;
</script>

<template>
    <Head :title="`Detail ${props.kelasKuliah.kode_kelas}`" />
    <AppLayout :breadcrumbs="[{ title: 'Detail Kelas Kuliah', href: '#' }]">
        <div class="min-h-full bg-[#f6f5f4]">
            <div class="mx-auto flex w-full max-w-[1000px] flex-col gap-4 px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="space-y-1">
                        <h1 class="text-[26px] font-bold leading-[1.23] tracking-[-0.625px] text-black">Detail Kelas Kuliah</h1>
                        <p class="max-w-xl text-sm leading-5 text-[#615d59]">Ringkasan kode kelas, tahun ajaran, dosen pengampu, dan mata kuliah.</p>
                    </div>
                    <div class="flex gap-2">
                        <Link :href="route('admin.kelas-kuliah.index')"><Button variant="outline" class="rounded-lg border-[#e6e6e6] bg-white text-black hover:bg-white">Kembali</Button></Link>
                        <Link :href="route('admin.kelas-kuliah.edit', props.kelasKuliah.id)"><Button class="rounded-full bg-[#0075de] text-white hover:bg-[#005bab]">Edit</Button></Link>
                    </div>
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
                    <h2 class="text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Dosen Pengampu</h2>
                    <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">Nama Dosen</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(dosen()?.user?.name) }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">NIDN</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(dosen()?.nidn) }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium uppercase tracking-[0.04em] text-[#a39e98]">Jabatan Fungsional</dt>
                            <dd class="break-words text-[15px] font-medium leading-5 text-black">{{ v(dosen()?.jabatan_fungsional) }}</dd>
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
            </div>
        </div>
    </AppLayout>
</template>
