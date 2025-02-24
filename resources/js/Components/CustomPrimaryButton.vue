<script setup>
defineProps({
    icon: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <q-btn
        color="primary"
        :icon="icon"
        :label="label"
        class="custom-primary-btn font-medium rounded-lg"
    >
        <slot />
    </q-btn>
</template>

<style>
.custom-primary-btn {
    @apply bg-primary hover:bg-primaryDark !important;
}
</style>
