<script setup lang="ts">
import TreeView from '@form-builder/components/TreeView.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useBreadcrumbs } from '@cms/composables/useBreadcrumbs';
import { useErrors } from '@cms/composables/useErrors';
import type { CmsBreadcrumb, CmsPageNode, CmsTreeDrop } from '@cms/types';
import { ui } from '@cms/ui';

const props = defineProps<{
    tree: CmsPageNode[];
    createUrl: string;
    breadcrumbs: CmsBreadcrumb[];
}>();

useBreadcrumbs(() => props.breadcrumbs);

const errors = useErrors(['target', 'position', 'parent_id', 'slug']);

const moveError = ref<string | null>(null);

const expandedIds = computed(() => {
    const ids: number[] = [];
    const collect = (nodes: CmsPageNode[]): void => {
        nodes.forEach((node) => {
            ids.push(node.id);
            collect(node.children);
        });
    };

    collect(props.tree);

    return ids;
});

function asPage(node: unknown): CmsPageNode {
    return node as CmsPageNode;
}

function open(id: string | number | null): void {
    const node = findNode(props.tree, id);

    if (node) {
        router.visit(node.editUrl);
    }
}

function findNode(
    nodes: CmsPageNode[],
    id: string | number | null,
): CmsPageNode | undefined {
    for (const node of nodes) {
        if (node.id === id) {
            return node;
        }

        const child = findNode(node.children, id);

        if (child) {
            return child;
        }
    }

    return undefined;
}

function move(event: CmsTreeDrop): void {
    const dragged = findNode(props.tree, event.dragged.id);

    if (event.target === null || dragged === undefined) {
        return;
    }

    moveError.value = dragged.moveBlockedReason;

    if (dragged.moveBlockedReason !== null) {
        return;
    }

    router.post(
        dragged.moveUrl,
        {
            target: event.target.id,
            position: event.position,
        },
        {
            preserveScroll: true,
        },
    );
}
</script>

<template>
    <Head :title="$t('vue-cms::pages.index.title')" />

    <div class="flex max-w-4xl flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h1 :class="ui.title">
                    {{ $t('vue-cms::pages.index.title') }}
                </h1>
                <p :class="ui.muted">
                    {{ $t('vue-cms::pages.index.description') }}
                </p>
            </div>

            <Link
                v-if="$can('cms.pages.edit')"
                :href="createUrl"
                :class="ui.primaryButton"
            >
                {{ $t('vue-cms::pages.actions.create') }}
            </Link>
        </div>

        <p v-if="!$can('cms.pages.publish')" :class="ui.notice">
            {{ $t('vue-cms::pages.publication.move_requires_publish') }}
        </p>

        <p v-if="moveError" :class="ui.error">{{ moveError }}</p>

        <p v-for="error in errors" :key="error" :class="ui.error">
            {{ error }}
        </p>

        <section :class="ui.panel">
            <p v-if="tree.length === 0" :class="ui.muted">
                {{ $t('vue-cms::pages.index.empty') }}
            </p>

            <TreeView
                v-else
                :nodes="tree"
                :draggable="$can('cms.pages.edit')"
                :expanded-ids="expandedIds"
                @update:model-value="open"
                @drop="move"
            >
                <template #actions="{ node }">
                    <span :class="ui.muted">{{ asPage(node).path }}</span>
                    <span v-if="asPage(node).isHome" :class="ui.badge">
                        {{ $t('vue-cms::pages.index.home') }}
                    </span>
                    <span
                        :class="
                            asPage(node).status === 'published'
                                ? ui.publishedBadge
                                : ui.badge
                        "
                    >
                        {{ asPage(node).statusLabel }}
                    </span>
                    <Link
                        :href="asPage(node).editUrl"
                        class="text-sm font-medium underline-offset-4 hover:underline"
                    >
                        {{ $t('vue-cms::pages.actions.edit') }}
                    </Link>
                </template>
            </TreeView>
        </section>
    </div>
</template>
