<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import type {
    CmsBlockDiff,
    CmsRevisionDiff,
    CmsValueDiff,
    CmsWordSegment,
} from '@cms/types';
import { ui } from '@cms/ui';

defineProps<{
    diff: CmsRevisionDiff;
}>();

const statusClasses: Record<CmsBlockDiff['status'], string> = {
    added: 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200',
    removed: 'bg-red-50 text-red-800 dark:bg-red-950 dark:text-red-200',
    changed: 'bg-amber-50 text-amber-900 dark:bg-amber-950 dark:text-amber-100',
    unchanged:
        'border border-gray-300 text-gray-700 dark:border-gray-600 dark:text-gray-200',
};

const segmentClasses: Record<CmsWordSegment['type'], string> = {
    added: 'rounded-sm bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200',
    removed:
        'rounded-sm bg-red-50 text-red-800 line-through dark:bg-red-950 dark:text-red-200',
    equal: '',
};

function shown(value: string | null): string {
    return value ?? trans('vue-cms::revisions.diff.empty');
}

function yesNo(value: boolean | null): string {
    return trans(
        value ? 'vue-cms::revisions.diff.yes' : 'vue-cms::revisions.diff.no',
    );
}

function visibilityChanged(block: CmsBlockDiff): boolean {
    return (
        block.status === 'changed' &&
        block.visible.before !== block.visible.after
    );
}

function isQuiet(block: CmsBlockDiff): boolean {
    return block.status === 'unchanged' && !block.moved;
}

function shownLinks(links: string[]): string[] {
    return links.length > 0
        ? links
        : [trans('vue-cms::revisions.diff.no_links')];
}

function hasWords(change: CmsValueDiff): boolean {
    return change.words !== null && change.words.length > 0;
}
</script>

<template>
    <div class="flex flex-col gap-4" data-test="revision-diff">
        <div v-if="diff.fields.length > 0" class="flex flex-col gap-2">
            <h3 class="text-sm font-medium">
                {{ $t('vue-cms::revisions.diff.fields') }}
            </h3>
            <dl class="flex flex-col gap-2 text-sm">
                <div
                    v-for="field in diff.fields"
                    :key="field.key"
                    class="flex flex-col gap-0.5"
                    :data-test="`diff-field-${field.key}`"
                >
                    <dt class="font-medium">{{ field.label }}</dt>
                    <dd v-if="hasWords(field)" class="break-words">
                        <span
                            v-for="(segment, index) in field.words ?? []"
                            :key="index"
                            :class="segmentClasses[segment.type]"
                            >{{ segment.text }}</span
                        >
                    </dd>
                    <dd v-else class="flex flex-col gap-0.5 break-words">
                        <span :class="segmentClasses.removed">
                            {{ shown(field.before) }}
                        </span>
                        <span :class="segmentClasses.added">
                            {{ shown(field.after) }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="flex flex-col gap-2">
            <h3 class="text-sm font-medium">
                {{ $t('vue-cms::revisions.diff.blocks') }}
            </h3>
            <ol class="flex flex-col gap-2 text-sm">
                <li
                    v-for="block in diff.blocks"
                    :key="`${block.status}-${block.id}`"
                    class="flex flex-col gap-1 rounded-md border border-gray-200 p-2 dark:border-gray-700"
                    :class="{ 'opacity-60': isQuiet(block) }"
                    :data-test="`diff-block-${block.status}`"
                >
                    <div :class="ui.toolbar">
                        <span
                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                            :class="statusClasses[block.status]"
                        >
                            {{
                                $t(
                                    `vue-cms::revisions.diff.status.${block.status}`,
                                )
                            }}
                        </span>
                        <span class="font-medium">{{ block.label }}</span>
                        <span
                            v-if="block.moved"
                            :class="ui.muted"
                            data-test="diff-block-moved"
                        >
                            {{
                                $t('vue-cms::revisions.diff.moved', {
                                    before: String(block.position.before),
                                    after: String(block.position.after),
                                })
                            }}
                        </span>
                        <span v-if="visibilityChanged(block)" :class="ui.muted">
                            {{
                                $t('vue-cms::revisions.diff.visibility', {
                                    before: yesNo(block.visible.before),
                                    after: yesNo(block.visible.after),
                                })
                            }}
                        </span>
                    </div>

                    <dl
                        v-if="block.changes.length > 0"
                        class="flex flex-col gap-1"
                    >
                        <div
                            v-for="change in block.changes"
                            :key="change.key"
                            class="flex flex-col gap-0.5"
                        >
                            <dt :class="ui.muted">{{ change.label }}</dt>
                            <dd v-if="hasWords(change)" class="break-words">
                                <span
                                    v-for="(segment, index) in change.words ??
                                    []"
                                    :key="index"
                                    :class="segmentClasses[segment.type]"
                                    >{{ segment.text }}</span
                                >
                            </dd>
                            <dd
                                v-else-if="!change.markupChanged"
                                class="flex flex-col gap-0.5 break-words"
                            >
                                <span
                                    v-if="block.status !== 'added'"
                                    :class="segmentClasses.removed"
                                >
                                    {{ shown(change.before) }}
                                </span>
                                <span
                                    v-if="block.status !== 'removed'"
                                    :class="segmentClasses.added"
                                >
                                    {{ shown(change.after) }}
                                </span>
                            </dd>
                            <dd
                                v-if="change.markupChanged"
                                :class="ui.notice"
                                data-test="diff-markup-changed"
                            >
                                {{
                                    $t('vue-cms::revisions.diff.markup_changed')
                                }}
                            </dd>
                            <dd
                                v-if="change.links"
                                class="flex flex-col gap-0.5 break-all"
                                data-test="diff-links"
                            >
                                <span :class="ui.muted">
                                    {{ $t('vue-cms::revisions.diff.links') }}
                                </span>
                                <ul
                                    v-if="block.status !== 'added'"
                                    class="flex flex-col gap-0.5"
                                >
                                    <li
                                        v-for="(link, index) in shownLinks(
                                            change.links.before,
                                        )"
                                        :key="index"
                                        :class="segmentClasses.removed"
                                    >
                                        {{ link }}
                                    </li>
                                </ul>
                                <ul
                                    v-if="block.status !== 'removed'"
                                    class="flex flex-col gap-0.5"
                                >
                                    <li
                                        v-for="(link, index) in shownLinks(
                                            change.links.after,
                                        )"
                                        :key="index"
                                        :class="segmentClasses.added"
                                    >
                                        {{ link }}
                                    </li>
                                </ul>
                            </dd>
                        </div>
                    </dl>
                </li>
            </ol>
        </div>
    </div>
</template>
