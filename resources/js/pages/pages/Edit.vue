<script setup lang="ts">
import Form from '@form-builder/components/Form.vue';
import type { FormSchema } from '@form-builder/types/form-builder';
import { Head, Link, router } from '@inertiajs/vue3';
import TableBuilder from '@table-builder/components/TableBuilder.vue';
import type { TableData } from '@table-builder/types/table-builder';
import { trans } from 'laravel-vue-i18n';
import { useBreadcrumbs } from '@cms/composables/useBreadcrumbs';
import { useConfirm } from '@cms/composables/useConfirm';
import { useErrors } from '@cms/composables/useErrors';
import type { CmsBreadcrumb } from '@cms/types';
import { ui } from '@cms/ui';

const props = defineProps<{
    page: {
        id: number;
        title: string;
        path: string;
        url: string;
        status: string;
        statusLabel: string;
        isPublished: boolean;
        isHome: boolean;
    };
    breadcrumbs: CmsBreadcrumb[];
    form: FormSchema;
    blocks: TableData;
    blockTypes: {
        key: string;
        label: string;
        createUrl: string;
    }[];
    revisions: TableData;
    revisionsDescription: string;
    publication: {
        canPublish: boolean;
        publishUrl: string;
        unpublishUrl: string;
        homeUrl: string;
        homeBlockedReason: string | null;
        unpublishBlockedReason: string | null;
        publishAt: string | null;
        unpublishAt: string | null;
        scheduleError: string | null;
        scheduleForm: FormSchema | null;
    };
    previewUrl: string;
    write: {
        blockedReason: string | null;
    };
    deletion: {
        url: string;
        blockedReason: string | null;
    };
}>();

useBreadcrumbs(() => props.breadcrumbs);

const errors = useErrors(['page']);

const confirm = useConfirm();

function moment(value: string): string {
    return new Date(value).toLocaleString(undefined, {
        dateStyle: 'long',
        timeStyle: 'short',
    });
}

function post(url: string): void {
    router.post(url, {}, { preserveScroll: true });
}

async function destroy(): Promise<void> {
    if (!(await confirm(trans('vue-cms::pages.actions.delete_confirm')))) {
        return;
    }

    router.delete(props.deletion.url);
}
</script>

