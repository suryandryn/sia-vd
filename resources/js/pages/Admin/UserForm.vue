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
const roleFields =
    props.type === 'karyawan'
        ? ['nomor_induk']
        : props.type === 'dosen'
          ? ['nidn', 'jabatan_fungsional', 'pendidikan_terakhir', 'status_kepegawaian', 'prodi_id']
          : props.type === 'mahasiswa'
            ? [
                  'nim',
                  'angkatan',
                  'semester',
                  'status',
                  'dosen_wali_id',
                  'prodi_id',
                  'sekolah_asal',
                  'nisn',
                  'email_alternatif',
                  'nama_ayah_kandung',
                  'tanggal_lahir_ayah',
                  'pendidikan_terakhir_ayah',
                  'pekerjaan_ayah',
                  'penghasilan_ayah',
                  'no_telepon_ayah',
                  'email_ayah',
                  'alamat_ayah',
                  'nama_ibu_kandung',
                  'tanggal_lahir_ibu',
                  'pendidikan_terakhir_ibu',
                  'pekerjaan_ibu',
                  'penghasilan_ibu',
                  'no_telepon_ibu',
                  'email_ibu',
                  'alamat_ibu',
              ]
            : [];
const labels: Record<string, string> = {
    name: 'Nama',
    username: 'Username',
    email: 'Email',
    tempat_lahir: 'Tempat Lahir',
    tanggal_lahir: 'Tanggal Lahir',
    no_telepon: 'Nomor Telepon',
    kewarganegaraan: 'Kewarganegaraan',
    nomor_induk: 'Nomor Induk',
    nidn: 'NIDN',
    jabatan_fungsional: 'Jabatan Fungsional',
    pendidikan_terakhir: 'Pendidikan Terakhir',
    status_kepegawaian: 'Status Kepegawaian',
    nim: 'NIM',
    angkatan: 'Angkatan',
    semester: 'Semester',
    status: 'Status',
    dosen_wali_id: 'Dosen Wali',
    prodi_id: 'Program Studi',
    sekolah_asal: 'Sekolah Asal',
    nisn: 'NISN',
    email_alternatif: 'Email Alternatif',
    nama_ayah_kandung: 'Nama Ayah Kandung',
    tanggal_lahir_ayah: 'Tanggal Lahir Ayah',
    pendidikan_terakhir_ayah: 'Pendidikan Terakhir Ayah',
    pekerjaan_ayah: 'Pekerjaan Ayah',
    penghasilan_ayah: 'Penghasilan Ayah',
    no_telepon_ayah: 'Nomor Telepon Ayah',
    email_ayah: 'Email Ayah',
    alamat_ayah: 'Alamat Ayah',
    nama_ibu_kandung: 'Nama Ibu Kandung',
    tanggal_lahir_ibu: 'Tanggal Lahir Ibu',
    pendidikan_terakhir_ibu: 'Pendidikan Terakhir Ibu',
    pekerjaan_ibu: 'Pekerjaan Ibu',
    penghasilan_ibu: 'Penghasilan Ibu',
    no_telepon_ibu: 'Nomor Telepon Ibu',
    email_ibu: 'Email Ibu',
    alamat_ibu: 'Alamat Ibu',
    alamat: 'Alamat',
    password: 'Kata Sandi',
    password_confirmation: 'Konfirmasi Kata Sandi',
};
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
const form = useForm(
    Object.fromEntries([...common, ...roleFields, 'jenis_kelamin', 'agama', 'alamat', 'password', 'password_confirmation'].map((field) => [field, props.user?.[field] ?? ''])),
);
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
const submit = () => (props.user ? form.put(route(`admin.users.${props.type}.update`, props.user.id)) : form.post(route(`admin.users.${props.type}.store`)));
</script>
<template>
    <Head :title="title" /><AppLayout :breadcrumbs="[{ title, href: '#' }]">
        <div :class="props.type === 'mahasiswa' ? 'max-w-3xl space-y-6 p-4' : 'max-w-xl space-y-6 p-4'">
            <div v-if="page.props.flash?.success" class="rounded border border-green-300 bg-green-50 p-3 text-green-800" role="alert">{{ page.props.flash.success }}</div>
            <div v-if="page.props.flash?.error" class="rounded border border-red-300 bg-red-50 p-3 text-red-800" role="alert">{{ page.props.flash.error }}</div>
            <div class="flex flex-wrap items-center justify-between gap-2"><h1 class="text-xl font-semibold">{{ title }}</h1><Link :href="route(`admin.users.${props.type}`)"><Button variant="outline">Kembali</Button></Link></div>
            <!-- Mahasiswa: sectioned form -->
            <form v-if="props.type === 'mahasiswa'" @submit.prevent="submit" class="space-y-6">
                <!-- Data Akun -->
                <section class="space-y-4 rounded-lg border p-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Data Akun</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2"><Label for="name">{{ labels.name }}</Label><Input id="name" v-model="form.name" required /><InputError :message="form.errors.name" /></div>
                        <div class="grid gap-2"><Label for="username">{{ labels.username }}</Label><Input id="username" v-model="form.username" required /><InputError :message="form.errors.username" /></div>
                    </div>
                    <div class="grid gap-2"><Label for="email">{{ labels.email }}</Label><Input id="email" type="email" v-model="form.email" required /><InputError :message="form.errors.email" /></div>
                </section>

                <!-- Data Pribadi -->
                <section class="space-y-4 rounded-lg border p-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Data Pribadi</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2"><Label for="tempat_lahir">{{ labels.tempat_lahir }}</Label><Input id="tempat_lahir" v-model="form.tempat_lahir" required /><InputError :message="form.errors.tempat_lahir" /></div>
                        <div class="grid gap-2"><Label for="tanggal_lahir">{{ labels.tanggal_lahir }}</Label><DatePicker id="tanggal_lahir" v-model="form.tanggal_lahir" placeholder="Pilih tanggal lahir" /><InputError :message="form.errors.tanggal_lahir" /></div>
                    </div>
                    <div class="grid gap-2"><Label>Jenis Kelamin</Label><div class="flex gap-4"><label v-for="gender in ['Laki-laki', 'Perempuan']" :key="gender" class="flex items-center gap-2"><input v-model="form.jenis_kelamin" type="radio" :value="gender" required /> {{ gender }}</label></div><InputError :message="form.errors.jenis_kelamin" /></div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2"><Label for="agama">Agama</Label><select id="agama" v-model="form.agama" class="rounded-md border px-3 py-2" required><option value="">Pilih agama</option><option v-for="item in agama" :key="item" :value="item">{{ item }}</option></select><InputError :message="form.errors.agama" /></div>
                        <div class="grid gap-2"><Label for="no_telepon">{{ labels.no_telepon }}</Label><Input id="no_telepon" v-model="form.no_telepon" required /><InputError :message="form.errors.no_telepon" /></div>
                    </div>
                    <div class="grid gap-2"><Label for="kewarganegaraan">{{ labels.kewarganegaraan }}</Label><Input id="kewarganegaraan" v-model="form.kewarganegaraan" required /><InputError :message="form.errors.kewarganegaraan" /></div>
                    <div class="grid gap-2"><Label for="alamat">{{ labels.alamat }}</Label><textarea id="alamat" v-model="form.alamat" class="min-h-24 rounded-md border px-3 py-2" required /><InputError :message="form.errors.alamat" /></div>
                </section>

                <!-- Data Akademik -->
                <section class="space-y-4 rounded-lg border p-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Data Akademik</h2>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="grid gap-2"><Label for="nim">{{ labels.nim }}</Label><Input id="nim" v-model="form.nim" required /><InputError :message="form.errors.nim" /></div>
                        <div class="grid gap-2"><Label for="angkatan">{{ labels.angkatan }}</Label><Input id="angkatan" v-model="form.angkatan" type="number" required /><InputError :message="form.errors.angkatan" /></div>
                        <div class="grid gap-2"><Label for="semester">{{ labels.semester }}</Label><Input id="semester" v-model="form.semester" type="number" required /><InputError :message="form.errors.semester" /></div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2"><Label for="status">{{ labels.status }}</Label><select id="status" v-model="form.status" class="rounded-md border px-3 py-2" required><option value="">Pilih status</option><option v-for="item in statuses" :key="item" :value="item">{{ item }}</option></select><InputError :message="form.errors.status" /></div>
                        <div class="grid gap-2"><Label for="prodi_id">{{ labels.prodi_id }}</Label><select id="prodi_id" v-model="form.prodi_id" class="rounded-md border px-3 py-2" required><option value="">Pilih program studi</option><optgroup v-for="[fakultas, prodiList] in groupedProgramStudi" :key="fakultas" :label="fakultas"><option v-for="item in prodiList" :key="item.id" :value="item.id">{{ item.jenjang }} - {{ item.nama_prodi }}</option></optgroup></select><InputError :message="form.errors.prodi_id" /></div>
                    </div>
                    <div class="grid gap-2">
                        <Label for="dosen_wali_id">{{ labels.dosen_wali_id }}</Label>
                        <div class="relative">
                            <button id="dosen_wali_id" type="button" class="flex w-full items-center justify-between rounded-md border px-3 py-2 text-left" role="combobox" :aria-expanded="dosenWaliOpen" aria-controls="dosen-wali-options" :aria-invalid="!!form.errors.dosen_wali_id" @click="openDosenWali" @keydown="handleDosenWaliKeydown"><span>{{ selectedDosen?.name ?? 'Pilih dosen wali' }}</span><span aria-hidden="true">⌄</span></button>
                            <div v-if="dosenWaliOpen" class="absolute z-10 mt-1 w-full rounded-md border bg-white p-2 shadow-md" role="listbox" id="dosen-wali-options"><Input v-model="search" placeholder="Cari dosen wali" aria-label="Cari dosen wali" autofocus /><div class="mt-1 max-h-48 overflow-y-auto"><button v-for="dosen in filteredDosen" :key="dosen.id" type="button" class="block w-full rounded px-2 py-1 text-left hover:bg-muted" role="option" :aria-selected="Number(form.dosen_wali_id) === dosen.id" @click="selectDosenWali(dosen.id)">{{ dosen.name }}</button><p v-if="filteredDosen.length === 0" class="px-2 py-1 text-sm text-muted-foreground">Dosen tidak ditemukan</p></div></div>
                            <input id="dosen_wali_id-value" v-model="form.dosen_wali_id" type="hidden" required />
                        </div>
                        <InputError :message="form.errors.dosen_wali_id" />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2"><Label for="sekolah_asal">{{ labels.sekolah_asal }}</Label><Input id="sekolah_asal" v-model="form.sekolah_asal" required /><InputError :message="form.errors.sekolah_asal" /></div>
                        <div class="grid gap-2"><Label for="nisn">{{ labels.nisn }}</Label><Input id="nisn" v-model="form.nisn" required /><InputError :message="form.errors.nisn" /></div>
                    </div>
                    <div class="grid gap-2"><Label for="email_alternatif">{{ labels.email_alternatif }}</Label><Input id="email_alternatif" type="email" v-model="form.email_alternatif" required /><InputError :message="form.errors.email_alternatif" /></div>
                </section>

                <!-- Data Ayah Kandung -->
                <section class="space-y-4 rounded-lg border p-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Data Ayah Kandung</h2>
                    <div class="grid gap-2"><Label for="nama_ayah_kandung">{{ labels.nama_ayah_kandung }}</Label><Input id="nama_ayah_kandung" v-model="form.nama_ayah_kandung" required /><InputError :message="form.errors.nama_ayah_kandung" /></div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2"><Label for="tanggal_lahir_ayah">{{ labels.tanggal_lahir_ayah }}</Label><DatePicker id="tanggal_lahir_ayah" v-model="form.tanggal_lahir_ayah" placeholder="Pilih tanggal lahir ayah" /><InputError :message="form.errors.tanggal_lahir_ayah" /></div>
                        <div class="grid gap-2"><Label for="pendidikan_terakhir_ayah">{{ labels.pendidikan_terakhir_ayah }}</Label><Input id="pendidikan_terakhir_ayah" v-model="form.pendidikan_terakhir_ayah" required /><InputError :message="form.errors.pendidikan_terakhir_ayah" /></div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2"><Label for="pekerjaan_ayah">{{ labels.pekerjaan_ayah }}</Label><Input id="pekerjaan_ayah" v-model="form.pekerjaan_ayah" required /><InputError :message="form.errors.pekerjaan_ayah" /></div>
                        <div class="grid gap-2"><Label for="penghasilan_ayah">{{ labels.penghasilan_ayah }}</Label><Input id="penghasilan_ayah" v-model="form.penghasilan_ayah" required /><InputError :message="form.errors.penghasilan_ayah" /></div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2"><Label for="no_telepon_ayah">{{ labels.no_telepon_ayah }}</Label><Input id="no_telepon_ayah" v-model="form.no_telepon_ayah" required /><InputError :message="form.errors.no_telepon_ayah" /></div>
                        <div class="grid gap-2"><Label for="email_ayah">{{ labels.email_ayah }}</Label><Input id="email_ayah" type="email" v-model="form.email_ayah" required /><InputError :message="form.errors.email_ayah" /></div>
                    </div>
                    <div class="grid gap-2"><Label for="alamat_ayah">{{ labels.alamat_ayah }}</Label><textarea id="alamat_ayah" v-model="form.alamat_ayah" class="min-h-20 rounded-md border px-3 py-2" required /><InputError :message="form.errors.alamat_ayah" /></div>
                </section>

                <!-- Data Ibu Kandung -->
                <section class="space-y-4 rounded-lg border p-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Data Ibu Kandung</h2>
                    <div class="grid gap-2"><Label for="nama_ibu_kandung">{{ labels.nama_ibu_kandung }}</Label><Input id="nama_ibu_kandung" v-model="form.nama_ibu_kandung" required /><InputError :message="form.errors.nama_ibu_kandung" /></div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2"><Label for="tanggal_lahir_ibu">{{ labels.tanggal_lahir_ibu }}</Label><DatePicker id="tanggal_lahir_ibu" v-model="form.tanggal_lahir_ibu" placeholder="Pilih tanggal lahir ibu" /><InputError :message="form.errors.tanggal_lahir_ibu" /></div>
                        <div class="grid gap-2"><Label for="pendidikan_terakhir_ibu">{{ labels.pendidikan_terakhir_ibu }}</Label><Input id="pendidikan_terakhir_ibu" v-model="form.pendidikan_terakhir_ibu" required /><InputError :message="form.errors.pendidikan_terakhir_ibu" /></div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2"><Label for="pekerjaan_ibu">{{ labels.pekerjaan_ibu }}</Label><Input id="pekerjaan_ibu" v-model="form.pekerjaan_ibu" required /><InputError :message="form.errors.pekerjaan_ibu" /></div>
                        <div class="grid gap-2"><Label for="penghasilan_ibu">{{ labels.penghasilan_ibu }}</Label><Input id="penghasilan_ibu" v-model="form.penghasilan_ibu" required /><InputError :message="form.errors.penghasilan_ibu" /></div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2"><Label for="no_telepon_ibu">{{ labels.no_telepon_ibu }}</Label><Input id="no_telepon_ibu" v-model="form.no_telepon_ibu" required /><InputError :message="form.errors.no_telepon_ibu" /></div>
                        <div class="grid gap-2"><Label for="email_ibu">{{ labels.email_ibu }}</Label><Input id="email_ibu" type="email" v-model="form.email_ibu" required /><InputError :message="form.errors.email_ibu" /></div>
                    </div>
                    <div class="grid gap-2"><Label for="alamat_ibu">{{ labels.alamat_ibu }}</Label><textarea id="alamat_ibu" v-model="form.alamat_ibu" class="min-h-20 rounded-md border px-3 py-2" required /><InputError :message="form.errors.alamat_ibu" /></div>
                </section>

                <!-- Keamanan -->
                <section class="space-y-4 rounded-lg border p-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Keamanan</h2>
                    <div v-for="field in ['password', 'password_confirmation']" :key="field" class="grid gap-2"><Label :for="field">{{ labels[field] }}</Label><Input :id="field" type="password" v-model="form[field]" :required="!props.user" autocomplete="new-password" /><InputError :message="form.errors[field]" /></div>
                </section>

                <Button :disabled="form.processing">Simpan</Button>
            </form>

            <!-- Dosen / Karyawan: flat form (tetap) -->
            <form v-else @submit.prevent="submit" class="space-y-4">
                <template v-for="field in common" :key="field">
                    <div v-if="field === 'tanggal_lahir'" class="grid gap-2"><Label :for="field">{{ labels[field] }}</Label><DatePicker :id="field" v-model="form[field]" placeholder="Pilih tanggal lahir" /><InputError :message="form.errors[field]" /></div>
                    <div v-else class="grid gap-2"><Label :for="field">{{ labels[field] }}</Label><Input :id="field" :type="field.includes('email') ? 'email' : 'text'" v-model="form[field]" required /><InputError :message="form.errors[field]" /></div>
                </template>
                <div class="grid gap-2"><Label>Jenis Kelamin</Label><div class="flex gap-4"><label v-for="gender in ['Laki-laki', 'Perempuan']" :key="gender" class="flex items-center gap-2"><input v-model="form.jenis_kelamin" type="radio" :value="gender" required /> {{ gender }}</label></div><InputError :message="form.errors.jenis_kelamin" /></div>
                <div class="grid gap-2"><Label for="agama">Agama</Label><select id="agama" v-model="form.agama" class="rounded-md border px-3 py-2" required><option value="">Pilih agama</option><option v-for="item in agama" :key="item" :value="item">{{ item }}</option></select><InputError :message="form.errors.agama" /></div>
                <div v-for="field in roleFields" :key="field" class="grid gap-2"><Label :for="field">{{ labels[field] }}</Label><select v-if="field === 'status'" :id="field" v-model="form[field]" class="rounded-md border px-3 py-2" required><option value="">Pilih status</option><option v-for="item in statuses" :key="item" :value="item">{{ item }}</option></select><select v-else-if="field === 'prodi_id'" :id="field" v-model="form[field]" class="rounded-md border px-3 py-2" required><option value="">Pilih program studi</option><optgroup v-for="[fakultas, prodiList] in groupedProgramStudi" :key="fakultas" :label="fakultas"><option v-for="item in prodiList" :key="item.id" :value="item.id">{{ item.jenjang }} - {{ item.nama_prodi }}</option></optgroup></select><template v-else-if="field === 'dosen_wali_id'"><div class="relative"><button :id="field" type="button" class="flex w-full items-center justify-between rounded-md border px-3 py-2 text-left" role="combobox" :aria-expanded="dosenWaliOpen" aria-controls="dosen-wali-options" :aria-invalid="!!form.errors[field]" @click="openDosenWali" @keydown="handleDosenWaliKeydown"><span>{{ selectedDosen?.name ?? 'Pilih dosen wali' }}</span><span aria-hidden="true">⌄</span></button><div v-if="dosenWaliOpen" class="absolute z-10 mt-1 w-full rounded-md border bg-white p-2 shadow-md" role="listbox" id="dosen-wali-options"><Input v-model="search" placeholder="Cari dosen wali" aria-label="Cari dosen wali" autofocus /><div class="mt-1 max-h-48 overflow-y-auto"><button v-for="dosen in filteredDosen" :key="dosen.id" type="button" class="block w-full rounded px-2 py-1 text-left hover:bg-muted" role="option" :aria-selected="Number(form[field]) === dosen.id" @click="selectDosenWali(dosen.id)">{{ dosen.name }}</button><p v-if="filteredDosen.length === 0" class="px-2 py-1 text-sm text-muted-foreground">Dosen tidak ditemukan</p></div></div><input :id="`${field}-value`" v-model="form[field]" type="hidden" required /></div></template><Input v-else :id="field" v-model="form[field]" :type="['angkatan', 'semester'].includes(field) ? 'number' : 'text'" required /><InputError :message="form.errors[field]" /></div>
                <div class="grid gap-2"><Label for="alamat">{{ labels.alamat }}</Label><textarea id="alamat" v-model="form.alamat" class="min-h-24 rounded-md border px-3 py-2" required /><InputError :message="form.errors.alamat" /></div>
                <div v-for="field in ['password', 'password_confirmation']" :key="field" class="grid gap-2"><Label :for="field">{{ labels[field] }}</Label><Input :id="field" type="password" v-model="form[field]" :required="!props.user" autocomplete="new-password" /><InputError :message="form.errors[field]" /></div>
                <Button :disabled="form.processing">Simpan</Button>
            </form>
        </div>
    </AppLayout>
</template>
