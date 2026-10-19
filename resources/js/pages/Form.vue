<script setup lang="ts">
import Form from '@form-builder/components/Form.vue';
import type { FormSchema } from '@form-builder/types/form-builder';
import { Head, Link } from '@inertiajs/vue3';
import { useBreadcrumbs } from '@cms/composables/useBreadcrumbs';
import type { CmsBreadcrumb } from '@cms/types';
import { ui } from '@cms/ui';

const props = defineProps<{
    title: string;
    description?: string | null;
    breadcrumbs: CmsBreadcrumb[];
    backUrl?: string | null;
    form: FormSchema;
    preview?: {
        url: string | null;
        alt: string;
        objectPosition: string;
    } | null;
}>();

useBreadcrumbs(() => props.breadcrumbs);
</script>

<template>
    <Head :title="title" />

    <div class="flex max-w-3xl flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h1 :class="ui.title">{{ title }}</h1>
                <p v-if="description" :class="ui.muted">{{ description }}</p>
            </div>

            <Link v-if="backUrl" :href="backUrl" :class="ui.button">
                {{ $t('vue-cms::common.back') }}
            </Link>
        </div>

        <img
            v-if="preview?.url"
            :src="preview.url"
            :alt="preview.alt"
            :style="{ objectPosition: preview.objectPosition }"
            class="h-48 w-full max-w-md rounded-lg object-cover"
        />

        <section :class="ui.panel">
            <Form :schema="form" />
        </section>
    </div>
</template>
