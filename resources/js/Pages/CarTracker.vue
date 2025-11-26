<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
  carDatas: Array
});

onMounted(() => {
  console.log(props.carDatas);
});

</script>

<template>
<PublicLayout>
<h1>valami</h1>
<div v-for="carData in carDatas">
  <p>{{ carData.name }}</p>
  <p>{{ carData.licence_plate }}</p>
  <p>{{ carData.car_type }}</p>
  <p>{{ carData.year }}</p>
  <p>{{ carData.oil_change_cycle_km }}</p>
  <p>{{ carData.oil_change_cycle_year }}</p>
  <p>{{ carData.break_oil_cycle_km }}</p>
</div>

</PublicLayout>
</template>