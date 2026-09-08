<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import {
    FwbButton,
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableHead,
    FwbTableHeadCell,
    FwbTableRow,
} from 'flowbite-vue';
import { computed, ref } from 'vue';
import DeleteProductModal from '@/components/products/DeleteProductModal.vue';
import ProductFormModal from '@/components/products/ProductFormModal.vue';
import { index as productsIndex } from '@/routes/products';
import type { Product, Team } from '@/types';

const props = defineProps<{
    products: Product[];
}>();

const page = usePage();
const team = computed(() => page.props.currentTeam);

defineOptions({
    layout: (layoutProps: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Products',
                href: layoutProps.currentTeam
                    ? productsIndex(layoutProps.currentTeam.slug)
                    : '/',
            },
        ],
    }),
});

const formModalOpen = ref(false);
const deleteModalOpen = ref(false);
const editingProduct = ref<Product | null>(null);
const deletingProduct = ref<Product | null>(null);

function openCreateModal() {
    editingProduct.value = null;
    formModalOpen.value = true;
}

function openEditModal(product: Product) {
    editingProduct.value = product;
    formModalOpen.value = true;
}

function openDeleteModal(product: Product) {
    deletingProduct.value = product;
    deleteModalOpen.value = true;
}

function formatPrice(cents: number): string {
    return (cents / 100).toLocaleString(undefined, {
        style: 'currency',
        currency: 'USD',
    });
}
</script>

<template>
    <Head title="Products" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div class="flex items-center justify-end">
            <FwbButton @click="openCreateModal">New product</FwbButton>
        </div>

        <FwbTable v-if="props.products.length > 0" hoverable>
            <FwbTableHead>
                <FwbTableHeadCell>Name</FwbTableHeadCell>
                <FwbTableHeadCell>Description</FwbTableHeadCell>
                <FwbTableHeadCell>Price</FwbTableHeadCell>
                <FwbTableHeadCell>Stock</FwbTableHeadCell>
                <FwbTableHeadCell>
                    <span class="sr-only">Actions</span>
                </FwbTableHeadCell>
            </FwbTableHead>
            <FwbTableBody>
                <FwbTableRow
                    v-for="product in props.products"
                    :key="product.id"
                >
                    <FwbTableCell
                        class="font-medium text-gray-900 dark:text-white"
                    >
                        {{ product.name }}
                    </FwbTableCell>
                    <FwbTableCell>{{
                        product.description ?? '—'
                    }}</FwbTableCell>
                    <FwbTableCell>{{
                        formatPrice(product.price)
                    }}</FwbTableCell>
                    <FwbTableCell>{{ product.stock }}</FwbTableCell>
                    <FwbTableCell>
                        <div class="flex justify-end gap-3">
                            <button
                                type="button"
                                class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                @click="openEditModal(product)"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                class="font-medium text-red-600 hover:underline dark:text-red-500"
                                @click="openDeleteModal(product)"
                            >
                                Delete
                            </button>
                        </div>
                    </FwbTableCell>
                </FwbTableRow>
            </FwbTableBody>
        </FwbTable>

        <p v-else class="text-sm text-gray-500 dark:text-gray-400">
            No products yet. Create your first one to get started.
        </p>

        <ProductFormModal
            v-if="team"
            :open="formModalOpen"
            :team="team"
            :product="editingProduct"
            @update:open="formModalOpen = $event"
        />

        <DeleteProductModal
            v-if="team && deletingProduct"
            :open="deleteModalOpen"
            :team="team"
            :product="deletingProduct"
            @update:open="deleteModalOpen = $event"
        />
    </div>
</template>
