import { Notify } from 'quasar'

export const notify = (message, color, position, icon) => {
  message = message ?? 'Something went wrong'
  color = color ?? 'negative'
  position = position ?? 'top'
  icon = icon ?? 'report_problem'

  Notify.create({
    message,
    color,
    position,
    icon,
  })
}
