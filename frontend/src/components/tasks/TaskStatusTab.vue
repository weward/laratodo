<template>
  <div class="">
    <q-btn
      @click="() => selectStatus('')"
      class="tw-mr-2"
      :color="props.status == '' ? 'amber-4' : 'white'"
      text-color="black"
      label="All"
      size="sm"
      rounded
    />
    <q-btn
      @click="() => selectStatus('todo')"
      class="tw-mr-2"
      :color="props.status == 'todo' ? 'amber-4' : 'white'"
      text-color="black"
      label="Todo"
      size="sm"
      rounded
    />
    <q-btn
      @click="() => selectStatus('is_completed')"
      class="tw-mr-2"
      :color="props.status == 'is_completed' ? 'amber-4' : 'white'"
      text-color="black"
      label="Completed"
      size="sm"
      rounded
    />
    <q-btn
      @click="() => selectStatus('is_archived')"
      class="tw-mr-2"
      :color="props.status == 'is_archived' ? 'amber-4' : 'white'"
      text-color="black"
      label="Archived"
      size="sm"
      rounded
    />
  </div>
</template>

<script setup>

  const props = defineProps({
    status: {
      type: String,
      default: 'todo',
    }
  })

  const emit = defineEmits(['onUpdatedStatus'])

  const selectStatus = (selectedStatus) => {
    emit('onUpdatedStatus', selectedStatus)
  }

</script>
