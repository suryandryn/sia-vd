<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarMenuSub, SidebarMenuSubButton, SidebarMenuSubItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
</script>

<template>
    <SidebarGroup class="px-0 py-0">
        <SidebarGroupLabel class="px-2 text-[11px]">Platform</SidebarGroupLabel>
        <SidebarMenu class="gap-0.5">
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton as-child :is-active="item.href === page.url">
                    <Link :href="item.href">
                        <component v-if="item.icon" :is="item.icon" class="text-[#615d59] group-data-[active=true]/menu-button:text-black" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
                <SidebarMenuSub v-if="item.items" class="ml-3.5 border-l border-[#e6e6e6] pl-2">
                    <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                        <SidebarMenuSubButton as-child :is-active="subItem.href === page.url" class="rounded-[5px] text-[14px] leading-5">
                            <Link :href="subItem.href"><span>{{ subItem.title }}</span></Link>
                        </SidebarMenuSubButton>
                    </SidebarMenuSubItem>
                </SidebarMenuSub>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
