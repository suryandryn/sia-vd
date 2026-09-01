<script setup lang="ts">
import { computed, ref } from 'vue';
import { Input } from '@/components/ui/input';

const props = defineProps<{
    id: string;
    options: { id: number; name: string }[];
    modelValue: number | string | null;
    placeholder?: string;
    searchPlaceholder?: string;
    required?: boolean;
}>();

const emit = defineEmits<{ (e: 'update:modelValue', value: number): void }>();

const open = ref(false);
const search = ref('');

const filtered = computed(() => props.options.filter((option) => option.name.toLowerCase().includes(search.value.toLowerCase())));
const selected = computed(() => props.options.find((option) => option.id === Number(props.modelValue)));

const openOptions = () => {
    open.value = true;
    search.value = '';
};

const select = (id: number) => {
    emit('update:modelValue', id);
    open.value = false;
    search.value = '';
};

const handleKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
        open.value = false;
    }
};
</script>

<template>
    <div class="relative">
        <button
            :id="id"
            type="button"
            class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-left text-sm shadow-sm"
            role="combobox"
            :aria-expanded="open"
            :aria-controls="`${id}-options`"
            @click="openOptions"
            @keydown="handleKeydown"
        >
            <span :class="{ 'text-muted-foreground': !selected }">{{ selected?.name ?? placeholder ?? 'Pilih' }}</span>
            <span aria-hidden="true">⌄</span>
        </button>
        <div v-if="open" :id="`${id}-options`" class="absolute z-10 mt-1 w-full rounded-md border bg-background p-2 shadow-md" role="listbox">
            <Input v-model="search" :placeholder="searchPlaceholder ?? 'Cari'" :aria-label="searchPlaceholder ?? 'Cari'" autofocus />
            <div class="mt-1 max-h-48 overflow-y-auto">
                <button
                    v-for="option in filtered"
                    :key="option.id"
                    type="button"
                    class="block w-full rounded px-2 py-1 text-left text-sm hover:bg-muted"
                    role="option"
                    :aria-selected="Number(props.modelValue) === option.id"
                    @click="select(option.id)"
                >
                    {{ option.name }}
                </button>
                <p v-if="filtered.length === 0" class="px-2 py-1 text-sm text-muted-foreground">Tidak ditemukan</p>
            </div>
        </div>
        <input :value="modelValue ?? ''" type="hidden" :required="required" />
    </div>
</template>
