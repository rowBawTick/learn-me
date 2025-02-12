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
                            color="primary"
                            :loading="isSending"
                            icon="send"
                            round
                            flat
                            size="md"
                        />
                    </div>
                </q-form>
            </q-card-section>
        </q-card>
    </q-dialog>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useQuasar } from 'quasar'
import { date } from 'quasar'

const props = defineProps({
    isOpen: Boolean,
    recipientId: Number,
    recipientName: String,
    userId: Number,
    userName: String
})

const emit = defineEmits(['update:isOpen'])
const $q = useQuasar()

const messages = ref([])
const newMessage = ref('')
const isSending = ref(false)

const defaultMessage = computed(() => {
    if (!props.recipientName || !props.userName) return ''
    return `Hi ${props.recipientName},\n\nI'm interested in your tutoring services. Would you be available for a lesson?\n\nBest regards,\n${props.userName}`
})

const formatDate = (dateStr) => {
    return date.formatDate(dateStr, 'MMM D, YYYY h:mm A')
}

const getMessageAlignment = (message) => {
    return message.sender_id === props.userId
        ? 'bg-primary text-white ml-auto'
        : 'bg-grey-3 mr-auto'
    ;
}

const getTimestampAlignment = (message) => {
    return message.sender_id === props.userId ? 'text-right' : 'text-left';
}

const loadMessages = async () => {
    try {
        const response = await axios.get(`/api/messages/${props.recipientId}`)
        messages.value = response.data
    } catch (error) {
        console.error('Error loading messages:', error)
        if ($q.notify) {
            $q.notify({
                type: 'negative',
                message: 'Failed to load messages'
            })
        }
    }
}

const sendMessage = async () => {
    if (!newMessage.value.trim()) {
        if ($q.notify) {
            $q.notify({
                type: 'warning',
                message: 'Please enter a message'
            })
        }
        return
    }

    isSending.value = true
    try {
        const response = await axios.post(`/api/messages/${props.recipientId}`, {
            message: newMessage.value.trim()
        })
        messages.value.push(response.data);
        newMessage.value = '';
    } catch (error) {
        console.error('Error sending message:', error)
        if ($q.notify) {
            $q.notify({
                type: 'negative',
                message: 'Failed to send message'
            })
        }
    } finally {
        isSending.value = false
    }
}

watch(() => props.isOpen, (newVal) => {
    if (newVal) {
        loadMessages().then(() => {
            // Only set default message if there are no existing messages
            if (messages.value.length === 0) {
                newMessage.value = defaultMessage.value
            } else {
                newMessage.value = ''
            }
        })
    }
})

onMounted(() => {
    if (props.isOpen) {
        loadMessages().then(() => {
            // Only set default message if there are no existing messages
            if (messages.value.length === 0) {
                newMessage.value = defaultMessage.value
            } else {
                newMessage.value = ''
            }
        })
    }
})
</script>

<style scoped>
.messages-container {
    scrollbar-width: thin;
    scrollbar-color: #e0e0e0 transparent;
}

.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    background: transparent;
}

.messages-container::-webkit-scrollbar-thumb {
    background-color: #e0e0e0;
    border-radius: 3px;
}

.message-bubble {
    white-space: pre-wrap;
    word-wrap: break-word;
    line-height: 1.4;
}
</style>
