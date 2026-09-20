export function useToast() {
  const toast = useState('app-toast', () => ({ show: false, message: '', type: 'info', _timer: null }))

  const show = (message, type = 'success', duration = 3000) => {
    toast.value.message = message
    toast.value.type = type
    toast.value.show = true
    if (toast.value._timer) clearTimeout(toast.value._timer)
    if (duration > 0) {
      toast.value._timer = setTimeout(() => {
        toast.value.show = false
        toast.value._timer = null
      }, duration)
    }
  }

  const close = () => {
    toast.value.show = false
    if (toast.value._timer) {
      clearTimeout(toast.value._timer)
      toast.value._timer = null
    }
  }

  return { toast, show, close }
}
