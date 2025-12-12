<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
  carDatas: Array
});

const page = usePage();
const flashMessage = computed(() => page.props.flash?.message);
const message = ref(flashMessage.value);
let editActive = false;
let addNewCar = ref(false);
const btnTitle = {
  'store': 'Feltöltés',
  'update': 'Frissítés'
}

let form  = useForm({
  name: '',
  licence_plate: '',
  car_type: '',
  average_fuel_consumption: '',
  year: '',
  current_km: '',
  oil_change_cycle_km: '',
  last_oil_change_km: '',
  oil_change_cycle_year: '',
  last_oil_change_date: '',
  break_oil_cycle_year: '',
  last_break_oil_change_date: '',
  inspection_valid_until: '',
  inspection_valid_from: '',
});

const store = () => {
  form.post('/car-store', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    }
  });
}

const deleteCar = (selected) => {
  router.delete(`/car-delete/${selected}`, {
    preserveScroll: true
  });
}

const loadSelectedCar = (selected) => {
  editActive = true;
  form.reset();
  form.id = selected.id,
  form.name = selected.name,
  form.licence_plate = selected.licence_plate,
  form.car_type = selected.car_type,
  form.average_fuel_consumption = selected.average_fuel_consumption,
  form.year = selected.year,
  form.current_km = selected.current_km,
  form.oil_change_cycle_km = selected.oil_change_cycle_km,
  form.last_oil_change_km = selected.last_oil_change_km,
  form.oil_change_cycle_year = selected.oil_change_cycle_year,
  form.last_oil_change_date = selected.last_oil_change_date
  form.break_oil_cycle_year = selected.break_oil_cycle_year,
  form.last_break_oil_change_date = selected.last_break_oil_change_date,
  form.inspection_valid_until = selected.inspection_valid_until,
  form.inspection_valid_from = selected.inspection_valid_from
}

const updateCar = (id) => {
  form.put(`/car-update/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    }
  });
  editActive = false;
}

</script>

<template>
<Head>
  <title>Gépjármű</title>
</Head>

<PublicLayout>

<section class="my-10" v-show="addNewCar || editActive">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <h2 class="font-bold text-2xl mb-5">Új gépjármű feltöltése</h2>
      
      <p v-if="message" class="font-medium text-red-500">
        {{ message }}
      </p>

      <form @submit.prevent="editActive ? updateCar(form.id) : store()">
      
        <div class="flex flex-col mb-5">
          <label for="name">Elnevezés:</label>
          <input type="text" placeholder="Szuzu" required v-model="form.name" id="name"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="type">Típus:</label>
          <input type="text" placeholder="Suzuki Swift" required v-model="form.car_type" id="type"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="nav-avg">NAV szerinti átlagfogyasztás:</label>
          <input type="number" placeholder="8,6" required step="0.01" v-model="form.average_fuel_consumption" id="nav-avg"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="licence-plate">Rendszám</label>
          <input type="text" placeholder="KZN-235" required v-model="form.licence_plate" id="licence-plate"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="year">Gyártás éve</label>
          <input type="number" placeholder="2008" required v-model="form.year" id="year"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="current-km">Km óra állása</label>
          <input type="number" placeholder="112544" required v-model="form.current_km" id="current-km"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="oil-change-cycle-km">Olajcsere ciklus (km)</label>
          <input type="number" placeholder="10000" required v-model="form.oil_change_cycle_km" id="oil-change-cycle-km"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="last-oil-change-cycle-km">Utolsó olajcsere (km)</label>
          <input type="number" placeholder="156223" required v-model="form.last_oil_change_km" id="last-oil-change-cycle-km"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="oil-change-cycle-year">Olajcsere ciklus (év)</label>
          <input type="number" placeholder="2" required v-model="form.oil_change_cycle_year" id="oil-change-cycle-year"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="last-oil-change-cycle-date">Utolsó olajcsere</label>
          <input type="date" placeholder="2024-05-22" required v-model="form.last_oil_change_date" id="last-oil-change-cycle-date"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="break-oil-cycle-km">Fékolaj csere ciklus (év)</label>
          <input type="number" placeholder="2" required v-model="form.break_oil_cycle_year" id="break-oil-cycle-km"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="last-break-oil-cycle-date">Utolsó fékolaj csere</label>
          <input type="date" placeholder="2024-05-22" required v-model="form.last_break_oil_change_date" id="last-break-oil-cycle-date"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="inspection-start">Műszaki érvényesség kezdete</label>
          <input type="date" required v-model="form.inspection_valid_from" id="inspection-start"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="inspection-end">Műszaki érvényesség vége</label>
          <input type="date" required v-model="form.inspection_valid_until" id="inspection-end"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <button type="submit" @click="addNewCar = false" class="transition ease-in-out delay-150 text-white
            rounded py-2 px-10 bg-gray-500 hover:bg-gray-700">{{ editActive ? btnTitle.update : btnTitle.store }}</button>
      </form>
    </div>
  </div>
</section>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <h2 class="font-bold text-2xl mb-5">Gépjárművek</h2>

      <div class="mb-5">
        <button @click="addNewCar = true" class="transition ease-in-out delay-150 text-white
          rounded py-2 px-10 bg-gray-500 hover:bg-gray-700">Gépjármű hozzáadása</button>
      </div>

      <div class="flex flex-col flex-wrap sm:flex-row gap-5">
        <div v-for="carData in carDatas"
          class="flex flex-col w-[285px] gap-5 shadow-lg rounded-md p-7">
          <h2 class="font-bold text-xl">{{ carData.name }}</h2>
          <div>
            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Típusa:</p>
              <p>{{ carData.car_type }}</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">NAV szerinti átlagfogyasztás:</p>
              <p>{{ carData.average_fuel_consumption }} l/100km</p>
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
              <p class="font-medium">Km óra állása:</p>
              <p>{{ carData.current_km }}</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Olajcsere ciklus (km):</p>
              <p>{{ carData.oil_change_cycle_km }} km</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Utolsó olajcsere (km):</p>
              <p>{{ carData.last_oil_change_km }} km</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Olajcsere ciklus (év):</p>
              <p>{{ carData.oil_change_cycle_year }} év</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Utolsó olajcsere:</p>
              <p>{{ carData.last_oil_change_date }}</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Fékolaj csere ciklus (év):</p>
              <p>{{ carData.break_oil_cycle_year }} év</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Utolsó fékolaj csere:</p>
              <p>{{ carData.last_break_oil_change_date }}</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Műszaki érvényesség kezdete:</p>
              <p>{{ carData.inspection_valid_from }}</p>
            </div>

            <div class="py-3 border-b border-gray-300">
              <p class="font-medium">Műszaki érvényesség vége:</p>
              <p>{{ carData.inspection_valid_until }}</p>
            </div>
          </div>
          <div class="flex flex-col gap-5">
            <button @click="loadSelectedCar(carData)" class="transition ease-in-out delay-150 text-white
              rounded py-2 px-10 bg-gray-500 hover:bg-gray-700">Módosítás</button>

            <button @click="deleteCar(carData.id)" class="transition ease-in-out delay-150 text-white
              rounded py-2 px-10 bg-red-500">Törlés</button>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</section>

</PublicLayout>
</template>