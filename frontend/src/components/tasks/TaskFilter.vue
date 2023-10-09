<template>
  <div>
    <q-card class="tw-bg-neutral-50">
      <q-card-section>
        <div>
          <div class="tw-text-lg tw-text-neutral-600 tw-pb-6 tw-pl-1">Filter</div>
        </div>
        <div class=" tw-mb-6">
          <q-form
            @submit="onSubmit"
            class="q-gutter-md"
            >
            <div class="tw-grid tw-gap-4 tw-grid-cols-1 md:tw-grid-cols-4">

                <div class="">
                  <q-input
                    v-model="form.search"
                    :disable="data.loading"
                    :error="errors.hasOwnProperty('search') ?? false"
                    :error-message="errors.search ?? ''"
                    label="Search"
                    filled
                    lazy-rules
                  />
                </div>

                <div class="">
                  <q-select
                    v-model="form.priority_id"
                    :options="priorityOptions"
                    :disable="data.loading"
                    :display-value="selectedPriorityLabel"
                    :error="errors.hasOwnProperty('priority_id') ?? false"
                    :error-message="errors.priority_id ?? ''"
                    label="Priority"
                    option-label="name"
                    option-value="id"
                    emit-value
                    filled
                    clearable
                  />
                </div>

                <div>
                  <DatePicker
                    @updatedValue="updateDateValue"
                    label="Due Date Start"
                    :selectedDate="form.due_date_from"
                    :errors="errors.form ?? ''"
                    :loading="data.loading"
                    mode="filter"
                    fieldKey="due_date_from"
                  />
                </div>
                <div>
                  <DatePicker
                    @updatedValue="updateDateValue"
                    label="Due Date End"
                    :selectedDate="form.due_date_to"
                    :errors="errors.form ?? ''"
                    :loading="data.loading"
                    mode="filter"
                    fieldKey="due_date_to"
                  />
                </div>

                <div>
                  <DatePicker
                    @updatedValue="updateDateValue"
                    label="Completed Date Start"
                    :selectedDate="form.completed_at_from"
                    :errors="errors.form ?? ''"
                    :loading="data.loading"
                    mode="filter"
                    fieldKey="completed_at_from"
                  />
                </div>
                <div>
                  <DatePicker
                    @updatedValue="updateDateValue"
                    label="Completed Date End"
                    :selectedDate="form.completed_at_to"
                    :errors="errors.form ?? ''"
                    :loading="data.loading"
                    mode="filter"
                    fieldKey="completed_at_to"
                  />
                </div>

                <div>
                  <DatePicker
                    @updatedValue="updateDateValue"
                    label="Archived Date Start"
                    :selectedDate="form.archived_at_from"
                    :errors="errors.form ?? ''"
                    :loading="data.loading"
                    mode="filter"
                    fieldKey="archived_at_from"
                  />
                </div>
                <div>
                  <DatePicker
                    @updatedValue="updateDateValue"
                    label="Archived Date End"
                    :selectedDate="form.archived_at_to"
                    :errors="errors.form ?? ''"
                    :loading="data.loading"
                    mode="filter"
                    fieldKey="archived_at_to"
                  />
                </div>

            </div>

            <q-separator />

            <div class="tw-grid tw-gap-4 tw-grid-cols-1 md:tw-grid-cols-4">
              <div></div> <div></div>

              <div class="">
                <q-select
                  v-model="form.sort_by"
                  :options="sortByOptions"
                  :disable="data.loading"
                  :display-value="selectedSortByLabel"
                  :error="errors.hasOwnProperty('sort_by') ?? false"
                  :error-message="errors.sort_by ?? ''"
                  label="Sorting"
                  option-label="name"
                  option-value="id"
                  emit-value
                  filled
                  clearable
                />
              </div>

              <div class="">
                <q-select
                  v-model="form.order_by"
                  :options="['asc', 'desc']"
                  :disable="data.loading"
                  :error="errors ?? ''"
                  label="Order"
                  emit-value
                  filled
                  clearable
                />
              </div>
            </div>

            <div class="tw-w-full tw-pr-3">
              <div class="tw-text-center">
                <q-btn
                  @click="() => closeFilterForm()"
                  type="button"
                  label="Close"
                  class="tw-mr-4"
                  flat
                />

                <q-btn
                  type="submit"
                  label="Filter"
                  color="amber-4"
                  text-color="black"
                />
              </div>
            </div>

          </q-form>
        </div>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup>
  import { watch, computed, reactive } from 'vue'
  import DatePicker from 'src/components/DatePicker.vue'

  const props = defineProps({
    filterParams: {
      type: Object,
      required: false,
    }
  })

  const emit = defineEmits(['updatedFilter', 'onCloseFilterForm'])

  const form = reactive({
    search: '',
    priority_id: '',
    due_date_from: '',
    due_date_to: '',
    completed_at_from: '',
    completed_at_to: '',
    archived_at_from: '',
    archived_at_to: '',
    sort_by: '',
    order_by: '',
  })

  const errors = reactive({
    form: {},
  })

  const data = reactive({
    loading: false,
    params: null,
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

  const sortByOptions = [
    {
      id: 'title',
      name: 'Title',
    },
    {
      id: 'description',
      name: 'Description',
    },
    {
      id: 'due_date',
      name: 'Due Date',
    },
    {
      id: 'created_at',
      name: 'Date created',
    },
    {
      id: 'completed_at',
      name: 'Date completed',
    },
    {
      id: 'archived_at',
      name: 'Date Archived',
    },
  ]

  const selectedPriorityLabel = computed(() => {
    let selectedOption = priorityOptions.filter((opt) => opt.id == form.priority_id)

    return selectedOption.length ? selectedOption[0].name : ''
  })

  const selectedSortByLabel = computed(() => {
    let selectedOption = sortByOptions.filter((opt) => opt.id == form.sort_by)

    return selectedOption.length ? selectedOption[0].name : ''
  })

  watch(() => props.filterParams, async (newValue) => {
    for (const key in form) {
      form[key] = await newValue?.[key]
    }
  })

  const updateDateValue = async (fieldKey, val) => {
    form[fieldKey] = await val

    await validateDateRange()
  }

  const validateDateRange = () => {

    let hasError = false
    let errorMessage = ['Invalid date range'];
    let dateRangeFields = [
      ['due_date_from', 'due_date_to'],
      ['completed_at_from', 'completed_at_to'],
      ['archived_at_from', 'archived_at_to'],
    ]

    dateRangeFields.forEach((item) => {
      if (new Date(form[item[0]]) > new Date(form[item[1]])) {
        errors.form[item[0]] = errorMessage
        errors.form[item[1]] = errorMessage
        hasError = true
      }
    })

    if (!hasError) {
      delete errors.form.due_date_from
      delete errors.form.due_date_to
      delete errors.form.completed_at_from
      delete errors.form.completed_at_to
      delete errors.form.archived_at_from
      delete errors.form.archived_at_to
    }
  }

  const onSubmit = () => {
    let filterFields  = {};
    // remove empty fields
    for (const field in form) {
      if (form.hasOwnProperty(field) && form[field]) {
        filterFields[field] = form[field]
      }
    }

    emit('updatedFilter', filterFields)
  }

  const closeFilterForm = () => {
    emit('onCloseFilterForm', true)
  }

</script>
