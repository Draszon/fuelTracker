<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm, router, usePage, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted, watch } from 'vue';

const props = defineProps({
  costs: Object,
  travelDatas: Object,
  carDatas: Object,
});

const page = usePage();
const flashMessage = computed(() => page.props.flash?.message);
const message = ref(flashMessage.value);
const selectedCarId = ref(null);

const dailyTravelForm = useForm({
  car_id: '',
  date: '',
  direction: '',
  distance: '',
});

const storeTravelData = () => {
  dailyTravelForm.post('/store-travel-data', {
    preserveScroll: true,
    onSuccess: () => {
      dailyTravelForm.reset();
    }
  })
}

const fuelPriceForm = useForm({
  fuel_price: '',
});

//havi üzemanyag árának frissítése
const updateFuelPrice = () => {
  fuelPriceForm.put('/update-fuel-price', {
    preserveScroll: true,
    onSuccess: () => {
      fuelPriceForm.reset();
    }
  });
}

const amortizationPriceForm = useForm({
  amortization_price: '',
});

//amortizációs díj frissítése
const updateAmortizationPrice = () => {
  amortizationPriceForm.put('/update-amortization', {
    preserveScroll: true,
    onSuccess: () => {
      amortizationPriceForm.reset();
    }
  });
}

//adatok szűrése a kiválasztott kocsira
const filteredDatas = computed(() => {
  if (!selectedCarId.value) return props.travelDatas;
  return props.travelDatas.filter(f => f.id === selectedCarId.value);
});
</script>

<template>
<Head>
  <title>Útiköltség kalkulátor</title>
</Head>

<PublicLayout>
<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">

      <h2 class="font-bold text-2xl mb-2">Havi üzemanyagköltség kiszámítása</h2>
      <h2 class="text-red-500" v-if="message">{{ message }}</h2>
      <form @submit.prevent="updateFuelPrice" class="my-5">
        <h3 class="text-red-500 font-bold mb-2">Nav szerinti üzemanyagár jelenleg: {{ costs[0].fuel_price }} Ft</h3>
        <h3 class="text-red-500 font-bold mb-2">Amortizációs költség jelenleg: {{ costs[0].amortization_price }} Ft/km</h3>

        <div class="flex flex-col mb-5">
          <label for="monthly-fuel-cost">Havi üzemanyagár:</label>
          <input type="number" name="monthly-fuel-cost" id="monthly-fuel-cost" v-model="fuelPriceForm.fuel_price"
          class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out">
        </div>

        <button type="submit" class="transition ease-in-out delay-150 text-white
          rounded py-2 px-10 bg-gray-500 hover:bg-gray-700">Frissítés</button>
      </form>

      <form @submit.prevent="updateAmortizationPrice">
        <div class="flex flex-col mb-5">
          <label for="amortization-cost">Kilométerenkénti amortizációs költség:</label>
          <input type="number" name="monthly-fuel-cost" id="amortization-cost" v-model="amortizationPriceForm.amortization_price"
          class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out">
        </div>

        <button type="submit" class="transition ease-in-out delay-150 text-white
          rounded py-2 px-10 bg-gray-500 hover:bg-gray-700">Frissítés</button>
      </form>
    </div>
  </div>
