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
    media: TableData;
    createUrl: string;
    breadcrumbs: CmsBreadcrumb[];
}>();

useBreadcrumbs(() => props.breadcrumbs);

const padded = usePagePadding();

const confirm = useConfirm();

const errors = useErrors(['media']);
</script>

<template>
    <Head :title="$t('vue-cms::media.index.title')" />

    <div class="flex flex-col gap-6" :class="{ 'p-4': padded }">
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h1 :class="ui.title">
                    {{ $t('vue-cms::media.index.title') }}
                </h1>
                <p :class="ui.muted">
                    {{ $t('vue-cms::media.index.description') }}
                </p>
            </div>

            <Link
                v-if="$can('cms.media')"
                :href="createUrl"
                :class="ui.primaryButton"
            >
                {{ $t('vue-cms::media.create.title') }}
            </Link>
        </div>

        <p v-for="error in errors" :key="error" :class="ui.error">
            {{ error }}
        </p>

        <TableBuilder :table="media" :confirm="confirm" />
    </div>
</template>
