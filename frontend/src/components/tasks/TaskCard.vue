<template>
  <div class="tw-w-full sm:tw-w-1/2 xl:tw-w-1/3 2xl:tw-w-1/4 sm:tw-px-1 tw-py-2 lg:tw-px-2 tw-relative ">



    <q-card :class="[data.task?.is_archived ? 'tw-opacity-80 ' : '', 'tw-h-full hover:tw-cursor-pointer']">
    <!-- <q-card class=""> -->
      <q-card-section class="tw-bg-neutral-400 tw-text-white tw-border-b-4 tw-border-amber-400 tw-p-0">
        <div class=" tw-float-right tw-pt-3 tw-pr-4">

          <TaskMenu @updateTaskStatus="updateStatus" :task="data.task"/>

        </div>
         <div @click="() => openModal()" class=" tw-p-4 tw-pr-2 tw-flex">
            <div class="tw-text-xs tw-text-gray-400 tw-self-center tw-flex-grow">
              <q-badge
                v-if="data.task?.priority?.id"
                :color="priorityTextColor"
                class="tw-font-semibold">
                {{ data.task.priority?.name?.toUpperCase() }}
              </q-badge>
              <span v-if="!data.task?.priority_id" class="">&nbsp;</span>
            </div>

            <div class="">

              <div
                v-if="data.task?.attachments?.length"
                class="tw-self-center tw-text-xs"
              >
                <q-icon
                  name="attach_file"
                  size="xs"
                  class="tw-text-amber-400 tw-self-center"
                />
                <span class="">{{ data.task?.attachments?.length }}</span>
              </div>

            </div>
         </div>
        </q-card-section>
      <div @click="() => openModal()" >
        <q-img
          :src="data.task?.attachments?.length ? data.task?.attachments?.[0].fileUrl : 'https://miro.medium.com/v2/resize:fit:1400/0*KJ94vjocvGzFbiD8'"
          :ratio="16/10"
          fit="cover">
          <div v-if="data.task?.is_archived" class="overlay tw-absolute tw-inset-0 tw-flex tw-items-center tw-h-full tw-w-full tw-justify-center" style="z-index:9;">
            <h1 class=" tw-text-red-400 tw-text-6xl tw-font-bold tw-opacity-50">Archived</h1>
          </div>
          <div class="tw-inset-0 absolute-top tw-text-xs tw-flex tw-justify-between">
            <div>
              {{ displayStatus }}
            </div>
            <div v-if="displayStatusDate" class="tw-float-right tw-text-xs">
              <q-icon name="today" />
              {{ displayStatusDate ?? "" }}
            </div>
          </div>
          <div class="absolute-bottom tw-text-md">
            {{ data.task?.title ?? "" }}
          </div>
        </q-img>

        <q-card-section>
          <div class="">
          {{ data.task?.description ? data.task.description.slice(0, 250) : "" }}
          </div>
        </q-card-section>
        <q-card-section >
          <div class="tw-text-xs tw-text-gray-500">{{ displayTags }}&nbsp;</div>
        </q-card-section>
      </div>

    </q-card>
  </div>
</template>

<script setup>

  import { watch, computed, reactive } from 'vue'
  import TaskMenu from 'src/components/tasks/TaskMenu.vue'
  import { useTaskStore } from 'src/stores/taskStore'

  const props = defineProps({
    task: {
      type: Object,
      required: true,
    }
  })

  const taskStore = useTaskStore()

  const data = reactive({
    task: null,
  })

  const priorityTextColor = computed(() => {
    let priority = data.task?.priority?.id

    switch(priority) {
      case 1: // low
        return 'gray'
      case 2: // normal
        return 'green'
      case 3: // high
        return 'orange'
      case 4: // urgent
        return 'red'
      default:
        return 'gray'
    }
  })

  const displayStatus = computed(() => {
    let res = taskStore.composeStatusDisplayData(data?.task)

    return res.label ?? ""
  })

  const displayStatusDate = computed(() => {
    let res = taskStore.composeStatusDisplayData(data?.task)

    return res.date ?? ''
  })

  const displayTags = computed(() => {
    let limit = 3
    let tags = data.task?.tags
    let total = tags?.length

    tags = tags?.splice(0, limit)

    tags = tags?.join(', ') ?? ""

    return tags + ((total > limit) ? "..." : "")
  })

  watch(() => props.task, (newValue) => {
    data.task = newValue.row
    data.task = {
      ...newValue.row,
      priority: newValue.row.priority ? { ...newValue.row.priority } : null,
      tags: [...newValue.row.tags],
      attachments: [...newValue.row.attachments],
    }
  })

  const updateStatus = (action, taskId) => {

  }

  const openModal = () => {
    taskStore.toggleModal(data.task.id)
  }


</script>
