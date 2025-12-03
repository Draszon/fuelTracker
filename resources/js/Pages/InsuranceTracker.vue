<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
  insuranceDatas: Array,
  carDatas: Array
});

let editActive = false;

const form = useForm({
  id: null,
  car_id: '',
  insturance_type: '',
  provider: '',
  cost: '',
  valid_from: '',
  valid_until: '',
  notes: ''
});

const storeInsuranceData = () => {
  form.post(`/insurance-store`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    }
  });
}

const loadSelectedInsurance = (selected) => {
  form.reset();
  editActive = true;
  form.id = selected.id;
  form.car_id = selected.car_id;
  form.insturance_type = selected.insturance_type;
  form.provider = selected.provider;
  form.cost = selected.cost;
  form.valid_from = selected.valid_from;
  form.valid_until = selected.valid_until;
  form.notes = selected.notes;
}

const updateSelectedInsurance = (id) => {
  form.put(`/insurance-update/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      editActive = false;
    }
  });
}

const deleteSelectedInsurance = (id) => {
  router.delete(`/insurance-delete/${id}`, {
    preserveScroll: true
  });
}

</script>

<template>
<Head>
  <title>Biztosítás</title>
</Head>

<PublicLayout>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <h2 class="font-bold text-2xl mb-5">Biztosítási adatok feltöltése</h2>
      
      <form @submit.prevent="editActive ? updateSelectedInsurance(form.id) : storeInsuranceData()">
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
          <label for="provider">Szolgáltató</label>
          <input type="text" placeholder="Alfa biztosító" required id="provider" v-model="form.provider"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="insurance-type">Szolgáltató</label>
          <input type="text" placeholder="Kötelező / casco" required id="insurance-type" v-model="form.insturance_type"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="cost">Biztosítás ára</label>
          <input type="number" placeholder="55000" required id="cost" v-model="form.cost"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="valid-from">Érv. kezdete</label>
          <input type="date" required id="valid-from" v-model="form.valid_from"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="valid_until">Érv. vége</label>
          <input type="date" required id="valid_from" v-model="form.valid_until"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="notes">Egyéb jegyzetek</label>
          <input type="text" placeholder="Jegyzetek" required id="notes" v-model="form.notes"
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
              <li class="flex-none w-32">Kocsi</li>
              <li class="flex-none w-32">Szolgáltató</li>
              <li class="flex-none w-32">Biztosítás ára</li>
              <li class="flex-none w-32">Érv. kezdete</li>
              <li class="flex-none w-32">Érv. vége</li>
              <li class="flex-none w-64">Egyéb jegyzetek</li>
              <li class="flex-none w-32">Műveletek</li>
            </ul>
          </div>
        </div>

        <div class="min-w-max border-b border-gray-300">
          <div v-for="insuranceData in insuranceDatas" :key="insuranceData.id"
            class="flex justify-center items-center border-b py-2">
            <ul class="flex flex-row gap-5 text-center font-medium">
              <li class="flex justify-center items-center w-32">{{ insuranceData.car.name }}</li>
              <li class="flex justify-center items-center w-32">{{ insuranceData.provider }}</li>
              <li class="flex justify-center items-center w-32">{{ insuranceData.cost }} Ft</li>
              <li class="flex justify-center items-center w-32">{{ insuranceData.valid_from }}</li>
              <li class="flex justify-center items-center w-32">{{ insuranceData.valid_until }}</li>
              <li class="flex justify-center items-center w-64">{{ insuranceData.notes }}</li>
              <li class="w-32 flex justify-center items-center gap-5">
                <button @click="deleteSelectedInsurance(insuranceData.id)"
                  class="px-2 h-8 rounded bg-red-500 text-white">Törlés</button>
                
                <button @click="loadSelectedInsurance(insuranceData)"
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