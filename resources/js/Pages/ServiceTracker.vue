<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, onMounted, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  serviceDatas: Array,
  carDatas: Array
});

let editActive = false;

let form = useForm({
  id: null,
  car_id: '',
  date: '',
  current_km: '',
  description: '',
  cost: ''
});

const store = () => {
  form.post('/service-store', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    }
  });
}

const destroy = (id) => {
  router.delete(`/service-delete/${id}`, {
    preserveScroll: true
  });
}

const loadSelectedService = (selected) => {
  editActive = true;
  form.reset();
  form.id = selected.id;
  form.car_id = selected.car_id;
  form.date = selected.date;
  form.current_km = selected.current_km;
  form.description = selected.description;
  form.cost = selected.cost;
}

const updateSelectedService = (id) => {
  form.put(`/service-update/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      editActive = false;
    }
  });
}

</script>

<template>
<Head>
  <title>Szerviz</title>
</Head>
<PublicLayout>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <h2 class="font-bold text-2xl mb-5">Szerviztevékenség feltöltése</h2>
      
      <form @submit.prevent="editActive ? updateSelectedService(form.id) : store()">
        <div class="flex flex-col mb-5">
          <label for="car">Válaszd ki a kocsit</label>
          <select required v-model="form.car_id" id="car"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
          <option v-for="car in carDatas" :key="car.id" :value="car.id">{{ car.name }}</option>
        </select>
        </div>

        <div class="flex flex-col mb-5">
          <label for="date">Dátum</label>
          <input type="date" placeholder="2" required id="date" v-model="form.date"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="km">KM óra állása</label>
          <input type="number" placeholder="230000" required id="km" v-model="form.current_km"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="description">Javítás leírása</label>
          <input type="text" placeholder="Olajcsere" required id="description" v-model="form.description"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="cost">Összeg</label>
          <input type="number" placeholder="13500" required id="cost" v-model="form.cost"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <button type="submit" class="transition ease-in-out delay-150 text-white
            rounded py-2 px-10 bg-gray-500 hover:bg-gray-700">{{ editActive ? 'Frissítés' : 'Feltöltés' }}</button>
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
              <li class="flex-none w-32">Kocsi</li>
              <li class="flex-none w-32">km óra állása</li>
              <li class="flex-none w-64">Javítás leírása</li>
              <li class="flex-none w-32">Összeg</li>
              <li class="flex-none w-32">Műveletek</li>
            </ul>
          </div>
        </div>

        <div class="min-w-max border-b border-gray-300">
          <div v-for="serviceData in serviceDatas" :key="serviceData.id"
            class="flex justify-center items-center border-b py-2">
            <ul class="flex flex-row gap-5 text-center font-medium">
              <li class="flex justify-center items-center w-32">{{ serviceData.date }}</li>
              <li class="flex justify-center items-center w-32">{{ serviceData.car.name }}</li>
              <li class="flex justify-center items-center w-32">{{ serviceData.current_km }} km</li>
              <li class="flex justify-center items-center w-64">{{ serviceData.description }}</li>
              <li class="flex justify-center items-center w-32">{{ serviceData.cost }} Ft</li>
              <li class="w-32 flex justify-center items-center gap-5">
                <button @click="destroy(serviceData.id)"
                  class="px-2 h-8 rounded bg-red-500 text-white">Törlés</button>
                
                <button @click="loadSelectedService(serviceData)"
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