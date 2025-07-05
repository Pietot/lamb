<template>
  <div :class="cardClasses">
    <slot />
  </div>
</template>

<script>
export default {
  name: "BaseCard",
  props: {
    padding: {
      type: String,
      default: "md",
      validator: (value) => ["none", "sm", "md", "lg"].includes(value),
    },
  },
  computed: {
    cardClasses() {
      const baseClasses =
        "bg-white rounded-lg shadow-md border border-gray-200";

      const paddingClasses = {
        none: "",
        sm: "p-4",
        md: "p-6",
        lg: "p-8",
      };

      return [baseClasses, paddingClasses[this.padding]].join(" ");
    },
  },
};
</script>
