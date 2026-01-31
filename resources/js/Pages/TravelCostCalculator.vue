<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm, router, usePage, Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
  costs: Object,
  travelDatas: Object,
  carDatas: Object,
  monthlyKm: Number,
  amortizationCost: Number,
  monthlyCostSum: Number
});

const page = usePage();
const flashMessage = computed(() => page.props.flash?.message);
const message = ref(flashMessage.value);
const selectedCarId = ref(null);
let editActive = ref(false);
let selectedMonth = ref(null);
let selectedCar = ref(null);
const months = ref({
  'Január': '01',
  'Február': '02',
  'Március': '03',
  'Április': '04',
  'Május': '05',
  'Június': '06',
  'Július': '07',
  'Augusztus': '08',
  'Szeptember': '09',
  'Október': '10',
  'November': '11',
  'December': '12'
});

watch(flashMessage, (val) => {
  if (val) {
    message.value = val;
    setTimeout(() => {
      message.value = null;
    }, 3000);
  }
});

const filterCostData = () => {
  const params = {};
  if (selectedMonth.value) params.month = selectedMonth.value;
  if (selectedCar.value) params.car = selectedCar.value;

  router.visit('/filtered-data', {
    data: {
      params
    },
    preserveScroll: true
  });
}

const dailyTravelForm = useForm({
  id: null,
  car_id: '',
  date: '',
  direction: '',
  distance: '',
});

