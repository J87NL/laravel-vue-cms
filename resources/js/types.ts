export type CmsBreadcrumb = {
    title: string;
    href: string;
};

export type CmsPageNode = {
    id: number;
    label: string;
    path: string;
    status: string;
    statusLabel: string;
    isHome: boolean;
    editUrl: string;
    moveUrl: string;
    moveBlockedReason: string | null;
    children: CmsPageNode[];
};

export type CmsTreeDrop = {
    dragged: { id: string | number };
    target: { id: string | number } | null;
    position: 'before' | 'after' | 'inside';
};

export type CmsWordSegment = {
    type: 'equal' | 'added' | 'removed';
    text: string;
};

export type CmsValueDiff = {
    key: string;
    label: string;
    before: string | null;
    after: string | null;
    words: CmsWordSegment[] | null;
};

export type CmsBlockDiff = {
    id: number;
    label: string;
    status: 'added' | 'removed' | 'changed' | 'unchanged';
    moved: boolean;
    position: {
        before: number | null;
        after: number | null;
    };
    visible: {
        before: boolean | null;
        after: boolean | null;
    };
    changes: CmsValueDiff[];
};

export type CmsRevisionDiff = {
    fields: CmsValueDiff[];
    blocks: CmsBlockDiff[];
};
