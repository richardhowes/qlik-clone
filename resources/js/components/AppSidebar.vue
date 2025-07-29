<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BarChart3, BookOpen, Clock, Database, FolderOpen, Home, LayoutDashboard, Search, Sparkles, Star, FileCode } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
        icon: Home,
    },
    {
        title: 'Dashboards',
        href: '/dashboards',
        icon: LayoutDashboard,
    },
    {
        title: 'Data Sources',
        href: '/data-sources',
        icon: Database,
    },
    {
        title: 'Queries',
        href: '/queries',
        icon: FileCode,
    },
    {
        title: 'Browse',
        href: '/browse',
        icon: Search,
    },
];

const analyticsNavItems: NavItem[] = [
    {
        title: 'Insights',
        href: '/insights',
        icon: Sparkles,
    },
    {
        title: 'Collections',
        href: '/collections',
        icon: FolderOpen,
    },
    {
        title: 'Favorites',
        href: '/favorites',
        icon: Star,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Learn',
        href: '/learn',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- Main Navigation -->
            <NavMain :items="mainNavItems" />

            <!-- Analytics Section -->
            <SidebarGroup class="mt-4">
                <SidebarGroupLabel>Analytics</SidebarGroupLabel>
                <NavMain :items="analyticsNavItems" />
            </SidebarGroup>

            <!-- Recents Section -->
            <SidebarGroup class="mt-4 px-2 py-0">
                <SidebarGroupLabel>
                    <div class="flex items-center gap-2">
                        <Clock class="h-4 w-4" />
                        <span>Recents</span>
                    </div>
                </SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton class="text-sm text-muted-foreground">
                            <span>No recent items</span>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
