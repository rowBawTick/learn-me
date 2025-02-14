<template>
    <q-dialog :model-value="isOpen" @update:model-value="$emit('update:isOpen', $event)" position="right">
        <q-card class="w-full max-w-md h-[80vh] flex flex-col">
            <!-- Header -->
            <q-card-section class="row items-center q-pb-sm bg-grey-2">
                <div class="text-h6">Message {{ recipientName }}</div>
                <q-space />
                <q-btn icon="close" flat round dense v-close-popup />
            </q-card-section>

            <q-separator />

            <!-- Messages Area -->
            <q-card-section class="col flex-grow q-pa-md overflow-hidden">
                <div class="messages-container h-full overflow-y-auto">
                    <template v-if="messages.length">
                        <div v-for="message in messages" :key="message.id" class="q-mb-md">
                            <div
                                class="message-bubble rounded-lg p-3 max-w-[80%] shadow-sm"
                                :class="getMessageAlignment(message)"
                            >
                                {{ message.message }}
                            </div>
                            <div class="text-xs text-grey-6 mt-1" :class="getTimestampAlignment(message)">
                                {{ formatDate(message.created_at) }}
                            </div>
                        </div>
                    </template>
                    <div v-else class="text-grey-6 text-center">
                        No messages yet
                    </div>
                </div>
            </q-card-section>

            <!-- Input Area -->
            <q-card-section class="q-px-md q-pb-md">
                <q-separator class="q-mb-md" />
                <q-form @submit.prevent="sendMessage">
                    <div class="flex items-end gap-2">
                        <q-input
                            v-model="newMessage"
                            type="textarea"
                            rows="3"
                            class="flex-grow"
                            filled
                            autogrow
                            square
                            bg-color="white"
                            placeholder="Type your message here..."
                            :max-height="150"
                        />
                        <q-btn
                            type="submit"
                            :loading="isSending"
                            :disable="!newMessage.trim()"
                            color="primary"
                            icon="send"
                            round
                        />
                    </div>
                </q-form>
            </q-card-section>
        </q-card>
    </q-dialog>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true
    },
    recipientId: {
        type: Number,
        required: true
    },
    recipientName: {
        type: String,
        required: true
    }
})

const emit = defineEmits(['update:isOpen'])

// State management
const page = usePage()
const messages = ref([])
const newMessage = ref('')
const isSending = ref(false)
const conversation = ref(null)

// Computed properties
const userId = computed(() => page.props.auth.user.id)
const authUser = computed(() => page.props.auth.user)
const defaultMessage = computed(() => {
    if (!props.recipientName || !authUser.value?.name) return ''

    return `Hi ${props.recipientName},

I'm interested in your tutoring services. Would you be available for a lesson?

Best regards,
${authUser.value.name}`
})

// Message styling utilities
const getMessageAlignment = (message) => ({
    'bg-primary text-white ml-auto': message.sender_id === userId.value,
    'bg-grey-2 mr-auto': message.sender_id !== userId.value
})

const getTimestampAlignment = (message) => ({
    'text-right': message.sender_id === userId.value,
    'text-left': message.sender_id !== userId.value
})

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

// API interactions
const loadMessages = async () => {
    try {
        const response = await axios.get(`/api/messages/${props.recipientId}`)
        conversation.value = response.data.conversation
        messages.value = response.data.messages || []

        // Only set default message if this is a new conversation with no messages
        if (!messages.value.length) {
            newMessage.value = defaultMessage.value
        }
    } catch (error) {
        console.error('Error loading messages:', error)
    }
}

const sendMessage = async () => {
    if (!newMessage.value.trim()) return

    isSending.value = true
    try {
        const response = await axios.post('/api/messages/send', {
            recipient_id: props.recipientId,
            message: newMessage.value.trim()
        })

        messages.value.push(response.data.message)
        newMessage.value = ''
    } catch (error) {
        console.error('Error sending message:', error)
    } finally {
        isSending.value = false
    }
}

// Lifecycle hooks
watch(() => props.isOpen, (newVal) => {
    if (newVal) {
        loadMessages()
    } else {
        // Clear messages when dialog closes
        messages.value = []
        newMessage.value = ''
        conversation.value = null
    }
})
</script>

<style scoped>
.messages-container {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    background: transparent;
}

.messages-container::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}

.message-bubble {
    white-space: pre-wrap;
    word-wrap: break-word;
}
</style>
