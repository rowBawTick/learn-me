<template>
  <div class="grid grid-cols-2 gap-4">
    <div>
      <label for="currency" class="block text-sm font-medium text-darkerGrey">Currency</label>
      <q-select
        id="currency"
        :model-value="currency_id"
        @update:model-value="$emit('update:currency_id', $event)"
        :options="currencyOptions"
        filled
        dense
        emit-value
        map-options
        class="w-full"
        required
      >
        <template v-slot:no-option>
          <q-item>
            <q-item-section class="text-grey">
              No currencies found
            </q-item-section>
          </q-item>
        </template>
      </q-select>
      <div v-if="errors?.currency_id" class="mt-1 text-error text-sm">{{ errors.currency_id }}</div>
    </div>

    <div>
      <label for="price" class="block text-sm font-medium text-darkerGrey">Price per hour</label>
      <q-input
        id="price"
        :model-value="price_per_hour"
        @update:model-value="$emit('update:price_per_hour', $event)"
        type="number"
        min="0"
        step="any"
        filled
        dense
        borderless
        :prefix="selectedCurrencySymbol"
        class="w-full"
        @keydown="allowDecimalInput"
        @keydown.up="incrementPrice"
        @keydown.down="decrementPrice"
        required
      />
      <div v-if="errors?.price_per_hour" class="mt-1 text-error text-sm">{{ errors.price_per_hour }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  currency_id: {
    type: [String, Number],
    required: true
  },
  price_per_hour: {
    type: [String, Number],
    required: true
  },
  currencies: {
    type: Array,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['update:currency_id', 'update:price_per_hour']);

const currencyOptions = computed(() => {
  return props.currencies.map(currency => ({
    label: `${currency.code} (${currency.symbol})`,
    value: currency.id
  }));
});

const selectedCurrencySymbol = computed(() => {
  const currency = props.currencies.find(c => c.id === props.currency_id);
  return currency?.symbol || '£';
});

function allowDecimalInput(event) {
  const key = event.key;
  if (key === '.' && event.target.value.includes('.')) {
    event.preventDefault();
  }
  if (!/[\d.]/.test(key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(key)) {
    event.preventDefault();
  }
}

function incrementPrice(event) {
  event.preventDefault();
  const currentValue = parseFloat(props.price_per_hour) || 0;
  emit('update:price_per_hour', (currentValue + 1).toString());
}

function decrementPrice(event) {
  event.preventDefault();
  const currentValue = parseFloat(props.price_per_hour) || 0;
  if (currentValue > 0) {
    emit('update:price_per_hour', (currentValue - 1).toString());
  }
}
</script>
