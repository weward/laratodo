<template>
  <div>
      <q-btn-group rounded flat dense>
        <q-btn
          @click="() => data.open = true"
          :text-color="props.mode ? 'gray' : 'white'"
          class="tw-text-xs tw-text-neutral-500"
          icon="more_vert"
          flat
          rounded
          dense
        />
        <q-menu
          v-if="data.open"
          auto-close
        >
          <q-list>

            <q-item
              v-if="props.mode && props.mode == 'view' && !data.task?.is_completed && !data.task?.is_archived"
              @click="() => updateModalMode()"
              clickable>
              <q-item-section>Edit</q-item-section>
            </q-item>

            <q-item
              v-if="props.mode && props.mode == 'edit'"
              @click="() => updateModalMode()"
              clickable>
              <q-item-section>Cancel Edit</q-item-section>
            </q-item>

            <q-item
              v-if="data.task?.is_completed && !data.task?.is_archived"
              @click="() => toggleCompletion()"
              clickable>
              <q-item-section>Mark As Todo</q-item-section>
            </q-item>

            <q-item
              v-if="!data.task?.is_completed && !data.task?.is_archived"
              @click="() => toggleCompletion()"
              clickable>
              <q-item-section>Mark As Completed</q-item-section>
            </q-item>

            <q-item
              v-if="!data.task?.is_archived"
              @click="() => toggleArchiving()"
              clickable>
              <q-item-section>Archive</q-item-section>
            </q-item>

            <q-item
              v-if="data.task?.is_archived"
              @click="() => toggleArchiving()"
              clickable>
              <q-item-section>Restore</q-item-section>
            </q-item>

            <q-separator></q-separator>

            <q-item
              @click="() => confirmDelete()"
              clickable>
              <q-item-section class="tw-text-red-700 tw-font-semibold">Delete</q-item-section>
            </q-item>

          </q-list>
        </q-menu>
    </q-btn-group>

    <ConfirmDialog :show="data.showConfirmDialog" :action="data.actionToConfirm" @proceed="proceed" @close="data.showConfirmDialog = false"/>

  </div>
</template>

<script setup>

  import { onMounted, watch, reactive } from 'vue'
  import { useTaskStore } from 'src/stores/taskStore'
  import ConfirmDialog from 'src/components/ConfirmDialog.vue'

  const props = defineProps({
    mode: {
      type: String,
      default: ''
    },
    task: {
      type: Object,
      required: true
    },
  })

  const taskStore = useTaskStore()
  const emit = defineEmits(['updateTaskStatus'])

  const data = reactive({
    showConfirmDialog: false,
    actionToConfirm: '',
    open: false,
    task: null,
  })

  const toggleCompletion = () => {
    taskStore.toggleTaskStatus(data.task.id, 'toggle-completion')
  }

  const toggleArchiving = () => {
    taskStore.toggleTaskStatus(data.task.id, 'toggle-archiving')
  }

  const updateModalMode = () => {
    emit('updateModalMode')
  }

  const confirmDelete = () => {
    data.actionToConfirm = 'delete'
    data.showConfirmDialog = true // if should proceed()
  }

  const proceed = (toProceed, action) => {
    switch(action) {
      case 'delete':
        deleteTask()
        break
      default:
        data.showConfirmDialog = false
        break
    }

    data.showConfirmDialog = false
  }

  const deleteTask = () => {
    taskStore.delete(data.task?.id)
      .then((res) => {
        if (props.mode ?? false) {
          taskStore.toggleModal()
        }
      })
  }

  watch(() => props.task, (newValue) => {
    data.task = newValue
  })

  onMounted(() => {
    data.task = props.task ?? data.task
  })

</script>
