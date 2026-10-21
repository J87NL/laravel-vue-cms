<script setup lang="ts">
import Form from '@form-builder/components/Form.vue';
import type { FormSchema } from '@form-builder/types/form-builder';
import { Head, Link, router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { ref } from 'vue';
import { useBreadcrumbs } from '@cms/composables/useBreadcrumbs';
import { useConfirm } from '@cms/composables/useConfirm';
import { useErrors } from '@cms/composables/useErrors';
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
    replace?: {
        url: string;
        accept: string[];
    } | null;
}>();

useBreadcrumbs(() => props.breadcrumbs);

const confirm = useConfirm();

const replaceErrors = useErrors(['file']);

const replacement = ref<File | null>(null);

const replacing = ref(false);

function pick(event: Event): void {
    const input = event.target as HTMLInputElement;

    replacement.value = input.files?.[0] ?? null;
}

async function replaceFile(): Promise<void> {
    if (!props.replace || !replacement.value) {
        return;
    }

    if (!(await confirm(trans('vue-cms::media.actions.replace_confirm')))) {
        return;
    }

    router.post(
        props.replace.url,
        { file: replacement.value },
        {
            forceFormData: true,
            preserveScroll: true,
            onStart: () => {
                replacing.value = true;
            },
            onFinish: () => {
                replacing.value = false;
            },
            onSuccess: () => {
                replacement.value = null;
            },
        },
    );
}
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

        <section v-if="replace && $can('cms.media')" :class="ui.panel">
            <div class="flex flex-col gap-1">
                <h2 :class="ui.heading">
                    {{ $t('vue-cms::media.sections.replace') }}
                </h2>
                <p :class="ui.muted">
                    {{ $t('vue-cms::media.help.replace') }}
                </p>
            </div>

            <p v-for="error in replaceErrors" :key="error" :class="ui.error">
                {{ error }}
            </p>

            <div :class="ui.toolbar">
                <input
                    type="file"
                    :accept="replace.accept.join(',')"
                    :aria-label="$t('vue-cms::media.fields.file')"
                    class="text-sm"
                    @change="pick"
                />

                <button
                    type="button"
                    :class="ui.button"
                    :disabled="!replacement || replacing"
                    @click="replaceFile"
                >
                    {{ $t('vue-cms::media.actions.replace') }}
                </button>
            </div>
        </section>
    </div>
</template>
