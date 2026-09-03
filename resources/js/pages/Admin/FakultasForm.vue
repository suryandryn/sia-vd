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

const props = defineProps<{ fakultas: Record<string, any> | null; dosen: { id: number; name: string }[] }>();

const title = `${props.fakultas ? 'Edit' : 'Tambah'} Fakultas`;

const form = useForm({
    kode_fakultas: props.fakultas?.kode_fakultas ?? '',
    nama_fakultas: props.fakultas?.nama_fakultas ?? '',
    dekan_id: props.fakultas?.dekan_id ?? '',
    tanggal_berdiri: props.fakultas?.tanggal_berdiri?.slice(0, 10) ?? '',
    no_telp: props.fakultas?.no_telp ?? '',
    email: props.fakultas?.email ?? '',
});

const submit = () => (props.fakultas ? form.put(route('admin.fakultas.update', props.fakultas.id)) : form.post(route('admin.fakultas.store')));
</script>

<template>
    <Head :title="title" />
    <AppLayout :breadcrumbs="[{ title: 'Fakultas', href: route('admin.fakultas.index') }]">
        <div class="max-w-xl space-y-6 p-4">
            <div v-if="page.props.flash?.success" class="rounded border border-green-300 bg-green-50 p-3 text-green-800" role="alert">{{ page.props.flash.success }}</div>
            <div v-if="page.props.flash?.error" class="rounded border border-red-300 bg-red-50 p-3 text-red-800" role="alert">{{ page.props.flash.error }}</div>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">{{ title }}</h1>
                <Link :href="route('admin.fakultas.index')" class="underline">Kembali</Link>
            </div>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="kode_fakultas">Kode Fakultas</Label>
                    <Input id="kode_fakultas" v-model="form.kode_fakultas" type="text" required />
                    <InputError :message="form.errors.kode_fakultas" />
                </div>
                <div class="grid gap-2">
                    <Label for="nama_fakultas">Nama Fakultas</Label>
                    <Input id="nama_fakultas" v-model="form.nama_fakultas" type="text" required />
                    <InputError :message="form.errors.nama_fakultas" />
                </div>
                <div class="grid gap-2">
                    <Label for="dekan_id">Dekan</Label>
                    <SearchSelect id="dekan_id" v-model="form.dekan_id" :options="props.dosen" placeholder="Pilih dekan" search-placeholder="Cari dekan" required />
                    <InputError :message="form.errors.dekan_id" />
                </div>
                <div class="grid gap-2">
                    <Label for="tanggal_berdiri">Tanggal Berdiri</Label>
                    <DatePicker id="tanggal_berdiri" v-model="form.tanggal_berdiri" placeholder="Pilih tanggal berdiri" />
                    <InputError :message="form.errors.tanggal_berdiri" />
                </div>
                <div class="grid gap-2">
                    <Label for="no_telp">Nomor Telepon</Label>
                    <Input id="no_telp" v-model="form.no_telp" type="text" required />
                    <InputError :message="form.errors.no_telp" />
                </div>
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" required />
                    <InputError :message="form.errors.email" />
                </div>
                <Button :disabled="form.processing">Simpan</Button>
            </form>
        </div>
    </AppLayout>
</template>
