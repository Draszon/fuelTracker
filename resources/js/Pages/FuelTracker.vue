<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, watch } from 'vue';

//props-ban megkapja a backend-től az adatokat
const props = defineProps({
  fuelDatas: Array,
  carDatas: Array
});

const page = usePage();
const flashMessage = computed(() => page.props.flash?.message);
const message = ref(flashMessage.value);

let editActive = false;
let btnText = {
  upload: 'Feltöltés',
  update: 'Frissítés'
}

watch(flashMessage, (val) => {
  if (val) {
    message.value = val;
    setTimeout(() => {
      message.value = null;
    }, 3000);
  }
});

//a form mezői amit később feldolgoz
let form = useForm({
  id: null,
  car_id: '',
  date: '',
  name: '',
  quantity: '',
  km: '',
  consumption: '',
  money: ''
});

//a form mezőibe betöltött adatokat http kérésen keresztül
//elküldi a backendnek, kiszámolja a fogyasztást, majd
//törli a form mezőit
function store() {
  form.consumption = (form.quantity / form.km) * 100;
  form.post('/fuel-store', {
    preserveScroll: true, //megakadályozza, hogy az oldal tetejére menjen vissza
    onSuccess: () => {
      form.reset();
    }
  });
}

//a kiválasztott eleme id-ját elküldi backendnek törlésre
function deleteFuelData(id) {
  router.delete(`/fuel-delete/${id}`, {
    preserveScroll: true
  });
}

//visszatölti a formba a kiválasztott elemet, a gomb feliratát
//feltöltésről frissítésre módosítja
function loadSelectedData(selected) {
  editActive = true;
  form.reset();
  form.id = selected.id;
  form.car_id = selected.car_id;
  form.date = selected.date;
  form.name = selected.name;
  form.quantity = selected.quantity;
  form.km = selected.km;
  form.consumption = selected.consumption;
  form.money = selected.money;  
}

//elküldi a backendnek a frissíteni kívánt elem adatait miután
//kiszámolta a fogyasztást
function update(id) {
  form.consumption = (form.quantity / form.km) * 100;
  form.put(`/fuel-update/${id}`, {
    preserveScroll: true,
    //ha kész, reseteli a form mezőit és visszállítja a gomb szövegét
    //feltöltésre
    onSuccess: () => {
      form.reset();
      editActive = false;
    }
  });
}
</script>

<template>
<Head>
  <title>Üzemanyag</title>
</Head>

<PublicLayout>

<section class="my-10">
  <div class="bg-white py-10 rounded-md shadow-sm w-full max-w-[1280px] xl:mx-auto">
    <div class="px-2 xl:px-10">
      <h2 class="font-bold text-2xl mb-2">Üzemanyag adatok feltöltése / módosítása:</h2>

      <p v-if="message" class="font-medium text-red-500">
        {{ message }}
      </p>

      <form @submit.prevent="editActive ? update(form.id) : store()">

        <div class="flex flex-col mb-5">
          <label for="date">Dátum</label>
          <input type="date" required v-model="form.date" id="date"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

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
          <label for="name">Töltőállomás neve</label>
          <input type="text" placeholder="MOL" required v-model="form.name" id="name"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="quantity">Mennyiség (l)</label>
          <input type="number" placeholder="25" required step="0.01" v-model="form.quantity" id="quantity"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="km">Megtett táv (km)</label>
          <input type="number" placeholder="560" required v-model="form.km" id="km"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <div class="flex flex-col mb-5">
          <label for="money">Összeg (Ft)</label>
          <input type="number" placeholder="21600" required v-model="form.money" id="money"
            class="rounded-lg border-gray-200 shadow-none max-w-80
            focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500
            focus:shadow-lg transition ease-in-out"
          >
        </div>

        <button type="submit"
          class="transition ease-in-out delay-150 text-white
          rounded py-2 px-10 bg-gray-500 hover:bg-gray-700"
        >{{ editActive ? btnText.update : btnText.upload }}</button>
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
          <div v-for="fuelData in fuelDatas" :key="fuelData.id"
            class="h-10 flex justify-center items-center border-b">
            <ul class="flex flex-row gap-5 text-center font-medium">
              <li class="flex-none w-32">{{ fuelData.date }}</li>
              <li class="flex-none w-32">{{ fuelData.car.name }}</li>
              <li class="flex-none w-32">{{ fuelData.name }}</li>
              <li class="flex-none w-32">{{ fuelData.quantity }} l</li>
              <li class="flex-none w-32">{{ fuelData.km }} km</li>
              <li class="flex-none w-32">{{ Number(fuelData.consumption).toFixed(1) }} l/100km</li>
              <li class="flex-none w-32">{{ fuelData.money }} Ft</li>
              <li class="w-32 flex gap-5">
                <button @click="deleteFuelData(fuelData.id)"
                  class="py-1 px-2 rounded bg-red-500 text-white">Törlés</button>
                
                <button @click="loadSelectedData(fuelData)"
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