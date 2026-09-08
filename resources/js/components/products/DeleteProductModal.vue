<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { FwbButton, FwbModal } from 'flowbite-vue';
import { ref } from 'vue';
import { destroy } from '@/routes/products';
import type { Product, Team } from '@/types';

type Props = {
    open: boolean;
    team: Team;
    product: Product;
};

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const formKey = ref(0);

function handleOpenChange(nextOpen: boolean) {
    emit('update:open', nextOpen);

    if (!nextOpen) {
        formKey.value++;
    }
}
</script>

<template>
    <FwbModal v-if="open" size="sm" @close="handleOpenChange(false)">
        <template #header>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Delete product
            </h3>
        </template>

        <template #body>
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                Are you sure you want to delete
                <strong>{{ props.product.name }}</strong
                >? This cannot be undone.
            </p>

            <Form
                :key="formKey"
                v-bind="destroy.form([props.team.slug, props.product.id])"
                class="flex justify-end gap-2"
                v-slot="{ processing }"
                @success="handleOpenChange(false)"
            >
                <FwbButton
                    color="alternative"
                    type="button"
                    @click="handleOpenChange(false)"
                >
                    Cancel
                </FwbButton>
                <FwbButton color="red" type="submit" :disabled="processing">
                    Delete
                </FwbButton>
            </Form>
        </template>
    </FwbModal>
</template>
