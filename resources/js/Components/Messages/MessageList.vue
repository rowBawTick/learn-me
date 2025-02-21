<template>
    <div class="messages-container h-full overflow-y-auto" ref="messagesContainer">
        <template v-if="messages.length">
            <div v-for="message in messages"
                 :key="message.id"
                 class="mb-3">
                <div class="flex px-2" :class="getMessageAlignment(message, userId)">
                    <div class="max-w-[75%] w-auto">
                        <div class="message-bubble rounded-lg p-3 shadow-sm break-words whitespace-pre-wrap"
                             :class="getMessageStyle(message, userId)">
                            {{ message.message }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1"
                             :class="getTimestampAlignment(message, userId)">
                            {{ formatDate(message.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div v-else class="flex items-center justify-center h-full text-gray-500">
            No messages yet
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useMessageFormatter } from '@/Composables/useMessageFormatter'

const props = defineProps({
    messages: {
        type: Array,
        required: true
    },
    userId: {
        type: Number,
        required: true
    },
    autoScroll: {
        type: Boolean,
        default: true
    }
})

const messagesContainer = ref(null)
const { formatDate, getMessageAlignment, getMessageStyle, getTimestampAlignment } = useMessageFormatter()

const scrollToBottom = () => {
    if (props.autoScroll && messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
}

// Auto-scroll when new messages are added
watch(() => props.messages, scrollToBottom, { deep: true })

// Initial scroll on mount
onMounted(scrollToBottom)
</script>

<style scoped>
.messages-container {
    scrollbar-width: thin;
    scrollbar-color: #CBD5E0 #EDF2F7;
}

.messages-container::-webkit-scrollbar {
    width: 8px;
}

.messages-container::-webkit-scrollbar-track {
    background: #EDF2F7;
}

.messages-container::-webkit-scrollbar-thumb {
    background-color: #CBD5E0;
    border-radius: 4px;
}
</style>
