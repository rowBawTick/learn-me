<template>
    <MainLayout>
        <div class="flex h-[90vh] md:mt-5 bg-gray-50 p-2 sm:p-4 w-full overflow-x-hidden">
            <div class="flex flex-col md:flex-row flex-1 gap-4 min-h-0 w-full overflow-x-hidden">
                <!-- Conversation List - Left on desktop, bottom on mobile -->
                <div class="order-2 md:order-1 flex-none md:w-1/3 h-[40vh] md:h-[90vh] bg-white rounded-lg overflow-hidden">
                    <ConversationList
                        :conversations="conversations"
                        :selected-id="selectedConversation?.id"
                        @select="selectConversation"
                    />
                </div>

                <!-- Selected Conversation - Right on desktop, top on mobile -->
                <div class="order-1 md:order-2 flex-1 h-[40vh] md:w-2/3 md:h-[90vh] bg-white rounded-lg overflow-hidden">
                    <SelectedConversation
                        :conversation="selectedConversation"
                        :user-id="$page.props.auth.user.id"
                        @message-sent="handleMessageSent"
                    />
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import MainLayout from '@/Layouts/MainLayout.vue'
import ConversationList from './Components/ConversationList.vue'
import SelectedConversation from './Components/SelectedConversation.vue'

const props = defineProps({
    conversations: {
        type: Array,
        required: true
    }
})

const selectedConversation = ref(null)

const selectConversation = (conversation) => {
    selectedConversation.value = conversation
}

const handleMessageSent = (message) => {
    // Update the conversations list
    const conversationIndex = props.conversations.findIndex(
        c => c.participant_id === message.recipient_id
    )

    if (conversationIndex !== -1) {
        const conversation = props.conversations[conversationIndex]
        conversation.last_message = message.message
        conversation.last_message_at = message.created_at
        conversation.is_sender = true

        // Move this conversation to the top
        props.conversations.splice(conversationIndex, 1)
        props.conversations.unshift(conversation)
    }
}
</script>
