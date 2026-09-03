<script setup lang="ts">
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';
import SearchSelect from '@/components/SearchSelect.vue';
import DatePicker from '@/components/DatePicker.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const page = usePage<{ flash: { success?: string; error?: string } }>();

const props = defineProps<{ programStudi: Record<string, any> | null; fakultas: { id: number; nama_fakultas: string }[]; dosen: { id: number; name: string }[] }>();

const title = `${props.programStudi ? 'Edit' : 'Tambah'} Program Studi`;

const jenjang = ['D3', 'D4', 'S1', 'S2', 'S3', 'Sp-1', 'Sp-2', 'Profesi'];
const statusAkreditasi = ['Unggul', 'Baik Sekali', 'Baik'];

const form = useForm({
    fakultas_id: props.programStudi?.fakultas_id ?? '',
    kode_prodi: props.programStudi?.kode_prodi ?? '',
    nama_prodi: props.programStudi?.nama_prodi ?? '',
    jenjang: props.programStudi?.jenjang ?? '',
    status_akreditasi: props.programStudi?.status_akreditasi ?? '',
    no_sk_akreditasi: props.programStudi?.no_sk_akreditasi ?? '',
    tanggal_akreditasi_mulai: props.programStudi?.tanggal_akreditasi_mulai?.slice(0, 10) ?? '',
    tanggal_akreditasi_akhir: props.programStudi?.tanggal_akreditasi_akhir?.slice(0, 10) ?? '',
    kaprodi: props.programStudi?.kaprodi ?? '',
    tahun_berdiri: props.programStudi?.tahun_berdiri ?? '',
});

const submit = () => (props.programStudi ? form.put(route('admin.program-studi.update', props.programStudi.id)) : form.post(route('admin.program-studi.store')));
</script>

<template>
    <Head :title="title" />
    <AppLayout :breadcrumbs="[{ title: 'Program Studi', href: route('admin.program-studi.index') }]">
        <div class="max-w-xl space-y-6 p-4">
            <div v-if="page.props.flash?.success" class="rounded border border-green-300 bg-green-50 p-3 text-green-800" role="alert">{{ page.props.flash.success }}</div>
            <div v-if="page.props.flash?.error" class="rounded border border-red-300 bg-red-50 p-3 text-red-800" role="alert">{{ page.props.flash.error }}</div>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">{{ title }}</h1>
                <Link :href="route('admin.program-studi.index')" class="underline">Kembali</Link>
            </div>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="fakultas_id">Fakultas</Label>
                    <SearchSelect id="fakultas_id" v-model="form.fakultas_id" :options="props.fakultas.map((item) => ({ id: item.id, name: item.nama_fakultas }))" placeholder="Pilih fakultas" search-placeholder="Cari fakultas" required />
                    <InputError :message="form.errors.fakultas_id" />
                </div>
                <div class="grid gap-2">
                    <Label for="kode_prodi">Kode Program Studi</Label>
                    <Input id="kode_prodi" v-model="form.kode_prodi" type="text" required />
                    <InputError :message="form.errors.kode_prodi" />
                </div>
                <div class="grid gap-2">
                    <Label for="nama_prodi">Nama Program Studi</Label>
                    <Input id="nama_prodi" v-model="form.nama_prodi" type="text" required />
                    <InputError :message="form.errors.nama_prodi" />
                </div>
                <div class="grid gap-2">
                    <Label for="jenjang">Jenjang</Label>
                    <select id="jenjang" v-model="form.jenjang" class="h-9 rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm" required>
                        <option value="">Pilih jenjang</option>
                        <option v-for="item in jenjang" :key="item" :value="item">{{ item }}</option>
                    </select>
                    <InputError :message="form.errors.jenjang" />
                </div>
                <div class="grid gap-2">
                    <Label for="status_akreditasi">Status Akreditasi</Label>
                    <select id="status_akreditasi" v-model="form.status_akreditasi" class="h-9 rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm" required>
                        <option value="">Pilih status akreditasi</option>
                        <option v-for="item in statusAkreditasi" :key="item" :value="item">{{ item }}</option>
                    </select>
                    <InputError :message="form.errors.status_akreditasi" />
                </div>
                <div class="grid gap-2">
                    <Label for="no_sk_akreditasi">Nomor SK Akreditasi</Label>
                    <Input id="no_sk_akreditasi" v-model="form.no_sk_akreditasi" type="text" />
                    <InputError :message="form.errors.no_sk_akreditasi" />
                </div>
                <div class="grid gap-2">
                    <Label for="tanggal_akreditasi_mulai">Tanggal Akreditasi Mulai</Label>
                    <DatePicker id="tanggal_akreditasi_mulai" v-model="form.tanggal_akreditasi_mulai" placeholder="Pilih tanggal mulai" />
                    <InputError :message="form.errors.tanggal_akreditasi_mulai" />
                </div>
                <div class="grid gap-2">
                    <Label for="tanggal_akreditasi_akhir">Tanggal Akreditasi Akhir</Label>
                    <DatePicker id="tanggal_akreditasi_akhir" v-model="form.tanggal_akreditasi_akhir" placeholder="Pilih tanggal akhir" />
                    <InputError :message="form.errors.tanggal_akreditasi_akhir" />
                </div>
                <div class="grid gap-2">
                    <Label for="kaprodi">Kaprodi</Label>
                    <SearchSelect id="kaprodi" v-model="form.kaprodi" :options="props.dosen" placeholder="Pilih kaprodi" search-placeholder="Cari kaprodi" required />
                    <InputError :message="form.errors.kaprodi" />
                </div>
                <div class="grid gap-2">
                    <Label for="tahun_berdiri">Tahun Berdiri</Label>
                    <Input id="tahun_berdiri" v-model="form.tahun_berdiri" type="number" required />
                    <InputError :message="form.errors.tahun_berdiri" />
                </div>
                <Button :disabled="form.processing">Simpan</Button>
            </form>
        </div>
    </AppLayout>
</template>
