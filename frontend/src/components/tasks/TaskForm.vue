<template>
  <div>
     <q-form
        @submit="() => data.showConfirmDialog = true"
        class="q-gutter-md"
      >
      <div class="form-body tw-mb-14">

        <div class="tw-mb-2">
          <q-input
            v-model="data.task.title"
            :readonly="props.mode == 'view'"
            :disable="data.loading"
            :error="errors.form.hasOwnProperty('title') ?? false"
            :error-message="errors.form.title?.[0] ?? ''"
            :rules="[val => val && val.length > 0 || 'Please provide a title']"
            label="Title *"
            filled
            lazy-rules
          />
        </div>
        <div class="tw-mb-2">
          <q-input
            v-model="data.task.description"
            :readonly="props.mode == 'view'"
            :disable="data.loading"
            :error="errors.form.hasOwnProperty('description') ?? false"
            :error-message="errors.form.description?.[0] ?? ''"
            :rules="[val => val && val.length > 0 || 'Please input description.']"
            label="Description *"
            type="textarea"
            rows="5"
            fill-height
            filled
            lazy-rules
          />
        </div>

        <div class="tw-mb-2">
          <DatePicker
            @updatedValue="updateDueDate"
            label="Due Date"
            :selectedDate="data.task.due_date"
            :errors="errors.form"
            :mode="props.mode"
            :loading="data.loading"
            fieldKey="due_date"
          />
        </div>

        <div class="tw-mb-3">
          <q-select
            v-model="data.task.priority_id"
            :options="priorityOptions"
            :readonly="props.mode == 'view'"
            :disable="data.loading"
            :error="errors.form.hasOwnProperty('priority_id') ?? false"
            :error-message="errors.form.priority_id?.[0] ?? ''"
            :display-value="selectedPriorityLabel"
            option-label="name"
            option-value="id"
            label="Priority"
            emit-value
            filled
            clearable
          />
        </div>

        <div class="tw-mb-3">
          <q-chip
            v-for="tag in data.task.tags"
            @remove="() => removeTag(tag)"
            :key="tag"
            :removable="props.mode != 'view'"
            color="amber-400"
            text-color="black"
            icon="tag">
            {{ tag }}
          </q-chip>
        </div>
        <div class="tw-mb-6">
          <q-input
            v-if="props.mode != 'view'"
            @keydown.enter.prevent="addTag"
            :disable="data.loading"
            type="text"
            label="Input new tag ..."
            filled
          />
        </div>
        <!-- attachments -->
        <div v-if="props.mode != 'view'" class="tw-w-full tw-mb-3">
          <q-file
            v-model="data.task.new_attachments"
            :disable="data.loading"
            :error="Object.entries(errors.form)
              .filter(([key, value]) => key.startsWith('new_attachments'))
              .map((key, value) => key[1])
              .join(', ') != ''"
            :error-message="Object.entries(errors.form)
              .filter(([key, value]) => key.startsWith('new_attachments'))
              .map((key, value) => key[1])
              .join(', ')"
            label="Add New Attachments"
            accept=".svg,.png,.jpg,.mp4,.csv,.txt,.doc,.docx"
            outlined
            use-chips
            multiple
            clearable
            />
          </div>

        <div v-if="data.task.attachments?.length" class="tw-mb-3">
          <div class="tw-text-gray-600 tw-text-xs tw-mt-6 tw-ml-2 tw-mb-3">Attachments</div>
          <div>
            <q-list class="tw-rounded-borders">

              <q-item
                v-for="attachment in data.task.attachments"
                class="tw-border tw-border-gray-200 tw-mx-1 tw-my-1 tw-px-2 tw-py-1 tw-text-xs"
                dense>

                <q-item-section class="hover:tw-cursor-default">
                  <div class="tw-flex tw-justify-between">
                    <div class=" tw-self-center">
                      {{ attachment.filename }}
                    </div>
                    <div  class="">
                      <q-btn
                        v-if="props.mode != 'view'"
                        @click="() => removeFile(attachment.id)"
                        :disable="data.loading"
                        icon="highlight_off"
                        color="red"
                        class="tw-px-0"
                        size="sm"
                        flat
                        round
                      />
                      <q-btn
                        v-if="props.mode == 'view'"
                        :href="attachment.fileUrl"
                        type="a"
                        target="_blank"
                        icon="download"
                        class="tw-px-0 hover:tw-text-amber-600 tw-text-amber-400"
                        size="sm"
                        download
                        flat
                        round
                      />
                    </div>
                  </div>
                </q-item-section>
              </q-item>

            </q-list>
          </div>
        </div>

      </div>

      <div v-if="props.mode != 'view'" class="form-action tw-w-full tw-text-right tw-px-4 tw-mb-3">
        <q-btn @click="() => onClose()" label="Close" type="button" flat class="q-ml-sm hover:tw-bg-none tw-px-6 tw-mr-1" />
        <q-btn label="Save" type="submit" class="tw-bg-amber-400 tw-text-white tw-px-6"/>
      </div>

      </q-form>

      <ConfirmDialog :show="data.showConfirmDialog" @proceed="() => proceed()" @close="data.showConfirmDialog = false"/>
  </div>
