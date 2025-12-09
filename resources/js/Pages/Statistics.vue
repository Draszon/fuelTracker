<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
  fuelMonth: Object,
  fuelYear: Object,
  statisticsMonth: Object,
  statisticsYear: Object,
  carDatas: Array
});

const selectedCarId = ref(null);

watch(selectedCarId, (id, oldId) => {
  if(!id) {
    router.get('/', {}, {
      preserveScroll: true,
      preserveState: true
    });
    return;
  }

  router.get('/filtered-statistics', { car_id: id }, {
    preserveScroll: true,
    preserveState: true
  });
  
});


</script>

<template>
<Head>
  <title>Statisztika</title>
</Head>
<PublicLayout>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <div class="overflow-x-auto">
        <h2 class="font-bold text-2xl mb-5">Üzemanyag statisztika</h2>

        <div class="mb-5">
          <h2 class="font-semibold mb-2">Szűrés kocsira</h2>
          <select required v-model="selectedCarId" id="car"
          class="rounded-lg border-gray-200 shadow-none max-w-80
          focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
          focus:shadow-lg transition ease-in-out w-56">
            <option v-for="car in carDatas" :key="car.id" :value="car.id">{{ car.name }}</option>
          </select>
          <button @click="selectedCarId = null"
          class="transition ease-in-out delay-150 text-white
          rounded py-2 px-10 bg-gray-500 hover:bg-gray-700 ml-5"
          >Szűrő törlése</button>
        </div>

        <h2 class="font-bold text-2xl">Havi</h2>
        <div class="px-2 mb-10 rounded-lg sm:flex sm:flex-wrap sm:gap-5">
          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Össz. üzemanyag</h3>
            <p class="text-2xl">{{ fuelMonth.total_liter }} l</p>
          </div>

          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Össz. megtett km</h3>
            <p class="text-2xl">{{ fuelMonth.total_km }} km</p>
          </div>

          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Össz.  költség</h3>
            <p class="text-2xl">{{ fuelMonth.total_cost }} Ft</p>
          </div>

          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Átlagfogyasztás</h3>
            <p class="text-2xl">{{ fuelMonth.avg_consumption }} l/100km</p>
          </div>

          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Tankolások száma</h3>
            <p class="text-2xl">{{ fuelMonth.monthly_fuel_count }} db</p>
          </div>
        </div>

        <h2 class="font-bold text-2xl">Éves</h2>
        <div class="px-2 rounded-lg sm:flex sm:flex-wrap sm:gap-5">
          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Össz. üzemanyag</h3>
            <p class="text-2xl">{{ fuelYear.total_liter }} l</p>
          </div>

          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Össz. megtett km</h3>
            <p class="text-2xl">{{ fuelYear.total_km }} km</p>
          </div>

          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Össz.  költség</h3>
            <p class="text-2xl">{{ fuelYear.total_cost }} Ft</p>
          </div>

          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Átlagfogyasztás</h3>
            <p class="text-2xl">{{ fuelYear.avg_consumption }} l/100km</p>
          </div>

          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Tankolások száma</h3>
            <p class="text-2xl">{{ fuelYear.yearly_fuel_count }} db</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <div class="overflow-x-auto">
        <h2 class="font-bold text-2xl mb-5">Szerviz statisztika</h2>

        <h2 class="font-bold text-2xl">Havi</h2>
        <div class="px-2 mb-10 rounded-lg sm:flex sm:flex-wrap sm:gap-5">
          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Össz. szervizköltség</h3>
            <p class="text-2xl">{{ statisticsMonth.total_cost }} Ft</p>
          </div>

          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Javítások száma</h3>
            <p class="text-2xl">{{ statisticsMonth.service_count }} db</p>
          </div>
        </div>

        <h2 class="font-bold text-2xl">Éves</h2>
        <div class="px-2 mb-10 rounded-lg sm:flex sm:flex-wrap sm:gap-5">
          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Össz. szervizköltség</h3>
            <p class="text-2xl">{{ statisticsYear.total_cost }} Ft</p>
          </div>

          <div class="bg-gray-100 rounded-2xl shadow-sm w-full p-5 my-5 sm:max-w-[350px]">
            <h3 class="text-lg font-medium">Javítások száma</h3>
            <p class="text-2xl">{{ statisticsYear.service_count }} db</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

</PublicLayout>
</template>