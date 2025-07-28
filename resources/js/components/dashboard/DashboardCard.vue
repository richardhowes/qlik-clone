<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Link, router } from '@inertiajs/vue3';
import { BarChart3, Calendar, Copy, MoreVertical, Pencil, Star, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import PlaceholderPattern from '../PlaceholderPattern.vue';

interface Dashboard {
    id: number;
    name: string;
    description?: string;
    slug: string;
    thumbnail?: string;
    is_favorite: boolean;
    updated_at: string;
    created_at: string;
}

const props = defineProps<{
    dashboard: Dashboard;
}>();

const formattedDate = computed(() => {
    const date = new Date(props.dashboard.updated_at);
    return new Intl.RelativeTimeFormat('en', { numeric: 'auto' }).format(Math.ceil((date.getTime() - Date.now()) / (1000 * 60 * 60 * 24)), 'day');
});

const toggleFavorite = () => {
    router.post(
        route('dashboards.favorite', props.dashboard.id),
        {},
        {
            preserveScroll: true,
        },
    );
};

const deleteDashboard = () => {
    if (confirm('Are you sure you want to delete this dashboard?')) {
        router.delete(route('dashboards.destroy', props.dashboard.id));
    }
};
</script>

<template>
    <Card class="group relative overflow-hidden transition-all hover:shadow-lg">
        <CardHeader class="pb-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-2">
                    <BarChart3 class="h-5 w-5 text-muted-foreground" />
                    <h3 class="leading-none font-semibold tracking-tight">
                        {{ dashboard.name }}
                    </h3>
                </div>
                <div class="flex items-center gap-1">
                    <Button variant="ghost" size="icon" class="h-8 w-8" @click="toggleFavorite">
                        <Star
                            :class="[
                                'h-4 w-4 transition-colors',
                                dashboard.is_favorite ? 'fill-yellow-400 text-yellow-400' : 'text-muted-foreground',
                            ]"
                        />
                    </Button>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" size="icon" class="h-8 w-8">
                                <MoreVertical class="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem as-child>
                                <Link :href="route('dashboards.edit', dashboard.id)">
                                    <Pencil class="mr-2 h-4 w-4" />
                                    Edit
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem>
                                <Copy class="mr-2 h-4 w-4" />
                                Duplicate
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem class="text-destructive" @click="deleteDashboard">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Delete
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </CardHeader>

        <CardContent class="pb-4">
            <Link :href="route('dashboards.show', dashboard.id)" class="block">
                <div class="relative aspect-[16/9] overflow-hidden rounded-md bg-muted">
                    <img v-if="dashboard.thumbnail" :src="dashboard.thumbnail" :alt="dashboard.name" class="h-full w-full object-cover" />
                    <PlaceholderPattern v-else />
                </div>

                <p v-if="dashboard.description" class="mt-3 line-clamp-2 text-sm text-muted-foreground">
                    {{ dashboard.description }}
                </p>
            </Link>
        </CardContent>

        <CardFooter class="pt-0">
            <div class="flex items-center gap-1 text-xs text-muted-foreground">
                <Calendar class="h-3 w-3" />
                <span>Updated {{ formattedDate }}</span>
            </div>
        </CardFooter>
    </Card>
</template>