</template>

<script setup>
  import { onMounted, watch, computed, reactive } from 'vue'
  import DatePicker from 'src/components/DatePicker.vue'
  import ConfirmDialog from 'src/components/ConfirmDialog.vue'
  import { useTaskStore } from 'src/stores/taskStore'

  const props = defineProps({
    task: {
      type: Object,
      required: false,
    },
    mode: {
      type: Object,
      required: false,
    }
  })

  const taskStore = useTaskStore()
  const emit = defineEmits(['showConfirmDialog', 'onClose'])

  const data = reactive({
    showConfirmDialog: false,
    task: {
      id: '',
      title: '',
      description: '',
      due_date: '',
      priority_id: '',
      tags: [],
      attachments: [], // existing
      new_attachments: [], // submitted
    },
  })

  const errors = reactive({
    form: {},
  })

  const priorityOptions = [
    {
      id: 1,
      name: 'Low',
    },
    {
      id: 2,
      name: 'Normal',
    },
    {
      id: 3,
      name: 'High',
    },
    {
      id: 4,
      name: 'Urgent',
    }
  ];

  const selectedPriorityLabel = computed(() => {
    let selectedOption = priorityOptions.filter((opt) => opt.id == data.task?.priority_id)

    return selectedOption.length ? selectedOption[0].name : ''
  })
  // when accesed from a card
  watch(() => props.task, async (newValue) => {
    if (newValue) {
      data.task = {
        ...data.task,
        ...newValue,
        priority: newValue.priority ? { ...newValue.priority } : null,
        tags: [...newValue.tags],
        attachments: [...newValue.attachments],
        new_attachments: [],
      }
    }
  })

  const proceed = () => {
    errors.form = {}
    data.loading = true

    switch (props.mode) {
      case 'new':
        save()
        break;
      case 'edit':
        update()
        break;
      default:
        save()
        break;
    }

    data.showConfirmDialog = false
  }

  const save = () => {
    let payload = composePayload()

    taskStore.save(payload)
    .then(async (res) => {
      await setMode('view')
    }).catch((err) => {
      errors.form = err ?? {}
    }).finally(() => {
      data.loading = false
    })
  }

  const update = () => {
    let payload = composePayload()

    taskStore.update(payload)
    .then((res) => {
      setMode('view')
    }).catch((err) => {
      errors.form = err ?? {}
    }).finally(() => {
      data.loading = false
    })
  }

  const updateDueDate = (fieldKey, val) => {
    data.task.due_date = val
  }

  const composePayload = () => {
    // translate attachments object array into array of ids
    let attachmentIds = []
    if (data.task.attachments?.length) {
      attachmentIds = data.task.attachments.map((att) => att.id)
    }

    const formData = new FormData()

    formData.append('id', data.task.id ?? '')
    formData.append('title', data.task.title)
    formData.append('description', data.task.description)
    formData.append('due_date', data.task.due_date ?? '')
    formData.append('priority_id', data.task.priority_id ?? '')

    if (data.task.tags?.length) {
      data.task.tags?.forEach((attachment, i) => {
        formData.append('tags[]', data.task.tags[i])
      })
    }

    if (attachmentIds?.length) {
      attachmentIds.forEach((attachment, i) => {
        formData.append('attachments[]', attachmentIds[i])
      })
    }

    if (data.task.new_attachments?.length) {
      data.task.new_attachments.forEach((attachment, i) => {
        formData.append('new_attachments[]', data.task.new_attachments[i])
      })
    }

    return formData
  }

  const removeFile = (attachmentId) => {
    let updatedAttachments = data.task.attachments.filter((att) => att.id != attachmentId)
    data.task.attachments = updatedAttachments
  }

  const onClose = () => {
    emit('onClose')
  }

  const setMode = (mode) => {
    emit('updateMode', mode)
  }

  const addTag = (evt) => {
    let input = evt.target.value
    if (input != '') {
      let regex = /[^a-zA-Z0-9\s\-_]/;
      let sanitized = input.replace(regex, "")

      let hasCopy = data.task.tags.filter(tag => tag == sanitized)

      if (!hasCopy.length) {
        data.task.tags.push(sanitized)
      }

      evt.target.value = ''
    }
  }

  const removeTag = (tag) => {
    let filteredTags = data.task.tags?.filter((item) => item != tag)

    data.task.tags = filteredTags
  }

  onMounted(() => {
    data.task = props.task ?? data.task

    data.task = {
      ...data.task,
      priority: data.task.prioirity ? {...data.task.priority} : null,
      tags: data.task.tags ? [...data.task.tags] : [],
      attachments: data.task.attachments ? [...data.task.attachments] : [],
      new_attachments: [],
    }

  })
</script>
