<script setup lang="ts">
import { TabsRoot } from 'reka-ui';

interface Props {
    defaultValue?: string;
    modelValue?: string;
    orientation?: 'horizontal' | 'vertical';
    dir?: 'ltr' | 'rtl';
    activationMode?: 'automatic' | 'manual';
}

const props = withDefaults(defineProps<Props>(), {
    orientation: 'horizontal',
    activationMode: 'automatic',
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();
</script>

<template>
    <TabsRoot v-bind="props" @update:model-value="emit('update:modelValue', $event)">
        <slot />
    </TabsRoot>
</template>