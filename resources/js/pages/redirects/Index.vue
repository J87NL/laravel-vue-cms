<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import TableBuilder from '@table-builder/components/TableBuilder.vue';
import type { TableData } from '@table-builder/types/table-builder';
import { useBreadcrumbs } from '@cms/composables/useBreadcrumbs';
import { useConfirm } from '@cms/composables/useConfirm';
import { useErrors } from '@cms/composables/useErrors';
import { usePagePadding } from '@cms/composables/usePagePadding';
import type { CmsBreadcrumb } from '@cms/types';
import { ui } from '@cms/ui';

const props = defineProps<{
    redirects: TableData;
    createUrl: string;
    importUrl: string;
    breadcrumbs: CmsBreadcrumb[];
}>();

useBreadcrumbs(() => props.breadcrumbs);

const padded = usePagePadding();

const confirm = useConfirm();

const errors = useErrors(['import']);
</script>

<template>
    <Head :title="$t('vue-cms::redirects.index.title')" />

    <div class="flex flex-col gap-6" :class="{ 'p-4': padded }">
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h1 :class="ui.title">
                    {{ $t('vue-cms::redirects.index.title') }}
                </h1>
                <p :class="ui.muted">
                    {{ $t('vue-cms::redirects.index.description') }}
                </p>
            </div>

            <div :class="ui.toolbar">
                <Link :href="importUrl" :class="ui.button">
                    {{ $t('vue-cms::redirects.import.title') }}
                </Link>
                <Link :href="createUrl" :class="ui.primaryButton">
                    {{ $t('vue-cms::redirects.create.title') }}
                </Link>
            </div>
        </div>

        <p v-for="error in errors" :key="error" :class="ui.error">
            {{ error }}
        </p>

        <TableBuilder :table="redirects" :confirm="confirm" />
    </div>
</template>
