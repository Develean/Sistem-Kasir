<template>
  <div class="fixed top-4 inset-x-4 sm:inset-x-auto sm:right-5 z-[70] flex justify-center sm:justify-end pointer-events-none">
    <transition name="fade">
      <div v-if="toast?.show" :class="containerClass" class="pointer-events-auto max-w-sm w-full rounded-2xl p-3.5 shadow-xl text-sm font-medium text-white">
        <div class="flex items-start gap-3">
          <div class="flex-1">{{ toast?.message }}</div>
          <button @click="close" class="ml-2 rounded px-2 py-1 text-xs opacity-80 hover:opacity-100">×</button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
const toast = useState('app-toast', () => ({ show: false, message: '', type: 'info' }))

const close = () => {
  if (toast.value) {
    toast.value.show = false
  }
}

const containerClass = computed(() => {
  const type = toast.value?.type || 'info'
  if (type === 'success') return 'bg-emerald-600'
  if (type === 'error') return 'bg-rose-600'
  if (type === 'warning') return 'bg-amber-600'
  return 'bg-slate-700'
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s }
.fade-enter-from, .fade-leave-to { opacity: 0 }
</style>
