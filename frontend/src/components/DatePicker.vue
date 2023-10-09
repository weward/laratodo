<template>
  <div>
    <q-input
      v-model="data.selectedDate"
      :label="props.label"
      :readonly="props.mode == 'view'"
      :disable="props.loading"
      :error="props.errors?.hasOwnProperty(props.fieldKey) ?? false"
      :error-message="props.errors?.[props.fieldKey]?.[0] ?? ''"
      filled
      clearable
      mask="####-##-##">
      <template v-slot:append>
        <q-icon
          v-if="props.loading || props.mode != 'view'"
          name="event"
          class="cursor-pointer">
          <q-popup-proxy cover transition-show="scale" transition-hide="scale">

            <q-date
              v-model="data.selectedDate"
              mask="YYYY-MM-DD"
              minimal
              :options="validDates">
              <div class="row items-center justify-end">
                <q-btn v-close-popup label="Close" color="primary" flat />
              </div>
            </q-date>

          </q-popup-proxy>
        </q-icon>
      </template>
    </q-input>
  </div>
</template>

<script setup>
  import { watch, reactive } from 'vue'

  const props = defineProps({
    label: {
      type: String,
      required: true,
    },
    selectedDate: {
      type: String,
      required: true,
    },
    errors: {
      type: Object,
      default: null
    },
    fieldKey: {
      type: String,
      default: '',
    },
    mode: {
      type: String,
      default: 'view',
    },
    loading: {
      type: Boolean,
      default: false,
    }
  })

  const emit = defineEmits(['updatedValue'])

  const data = reactive({
    showCalendar: false,
    selectedDate: '',
  })

  const validDates = (date) => {
    if (props.mode == 'filter') {
      return true; // all valid
    }

    const today = new Date();
    const day = String(today.getDate()).padStart(2, '0');
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const year = today.getFullYear();
    const formattedDate = `${year}/${month}/${day}`;

    return date >= formattedDate
  }

  watch(() => props.selectedDate, (newValue) => {
    data.selectedDate = newValue
  })

  watch(() => data.selectedDate, (newValue) => {
    emit('updatedValue', props.fieldKey, newValue)
  })


</script>
