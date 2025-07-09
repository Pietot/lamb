<template>
  <span :class="badgeClasses">
    <slot />
  </span>
</template>

<script>
  export default {
    name: "BaseBadge",
    props: {
      variant: {
        type: String,
        default: "neutral",
        validator: value => ["success", "warning", "danger", "info", "neutral"].includes(value),
      },
      size: {
        type: String,
        default: "sm",
        validator: value => ["sm", "md"].includes(value),
      },
    },
    computed: {
      badgeClasses() {
        const baseClasses = "inline-flex items-center font-medium rounded-full";

        const variantClasses = {
          success: "bg-green-100 text-green-800",
          warning: "bg-yellow-100 text-yellow-800",
          danger: "bg-red-100 text-red-800",
          info: "bg-blue-100 text-blue-800",
          neutral: "bg-gray-100 text-gray-800",
        };

        const sizeClasses = {
          sm: "px-2 py-1 text-xs",
          md: "px-3 py-1 text-sm",
        };

        return [baseClasses, variantClasses[this.variant], sizeClasses[this.size]].join(" ");
      },
    },
  };
</script>
