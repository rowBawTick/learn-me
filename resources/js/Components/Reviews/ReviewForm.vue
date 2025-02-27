<template>
    <div>
        <QDialog v-model="isOpen" persistent>
            <QCard style="min-width: 350px">
                <div class="p-4 flex justify-between">
                    <div class="text-h6">Write a Review</div>
                    <QBtn icon="close" flat round dense v-close-popup />
                </div>

                <QCardSection class="q-pt-sm">
                    <form @submit.prevent="submitReview">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                <QRating
                                    v-model="form.rating"
                                    :max="5"
                                    size="2em"
                                    color="primary"
                                    icon="star_border"
                                    icon-selected="star"
                                />
                                <div v-if="form.errors.rating" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.rating }}
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Review</label>
                                <QInput
                                    v-model="form.description"
                                    type="textarea"
                                    rows="4"
                                    class="w-full"
                                    placeholder="Share your experience with this tutor..."
                                    :error="!!form.errors.description"
                                    :error-message="form.errors.description"
                                />
                            </div>

                            <div class="flex justify-end gap-2">
                                <QBtn
                                    danger
                                    label="Cancel"
                                    color="negative"
                                    v-close-popup
                                />
                                <QBtn
                                    type="submit"
                                    color="primary"
                                    :loading="form.processing"
                                    :disable="form.processing"
                                    label="Submit Review"
                                />
                            </div>
                        </div>
                    </form>
                </QCardSection>
            </QCard>
        </QDialog>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { QDialog, QCard, QCardSection, QBtn, QRating, QInput, QSpace } from 'quasar'
import { ref, computed } from 'vue'

const props = defineProps({
    advertId: {
        type: Number,
        required: true
    },
    advertUserId: {
        type: Number,
        required: true
    },
    auth: {
        type: Object,
        required: true
    },
    modelValue: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['reviewSubmitted', 'update:modelValue'])

const isOpen = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

const form = useForm({
    rating: 0,
    description: ''
})

const submitReview = async () => {
    try {
        const response = await axios.post(route('reviews.store', props.advertId), form)
        form.reset()
        emit('reviewSubmitted', response.data.review)
        isOpen.value = false
    } catch (error) {
        if (error.response?.data?.message) {
            form.setError('description', error.response.data.message)
        }
    }
}
</script>
