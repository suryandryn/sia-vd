<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';

interface Props {
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center rounded-[8px] bg-[#0075de] text-white">
        <AppLogoIcon class="size-5 fill-current text-white" />
    </div>
    <div class="ml-1 grid flex-1 text-left">
        <span class="truncate text-[14px] font-semibold leading-none tracking-[-0.01em] text-black">Laravel Starter Kit</span>
        <span class="truncate text-[11px] font-medium uppercase tracking-[0.06em] text-[#a39e98]">SIA</span>
    </div>
</template>
