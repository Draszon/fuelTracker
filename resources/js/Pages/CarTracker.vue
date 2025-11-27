<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
  carDatas: Array
});

const addNewCar = ref(false);


</script>

<template>
<PublicLayout>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <h2 class="font-bold text-2xl mb-5">Gépjárművek</h2>

      <div class="flex flex-col sm:flex-row gap-5">
        <div v-for="carData in carDatas"
          class="flex flex-col w-[300px] gap-5 shadow-lg rounded-md p-7">
          <h2 class="font-bold text-xl">{{ carData.name }}</h2>
          <div>
            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Típusa:</p>
              <p>{{ carData.car_type }}</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Rendszám:</p>
              <p>{{ carData.licence_plate }}</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Gyártás éve:</p>
              <p>{{ carData.year }}</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Olajcsere ciklus (km):</p>
              <p>{{ carData.oil_change_cycle_km }}</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Olajcsere ciklus (év):</p>
              <p>{{ carData.oil_change_cycle_year }}</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Fékolaj csere ciklus (év):</p>
              <p>{{ carData.break_oil_cycle_km }}</p>
            </div>
          </div>
          <div class="flex flex-col gap-5">
            <button class="transition ease-in-out delay-150 text-white
              rounded py-2 px-10 bg-gray-500 hover:bg-gray-700">Módosítás</button>

            <button class="transition ease-in-out delay-150 text-white
              rounded py-2 px-10 bg-red-500">Törlés</button>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</section>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <h2 class="font-bold mb-2">Új gépjármű feltöltése</h2>
    </div>
  </div>
</section>

</PublicLayout>
</template>