</section>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">

      <h2 class="font-bold text-xl mb-2">Napi adatok feltöltése</h2>

      <div class="flex flex-wrap">
        <div class="mb-10 sm:mb-0 basis-1/2">
          <form class="mt-5" @submit.prevent="storeTravelData">

            <div class="flex flex-col mb-5">
              <label for="car">Kocsi kiválasztása</label>
              <select required id="car" v-model="dailyTravelForm.car_id"
                class="rounded-lg border-gray-200 shadow-none max-w-52
                focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
                focus:shadow-lg transition ease-in-out"
              >
                <option v-for="carData in carDatas" :key="carDatas.id" :value="carDatas.id">{{ carData.name }}</option>
              </select>
            </div>

            <div class="flex flex-col mb-5">
              <label for="date">Dátum</label>
              <input type="date" required id="date" v-model="dailyTravelForm.date"
                class="rounded-lg border-gray-200 shadow-none max-w-80
                focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
                focus:shadow-lg transition ease-in-out"
              >
            </div>

            <div class="flex flex-col mb-5">
              <label for="direction">Útirány (honnan - hova)</label>
              <input type="text" required id="direction" v-model="dailyTravelForm.direction"
                class="rounded-lg border-gray-200 shadow-none max-w-80
                focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
                focus:shadow-lg transition ease-in-out"
              >
            </div>

            <div class="flex flex-col mb-5">
              <label for="distance">Megtett táv (km)</label>
              <input type="number" required id="distance" v-model="dailyTravelForm.distance"
                class="rounded-lg border-gray-200 shadow-none max-w-80
                focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
                focus:shadow-lg transition ease-in-out"
              >
            </div>
            <button type="submit" class="transition ease-in-out delay-150 text-white
              rounded py-2 px-10 bg-gray-500 hover:bg-gray-700">Feltöltés</button>
          </form>
        </div>

        <div class="basis-1/2 flex flex-col gap-2">
          <div class="flex gap-2 min-w-80 mb-5">
            <form>
              <div class="flex flex-col sm:flex-row sm:gap-5">
                <div>
                  <p>Hónap kiválasztása</p>
                  <select required id="car"
                    class="rounded-lg border-gray-200 shadow-none max-w-52
                    focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
                    focus:shadow-lg transition ease-in-out mb-5"
                  >
                    <option>Január</option>
                    <option>Február</option>
                    <option>Március</option>
                  </select>
                </div>  
              
                <div>
                  <p>Kocsi kiválasztása</p>
                  <select required id="car"
                    class="rounded-lg border-gray-200 shadow-none max-w-52
                    focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
                    focus:shadow-lg transition ease-in-out mb-5"
                  >
                    <option>Suzu</option>
                    <option>Adi</option>
                  </select>
                </div>
                </div>
              <button type="submit" class="transition ease-in-out delay-150 text-white
              rounded py-2 px-10 bg-gray-500 hover:bg-gray-700">Szűrés</button>
            </form>
          </div>

          <div class="flex gap-2 min-w-80">
            <p>Havi össz. km =</p>
            <p class="font-bold">435 km</p>
          </div>

          <div class="flex gap-2 min-w-52">
            <p>x 15 Ft =</p>
            <p class="font-bold">12650 Ft</p>
          </div>

          <div class="flex gap-2 min-w-52">
            <p>Havi teljes költség =</p>
            <p class="font-bold">43783 Ft</p>
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
        <div class="mb-5">
          <h2 class="font-semibold mb-2">Szűrés kocsira</h2>
          <select required id="car" v-model="selectedCarId"
          class="rounded-lg border-gray-200 shadow-none max-w-80
          focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
          focus:shadow-lg transition ease-in-out w-56">
            <option v-for="carData in carDatas" :key="carData.id" :value="carData.id">{{ carData.name }}</option>
          </select>
          <button @click="selectedCarId = null"
          class="transition ease-in-out delay-150 text-white
          rounded py-2 px-10 bg-gray-500 hover:bg-gray-700 my-5 sm:ml-5"
          >Szűrő törlése</button>
        </div>
        
        <div class="min-w-max bg-gray-200 rounded border-b-2 border-gray-300 shadow-md">
          <div class="h-10 flex justify-center items-center">
            <ul class="flex flex-row gap-5 text-center font-medium">
              <li class="flex-none w-32">Dátum</li>
              <li class="flex-none w-32">Kocsi</li>
              <li class="flex-none w-64">Honnan - Hova</li>
              <li class="flex-none w-32">Megtett táv (km)</li>
              <li class="flex-none w-32">Útiköltség</li>
              <li class="flex-none w-32">Üzemanyagár</li>
              <li class="flex-none w-32">Műveletek</li>
            </ul>
          </div>
        </div>

        <div class="min-w-max border-b border-gray-300">
          <div v-for="filteredData in filteredDatas" :key="filteredData.id"
            class="flex justify-center items-center border-b py-2">
            <ul class="flex flex-row gap-5 text-center font-medium">
              <li class="flex justify-center items-center w-32">{{ filteredData.date }}</li>
              <li class="flex justify-center items-center w-32">{{ filteredData.car.name }}</li>
              <li class="flex justify-center items-center w-64">{{ filteredData.direction }}</li>
              <li class="flex justify-center items-center w-32">{{ filteredData.distance }} km</li>
              <li class="flex justify-center items-center w-32">{{ filteredData.travel_expenses }} Ft</li>
              <li class="flex justify-center items-center w-32">{{ filteredData.fuel_costs }} Ft/l</li>
              <li class="w-32 flex justify-center items-center gap-5">
                <button
                  class="px-2 h-8 rounded bg-red-500 text-white">Törlés</button>
                
                <button
                  class="py-1 h-8 px-2 rounded bg-gray-500 text-white">Szerk.</button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</PublicLayout>
</template>