<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import { Button } from '@/components/ui/button';

type KelasKuliah = {
    id: number;
    kode_kelas: string;
    kapasitas: number;
    mata_kuliah?: {
        id: number;
        kode_matkul: string;
        nama_matkul: string;
        sks: number;
        semester: number;
        prodi?: { nama_prodi: string } | null;
    } | null;
    mataKuliah?: {
        id: number;
        kode_matkul: string;
        nama_matkul: string;
        sks: number;
        semester: number;
        prodi?: { nama_prodi: string } | null;
    } | null;
    tahun_akademik?: { tahun: string; semester: string } | null;
    dosen?: { user?: { name: string } | null } | null;
    jadwals?: { hari: string; jam_mulai: string; jam_akhir: string; ruang?: { kode_ruang: string } | null }[];
};

const props = defineProps<{
    kelasKuliahs: KelasKuliah[];
    mahasiswa: { semester: number; angkatan: string; prodi_id: number };
    kelasDiambil: number[];
}>();

const page = usePage<{ flash?: { krs_success?: string; krs_error?: string } }>();
const isTaken = (kelasId: number) => props.kelasDiambil.includes(kelasId);
const ambilKelas = (kelasId: number) => router.post(route('mahasiswa.krs.store', kelasId), {}, { preserveScroll: true });

onMounted(() => {
    if (page.props.flash?.krs_error) window.alert(page.props.flash.krs_error);
    if (page.props.flash?.krs_success) window.alert(page.props.flash.krs_success);
});

const matkul = (kelas: KelasKuliah) => kelas.mataKuliah ?? kelas.mata_kuliah;

const groupedKelasKuliahs = computed(() => {
    const groups = new Map<string, { matkul: NonNullable<KelasKuliah['mataKuliah']>; kelas: KelasKuliah[] }>();

    for (const kelas of [...props.kelasKuliahs].sort((first, second) => (first.jadwals?.[0]?.jam_mulai ?? '99:99').localeCompare(second.jadwals?.[0]?.jam_mulai ?? '99:99'))) {
        const mataKuliah = matkul(kelas);
        if (!mataKuliah) continue;
        const key = String(mataKuliah.id ?? mataKuliah.kode_matkul);
        const group = groups.get(key);
        if (group) group.kelas.push(kelas);
        else groups.set(key, { matkul: mataKuliah, kelas: [kelas] });
    }

    return [...groups.values()];
});

const jam = (value: string) => value.slice(0, 5);
const jadwal = (kelas: KelasKuliah) => kelas.jadwals?.map((item) => `${item.hari}, ${jam(item.jam_mulai)}-${jam(item.jam_akhir)}${item.ruang?.kode_ruang ? ` (${item.ruang.kode_ruang})` : ''}`).join(' | ') || '-';
</script>

<template>
    <Head title="Rencana Studi (KRS)" />
    <AppLayout :breadcrumbs="[{ title: 'Rencana Studi (KRS)', href: route('mahasiswa.krs') }]">
        <div class="min-h-full bg-[#f6f5f4]">
            <div class="mx-auto flex w-full max-w-[1100px] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
                <div class="space-y-1">
                    <h1 class="text-[26px] font-bold leading-[1.23] tracking-[-0.625px] text-black">Rencana Studi (KRS)</h1>
                    <p class="text-sm leading-5 text-[#615d59]">Kelas kuliah yang tersedia sesuai semester, program studi, dan tahun akademik aktif.</p>
                </div>

                <div class="rounded-xl border border-[#e6e6e6] bg-white p-5 shadow-sm">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div><p class="text-xs uppercase tracking-[0.08em] text-[#a39e98]">Semester</p><p class="mt-1 font-medium text-black">{{ mahasiswa.semester }}</p></div>
                        <div><p class="text-xs uppercase tracking-[0.08em] text-[#a39e98]">Angkatan</p><p class="mt-1 font-medium text-black">{{ mahasiswa.angkatan }}</p></div>
                        <div><p class="text-xs uppercase tracking-[0.08em] text-[#a39e98]">Jumlah Kelas</p><p class="mt-1 font-medium text-black">{{ kelasKuliahs.length }}</p></div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-[#e6e6e6] bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="border-b border-[#e6e6e6] bg-[#f6f5f4]">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Kode</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Mata Kuliah</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Kelas</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Dosen</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Jadwal</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Tahun Akademik</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.08em] text-[#a39e98]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e6e6e6]">
                                <template v-for="group in groupedKelasKuliahs" :key="group.matkul.kode_matkul">
                                    <tr class="bg-[#f6f5f4]">
                                        <td colspan="7" class="px-4 py-3 text-sm font-semibold text-black">{{ group.matkul.kode_matkul }} — {{ group.matkul.nama_matkul }} <span class="font-normal text-[#615d59]">({{ group.matkul.sks }} SKS)</span></td>
                                    </tr>
                                    <tr v-for="kelas in group.kelas" :key="kelas.id" class="hover:bg-[#f6f5f4]/60">
                                        <td class="px-4 py-3 text-sm font-medium text-black">{{ group.matkul.kode_matkul }}</td>
                                        <td class="px-4 py-3 text-sm text-[#31302e]">{{ group.matkul.nama_matkul }}</td>
                                        <td class="px-4 py-3 text-sm text-[#31302e]">{{ kelas.kode_kelas }}</td>
                                        <td class="px-4 py-3 text-sm text-[#31302e]">{{ kelas.dosen?.user?.name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-[#31302e]">{{ jadwal(kelas) }}</td>
                                        <td class="px-4 py-3 text-sm text-[#31302e]">{{ kelas.tahun_akademik?.tahun ?? '-' }} ({{ kelas.tahun_akademik?.semester ?? '-' }})</td>
                                        <td class="px-4 py-3 text-right">
                                            <Button size="sm" :disabled="isTaken(kelas.id)" @click="ambilKelas(kelas.id)">
                                                {{ isTaken(kelas.id) ? 'Sudah Diambil' : 'Ambil' }}
                                            </Button>
                                        </td>
                                    </tr>
                                </template>
                                <tr v-if="!kelasKuliahs.length"><td colspan="7" class="px-4 py-14 text-center text-sm text-[#615d59]">Belum ada kelas kuliah yang sesuai dengan data akademik Anda.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
