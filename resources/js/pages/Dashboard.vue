<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head :title="$t('vue-cms::dashboard.title')" />

    <div class="flex flex-col gap-2 p-4">
        <h1 class="text-xl font-semibold tracking-tight">
            {{ $t('vue-cms::dashboard.title') }}
        </h1>
        <p class="text-sm text-muted-foreground">
            {{ $t('vue-cms::dashboard.description') }}
        </p>
    </div>
</template>