const updateSelectedData = (id) => {
  dailyTravelForm.put(`/update-travel-data/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      dailyTravelForm.reset();
      editActive.value = false;
    },
    error: (errors) => {
      console.log('Hiba az adatok frissítése közben: ', errors);
    }
  });
}

const loadSelectedData = (selected) => {
  editActive.value = true;
  dailyTravelForm.reset();
  dailyTravelForm.id = selected.id,
  dailyTravelForm.car_id = selected.car_id,
  dailyTravelForm.date = selected.date,
  dailyTravelForm.direction = selected.direction,
  dailyTravelForm.distance = selected.distance
}

const cancelEdit = () => {
  dailyTravelForm.reset();
  editActive.value = false;
}

const storeTravelData = () => {
  dailyTravelForm.post('/store-travel-data', {
    preserveScroll: true,
    onSuccess: () => {
      dailyTravelForm.reset();
    },
    onError: (errors) => {
      console.log('Hiba történt az adatok rögzítése közben: ', errors);
    }
  })
}

const deleteData = (id) => {
  router.delete(`/delete-travel-data/${id}`, {
    preserveScroll: true
  });
}

const fuelPriceForm = useForm({
  fuel_price: '',
});

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

const updateAmortizationPrice = () => {
  amortizationPriceForm.put('/update-amortization', {
    preserveScroll: true,
    onSuccess: () => {
      amortizationPriceForm.reset();
    }
  });
}

const filteredDatas = computed(() => {
  if (!selectedCarId.value) return props.travelDatas;
  return props.travelDatas.filter(f => f.car_id === selectedCarId.value);
});

// Pagination
const currentPage = ref(1);
const itemsPerPage = 30;

const totalPages = computed(() => Math.ceil(filteredDatas.value.length / itemsPerPage));

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredDatas.value.slice(start, end);
});

watch(selectedCarId, () => {
  currentPage.value = 1;
});

function goToPage(pageNum) {
  if (pageNum >= 1 && pageNum <= totalPages.value) {
    currentPage.value = pageNum;
  }
}

const visiblePages = computed(() => {
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;
  
  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    if (current <= 3) {
      pages.push(1, 2, 3, 4, '...', total);
    } else if (current >= total - 2) {
      pages.push(1, '...', total - 3, total - 2, total - 1, total);
    } else {
      pages.push(1, '...', current - 1, current, current + 1, '...', total);
    }
  }
  return pages;
});

// Summary stats
const travelCount = computed(() => filteredDatas.value.length);
const totalDistance = computed(() => filteredDatas.value.reduce((sum, t) => sum + Number(t.distance), 0));
const totalTravelCost = computed(() => filteredDatas.value.reduce((sum, t) => sum + Number(t.travel_expenses), 0));
</script>

<template>
<Head>
  <title>Útiköltség kalkulátor</title>
</Head>

<PublicLayout>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

  <!-- Beállítások szekció -->
  <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Költség beállítások
      </h2>
    </div>
    <div class="p-6">
      <!-- Flash üzenet -->
      <div v-if="message" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
        <p class="font-medium text-red-600 flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ message }}
        </p>
      </div>

      <!-- Aktuális értékek -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 border border-amber-100">
          <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">NAV szerinti üzemanyagár</p>
          <p class="text-2xl font-bold text-gray-800 mt-1">{{ costs[0]?.fuel_price ?? 0 }} <span class="text-base font-normal text-gray-500">Ft/l</span></p>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 border border-amber-100">
          <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Amortizációs költség</p>
          <p class="text-2xl font-bold text-gray-800 mt-1">{{ costs[0]?.amortization_price ?? 0 }} <span class="text-base font-normal text-gray-500">Ft/km</span></p>
        </div>
      </div>

      <!-- Frissítő űrlapok -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <form @submit.prevent="updateFuelPrice" class="space-y-4">
          <div class="space-y-2">
            <label for="monthly-fuel-cost" class="block text-sm font-medium text-gray-700">Üzemanyagár frissítése (Ft/l)</label>
            <input type="number" id="monthly-fuel-cost" v-model="fuelPriceForm.fuel_price" placeholder="480"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20
              focus:bg-white transition-all duration-200">
          </div>
          <button type="submit" 
            class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium
            rounded-xl transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Frissítés
          </button>
        </form>

        <form @submit.prevent="updateAmortizationPrice" class="space-y-4">
          <div class="space-y-2">
            <label for="amortization-cost" class="block text-sm font-medium text-gray-700">Amortizáció frissítése (Ft/km)</label>
            <input type="number" id="amortization-cost" v-model="amortizationPriceForm.amortization_price" placeholder="15"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20
              focus:bg-white transition-all duration-200">
          </div>
          <button type="submit" 
            class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium
            rounded-xl transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Frissítés
          </button>
        </form>
      </div>
    </div>
  </section>

  <!-- Napi adatok feltöltése + Havi összesítő -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Napi adatok form -->
    <section class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="bg-gradient-to-r from-teal-500 to-emerald-500 px-6 py-4">
        <h2 class="text-lg font-semibold text-white flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          {{ editActive ? 'Útadat szerkesztése' : 'Napi útadat felvétele' }}
        </h2>
      </div>
      <div class="p-6">
        <form @submit.prevent="editActive ? updateSelectedData(dailyTravelForm.id) : storeTravelData()" class="space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label for="car" class="block text-sm font-medium text-gray-700">Jármű</label>
              <select required id="car" v-model="dailyTravelForm.car_id"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20
                focus:bg-white transition-all duration-200">
                <option value="" disabled>Válassz járművet...</option>
                <option v-for="carData in carDatas" :key="carData.id" :value="carData.id">{{ carData.name }}</option>
              </select>
            </div>

            <div class="space-y-2">
              <label for="date" class="block text-sm font-medium text-gray-700">Dátum</label>
              <input type="date" required id="date" v-model="dailyTravelForm.date"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20
                focus:bg-white transition-all duration-200">
            </div>

            <div class="space-y-2">
              <label for="direction" class="block text-sm font-medium text-gray-700">Útirány (honnan - hova)</label>
              <input type="text" required id="direction" v-model="dailyTravelForm.direction" placeholder="Budapest - Debrecen"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20
                focus:bg-white transition-all duration-200">
            </div>

            <div class="space-y-2">
              <label for="distance" class="block text-sm font-medium text-gray-700">Megtett táv (km)</label>
              <input type="number" required id="distance" v-model="dailyTravelForm.distance" placeholder="230"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20
                focus:bg-white transition-all duration-200">
            </div>
          </div>

          <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit" 
              class="px-8 py-3 bg-teal-500 hover:bg-teal-600 text-white font-medium
              rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              {{ editActive ? 'Frissítés' : 'Feltöltés' }}
            </button>
            <button v-if="editActive" type="button" @click="cancelEdit"
              class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium
              rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Mégse
            </button>
          </div>
        </form>
      </div>
    </section>

    <!-- Havi összesítő -->
    <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
        <h2 class="text-lg font-semibold text-white flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          Havi összesítő
        </h2>
      </div>
      <div class="p-6 space-y-6">
        <!-- Szűrők -->
        <form @submit.prevent="filterCostData" class="space-y-4">
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Hónap</label>
            <select v-model="selectedMonth"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-2.5 px-4 text-sm
              focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20
              focus:bg-white transition-all duration-200">
              <option :value="null">Válassz hónapot...</option>
              <option v-for="(value, month) in months" :key="value" :value="value">{{ month }}</option>
            </select>
          </div>
          
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Jármű</label>
            <select v-model="selectedCar"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-2.5 px-4 text-sm
              focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20
              focus:bg-white transition-all duration-200">
              <option :value="null">Válassz járművet...</option>
              <option v-for="carData in carDatas" :key="carData.id" :value="carData.id">{{ carData.name }}</option>
            </select>
          </div>

          <div class="flex gap-2">
            <button type="submit" 
              class="flex-1 px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white font-medium text-sm
              rounded-xl transition-all duration-200">
              Szűrés
            </button>
            <button type="button" @click="router.visit('/travel-cost-calculator', {preserveScroll: true});"
              class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm
              rounded-xl transition-all duration-200">
              Törlés
            </button>
          </div>
        </form>

        <!-- Eredmények -->
        <div class="space-y-3 pt-4 border-t border-gray-100">
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Havi össz. km</span>
            <span class="text-lg font-bold text-gray-800">{{ monthlyKm ?? 0 }} km</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Amortizáció (× {{ costs[0]?.amortization_price ?? 15 }} Ft)</span>
            <span class="text-lg font-bold text-gray-800">{{ amortizationCost ?? 0 }} Ft</span>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <span class="text-sm font-medium text-gray-700">Havi teljes költség</span>
            <span class="text-xl font-bold text-indigo-600">{{ (monthlyCostSum ?? 0).toLocaleString('hu-HU') }} Ft</span>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Útadatok listája -->
  <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Útadatok
      </h2>
    </div>
    <div class="p-6">
      <!-- Szűrő -->
      <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
          <div class="flex items-center gap-2 text-sm font-medium text-gray-700">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Szűrés:
          </div>
          <select v-model="selectedCarId"
            class="w-full sm:w-56 rounded-xl border-gray-200 bg-white py-2.5 px-4 text-sm
            focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20
            transition-all duration-200">
            <option :value="null">Összes jármű</option>
            <option v-for="carData in carDatas" :key="carData.id" :value="carData.id">{{ carData.name }}</option>
          </select>
          <button v-if="selectedCarId" @click="selectedCarId = null"
            class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium text-sm
            rounded-xl transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Szűrő törlése
          </button>
        </div>
      </div>

      <!-- Táblázat -->
      <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full">
          <thead>
            <tr class="bg-gradient-to-r from-gray-100 to-gray-50">
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dátum</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jármű</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Útirány</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Távolság</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Üzemanyagár</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Útiköltség</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Műveletek</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="filteredData in paginatedData" :key="filteredData.id"
              class="hover:bg-gray-50 transition-colors duration-150">
              <td class="px-4 py-3 text-sm text-gray-800 whitespace-nowrap">{{ filteredData.date }}</td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded-lg">
                  {{ filteredData.car.name }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-800">{{ filteredData.direction }}</td>
              <td class="px-4 py-3 text-sm text-gray-800 text-right whitespace-nowrap">
                <span class="font-medium">{{ filteredData.distance }}</span> <span class="text-gray-500">km</span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-800 text-right whitespace-nowrap">
                {{ filteredData.fuel_costs }} Ft/l
              </td>
              <td class="px-4 py-3 text-right whitespace-nowrap">
                <span class="px-2.5 py-1 bg-teal-100 text-teal-700 text-xs font-bold rounded-lg">
                  {{ Number(filteredData.travel_expenses).toLocaleString('hu-HU') }} Ft
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex justify-center gap-2">
                  <button @click="loadSelectedData(filteredData)"
                    class="p-2 text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-all duration-200"
                    title="Szerkesztés">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button @click="deleteData(filteredData.id)"
                    class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all duration-200"
                    title="Törlés">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Üres állapot -->
        <div v-if="filteredDatas.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
          </svg>
          <p class="text-gray-500">Nincs útadat</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-500">
          Összesen <span class="font-semibold text-gray-700">{{ filteredDatas.length }}</span> rekord, 
          <span class="font-semibold text-gray-700">{{ totalPages }}</span> oldal
        </div>
        <nav class="flex items-center gap-1">
          <button @click="goToPage(1)" :disabled="currentPage === 1"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
          </button>
          <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          
          <template v-for="pageNum in visiblePages" :key="pageNum">
            <span v-if="pageNum === '...'" class="px-2 text-gray-400">...</span>
            <button v-else @click="goToPage(pageNum)"
              :class="[
                'min-w-[40px] h-10 rounded-lg font-medium transition-all duration-200',
                currentPage === pageNum 
                  ? 'bg-teal-500 text-white shadow-sm' 
                  : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'
              ]">
              {{ pageNum }}
            </button>
          </template>

          <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
          <button @click="goToPage(totalPages)" :disabled="currentPage === totalPages"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
          </button>
        </nav>
      </div>

      <!-- Összesítés -->
      <div v-if="filteredDatas.length > 0" class="mt-6 grid grid-cols-3 gap-2 sm:gap-4">
        <div class="bg-gradient-to-br from-teal-50 to-emerald-50 rounded-xl p-3 sm:p-4 border border-teal-100">
          <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">Utak száma</p>
          <p class="text-lg sm:text-2xl font-bold text-gray-800 mt-1">{{ travelCount }} <span class="text-sm sm:text-base font-normal text-gray-500">db</span></p>
        </div>
        <div class="bg-gradient-to-br from-teal-50 to-emerald-50 rounded-xl p-3 sm:p-4 border border-teal-100">
          <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">Össz. távolság</p>
          <p class="text-lg sm:text-2xl font-bold text-gray-800 mt-1 truncate">{{ totalDistance.toLocaleString('hu-HU') }} <span class="text-sm sm:text-base font-normal text-gray-500">km</span></p>
        </div>
        <div class="bg-gradient-to-br from-teal-50 to-emerald-50 rounded-xl p-3 sm:p-4 border border-teal-100">
          <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">Össz. költség</p>
          <p class="text-lg sm:text-2xl font-bold text-gray-800 mt-1 truncate">{{ totalTravelCost.toLocaleString('hu-HU') }} <span class="text-sm sm:text-base font-normal text-gray-500">Ft</span></p>
        </div>
      </div>
    </div>
  </section>

</div>

</PublicLayout>
</template>