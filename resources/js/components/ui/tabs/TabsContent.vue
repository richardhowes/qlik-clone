<script setup lang="ts">
import { cn } from '@/lib/utils';
import { TabsContent } from 'reka-ui';

interface Props {
    value: string;
    forceMount?: boolean;
    class?: string;
}

const props = defineProps<Props>();
</script>

<template>
    <TabsContent
        :class="cn(
            'mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2',
            props.class
        )"
        :value="props.value"
        :force-mount="props.forceMount"
    >
        <slot />
    </TabsContent>
</template>