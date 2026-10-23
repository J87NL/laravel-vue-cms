<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import TableBuilder from '@table-builder/components/TableBuilder.vue';
import type { TableData } from '@table-builder/types/table-builder';
import { useBreadcrumbs } from '@cms/composables/useBreadcrumbs';
import { useConfirm } from '@cms/composables/useConfirm';
import { usePagePadding } from '@cms/composables/usePagePadding';
import type { CmsBreadcrumb } from '@cms/types';
import { ui } from '@cms/ui';

const props = defineProps<{
    menus: {
        key: string;
        label: string;
        items: TableData;
        createUrl: string;
    }[];
    breadcrumbs: CmsBreadcrumb[];
}>();

useBreadcrumbs(() => props.breadcrumbs);

const padded = usePagePadding();

const confirm = useConfirm();
</script>

<template>
    <Head :title="$t('vue-cms::menus.index.title')" />

    <div class="flex max-w-5xl flex-col gap-6" :class="{ 'p-4': padded }">
        <div class="flex flex-col gap-1">
            <h1 :class="ui.title">{{ $t('vue-cms::menus.index.title') }}</h1>
            <p :class="ui.muted">
                {{ $t('vue-cms::menus.index.description') }}
            </p>
        </div>

        <section v-for="menu in menus" :key="menu.key" :class="ui.panel">
            <div class="flex items-center justify-between gap-4">
                <h2 :class="ui.heading">{{ menu.label }}</h2>
                <Link
                    v-if="$can('cms.menus')"
                    :href="menu.createUrl"
                    :class="ui.button"
                >
                    {{ $t('vue-cms::menus.actions.create') }}
                </Link>
            </div>

            <TableBuilder :table="menu.items" :confirm="confirm" />
        </section>
    </div>
</template>
