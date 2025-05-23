<template>
  <div class="w-full">
    <label 
      v-if="label" 
      :for="inputId" 
      class="block text-sm font-medium text-gray-700 mb-2"
    >
      {{ label }}
    </label>
    <div class="relative">
      <component 
        v-if="icon" 
        :is="icon" 
        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" 
      />
      <input
        :id="inputId"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="inputClasses"
        v-bind="$attrs"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur', $event)"
        @focus="$emit('focus', $event)"
      />
    </div>
    <p v-if="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </p>
  </div>
</template>

<script>
export default {
  name: 'BaseInput',
  inheritAttrs: false,
  emits: ['update:modelValue', 'blur', 'focus'],
  props: {
    modelValue: {
      type: [String, Number],
      default: ''
    },
    type: {
      type: String,
      default: 'text'
    },
    label: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: ''
    },
    disabled: {
      type: Boolean,
      default: false
    },
    error: {
      type: String,
      default: ''
    },
    icon: {
      type: Object,
      default: null
    }
  },
  computed: {
    inputId() {
      return `input-${Math.random().toString(36).substr(2, 9)}`
    },
    inputClasses() {
      const baseClasses = 'block w-full px-3 py-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-turquoise focus:border-turquoise disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200'
      const iconClasses = this.icon ? 'pl-10' : ''
      const errorClasses = this.error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300'
      
      return [baseClasses, iconClasses, errorClasses].join(' ')
    }
  }
}
</script>