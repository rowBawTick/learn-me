<template>
    <div class="h-full flex flex-col bg-white rounded-lg shadow-lg overflow-hidden">
        <template v-if="conversation">
            <!-- Conversation Header -->
            <div class="p-3 border-b border-gray-200 bg-white flex-none">
                <h3 class="text-lg font-semibold truncate">{{ getOtherParticipantName(conversation) }}</h3>
            </div>

            <!-- Messages - Scrollable area -->
            <div class="flex-1 overflow-y-auto p-3 min-h-0" ref="messagesContainer">
                <template v-if="messages.length">
                    <div v-for="message in messages"
                         :key="message.id"
                         class="mb-3">
                        <div class="flex" :class="message.sender_id === userId ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[75%] w-auto">
                                <div class="message-bubble rounded-lg p-3 shadow-sm break-words"
                                     :class="message.sender_id === userId ?
                                        'bg-primary text-white' :
                                        'bg-gray-100'">
                                    {{ message.message }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1"
                                     :class="message.sender_id === userId ? 'text-right' : 'text-left'">
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

            <!-- Message Input - Fixed at bottom -->
            <div class="p-3 border-t border-gray-200 bg-white flex-none w-full">
                <form @submit.prevent="sendMessage" class="flex items-end gap-2">
                    <q-input
                        v-model="newMessage"
                        type="textarea"
                        class="flex-grow"
                        filled
                        autogrow
                        square
                        bg-color="white"
                        placeholder="Type your message here..."
                        :max-height="150"
                        rows="2"
                    />
                    <q-btn
                        type="submit"
                        :loading="sending"
                        :disable="!newMessage.trim()"
                        color="primary"
                        icon="send"
                        round
                    />
                </form>
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

const props = defineProps({
    conversation: {
        type: Object,
        default: null
    }
})

const userId = usePage().props.auth.user.id
const messages = ref([])
const newMessage = ref('')
const sending = ref(false)
const messagesContainer = ref(null)

const getOtherParticipantName = (conversation) => {
    return conversation.participants.find(p => p.id !== userId)?.name || 'Unknown'
}

const formatDate = (dateStr) => {
    const date = new Date(dateStr)
    return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    })
}

const scrollToBottom = () => {
    if (messagesContainer.value) {
        setTimeout(() => {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
        }, 50)
    }
}

const loadMessages = async () => {
    if (!props.conversation?.id) return

    try {
        const response = await axios.get(`/api/conversation/${props.conversation.id}`)
        messages.value = response.data.messages
        scrollToBottom()
    } catch (error) {
        console.error('Error loading messages:', error)
    }
}

const sendMessage = async () => {
    if (!newMessage.value.trim() || !props.conversation?.id) return

    sending.value = true
    try {
        const response = await axios.post('/api/conversation/message/send', {
            recipient_id: props.conversation.participants.find(p => p.id !== userId)?.id,
            message: newMessage.value.trim()
        })

        messages.value.push(response.data.message)
        newMessage.value = ''
        scrollToBottom()
    } catch (error) {
        console.error('Error sending message:', error)
    } finally {
        sending.value = false
    }
}

// Auto-scroll when new messages are added
watch(messages, scrollToBottom)

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
