<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, onMounted, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  serviceDatas: Array,
  carDatas: Array
});

onMounted(() => {
  console.log(props.serviceDatas);
  console.log(props.carDatas);
});

</script>

<template>
<Head>
  <title>Szerviz</title>
</Head>
<PublicLayout>



</PublicLayout>
</template>