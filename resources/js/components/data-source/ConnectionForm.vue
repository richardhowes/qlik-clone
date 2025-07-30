<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ref, watch, onMounted } from 'vue';

interface ConfigField {
    name: string;
    label: string;
    type: 'text' | 'number' | 'password' | 'checkbox' | 'select';
    required: boolean;
    default?: any;
    options?: Array<{ value: string; label: string }>;
}

interface Props {
    modelValue: Record<string, any>;
    type: string;
    errors?: Record<string, string>;
}

const props = defineProps<Props>();
const emit = defineEmits(['update:modelValue']);

const fields = ref<ConfigField[]>([]);
const formData = ref<Record<string, any>>({});

// Fetch field configuration based on type
const fetchFields = async () => {
    try {
        const response = await fetch(route('data-sources.config-fields') + `?type=${props.type}`);
        const data = await response.json();
        
        if (data.fields) {
            fields.value = data.fields;
            
            // Initialize form data with existing values or defaults
            const initialData: Record<string, any> = {};
            data.fields.forEach((field: ConfigField) => {
                if (props.modelValue && props.modelValue[field.name] !== undefined) {
                    initialData[field.name] = props.modelValue[field.name];
                } else if (field.default !== undefined) {
                    initialData[field.name] = field.default;
                } else {
                    initialData[field.name] = '';
                }
            });
            formData.value = initialData;
        }
    } catch (error) {
        console.error('Failed to fetch field configuration:', error);
    }
};

// Watch for type changes
watch(() => props.type, () => {
    fetchFields();
});

// Watch form data changes and emit updates
watch(formData, (newValue) => {
    emit('update:modelValue', newValue);
}, { deep: true });

onMounted(() => {
    console.log('ConnectionForm mounted with modelValue:', props.modelValue);
    if (props.type) {
        fetchFields();
    }
});

const updateField = (fieldName: string, value: any) => {
    formData.value[fieldName] = value;
};
</script>

<template>
    <div class="space-y-4">
        <div v-for="field in fields" :key="field.name" class="space-y-2">
            <!-- Text/Number/Password Input -->
            <template v-if="['text', 'number', 'password'].includes(field.type)">
                <Label :for="field.name">
                    {{ field.label }}
                    <span v-if="field.required" class="text-destructive">*</span>
                </Label>
                <Input
                    :id="field.name"
                    :type="field.type"
                    :modelValue="formData[field.name]"
                    @update:modelValue="updateField(field.name, $event)"
                    :placeholder="field.default?.toString()"
                    :class="{ 'border-destructive': errors?.[field.name] }"
                />
                <p v-if="errors?.[field.name]" class="text-sm text-destructive">
                    {{ errors[field.name] }}
                </p>
            </template>

            <!-- Checkbox -->
            <template v-else-if="field.type === 'checkbox'">
                <div class="flex items-center space-x-2">
                    <Checkbox
                        :id="field.name"
                        :checked="formData[field.name]"
                        @update:checked="updateField(field.name, $event)"
                    />
                    <Label :for="field.name" class="cursor-pointer">
                        {{ field.label }}
                    </Label>
                </div>
            </template>

            <!-- Select -->
            <template v-else-if="field.type === 'select' && field.options">
                <Label :for="field.name">
                    {{ field.label }}
                    <span v-if="field.required" class="text-destructive">*</span>
                </Label>
                <Select
                    :value="formData[field.name]"
                    @update:modelValue="updateField(field.name, $event)"
                >
                    <SelectTrigger :id="field.name">
                        <SelectValue :placeholder="`Select ${field.label.toLowerCase()}`" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem 
                            v-for="option in field.options" 
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="errors?.[field.name]" class="text-sm text-destructive">
                    {{ errors[field.name] }}
                </p>
            </template>
        </div>

        <!-- Test Connection Button -->
        <div class="pt-4">
            <p class="text-sm text-muted-foreground mb-2">
                Test your connection to ensure the settings are correct
            </p>
        </div>
    </div>
</template>