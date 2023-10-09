import { defineStore } from 'pinia'
import { api } from 'src/boot/axios'
import { notify } from 'src/config/elements'

export const useTaskStore = defineStore('taskStore', {
  state: () => ({
    tasks: null,
    task: null,
    showModal: false,
  }),
  getters: {
    getTask: (state) => state.task,
    getTasks: (state) => state.tasks,
    isModalOpen: (state) => state.showModal
  },
  actions: {
    getAllTasks(query) {
      return new Promise((resolve, reject) => {
        api.get('admin/tasks', {
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('authToken')}`
          },
          params: query,
        }).then((res) => {
          this.tasks = res.data.data

          resolve(res.data)
        }).catch((err) => {
          if (err.response.status == 500) {
            notify(err.response?.data)
          }

          reject()
        })
      })
    },
    getTaskRecord(taskId) {
      return new Promise((resolve, reject) => {
        api.get(`admin/tasks/${taskId}`, {
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('authToken')}`
          },
        }).then((res) => {
          this.task = res.data.data

          resolve(res)
        }).catch((err) => {
          if ([404, 500].includes(err.response.status)) {
            notify(err.response?.data)
          }
        })
      })
    },
    save(formData) {
      return new Promise((resolve, reject) => {
        api.post(`admin/tasks`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${localStorage.getItem('authToken')}`
          },
        }).then(async (res) => {
          await this.updateTaskInList(res.data.data)

          await resolve()
        }).catch((err) => {
          if ([404, 500].includes(err.response?.status)) {
            notify(err.response?.data)
          }
          if (err.response?.status == 422) {
            reject(err.response?.data?.errors)
            return
          }

          reject()
        })
      })
    },
    update(formData) {
      return new Promise((resolve, reject) => {
        formData.append('_method', 'PUT')

        api.post(`admin/tasks/${formData.get('id')}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
          },
        }).then(async (res) => {
          this.updateTaskInList(res.data.data)
          resolve()
        }).catch((err) => {
          if ([404, 500].includes(err.response?.status)) {
            notify(err.response?.data)
          }
          if (err.response?.status == 422) {
            reject(err.response?.data?.errors)
            return
          }

          reject()
        })
      })
    },
    delete(taskId) {
      return new Promise((resolve, reject) => {
        api.delete(`admin/tasks/${taskId}`, {
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('authToken')}`
          },
        }).then((res) => {
          this.task = null
          this.removeFromList(taskId)
          notify("Successfully deleted task!", "green", "top", "done_outline")

          resolve()
        }).catch((err) => {
          if ([404, 500].includes(err.response?.status)) {
            notify("Failed to delete task!")
            return
          }


          reject()
        })
      })
    },
    toggleTaskStatus(taskId, statusType) {
      return new Promise((resolve, reject) => {
        // statusType = toggle-completion /  toggle-archiving
        api.post(`admin/tasks/${taskId}/${statusType}`, {}, {
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('authToken')}`
          },
        }).then((res) => {
          this.task = {
            ...this.task,
            ...res.data.data
          }
          const queryParams = new URLSearchParams(window.location.search);
          const filterStatus = queryParams.get('status') ?? ''
          // if changed status while filter status == '' (all)
          if (filterStatus == '') {
            this.updateTaskInList(res.data.data)
          }
          // if changed status while filter status != '' (all)
          if (filterStatus != '') {
            this.removeFromList(res.data.data.id)
          }

          notify("Successfully updated task status!", "green", "top", "done_outline")

          resolve()
        }).catch((err) => {
          if ([404, 500].includes(err.response?.status)) {
            notify("Failed to update task status!")
            return
          }

          reject()
        })
      })
    },
    async updateTaskInList(task) {
      try {
        // Is existing?
        this.task = await {
          ...this.task,
          ...task,
          priority: task.priority,
          tags: task.tags,
          attachments: task.attachments,
          new_attachments: [],
        }

        let tasks = await this.getTasks
        const index = await tasks.findIndex((item, i) => item.id == task.id)
        // UPDATE
        if (index !== -1) {
          this.tasks[index] = {
            ...this.tasks[index],
            ...task,
            tags: task.tags,
            priority: task.priority,
            attachments: task.attachments,
            new_attachments: [],
          }

          return
        }

        // ELSE, INSERT
        const queryParams = await new URLSearchParams(window.location.search)
        const filterStatus = await queryParams.get('status') ?? ''
        // insert when currently in todo or 'all' list
        if (filterStatus != 'is_completed' && filterStatus != 'is_archived') {
          await this.tasks.unshift(this.task)
          await this.tasks.pop()

        }

      } catch (e) {
        console.log(e.message)
      }
    },
    async removeFromList(taskId) {
      const updatedList = this.tasks.filter((item, i) => item.id != taskId)

      this.tasks = [
        ...updatedList
      ]
    },
    async toggleModal(taskId) {
      this.task = await null

      if (!taskId) {
        this.showModal = await !this.showModal
        return
      }

      await this.getTaskRecord(taskId)
        .then((res) => {
          this.showModal = !this.showModal
        })
    },
    composeStatusDisplayData(taskPayload) {
      let task = taskPayload ?? this.task
      let status = task?.is_archived ? "archived" : false

      status = status ? status : (task?.is_completed ? "completed" : false)
      status = status ? status : "todo"

      switch(status) {
        case 'archived':
          return {
            label: 'ARCHIVED',
            date: task?.archived_at,
            color: 'brown',
          }
        case 'completed':
          return {
            label: 'COMPLETED',
            date: task?.completed_at,
            color: 'green',
          }
        default:
          return {
            label: 'TODO',
            date: task?.due_date ?? "",
            color: 'amber'
          }
      }
    }
  },
});
