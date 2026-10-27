<script setup lang="ts">
import Form from '@form-builder/components/Form.vue';
import type { FormSchema } from '@form-builder/types/form-builder';
import { Head, Link } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed, ref } from 'vue';
import { useBreadcrumbs } from '@cms/composables/useBreadcrumbs';
import { useConfirm } from '@cms/composables/useConfirm';
import { usePagePadding } from '@cms/composables/usePagePadding';
import type { CmsBreadcrumb } from '@cms/types';
import { ui } from '@cms/ui';

const props = defineProps<{
    title: string;
    description?: string | null;
    notice?: string | null;
    breadcrumbs: CmsBreadcrumb[];
    backUrl?: string | null;
    form: FormSchema;
    preview?: {
        url: string | null;
        alt: string;
        objectPosition: string;
    } | null;
    replace?: FormSchema | null;
}>();

useBreadcrumbs(() => props.breadcrumbs);

const padded = usePagePadding();

const confirm = useConfirm();

type ReplaceForm = {
    submit: () => void;
    form: {
        file: unknown;
        processing: boolean;
    };
};

const replaceForm = ref<ReplaceForm | null>(null);

const canReplace = computed(
    () =>
        replaceForm.value?.form.file instanceof File &&
        !replaceForm.value.form.processing,
);

async function replaceFile(): Promise<void> {
    if (!replaceForm.value || !canReplace.value) {
        return;
    }

    if (!(await confirm(trans('vue-cms::media.actions.replace_confirm')))) {
        return;
    }

    replaceForm.value.submit();
}
</script>

<template>
    <Head :title="title" />

    <div class="flex max-w-3xl flex-col gap-6" :class="{ 'p-4': padded }">
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h1 :class="ui.title">{{ title }}</h1>
                <p v-if="description" :class="ui.muted">{{ description }}</p>
            </div>

            <Link v-if="backUrl" :href="backUrl" :class="ui.button">
                {{ $t('vue-cms::common.back') }}
            </Link>
        </div>

        <p v-if="notice" :class="[ui.panel, 'text-sm']" role="status">
            {{ notice }}
        </p>

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

        <section v-if="replace && $can('cms.media')" :class="ui.panel">
            <div class="flex flex-col gap-1">
                <h2 :class="ui.heading">
                    {{ $t('vue-cms::media.sections.replace') }}
                </h2>
                <p :class="ui.muted">
                    {{ $t('vue-cms::media.help.replace') }}
                </p>
            </div>

            <Form
                ref="replaceForm"
                :schema="replace"
                :options="{ preserveScroll: true }"
            />

            <div :class="ui.toolbar">
                <button
                    type="button"
                    :class="ui.button"
                    :disabled="!canReplace"
                    @click="replaceFile"
                >
                    {{ $t('vue-cms::media.actions.replace') }}
                </button>
            </div>
        </section>
    </div>
</template>
