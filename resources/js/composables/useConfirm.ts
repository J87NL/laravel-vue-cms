import { inject } from 'vue';

export type CmsConfirm = (message: string) => Promise<boolean>;

const decline: CmsConfirm = () => Promise.resolve(false);

export function useConfirm(): CmsConfirm {
    return inject<CmsConfirm>('confirm', decline);
}
