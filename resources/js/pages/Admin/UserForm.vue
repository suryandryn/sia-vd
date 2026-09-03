<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage<{ flash: { success?: string; error?: string } }>();
import { computed, ref } from 'vue';
import DatePicker from '@/components/DatePicker.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{ title: string; type: string; user: Record<string, any> | null; dosenWali: { id: number; name: string }[]; programStudi: { id: number; nama_prodi: string; jenjang: string; fakultas: string | null }[] }>();
const common = ['name', 'username', 'email', 'tempat_lahir', 'tanggal_lahir', 'no_telepon', 'kewarganegaraan'];
const roleFields = props.type === 'karyawan' ? ['nomor_induk'] : props.type === 'dosen' ? ['nidn', 'jabatan_fungsional', 'pendidikan_terakhir', 'status_kepegawaian', 'prodi_id'] : props.type === 'mahasiswa' ? ['nim', 'angkatan', 'semester', 'status', 'dosen_wali_id', 'prodi_id', 'sekolah_asal', 'nisn', 'email_alternatif', 'nama_ayah_kandung', 'nama_ibu_kandung'] : [];
const labels: Record<string, string> = { name: 'Nama', username: 'Username', email: 'Email', tempat_lahir: 'Tempat Lahir', tanggal_lahir: 'Tanggal Lahir', no_telepon: 'Nomor Telepon', kewarganegaraan: 'Kewarganegaraan', nomor_induk: 'Nomor Induk', nidn: 'NIDN', jabatan_fungsional: 'Jabatan Fungsional', pendidikan_terakhir: 'Pendidikan Terakhir', status_kepegawaian: 'Status Kepegawaian', nim: 'NIM', angkatan: 'Angkatan', semester: 'Semester', status: 'Status', dosen_wali_id: 'Dosen Wali', prodi_id: 'Program Studi', sekolah_asal: 'Sekolah Asal', nisn: 'NISN', email_alternatif: 'Email Alternatif', nama_ayah_kandung: 'Nama Ayah Kandung', nama_ibu_kandung: 'Nama Ibu Kandung', alamat: 'Alamat', password: 'Kata Sandi', password_confirmation: 'Konfirmasi Kata Sandi' };
const agama = ['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
const statuses = ['Aktif', 'Nonaktif', 'Lulus', 'Dropout', 'Cuti', 'Mengundurkan Diri', 'Meninggal', 'Transfer Masuk'];
const groupedProgramStudi = computed(() => {
    const groups: Record<string, typeof props.programStudi> = {};
    for (const prodi of props.programStudi) {
        const key = prodi.fakultas ?? 'Fakultas Lainnya';
        (groups[key] ??= []).push(prodi);
    }

    return Object.entries(groups);
});
const title = computed(() => `${props.user ? 'Edit' : 'Tambah'} Pengguna - ${props.type.charAt(0).toUpperCase()}${props.type.slice(1)}`);
const form = useForm(Object.fromEntries([...common, ...roleFields, 'jenis_kelamin', 'agama', 'alamat', 'password', 'password_confirmation'].map((field) => [field, props.user?.[field] ?? ''])));
const search = ref('');
const dosenWaliOpen = ref(false);
const filteredDosen = computed(() => props.dosenWali.filter((dosen) => dosen.name.toLowerCase().includes(search.value.toLowerCase())));
const selectedDosen = computed(() => props.dosenWali.find((dosen) => dosen.id === Number(form.dosen_wali_id)));
const openDosenWali = () => {
    dosenWaliOpen.value = true;
    search.value = '';
};
const selectDosenWali = (id: number) => {
    form.dosen_wali_id = id;
    dosenWaliOpen.value = false;
    search.value = '';
};
const handleDosenWaliKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
        dosenWaliOpen.value = false;
    }
};
const submit = () => props.user ? form.put(route(`admin.users.${props.type}.update`, props.user.id)) : form.post(route(`admin.users.${props.type}.store`));
</script>
<template>
    <Head :title="title" /><AppLayout :breadcrumbs="[{ title, href: '#' }]">
        <div class="max-w-xl space-y-6 p-4"><div v-if="page.props.flash?.success" class="rounded border border-green-300 bg-green-50 p-3 text-green-800" role="alert">{{ page.props.flash.success }}</div><div v-if="page.props.flash?.error" class="rounded border border-red-300 bg-red-50 p-3 text-red-800" role="alert">{{ page.props.flash.error }}</div><div class="flex items-center justify-between"><h1 class="text-xl font-semibold">{{ title }}</h1><Link :href="route(`admin.users.${props.type}`)" class="underline">Kembali</Link></div>
            <form @submit.prevent="submit" class="space-y-4">
                <template v-for="field in common" :key="field">
                    <div v-if="field === 'tanggal_lahir'" class="grid gap-2"><Label :for="field">{{ labels[field] }}</Label><DatePicker :id="field" v-model="form[field]" placeholder="Pilih tanggal lahir" /><InputError :message="form.errors[field]" /></div>
                    <div v-else class="grid gap-2"><Label :for="field">{{ labels[field] }}</Label><Input :id="field" :type="field.includes('email') ? 'email' : 'text'" v-model="form[field]" required /><InputError :message="form.errors[field]" /></div>
                </template>
                <div class="grid gap-2"><Label>Jenis Kelamin</Label><label v-for="gender in ['Laki-laki', 'Perempuan']" :key="gender"><input v-model="form.jenis_kelamin" type="radio" :value="gender" required /> {{ gender }}</label><InputError :message="form.errors.jenis_kelamin" /></div>
                <div class="grid gap-2"><Label for="agama">Agama</Label><select id="agama" v-model="form.agama" class="rounded-md border px-3 py-2" required><option value="">Pilih agama</option><option v-for="item in agama" :key="item" :value="item">{{ item }}</option></select><InputError :message="form.errors.agama" /></div>
                <div v-for="field in roleFields" :key="field" class="grid gap-2"><Label :for="field">{{ labels[field] }}</Label><select v-if="field === 'status'" :id="field" v-model="form[field]" class="rounded-md border px-3 py-2" required><option value="">Pilih status</option><option v-for="item in statuses" :key="item" :value="item">{{ item }}</option></select><select v-else-if="field === 'prodi_id'" :id="field" v-model="form[field]" class="rounded-md border px-3 py-2" required><option value="">Pilih program studi</option><optgroup v-for="[fakultas, prodiList] in groupedProgramStudi" :key="fakultas" :label="fakultas"><option v-for="item in prodiList" :key="item.id" :value="item.id">{{ item.jenjang }} - {{ item.nama_prodi }}</option></optgroup></select><template v-else-if="field === 'dosen_wali_id'"><div class="relative"><button :id="field" type="button" class="flex w-full items-center justify-between rounded-md border px-3 py-2 text-left" role="combobox" :aria-expanded="dosenWaliOpen" aria-controls="dosen-wali-options" :aria-invalid="!!form.errors[field]" @click="openDosenWali" @keydown="handleDosenWaliKeydown"><span>{{ selectedDosen?.name ?? 'Pilih dosen wali' }}</span><span aria-hidden="true">⌄</span></button><div v-if="dosenWaliOpen" class="absolute z-10 mt-1 w-full rounded-md border bg-white p-2 shadow-md" role="listbox" id="dosen-wali-options"><Input v-model="search" placeholder="Cari dosen wali" aria-label="Cari dosen wali" autofocus /><div class="mt-1 max-h-48 overflow-y-auto"><button v-for="dosen in filteredDosen" :key="dosen.id" type="button" class="block w-full rounded px-2 py-1 text-left hover:bg-muted" role="option" :aria-selected="Number(form[field]) === dosen.id" @click="selectDosenWali(dosen.id)">{{ dosen.name }}</button><p v-if="filteredDosen.length === 0" class="px-2 py-1 text-sm text-muted-foreground">Dosen tidak ditemukan</p></div></div><input :id="`${field}-value`" v-model="form[field]" type="hidden" required /></div></template><Input v-else :id="field" v-model="form[field]" :type="['angkatan', 'semester'].includes(field) ? 'number' : 'text'" required /><InputError :message="form.errors[field]" /></div>
                <div class="grid gap-2"><Label for="alamat">{{ labels.alamat }}</Label><textarea id="alamat" v-model="form.alamat" class="min-h-24 rounded-md border px-3 py-2" required /><InputError :message="form.errors.alamat" /></div>
                <div v-for="field in ['password', 'password_confirmation']" :key="field" class="grid gap-2"><Label :for="field">{{ labels[field] }}</Label><Input :id="field" type="password" v-model="form[field]" :required="!props.user" autocomplete="new-password" /><InputError :message="form.errors[field]" /></div>
                <Button :disabled="form.processing">Simpan</Button>
            </form>
        </div>
    </AppLayout>
</template>