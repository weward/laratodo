<template>
  <div>
    <q-dialog v-model="data.show" persistent transition-show="scale" transition-hide="scale">
      <q-card class=" tw-bg-neutral-500 text-white" style="width: 300px">
        <q-card-section>
          <div class="text-h6">Confirmation</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          Are you sure you want to proceed?
        </q-card-section>

        <q-card-actions align="right" class="bg-white text-teal">
          <q-btn @click="() => cancel()" flat label="Cancel" class="tw-text-red-600" v-close-popup />
          <q-btn @click="() => proceed()" flat label="Proceed"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
  import { watch, reactive } from 'vue'

  const props = defineProps({
    show: {
      type: Boolean,
      default: false
    },
    action: {
      type: String,
      required: true,
    }
  })

  const emit = defineEmits(['proceed', 'close'])

  const data = reactive({
    show: false,
  })

  watch(() => props.show, (newValue) => {
    data.show = newValue
  })

  const proceed = () => {
    emit('proceed', true, props.action)
  }

  const cancel = () => {
    emit('close', false)
  }

</script>
