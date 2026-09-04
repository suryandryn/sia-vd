<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
    title: string;
    type: string;
    user: Record<string, any>;
}>();

const v = (val: unknown): string => {
    if (val === null || val === undefined || val === '') return '-';
    if (typeof val === 'string' && val.includes('T')) return val.slice(0, 10);
    return String(val);
};

const isMahasiswa = props.type === 'mahasiswa';

const akun = [
    { label: 'Nama', key: 'name' },
    { label: 'Username', key: 'username' },
    { label: 'Email', key: 'email' },
];

const pribadi = [
    { label: 'Tempat Lahir', key: 'tempat_lahir' },
    { label: 'Tanggal Lahir', key: 'tanggal_lahir' },
    { label: 'Jenis Kelamin', key: 'jenis_kelamin' },
    { label: 'Agama', key: 'agama' },
    { label: 'No Telepon', key: 'no_telepon' },
    { label: 'Kewarganegaraan', key: 'kewarganegaraan' },
    { label: 'Alamat', key: 'alamat' },
];

const akademik = [
    { label: 'NIM', key: 'nim' },
    { label: 'Angkatan', key: 'angkatan' },
    { label: 'Semester', key: 'semester' },
    { label: 'Status', key: 'status' },
    { label: 'Program Studi', key: 'prodi_name' },
    { label: 'Jenjang', key: 'prodi_jenjang' },
    { label: 'Fakultas', key: 'fakultas_name' },
    { label: 'Dosen Wali', key: 'dosen_wali_name' },
    { label: 'Sekolah Asal', key: 'sekolah_asal' },
    { label: 'NISN', key: 'nisn' },
    { label: 'Email Alternatif', key: 'email_alternatif' },
];

const ayah = [
    { label: 'Nama Ayah Kandung', key: 'nama_ayah_kandung' },
    { label: 'Tanggal Lahir Ayah', key: 'tanggal_lahir_ayah' },
    { label: 'Pendidikan Terakhir Ayah', key: 'pendidikan_terakhir_ayah' },
    { label: 'Pekerjaan Ayah', key: 'pekerjaan_ayah' },
    { label: 'Penghasilan Ayah', key: 'penghasilan_ayah' },
    { label: 'No Telepon Ayah', key: 'no_telepon_ayah' },
    { label: 'Email Ayah', key: 'email_ayah' },
    { label: 'Alamat Ayah', key: 'alamat_ayah' },
];

const ibu = [
    { label: 'Nama Ibu Kandung', key: 'nama_ibu_kandung' },
    { label: 'Tanggal Lahir Ibu', key: 'tanggal_lahir_ibu' },
    { label: 'Pendidikan Terakhir Ibu', key: 'pendidikan_terakhir_ibu' },
    { label: 'Pekerjaan Ibu', key: 'pekerjaan_ibu' },
    { label: 'Penghasilan Ibu', key: 'penghasilan_ibu' },
    { label: 'No Telepon Ibu', key: 'no_telepon_ibu' },
    { label: 'Email Ibu', key: 'email_ibu' },
    { label: 'Alamat Ibu', key: 'alamat_ibu' },
];
</script>

<template>
    <Head :title="props.title" />
    <AppLayout :breadcrumbs="[{ title: 'Detail Mahasiswa', href: '#' }]">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h1 class="text-xl font-semibold">{{ props.title }}</h1>
                <div class="flex gap-2">
                    <Link :href="route(`admin.users.${props.type}`)"><Button variant="outline">Kembali</Button></Link>
                    <Link :href="route(`admin.users.${props.type}.edit`, props.user.id)"><Button>Edit</Button></Link>
                </div>
            </div>

            <Card>
                <CardHeader><CardTitle>Akun</CardTitle></CardHeader>
                <CardContent>
                    <dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="f in akun" :key="f.key" class="space-y-1">
                            <dt class="text-sm text-muted-foreground">{{ f.label }}</dt>
                            <dd class="text-sm font-medium break-all">{{ v(props.user[f.key]) }}</dd>
                        </div>
                    </dl>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Data Pribadi</CardTitle></CardHeader>
                <CardContent>
                    <dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="f in pribadi" :key="f.key" class="space-y-1">
                            <dt class="text-sm text-muted-foreground">{{ f.label }}</dt>
                            <dd class="text-sm font-medium break-words">{{ v(props.user[f.key]) }}</dd>
                        </div>
                    </dl>
                </CardContent>
            </Card>

            <template v-if="isMahasiswa">
                <Card>
                    <CardHeader><CardTitle>Data Akademik</CardTitle></CardHeader>
                    <CardContent>
                        <dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <div v-for="f in akademik" :key="f.key" class="space-y-1">
                                <dt class="text-sm text-muted-foreground">{{ f.label }}</dt>
                                <dd class="text-sm font-medium break-words">{{ v(props.user[f.key]) }}</dd>
                            </div>
                        </dl>
                    </CardContent>
                </Card>

                <div class="grid gap-4 lg:grid-cols-2">
                    <Card>
                        <CardHeader><CardTitle>Data Ayah</CardTitle></CardHeader>
                        <CardContent>
                            <dl class="grid gap-3">
                                <div v-for="f in ayah" :key="f.key" class="space-y-1">
                                    <dt class="text-sm text-muted-foreground">{{ f.label }}</dt>
                                    <dd class="text-sm font-medium break-words">{{ v(props.user[f.key]) }}</dd>
                                </div>
                            </dl>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader><CardTitle>Data Ibu</CardTitle></CardHeader>
                        <CardContent>
                            <dl class="grid gap-3">
                                <div v-for="f in ibu" :key="f.key" class="space-y-1">
                                    <dt class="text-sm text-muted-foreground">{{ f.label }}</dt>
                                    <dd class="text-sm font-medium break-words">{{ v(props.user[f.key]) }}</dd>
                                </div>
                            </dl>
                        </CardContent>
                    </Card>
                </div>
            </template>

            <template v-else>
                <Card>
                    <CardHeader><CardTitle>Profil</CardTitle></CardHeader>
                    <CardContent>
                        <pre class="whitespace-pre-wrap break-words text-sm">{{ JSON.stringify(props.user, null, 2) }}</pre>
                    </CardContent>
                </Card>
            </template>
        </div>
    </AppLayout>
</template>
