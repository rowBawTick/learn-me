<template>
    <div class="h-full flex flex-col">
        <div class="p-3 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Messages</h2>
        </div>
        <div class="flex-1 overflow-y-auto">
            <div v-for="conversation in conversations"
                 :key="conversation.id"
                 @click="$emit('select', conversation)"
                 class="p-3 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors"
                 :class="{'bg-gray-50': selectedId === conversation.id}">
                <div class="flex justify-between items-start gap-2">
                    <div class="text-sm font-semibold truncate flex-1">
                        {{ getOtherParticipantName(conversation) }}
                        <span v-if="conversation.unread_count" 
                              class="ml-2 px-2 py-0.5 bg-primary text-white text-xs rounded-full">
                            {{ conversation.unread_count }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 whitespace-nowrap">
                        {{ formatDate(conversation.last_message?.created_at) }}
                    </div>
                </div>
                <div class="text-xs text-gray-600 truncate pr-2 mt-1">
                    <span v-if="conversation.last_message?.sender_id === userId" class="text-gray-500">You: </span>
                    {{ conversation.last_message?.message }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    conversations: {
        type: Array,
        required: true
    },
    selectedId: {
        type: Number,
        default: null
    }
})

defineEmits(['select'])

const userId = usePage().props.auth.user.id

const getOtherParticipantName = (conversation) => {
    return conversation.participants.find(p => p.id !== userId)?.name || 'Unknown'
}

const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    })
}
</script>
