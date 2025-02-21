<template>
    <form @submit.prevent="handleSubmit" class="flex items-end gap-2">
        <q-input
            v-model="localMessage"
            type="textarea"
            class="flex-grow"
            filled
            autogrow
            square
            bg-color="white"
            placeholder="Type your message here..."
            :max-height="150"
            rows="2"
            @keydown.enter.exact.prevent="handleSubmit"
            @keydown.shift.enter.exact="handleNewLine"
        />
        <q-btn
            type="submit"
            :loading="isLoading"
            :disable="!localMessage.trim()"
            color="primary"
            icon="send"
            round
        />
    </form>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue', 'send-message'])

const localMessage = ref(props.modelValue);

watch(() => props.modelValue, (newVal) => {
    if (newVal !== localMessage.value) {
        localMessage.value = newVal;
    }
})

watch(localMessage, (newVal) => {
    emit('update:modelValue', newVal)
})

const handleSubmit = () => {
    if (!localMessage.value.trim() || props.isLoading) return
    emit('send-message', localMessage.value.trim())
    localMessage.value = ''
}

const handleNewLine = (event) => {
    // Insert a newline character at the cursor position
    const target = event.target
    const start = target.selectionStart
    const end = target.selectionEnd
    const value = localMessage.value
    localMessage.value = value.substring(0, start) + '\n' + value.substring(end)

    // Move cursor after the inserted newline
    nextTick(() => {
        target.selectionStart = target.selectionEnd = start + 1
    })
}
</script>
