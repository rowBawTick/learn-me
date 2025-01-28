<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-darkerGrey">Available Time Slots</h3>
            <q-btn
                flat
                color="primary"
                label="Add Time Slot"
                @click="addTimeSlot"
                :disable="modelValue.length >= 10"
            />
        </div>

        <div v-if="error" class="mt-1 text-error text-sm">
            {{ error }}
        </div>

        <!-- Table layout for time slots -->
        <div v-if="modelValue.length > 0" class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 text-left text-sm font-medium text-darkerGrey">#</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-darkerGrey">Day of Week</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-darkerGrey">Start Time</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-darkerGrey">End Time</th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-darkerGrey">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(slot, index) in modelValue"
                    :key="index"
                    class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm text-darkerGrey">{{ index + 1 }}</td>
                    <td class="px-4 py-2">
                        <q-select
                            v-model="slot.day_of_week"
                            :options="daysOfWeek"
                            filled
                            dense
                            emit-value
                            map-options
                            class="w-full"
                            borderless
                            input-class="no-border"
                            required
                        />
                    </td>
                    <td class="px-4 py-2">
                        <q-input
                            v-model="slot.start_time"
                            type="time"
                            filled
                            dense
                            borderless
                            input-class="no-border"
                            class="w-full cursor-pointer"
                            required
                        />
                    </td>
                    <td class="px-4 py-2">
                        <q-input
                            v-model="slot.end_time"
                            type="time"
                            filled
                            dense
                            borderless
                            input-class="no-border"
                            class="w-full cursor-pointer"
                            required
                        />
                    </td>
                    <td class="px-4 py-2 text-right">
                        <q-btn
                            flat
                            round
                            color="negative"
                            icon="delete"
                            size="sm"
                            @click="confirmDelete(index)"
                        />
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div v-if="modelValue.length === 0" class="text-center py-4 text-gray-500">
            No time slots added yet. Click "Add Time Slot" to begin.
        </div>

        <!-- Delete Confirmation Dialog -->
        <q-dialog v-model="showDeleteDialog" persistent>
            <q-card>
                <q-card-section class="row items-center">
                    <q-avatar icon="warning" color="warning" text-color="white" />
                    <span class="q-ml-sm">Are you sure you want to delete this time slot?</span>
                </q-card-section>

                <q-card-actions align="right">
                    <q-btn flat label="Cancel" color="primary" v-close-popup />
                    <q-btn flat label="Delete" color="negative" @click="deleteTimeSlot" v-close-popup />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    modelValue: {
        type: Array,
        required: true
    },
    error: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue'])

const daysOfWeek = [
    { label: 'Monday', value: 'Monday' },
    { label: 'Tuesday', value: 'Tuesday' },
    { label: 'Wednesday', value: 'Wednesday' },
    { label: 'Thursday', value: 'Thursday' },
    { label: 'Friday', value: 'Friday' },
    { label: 'Saturday', value: 'Saturday' },
    { label: 'Sunday', value: 'Sunday' }
]

const showDeleteDialog = ref(false)
const indexToDelete = ref(null)

const addTimeSlot = () => {
    if (props.modelValue.length < 10) {
        const newTimeSlots = [...props.modelValue]
        newTimeSlots.push({
            day_of_week: daysOfWeek[0].value,
            start_time: '09:00',
            end_time: '17:00',
            is_recurring: true
        })
        emit('update:modelValue', newTimeSlots)
    }
}

const confirmDelete = (index) => {
    indexToDelete.value = index
    showDeleteDialog.value = true
}

const deleteTimeSlot = () => {
    if (indexToDelete.value !== null) {
        const newTimeSlots = [...props.modelValue]
        newTimeSlots.splice(indexToDelete.value, 1)
        emit('update:modelValue', newTimeSlots)
        indexToDelete.value = null
    }
    showDeleteDialog.value = false
}
</script>
