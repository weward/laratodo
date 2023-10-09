<template>
  <div>
    <div class="heading tw-mx-2 tw-mb-1 md:tw-m-6">
      <q-card class="tw-bg-neutral-50">
        <q-card-section class="tw-flex tw-justify-between">
          <div class=" tw-text-lg tw-font-bold tw-uppercase tw-text-gray-600">
            My tasks
          </div>
          <div>
            <q-btn
              @click="() => taskStore.toggleModal()"
              label="Add New"
              class="tw-text-white tw-text-xs tw-bg-amber-400"/>
          </div>
        </q-card-section>
      </q-card>
    </div>
    <div class="content">
      <q-card class="" flat>
        <q-card-section>

          <TaskTable />

        </q-card-section>
      </q-card>
    </div>

    <TaskModal />

  </div>
</template>

<script setup>
  import TaskTable from 'src/components/tasks/TaskTable.vue'
  import TaskModal from 'src/components/tasks/TaskModal.vue'
  import { useTaskStore } from 'src/stores/taskStore';

  const taskStore = useTaskStore()

</script>
