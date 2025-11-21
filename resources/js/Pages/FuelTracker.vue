<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, onMounted } from 'vue';

const props = defineProps({
  fuelDatas: Array
});
</script>

<template>
<PublicLayout>
<div>
  <ul>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
  </ul>
</div>
</PublicLayout>
</template>