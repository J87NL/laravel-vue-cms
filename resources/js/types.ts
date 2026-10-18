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
