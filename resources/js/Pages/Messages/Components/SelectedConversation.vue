<template>
    <div class="h-full flex flex-col bg-white rounded-lg shadow-lg overflow-hidden">
        <template v-if="conversation">
            <!-- Conversation Header -->
            <div class="p-3 border-b border-gray-200 bg-white flex-none">
                <h3 class="text-lg font-semibold truncate">{{ getOtherParticipantName(conversation) }}</h3>
            </div>

            <!-- Messages - Scrollable area -->
            <div class="flex-1 min-h-0">
                <MessageList
                    :messages="messages"
                    :user-id="userId"
                    :auto-scroll="true"
                />
            </div>

            <!-- Message Input - Fixed at bottom -->
            <div class="p-3 border-t border-gray-200 bg-white flex-none w-full">
                <MessageInput
                    v-model="newMessage"
                    :isLoading="sending"
                    @send-message="sendMessage"
                />
            </div>
        </template>
        <div v-else class="flex items-center justify-center h-full text-gray-500">
            Select a conversation to start messaging
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import MessageList from '@/Components/Messages/MessageList.vue'
import MessageInput from '@/Components/Messages/MessageInput.vue'

const props = defineProps({
    conversation: {
        type: Object,
        required: true
    }
})

const userId = usePage().props.auth.user.id
const messages = ref([])
const newMessage = ref('')
const sending = ref(false)

const getOtherParticipantName = (conversation) => {
    return conversation.participants.find(p => p.id !== userId)?.name || 'Unknown'
}

const loadMessages = async () => {
    if (!props.conversation?.id) return

    try {
        const response = await axios.get(`/api/conversation/${props.conversation.id}`)
        messages.value = response.data.messages
    } catch (error) {
        console.error('Error loading messages:', error)
    }
}

const sendMessage = async () => {
    if (!newMessage.value.trim() || !props.conversation?.id) return

    sending.value = true
    try {
        const response = await axios.post(`/api/conversation/${props.conversation.id}/message`, {
            message: newMessage.value.trim()
        })

        messages.value.push(response.data.message)
        newMessage.value = ''
    } catch (error) {
        console.error('Error sending message:', error)
    } finally {
        sending.value = false
    }
}

// Load messages when conversation changes
watch(() => props.conversation?.id, loadMessages, { immediate: true })

onMounted(() => {
    if (props.conversation) {
        loadMessages()
    }
})
</script>

<style scoped>
.message-bubble {
    white-space: pre-wrap;
}
</style>