<template>
    <Head :title="page.title" />

    <div class="flex max-w-5xl flex-col gap-6 p-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h1 :class="ui.title">{{ page.title }}</h1>
                <div :class="ui.toolbar">
                    <span
                        :class="page.isPublished ? ui.publishedBadge : ui.badge"
                    >
                        {{ page.statusLabel }}
                    </span>
                    <span v-if="page.isHome" :class="ui.badge">
                        {{ $t('vue-cms::pages.index.home') }}
                    </span>
                    <span :class="ui.muted">{{ page.path }}</span>
                </div>
            </div>

            <div :class="ui.toolbar">
                <a
                    v-if="page.isPublished"
                    :href="page.url"
                    target="_blank"
                    rel="noopener"
                    :class="ui.button"
                >
                    {{ $t('vue-cms::pages.edit.view') }}
                </a>
                <a
                    :href="previewUrl"
                    target="_blank"
                    rel="noopener"
                    :class="ui.button"
                    data-test="preview"
                >
                    {{ $t('vue-cms::pages.edit.preview') }}
                </a>
                <Link :href="breadcrumbs[0]?.href ?? '/'" :class="ui.button">
                    {{ $t('vue-cms::common.back') }}
                </Link>
            </div>
        </div>

        <p v-for="error in errors" :key="error" :class="ui.error">
            {{ error }}
        </p>

        <section :class="ui.panel" data-test="publication">
            <h2 :class="ui.heading">
                {{ $t('vue-cms::pages.publication.title') }}
            </h2>

            <p
                v-if="!publication.canPublish || !$can('cms.pages.publish')"
                :class="ui.notice"
            >
                {{ $t('vue-cms::pages.publication.requires_permission') }}
            </p>

            <div :class="ui.toolbar">
                <button
                    v-if="!page.isPublished"
                    type="button"
                    :class="ui.primaryButton"
                    :disabled="
                        !publication.canPublish || !$can('cms.pages.publish')
                    "
                    :title="
                        publication.canPublish
                            ? undefined
                            : $t(
                                  'vue-cms::pages.publication.requires_permission',
                              )
                    "
                    data-test="publish"
                    @click="post(publication.publishUrl)"
                >
                    {{ $t('vue-cms::pages.actions.publish') }}
                </button>
                <button
                    v-else
                    type="button"
                    :class="ui.button"
                    :disabled="
                        !publication.canPublish ||
                        !$can('cms.pages.publish') ||
                        publication.unpublishBlockedReason !== null
                    "
                    :title="
                        publication.unpublishBlockedReason ??
                        (publication.canPublish
                            ? undefined
                            : $t(
                                  'vue-cms::pages.publication.requires_permission',
                              ))
                    "
                    data-test="unpublish"
                    @click="post(publication.unpublishUrl)"
                >
                    {{ $t('vue-cms::pages.actions.unpublish') }}
                </button>
                <button
                    v-if="!page.isHome"
                    type="button"
                    :class="ui.button"
                    :disabled="
                        !publication.canPublish ||
                        !$can('cms.pages.publish') ||
                        publication.homeBlockedReason !== null
                    "
                    :title="
                        publication.homeBlockedReason ??
                        (publication.canPublish
                            ? undefined
                            : $t(
                                  'vue-cms::pages.publication.requires_permission',
                              ))
                    "
                    data-test="make-home"
                    @click="post(publication.homeUrl)"
                >
                    {{ $t('vue-cms::pages.actions.make_home') }}
                </button>
                <span v-else :class="ui.muted">
                    {{ $t('vue-cms::pages.publication.is_home') }}
                </span>
            </div>

            <p
                v-if="!page.isHome && publication.homeBlockedReason"
                :class="ui.muted"
                data-test="home-blocked"
            >
                {{ publication.homeBlockedReason }}
            </p>
            <p
                v-if="page.isPublished && publication.unpublishBlockedReason"
                :class="ui.muted"
                data-test="unpublish-blocked"
            >
                {{ publication.unpublishBlockedReason }}
            </p>

            <p
                v-if="publication.publishAt"
                :class="ui.muted"
                data-test="publish-at"
            >
                {{
                    $t('vue-cms::pages.publication.publish_at', {
                        moment: moment(publication.publishAt),
                    })
                }}
            </p>
            <p
                v-if="publication.unpublishAt"
                :class="ui.muted"
                data-test="unpublish-at"
            >
                {{
                    $t('vue-cms::pages.publication.unpublish_at', {
                        moment: moment(publication.unpublishAt),
                    })
                }}
            </p>

            <p
                v-if="publication.scheduleError"
                :class="ui.notice"
                data-test="schedule-error"
            >
                {{
                    $t('vue-cms::pages.publication.schedule_error', {
                        reason: publication.scheduleError,
                    })
                }}
            </p>

            <div
                v-if="publication.scheduleForm && $can('cms.pages.publish')"
                class="flex flex-col gap-2 border-t border-gray-200 pt-4 dark:border-gray-700"
                data-test="schedule"
            >
                <h3 class="text-sm font-medium">
                    {{ $t('vue-cms::pages.publication.schedule') }}
                </h3>
                <Form :schema="publication.scheduleForm" />
            </div>
        </section>

        <p
            v-if="write.blockedReason"
            :class="ui.notice"
            data-test="write-blocked"
        >
            {{ write.blockedReason }}
        </p>

        <section :class="ui.panel">
            <Form :schema="form" />
        </section>

        <section :class="ui.panel">
            <div class="flex flex-col gap-1">
                <h2 :class="ui.heading">
                    {{ $t('vue-cms::pages.edit.blocks') }}
                </h2>
                <p :class="ui.muted">
                    {{ $t('vue-cms::pages.edit.blocks_description') }}
                </p>
            </div>

            <TableBuilder :table="blocks" :confirm="confirm" />

            <div class="flex flex-col gap-2">
                <span class="text-sm font-medium">
                    {{ $t('vue-cms::pages.edit.add_block') }}
                </span>
                <div :class="ui.toolbar">
                    <template v-for="type in blockTypes" :key="type.key">
                        <button
                            v-if="write.blockedReason"
                            type="button"
                            :class="ui.button"
                            :title="write.blockedReason"
                            disabled
                        >
                            + {{ type.label }}
                        </button>
                        <Link
                            v-else
                            :href="type.createUrl"
                            :class="ui.button"
                            :data-test="`add-block-${type.key}`"
                        >
                            + {{ type.label }}
                        </Link>
                    </template>
                </div>
            </div>
        </section>

        <section :class="ui.panel">
            <div class="flex flex-col gap-1">
                <h2 :class="ui.heading">
                    {{ $t('vue-cms::pages.edit.revisions') }}
                </h2>
                <p :class="ui.muted">
                    {{ revisionsDescription }}
                </p>
            </div>

            <TableBuilder :table="revisions" :confirm="confirm" />
        </section>

        <section :class="ui.panel">
            <div class="flex flex-col gap-1">
                <h2 :class="ui.heading">
                    {{ $t('vue-cms::pages.edit.danger') }}
                </h2>
                <p :class="ui.muted">
                    {{ $t('vue-cms::pages.edit.danger_description') }}
                </p>
            </div>

            <p v-if="deletion.blockedReason" :class="ui.notice">
                {{ deletion.blockedReason }}
            </p>

            <div>
                <button
                    type="button"
                    :class="ui.dangerButton"
                    :disabled="deletion.blockedReason !== null"
                    data-test="delete-page"
                    @click="destroy"
                >
                    {{ $t('vue-cms::pages.actions.delete') }}
                </button>
            </div>
        </section>
    </div>
</template>
