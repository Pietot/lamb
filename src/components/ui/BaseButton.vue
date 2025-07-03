<template>
  <button
    :class="buttonClasses"
    :disabled="disabled || isLoading"
    v-bind="$attrs"
    @click="$emit('click', $event)"
  >
    <Loader2 v-if="isLoading" class="w-4 h-4 mr-2 animate-spin" />
    <component v-else-if="icon" :is="icon" class="w-4 h-4 mr-2" />
    <slot />
  </button>
</template>

<script>
import { Loader2 } from "lucide-vue-next";

export default {
  name: "BaseButton",
  components: { Loader2 },
  inheritAttrs: false,
  emits: ["click"],
  props: {
    variant: {
      type: String,
      default: "primary",
      validator: (value) =>
        ["primary", "secondary", "tertiary", "danger"].includes(value),
    },
    size: {
      type: String,
      default: "md",
      validator: (value) => ["sm", "md", "lg"].includes(value),
    },
    isLoading: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    icon: {
      type: Object,
      default: null,
    },
  },
  computed: {
    buttonClasses() {
      const baseClasses =
        "inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed";

      const variantClasses = {
        primary:
          "bg-turquoise text-white hover:bg-opacity-90 focus:ring-turquoise shadow-md hover:shadow-lg",
        secondary:
          "border border-turquoise text-turquoise hover:bg-turquoise hover:text-white focus:ring-turquoise",
        tertiary: "text-turquoise hover:bg-gray-100 focus:ring-turquoise",
        danger:
          "bg-red-500 text-white hover:bg-red-600 focus:ring-red-500 shadow-md hover:shadow-lg",
      };

      const sizeClasses = {
        sm: "px-3 py-1.5 text-sm",
        md: "px-4 py-2 text-sm",
        lg: "px-6 py-3 text-base",
      };

      return [
        baseClasses,
        variantClasses[this.variant],
        sizeClasses[this.size],
      ].join(" ");
    },
  },
};
</script>
