<template>
    <MainLayout>
        <div class="flex flex-col gap-4 w-full h-screen overflow-hidden bg-gray-50 p-2 sm:p-4">
            <div class="flex-none h-[40vh] bg-white w-full overflow-hidden rounded-lg">
                <SelectedConversation
                    :conversation="selectedConversation"
                    :user-id="$page.props.auth.user.id"
                    @message-sent="handleMessageSent"
                    class="h-full"
                />
            </div>

            <div class="flex-1 h-[40vh] overflow-y-auto">
                <ConversationList
                    :conversations="conversations"
                    :selected-id="selectedConversation?.participant_id"
                    @select="selectConversation"
                />
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
