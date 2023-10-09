<template>
  <div class="q-pa-md q-gutter-sm">

    <q-dialog v-model="isModalOpen">
      <q-card class="tw-w-full md:tw-w-4/6">
        <q-card-section class=" tw-bg-neutral-500 tw-text-white tw-mb-3 tw-border-b-4 tw-border-amber-400">
          <div class="tw-flex tw-justify-between ">
            <div class="text-h6">
              {{ displayModalMode }} Task
            </div>
            <div class="">
              <q-btn
                @click="() => taskStore.toggleModal()"
                class="tw-text-gray-300"
                icon="close"
                size="sm"
                flat
                v-close-popup />
            </div>
          </div>
        </q-card-section>

        <q-card-section class="tw-mb-6">
          <div class="tw-w-full tw-flex tw-justify-between">
            <div>
              <q-badge
                  v-if="displayStatus?.label"
                  :color="displayStatus?.color ?? 'primary'"
                  class="tw-p-1 tw-font-semibold">
                  {{ displayStatus?.label ?? "" }}
                </q-badge>
            </div>
            <div class="tw-text-right ">
              <TaskMenu
                v-if="data.modalMode == 'view'"
                :task="data.task"
                :mode="data.modalMode"
                @updateModalMode="() => toggleModalEditMode()"
              />
            </div>
          </div>
        </q-card-section>

        <q-card-section class="q-pt-none">

          <TaskForm
            :task="data.task"
            :mode="data.modalMode"
            @showConfirmDialog="() => data.showConfirmDialog = true"
            @updateMode="(mode) => data.modalMode = mode"
            @onClose="() => taskStore.toggleModal()"
          />

        </q-card-section>

      </q-card>
    </q-dialog>

  </div>
</template>

<script setup>
  import { watch, computed, reactive } from 'vue'
  import { useTaskStore } from 'src/stores/taskStore';
  import TaskForm from 'src/components/tasks/TaskForm.vue'
  import TaskMenu from 'src/components/tasks/TaskMenu.vue';

  const taskStore = useTaskStore()

  const data = reactive({
    showConfirmDialog: false,
    task: null,
    modalMode: 'view',
  })

  const isModalOpen = computed(() => {
    return taskStore.isModalOpen
  })

  const displayModalMode = computed(() => {
    let mode = data.modalMode.charAt(0).toUpperCase() + '' + data.modalMode.slice(1)
    return mode
  })

  const displayStatus = computed(() => {
    let res = (['view', 'edit'].includes(data.modalMode)) ? taskStore.composeStatusDisplayData() : ""

    return res
  })

  watch(() => taskStore.task, (newValue) => {
    // takes updates from taskStore and updates self and propagate to:
    // - TaskForm and TaskMenu components
    data.task = null

    if (newValue) {
      data.task = {
        ...data.task,
        ...newValue,
        priority: newValue.priority ? {...newValue.priority} : null,
        tags: [...newValue.tags],
        attachments: [...newValue.attachments],
      }
    }
  })

  watch(() => taskStore.showModal, (newValue) => {
    // Set Modal to 'view' when closed
    data.modalMode = data.task ? 'view' : 'new'
  })

  const toggleModalEditMode = () => {
    data.modalMode = data.modalMode == 'view' ? 'edit' : 'view'
  }

</script>
