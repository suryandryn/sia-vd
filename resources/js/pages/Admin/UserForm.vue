<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';

type User = { id: number; name: string; username: string; email: string; nomor_induk: string | null; tempat_lahir: string | null; tanggal_lahir: string | null; jenis_kelamin: string | null; agama: string | null; no_telepon: string | null; alamat: string | null; kewarganegaraan: string | null };
const props = defineProps<{ title: string; type: string; user: User | null }>();
const form = useForm({ name: props.user?.name ?? '', username: props.user?.username ?? '', email: props.user?.email ?? '', nomor_induk: props.user?.nomor_induk ?? '', tempat_lahir: props.user?.tempat_lahir ?? '', tanggal_lahir: props.user?.tanggal_lahir ?? '', jenis_kelamin: props.user?.jenis_kelamin ?? '', agama: props.user?.agama ?? '', no_telepon: props.user?.no_telepon ?? '', alamat: props.user?.alamat ?? '', kewarganegaraan: props.user?.kewarganegaraan ?? '', password: '', password_confirmation: '' });
const submit = () => props.user ? form.put(route(`admin.users.${props.type}.update`, props.user.id)) : form.post(route(`admin.users.${props.type}.store`));
</script>
<template>
    <Head :title="props.title" />
    <AppLayout :breadcrumbs="[{ title: props.title, href: '#' }]">
        <div class="max-w-xl space-y-6 p-4">
            <div class="flex items-center justify-between"><h1 class="text-xl font-semibold">{{ props.title }}</h1><Link :href="route(`admin.users.${props.type}`)" class="underline">Kembali</Link></div>
            <form @submit.prevent="submit" class="space-y-4">
                <div v-for="field in ['name', 'username', 'email', 'nomor_induk', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'no_telepon', 'kewarganegaraan']" :key="field" class="grid gap-2"><Label :for="field">{{ field }}</Label><Input :id="field" :type="field === 'email' ? 'email' : field === 'tanggal_lahir' ? 'date' : 'text'" v-model="form[field]" :required="['name', 'username', 'email'].includes(field)" /><InputError :message="form.errors[field]" /></div>
                <div class="grid gap-2"><Label for="alamat">alamat</Label><textarea id="alamat" v-model="form.alamat" class="min-h-24 rounded-md border px-3 py-2" /><InputError :message="form.errors.alamat" /></div>
                <div class="grid gap-2"><Label for="password">Password</Label><Input id="password" type="password" v-model="form.password" :required="!props.user" autocomplete="new-password" /><InputError :message="form.errors.password" /></div>
                <div class="grid gap-2"><Label for="password_confirmation">Konfirmasi Password</Label><Input id="password_confirmation" type="password" v-model="form.password_confirmation" :required="!props.user" autocomplete="new-password" /><InputError :message="form.errors.password_confirmation" /></div>
                <Button :disabled="form.processing">Simpan</Button>
            </form>
        </div>
    </AppLayout>
</template>
