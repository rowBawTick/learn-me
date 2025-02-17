export function useMessageFormatter() {
    const formatDate = (dateStr) => {
        const date = new Date(dateStr)
        const now = new Date()
        const yesterday = new Date(now)
        yesterday.setDate(yesterday.getDate() - 1)
        
        // Check if date is today
        if (date.toDateString() === now.toDateString()) {
            return date.toLocaleTimeString('en-US', { 
                hour: 'numeric',
                minute: '2-digit',
                hour12: true 
            })
        }
        
        // Check if date is yesterday
        if (date.toDateString() === yesterday.toDateString()) {
            return `Yesterday ${date.toLocaleTimeString('en-US', { 
                hour: 'numeric',
                minute: '2-digit',
                hour12: true 
            })}`
        }
        
        // Otherwise show full date
        return date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        })
    }

    const getMessageAlignment = (message, userId) => {
        return {
            'justify-end': message.sender_id === userId,
            'justify-start': message.sender_id !== userId
        }
    }

    const getMessageStyle = (message, userId) => {
        return {
            'bg-primary text-white': message.sender_id === userId,
            'bg-gray-100': message.sender_id !== userId
        }
    }

    const getTimestampAlignment = (message, userId) => {
        return {
            'text-right': message.sender_id === userId,
            'text-left': message.sender_id !== userId
        }
    }

    return {
        formatDate,
        getMessageAlignment,
        getMessageStyle,
        getTimestampAlignment
    }
}
