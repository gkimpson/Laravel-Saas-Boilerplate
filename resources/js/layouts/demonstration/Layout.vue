<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { FwbSidebar, FwbSidebarItem, FwbSidebarItemGroup } from 'flowbite-vue';
import { Toaster } from '@/components/ui/sonner';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { index as productsIndex } from '@/routes/products';
import type { NavItem } from '@/types';

const page = usePage();

const productsUrl = page.props.currentTeam
    ? productsIndex(page.props.currentTeam.slug).url
    : '/';

const navItems: NavItem[] = [
    {
        title: 'Products',
        href: productsUrl,
    },
];

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <div>
        <FwbSidebar>
            <FwbSidebarItemGroup>
                <FwbSidebarItem
                    v-for="item in navItems"
                    :key="item.title"
                    tag="div"
                >
                    <template #default>
                        <Link
                            :href="item.href"
                            :class="[
                                'block',
                                {
                                    'font-semibold text-blue-700 dark:text-blue-500':
                                        isCurrentOrParentUrl(item.href),
                                },
                            ]"
                        >
                            {{ item.title }}
                        </Link>
                    </template>
                </FwbSidebarItem>
            </FwbSidebarItemGroup>
        </FwbSidebar>

        <main class="p-4 sm:ml-64">
            <slot />
        </main>

        <Toaster />
    </div>
</template>
