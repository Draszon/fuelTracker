<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
  fuelMonth: Object,
  fuelYear: Object,
  statisticsMonth: Object,
  statisticsYear: Object,
  carDatas: Array,
  periodicMaintenances: Object
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

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

  <!-- Szűrő szekció -->
  <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        Szűrés gépjárműre
      </h2>
    </div>
    <div class="p-6">
      <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
        <select required v-model="selectedCarId" id="car"
          class="w-full sm:w-64 rounded-xl border-gray-200 bg-gray-50 py-3 px-4
          focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
          focus:bg-white transition-all duration-200 text-gray-700">
          <option :value="null" disabled>Válassz gépjárművet...</option>
          <option v-for="car in carDatas" :key="car.id" :value="car.id">{{ car.name }}</option>
        </select>
        <button @click="selectedCarId = null"
          class="w-full sm:w-auto px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium
          rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Szűrő törlése
        </button>
      </div>
    </div>
  </section>

  <!-- Időszakos karbantartások -->
  <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Időszakos karbantartások
      </h2>
    </div>
    <div class="p-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-5 border border-amber-100">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-sm font-medium text-gray-600">Következő olajcsere</h3>
          </div>
          <p class="text-2xl font-bold text-gray-800">{{ periodicMaintenances.next_oil_change_date }}</p>
        </div>

        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-5 border border-amber-100">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
            <h3 class="text-sm font-medium text-gray-600">Olajcsere km</h3>
          </div>
          <p class="text-2xl font-bold text-gray-800">{{ periodicMaintenances.next_oil_change_km }} <span class="text-base font-normal text-gray-500">km</span></p>
          <p class="text-sm text-gray-500 mt-1">Hátravan: <span class="font-semibold text-amber-600">{{ periodicMaintenances.oil_change_km_left }} km</span></p>
        </div>

        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-5 border border-amber-100">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <h3 class="text-sm font-medium text-gray-600">Fékolajcsere</h3>
          </div>
          <p class="text-2xl font-bold text-gray-800">{{ periodicMaintenances.next_break_oil_change_date }}</p>
        </div>

        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-5 border border-amber-100">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-sm font-medium text-gray-600">Műszaki érvényesség</h3>
          </div>
          <p class="text-2xl font-bold text-gray-800">{{ periodicMaintenances.inspection_valid_until }}</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Üzemanyag statisztika -->
  <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        Üzemanyag statisztika
      </h2>
    </div>
    <div class="p-6 space-y-8">
      <!-- Havi -->
      <div>
        <div class="flex items-center gap-2 mb-4">
          <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-full">Havi</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
          <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-5 border border-emerald-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Össz. üzemanyag</h3>
            <p class="text-2xl font-bold text-gray-800">{{ fuelMonth.total_liter }} <span class="text-base font-normal text-gray-500">l</span></p>
          </div>
          <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-5 border border-emerald-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Megtett km</h3>
            <p class="text-2xl font-bold text-gray-800">{{ fuelMonth.total_km }} <span class="text-base font-normal text-gray-500">km</span></p>
          </div>
          <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-5 border border-emerald-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Össz. költség</h3>
            <p class="text-2xl font-bold text-gray-800">{{ fuelMonth.total_cost }} <span class="text-base font-normal text-gray-500">Ft</span></p>
          </div>
          <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-5 border border-emerald-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Átlagfogyasztás</h3>
            <p class="text-2xl font-bold text-gray-800">{{ fuelMonth.avg_consumption }} <span class="text-base font-normal text-gray-500">l/100km</span></p>
          </div>
          <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-5 border border-emerald-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Tankolások</h3>
            <p class="text-2xl font-bold text-gray-800">{{ fuelMonth.monthly_fuel_count }} <span class="text-base font-normal text-gray-500">db</span></p>
          </div>
        </div>
      </div>

      <!-- Éves -->
      <div>
        <div class="flex items-center gap-2 mb-4">
          <span class="px-3 py-1 bg-teal-100 text-teal-700 text-sm font-medium rounded-full">Éves</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
          <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-5 border border-teal-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Össz. üzemanyag</h3>
            <p class="text-2xl font-bold text-gray-800">{{ fuelYear.total_liter }} <span class="text-base font-normal text-gray-500">l</span></p>
          </div>
          <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-5 border border-teal-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Megtett km</h3>
            <p class="text-2xl font-bold text-gray-800">{{ fuelYear.total_km }} <span class="text-base font-normal text-gray-500">km</span></p>
          </div>
          <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-5 border border-teal-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Össz. költség</h3>
            <p class="text-2xl font-bold text-gray-800">{{ fuelYear.total_cost }} <span class="text-base font-normal text-gray-500">Ft</span></p>
          </div>
          <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-5 border border-teal-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Átlagfogyasztás</h3>
            <p class="text-2xl font-bold text-gray-800">{{ fuelYear.avg_consumption }} <span class="text-base font-normal text-gray-500">l/100km</span></p>
          </div>
          <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-5 border border-teal-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Tankolások</h3>
            <p class="text-2xl font-bold text-gray-800">{{ fuelYear.yearly_fuel_count }} <span class="text-base font-normal text-gray-500">db</span></p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Szerviz statisztika -->
  <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-violet-500 to-purple-500 px-6 py-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        Szerviz statisztika
      </h2>
    </div>
    <div class="p-6 space-y-8">
      <!-- Havi -->
      <div>
        <div class="flex items-center gap-2 mb-4">
          <span class="px-3 py-1 bg-violet-100 text-violet-700 text-sm font-medium rounded-full">Havi</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-xl p-5 border border-violet-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Össz. szervizköltség</h3>
            <p class="text-2xl font-bold text-gray-800">{{ statisticsMonth.total_cost }} <span class="text-base font-normal text-gray-500">Ft</span></p>
          </div>
          <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-xl p-5 border border-violet-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Javítások száma</h3>
            <p class="text-2xl font-bold text-gray-800">{{ statisticsMonth.service_count }} <span class="text-base font-normal text-gray-500">db</span></p>
          </div>
        </div>
      </div>

      <!-- Éves -->
      <div>
        <div class="flex items-center gap-2 mb-4">
          <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-medium rounded-full">Éves</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="bg-gradient-to-br from-purple-50 to-fuchsia-50 rounded-xl p-5 border border-purple-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Össz. szervizköltség</h3>
            <p class="text-2xl font-bold text-gray-800">{{ statisticsYear.total_cost }} <span class="text-base font-normal text-gray-500">Ft</span></p>
          </div>
          <div class="bg-gradient-to-br from-purple-50 to-fuchsia-50 rounded-xl p-5 border border-purple-100">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Javítások száma</h3>
            <p class="text-2xl font-bold text-gray-800">{{ statisticsYear.service_count }} <span class="text-base font-normal text-gray-500">db</span></p>
          </div>
        </div>
      </div>
    </div>
  </section>

</div>

</PublicLayout>
</template>