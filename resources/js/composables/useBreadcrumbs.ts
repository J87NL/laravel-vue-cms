import { setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import type { CmsBreadcrumb } from '@cms/types';

export function useBreadcrumbs(breadcrumbs: () => CmsBreadcrumb[]): void {
    watchEffect(() => {
        setLayoutProps({
            breadcrumbs: breadcrumbs(),
        });
    });
}
