<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
  fuelDatas: Array
});

let form = useForm({
  date: '',
  name: '',
  quantity: '',
  km: '',
  consumption: '',
  money: ''
});

function submit() {
  form.consumption = (form.quantity / form.km) * 100;
  form.post('/fuel-store', {
    preserveScroll: true
  });
  form.reset();
}

function deleteFuelData(id) {
  router.delete(`/fuel-delete/${id}`);
}

function editFuelData(selected) {
  form.reset();
  form.date = selected.date;
  form.name = selected.name;
  form.quantity = selected.quantity;
  form.km = selected.km;
  form.money = selected.money;
}
</script>

<template>
<PublicLayout>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <h2 class="font-bold mb-2">Üzemanyag adatok feltöltése / módosítása:</h2>
      
      <form @submit.prevent="submit">
        <div class="flex flex-col mb-5">
          <label for="date">Dátum</label>
          <input type="date" required v-model="form.date"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="name">Töltőállomás neve</label>
          <input type="text" placeholder="MOL" required v-model="form.name"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="quantity">Mennyiség (l)</label>
          <input type="number" placeholder="25" required step="0.01" v-model="form.quantity"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="km">Megtett táv (km)</label>
          <input type="number" placeholder="560" required v-model="form.km"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="money">Összeg (Ft)</label>
          <input type="number" placeholder="21600" required v-model="form.money"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <button type="submit"
          class="transition ease-in-out delay-150 text-white
          rounded py-2 px-10 bg-gray-500 hover:bg-gray-700"
        >Feltölt</button>
      </form>
    </div>
  </div>
</section>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <div class="overflow-x-auto">
        <div class="min-w-max bg-gray-200 rounded border-b-2 border-gray-300 shadow-md">
          <div class="h-10 flex justify-center items-center">
            <ul class="flex flex-row gap-5 text-center font-medium">
              <li class="flex-none w-32">Dátum</li>
              <li class="flex-none w-32">Töltőállomás</li>
              <li class="flex-none w-32">Mennyiség (l)</li>
              <li class="flex-none w-32">Megtett táv (km)</li>
              <li class="flex-none w-32">Fogyasztás</li>
              <li class="flex-none w-32">Összeg</li>
              <li class="flex-none w-32">Műveletek</li>
            </ul>
          </div>
        </div>

        <div class="min-w-max border-b border-gray-300">
          <div v-for="fuelData in fuelDatas"
            class="h-10 flex justify-center items-center border-b">
            <ul class="flex flex-row gap-5 text-center font-medium">
              <li class="flex-none w-32">{{ fuelData.date }}</li>
              <li class="flex-none w-32">{{ fuelData.name }}</li>
              <li class="flex-none w-32">{{ fuelData.quantity }} l</li>
              <li class="flex-none w-32">{{ fuelData.km }} km</li>
              <li class="flex-none w-32">{{ Number(fuelData.consumption).toFixed(1) }} l/100km</li>
              <li class="flex-none w-32">{{ fuelData.money }} Ft</li>
              <li class="w-32 flex gap-5">
                <button @click="deleteFuelData(fuelData.id)"
                  class="py-1 px-2 rounded bg-red-500 text-white">Törlés</button>
                
                <button @click="editFuelData(fuelData)"
                  class="py-1 px-2 rounded bg-gray-500 text-white">Szerk.</button>
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