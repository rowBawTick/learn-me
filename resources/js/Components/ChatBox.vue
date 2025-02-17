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
                <MessageList
                    :messages="messages"
                    :user-id="userId"
                    :auto-scroll="true"
                />
            </q-card-section>

            <!-- Input Area -->
            <q-card-section class="q-px-md q-pb-md">
                <q-separator class="q-mb-md" />
                <q-form @submit.prevent="sendDirectMessage">
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
import MessageList from '@/Components/Messages/MessageList.vue'

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
const userId = computed(() => page.props.auth.user.id)
const authUser = computed(() => page.props.auth.user)

// Computed properties
const defaultMessage = computed(() => {
    if (!props.recipientName || !authUser.value?.name) return ''

    const message = `
        Hi ${props.recipientName},
        I'm interested in your tutoring services. Would you be available for a lesson?
        Best regards,
        ${authUser.value.name}
        `;

    return message;
})

// API interactions
const loadDirectMessages = async () => {
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

const sendDirectMessage = async () => {
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
        loadDirectMessages()
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
