<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { FwbButton, FwbInput, FwbModal, FwbTextarea } from 'flowbite-vue';
import { computed, ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { store, update } from '@/routes/products';
import type { Product, Team } from '@/types';

type Props = {
    open: boolean;
    team: Team;
    product?: Product | null;
};

const props = withDefaults(defineProps<Props>(), {
    product: null,
});

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const isEditing = computed(() => props.product !== null);

const name = ref('');
const description = ref('');
const price = ref<number>(0);
const stock = ref<number>(0);
const formKey = ref(0);

function resetFields() {
    name.value = props.product?.name ?? '';
    description.value = props.product?.description ?? '';
    price.value = props.product?.price ?? 0;
    stock.value = props.product?.stock ?? 0;
}

watch(
    () => props.open,
    (open) => {
        if (open) {
            resetFields();
        }
    },
);

function handleOpenChange(nextOpen: boolean) {
    emit('update:open', nextOpen);

    if (!nextOpen) {
        formKey.value++;
    }
}

const formBinding = computed(() =>
    isEditing.value && props.product
        ? update.form([props.team.slug, props.product.id])
        : store.form(props.team.slug),
);
</script>

<template>
    <FwbModal v-if="open" @close="handleOpenChange(false)">
        <template #header>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ isEditing ? 'Edit product' : 'New product' }}
            </h3>
        </template>

        <template #body>
            <Form
                :key="formKey"
                v-bind="formBinding"
                class="space-y-4"
                v-slot="{ errors, processing }"
                @success="handleOpenChange(false)"
            >
                <div>
                    <FwbInput
                        v-model="name"
                        name="name"
                        label="Name"
                        required
                    />
                    <InputError :message="errors.name" />
                </div>

                <div>
                    <FwbTextarea
                        v-model="description"
                        name="description"
                        label="Description"
                        :rows="3"
                    />
                    <InputError :message="errors.description" />
                </div>

                <div>
                    <FwbInput
                        v-model="price"
                        type="number"
                        name="price"
                        label="Price (in cents)"
                        min="0"
                        required
                    />
                    <InputError :message="errors.price" />
                </div>

                <div>
                    <FwbInput
                        v-model="stock"
                        type="number"
                        name="stock"
                        label="Stock"
                        min="0"
                        required
                    />
                    <InputError :message="errors.stock" />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <FwbButton
                        color="alternative"
                        type="button"
                        @click="handleOpenChange(false)"
                    >
                        Cancel
                    </FwbButton>
                    <FwbButton type="submit" :disabled="processing">
                        {{ isEditing ? 'Save changes' : 'Create product' }}
                    </FwbButton>
                </div>
            </Form>
        </template>
    </FwbModal>
</template>
