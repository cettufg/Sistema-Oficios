<template>
    <img src="@/images/logotipo.png" alt="Logotipo" :class="props.class">
</template>

<script setup>
const props = defineProps({
    class: {
        default: ''
    }
})
</script>