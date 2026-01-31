<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

// Autók listája a backend-től
const props = defineProps({
  carDatas: Array,
  userId: Number,
});

// Flash üzenetek kezelése
const page = usePage();

// UI állapotok (szerkesztés / új autó hozzáadása)
let editActive = ref(false);
let addNewCar = ref(false);

const btnTitle = {
  'store': 'Feltöltés',
  'update': 'Frissítés'
}

// Form adatok
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

// Új autó mentése az adatbázisba
const store = () => {
  form.post('/car-store', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      addNewCar.value = false;
    }
  });
}

// Autó törlése
const deleteCar = (selected) => {
  router.delete(`/car-delete/${selected}`, {
    preserveScroll: true
  });
}

// Kiválasztott autó adatainak betöltése a formba szerkesztéshez
const loadSelectedCar = (selected) => {
  editActive.value = true;
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

// Autó adatok frissítése
const updateCar = (id) => {
  form.put(`/car-update/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      addNewCar.value = false;
      editActive.value = false;
    }
  });
}

</script>

<template>
<Head>
  <title>Gépjármű</title>
</Head>

<PublicLayout>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

  <!-- Új autó hozzáadása / szerkesztés form -->
  <section v-show="addNewCar || editActive" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ editActive ? 'Gépjármű szerkesztése' : 'Új gépjármű feltöltése' }}
      </h2>
    </div>
    <div class="p-6">
      <form @submit.prevent="editActive ? updateCar(form.id) : store()" class="space-y-6">
        
        <!-- Alapadatok -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-gray-700">Elnevezés</label>
            <input type="text" placeholder="Szuzu" required v-model="form.name" id="name"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="type" class="block text-sm font-medium text-gray-700">Típus</label>
            <input type="text" placeholder="Suzuki Swift" required v-model="form.car_type" id="type"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="licence-plate" class="block text-sm font-medium text-gray-700">Rendszám</label>
            <input type="text" placeholder="KZN-235" required v-model="form.licence_plate" id="licence-plate"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="nav-avg" class="block text-sm font-medium text-gray-700">NAV szerinti átlagfogyasztás (l/100km)</label>
            <input type="number" placeholder="8.6" required step="0.01" v-model="form.average_fuel_consumption" id="nav-avg"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="year" class="block text-sm font-medium text-gray-700">Gyártás éve</label>
            <input type="number" placeholder="2008" required v-model="form.year" id="year"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="current-km" class="block text-sm font-medium text-gray-700">Km óra állása</label>
            <input type="number" placeholder="112544" required v-model="form.current_km" id="current-km"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
          </div>
        </div>

        <!-- Olajcsere adatok -->
        <div class="pt-4 border-t border-gray-100">
          <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
            Olajcsere adatok
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="space-y-2">
              <label for="oil-change-cycle-km" class="block text-sm font-medium text-gray-700">Olajcsere ciklus (km)</label>
              <input type="number" placeholder="10000" required v-model="form.oil_change_cycle_km" id="oil-change-cycle-km"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
                focus:bg-white transition-all duration-200">
            </div>

            <div class="space-y-2">
              <label for="last-oil-change-cycle-km" class="block text-sm font-medium text-gray-700">Utolsó olajcsere (km)</label>
              <input type="number" placeholder="156223" required v-model="form.last_oil_change_km" id="last-oil-change-cycle-km"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
                focus:bg-white transition-all duration-200">
            </div>

            <div class="space-y-2">
              <label for="oil-change-cycle-year" class="block text-sm font-medium text-gray-700">Olajcsere ciklus (év)</label>
              <input type="number" placeholder="2" required v-model="form.oil_change_cycle_year" id="oil-change-cycle-year"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
                focus:bg-white transition-all duration-200">
            </div>

            <div class="space-y-2">
              <label for="last-oil-change-cycle-date" class="block text-sm font-medium text-gray-700">Utolsó olajcsere dátuma</label>
              <input type="date" required v-model="form.last_oil_change_date" id="last-oil-change-cycle-date"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
                focus:bg-white transition-all duration-200">
            </div>
          </div>
        </div>

        <!-- Fékolaj adatok -->
        <div class="pt-4 border-t border-gray-100">
          <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
            Fékolaj adatok
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label for="break-oil-cycle-km" class="block text-sm font-medium text-gray-700">Fékolaj csere ciklus (év)</label>
              <input type="number" placeholder="2" required v-model="form.break_oil_cycle_year" id="break-oil-cycle-km"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
                focus:bg-white transition-all duration-200">
            </div>

            <div class="space-y-2">
              <label for="last-break-oil-cycle-date" class="block text-sm font-medium text-gray-700">Utolsó fékolaj csere</label>
              <input type="date" required v-model="form.last_break_oil_change_date" id="last-break-oil-cycle-date"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
                focus:bg-white transition-all duration-200">
            </div>
          </div>
        </div>

        <!-- Műszaki vizsga -->
        <div class="pt-4 border-t border-gray-100">
          <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
            Műszaki vizsga
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label for="inspection-start" class="block text-sm font-medium text-gray-700">Műszaki érvényesség kezdete</label>
              <input type="date" required v-model="form.inspection_valid_from" id="inspection-start"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
                focus:bg-white transition-all duration-200">
            </div>

            <div class="space-y-2">
              <label for="inspection-end" class="block text-sm font-medium text-gray-700">Műszaki érvényesség vége</label>
              <input type="date" required v-model="form.inspection_valid_until" id="inspection-end"
                class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
                focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
                focus:bg-white transition-all duration-200">
            </div>
          </div>
        </div>

        <!-- Gombok -->
        <div class="pt-4 flex flex-col sm:flex-row gap-3">
          <button type="submit" 
            class="px-8 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium
            rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ editActive ? btnTitle.update : btnTitle.store }}
          </button>
          <button type="button" @click="addNewCar = false; editActive = false; form.reset()"
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

  <!-- Autók listája -->
  <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        Gépjárművek
      </h2>
      <button @click="addNewCar = true" 
        class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-medium
        rounded-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Gépjármű hozzáadása
      </button>
    </div>
    <div class="p-6">
      <!-- Flash üzenet -->
      <div v-if="page.props.flash?.message" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
        <p class="font-medium text-red-600 flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ page.props.flash.message }}
        </p>
      </div>

      <!-- Autók kártyák -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div v-for="carData in carDatas" :key="carData.id"
          class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
          
          <!-- Kártya fejléc -->
          <div class="bg-gradient-to-r from-slate-700 to-slate-600 px-5 py-4">
            <h3 class="font-bold text-xl text-white">{{ carData.name }}</h3>
            <p class="text-slate-300 text-sm">{{ carData.car_type }}</p>
          </div>

          <!-- Kártya tartalom -->
          <div class="p-5 space-y-4">
            <!-- Rendszám badge -->
            <div class="flex items-center gap-2">
              <span class="px-3 py-1.5 bg-blue-100 text-blue-700 font-bold rounded-lg text-sm tracking-wider">
                {{ carData.licence_plate }}
              </span>
              <span class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-sm">
                {{ carData.year }}
              </span>
            </div>

            <!-- Adatok grid -->
            <div class="space-y-3">
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Tulajdonos</span>
                <span class="text-sm font-medium text-gray-800">{{ carData.user ? carData.user.name : 'Ismeretlen' }}</span>
              </div>

              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">NAV átlagfogyasztás</span>
                <span class="text-sm font-medium text-gray-800">{{ carData.average_fuel_consumption }} l/100km</span>
              </div>

              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Kilométeróra</span>
                <span class="text-sm font-bold text-emerald-600">{{ carData.current_km.toLocaleString() }} km</span>
              </div>
            </div>

            <!-- Karbantartás info -->
            <div class="bg-amber-50 rounded-xl p-4 space-y-2">
              <h4 class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Karbantartás</h4>
              <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                  <p class="text-gray-500 text-xs">Olajcsere ciklus</p>
                  <p class="font-medium text-gray-800">{{ carData.oil_change_cycle_km.toLocaleString() }} km / {{ carData.oil_change_cycle_year }} év</p>
                </div>
                <div>
                  <p class="text-gray-500 text-xs">Utolsó olajcsere</p>
                  <p class="font-medium text-gray-800">{{ carData.last_oil_change_date }}</p>
                </div>
                <div>
                  <p class="text-gray-500 text-xs">Fékolaj ciklus</p>
                  <p class="font-medium text-gray-800">{{ carData.break_oil_cycle_year }} év</p>
                </div>
                <div>
                  <p class="text-gray-500 text-xs">Utolsó fékolaj</p>
                  <p class="font-medium text-gray-800">{{ carData.last_break_oil_change_date }}</p>
                </div>
              </div>
            </div>

            <!-- Műszaki info -->
            <div class="bg-emerald-50 rounded-xl p-4">
              <h4 class="text-xs font-semibold text-emerald-700 uppercase tracking-wider mb-2">Műszaki vizsga</h4>
              <div class="flex justify-between text-sm">
                <div>
                  <p class="text-gray-500 text-xs">Érvényes</p>
                  <p class="font-medium text-gray-800">{{ carData.inspection_valid_from }}</p>
                </div>
                <div class="text-right">
                  <p class="text-gray-500 text-xs">Lejárat</p>
                  <p class="font-bold text-emerald-600">{{ carData.inspection_valid_until }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Kártya gombok -->
          <div class="px-5 pb-5 flex gap-3">
            <button @click="loadSelectedCar(carData)" 
              class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium
              rounded-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Módosítás
            </button>
            <button @click="deleteCar(carData.id)" 
              class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-medium
              rounded-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Üres állapot -->
      <div v-if="carDatas.length === 0" class="text-center py-12">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <p class="text-gray-500">Még nincs gépjármű hozzáadva</p>
        <button @click="addNewCar = true" class="mt-4 text-emerald-600 hover:text-emerald-700 font-medium">
          + Első gépjármű hozzáadása
        </button>
      </div>
    </div>
  </section>

</div>

</PublicLayout>
</template>