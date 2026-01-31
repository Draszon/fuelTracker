<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm, router, usePage, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted, watch } from 'vue';

//props-ban megkapja a backend-től az adatokat
const props = defineProps({
  fuelDatas: Array,
  carDatas: Array
});

const page = usePage();
const flashMessage = computed(() => page.props.flash?.message);
const message = ref(flashMessage.value);
const selectedCarId = ref(null);

let editActive = false;

watch(flashMessage, (val) => {
  if (val) {
    message.value = val;
    setTimeout(() => {
      message.value = null;
    }, 3000);
  }
});

//a form mezői amit később feldolgoz
const form = useForm({
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
//és visszaírja a km-t az aktuális km-hez
function deleteFuelData(id, km, car_id) {
  router.delete(`/fuel-delete/${id}`, {
    data: {
      km,
      car_id
    },
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

const filteredFuelData = computed(() => {
  if (!selectedCarId.value) return props.fuelDatas;
  return props.fuelDatas.filter(f => f.car_id === selectedCarId.value);
});

// Pagination
const currentPage = ref(1);
const itemsPerPage = 30;

const totalPages = computed(() => Math.ceil(filteredFuelData.value.length / itemsPerPage));

const paginatedFuelData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredFuelData.value.slice(start, end);
});

// Reset page when filter changes
watch(selectedCarId, () => {
  currentPage.value = 1;
});

function goToPage(pageNum) {
  if (pageNum >= 1 && pageNum <= totalPages.value) {
    currentPage.value = pageNum;
  }
}

// Generate page numbers to display
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

</script>

<template>
<Head>
  <title>Üzemanyag</title>
</Head>

<PublicLayout>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

  <!-- Üzemanyag feltöltés form -->
  <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        Üzemanyag adatok feltöltése / módosítása
      </h2>
      <Link href="/travel-cost-calculator" 
        class="px-5 py-2 bg-white/20 hover:bg-white/30 text-white font-medium
        rounded-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm backdrop-blur-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
        Útiköltség kalkulátor
      </Link>
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

      <form @submit.prevent="editActive ? update(form.id) : store()" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="space-y-2">
            <label for="date" class="block text-sm font-medium text-gray-700">Dátum</label>
            <input type="date" required v-model="form.date" id="date"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="car" class="block text-sm font-medium text-gray-700">Gépjármű</label>
            <select required v-model="form.car_id" id="car"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
              <option value="" disabled>Válassz gépjárművet...</option>
              <option v-for="car in carDatas" :key="car.id" :value="car.id">{{ car.name }}</option>
            </select>
          </div>

          <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-gray-700">Töltőállomás neve</label>
            <input type="text" placeholder="MOL" required v-model="form.name" id="name"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="quantity" class="block text-sm font-medium text-gray-700">Mennyiség (liter)</label>
            <input type="number" placeholder="25" required step="0.01" v-model="form.quantity" id="quantity"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="km" class="block text-sm font-medium text-gray-700">Megtett táv (km)</label>
            <input type="number" placeholder="560" required v-model="form.km" id="km"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
          </div>

          <div class="space-y-2">
            <label for="money" class="block text-sm font-medium text-gray-700">Összeg (Ft)</label>
            <input type="number" placeholder="21600" required v-model="form.money" id="money"
              class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4
              focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
              focus:bg-white transition-all duration-200">
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
          <button type="submit" 
            class="px-8 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium
            rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ editActive ? 'Frissítés' : 'Feltöltés' }}
          </button>
          <button v-if="editActive" type="button" @click="form.reset(); editActive = false"
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

  <!-- Tankolások listája -->
  <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-4">
      <h2 class="text-lg font-semibold text-white flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        Tankolási napló
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
            focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20
            transition-all duration-200">
            <option :value="null">Összes gépjármű</option>
            <option v-for="car in carDatas" :key="car.id" :value="car.id">{{ car.name }}</option>
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
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Gépjármű</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Töltőállomás</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Mennyiség</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Táv</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Fogyasztás</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Összeg</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Műveletek</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="fuelData in paginatedFuelData" :key="fuelData.id" 
              class="hover:bg-gray-50 transition-colors duration-150">
              <td class="px-4 py-3 text-sm text-gray-800 whitespace-nowrap">{{ fuelData.date }}</td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded-lg">
                  {{ fuelData.car.name }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-800 whitespace-nowrap">{{ fuelData.name }}</td>
              <td class="px-4 py-3 text-sm text-gray-800 text-right whitespace-nowrap">
                <span class="font-medium">{{ fuelData.quantity }}</span> <span class="text-gray-500">l</span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-800 text-right whitespace-nowrap">
                <span class="font-medium">{{ fuelData.km }}</span> <span class="text-gray-500">km</span>
              </td>
              <td class="px-4 py-3 text-right whitespace-nowrap">
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg">
                  {{ Number(fuelData.consumption).toFixed(1) }} l/100km
                </span>
              </td>
              <td class="px-4 py-3 text-sm font-semibold text-gray-800 text-right whitespace-nowrap">
                {{ fuelData.money.toLocaleString() }} Ft
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex justify-center gap-2">
                  <button @click="loadSelectedData(fuelData)"
                    class="p-2 text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-all duration-200"
                    title="Szerkesztés">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button @click="deleteFuelData(fuelData.id, fuelData.km, fuelData.car_id)"
                    class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all duration-200"
                    title="Törlés">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Üres állapot -->
        <div v-if="filteredFuelData.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          <p class="text-gray-500">Nincs tankolási adat</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-500">
          Összesen <span class="font-semibold text-gray-700">{{ filteredFuelData.length }}</span> rekord, 
          <span class="font-semibold text-gray-700">{{ totalPages }}</span> oldal
        </div>
        <nav class="flex items-center gap-1">
          <!-- Első oldal -->
          <button @click="goToPage(1)" :disabled="currentPage === 1"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
          </button>
          <!-- Előző oldal -->
          <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          
          <!-- Oldalszámok -->
          <template v-for="pageNum in visiblePages" :key="pageNum">
            <span v-if="pageNum === '...'" class="px-2 text-gray-400">...</span>
            <button v-else @click="goToPage(pageNum)"
              :class="[
                'min-w-[40px] h-10 rounded-lg font-medium transition-all duration-200',
                currentPage === pageNum 
                  ? 'bg-emerald-500 text-white shadow-sm' 
                  : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'
              ]">
              {{ pageNum }}
            </button>
          </template>

          <!-- Következő oldal -->
          <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
          <!-- Utolsó oldal -->
          <button @click="goToPage(totalPages)" :disabled="currentPage === totalPages"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
          </button>
        </nav>
      </div>

      <!-- Összesítés -->
      <div v-if="filteredFuelData.length > 0" class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4">
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-3 sm:p-4 border border-emerald-100">
          <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">Tankolás</p>
          <p class="text-lg sm:text-2xl font-bold text-gray-800 mt-1">{{ filteredFuelData.length }} <span class="text-sm sm:text-base font-normal text-gray-500">db</span></p>
        </div>
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-3 sm:p-4 border border-emerald-100">
          <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">Mennyiség</p>
          <p class="text-lg sm:text-2xl font-bold text-gray-800 mt-1">{{ filteredFuelData.reduce((sum, f) => sum + Number(f.quantity), 0).toFixed(1) }} <span class="text-sm sm:text-base font-normal text-gray-500">l</span></p>
        </div>
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-3 sm:p-4 border border-emerald-100">
          <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">Átlagfogy.</p>
          <p class="text-lg sm:text-2xl font-bold text-gray-800 mt-1">{{ (filteredFuelData.reduce((sum, f) => sum + Number(f.consumption), 0) / filteredFuelData.length).toFixed(1) }} <span class="text-sm sm:text-base font-normal text-gray-500">l/100</span></p>
        </div>
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-3 sm:p-4 border border-emerald-100">
          <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">Költség</p>
          <p class="text-lg sm:text-2xl font-bold text-gray-800 mt-1 truncate">{{ filteredFuelData.reduce((sum, f) => sum + Number(f.money), 0).toLocaleString() }} <span class="text-sm sm:text-base font-normal text-gray-500">Ft</span></p>
        </div>
      </div>
    </div>
  </section>

</div>

</PublicLayout>
</template>