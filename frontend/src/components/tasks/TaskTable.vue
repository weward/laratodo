<template>
  <div class="">

    <div class="tw-p-0">

      <TaskFilter
        v-show="data.toggleFilterForm"
        @updatedFilter="filter"
        @onCloseFilterForm="() => data.toggleFilterForm = false"
        :filterParams="data.filter"
      />

    </div>

    <div class="tw-p-3 md:tw-flex md:tw-justify-between">

      <div>
        <TaskStatusTab
          :status="data.filter.status"
          @onUpdatedStatus="filterStatus"
        />
      </div>
      <div class=" tw-pt-6 tw-text-right md:tw-pt-0">
        <q-btn
          v-if="!data.toggleFilterForm"
          @click="data.toggleFilterForm = true"
          icon="filter_alt"
          size="sm"
          class="tw-text-neutral-600 "
        />
      </div>

    </div>

    <div class="">
      <q-table
        grid
        flat
        bordered
        title=""
        :rows="data.tasks"
        :columns="columns"
        :loading="data.loading"
        row-key="id"
        hide-header
        v-model:pagination="data.pagination"
        rows-per-page-options=""
        @request="getAllTasks"
        class="tw-flex">
          <template v-slot:item="props">

            <TaskCard :task="props"/>

          </template>

          <template v-slot:no-data="{ icon, message, filter }">
            <div class="full-width row flex-center q-gutter-sm tw-text-gray-400 tw-italic tw-py-6">
              <q-icon size="2em" name="sentiment_dissatisfied" />
              <span>
                No data available
              </span>
            </div>
          </template>

          <template v-slot:pagination="scope">
            <div class="tw-text-xs tw-text-gray-600">
              {{ pageDisplay }}
              <span
                v-if="data.pagination.rowsNumber"
                class="tw-text-gray-400">
                ({{ data.pagination.rowsNumber }} Tasks)
              </span>
            </div>

            <q-btn
              v-if="scope.pagesNumber > 2"
              icon="first_page"
              color="grey-8"
              round
              dense
              flat
              :disable="scope.isFirstPage"
              @click="scope.firstPage"
            />

            <q-btn
              icon="chevron_left"
              color="grey-8"
              round
              dense
              flat
              :disable="scope.isFirstPage"
              @click="scope.prevPage"
            />

            <q-btn
              icon="chevron_right"
              color="grey-8"
              round
              dense
              flat
              :disable="scope.isLastPage"
              @click="scope.nextPage"
            />

            <q-btn
              v-if="scope.pagesNumber > 2"
              icon="last_page"
              color="grey-8"
              round
              dense
              flat
              :disable="scope.isLastPage"
              @click="scope.lastPage"
            />
          </template>
        </q-table>
    </div>

  </div>
</template>

<script setup>
  import { onMounted, watch, computed, reactive } from 'vue'
  import { useRouter } from 'vue-router';
  import { useTaskStore }  from 'src/stores/taskStore'
  import TaskCard from 'src/components/tasks/TaskCard.vue'
  import TaskStatusTab from 'src/components/tasks/TaskStatusTab.vue';
  import TaskFilter from 'src/components/tasks/TaskFilter.vue';

  const router = useRouter()
  const taskStore = useTaskStore()

  const data = reactive({
    loading: false,
    toggleFilterForm: false,
    tasks: [],
    columns: {},
    pagination: {
      page: 1,
      rowsPerPage: 12,
      rowsNumber: 0, // total
    },
    filter: {
      status: 'todo',
    },
  })

  const pageDisplay = computed(() => {
    let page = data.pagination.page
    let totalRows = data.pagination.rowsNumber ? data.pagination.rowsNumber : 1
    let rowsPerPage = data.pagination.rowsPerPage

    let totalPages = parseInt(Math.ceil(totalRows / rowsPerPage))

    return `Page: ${page} of ${totalPages}`
  })

  watch(() => taskStore.tasks, (newValue) => {
    data.tasks = [...newValue]
  })

  const composeQuery = async (propsPagination) => {
    let pagination = await propsPagination ?? data.pagination
    const { page } = await pagination

    const query = await {}

    if (page && page > 1) {
      query.page = await page
    }

    for (const key in data.filter) {
      if (data.filter.hasOwnProperty(key) && data.filter[key]) {
        query[key] = await data.filter[key]
      }
    }

    data.pagination = await {...pagination}

    return await query
  }

  const getAllTasks = async (props) => {

    const query = await composeQuery(props?.pagination)

    data.loading = await true

    await taskStore.getAllTasks(query)
      .then(async (res) => {
        data.tasks = await res.data
        data.pagination.rowsNumber = await res.meta.total

        router.push({ name: 'admin.tasks.index', query })
      }).finally(() => {
        data.loading = false
      })
  }

  const filterStatus = (selectedStatus) => {
    data.filter.status = selectedStatus
    data.pagination.page = 1
    // removeOldFilters()
    getAllTasks()
  }

  const filter = async(filterForm) => {
    removeOldFilters()

    for (const field in filterForm) {
      data.filter[field] = await filterForm[field]
    }

    data.filter = { ...data.filter }

    await getAllTasks()
  }

  const removeOldFilters = () => {
    for (const key in data.filter) {
      if (key != 'status') {
        data.filter[key] = ''
      }
    }
  }

  const loadUrlQueryParams = async () => {
    data.filter = {}

    const queryParams = await new URLSearchParams(window.location.search)
    const page = await queryParams.get('page') ?? ''

    await queryParams.forEach((value, key) => {
      if (value && key != 'page') {
        data.filter[key] = value
      }
    })

    data.filter = { ...data.filter }

    if (page) {
      data.pagination.page = await parseInt(page)
    }

  }

  onMounted(async () => {
    await loadUrlQueryParams()
    await getAllTasks()
  })

</script>
